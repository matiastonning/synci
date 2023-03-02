
<script>

import {router} from "@inertiajs/vue3";


export default {
    props: {
        open: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            required: true
        },
        message: {
            type: String,
            required: true
        },
        action: {
            type: String,
            default: 'Delete'
        },
        sourceId: {
            type: Number
        },
        budgetItems: {
            type: Object,
            default: []
        }
    },
    data(){
        return {
            startDate: '',
            budgetId: '',
        }
    },
    methods: {
        close() {
            this.$emit('closeModal')
        },
        confirm() {
            this.$emit('confirmModal', {sourceId: this.sourceId, budgetId: this.budgetId, startDate: this.startDate})
        }
    }
}
</script>

<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-10" @close="close">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-30 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel class="relative transform overflow-hidden rounded-lg bg-opacity-90 bg-white backdrop-blur text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <BookOpenIcon class="h-6 w-6 text-indigo-600" aria-hidden="true" />
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left min-h-[550px]">
                                        <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">{{ title }}</DialogTitle>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">{{ message }}</p>

                                            <hr class="mt-8">

                                            <label for="transfer_start_date" class="block text-sm font-medium text-gray-700 mt-8">Transfer Start Date</label>
                                            <vue-tailwind-datepicker :formatter="formatter" id="transfer_start_date" as-single v-model="dateValue" @update:modelValue="startDate = dateValue[0]" inputClasses="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                            <p class="mt-2 text-xs text-gray-500">Transactions with a booking date earlier than the transfer start date will not be transferred.</p>

                                            <hr class="mt-8">

                                            <label class="block text-sm font-medium text-gray-700 mt-8">Select Budget</label>
                                            <div v-if="budgetItems.length > 0">
                                                <StackedCards :items="budgetItems" @stacked-card-selected="(item) => budgetId = item.id" class="overflow-auto p-4 max-h-72 rounded-lg border mt-1" />
                                                <p class="mt-2 text-xs text-gray-500">New transactions will be transferred to the selected budget upon activation.</p>
                                            </div>

                                            <EmptyState v-else title="No budgets connected" class="overflow-auto p-5 rounded-lg border mt-1" subtitle="Please add a budget to activate this source." action="Add Budget" :action-href="route('connect.budgets')" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-200 bg-opacity-30 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="button" :disabled="budgetItems.length > 0 && !dateValue.length > 0" class="inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50" @click="confirm">{{ action }}</button>
                                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="close" ref="cancelButtonRef">Cancel</button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { BookOpenIcon } from '@heroicons/vue/24/outline'
import StackedCards from "./StackedCards.vue";
import VueTailwindDatepicker from 'vue-tailwind-datepicker';
import {ref} from "vue";
import EmptyState from "./EmptyState.vue";

const dateValue = ref([]);
const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MMM'
})
</script>
