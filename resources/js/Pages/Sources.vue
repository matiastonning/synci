<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import ListCardWithActionSources from "../Comps/ListCardWithActionSources.vue";
import Slider from "../Comps/Slider.vue";
import DeleteModal from "../Comps/DeleteModal.vue";
import DescriptionList from "../Comps/DescriptionList.vue";
import StackedCards from "../Comps/StackedCards.vue";
import ActionModal from "../Comps/ActionModal.vue";
import SelectMenu from "../Comps/SelectMenu.vue";
import {BuildingLibraryIcon} from "@heroicons/vue/24/outline";

const providers = [
    { id: 0,title: 'Bank Account', subtitle: 'Connect a bank account', customIcon: true, icon: BuildingLibraryIcon, extra: 'https://www.tink.com/' },
];

</script>

<script>
import moment from "moment/moment";
import {router} from "@inertiajs/vue3";

export default {
    props: {
        sources: {
            type: Object,
            default: []
        },
        activeBudgets: {
            type: Object,
            default: []
        }
    },
    data() {
        return {
            sourceItems: [],
            sliderOpen: false,
            selectedItem: {},
            selectedItemId: 0,
            deleteModalOpen: false,
            activateModalOpen: false,
            sourceTypes: this.$page['props']['source_types'],
            country: this.$page.props.countries[0].key,
        }
    },
    methods: {
        sliderActionClicked(selectedItemId) {
            if (selectedItemId) {
                // id provided, show delete budget modal
                this.$emit('closeSlider');
                this.deleteModalOpen = true;

            } else {
                // no id provided, add budget
                axios.get(route('connect.sources.oauth', {sourceType: this.sourceTypes['Tink'], country: this.country}))
                    // 200
                    .then(res => {
                        if(res.data.url) {
                            //Redirect to authentication page
                            window.location.replace(res.data.url);
                        } else {
                            this.$emitter.emit('notify', {
                                status: 'error',
                                statusTitle: 'Error',
                                statusMessage: 'Failed to get OAuth URL. Please contact support.'
                            });
                            this.closeSlider();
                        }
                    })
                    // Not 200
                    .catch((error) => {
                        if (error.response) {
                            // Request made and server responded
                            if(error.response.status === 400) {
                                this.$emitter.emit('notify', {
                                    status: 'error',
                                    statusTitle: 'Error',
                                    statusMessage: 'Failed to get OAuth URL. Please contact support.'
                                });
                            } else {
                                this.$emitter.emit('notify', {
                                    status: 'error',
                                    statusTitle: 'Error',
                                    statusMessage: 'Unknown server error. Please contact support.'
                                });
                            }
                        } else if (error.request) {
                            // The request was made but no response was received
                            this.$emitter.emit('notify', {
                                status: 'error',
                                statusTitle: 'No response',
                                statusMessage: 'Server did not respond.'
                            });
                        } else {
                            // Something happened in setting up the request that triggered an Error
                            this.$emitter.emit('notify', {
                                status: 'error',
                                statusTitle: 'Error',
                                statusMessage: 'Unknown client error. Please contact support.'
                            });
                        }
                        this.closeSlider();
                    })
            }

        },
        alterSource(sourceId, budgetId = null, startDate = null) {
            axios.put(route('connect.sources.alter', sourceId), {startDate: startDate, budgetId: budgetId})
                // 200
                .then(res => {
                    if (res.data) {
                        router.visit(route('connect.sources'), {
                            method: 'get',
                            preserveScroll: true,
                            only: ['sources'],
                            onSuccess: () => {
                                this.$emitter.emit('notify', {
                                    status: res.data.status,
                                    statusTitle: res.data.statusTitle,
                                    statusMessage: res.data.statusMessage,
                                });
                            }
                        });
                    } else {
                        this.$emitter.emit('notify', {
                            status: 'error',
                            statusTitle: 'Error',
                            statusMessage: 'Unable to process request. Please contact support.'
                        });

                        this.activateModalOpen = false;
                    }
                })
                // Not 200
                .catch((error) => {
                    if (error.response) {
                        // Request made and server responded
                        if (error.response.status === 400) {
                            this.$emitter.emit('notify', {
                                status: 'error',
                                statusTitle: 'Error',
                                statusMessage: 'Unable to process request. Please contact support.'
                            });
                        } else {
                            this.$emitter.emit('notify', {
                                status: 'error',
                                statusTitle: 'Error',
                                statusMessage: 'Unknown server error. Please contact support.'
                            });
                        }
                    } else if (error.request) {
                        // The request was made but no response was received
                        this.$emitter.emit('notify', {
                            status: 'error',
                            statusTitle: 'No response',
                            statusMessage: 'Server did not respond.'
                        });
                    } else {
                        // Something happened in setting up the request that triggered an Error
                        this.$emitter.emit('notify', {
                            status: 'error',
                            statusTitle: 'Error',
                            statusMessage: 'Unknown client error. Please contact support.'
                        });
                    }

                    this.activateModalOpen = false;
                })
        },
        sliderClosed() {
            this.sliderOpen = false;
        },
        tableActionClicked(item) {
            if (item) {
                this.selectedItemId = item.id;
                this.selectedItem = {
                    'Status': item.active ? 'Connected to ' + item.destination_name + ' (' + item.destination_account + ')' : 'Inactive',
                    'Type': item.type,
                    'Integrator': item.integrator,
                    'Account Name': item.name,
                    'Account Number': item.account,
                    'Start Date': moment(item.start_date).utc().format('MMMM Do, YYYY'),
                    'Last Sync': moment(item.last_synced).utc().format('MMMM Do, YYYY - HH:mm:ss (UTC)'),
                    'Created': moment(item.created_at).utc().format('MMMM Do, YYYY - HH:mm:ss (UTC)'),
                };
            } else {
                this.selectedItemId = null;
            }

            this.sliderOpen = true;
        },

        tableActionButtonClicked(item) {
            this.selectedItem = item;
            this.selectedItemId = item.id;
            this.openActivateSourceModal();
        },

        deleteSource(id) {
            router.delete(route('connect.sources.delete', id), {
                onSuccess: () => {
                    router.visit(route('connect.sources'), {
                        method: 'get',
                        preserveScroll: true,
                        only: ['sources'],
                    });
                }
            })
        },

        openActivateSourceModal() {
            this.activateModalOpen = true;
            this.sliderOpen = false;
        },
    },
    mounted() {
        this.sources.forEach(source => {
            if(source.type === 0) {
                this.sourceItems.push({
                    id: source.id,
                    type: 'Bank Account',
                    integrator: 'Aiia',
                    displayName: source.name + ' (' + source.identifier + ')',
                    name: source.name,
                    account: source.identifier,
                    icon: '../assets/icons/curve.png',
                    iconAlt: 'Aiia icon',
                    created_at: source.created_at,
                    last_synced: source.last_synced,
                    start_date: source.start_date,
                    active: source.active,
                    destination_type: source.destination_type,
                    destination_name: source.destination_name,
                    destination_account: source.destination_account,
                })
            } else if(source.type === 1) {
                this.sourceItems.push({
                    id: source.id,
                    type: 'Bank Account',
                    integrator: 'Tink',
                    displayName: source.name + ' (' + source.identifier + ')',
                    name: source.name,
                    account: source.identifier,
                    icon: '../assets/icons/tink.png',
                    iconAlt: 'Tink icon',
                    created_at: source.created_at,
                    last_synced: source.last_synced,
                    start_date: source.start_date,
                    active: source.active,
                    destination_type: source.destination_type,
                    destination_name: source.destination_name,
                    destination_account: source.destination_account,
                })
            }

        });
    }
}

