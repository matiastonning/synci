<script setup>
import { CheckCircleIcon, ChevronRightIcon, WalletIcon, CircleStackIcon } from '@heroicons/vue/24/solid'
import { BuildingLibraryIcon } from '@heroicons/vue/24/outline'
import moment from "moment";
import EmptyState from "./EmptyState.vue";

defineProps({
    title: String,
    action: String,
    items: Array,
    emptyTitle: String,
    emptySubtitle: String,
    emptyAction: String,
    emptyActionHref: String,
});

</script>

<script>
export default {
    emits: ['actionClicked', 'actionButtonClicked'],
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
  <div class="border-b border-gray-200 border-opacity-70 dark:border-gray-700 dark:border-opacity-70 px-4 py-5 sm:px-6">
    <div class="-ml-4 -mt-2 flex flex-wrap items-center justify-between sm:flex-nowrap">
      <div class="ml-4 mt-2">
        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ title }}</h3>
      </div>
      <div class="ml-4 mt-2 flex-shrink-0">
        <button @click="this.$emit('actionClicked', null)" type="button" class="relative inline-flex items-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700">{{ action }}</button>
      </div>
    </div>
  </div>
    <div class="overflow-hidden shadow">
        <ul role="list" class="divide-y divide-gray-200 divide-opacity-80 dark:divide-gray-700 dark:divide-opacity-70">
            <li v-for="item in items" :key="item.id">
                <a @click="this.$emit('actionClicked', item)" class="block hover:bg-gray-50 dark:hover:bg-opacity-[2.5%] cursor-pointer">
                    <div class="flex items-center px-4 py-4 sm:px-6">
                        <div class="flex min-w-0 flex-1 items-center">
                            <div class="flex-shrink-0">
                                <span v-if="item.icon" v-html="item.icon"></span>
                                <div v-else>
                                    <BuildingLibraryIcon v-if="item.title==='Bank Account'" class="h-12 w-12 bg-orange-100 p-2.5 dark:bg-orange-600 dark:bg-opacity-25 text-orange-400 dark:text-orange-300 rounded-full object-cover" aria-hidden="true" />
                                </div>
                            </div>
                            <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
                                <div>
                                    <p class="truncate text-sm font-medium text-teal-600 dark:text-teal-400"><span v-html="item.title"></span></p>
                                    <p class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <component :is="item.subIcon" class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" />
                                        <span class="truncate"><span v-html="item.subtitle"></span></span>
                                    </p>
                                </div>
                                <div class="hidden md:block">
                                    <div v-if="item.type === 'source'">
                                        <div v-if="item.active">
                                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                                Synced
                                                {{ ' ' }}
                                                <time :datetime="item.last_synced">{{ moment(item.last_synced).utc().fromNow() }}</time>
                                            </p>
                                            <p class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                <CheckCircleIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-teal-400 dark:text-teal-500" aria-hidden="true" />
                                                Connected to {{ item.destination_name + ' (' + item.destination_account + ')' }}
                                            </p>
                                        </div>
                                        <div v-else class="flex mt-1 items-center">
                                            <button @click.stop="this.$emit('actionButtonClicked', item)" class="flex w-24 items-center justify-center rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-transparent dark:bg-gray-500 dark:bg-opacity-[25%] dark:text-white dark:hover:bg-opacity-[12%]">Activate</button>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <p class="text-sm text-gray-900 dark:text-gray-100">
                                            <span v-html="item.extended_title"></span>
                                        </p>
                                        <p class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                            <CheckCircleIcon v-if="item.active" class="mr-1.5 h-5 w-5 flex-shrink-0 text-teal-400 dark:text-teal-500" aria-hidden="true" /> {{ item.active ? 'Connected' : 'Disconnected' }}
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
                <EmptyState :title="emptyTitle" :subtitle="emptySubtitle" :action="emptyAction" :action-href="emptyActionHref" />
            </div>
        </ul>
    </div>
</template>
