<template>
    <small
        class="badge rounded-pill ms-2"
        :class="badgeClass"
    >{{ badgeText }}</small>
</template>

<script setup lang="ts">
import {computed} from "vue";
import {useGettext} from "vue3-gettext";

const props = withDefaults(defineProps<{
    running: boolean,
    extraClasses?: string,
}>(), {
    extraClasses: ''
});

const badgeClass = computed(() => {
    return (props.running)
        ? props.extraClasses +' '+'text-bg-success'
        : props.extraClasses +' '+'text-bg-danger';
});

const {$gettext} = useGettext();

const badgeText = computed(() => {
    return (props.running)
        ? $gettext('Running')
        : $gettext('Not Running');
});
</script>
