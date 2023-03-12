<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import ListCardWithAction from "../Comps/ListCardWithAction.vue";
import Slider from "../Comps/Slider.vue";
import DescriptionList from "../Comps/DescriptionList.vue";
import StackedCards from "../Comps/StackedCards.vue";
import DeleteModal from "../Comps/DeleteModal.vue";
</script>

<script>
import moment from "moment";
import {router} from "@inertiajs/vue3";
import { WalletIcon } from '@heroicons/vue/24/outline'


export default {
    props: {
        budgets: {
            type: Object,
            default: []
        },
    },
    data() {
        return {
            budgetItems: [],
            budgetApps: [
                { id: 0, title: 'YNAB', subtitle: 'Connect to YNAB', icon: '../assets/icons/ynab.png', extra: 'https://youneedabudget.com/' },
            ],
            budgetTypes: this.$page['props']['budget_types'],
            sliderOpen: false,
            selectedItem: {},
            selectedItemId: 0,
            deleteModalOpen: false,
            activeHref: 'https://youneedabudget.com'
        }
    },
    methods: {
        sliderActionClicked(selectedItemId) {
            if(selectedItemId) {
                // id provided, show delete budget modal
                this.$emit('closeSlider');
                this.deleteModalOpen = true;

            } else {
                // no id provided, add budget
                axios.get(route('connect.budgets.oauth', {budgetType: this.budgetTypes['YNAB']}))
                    // 200
                    .then(res => {
                        if(res.data.url) {
                            //Redirect to authentication page
                            window.location.replace(res.data.url);
                        } else {
                            console.log('Failed to get OAuth URL. Please contact support.')
                            this.$emitter.emit('notify', {
                                status: 'error',
                                statusTitle: 'Error',
                                statusMessage: 'Failed to get OAuth URL. Please contact support.'
                            });
                            this.$emit('closeSlider');
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
                                this.$emit('closeSlider');
                            } else {
                                this.$emitter.emit('notify', {
                                    status: 'error',
                                    statusTitle: 'Error',
                                    statusMessage: 'Unknown server error. Please contact support.'
                                });
                                this.$emit('closeSlider');
                            }
                        } else if (error.request) {
                            // The request was made but no response was received
                            this.$emitter.emit('notify', {
                                status: 'error',
                                statusTitle: 'No response',
                                statusMessage: 'Server did not respond.'
                            });
                            this.$emit('closeSlider');
                        } else {
                            // Something happened in setting up the request that triggered an Error
                            this.$emitter.emit('notify', {
                                status: 'error',
                                statusTitle: 'Error',
                                statusMessage: 'Unknown client error. Please contact support.'
                            });
                            this.$emit('closeSlider');
                        }
                    })
            }

        },
        sliderClosed() {
            this.sliderOpen = false;
        },
        tableActionClicked(item) {
            if(item) {
                this.selectedItemId = item.id;
                this.selectedItem = {
                    'Budget app': item.type,
                    'Budget name': item.title,
                    'Budget Account': item.subtitle,
                    'Status': item.active ? 'Connected' : 'Disconnected',
                    'Created': moment(item.created_at).utc().format('MMMM Do, YYYY - HH:mm:ss (UTC)'),
                };
            } else {
                this.selectedItemId = null;
            }

            this.sliderOpen = true;
        },
        deleteBudget(id) {
            router.delete(route('connect.budgets.delete', id), {
                onSuccess: () => {
                    router.visit(route('connect.budgets'), {
                        method: 'get',
                        preserveScroll: true,
                        only: ['budgets'],
                    });
                }
            })
        }
    },
    mounted() {
        this.budgets.forEach(budget => {
            if(budget.type === 0) {
                this.budgetItems.push({
                    id: budget.id,
                    type: 'YNAB',
                    title: budget.name,
                    subtitle: budget.account_name,
                    extended_title: 'Created <time :datetime="item.created_at">' + moment(budget.created_at).utc().fromNow() + '</time>',
                    identifier: budget.identifier,
                    account_identifier: budget.account_identifier,
                    subIcon: WalletIcon,
                    icon: '<img class="h-12 w-12 rounded-full object-scale-down bg-gray-100 dark:bg-gray-900" src="' + "../assets/icons/ynab.png" + '" :alt="YNAB" />',
                    created_at: budget.created_at,
                    active: budget.active,
                })
            }

        });
    }
}

</script>

<template>
    <AppLayout title="Budgets">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Budgets
            </h2>
        </template>

        <div class="py-5">
            <div class="max-w-7xl mx-auto">
                <div class="bg-white dark:bg-gray-800 dark:bg-opacity-80 overflow-hidden shadow-xl rounded-lg">
                    <ListCardWithAction
                        title="Connected Accounts"
                        action="Add Budget"
                        :items="budgetItems"
                        empty-title="No budgets connected"
                        empty-subtitle="Get started by adding a budgeting app to your Synci.io account."
                        @action-clicked="(item) => tableActionClicked(item)"
                    />
                </div>
            </div>
        </div>

        <Slider :title="selectedItemId ? 'Edit Budget' : 'Add Budget'"
                :subtitle="selectedItemId ? 'Manage budget connection.' : 'Add a new budgeting app to your Synci.io account.'"
                :action-button="selectedItemId ? {
                    text: 'Delete Budget',
                    class: 'hover:bg-red-700 bg-red-600'
                } : {
                    text: 'Add Budget',
                    class: 'hover:bg-teal-700 bg-teal-600'
                }"
                :header-class="selectedItemId ? 'bg-gray-200 bg-opacity-60 dark:bg-gray-700 dark:bg-opacity-40' : 'bg-teal-800 bg-opacity-70 dark:bg-teal-700 dark:bg-opacity-50 text-white'"
                :title-class="selectedItemId ? 'text-gray-900 dark:text-gray-100' : 'text-white'"
                :subtitle-class="selectedItemId ? 'text-gray-500 dark:text-gray-400' : 'text-white opacity-70'"
                :x-button-class="selectedItemId ? 'text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-600' : 'text-white hover:text-gray-100'"
                :open="sliderOpen" :selected-id="selectedItemId"
                @close-slider="sliderClosed" @action-clicked="(e) => sliderActionClicked(e)">

            <DescriptionList v-if="selectedItemId" :selected-item="selectedItem" />
            <StackedCards v-else :items="budgetApps" @stacked-card-selected="" />

        </Slider>

        <DeleteModal
            :open="deleteModalOpen"
            :selected-id="selectedItemId"
            title="Delete Account"
            :message="'Are you sure you want to delete this budget account? This action cannot be undone.'"
            @close-modal="deleteModalOpen = false"
            @confirm-modal="(id) => deleteBudget(id)"
        />
    </AppLayout>
</template>
