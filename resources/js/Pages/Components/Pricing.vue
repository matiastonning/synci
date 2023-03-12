<template>
    <div class="bg-gray-900 sm:pt-52 pt-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <h2 class="text-base font-semibold leading-7 text-teal-400">Pricing</h2>
                <p class="mt-2 text-4xl font-bold tracking-tight text-white sm:text-5xl">Pricing plans for every need</p>
            </div>
            <p class="mx-auto mt-6 max-w-2xl text-center text-lg leading-8 text-gray-300">Choose an affordable plan that’s packed with the features you need, at a price that fits your budget.</p>
            <div class="mt-16 flex justify-center">
                <RadioGroup v-model="frequency" class="grid grid-cols-2 gap-x-1 rounded-full bg-white/5 p-1 text-center text-xs font-semibold leading-5 text-white">
                    <RadioGroupLabel class="sr-only">Payment frequency</RadioGroupLabel>
                    <RadioGroupOption as="template" v-for="option in frequencies" :key="option.value" :value="option" v-slot="{ checked }">
                        <div :class="[checked ? 'bg-teal-500' : '', 'cursor-pointer rounded-full py-1 px-2.5']">
                            <span>{{ option.label }}</span>
                        </div>
                    </RadioGroupOption>
                </RadioGroup>
            </div>
            <div class="isolate mx-auto mt-10 grid max-w-md grid-cols-1 gap-8 lg:mx-0 lg:max-w-none lg:grid-cols-3">
                <div v-for="tier in tiers" :key="tier.id" :class="[tier.mostPopular ? 'bg-white/5 ring-2 ring-teal-500' : 'ring-1 ring-white/10', 'rounded-3xl p-8 xl:p-10']">
                    <div class="flex items-center justify-between gap-x-4">
                        <h3 :id="tier.id" class="text-lg font-semibold leading-8 text-white">{{ tier.name }}</h3>
                        <p v-if="tier.mostPopular" class="rounded-full bg-teal-500 py-1 px-2.5 text-xs font-semibold leading-5 text-white">Recommended</p>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-gray-300">{{ tier.description }}</p>
                    <p class="mt-6 flex items-baseline gap-x-1">
                        <span class="text-4xl font-bold tracking-tight text-white">{{ tier.price[frequency.value] }}</span>
                        <span class="text-sm font-semibold leading-6 text-gray-300">{{ frequency.priceSuffix }}</span>
                    </p>
                    <a :href="tier.href" :aria-describedby="tier.id" :class="[tier.mostPopular ? 'bg-teal-500 text-white shadow-sm hover:bg-teal-400 focus-visible:outline-teal-500' : 'bg-white/10 text-white hover:bg-white/20 focus-visible:outline-white', 'mt-6 block rounded-md py-2 px-3 text-center text-sm font-semibold leading-6 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2']">Start free trial</a>
                    <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-gray-300 xl:mt-10">
                        <li v-for="feature in tier.features" :key="feature" class="flex gap-x-3">
                            <CheckIcon class="h-6 w-5 flex-none text-white" aria-hidden="true" />
                            <span v-html="feature"></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { RadioGroup, RadioGroupLabel, RadioGroupOption } from '@headlessui/vue'
import { CheckIcon } from '@heroicons/vue/20/solid'

const frequencies = [
    { value: 'monthly', label: 'Monthly', priceSuffix: '/month' },
    { value: 'annually', label: 'Annually', priceSuffix: '/year' },
]
const tiers = [
    {
        name: 'Starter',
        id: 'tier-starter',
        href: '#',
        price: { monthly: '$3', annually: '$30' },
        description: 'Everything you need to get started, if you only use one bank.',
        features: [
            '<b>1 bank connection</b>',
            'Unlimited budget connections',
            'Automatic transfer',
            'Detailed logging',
            'Transfer-only mode'
        ],
        mostPopular: false,
    },
    {
        name: 'Duo',
        id: 'tier-duo',
        href: '#',
        price: { monthly: '$5', annually: '$50' },
        description: 'If you use two separate banks, this plan is made for you.',
        features: [
            '<b>2 bank connections</b>',
            'Unlimited budget connections',
            'Automatic transfer',
            'Detailed logging',
            'Transfer-only mode'
        ],
        mostPopular: true,
    },
    {
        name: 'Penta',
        id: 'tier-penta',
        href: '#',
        price: { monthly: '$10', annually: '$100' },
        description: "Mo' banks, mo' problems. This plan is for those who need broad connectivity.",
        features: [
            '<b>5 bank connections</b>',
            'Unlimited budget connections',
            'Automatic transfer',
            'Detailed logging',
            'Transfer-only mode'
        ],
        mostPopular: false,
    },
]

const frequency = ref(frequencies[0])
</script>
