<template>
  <div class="dashboard_posSystem__header__flex">
    <div class="dashboard_posSystem__header__item">
      <div class="dashboard_posSystem__category category__parent">
        <HeaderCategories @emitCategorySelected="handleCategorySelection" @emitSearch="emitSearch" />
      </div>
    </div>
    <div class="dashboard_posSystem__header__item scaning">
      <HeaderScan @emitSearch="emitSearch" />
    </div>
  </div>
</template>

<script>
  import HeaderScan from "./HeaderScan.vue";
  import HeaderCategories from "./HeaderCategories.vue";

  export default {
    name: "TopHeader",
    components: {HeaderCategories, HeaderScan},

      beforeMount() {

      },
      mounted: () => {
      $('.select2_activation').select2();
    },
    setup(props, {emit}){
      function emitSearch(data){
          emit('emitSearch', data);
      }

      function handleCategorySelection(data){
        emit('emitCategorySelected', data);
      }

      return {
        emitSearch,
        handleCategorySelection
      }
    }
  }
</script>
