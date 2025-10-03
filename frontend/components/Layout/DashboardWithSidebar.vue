<template>
    <nav
        id="sidebar"
        class="navdrawer offcanvas offcanvas-start"
        tabindex="-1"
        :aria-label="$gettext('Sidebar')"
    >
        <slot name="sidebar"/>
    </nav>

    <div id="page-wrapper" class="has-sidebar">
        <main id="main">
            <template v-if="stationAlerts?.showAlerts">
                <div
                    v-if="!stationAlerts.hasStarted"
                    class="alert alert-success mx-3 mt-3"
                    role="alert"
                >
                    <router-link
                        :to="{name: 'stations:restart:index'}"
                        class="text-decoration-none alert-link"
                    >
                        <span class="fw-bold">{{ $gettext('Start Station') }}</span><br>
                        <small>
                            {{ $gettext('Ready to start broadcasting? Click to start your station.') }}
                        </small>
                    </router-link>
                </div>
                <div
                    v-else-if="stationAlerts.needsRestart"
                    class="alert alert-warning mx-3 mt-3"
                    role="alert"
                >
                    <router-link
                        :to="{name: 'stations:restart:index'}"
                        class="text-decoration-none alert-link"
                    >
                        <span class="fw-bold">{{ $gettext('Reload to Apply Changes') }}</span><br>
                        <small>
                            {{ $gettext('Your station has changes that require a reload to apply.') }}
                        </small>
                    </router-link>
                </div>
            </template>
            <div class="container" id="content">
                <slot/>
            </div>
        </main>

        <PanelFooter/>
    </div>
</template>

<script setup lang="ts">
import PanelFooter from "~/components/Common/PanelFooter.vue";
import {inject, onMounted, onUnmounted} from "vue";

const stationAlerts = inject<{showAlerts: boolean, hasStarted: boolean, needsRestart: boolean} | null>('stationAlerts', null);

onMounted(() => {
    document.body.classList.add('has-sidebar');
});

onUnmounted(() => {
    document.body.classList.remove('has-sidebar');
});
</script>
