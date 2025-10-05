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

                <loading :loading="isLoading" lazy>
                    <log-list
                        v-if="data"
                        :logs="data"
                        @view="viewLog"
                    />
                </loading>
            </section>

            <streaming-log-modal ref="$modal" />
        </div>
    </div>
</template>

<script setup lang="ts">
import StreamingLogModal from "~/components/Common/StreamingLogModal.vue";
import LogList from "~/components/Common/LogList.vue";
import {useTemplateRef} from "vue";
import {getStationApiUrl} from "~/router";
import {QueryKeys, queryKeyWithStation} from "~/entities/Queries.ts";
import {useQuery} from "@tanstack/vue-query";
import {ApiLogType} from "~/entities/ApiInterfaces.ts";
import {useAxios} from "~/vendor/axios.ts";
import Loading from "~/components/Common/Loading.vue";

const logsUrl = getStationApiUrl('/logs');

const {axios} = useAxios();

type ApiLogRow = Required<ApiLogType>

const {data, isLoading} = useQuery<ApiLogRow[]>({
    queryKey: queryKeyWithStation([
        QueryKeys.StationLogs
    ]),
    queryFn: async ({signal}) => {
        const {data} = await axios.get<ApiLogRow[]>(logsUrl.value, {signal});
        return data;
    },
    placeholderData: () => []
});

const $modal = useTemplateRef('$modal');

const viewLog = (url: string, isStreaming: boolean) => {
    $modal.value?.show(url, isStreaming);
};
</script>
