<template>
    <a
        class="visually-hidden-focusable"
        href="#content"
    >
        {{ $gettext('Skip to main content') }}
    </a>

    <header class="navbar bg-primary-dark shadow-sm fixed-top">
        <template v-if="slots.sidebar">
            <button
                id="navbar-toggle"
                data-bs-toggle="offcanvas"
                data-bs-target="#sidebar"
                aria-controls="sidebar"
                aria-expanded="false"
                :aria-label="$gettext('Toggle Sidebar')"
                class="navbar-toggler d-inline-flex d-lg-none me-3"
            >
                <icon
                    :icon="IconMenu"
                    class="lg"
                />
            </button>
        </template>

        <a
            class="navbar-brand ms-0 me-auto"
            :href="homeUrl"
        >
            <img
                :src="logo"
                alt="Caster.fm"
                style="height:40px;"
            >
        </a>

        <div id="radio-player-controls" />

        <div class="dropdown ms-3 d-inline-flex align-items-center">
            <div id="station-time-wrapper" />
            <clock
                v-if="name !== null"
                :station-name="name"
            />
            <button
                aria-expanded="false"
                aria-haspopup="true"
                class="navbar-toggler"
                :aria-label="$gettext('Toggle Menu')"
                data-bs-toggle="dropdown"
                type="button"
            >
                <icon
                    :icon="IconMenuOpen"
                    class="xl"
                />
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a
                        class="dropdown-item"
                        :href="homeUrl"
                    >
                        <icon :icon="IconHome" />
                        {{ $gettext('Dashboard') }}
                    </a>
                </li>
                <li class="dropdown-divider">
&nbsp;
                </li>
                <li v-if="showAdmin">
                    <a
                        class="dropdown-item"
                        :href="adminUrl"
                    >
                        <icon :icon="IconSettings" />
                        {{ $gettext('System Administration') }}
                    </a>
                </li>
                <li>
                    <a
                        class="dropdown-item"
                        :href="profileUrl"
                    >
                        <icon :icon="IconAccountCircle" />
                        {{ $gettext('My Account') }}
                    </a>
                </li>
                <!--                <li>-->
                <!--                    <a-->
                <!--                        class="dropdown-item theme-switcher"-->
                <!--                        href="#"-->
                <!--                        @click.prevent="toggleTheme"-->
                <!--                    >-->
                <!--                        <icon :icon="IconInvertColors" />-->
                <!--                        {{ $gettext('Switch Theme') }}-->
                <!--                    </a>-->
                <!--                </li>-->
                <li class="dropdown-divider">
                    &nbsp;
                </li>
                <li>
                    <a
                        class="dropdown-item"
                        href="https://www.caster.fm/help/pro-plan/"
                        target="_blank"
                    >
                        <icon :icon="IconHelp" />
                        {{ $gettext('Help') }}
                    </a>
                </li>
                <li class="dropdown-divider">
