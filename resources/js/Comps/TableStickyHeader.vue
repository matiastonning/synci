<script setup>
import Paginator from "./Paginator.vue";
import EmptyState from "./EmptyState.vue";

</script>

<script>
export default {
    emits: ['actionClicked'],
    props: {
        rows: {
            type: Array,
            default: []
        },
        titles: {
            type: Array,
            default: []
        },
        paginationLinks: {
            type: Object,
            default: {}
        }
    }
}
</script>

<template>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flow-root">
            <div class="-my-2 -mx-4 sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle">
                    <table class="min-w-full border-separate border-spacing-0">
                        <thead>
                        <tr>
                            <th v-for="(val, index) in titles" scope="col"
                                class="sticky top-0 z-10 border-b border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 bg-opacity-75 dark:bg-opacity-60 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white backdrop-blur backdrop-filter"
                                :class="[{
                                    'lg:pl-8 pl-4 pr-3 rounded-tl-lg': index === 0, // first item
                                    'px-3': index !== 0, // other items
                                }, val.class]">
                                {{ val.name }}
                            </th>

                            <th scope="col" class="sticky rounded-tr-lg top-0 z-10 border-b border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 bg-opacity-75 dark:bg-opacity-60 py-3.5 pr-4 pl-3 backdrop-blur backdrop-filter sm:pr-6 lg:pr-8">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody v-if="rows.length > 0">
                            <tr v-for="(row, rowIndex) in rows" :key="row.id" :id="row.id">
                                <td v-for="(item, itemIndex) in row.items" class="whitespace-nowrap py-4 text-sm text-gray-500 dark:text-gray-100"
                                    :class="[{
                                        'lg:pl-8 pl-4 pr-3 font-medium text-gray-900': itemIndex === 0, // first item
                                        'px-3': itemIndex !== row.items.length - 1 && itemIndex !== 0, // middle items
                                        'border-b border-gray-200 dark:border-gray-700 dark:border-opacity-80': rowIndex !== rows.length - 1, // not last item
                                    }, item.class]">
                                    <span v-html="item.value"></span>
                                </td>
                                <td :class="[rowIndex !== rows.length - 1 ? 'border-b border-gray-200 dark:border-gray-700 dark:border-opacity-80' : '', 'relative whitespace-nowrap rounded-tr-lg py-4 pr-4 pl-3 text-right text-sm font-medium sm:pr-8 lg:pr-8']">
                                    <button is="link" @click="this.$emit('actionClicked', row)" class="text-teal-600 hover:text-teal-900 dark:text-teal-500 dark:hover:text-teal-600">{{ row.action.text }}</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <EmptyState v-if="rows.length === 0" class="mx-auto my-20 mx-10" :action-href="route('connect.sources')" action="Add Source" title="No transactions found" subtitle="Add a source to start syncing transactions." />
                </div>
            </div>
        </div>
    </div>
    <Paginator v-if="paginationLinks" :links="paginationLinks.links"
    :showing-from="paginationLinks.from" :showing-to="paginationLinks.to" :showing-total="paginationLinks.total"
    results-label="transactions" />
</template>
