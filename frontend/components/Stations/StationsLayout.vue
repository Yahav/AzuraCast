<template>
    <panel-layout v-bind="{...panelProps, ...stationPropsForPanel}">
        <template #sidebar>
            <sidebar
                v-bind="sidebarProps"
            />
        </template>
        <template #default>
            <router-view />

            <header-inline-player />

            <lightbox ref="$lightbox" />
        </template>
    </panel-layout>
</template>

<script setup lang="ts">
import PanelLayout from "~/components/PanelLayout.vue";
import {useAzuraCast} from "~/vendor/azuracast.ts";
import Sidebar from "~/components/Stations/Sidebar.vue";
import Lightbox from "~/components/Common/Lightbox.vue";
import HeaderInlinePlayer from "~/components/HeaderInlinePlayer.vue";
import {ref} from "vue";
import {LightboxTemplateRef, useProvideLightbox} from "~/vendor/lightbox.ts";
import {pickProps} from "~/functions/pickProps.ts";
// import {useRestartEventBus} from "~/functions/useMayNeedRestart.ts";
// import {getStationApiUrl} from "~/router.ts";
// import {useAxios} from "~/vendor/axios.ts";

const {panelProps, sidebarProps} = useAzuraCast();
const stationPropsForPanel = pickProps(sidebarProps.station, {
    hasStarted: {
        type: Boolean,
        required: false,
        default: null
    },
    needsRestart: {
        type: Boolean,
        required: false,
        default: null
    },
    stationName: {
        type: String,
        required: false,
        default: null,
    }
    });

// const restartEventBus = useRestartEventBus();
// const restartStatusUrl = getStationApiUrl('/restart-status');
// const needsRestart = ref(sidebarProps.station.needsRestart);
// const {axios} = useAxios();

// restartEventBus.on((forceRestart: boolean): void => {
//     if (forceRestart) {
//         needsRestart.value = true;
//     } else {
//         axios.get(restartStatusUrl.value).then((resp) => {
//             needsRestart.value = resp.data.needs_restart;
//         });
//     }
// });

const $lightbox = ref<LightboxTemplateRef>(null);
useProvideLightbox($lightbox);
</script>
