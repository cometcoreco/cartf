<template>
  <div class="dashboard_posSystem__header__item__flex">
    <div class="dashboard_posSystem__header__scan">
      <div class="dashboard_posSystem__header__scan__flex tested">
        <div class="dashboard_posSystem__header__scan__input">
          <input id="search-sku" @keyup="searchProduct()" type="text" class="form--control radius-10" v-bind:placeholder="searchType === 'sku' ? 'Enter product SKU' : 'Enter product name'">
          <button  @click="searchProduct" class="search_icon tested"><i class='las la-search'></i> </button>
        </div>

        <div class="dashboard_posSystem__header__scan__code gap-4">
            <a href="#" class="scan_btn scan_sku_btn radius-10 mx-1" :class="searchType === 'sku' ? 'active' : ''" @click.prevent="handleProductSearch">
                <span class="scan_icon"><i class="las la-barcode"></i></span>
                <span class="scan_title">Scan Code</span>
            </a>
            <a href="#" class="scan_btn reset_btn radius-10 mx-1" @click.prevent="resetSearch">
                <span class="scan_icon">
                    <i class="las la-undo-alt"></i>
                </span>
                <span class="scan_title">Reset Search</span>
            </a>
        </div>
      </div>
    </div>

<!--    <RightCategory />-->
  </div>
</template>

<script>
  import RightCategory from "./RightCategory.vue";
  import {ref} from "vue";

  export default {
    name: "HeaderScan",
    components: {RightCategory},
    setup(props, {emit}){
        const searchType = ref('name');
      function searchProduct(){
          let key = document.querySelector("#search-sku").value;

          if (searchType.value === "sku")
          {
              emit("emitSearch", "sku=" + key);
          } else {
              emit("emitSearch", "name=" + key);
          }
      }

      function resetSearch(){
          emit("emitSearch","reset=true");
          searchType.value = 'name';
          document.querySelector("#search-sku").value = "";
      }

      function handleProductSearch(){
          document.getElementById("search-sku").focus();
          searchType.value = 'sku';
      }

      return {
          searchProduct,
          resetSearch,
          handleProductSearch,
          searchType
      };
    }
  }
</script>

<style scoped>
    .reset_btn:hover{
        background: var(--delete-color);
        border-color: var(--delete-color);
        color: var(--white);
    }
    .scan_sku_btn.active{
        background: var(--main-color-one);
        border-color: var(--main-color-one);
        color: var(--white);
    }
</style>
