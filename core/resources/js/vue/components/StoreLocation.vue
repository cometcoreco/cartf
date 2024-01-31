<script setup>
    import axios from "axios";
    import {onMounted, ref} from "vue";

    const location = ref(false);
    const locationSettingsUrl = ref("#");
    const hasLocation = () => {
        axios.get(window.appUrl + '/admin-home/pos/store-location')
            .then((response) => {
                location.value = response.data.location == null;
            }).catch((error) => {
                prepare_errors(error)
        });
    }

    const locationSettingsPage = () => {
        axios.get(window.appUrl + '/admin-home/pos/location-settings')
            .then((response) => {
                locationSettingsUrl.value = response.data;
            }).catch((error) => {
            prepare_errors(error)
        });
    }

    onMounted(() => {
        hasLocation();
        locationSettingsPage();
    });
</script>

<template>
    <p class="alert alert-danger" v-if="location">Please select your store location first. <a class="text-primary" :href="locationSettingsUrl">Read more</a></p>
</template>

<style scoped>

</style>
