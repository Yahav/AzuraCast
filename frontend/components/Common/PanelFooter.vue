<template>
    <footer id="footer">
        {{ $gettext('Powered by') }}
        <a
            href="https://www.caster.fm/"
            target="_blank"
        >Caster.fm</a>
    </footer>
</template>

<script setup lang="ts">
import {useAzuraCastDashboardGlobals} from "~/vendor/azuracast.ts";

const {version, platform} = useAzuraCastDashboardGlobals();
</script>
