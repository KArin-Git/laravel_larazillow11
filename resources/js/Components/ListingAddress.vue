<template>
    <span>
        {{ listing.street }} {{ listing.street_number }}, {{ listing.city }}, for ${{ listing.price }}
    </span>
</template>

<script setup>
defineProps({
    listing: Object,
})
</script>