</script>

<template>
    <AppLayout title="Sources">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Sources
            </h2>
        </template>

        <div class="py-5">
            <div class="max-w-7xl mx-auto">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg">
                    <ListCardWithActionSources
                        title="Connected Sources"
                        action="Add Source"
                        @action-clicked="(item) => tableActionClicked(item)"
                        @action-button-clicked="(item) => tableActionButtonClicked(item)"
                        :items="sourceItems"
                    />
                </div>
            </div>
        </div>

        <Slider :title="selectedItemId ? 'Edit Source' : 'Add Source'"
                :subtitle="selectedItemId ? 'Manage source connection.' : 'Connect the sources you want to integrate with Synci to get started. We have partnered with Tink to simplify this process for bank accounts.'"
                :action-button="selectedItemId ? {
                    text: 'Delete Source',
                    class: 'hover:bg-red-700 bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2'
                } : {
                    text: 'Add Source',
                    class: 'hover:bg-teal-700 bg-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2'
                }"
                :header-class="selectedItemId ? 'bg-gray-200 bg-opacity-60' : 'bg-teal-800 bg-opacity-70 text-white'"
                :title-class="selectedItemId ? 'text-gray-900' : 'text-white'"
                :subtitle-class="selectedItemId ? 'text-gray-500' : 'text-white opacity-70'"
                :x-button-class="selectedItemId ? 'text-gray-400 hover:text-gray-500' : 'text-white hover:text-gray-100'"
                :open="sliderOpen" :selected-id="selectedItemId"
                @close-slider="sliderClosed" @action-clicked="(e) => sliderActionClicked(e)">

            <DescriptionList v-if="selectedItemId" :selected-item="selectedItem"
                             :sourceMode='true'
                             :selected-id="selectedItemId"
                             @open-activate-source-modal="openActivateSourceModal"
                             @deactivate-source="(sourceId) => alterSource(sourceId)" />
            <div v-else>
                <SelectMenu @selected-changed="(selectedCountry) => country = selectedCountry" :items="$page['props']['countries']" label="Country" class="mb-6" />
                <hr class="mb-6" />
                <StackedCards :items="providers" @stacked-card-selected="" />
            </div>
        </Slider>

        <DeleteModal
            :open="deleteModalOpen"
            :selected-id="selectedItemId"
            title="Delete Source"
            :message="'Are you sure you want to delete this source and its transactions? This action cannot be undone.'"
            @close-modal="deleteModalOpen = false"
            @confirm-modal="(id) => deleteSource(id)"
        />
        <ActionModal
            v-if="selectedItem"
            title="Connect to Budget"
            :message='"Please select which budget you would like to connect the source \"" + selectedItem.name + " (" + selectedItem.account + ")" + "\" to."'
            :open="activateModalOpen"
            :source-id="selectedItemId"
            action="Activate"
            :budget-items="activeBudgets"
            @close-modal="activateModalOpen = false"
            @confirm-modal="(sourceId, budgetId, startDate) => alterSource(sourceId, budgetId, startDate)"
        />
    </AppLayout>
</template>
