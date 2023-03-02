<script setup>
import { CheckCircleIcon, ChevronRightIcon, WalletIcon } from '@heroicons/vue/24/solid'
import AddEditBudget from "./AddEditBudget.vue";
import moment from "moment";
import EmptyState from "./EmptyState.vue";
import AddEditSource from "./AddEditSource.vue";

defineProps({
    title: String,
    action: String,
    items: Array,
});

</script>

<script>
export default {
    emits: ['actionClicked'],
    methods: {
        openSlider(edit, item) {
            this.selectedItem = item;
            this.sliderOpen = true;
            this.editMode = edit;
        }
    },
    data(){
        return {
            editMode: false,
            sliderOpen: false,
            selectedItem: []
        }
    }
}
</script>

<template>
  <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
    <div class="-ml-4 -mt-2 flex flex-wrap items-center justify-between sm:flex-nowrap">
      <div class="ml-4 mt-2">
        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ title }}</h3>
      </div>
      <div class="ml-4 mt-2 flex-shrink-0">
        <button @click="this.$emit('actionClicked', null)" type="button" class="relative inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">{{ action }}</button>
      </div>
    </div>
  </div>
    <div class="overflow-hidden bg-white shadow">
        <ul role="list" class="divide-y divide-gray-200">
            <li v-for="item in items" :key="item.id">
                <a @click="this.$emit('actionClicked', item)" class="block hover:bg-gray-50 cursor-pointer">
                    <div class="flex items-center px-4 py-4 sm:px-6">
                        <div class="flex min-w-0 flex-1 items-center">
                            <div class="flex-shrink-0">
                                <img class="h-12 w-12 rounded-full object-scale-down bg-gray-100" :src="item.icon" :alt="item.iconAlt" />
                            </div>
                            <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
                                <div>
                                    <p class="truncate text-sm font-medium text-indigo-600">{{ item.type }}</p>
                                    <p class="mt-2 flex items-center text-sm text-gray-500">
                                        <WalletIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" />
                                        <span class="truncate">{{ item.name }}</span>
                                    </p>
                                </div>
                                <div class="hidden md:block">
                                    <div>
                                        <p class="text-sm text-gray-900">
                                            Created
                                            {{ ' ' }}
                                            <time :datetime="item.created_at">{{ moment(item.created_at).fromNow() }}</time>
                                        </p>
                                        <p class="mt-2 flex items-center text-sm text-gray-500">
                                            <CheckCircleIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-green-400" aria-hidden="true" />
                                            {{ item.active ? 'Connected' : 'Disconnected' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <ChevronRightIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        </div>
                    </div>
                </a>
            </li>
            <div v-if="items.length === 0" class="my-20">
                <EmptyState title="No budgets connected" subtitle="Get started by adding a budgeting app to your Synci.io account." action="Add Budget" />
            </div>
        </ul>
    </div>
    <AddEditBudget
        :title="editMode ? 'Edit Budget' : 'Add Budget'"
        :subtitle="editMode ? 'Manage budget connection.' : 'Add a new budgeting app to your Synci.io account.'"
        :edit="editMode"
        :open="sliderOpen"
        :selected-item="selectedItem"
        :budget-types="$page['props']['budget_types']"
        @close-slider="sliderOpen = false"
    />
</template>
