<template>
    <div class="navdrawer-header offcanvas-header">
        <router-link
            :to="{ name: 'stations:index' }"
            class="navbar-brand"
        >
            {{ name }}
            <div
                id="station-time"
                class="fs-6"
                :title="$gettext('Station Time')"
            >
                {{ clock }}
            </div>
        </router-link>
    </div>

    <div class="offcanvas-body">
        <sidebar-menu :menu="menuItems" />
    </div>
</template>

<script setup lang="ts">
import {ref} from "vue";
import SidebarMenu from "~/components/Common/SidebarMenu.vue";
import {toRefs, useIntervalFn} from "@vueuse/core";
import {useStationsMenu} from "~/components/Stations/menu";
import useStationDateTimeFormatter from "~/functions/useStationDateTimeFormatter.ts";
import {useLuxon} from "~/vendor/luxon.ts";
import {useStationData} from "~/functions/useStationQuery.ts";

const menuItems = useStationsMenu();

const stationData = useStationData();
const {name, timezone} = toRefs(stationData);

const {DateTime} = useLuxon();
const {now, formatDateTimeAsTime} = useStationDateTimeFormatter(timezone);

const clock = ref('');

useIntervalFn(() => {
    clock.value = formatDateTimeAsTime(now(), DateTime.TIME_WITH_SHORT_OFFSET);
}, 1000, {
    immediate: true,
    immediateCallback: true
});
</script>
