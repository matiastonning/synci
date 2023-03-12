<template>
    <footer class="bg-gray-900" aria-labelledby="footer-heading">
        <h2 id="footer-heading" class="sr-only">Footer</h2>
        <div class="mx-auto max-w-7xl px-6 pb-8 pt-16 sm:pt-24 lg:px-8 lg:pt-32">
            <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                <img class="h-7" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=500" alt="Company name" />
                <div class="mt-16 grid grid-cols-2 gap-8 xl:col-span-2 xl:mt-0">
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold leading-6 text-white">Features</h3>
                            <ul role="list" class="mt-6 space-y-4">
                                <li v-for="item in footerNavigation.features" :key="item.name">
                                    <Link :href="item.href" class="text-sm leading-6 text-gray-300 hover:text-white">{{ item.name }}</Link>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-10 md:mt-0">
                            <h3 class="text-sm font-semibold leading-6 text-white">Support</h3>
                            <ul role="list" class="mt-6 space-y-4">
                                <li v-for="item in footerNavigation.support" :key="item.name">
                                    <Link :href="item.href" class="text-sm leading-6 text-gray-300 hover:text-white">{{ item.name }}</Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold leading-6 text-white">Company</h3>
                            <ul role="list" class="mt-6 space-y-4">
                                <li v-for="item in footerNavigation.company" :key="item.name">
                                    <Link :href="item.href" class="text-sm leading-6 text-gray-300 hover:text-white">{{ item.name }}</Link>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-10 md:mt-0">
                            <h3 class="text-sm font-semibold leading-6 text-white">Legal</h3>
                            <ul role="list" class="mt-6 space-y-4">
                                <li v-for="item in footerNavigation.legal" :key="item.name">
                                    <Link :href="item.href" class="text-sm leading-6 text-gray-300 hover:text-white">{{ item.name }}</Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="false" class="mt-16 border-t border-white/10 pt-8 sm:mt-20 lg:mt-24 lg:flex lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-sm font-semibold leading-6 text-white">Subscribe to our newsletter</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-300">The latest news, articles, and resources, sent to your inbox weekly.</p>
                </div>
                <form class="mt-6 sm:flex sm:max-w-md lg:mt-0">
                    <label for="email-address" class="sr-only">Email address</label>
                    <input type="email" name="email-address" id="email-address" autocomplete="email" required="" class="w-full min-w-0 appearance-none rounded-md border-0 bg-white/5 px-3 py-1.5 text-base text-white shadow-sm ring-1 ring-inset ring-white/10 placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:w-56 sm:text-sm sm:leading-6" placeholder="Enter your email" />
                    <div class="mt-4 sm:mt-0 sm:ml-4 sm:flex-shrink-0">
                        <button type="submit" class="flex w-full items-center justify-center rounded-md bg-indigo-500 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Subscribe</button>
                    </div>
                </form>
            </div>
            <div class="mt-8 border-t border-white/10 pt-8 md:flex md:items-center md:justify-between">
                <div class="flex space-x-6 md:order-2">
                    <a v-for="item in footerNavigation.social" :key="item.name" :href="item.href" class="text-gray-500 hover:text-gray-400">
                        <span class="sr-only">{{ item.name }}</span>
                        <component :is="item.icon" class="h-6 w-6" aria-hidden="true" />
                    </a>
                </div>
                <p class="mt-8 text-xs leading-5 text-gray-400 md:order-1 md:mt-0">&copy; 2023 Synci.io. All rights reserved.</p>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { defineComponent, h, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Dialog, DialogPanel } from '@headlessui/vue'
import { Bars3Icon, BuildingOffice2Icon, EnvelopeIcon, PhoneIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const navigation = [
    { name: 'Home', href: '#home' },
    { name: 'Features', href: '#features' },
    { name: 'Pricing', href: '#pricing' },
    { name: 'Company', href: '#footer' },
]

const mobileMenuOpen = ref(false);

const footerNavigation = {
    features: [
        { name: 'Feature #1', href: '#' },
        { name: 'Feature #2', href: '#' },
        { name: 'Feature #3', href: '#' },
    ],
    support: [
        { name: 'Pricing', href: '#' },
        { name: 'Documentation', href: '#' },
        { name: 'Guides', href: '#' },
    ],
    company: [
        { name: 'About', href: '#' },
        { name: 'Partners', href: '#' },
    ],
    legal: [
        { name: 'Terms of Service', href: route('terms') },
        { name: 'Privacy Policy', href: route('privacy') },
        { name: 'Cookie Policy', href: route('cookies') },
        { name: 'Acceptable Use', href: route('acceptable-use') },
    ],
    social: [
        {
            name: 'Twitter',
            href: 'https://twitter.com/synciio',
            icon: defineComponent({
                render: () =>
                    h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
                        h('path', {
                            d: 'M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84',
                        }),
                    ]),
            }),
        },
    ],
}
</script>

<script>
export default {
    name: "Navbar"
}
</script>

<style scoped>

</style>
