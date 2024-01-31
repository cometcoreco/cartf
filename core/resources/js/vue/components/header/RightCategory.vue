<template>
  <div class="dashboard_posSystem__header__filter">
    <div class="dashboard_posSystem__header__filter__select radius-10">
      <span class="icon"><i class="material-symbols-outlined">filter_list</i></span>

      <select class="select2_activation">
        <option v-for="category in categories" :key="category.id" :value="category.id">{{category.name}}</option>
      </select>
    </div>
  </div>
</template>

<script>
  import axios from "axios";
  import {ref} from "vue";

  export default {
    name: "RightCategory",
    setup (){
      const categories = ref([]);
      function test ( ){
        axios.get(window.appUrl + "/api/tenant/v1/category")
          .then((response) => {
            categories.value = response.data.categories;
          })
          .catch((error) => {

          });
      }

      return {
        test, categories
      }
    },
    mounted() {
      this.test();
    }
  }
</script>

<style scoped>

</style>
