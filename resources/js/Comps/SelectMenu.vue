<script>
import {ref} from "vue";

export default {
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
            selected: ref(this.items[0])
        }
    },
    watch: {
        selected: function(item, oldVal) {
            this.$emit('selectedChanged', item.key);
        }
    }
}
</script>

<template>
    <Listbox as="div" v-model="selected">
        <ListboxLabel class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ label }}</ListboxLabel>
        <div class="relative mt-1">
            <ListboxButton class="relative w-full cursor-default rounded-md border border-gray-300 bg-white dark:bg-opacity-80 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 py-2 pl-3 pr-10 text-left shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 sm:text-sm">
        <span class="flex items-center">
          <img :src="selected.icon" alt="" class="h-6 w-6 flex-shrink-0 rounded-full" />
          <span class="ml-3 block truncate">{{ selected.name }}</span>
        </span>
                <span class="pointer-events-none absolute inset-y-0 right-0 ml-3 flex items-center pr-2">
          <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
        </span>
            </ListboxButton>

            <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <ListboxOptions class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-md bg-white dark:bg-opacity-80 dark:bg-gray-700 dark:backdrop-blur py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                    <ListboxOption as="template" v-for="item in items" :key="item.key" :value="item" v-slot="{ active, selected }">
                        <li :class="[active ? ' bg-gray-100 dark:bg-opacity-10 dark:text-gray-100' : 'text-gray-900 dark:text-gray-300', 'relative rounded-md cursor-default select-none mx-1 py-2 pl-3 pr-9']">
                            <div class="flex items-center">
                                <img :src="item.icon" alt="" class="h-6 w-6 flex-shrink-0 rounded-full" />
                                <span :class="[selected ? 'font-semibold dark:text-gray-100' : 'font-normal', 'ml-3 block truncate']">{{ item.name }}</span>
                            </div>

                            <span v-if="selected" :class="['text-teal-600 dark:text-teal-500 absolute inset-y-0 right-0 flex items-center pr-4']">
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
