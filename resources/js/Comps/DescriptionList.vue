<script setup>
import moment from "moment";
</script>

<script>
export default {
    props: {
        selectedItem: {
            type: Object,
            default: []
        },
        sourceMode: {
            type: Boolean,
            default: false
        },
        selectedId: {
            type: [String, Number],
            default: null
        }
    }
}

</script>

<template>
    <div>
            <dl class="sm:divide-y sm:divide-gray-200 dark:sm:divide-gray-700">
                <div v-for="(item, key) in selectedItem" class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ key }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2 sm:mt-0">
                        <span v-html="item"></span>
                        <button type="button" v-if="sourceMode === true & key === 'Status'" @click="item === 'Inactive' ? this.$emit('openActivateSourceModal') : this.$emit('deactivateSource', selectedId)" :class="item === 'Inactive' ? 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50 shadow-sm dark:border-transparent dark:bg-gray-500 dark:bg-opacity-[25%] dark:text-white dark:hover:bg-opacity-[12%]' : 'border-transparent bg-red-100 text-red-700 hover:bg-red-700 dark:bg-red-600 dark:text-red-300 dark:hover:bg-red-500 dark:bg-opacity-40 dark:hover:bg-opacity-40 focus:ring-0'" class="mt-3 flex w-24 items-center justify-center rounded-full border px-4 py-2 text-sm font-medium">
                            {{ item === 'Inactive' ? 'Activate' : 'Deactivate' }}
                        </button>
                    </dd>
                </div>
            </dl>
    </div>
</template>
