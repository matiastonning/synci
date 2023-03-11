<script>

export default {
    props: {
        events: {
            type: Object,
            default: {},
        }
    },
}
</script>

<template>
    <div class="flow-root">
        <ul role="list" class="-mb-8">
            <li v-for="(event, eventIdx) in events">
                <div class="relative pb-8">
                    <span v-if="eventIdx !== events.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true" />
                    <div class="relative flex space-x-3">
                        <div>
              <span :class="[event.iconBackground, 'h-8 w-8 rounded-full flex items-center justify-center']">
                <component :is="event.icon" class="h-5 w-5" :class="event.iconColor" aria-hidden="true" />
              </span>
                        </div>
                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                            <div>
                                <p class="text-sm text-gray-500">
                                    <span v-html="event.content"></span>
                                </p>
                            </div>
                            <div class="whitespace-nowrap text-right text-xs text-gray-400">
                                <time :datetime="event.datetime">{{ event.date }}</time>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>
