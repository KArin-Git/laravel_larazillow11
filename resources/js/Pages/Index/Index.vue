<template>
    <div>Hello!</div>
    <div>
        The message is {{ message }}
    </div>
</template>

<script setup>
defineProps({
    message: String
})
</script>

<script>
import MainLayout from '../../Layouts/MainLayout.vue';
export default {
    layout: MainLayout
}
</script>