<script>
export default {
    emits: ['closeNotification'],
    methods: {
        timer: function(callback, delay) {
            let timerId, start, remaining = delay;

            this.pause = function() {
                window.clearTimeout(timerId);
                timerId = null;
                remaining -= Date.now() - start;
            };

            this.resume = function() {
                if (timerId) {
                    return;
                }

                start = Date.now();
                timerId = window.setTimeout(callback, remaining);
            };


            this.resume();
        },
        mouseEnterNotification() {
            this.timer.pause();
        },
        mouseLeaveNotification() {
            this.timer.resume();
        },
    },
    data() {
        return {
            timerFun: this.timer
        }
    },

    created() {
        this.timer = new this.timer(() => {
            this.$emit('closeNotification');
        }, this.timeout);
    },

    props: {
        show: {
            type: Boolean,
            default: false,
        },
        type: {
            type: String, // success, error, warning, info
            default: 'success',
        },
        title: {
            type: String,
            required: true,
        },
        message: {
            type: String,
            required: true,
        },
        timeout: {
            type: Number,
            default: 5000,
        },
    },
}
</script>

<template>
    <!-- Global notification live region, render this permanently at the end of the document -->
    <div aria-live="assertive" class="pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6">
        <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
            <!-- Notification panel, dynamically insert this into the live region when it needs to be displayed -->
            <transition enter-active-class="transform ease-out duration-300 transition" enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" enter-to-class="translate-y-0 opacity-100 sm:translate-x-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div @mouseenter="mouseEnterNotification()" @mouseleave="mouseLeaveNotification()" v-if="show" class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white dark:bg-gray-700 shadow-lg ring-1 ring-black ring-opacity-5">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <CheckCircleIcon v-if="type==='success'" class="h-6 w-6 text-teal-400 dark:text-teal-500" aria-hidden="true" />
                                <ExclamationTriangleIcon v-else-if="type==='error'" class="h-6 w-6 text-red-400 dark:text-red-500" aria-hidden="true" />
                                <ExclamationCircleIcon v-else-if="type==='warning'" class="h-6 w-6 text-orange-400 dark:text-orange-500" aria-hidden="true" />
                                <InformationCircleIcon v-else-if="type==='info'" class="h-6 w-6 text-indigo-400" aria-hidden="true" />
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ title }}</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ message }}</p>
                            </div>
                            <div class="ml-4 flex flex-shrink-0">
                                <button type="button" @click="this.$emit('closeNotification')" class="inline-flex rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <span class="sr-only">Close</span>
                                    <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script setup>
import { CheckCircleIcon, ExclamationCircleIcon, ExclamationTriangleIcon, InformationCircleIcon } from '@heroicons/vue/24/outline'
import { XMarkIcon } from '@heroicons/vue/20/solid'
</script>
