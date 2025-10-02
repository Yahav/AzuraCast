<template>
    <footer id="footer">
        {{ $gettext('Powered by') }}
        <a
            href="https://www.Caster.fm/"
            target="_blank"
        >Caster.fm</a>
        &bull;
        <span v-html="version"/>
        &bull;
        <span v-html="platform"/><br>
    </footer>
</template>

<script setup lang="ts">
import {useAzuraCastDashboardGlobals} from "~/vendor/azuracast.ts";

const {version, platform} = useAzuraCastDashboardGlobals();
</script>
