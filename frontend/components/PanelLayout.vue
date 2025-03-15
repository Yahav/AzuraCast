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
            <div
                v-if="slots.clock"
                id="clock"
            >
                <slot name="clock" />
            </div>
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
                <div
                    class="mb-3"
                    v-if="slots.restartSection"
                    id="restartSection"
                >
                    <slot name="restartSection" />
                </div>

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
import {nextTick, onMounted, useSlots, watch} from "vue";
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
    IconSettings,
} from "~/components/Common/icons";
import {useProvidePlayerStore} from "~/functions/usePlayerStore.ts";
import logo from '~/caster_logo.svg';


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
