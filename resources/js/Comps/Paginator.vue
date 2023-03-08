<script>
export default {
    props: {
        links: {
            type: Object,
            required: true,
        },
        showingFrom: {
            type: Number,
        },
        showingTo: {
            type: Number,
        },
        showingTotal: {
            type: Number,
        },
        resultsLabel: {
            type: String,
            default: 'results',
        },
    },
}
</script>

<template>
    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-5 sm:px-6 rounded-b-lg">
        <div class="flex flex-1 items-center justify-between">
            <p class="text-sm text-gray-700" v-if="showingFrom !== 0 && showingTo !== 0 && showingTotal !== 0">
                Showing
                {{ ' ' }}
                <span class="font-medium">{{ showingFrom }}</span>
                {{ ' ' }}
                to
                {{ ' ' }}
                <span class="font-medium">{{ showingTo }}</span>
                {{ ' ' }}
                of
                {{ ' ' }}
                <span class="font-medium">{{ showingTotal }}</span>
                {{ ' ' }}
                {{ resultsLabel }}
            </p>
            <p class="text-sm text-gray-700" v-else>
                No {{ resultsLabel }} found
            </p>

            <div>
                <nav class="isolate inline-flex space-x-1 -space-x-px rounded-md" aria-label="Pagination">
                    <Link :class="!links[0].url ? 'pointer-events-none opacity-50' : null" preserveScroll :href="links[0].url ? links[0].url : ''" class="relative rounded-md inline-flex lg:hidden xl:inline-flex items-center rounded-l-md px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20">
                        <span class="sr-only">Previous</span>
                        <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                    </Link>
                    <Link v-for="(link, index) in links" :href="link.url === null ? '#' : link.url" preserveScroll
                       v-show="!link.label.includes('Next') && !link.label.includes('Previous')"
                       class="hidden lg:block relative items-center py-2 px-3.5 text-sm font-medium rounded-md"
                       :class="{
                                    'z-10 bg-teal-500 bg-opacity-10 text-teal-600': link.active, // active
                                    'text-gray-500 hover:bg-gray-50': !link.active, // default
                                    'hidden lg:inline-flex': (links[index-1] && links[index-1].label.includes('...')) || (links[index+1] && links[index+1].label.includes('...')), // one before or after middle item
                                    'inline-flex text-gray-700 pointer-events-none opacity-50': link.label.includes('...'), // ...
                                    'focus:z-20': !link.label.includes('...'), // not ...
                                }"
                    >{{ link.label }}</Link>

                    <Link :class="!links[links.length - 1].url ? 'pointer-events-none opacity-50' : null" preserveScroll :href="links[links.length - 1].url ? links[links.length - 1].url : ''" class="relative rounded-md inline-flex lg:hidden xl:inline-flex items-center rounded-r-md px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20">
                        <span class="sr-only">Next</span>
                        <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                    </Link>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'
import { Link } from '@inertiajs/vue3'

const items = [
    { id: 1, title: 'Back End Developer', department: 'Engineering', type: 'Full-time', location: 'Remote' },
    { id: 2, title: 'Front End Developer', department: 'Engineering', type: 'Full-time', location: 'Remote' },
    { id: 3, title: 'User Interface Designer', department: 'Design', type: 'Full-time', location: 'Remote' },
]
</script>
