
<script>
import {ref} from "vue";
import { RadioGroup, RadioGroupDescription, RadioGroupLabel, RadioGroupOption } from '@headlessui/vue'
export default {
    components: {RadioGroupDescription, RadioGroupOption, RadioGroupLabel, RadioGroup},
    props: {
        items: {
            type: Array,
            required: true
        },
    },
    data(){
        return {
            selected: ref(this.items[0])
        }
    },
    mounted() {
        this.$emit('stackedCardSelected', this.selected);
    }
}
</script>

<template>
    <RadioGroup v-model="selected">
        <RadioGroupLabel class="sr-only"> Server size </RadioGroupLabel>
        <div class="space-y-4">
            <RadioGroupOption as="template" v-for="item in items" :key="item.id" :value="item" v-slot="{ checked, active }" @click="this.$emit('stackedCardSelected', selected);">
                <div :class="[checked ? 'border-transparent' : 'border-gray-300', active ? 'border-indigo-500 ring-2 ring-indigo-500' : '', 'relative block cursor-pointer rounded-lg border bg-white px-6 py-4 shadow-sm focus:outline-none sm:flex sm:justify-between']">
                      <span class="flex items-center">
                        <span class="flex min-w-0 flex-1 items-center text-sm">
                            <div class="flex-shrink-0">
                                <img class="h-9 w-9 rounded-full" :src="item.icon"  />
                            </div>
                            <div class="min-w-0 flex-1 px-4 grid">
                              <RadioGroupLabel as="span" class="font-medium text-gray-900">{{ item.title }}</RadioGroupLabel>
                              <RadioGroupDescription as="span" class="text-gray-500">
                                <span class="block sm:inline">{{ item.subtitle }}</span>
                              </RadioGroupDescription>
                            </div>
                        </span>
                      </span>
                    <span :class="[active ? 'border' : 'border-2', checked ? 'border-indigo-500' : 'border-transparent', 'pointer-events-none absolute -inset-px rounded-lg']" aria-hidden="true" />
                </div>
            </RadioGroupOption>
        </div>
    </RadioGroup>
</template>
