<template>
    Hello! {{ counter }}
    <Link href="/hello">Show Page</Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
const counter = ref(0)
setInterval(() => counter.value++, 1000)
</script>