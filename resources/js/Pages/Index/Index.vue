<template>
  <div>Hello!</div>
  <div>
    The message is {{ message }}
  </div>
</template>

<script setup>
defineProps({
  message: String,
})
</script>