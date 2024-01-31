<template>
    <span class="loading-icon mdi mdi-loading"></span>
</template>

<style scoped>
    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .loading-icon {
        font-size: inherit;
        animation: rotate 1s linear infinite;
    }
</style>
