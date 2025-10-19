<template>
  <Link href="/">Main Page</Link>&nbsp;
  <Link href="/hello">Show Page</Link>
  <div v-if="flashMessage" class="success">
    {{ flashMessage }}
  </div>
  <slot>Default</slot>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

// page.props are shared from Laravel controllers
const page = usePage()
const flashMessage = computed(() => page.props.flash.message)
</script>

<style scoped>
.success {
  color: white;
  background-color: green;
}
</style>
