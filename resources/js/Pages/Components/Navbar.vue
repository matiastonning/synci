<template>
    <header class="absolute inset-x-0 top-0 z-50">
        <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">
            <div class="flex items-center gap-x-12">
                <Link :href="route('home')" class="-m-1.5 p-1.5">
                    <span class="sr-only">Synci.io</span>
                    <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=teal&shade=500" alt="" />
                </Link>
                <div class="hidden lg:flex lg:gap-x-6">
                    <Link v-for="item in navigation" :key="item.name" :href="item.href" class="text-sm hover:bg-gray-700 hover:backdrop-blur hover:bg-opacity-25 rounded-md py-2 px-4 font-semibold leading-6 text-white">{{ item.name }}</Link>
                </div>
            </div>
            <div class="flex lg:hidden">
                <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-400" @click="mobileMenuOpen = true">
                    <span class="sr-only">Open main menu</span>
                    <Bars3Icon class="h-6 w-6" aria-hidden="true" />
                </button>
            </div>
            <div class="hidden lg:flex">
                <Link :href="route('login')" class="text-sm hover:bg-gray-700 hover:backdrop-blur hover:bg-opacity-25 rounded-md py-2 px-4 font-semibold leading-6 text-white">Log in <span aria-hidden="true">&rarr;</span></Link>
            </div>
        </nav>
        <Dialog as="div" class="lg:hidden" @close="mobileMenuOpen = false" :open="mobileMenuOpen">
            <div class="fixed inset-0 z-50" />
            <DialogPanel class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
                <div class="flex items-center justify-between">
                    <Link :href="route('home')" class="-m-1.5 p-1.5">
                        <span class="sr-only">Synci.io</span>
                        <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=teal&shade=600" alt="" />
                    </Link>
                    <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700" @click="mobileMenuOpen = false">
                        <span class="sr-only">Close menu</span>
                        <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                    </button>
                </div>
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/10">
                        <div class="space-y-2 py-6">
                            <Link v-for="item in navigation" :key="item.name" :href="item.href" class="-mx-3 block rounded-lg py-2 px-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-700 hover:backdrop-blur hover:bg-opacity-25 rounded-md py-2 px-4">{{ item.name }}</Link>
                        </div>
                        <div class="py-6">
                            <Link :href="route('login')" class="-mx-3 block rounded-lg py-2.5 px-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-700 hover:backdrop-blur hover:bg-opacity-25 rounded-md py-2 px-4">Log in</Link>
                        </div>
                    </div>
                </div>
            </DialogPanel>
        </Dialog>
    </header>
</template>

<script setup>
import { defineComponent, h, ref } from 'vue'
import { Dialog, DialogPanel } from '@headlessui/vue'
import { Bars3Icon, BuildingOffice2Icon, EnvelopeIcon, PhoneIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { Link } from '@inertiajs/vue3'


const navigation = [
    { name: 'Home', href: route('home') },
    { name: 'Features', href: '#features' },
    { name: 'Pricing', href: '#pricing' },
    { name: 'Company', href: '#footer' },
]

const mobileMenuOpen = ref(false)
</script>

<script>
export default {
    name: "Navbar"
}
</script>

<style scoped>

</style>
