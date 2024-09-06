<template>
    <!--    <template v-if="userAllowedForStation(StationPermission.Broadcasting)">-->
    <!--        <div-->
    <!--            v-if="!station.hasStarted"-->
    <!--            class="navdrawer-alert bg-success text-success-emphasis"-->
    <!--        >-->
    <!--            <router-link-->
    <!--                :to="{name: 'stations:restart:index'}"-->
    <!--            >-->
    <!--                <span class="fw-bold">{{ $gettext('Start Station') }}</span><br>-->
    <!--                <small>-->
    <!--                    {{ $gettext('Ready to start broadcasting? Click to start your station.') }}-->
    <!--                </small>-->
    <!--            </router-link>-->
    <!--        </div>-->
    <!--        <div-->
    <!--            v-else-if="needsRestart"-->
    <!--            class="navdrawer-alert bg-warning text-warning-emphasis"-->
    <!--        >-->
    <!--            <router-link-->
    <!--                :to="{name: 'stations:restart:index'}"-->
    <!--            >-->
    <!--                <span class="fw-bold">{{ $gettext('Reload to Apply Changes') }}</span><br>-->
    <!--                <small>-->
    <!--                    {{ $gettext('Your station has changes that require a reload to apply.') }}-->
    <!--                </small>-->
    <!--            </router-link>-->
    <!--        </div>-->
    <!--    </template>-->
    <div class="offcanvas-body">
        <sidebar-menu :menu="menuItems" />
    </div>
</template>

<script setup lang="ts">
// import {ref} from "vue";
import SidebarMenu from "~/components/Common/SidebarMenu.vue";
import {useStationsMenu} from "~/components/Stations/menu";
//import {StationPermission, userAllowedForStation} from "~/acl";
// import {useAxios} from "~/vendor/axios.ts";
// import {getStationApiUrl} from "~/router.ts";
// import {useRestartEventBus} from "~/functions/useMayNeedRestart.ts";

const props = defineProps({
    station: {
        type: Object,
        required: true
    }
});

const menuItems = useStationsMenu();


// const restartEventBus = useRestartEventBus();
// const restartStatusUrl = getStationApiUrl('/restart-status');
// const needsRestart = ref(props.station.needsRestart);
// const {axios} = useAxios();
//
// restartEventBus.on((forceRestart: boolean): void => {
//     if (forceRestart) {
//         needsRestart.value = true;
//     } else {
//         axios.get(restartStatusUrl.value).then((resp) => {
//             needsRestart.value = resp.data.needs_restart;
//         });
//     }
// });
</script>
