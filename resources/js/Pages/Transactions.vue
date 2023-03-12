<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import TableStickyHeader from "../Comps/TableStickyHeader.vue";
import Slider from "../Comps/Slider.vue";
import DescriptionList from "../Comps/DescriptionList.vue";
import SimpleFeed from "../Comps/SimpleFeed.vue";
</script>

<script>
import moment from "moment";
import {ExclamationCircleIcon, CheckIcon, ExclamationTriangleIcon, ArrowsRightLeftIcon} from "@heroicons/vue/24/outline";

export default {
    props: {
        transactions: {
            type: Object,
            default: {},
        },
        transactionStatuses: {
            type: Object,
            default: {}
        },
        transferLogStatuses: {
            type: Object,
            default: {}
        },
        sources: {
            type: Object,
            default: {}
        }
    },
    data() {
        return {
            transactionLinks: {},
            transactionItems: [],
            sliderOpen: false,
            selectedItem: {},
            selectedItemLogs: [],
            selectedItemTitle: '',
            transactionTitles: [
                { name: 'Description', class: '' },
                { name: 'Amount', class: '' },
                { name: 'Booking Date', class: 'hidden sm:table-cell md:hidden lg:table-cell' },
                { name: 'Source', class: 'hidden xl:table-cell' },
                { name: 'Status', class: 'hidden lg:table-cell' },
            ]
        }
    },
    methods: {
        sliderActionClicked(selectedItemId) {
            //TODO: Implement retry
            console.log('Retry clicked for ' + selectedItemId);
        },
        tableActionClicked(event) {
            // transaction details
            const transaction = this.transactions.data.find(transaction => transaction.uuid === event.id);
            const transactionLogs = transaction.transfer_log;

            this.selectedItemTitle = transaction.description_short ? transaction.description_short : '';

            const details = transaction.description_long ? transaction.description_long : '';
            const amount = transaction.amount && transaction.currency ? transaction.amount + ' ' + transaction.currency : '';
            const date = transaction.booking_date ? moment(transaction.booking_date).utc().format('MMM Do, YYYY') : '';
            const sourceName = transaction.source.name ? transaction.source.name : '';
            const sourceIdentifier = transaction.source.identifier ? transaction.source.identifier : '';
            const status = event.items[4] ? event.items[4].value : '';
            const id = transaction.uuid ? transaction.uuid : '';

            this.selectedItem = {
                'Description': details,
                'Amount': amount,
                'Booking Date': date,
                'Source': sourceName + ' (' + sourceIdentifier + ')',
                'Status': '<span class="-ml-4">'+ status + '</span>',
                'ID': '<span class="text-xs text-gray-500 dark:text-gray-400">'+ id + '</span>',
            };

            this.selectedItemLogs = [];
            transactionLogs.forEach(transactionLog => {
                let iconBackground = 'bg-gray-200 dark:bg-gray-700';
                let iconColor = 'text-gray-600 dark:text-gray-400';
                let icon = ArrowsRightLeftIcon;

                if(transactionLog.status_code === this.transferLogStatuses['SUCCESS']) {
                    iconBackground = 'bg-teal-200 dark:bg-teal-800';
                    iconColor = 'text-teal-700 dark:text-teal-300';
                    icon = CheckIcon;
                } else if(transactionLog.status_code === this.transferLogStatuses['WARNING']) {
                    iconBackground = 'bg-orange-200 dark:bg-orange-600 dark:bg-opacity-40';
                    iconColor = 'text-orange-700 dark:text-orange-300';
                    icon = ExclamationTriangleIcon;
                } else if(transactionLog.status_code === this.transferLogStatuses['ERROR']) {
                    iconBackground = 'bg-red-200 dark:bg-red-600 dark:bg-opacity-40';
                    iconColor = 'text-red-700 dark:text-red-300';
                    icon = ExclamationCircleIcon;
                }

                this.selectedItemLogs.push({
                    'iconBackground': iconBackground,
                    'iconColor': iconColor,
                    'icon': icon,
                    'content': transactionLog.status_message.replace(/"([^"]+)"/g, '<span class="font-semibold text-gray-600 dark:text-gray-200">$1</span>'),
                    'date': moment(transactionLog.created_at).utc().format('MMM D, HH:mm'),
                });
            });
            this.sliderOpen = true;
        },
        sliderClosed() {
            this.sliderOpen = false;
        }
    },
    mounted() {
        this.transactionLinks.links = this.transactions.links;
        this.transactionLinks.per_page = this.transactions.per_page;
        this.transactionLinks.total = this.transactions.total;
        this.transactions.data.forEach(transaction => {
            let transactionStatus = '';
            let transactionClass = '';
            if(transaction.status === this.transactionStatuses['PENDING']) {
                transactionStatus = 'Pending';
                transactionClass = 'bg-black bg-opacity-5 text-gray-800';
            } else if(transaction.status === this.transactionStatuses['TRANSFERRED']) {
                transactionStatus = 'Transferred';
                transactionClass = 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200';
            } else if(transaction.status === this.transactionStatuses['TRANSFERRING']) {
                transactionStatus = 'Transferring';
                transactionClass = 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200';
            } else if(transaction.status === this.transactionStatuses['FAILED_SOME']) {
                transactionStatus = 'Failed';
                transactionClass = 'bg-red-100 text-red-800 dark:bg-red-600 dark:text-red-200 dark:bg-opacity-40';
            } else if(transaction.status === this.transactionStatuses['FAILED_ALL']) {
                transactionStatus = 'Failed';
                transactionClass = 'bg-red-100 text-red-800 dark:bg-red-600 dark:text-red-200 dark:bg-opacity-40';
            } else if(transaction.status === this.transactionStatuses['FAILED_DUPLICATE']) {
                transactionStatus = 'Duplicate';
                transactionClass = 'bg-orange-100 text-orange-800 dark:bg-orange-600 dark:text-orange-200 dark:bg-opacity-30';
            }

            this.transactionItems.push({
                id: transaction.uuid,
                items: [
                    {
                        value: transaction.description_short,
                        class: '',
                    },
                    {
                        value: transaction.amount + ' <span class="opacity-60 hidden sm:inline">' + transaction.currency + '</span>',
                        class: '',
                    },
                    {
                        value: moment(transaction.booking_date).utc().format('MMM Do, YYYY'),
                        class: 'hidden sm:table-cell md:hidden lg:table-cell',
                    },
                    {
                        value: transaction.source.name,
                        class: 'hidden xl:table-cell',
                    },
                    {
                        value: '<span class="pl-3"><span class="inline-flex items-center rounded-full px-3 py-0.5 text-xs font-medium ' + transactionClass + '">'
                            + transactionStatus
                            + '</span></span>',
                        class: 'hidden lg:table-cell',
                    },
                ],
                action: {
                    text: 'Details',
                },
            });

        });
    }
}

</script>

<template>
    <AppLayout title="Transactions" :search-select-items="sources">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Transactions
            </h2>
        </template>

        <div class="md:py-5 py-4">
            <div class="max-w-7xl mx-auto">
                <div class="bg-white dark:bg-gray-800 dark:bg-opacity-80 shadow-xl rounded-lg">
                    <TableStickyHeader @action-clicked="tableActionClicked" :rows="transactionItems" :titles="transactionTitles" :pagination-links="transactions" />
                </div>
            </div>
        </div>
        <Slider :title="selectedItemTitle" subtitle="Transaction details"
                :action-button="{ text: 'Retry', class: 'hover:bg-teal-700 bg-teal-600' }"
                :open="sliderOpen" :selected-id="selectedItem.ID"
                @close-slider="sliderClosed" @action-clicked="(e) => sliderActionClicked(e)">

            <DescriptionList :selected-item="selectedItem" />

            <hr class="dark:opacity-20"/>

            <h2 class="font-medium dark:text-gray-300">Logs</h2>
            <SimpleFeed :events="selectedItemLogs" />

        </Slider>
    </AppLayout>
</template>
