<template>
    <div
        id="station-time"
        class="fs-6 d-none d-md-block"
        :title="$gettext('Station Time')"
    >
        {{ stationName }}
        <span class="mx-2">&#x2022;</span>
        {{ clock }}
    </div>
</template>
<script setup lang="ts">
import {useLuxon} from "~/vendor/luxon.ts";
import useStationDateTimeFormatter from "~/functions/useStationDateTimeFormatter.ts";
import {useIntervalFn} from "@vueuse/core";
import {ref} from "vue";


const props = defineProps({
    stationName: {
        type: String,
        required: false,
        default: null,
    },
});


const {DateTime} = useLuxon();
const {now, formatDateTimeAsTime} = useStationDateTimeFormatter();

const clock = ref('');

useIntervalFn(() => {
    clock.value = formatDateTimeAsTime(now(), DateTime.TIME_WITH_SHORT_OFFSET);
}, 1000, {
    immediate: true,
    immediateCallback: true
});
</script>
