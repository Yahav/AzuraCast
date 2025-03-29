<template>
    <div class="row row-of-cards">
        <div class="col-md-8">
            <section
                class="card"
                role="region"
                aria-labelledby="hdr_available_logs"
            >
                <div class="card-header text-bg-primary">
                    <h2
                        id="hdr_available_logs"
                        class="card-title"
                    >
                        {{ $gettext('Available Logs') }}
                    </h2>
                </div>

                <log-list
                    :url="logsUrl"
                    @view="viewLog"
                />
            </section>

            <streaming-log-modal ref="$modal" />
        </div>
        <div class="col-md-4" />
    </div>
</template>

<script setup lang="ts">
import StreamingLogModal from "~/components/Common/StreamingLogModal.vue";
import LogList from "~/components/Common/LogList.vue";
import {useTemplateRef} from "vue";
import {getStationApiUrl} from "~/router";

const logsUrl = getStationApiUrl('/logs');

const $modal = useTemplateRef('$modal');

const viewLog = (url: string, isStreaming: boolean) => {
    $modal.value?.show(url, isStreaming);
};
</script>
