
<script>
    export default {
        props: {
            title: {
                type: String,
                required: true
            },
            subtitle: {
                type: String,
                required: true
            },
            action: {
                type: String,
            },
            actionHref: {
                type: String,
            },
            icon: {
                type: String,
                default: 'inbox'
            }
        }
    }
</script>

<template>
    <div class="text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400 text-opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <InboxIcon v-if="icon === 'inbox'" class="h-6 w-6" aria-hidden="true" />
            <path v-else-if="icon === 'folder-plus'" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ title }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ subtitle }}</p>
        <div v-if="action && actionHref" class="mt-8">
            <Link :href="actionHref" class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 sm:w-auto sm:text-sm">
                <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                {{ action }}
            </Link>
        </div>
    </div>
</template>

<script setup>
import { PlusIcon } from '@heroicons/vue/20/solid'
import { InboxIcon } from '@heroicons/vue/24/outline'
import { Link } from '@inertiajs/vue3';
</script>
