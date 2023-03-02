
<script setup>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { LinkIcon, PlusIcon, QuestionMarkCircleIcon } from '@heroicons/vue/20/solid'
import StackedCards from "./StackedCards.vue";
import DescriptionList from "./DescriptionList.vue";
import DeleteModal from "./DeleteModal.vue";
import moment from "moment";

</script>


<script>

import {router, useForm} from "@inertiajs/vue3";

export default {
    emits: ['closeSlider'],
    props: {
        open: {
            type: Boolean
        },
        title: {
            type: String,
            required: true
        },
        subtitle: {
            type: String,
            required: true
        },
        edit: {
            type: Boolean,
            required: true
        },
        activeHref: {
            type: String,
            default: 'https://youneedabudget.com'
        },
        budgetTypes: {
            type: Object,
            required: true
        },
        selectedItem: {
            type: Object,
            default: []
        }
    },

    data(){
        return {
            addConnectionOpen: false,
            deleteModalOpen: false,
            budgetApps: [
                { id: 0, title: 'YNAB', subtitle: 'Connect to YNAB', icon: '../assets/icons/ynab.png', extra: 'https://youneedabudget.com/' },
            ],
        }
    },
    methods: {
        openDeleteModal() {
            this.deleteModalOpen = true;
            this.$emit('closeSlider');
        },
        deleteFormSubmit() {
            router.delete(route('connect.budgets.delete', this.selectedItem.id), {
                onSuccess: () => {
                    router.visit(route('connect.budgets'), {
                        method: 'get',
                        preserveScroll: true,
                        only: ['budgets'],
                    });
                }
            })
        },
        getOauthUrl() {
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
    }
}
</script>

<template>
  <TransitionRoot as="template" :show="open">
    <Dialog as="div" class="relative z-10" @close="this.$emit('closeSlider')">
        <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100" leave-to="opacity-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
        </TransitionChild>
      <div class="fixed inset-0" />

      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
            <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
              <DialogPanel class="pointer-events-auto w-screen max-w-md">
                <form class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl">
                  <div class="h-0 flex-1 overflow-y-auto">
                    <div :class="(this.edit ? 'bg-gray-50' : 'bg-indigo-700')" class="py-6 px-4 sm:px-6">
                      <div class="flex items-center justify-between">
                        <DialogTitle :class="(this.edit ? 'text-gray-900' : 'text-white')" class="text-lg font-medium">{{ title }}</DialogTitle>
                        <div class="ml-3 flex h-7 items-center">
                          <button type="button" :class="(this.edit ? 'text-gray-400 hover:text-gray-500' : 'bg-indigo-700 text-indigo-200 hover:text-white')" class="rounded-md" @click="this.$emit('closeSlider')">
                            <span class="sr-only">Close panel</span>
                            <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                          </button>
                        </div>
                      </div>
                      <div class="mt-1">
                        <p :class="(this.edit ? 'text-gray-500' : 'text-indigo-300')" class="text-sm">{{ subtitle }}</p>
                      </div>
                    </div>
                    <div class="flex flex-1 flex-col justify-between">

                      <div class="divide-y divide-gray-200 px-4 sm:px-6">
                        <div class="space-y-6 pt-6 pb-5">
                            <StackedCards v-if="!this.edit" :items="budgetApps" @stacked-card-selected="(item) => activeHref = item.extra" />

                            <DescriptionList
                                :selected-item="{
                                'Budget app': selectedItem.type,
                                'Budget name': selectedItem.name,
                                'Status': selectedItem.active ? 'Connected' : 'Disconnected',
                                'Created': moment(selectedItem.created_at).format('MMMM Do YYYY, HH:mm:ss (UTC)'),
                                }" v-else />
                        </div>
                        <div class="pt-4 pb-6">
                          <div class="flex text-sm">
                            <a :href="activeHref" target="_blank" class="group inline-flex items-center text-gray-500 hover:text-gray-900">
                              <QuestionMarkCircleIcon class="h-5 w-5 text-gray-400 group-hover:text-gray-500" aria-hidden="true" />
                              <span class="ml-2">Learn more about this app</span>
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="flex flex-shrink-0 justify-end px-4 py-4">
                      <button type="button" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" @click="this.$emit('closeSlider')">Cancel</button>

                      <button v-if="!this.edit" @click="getOauthUrl" type="button" class="ml-4 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Connect</button>
                      <form v-else @submit.prevent="openDeleteModal">
                          <button type="submit" class="ml-4 inline-flex justify-center rounded-md border border-transparent bg-red-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Delete Budget</button>
                      </form>
                  </div>
                </form>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
    <DeleteModal
        v-if="selectedItem"
        :open="deleteModalOpen"
        title="Delete Budget"
        :message="'Are you sure you want to delete the budget &quot;' + selectedItem.name + '&quot;? This action cannot be undone.'"
        @close-modal="deleteModalOpen = false"
        @confirm-modal="deleteFormSubmit"
    />
</template>
