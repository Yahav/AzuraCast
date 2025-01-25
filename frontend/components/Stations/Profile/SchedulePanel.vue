<template>
    <card-page
        v-if="processedScheduleItems.length > 0"
        class="scheduled"
        header-id="hdr_scheduled"
        :title="$gettext('Scheduled')"
    >
        <table class="table table-striped mb-0">
            <tbody>
                <tr
                    v-for="row in processedScheduleItems"
                    :key="row.id"
                >
                    <td>
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="m-0">
                                <template v-if="row.type === 'playlist'">
                                    <small class="badge rounded-pill me-2 text-bg-info">{{ $gettext('Playlist') }}</small>
                                </template>
                                <template v-else>
                                    <small class="badge rounded-pill me-2 text-bg-danger">{{ $gettext('Streamer/DJ') }}</small>
                                </template>
                                <strong>{{ row.name }}</strong>
                            </div>
                            <p class="text-end m-0">
                                <small>{{ row.start_formatted }} - {{ row.end_formatted }}</small>
                                <br>
                                <strong>
                                    <template v-if="row.is_now">
                                        {{ $gettext('Now') }}
                                    </template>
                                    <template v-else>{{ row.time_until }}</template>
                                </strong>
                            </p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </card-page>
</template>

<script setup lang="ts">
import {map} from "lodash";
import {computed} from "vue";
import CardPage from "~/components/Common/CardPage.vue";
import useStationDateTimeFormatter from "~/functions/useStationDateTimeFormatter.ts";
import {useLuxon} from "~/vendor/luxon.ts";

defineOptions({
    inheritAttrs: false
});

const props = defineProps<{
    scheduleItems: Array<any>
}>();

const {DateTime} = useLuxon();
const {
    now,
    timestampToDateTime,
    formatDateTime
} = useStationDateTimeFormatter();

const processedScheduleItems = computed(() => {
    const nowTz = now();

    return map(props.scheduleItems, (row) => {
        const startMoment = timestampToDateTime(row.start_timestamp);
        const endMoment = timestampToDateTime(row.end_timestamp);

        row.time_until = startMoment.toRelative();

        row.start_formatted = formatDateTime(
            startMoment,
            startMoment.hasSame(nowTz, 'day')
                ? DateTime.TIME_SIMPLE
                : DateTime.DATETIME_MED
        );

        row.end_formatted = formatDateTime(
            endMoment,
            endMoment.hasSame(startMoment, 'day')
                ? DateTime.TIME_SIMPLE
                : DateTime.DATETIME_MED
        );

        return row;
    });
});
</script>
