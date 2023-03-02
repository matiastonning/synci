<script>
import {ref} from "vue";
import Flag from 'vue-flagpack'

export default {
    components: {
        Flag
    },
    props: {
        items: {
            type: Array,
            required: true
        },
        label: {
            type: String,
            default: 'Select'
        },
    },
    data(){
        return {
            selected: ref(this.items[3])
        }
    }
}
</script>

<template>
    <Flag code="NO" />

    <Listbox as="div" v-model="selected">
        <ListboxLabel class="block text-sm font-medium text-gray-700">{{ label }}</ListboxLabel>
        <div class="relative mt-1">
            <ListboxButton class="relative w-full cursor-default rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-left shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 sm:text-sm">
        <span class="flex items-center">
            <Flag code="NO" :alt="selected.name + 'flag'" class="h-6 w-6 flex-shrink-0 rounded-full" />

          <span class="ml-3 block truncate">{{ selected.name }}</span>
        </span>
                <span class="pointer-events-none absolute inset-y-0 right-0 ml-3 flex items-center pr-2">
          <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
        </span>
            </ListboxButton>

            <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <ListboxOptions class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                    <ListboxOption as="template" v-for="item in items" :key="item.key" :value="item" v-slot="{ active, selected }">
                        <li :class="[active ? 'text-white bg-teal-600' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                            <div class="flex items-center">
                                <Flag :code="item.key" :alt="item.name + 'flag'" class="h-6 w-6 flex-shrink-0 rounded-full" />
                                <span :class="[selected ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']">{{ item.name }}</span>
                            </div>

                            <span v-if="selected" :class="[active ? 'text-white' : 'text-teal-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                <CheckIcon class="h-5 w-5" aria-hidden="true" />
              </span>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>

<script setup>
import { ref } from 'vue'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'
</script>
