<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import {
    Bars3Icon,
    HomeIcon,
    QueueListIcon,
    XMarkIcon,
    BookOpenIcon,
    BuildingLibraryIcon,
} from '@heroicons/vue/24/outline'
import Notification from "../Comps/Notification.vue";
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'

const navigation = [
    { name: 'Dashboard', href: route('dashboard'), icon: HomeIcon, current: route().current('dashboard') },
    { name: 'Transactions', href: route('transactions'), icon: QueueListIcon, current: route().current('transactions') },
    { name: 'Budgets', href: route('connect.budgets'), icon: BookOpenIcon, current: route().current('connect.budgets') },
    { name: 'Sources', href: route('connect.sources'), icon: BuildingLibraryIcon, current: route().current('connect.sources') },
]

const sidebarOpen = ref(false)

defineProps({
    title: String,
    searchSelectItems: {
        type: Object,
        default: {}
    }
});

const logout = () => {
    router.post(route('logout'));
};


</script>

<script>

import {ref} from "vue";

export default {
    data() {
        return {
            message: null,
            searchValue: '',
            sourceValue: ref(null),
        }
    },
    methods: {
        search(searchValue, sourceValue) {
            let data = {
                // set query params if they are not empty
                ...((searchValue !== '' && searchValue !== null) && {search: searchValue}),
                ...((sourceValue !== '' && sourceValue !== null) && {source: sourceValue}),
            };

            router.get(
                this.route(this.route().current()),
                data,
                {
                    replace: true,
                    only: ['transactions'],
                }
            )
        },
        searchInput(event) {
            this.searchValue = event.target.value
        },
        sourceInput(event) {
            const val = event.target.value;

            if(val === 'All Sources' || val === '') {
                this.sourceValue = null;
            } else {
                this.sourceValue = val;
            }
            this.searchEnter();
        },
        searchEnter() {
            this.search(this.searchValue, this.sourceValue);
        },
    },
    mounted() {
        let urlParams = new URLSearchParams(window.location.search);
        this.searchValue = (urlParams.get('search'));
        this.sourceValue = (urlParams.get('source'));

        if(urlParams.get('search') && !urlParams.get('page')) {
            this.$nextTick(() => this.$refs.search_input.focus())
        }

        this.$emitter.on("notify", clientMessage => {
            if(clientMessage) {
                this.message = clientMessage;
            }
            this.message.show = true;
        });

        if(this.$page.props.flash.message) {
            this.message = this.$page.props.flash.message;
            this.message.show = true;
        }
    },
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <TransitionRoot as="template" :show="sidebarOpen">
            <Dialog as="div" class="relative z-40 md:hidden" @close="sidebarOpen = false">
                <TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="transition-opacity ease-linear duration-300" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-500 dark:bg-black bg-opacity-30 dark:bg-opacity-40 transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 z-40 flex">
                    <TransitionChild as="template" enter="transition ease-in-out duration-300 transform" enter-from="-translate-x-full" enter-to="translate-x-0" leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0" leave-to="-translate-x-full">
                        <DialogPanel class="relative flex w-full max-w-xs flex-1 flex-col bg-teal-800 dark:backdrop-blur dark:bg-gray-800/80">
                            <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100" leave-to="opacity-0">
                                <div class="absolute top-0 right-0 -mr-12 pt-2">
                                    <button type="button" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none" @click="sidebarOpen = false">
                                        <span class="sr-only">Close sidebar</span>
                                        <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
                                    </button>
                                </div>
                            </TransitionChild>
                            <div class="h-0 flex-1 overflow-y-auto pt-5 pb-4">
                                <div class="flex flex-shrink-0 items-center px-4">
                                    <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=teal&shade=500" alt="Your Company" />
                                </div>
                                <nav class="mt-5 space-y-1 px-2">
                                    <Link v-for="item in navigation" :key="item.name" :href="item.href" :class="[item.current ? 'bg-white bg-opacity-10 dark:bg-opacity-40 dark:bg-gray-700' : 'opacity-60 hover:bg-black hover:bg-opacity-10 dark:hover:bg-opacity-20', 'group text-white flex items-center px-2 py-3 text-base font-medium rounded-md']">
                                        <component :is="item.icon" class="text-white mr-4 flex-shrink-0 h-5 w-5" aria-hidden="true" />
                                        {{ item.name }}
                                    </Link>
                                </nav>
                            </div>
                            <Menu as="div" class="flex flex-shrink-0 border-t border-white border-opacity-10 p-1.5">
                                <MenuButton class="group block flex-shrink-0 rounded-md w-full" :class="route().current('profile.show') ? 'bg-white bg-opacity-10' : 'hover:bg-white hover:bg-opacity-10 dark:hover:bg-opacity-40 dark:hover:bg-gray-700'">
                                    <div class="flex items-center min-h-[80px] pl-3">
                                        <div>
                                            <img class="inline-block h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" />
                                        </div>
                                        <div class="ml-3 text-left">
                                            <p class="text-base font-medium text-white opacity-90">{{ $page.props.auth.user.name }}</p>
                                            <p class="text-sm font-medium text-white opacity-60">Manage account</p>
                                        </div>
                                    </div>
                                </MenuButton>
                                <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                                    <MenuItems class="absolute bottom-1.5 right-0 left-0 z-10 mx-1.5 mt-1 origin-bottom divide-y divide-white divide-opacity-10 rounded-md dark:bg-gray-700 dark:bg-opacity-60 bg-teal-600 bg-opacity-60 backdrop-blur shadow focus:outline-none">
                                        <div class="py-1 mx-1">
                                            <MenuItem v-slot="{ active }">
                                                <Link :href="route('profile.show')" :class="[active ? 'bg-white bg-opacity-[7.5%] rounded' : '', 'text-white text-opacity-90 block px-4 py-3 text-base font-medium']">Account settings</Link>
                                            </MenuItem>
                                        </div>
                                        <div class="py-1 mx-1">
                                            <MenuItem v-slot="{ active }">
                                                <Link :href="route('logout')" method="POST" :class="[active ? 'bg-white bg-opacity-[7.5%] rounded' : '', 'text-white text-opacity-90 block px-4 py-3 text-base font-medium']">Sign out</Link>
                                            </MenuItem>
                                        </div>
                                    </MenuItems>
                                </transition>
                            </Menu>
                        </DialogPanel>
                    </TransitionChild>
                    <div class="w-14 flex-shrink-0">
                        <!-- Force sidebar to shrink to fit close icon -->
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- Static sidebar for desktop -->
        <div class="hidden md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col md:border-r dark:md:border-gray-700 dark:md:border-opacity-60">
            <!-- Sidebar component, swap this element with another sidebar if you like -->
            <div class="flex min-h-0 flex-1 flex-col bg-teal-800 dark:bg-gray-800/60">
                <div class="flex flex-1 flex-col overflow-y-auto pt-5 pb-4">
                    <div class="flex flex-shrink-0 items-center px-4">
                        <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=teal&shade=500" alt="Your Company" />
                    </div>
                    <nav class="mt-5 flex-1 space-y-1 px-2">
                        <Link v-for="item in navigation" :key="item.name" :href="item.href" :class="[item.current ? 'bg-white bg-opacity-10 dark:bg-opacity-40 dark:bg-gray-700' : 'opacity-60 hover:bg-black hover:bg-opacity-10 dark:hover:bg-opacity-20', 'group text-white flex items-center px-2 py-3 text-sm font-medium rounded-md']">
                            <component :is="item.icon" class="text-white mr-4 flex-shrink-0 h-5 w-5" aria-hidden="true" />
                            {{ item.name }}
                        </Link>
                    </nav>
                </div>
                <Menu as="div" class="flex flex-shrink-0 border-t border-gray-100 border-opacity-10 p-1.5">
                    <MenuButton class="group block flex-shrink-0 rounded-md w-full" :class="route().current('profile.show') ? 'bg-white bg-opacity-10' : 'hover:bg-white hover:bg-opacity-10 dark:hover:bg-opacity-40 dark:hover:bg-gray-700'">
                        <div class="flex items-center min-h-[60px] pl-3">
                            <div>
                                <img class="inline-block h-9 w-9 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" />
                            </div>
                            <div class="ml-3 text-left">
                                <p class="text-sm font-medium text-white opacity-90">{{ $page.props.auth.user.name }}</p>
                                <p class="text-xs font-medium text-white opacity-60">Manage account</p>
                            </div>
                        </div>
                    </MenuButton>
                    <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                        <MenuItems class="absolute bottom-1.5 right-0 left-0 z-10 mx-1.5 mt-1 origin-bottom divide-y divide-white divide-opacity-10 rounded-md bg-teal-600 dark:bg-gray-700 dark:bg-opacity-60 bg-opacity-60 backdrop-blur shadow focus:outline-none">
                            <div class="py-1 mx-1">
                                <MenuItem v-slot="{ active }">
                                    <Link :href="route('profile.show')" :class="[active ? 'bg-white bg-opacity-[7.5%] rounded' : '', 'text-white text-opacity-90 block px-4 py-2.5 text-sm font-medium']">Account settings</Link>
                                </MenuItem>
                            </div>
                            <div class="py-1 mx-1">
                                <MenuItem v-slot="{ active }">
                                    <Link :href="route('logout')" method="POST" :class="[active ? 'bg-white bg-opacity-[7.5%] rounded' : '', 'text-white text-opacity-90 block px-4 py-2.5 text-sm font-medium']">Sign out</Link>
                                </MenuItem>
                            </div>
                        </MenuItems>
                    </transition>
                </Menu>
            </div>
        </div>
        <div class="flex flex-1 flex-col md:pl-64">
            <main class="flex-1">
                <div class="py-6">
                    <div class="mx-auto max-w-7xl px-4 sm:px-8 md:px-12 lg:px-20 flex flex-row">
                        <button type="button" class="md:hidden -ml-0.5 inline-flex h-12 w-12 items-center justify-center rounded-md text-gray-500 hover:text-gray-900 dark:hover:text-gray-300 focus:outline-none" @click="sidebarOpen = true">
                            <span class="sr-only">Open sidebar</span>
                            <Bars3Icon class="h-6 w-6" aria-hidden="true" />
                        </button>
                        <h1 class="text-2xl mt-2 ml-2 mr-6 md:ml-0 font-semibold text-gray-900 dark:text-gray-100">
                            {{ title }}
                        </h1>

                        <!-- Search -->
                        <div v-if="this.route().current() === 'transactions'" class="max-w-sm ml-auto">
                            <div class="flex -space-x-px mt-1">
                                <label for="search" class="sr-only">Search</label>
                                <div class="relative text-gray-400 text-opacity-75">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <MagnifyingGlassIcon class="h-4 w-4" aria-hidden="true" />
                                    </div>
                                    <input ref="search_input" name="search_input" @keyup="searchInput" @keyup.enter="searchEnter" :value="searchValue" id="search" class="block text-sm float-left peer w-full sm:w-[60%] dark:focus:bg-opacity-70 dark:focus:border-opacity-70 border-r-transparent sm:border-opacity-100 dark:sm:border-opacity-60 bg-opacity-70 rounded-md sm:rounded-r-none border border-transparent dark:border-gray-700 dark:border-r-transparent focus:border-gray-300 dark:focus:border-gray-600 focus:border-r-gray-200 focus:shadow-sm bg-gray-200 dark:bg-gray-800 py-2 pl-10 pr-3 sm:border-r-[1px] sm:focus:border-r-[0.5px] leading-5 focus:bg-white dark:focus:bg-gray-700 text-gray-500 focus:text-gray-900 dark:text-gray-400 dark:focus:text-gray-100 dark:focus:text-gray-100 sm:focus:placeholder-gray-500 dark:sm:focus:placeholder-gray-400 focus:outline-none focus:ring-0 sm:text-sm placeholder-gray-400 dark:placeholder-gray-500" placeholder="Search..." type="search" />
                                    <select v-model="sourceValue" @change="sourceInput" ref="source_select" name="source_select" class="bg-gray-200 dark:bg-gray-800 bg-opacity-70 w-[40%] border-l-transparent dark:border-l-transparent border dark:peer-focus:bg-opacity-70 dark:peer-focus:border-opacity-70 border-transparent dark:border-gray-700 leading-5 hidden sm:block peer-focus:border-gray-300 peer-focus:border-l-gray-200 peer-focus:bg-white dark:peer-focus:border-gray-600 dark:peer-focus:border-l-gray-600 dark:peer-focus:bg-gray-700 peer-focus:outline-none peer-focus:ring-0 peer-focus:shadow-sm py-2 pr-9 relative rounded-none rounded-r-md sm:border-l-[0.5px] sm:border-opacity-100 dark:sm:border-opacity-60 sm:border-l-[0.5px] sm:text-sm text-gray-500 dark:text-gray-400 text-right focus:border-transparent dark:focus:border-gray-700 dark:focus:border-opacity-60 focus:outline-none focus:ring-0 focus:z-10">
                                        <option :value="null">All Sources</option>
                                        <option v-for="selectItem in searchSelectItems" :value="selectItem.name">{{ selectItem.name }}</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="mx-auto max-w-7xl px-4 sm:px-8 md:px-12 lg:px-20">
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Client notifications -->
    <Notification
                  :type="message.status"
                  :title="message.statusTitle"
                  :message="message.statusMessage"
                  :show="message.show"
                  @close-notification="message.show = false"
                  v-if="message !== null"
    />
</template>
