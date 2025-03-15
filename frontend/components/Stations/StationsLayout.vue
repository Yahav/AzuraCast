<template>
    <panel-layout v-bind="panelProps">
        <template #sidebar>
            <sidebar/>
        </template>
        <template #clock>
            <div
                id="station-time"
                class="fs-6 d-none d-md-block"
                :title="$gettext('Station Time')"
            >
                {{ name }}
                <span class="mx-2">&#x2022;</span>
                {{ clock }}
            </div>
        </template>
        <template #restartSection>
            <template v-if="userAllowedForStation(StationPermissions.Broadcasting)">
                <div
                    v-if="!hasStarted"
                    class="navdrawer-alert bg-success-subtle text-success-emphasis fs-5"
                >
                    <router-link
                        :to="{name: 'stations:restart:index'}"
                    >
                        <span class="fw-bold">{{ $gettext('Start Station') }}</span><br>
                        <small>
                            {{ $gettext('Ready to start broadcasting? Click to start your station.') }}
                        </small>
                    </router-link>
                </div>
                <div
                    v-else-if="needsRestart"
                    class="navdrawer-alert bg-warning-subtle text-warning-emphasis  fs-5"
                >
                    <router-link
                        :to="{name: 'stations:restart:index'}"
                    >
                        <span class="fw-bold">{{ $gettext('Reload to Apply Changes') }}</span><br>
                        <small>
                            {{ $gettext('Your station has changes that require a reload to apply.') }}
                        </small>
                    </router-link>
                </div>
            </template>
        </template>
        <template #default>
            <router-view />

            <header-inline-player />

            <lightbox ref="$lightbox" />
        </template>
    </panel-layout>
</template>

<script setup lang="ts">
import {ref} from "vue";
import {useAzuraCastStation} from "~/vendor/azuracast";
import {useIntervalFn} from "@vueuse/core";
import {userAllowedForStation} from "~/acl";
import {useAxios} from "~/vendor/axios.ts";
import {getStationApiUrl} from "~/router.ts";
import useStationDateTimeFormatter from "~/functions/useStationDateTimeFormatter.ts";
import {useLuxon} from "~/vendor/luxon.ts";
import {useRestartEventBus} from "~/functions/useMayNeedRestart.ts";
import {ApiStationRestartStatus, StationPermissions} from "~/entities/ApiInterfaces.ts";

import PanelLayout from "~/components/PanelLayout.vue";
import {useAzuraCastPanelProps} from "~/vendor/azuracast.ts";
import Sidebar from "~/components/Stations/Sidebar.vue";
import Lightbox from "~/components/Common/Lightbox.vue";
import HeaderInlinePlayer from "~/components/HeaderInlinePlayer.vue";
import {useTemplateRef} from "vue";
import {useProvideLightbox} from "~/vendor/lightbox.ts";

const panelProps = useAzuraCastPanelProps();
const $lightbox = useTemplateRef('$lightbox');

useProvideLightbox($lightbox);

const {name, hasStarted, needsRestart: initialNeedsRestart} = useAzuraCastStation();

const {DateTime} = useLuxon();
const {now, formatDateTimeAsTime} = useStationDateTimeFormatter();

const clock = ref('');

useIntervalFn(() => {
    clock.value = formatDateTimeAsTime(now(), DateTime.TIME_WITH_SHORT_OFFSET);
}, 1000, {
    immediate: true,
    immediateCallback: true
});

const restartEventBus = useRestartEventBus();
const restartStatusUrl = getStationApiUrl('/restart-status');
const needsRestart = ref<boolean>(initialNeedsRestart);
const {axios} = useAxios();

restartEventBus.on((forceRestart: boolean): void => {
    if (forceRestart) {
        needsRestart.value = true;
    } else {
        void axios.get<Required<ApiStationRestartStatus>>(restartStatusUrl.value).then(
            ({data}) => {
                needsRestart.value = data.needs_restart;
            }
        );
    }
});
</script>
