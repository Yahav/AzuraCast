<template>
    <card-page
        id="station-disabled"
        :title="$gettext('Station Disabled')"
    >
        <div class="card-body">
            <p class="card-text">
                {{ $gettext('Your station is currently not enabled for broadcasting. You can still manage media, playlists, and other station settings. To re-enable broadcasting, edit your station profile.') }}
            </p>
        </div>
    </card-page>
</template>
<script setup lang="ts">

import CardPage from "~/components/Common/CardPage.vue";

</script>
