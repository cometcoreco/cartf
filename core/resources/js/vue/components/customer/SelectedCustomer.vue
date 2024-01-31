<template>
    <div class="card mt-3" v-if="customer != null">
        <div class="card-body mt-0">
            <div class="d-flex justify-content-between align-items-center py-0">
                <h6 class="m-0">Selected Customer</h6>
            </div>
            <hr>
            <div class="searchWrap__item__flex">
                <div class="searchWrap__left">
                    <div class="searchWrap__left__flex">
                        <div class="searchWrap__left__thumb">
                            <img v-bind:src="customer.image" alt="profile">
                        </div>
                        <div class="searchWrap__left__contents">
                            <h5 class="searchWrap__left__contents__title">{{ customer.phone }}</h5>
                            <p class="searchWrap__left__contents__subtitle">{{ customer.name }}</p>
                        </div>
                    </div>
                </div>

                <div class="searchWrap__right">
                    <h6 class="text-center">User from</h6>
                    <p>{{ customer.date }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
      name: "SelectedCustomer",
      props: {
          customer: null,
      }
  }
</script>

<style scoped>

</style>