<script setup>
import { CheckCircleIcon, ChevronRightIcon, CircleStackIcon } from '@heroicons/vue/24/solid'
import { BuildingLibraryIcon } from '@heroicons/vue/24/outline'
import moment from "moment";
import EmptyState from "./EmptyState.vue";
import ActionModal from "./ActionModal.vue";

</script>

<script>

import {router} from "@inertiajs/vue3";

export default {
    emits: ['actionClicked', 'actionButtonClicked'],
    props: {
        title: {
            type: String
        },
        action: {
            type: String
        },
        items: {
            type: Array
        }
    },
    methods: {
        openActivateSourceModal(item) {
            this.selectedItem = item;
            this.activateModalOpen = true;
        }
    },
}
</script>

<template>
  <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
    <div class="-ml-4 -mt-2 flex flex-wrap items-center justify-between sm:flex-nowrap">
      <div class="ml-4 mt-2">
        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ title }}</h3>
      </div>
      <div class="ml-4 mt-2 flex-shrink-0">
        <button @click="this.$emit('actionClicked', null)" type="button" class="relative inline-flex items-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">{{ action }}</button>
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
                                <BuildingLibraryIcon v-if="item.type==='Bank Account'" class="h-12 w-12 bg-orange-100 p-2.5 text-orange-400 rounded-full object-cover" aria-hidden="true" />
                            </div>
                            <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
                                <div>
                                    <div class="flex text-sm">
                                        <p class="truncate text-sm font-medium text-teal-600">{{ item.type }}</p>
                                    </div>
                                    <p class="mt-2 flex items-center text-sm text-gray-500">
                                        <CircleStackIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" />
                                        <span class="truncate">{{ item.displayName }}</span>
                                    </p>
                                </div>
                                <div class="hidden md:block">
                                    <div>
                                        <div v-if="item.active">
                                            <p class="text-sm text-gray-900">
                                                Synced
                                                {{ ' ' }}
                                                <time :datetime="item.last_synced">{{ moment(item.last_synced).utc().fromNow() }}</time>
                                            </p>
                                            <p class="mt-2 flex items-center text-sm text-gray-500">
                                                <CheckCircleIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-teal-400" aria-hidden="true" />
                                                Connected to {{ item.destination_name + ' (' + item.destination_account + ')' }}
                                            </p>
                                        </div>
                                        <div v-else class="flex mt-1 items-center">
                                            <button @click.stop="this.$emit('actionButtonClicked', item)" class="flex w-24 items-center justify-center rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Activate</button>
                                        </div>
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
                <EmptyState title="No sources connected" subtitle="Get started by adding a source to your Synci.io account." action="Add Source" />
            </div>
        </ul>
    </div>
</template>
