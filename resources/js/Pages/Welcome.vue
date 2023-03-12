<template>
    <Head title="Sync your bank with your budget" />

    <div class="bg-gray-900">
        <!-- Header -->
        <Navbar />

        <main>
            <!-- Hero section -->
            <div class="relative isolate overflow-hidden">
                <svg class="absolute inset-0 -z-10 h-full w-full stroke-white/10 [mask-image:radial-gradient(100%_100%_at_top_right,white,transparent)]" aria-hidden="true">
                    <defs>
                        <pattern id="983e3e4c-de6d-4c3f-8d64-b9761d1534cc" width="200" height="200" x="50%" y="-1" patternUnits="userSpaceOnUse">
                            <path d="M.5 200V.5H200" fill="none" />
                        </pattern>
                    </defs>
                    <svg x="50%" y="-1" class="overflow-visible fill-gray-800/20">
                        <path d="M-200 0h201v201h-201Z M600 0h201v201h-201Z M-400 600h201v201h-201Z M200 800h201v201h-201Z" stroke-width="0" />
                    </svg>
                    <rect width="100%" height="100%" stroke-width="0" fill="url(#983e3e4c-de6d-4c3f-8d64-b9761d1534cc)" />
                </svg>
                <svg viewBox="0 0 1108 632" aria-hidden="true" class="absolute top-10 left-[calc(50%-4rem)] -z-10 w-[69.25rem] max-w-none transform-gpu blur-3xl sm:left-[calc(50%-18rem)] lg:left-48 lg:top-[calc(50%-30rem)] xl:left-[calc(50%-24rem)]">
                    <path fill="url(#175c433f-44f6-4d59-93f0-c5c51ad5566d)" fill-opacity=".2" d="M235.233 402.609 57.541 321.573.83 631.05l234.404-228.441 320.018 145.945c-65.036-115.261-134.286-322.756 109.01-230.655C968.382 433.026 1031 651.247 1092.23 459.36c48.98-153.51-34.51-321.107-82.37-385.717L810.952 324.222 648.261.088 235.233 402.609Z" />
                    <defs>
                        <linearGradient id="175c433f-44f6-4d59-93f0-c5c51ad5566d" x1="1220.59" x2="-85.053" y1="432.766" y2="638.714" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#4F46E5" />
                            <stop offset="1" stop-color="#80CAFF" />
                        </linearGradient>
                    </defs>
                </svg>
                <div class="mx-auto max-w-7xl px-6 pt-10 pb-24 sm:pb-40 lg:flex lg:px-8 lg:pt-40">
                    <div class="mx-auto max-w-2xl flex-shrink-0 lg:mx-0 lg:max-w-xl lg:pt-8">
                        <div class="mt-24 sm:mt-32 lg:mt-16">
                            <a href="https://twitter.com/synciio" target="_blank" class="inline-flex space-x-6">
                                <span class="rounded-full bg-teal-500/10 px-3 py-1 text-sm font-semibold leading-6 text-teal-400 ring-1 ring-inset ring-teal-500/20">Latest updates</span>
                                <span class="inline-flex items-center space-x-2 text-sm font-medium leading-6 text-gray-300">
                  <span>Synci.io open beta released</span>
                  <ChevronRightIcon class="h-5 w-5 text-gray-500" aria-hidden="true" />
                </span>
                            </a>
                        </div>
                        <h1 class="mt-10 text-4xl font-bold tracking-tight text-white sm:text-6xl">Sync your bank with your budget</h1>
                        <p class="mt-6 text-lg leading-8 text-gray-300">Synci.io is the ultimate integration for automatically importing bank transactions to budget-apps like YNAB.</p>
                        <div class="mt-10 flex items-center gap-x-6">
                            <Link href="#" class="relative inline-flex items-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">Start a free 14-day trial</Link>
                            <Link :href="route('dashboard')" class="text-sm font-semibold leading-6 text-white">Dashboard <span aria-hidden="true">→</span></Link>
                        </div>
                    </div>
                    <div class="mx-auto mt-16 flex max-w-2xl sm:mt-24 lg:ml-10 lg:mt-0 lg:mr-0 lg:max-w-none lg:flex-none xl:ml-32">
                        <div class="max-w-3xl flex-none sm:max-w-5xl lg:max-w-none">
                            <img src="https://tailwindui.com/img/component-images/dark-project-app-screenshot.png" alt="App screenshot" width="2432" height="1442" class="w-[76rem] rounded-md bg-white/5 shadow-2xl ring-1 ring-white/10" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo cloud -->
            <div class="mx-auto mt-0 max-w-7xl px-6 sm:mt-0 lg:px-8 rounded-xl">
                <h2 class="text-center font-semibold leading-7 text-teal-400">Works with</h2>
                <div class="mx-auto mt-10 grid max-w-lg grid-cols-4 items-center gap-x-8 gap-y-10 sm:max-w-xl sm:grid-cols-6 sm:gap-x-10 lg:mx-0 lg:max-w-none lg:grid-cols-1">
                    <img class="col-span-2 max-h-12 w-full object-contain lg:col-span-1 filter text-white" src="/assets/logos/ynab.svg" alt="YNAB" width="158" height="48" />
                </div>
            </div>

            <!-- Feature section -->
            <div v-if="false" class="mx-auto mt-32 max-w-7xl sm:mt-56 sm:px-6 lg:px-8">
                <div class="relative isolate overflow-hidden bg-gray-800 bg-opacity-40 py-20 px-6 sm:rounded-3xl sm:py-24 sm:px-10 lg:py-24 xl:px-24">
                    <div class="mx-auto grid max-w-2xl grid-cols-1 gap-y-16 gap-x-8 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-2 lg:items-center lg:gap-y-0">
                        <div class="lg:row-start-2 lg:max-w-md">
                            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Boost your productivity.<br />Start using our app today.</h2>
                            <p class="mt-6 text-lg leading-8 text-gray-300">Ac euismod vel sit maecenas id pellentesque eu sed consectetur. Malesuada adipiscing sagittis vel nulla. Ac euismod vel sit maecenas.</p>
                        </div>
                        <img src="https://tailwindui.com/img/component-images/dark-project-app-screenshot.png" alt="Product screenshot" class="relative -z-20 min-w-full max-w-xl rounded-xl shadow-xl ring-1 ring-white/10 lg:row-span-4 lg:w-[64rem] lg:max-w-none" width="2432" height="1442" />
                        <div class="max-w-xl lg:row-start-3 lg:mt-10 lg:max-w-md lg:border-t lg:border-white/10 lg:pt-10">
                            <dl class="max-w-xl space-y-8 text-base leading-7 text-gray-300 lg:max-w-none">
                                <div v-for="feature in primaryFeatures" :key="feature.name" class="relative">
                                    <dt class="ml-9 inline-block font-semibold text-white">
                                        <component :is="feature.icon" class="absolute top-1 left-1 h-5 w-5 text-indigo-500" aria-hidden="true" />
                                        {{ feature.name }}
                                    </dt>
                                    {{ ' ' }}
                                    <dd class="inline">{{ feature.description }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    <svg class="pointer-events-none absolute top-1/2 left-12 -z-10 h-[42.375rem] -translate-y-1/2 transform-gpu blur-3xl lg:top-auto lg:bottom-[-12rem] lg:translate-y-0" viewBox="0 0 1155 678" fill="none">
                        <path fill="url(#c0458c57-1330-459f-9d5c-f0d75c210466)" fill-opacity=".25" d="M317.219 518.975L203.852 678 0 438.341l317.219 80.634 204.172-286.402c1.307 132.337 45.083 346.658 209.733 145.248C936.936 126.058 882.053-94.234 1031.02 41.331c119.18 108.451 130.68 295.337 121.53 375.223L855 299l21.173 362.054-558.954-142.079z" />
                        <defs>
                            <linearGradient id="c0458c57-1330-459f-9d5c-f0d75c210466" x1="1155.49" x2="-78.208" y1=".177" y2="474.645" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#9089FC" />
                                <stop offset="1" stop-color="#FF80B5" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>

            <!-- Feature section -->
            <div v-if="false" class="mx-auto mt-32 max-w-7xl px-6 sm:mt-56 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-base font-semibold leading-7 text-teal-400">Stay up-to-date</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">Everything you need to stay in control</p>
                    <p class="mt-6 text-lg leading-8 text-gray-300">Synci.io is built for users who like to stay in control of their data. Stay up to date with your bank data, without doing any of the heavy lifting.</p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                        <div v-for="feature in primaryFeatures" :key="feature.name" class="flex flex-col">
                            <dt class="text-base font-semibold leading-7 text-white">
                                <div class="mb-6 flex h-10 w-10 items-center justify-center rounded-lg bg-teal-500">
                                    <component :is="feature.icon" class="h-6 w-6 text-white" aria-hidden="true" />
                                </div>
                                {{ feature.name }}
                            </dt>
                            <dd class="mt-1 flex flex-auto flex-col text-base leading-7 text-gray-300">
                                <p class="flex-auto">{{ feature.description }}</p>
                                <p class="mt-6">
                                    <a :href="feature.href" class="text-sm font-semibold leading-6 text-teal-400">Learn more <span aria-hidden="true">→</span></a>
                                </p>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Feature section -->
            <div class="mt-32 sm:mt-32">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl sm:text-center">
                        <h2 class="text-base font-semibold leading-7 text-teal-400">Everything you need</h2>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">Effortless Control</p>
                        <p class="mt-6 text-lg leading-8 text-gray-300">Synci is built for users who like to stay in control of their data. Stay up to date with your bank accounts, without doing any of the heavy lifting.</p>
                    </div>
                </div>
                <div class="relative overflow-hidden pt-16">
                    <div class="mx-auto max-w-7xl px-6 lg:px-8">
                        <img src="https://tailwindui.com/img/component-images/dark-project-app-screenshot.png" alt="App screenshot" class="mb-[-12%] rounded-xl shadow-2xl ring-1 ring-white/10" width="2432" height="1442" />
                        <div class="relative" aria-hidden="true">
                            <div class="absolute -inset-x-20 bottom-0 bg-gradient-to-t from-gray-900 pt-[7%]" />
                        </div>
                    </div>
                </div>
                <div class="mx-auto mt-16 max-w-7xl px-6 sm:mt-20 md:mt-24 lg:px-8">
                    <dl class="mx-auto grid max-w-2xl grid-cols-1 gap-x-6 gap-y-10 text-base leading-7 text-gray-300 sm:grid-cols-2 lg:mx-0 lg:max-w-none lg:grid-cols-3 lg:gap-x-8 lg:gap-y-16">
                        <div v-for="feature in secondaryFeatures" :key="feature.name" class="relative pl-9">
                            <dt class="inline font-semibold text-white">
                                <component :is="feature.icon" class="absolute top-1 left-1 h-5 w-5 text-teal-500" aria-hidden="true" />
                                {{ feature.name }}
                            </dt>
                            {{ ' ' }}
                            <dd class="inline">{{ feature.description }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Pricing section -->
            <Pricing />

            <!-- Stats -->
            <div v-if="false" class="mx-auto mt-32 max-w-7xl px-6 sm:mt-56 lg:px-8">
                <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-xl">
                    <h2 class="text-base font-semibold leading-8 text-teal-400">Our track record</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">Trusted by thousands of developers&nbsp;worldwide</p>
                    <p class="mt-6 text-lg leading-8 text-gray-300">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Maiores impedit perferendis suscipit eaque, iste dolor cupiditate blanditiis ratione.</p>
                </div>
                <dl class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-10 text-white sm:mt-20 sm:grid-cols-2 sm:gap-y-16 lg:mx-0 lg:max-w-none lg:grid-cols-4">
                    <div v-for="stat in stats" :key="stat.id" class="flex flex-col gap-y-3 border-l border-white/10 pl-6">
                        <dt class="text-sm leading-6">{{ stat.name }}</dt>
                        <dd class="order-first text-3xl font-semibold tracking-tight">{{ stat.value }}</dd>
                    </div>
                </dl>
            </div>

            <!-- CTA section -->
            <div class="relative isolate mt-20 px-6 py-32 sm:py-40 lg:px-8">
                <svg class="absolute inset-0 -z-10 h-full w-full stroke-white/10 [mask-image:radial-gradient(100%_100%_at_top_right,white,transparent)]" aria-hidden="true">
                    <defs>
                        <pattern id="1d4240dd-898f-445f-932d-e2872fd12de3" width="200" height="200" x="50%" y="0" patternUnits="userSpaceOnUse">
                            <path d="M.5 200V.5H200" fill="none" />
                        </pattern>
                    </defs>
                    <svg x="50%" y="0" class="overflow-visible fill-gray-800/20">
                        <path d="M-200 0h201v201h-201Z M600 0h201v201h-201Z M-400 600h201v201h-201Z M200 800h201v201h-201Z" stroke-width="0" />
                    </svg>
                    <rect width="100%" height="100%" stroke-width="0" fill="url(#1d4240dd-898f-445f-932d-e2872fd12de3)" />
                </svg>
                <div class="absolute inset-x-0 top-10 -z-10 flex transform-gpu justify-center overflow-hidden blur-3xl">
                    <svg viewBox="0 0 1108 632" aria-hidden="true" class="w-[69.25rem] flex-none">
                        <path fill="url(#175c433f-44f6-4d59-93f0-c5c51ad5566d)" fill-opacity=".2" d="M235.233 402.609 57.541 321.573.83 631.05l234.404-228.441 320.018 145.945c-65.036-115.261-134.286-322.756 109.01-230.655C968.382 433.026 1031 651.247 1092.23 459.36c48.98-153.51-34.51-321.107-82.37-385.717L810.952 324.222 648.261.088 235.233 402.609Z" />
                        <defs>
                            <linearGradient id="175c433f-44f6-4d59-93f0-c5c51ad5566d" x1="1220.59" x2="-85.053" y1="432.766" y2="638.714" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#4F46E5" />
                                <stop offset="1" stop-color="#80CAFF" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Get connected.<br />Focus on actual budgeting.</h2>
                    <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-gray-300">Tired of the tedious task of manually importing your bank transactions into your budgeting app? Say hello to Synci.io! Just sign up for the free 14-day trial, connect your bank account, and let Synci do the heavy lifting. With more time and energy freed up, you can focus on the fun part of budgeting - planning your next adventure or treating yourself to something special!</p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <a href="#" class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Get started</a>
                        <a v-if="false" href="#" class="text-sm font-semibold leading-6 text-white">Learn more <span aria-hidden="true">→</span></a>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <Footer />
    </div>
</template>

<script setup>
import { defineComponent, h } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowPathIcon,
    ChevronRightIcon,
    CloudArrowUpIcon,
    Cog6ToothIcon,
    FingerPrintIcon,
    LockClosedIcon,
    ServerIcon,
} from '@heroicons/vue/20/solid'
import { BoltIcon, CalendarDaysIcon, UsersIcon } from '@heroicons/vue/24/outline'
import Pricing from "./Components/Pricing.vue";
import Navbar from "./Components/Navbar.vue";
import Footer from "./Components/Footer.vue";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const primaryFeatures = [
    {
        name: 'Server monitoring',
        description:
            'Non quo aperiam repellendus quas est est. Eos aut dolore aut ut sit nesciunt. Ex tempora quia. Sit nobis consequatur dolores incidunt.',
        href: '#',
        icon: BoltIcon,
    },
    {
        name: 'Collaborate',
        description:
            'Vero eum voluptatem aliquid nostrum voluptatem. Vitae esse natus. Earum nihil deserunt eos quasi cupiditate. A inventore et molestiae natus.',
        href: '#',
        icon: UsersIcon,
    },
    {
        name: 'Task scheduling',
        description:
            'Et quod quaerat dolorem quaerat architecto aliquam accusantium. Ex adipisci et doloremque autem quia quam. Quis eos molestiae at iure impedit.',
        href: '#',
        icon: CalendarDaysIcon,
    },
]
const secondaryFeatures = [
    {
        name: 'Supports over 3400 banks.',
        description: 'Synci.io is integrated with Tink, supporting over 3400 banks and financial institutions across 18 countries in Europe.',
        icon: CloudArrowUpIcon,
    },
    {
        name: 'Strong security.',
        description: 'We use secure Open Banking solutions to fetch data, and all keys and tokens are encrypted using the AES-256-CBC cipher.',
        icon: LockClosedIcon,
    },
    {
        name: 'Completely automatic.',
        description: 'Synci.io automatically transfers transactions from your bank to your budget. We also assign a payee name to each transaction, making it even easier to categorize transactions.',
        icon: ArrowPathIcon,
    },
    {
        name: 'Privacy focused.',
        description: 'Enable "transfer-only mode" to automatically delete transactions from Synci, once they have been transferred. Additionally, we do not share any of your data with undisclosed third parties.',
        icon: FingerPrintIcon,
    },
    {
        name: 'Extensive logging.',
        description: 'Synci.io logs everything that happens with each of your transactions if "transfer-only mode" is turned off, giving you full control of the data-flow.',
        icon: Cog6ToothIcon,
    },
    {
        name: 'Easy setup.',
        description: 'Setting up new budget or bank connection is quick and easy. Barely an inconvenience.',
        icon: ServerIcon,
    },
]
const stats = [
    { id: 1, name: 'Developers on the platform', value: '8,000+' },
    { id: 2, name: 'Daily requests', value: '900m+' },
    { id: 3, name: 'Uptime guarantee', value: '99.9%' },
    { id: 4, name: 'Projects deployed', value: '12m' },
]
</script>
