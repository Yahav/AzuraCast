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

const $lightbox = ref<LightboxTemplateRef>(null);
useProvideLightbox($lightbox);
</script>