&nbsp;
                </li>
                <li>
                    <a
                        class="dropdown-item"
                        :href="logoutUrl"
                    >
                        <icon :icon="IconExitToApp" />
                        {{ $gettext('Sign Out') }}
                    </a>
                </li>
            </ul>
        </div>
    </header>

    <nav
        v-if="slots.sidebar"
        id="sidebar"
        class="navdrawer offcanvas offcanvas-start"
        tabindex="-1"
        :aria-label="$gettext('Sidebar')"
    >
        <slot name="sidebar" />
    </nav>

    <div
        id="page-wrapper"
        :class="[(slots.sidebar) ? 'has-sidebar' : '']"
    >
        <main id="main">
            <div class="container">
                <template v-if="hasStarted !== null && needsRestart !== null && userAllowedForStation(StationPermission.Broadcasting)">
                    <div
                        v-if="!hasStarted"
                        class="navdrawer-alert bg-success text-white mb-3 mb-3 d-flex align-items-center"
                    >
                        <icon
                            :icon="IconWarning"
                            class="lg me-3 ms-4"
                        />
                        <router-link
                            :to="{name: 'stations:restart:index'}"
                            :class="'alert-link'"
                        >
                            <h4 class="fw-bold">
                                {{ $gettext('Start Station') }}
                            </h4>
                            <div>
                                {{ $gettext('Ready to start broadcasting? Click to start your station.') }}
                            </div>
                        </router-link>
                    </div>
                    <div
                        v-else-if="needsRestart"
                        class="alert alert-warning bg-warning text-warning-emphasis mb-3 d-flex align-items-center"
                    >
                        <icon
                            :icon="IconWarning"
                            class="lg me-3"
                        />
                        <router-link
                            :to="{name: 'stations:restart:index'}"
                            :class="'alert-link'"
                        >
                            <h4 class="fw-bold">
                                {{ $gettext('Reload to Apply Changes') }}
                            </h4>
                            <div>
                                {{ $gettext('Your station has changes that require a reload to apply.') }}
                            </div>
                        </router-link>
                    </div>
                </template>

                <slot />
            </div>
        </main>

        <footer id="footer">
            {{ $gettext('Powered by') }}
            <a
                href="https://www.caster.fm/"
                target="_blank"
            >Caster.fm</a>
        </footer>
    </div>
</template>

<script setup lang="ts">
import {nextTick, onMounted, ref, /*ref,*/ useSlots, watch} from "vue";
import Icon from "~/components/Common/Icon.vue";
//import useTheme from "~/functions/theme";
import {
    IconAccountCircle,
    IconExitToApp,
    IconHelp,
    IconHome,
    //IconInvertColors,
    IconMenu,
    IconMenuOpen,
    IconSettings, IconWarning,
} from "~/components/Common/icons";
import {useAzuraCastStation} from "~/vendor/azuracast";
import {useProvidePlayerStore} from "~/functions/usePlayerStore.ts";
import {StationPermission, userAllowedForStation} from "~/acl.ts";
import Clock from "~/components/Stations/Clock.vue";
import {useRestartEventBus} from "~/functions/useMayNeedRestart.ts";
import {getStationApiUrl} from "~/router.ts";
import {useAxios} from "~/vendor/axios.ts";
import logo from '~/caster_logo.svg';

const station = useAzuraCastStation()

let name, hasStarted, initialNeedsRestart;
if (station) {
    ({ name, hasStarted, needsRestart: initialNeedsRestart } = station);
} else {
    name = null;
    hasStarted = null;
    initialNeedsRestart = null;
}

export interface PanelLayoutProps {
    instanceName: string,
    userDisplayName: string,
    homeUrl: string,
    profileUrl: string,
    adminUrl: string,
    logoutUrl: string,
    showAdmin: boolean,
    version: string,
    platform: string,
}

defineProps<PanelLayoutProps>();


const slots = useSlots();

const handleSidebar = () => {
    if (slots.sidebar) {
        document.body.classList.add('has-sidebar');
    } else {
        document.body.classList.remove('has-sidebar');
    }
}

//const {toggleTheme} = useTheme();


const restartEventBus = useRestartEventBus();

const needsRestart = ref<boolean>(initialNeedsRestart);
const {axios} = useAxios();

restartEventBus.on((forceRestart: boolean): void => {
    const restartStatusUrl = getStationApiUrl('/restart-status');
    if (forceRestart) {
        needsRestart.value = true;
    } else {
        axios.get(restartStatusUrl.value).then((resp) => {
            needsRestart.value = resp.data.needs_restart;
        });
    }
});


watch(
    () => slots.sidebar,
    handleSidebar,
    {
        immediate: true
    }
);

useProvidePlayerStore('global');

onMounted(() => {
    void nextTick(() => {
        document.dispatchEvent(new CustomEvent("vue-ready"));
    });
});
</script>
