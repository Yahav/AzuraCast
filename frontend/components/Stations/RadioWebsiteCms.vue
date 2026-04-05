<template>
    <div class="d-flex align-items-center justify-content-center" style="min-height: 300px;">
        <div v-if="error" class="text-center">
            <div class="alert alert-danger" role="alert">
                {{ error }}
            </div>
            <button class="btn btn-primary" @click="doSsoRedirect">
                {{ $gettext('Try Again') }}
            </button>
        </div>
        <div v-else class="text-center">
            <div class="spinner-border mb-3" role="status">
                <span class="visually-hidden">{{ $gettext('Loading...') }}</span>
            </div>
            <p>{{ $gettext('Redirecting to Radio Website CMS...') }}</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import {onMounted, ref} from "vue";
import {useAxios} from "~/vendor/axios.ts";
import {useTranslate} from "~/vendor/gettext.ts";

const {axios} = useAxios();
const {$gettext} = useTranslate();

const error = ref<string | null>(null);

const doSsoRedirect = async () => {
    error.value = null;

    try {
        const {data} = await axios.post('/api/frontend/account/radio-website-cms');

        if (data.success && data.redirectTo) {
            window.location.href = data.redirectTo;
        } else {
            error.value = data.message || $gettext('An unexpected error occurred.');
        }
    } catch (e: any) {
        error.value = e?.response?.data?.message || $gettext('An unexpected error occurred.');
    }
};

onMounted(() => {
    doSsoRedirect();
});
</script>
