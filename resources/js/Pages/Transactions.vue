<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import TableStickyHeader from "../Comps/TableStickyHeader.vue";
import Slider from "../Comps/Slider.vue";
import DescriptionList from "../Comps/DescriptionList.vue";
</script>

<script>
import moment from "moment";

export default {
    props: {
        transactions: {
            type: Object,
            default: {},
        },
        transactionStatuses: {
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
            const description = event.items[0] ? this.removeHtmlTags(event.items[0].value) : '';
            const details = event.items[1] ? this.removeHtmlTags(event.items[1].value) : '';
            const amount = event.items[2] ? this.removeHtmlTags(event.items[2].value) : '';
            const date = event.items[3] ? this.removeHtmlTags(event.items[3].value) : '';
            const sourceName = event.items[4] ? this.removeHtmlTags(event.items[4].value) : '';
            const sourceIdentifier = event.items[5] ? this.removeHtmlTags(event.items[5].value) : '';
            const status = event.items[6] ? this.removeHtmlTags(event.items[6].value) : '';
            const id = event.items[7] ? this.removeHtmlTags(event.items[7].value) : '';

            this.selectedItem = {
                'Description': description,
                'Details': details,
                'Amount': amount,
                'Date': date,
                'Source Name': sourceName,
                'Source Identifier': sourceIdentifier,
                'Status': status,
                'ID': id,
            };

            this.sliderOpen = true;
        },
        sliderClosed() {
            this.sliderOpen = false;
        },
        removeHtmlTags(htmlString) {
            const tmp = document.createElement('DIV');
            tmp.innerHTML = htmlString;
            return tmp.textContent || tmp.innerText || '';
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
                transactionClass = 'bg-gray-100 text-gray-800';
            } else if(transaction.status === this.transactionStatuses['TRANSFERRED']) {
                transactionStatus = 'Transferred';
                transactionClass = 'bg-green-100 text-green-800';
            } else if(transaction.status === this.transactionStatuses['TRANSFERRING']) {
                transactionStatus = 'Transferring';
                transactionClass = 'bg-indigo-100 text-indigo-800';
            } else if(transaction.status === this.transactionStatuses['FAILED_SOME']) {
                transactionStatus = 'Failed';
                transactionClass = 'bg-red-100 text-red-800';
            } else if(transaction.status === this.transactionStatuses['FAILED_ALL']) {
                transactionStatus = 'Failed';
                transactionClass = 'bg-red-100 text-red-800';
            }

            this.transactionItems.push({
                id: transaction.uuid,
                items: [
                    {
                        value: transaction.description_short,
                        class: '',
                    },
                    {
                        value: transaction.description_long,
                        class: 'hidden',
                    },
                    {
                        value: transaction.amount + ' <span class="opacity-60 hidden sm:inline">' + transaction.currency + '</span>',
                        class: '',
                    },
                    {
                        value: moment(transaction.booking_date).format('MMM Do, YYYY'),
                        class: 'hidden sm:table-cell md:hidden lg:table-cell',
                    },
                    {
                        value: transaction.source.name,
                        class: 'hidden xl:table-cell',
                    },
                    {
                        value: transaction.source.identifier,
                        class: 'hidden',
                    },
                    {
                        value: '<span class="inline-flex items-center rounded-full px-3 py-0.5 text-xs font-medium ' + transactionClass + '">'
                            + transactionStatus
                            + '</span>',
                        class: 'hidden lg:table-cell',
                    },
                    {
                        value: transaction.uuid,
                        class: 'hidden',
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
    <AppLayout title="Transactions">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Transactions
            </h2>
        </template>

        <div class="md:py-5 py-4">
            <div class="max-w-7xl mx-auto">
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                    <TableStickyHeader @action-clicked="tableActionClicked" :rows="transactionItems" :titles="transactionTitles" :pagination-links="transactions" />
                </div>
            </div>
        </div>
        <Slider title="Transaction" subtitle="Transaction details"
                :action-button="{ text: 'Retry', class: 'hover:bg-indigo-700 bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2' }"
                :open="sliderOpen" :selected-id="selectedItem.ID"
                @close-slider="sliderClosed" @action-clicked="(e) => sliderActionClicked(e)">

            <DescriptionList :selected-item="selectedItem" />

        </Slider>
    </AppLayout>
</template>
