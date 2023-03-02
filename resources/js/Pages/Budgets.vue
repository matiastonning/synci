<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import ListCardWithActionBudgets from "../Comps/ListCardWithActionBudgets.vue";
import Slider from "../Comps/Slider.vue";
import DescriptionList from "../Comps/DescriptionList.vue";
import StackedCards from "../Comps/StackedCards.vue";
import DeleteModal from "../Comps/DeleteModal.vue";
</script>

<script>
import moment from "moment";
import {router} from "@inertiajs/vue3";

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
                    'Budget name': item.name,
                    'Status': item.active ? 'Connected' : 'Disconnected',
                    'Created': moment(item.created_at).format('MMMM Do YYYY, HH:mm:ss (UTC)'),
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
                    name: budget.name,
                    identifier: budget.identifier,
                    icon: '../assets/icons/ynab.png',
                    iconAlt: 'YNAB icon',
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
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg">
                    <ListCardWithActionBudgets
                        title="Connected Budgets"
                        action="Add Budget"
                        :items="budgetItems"
                        @action-clicked="(item) => tableActionClicked(item)"
                    />
                </div>
            </div>
        </div>

        <Slider :title="selectedItemId ? 'Edit Budget' : 'Add Budget'"
                :subtitle="selectedItemId ? 'Manage budget connection.' : 'Add a new budgeting app to your Synci.io account.'"
                :action-button="selectedItemId ? {
                    text: 'Delete Budget',
                    class: 'hover:bg-red-700 bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2'
                } : {
                    text: 'Add Budget',
                    class: 'hover:bg-indigo-700 bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2'
                }"
                :header-class="selectedItemId ? 'bg-gray-200 bg-opacity-60' : 'bg-indigo-700 bg-opacity-70 text-white'"
                :title-class="selectedItemId ? 'text-gray-900' : 'text-white'"
                :subtitle-class="selectedItemId ? 'text-gray-500' : 'text-white opacity-70'"
                :x-button-class="selectedItemId ? 'text-gray-400 hover:text-gray-500' : 'text-white hover:text-gray-100'"
                :open="sliderOpen" :selected-id="selectedItemId"
                @close-slider="sliderClosed" @action-clicked="(e) => sliderActionClicked(e)">

            <DescriptionList v-if="selectedItemId" :selected-item="selectedItem" />
            <StackedCards v-else :items="budgetApps" @stacked-card-selected="" />

        </Slider>

        <DeleteModal
            :open="deleteModalOpen"
            :selected-id="selectedItemId"
            title="Delete Budget"
            :message="'Are you sure you want to delete this budget? This action cannot be undone.'"
            @close-modal="deleteModalOpen = false"
            @confirm-modal="(id) => deleteBudget(id)"
        />
    </AppLayout>
</template>
