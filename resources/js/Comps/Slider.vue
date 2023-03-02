
<script setup>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { LinkIcon, PlusIcon, QuestionMarkCircleIcon } from '@heroicons/vue/20/solid'
import StackedCards from "./StackedCards.vue";
import DescriptionList from "./DescriptionList.vue";
import DeleteModal from "./DeleteModal.vue";
import moment from "moment";

</script>


<script>

import {router, useForm} from "@inertiajs/vue3";

export default {
    emits: ['closeSlider', 'actionClicked'],
    props: {
        open: {
            type: Boolean
        },
        title: {
            type: String,
            required: true
        },
        subtitle: {
            type: String,
            required: true
        },
        headerClass: {
            type: String,
            default: 'bg-gray-200 bg-opacity-60'
        },
        titleClass: {
            type: String,
            default: 'text-gray-900'
        },
        subtitleClass: {
            type: String,
            default: 'text-gray-500'
        },
        xButtonClass: {
            type: String,
            default: 'text-gray-400 hover:text-gray-500'
        },
        actionButton: {
            default: {
                class: 'hover:bg-indigo-700 bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
                text: 'Connect',
            },
            type: Object,
        },
        selectedId: {
            type: [Number, String],
            default: null
        }
    },

    data(){
        return {
        }
    },
    methods: {
        actionClicked(selectedItem){
            this.$emit('closeSlider');
            this.$emit('actionClicked', selectedItem);
        }
    }
}
</script>

<template>
  <TransitionRoot as="template" :show="open">
    <Dialog as="div" class="relative z-10" @close="this.$emit('closeSlider')">

        <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100" leave-to="opacity-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-30 transition-opacity" />
        </TransitionChild>
        <div class="fixed inset-0" />

      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
            <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
              <DialogPanel class="pointer-events-auto w-screen max-w-md my-5">
                <form class="rounded-l-lg flex h-full flex-col divide-y divide-gray-200 bg-opacity-80 bg-white backdrop-blur shadow-xl">
                  <div class="h-0 flex-1 overflow-y-auto">
                    <div :class="headerClass" class="rounded-tl-lg backdrop-blur py-6 px-4 sm:px-6">
                      <div class="flex items-center justify-between">
                        <DialogTitle :class="titleClass" class="text-lg font-medium">{{ title }}</DialogTitle>
                        <div class="ml-3 flex h-7 items-center">
                          <button type="button" :class="xButtonClass" class="rounded-md" @click="this.$emit('closeSlider')">
                            <span class="sr-only">Close panel</span>
                            <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                          </button>
                        </div>
                      </div>
                      <div class="mt-1">
                        <p :class="subtitleClass" class="text-sm">{{ subtitle }}</p>
                      </div>
                    </div>
                    <div class="flex flex-1 flex-col justify-between">

                      <div class="divide-y divide-gray-200 px-4 sm:px-6">
                        <div class="space-y-6 pt-6 pb-5">
                            <slot></slot>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="flex flex-shrink-0 justify-end px-4 py-4">
                      <button type="button" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" @click="this.$emit('closeSlider')">Close</button>
                      <button :class="actionButton.class" @click="actionClicked(selectedId)" type="button" class="ml-4 inline-flex justify-center rounded-md border border-transparent py-2 px-4 text-sm font-medium text-white shadow-sm">
                          {{ actionButton.text }}
                      </button>
                  </div>
                </form>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
