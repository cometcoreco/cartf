<template>
  <div class="dashboard_posSystem__sidebar__item bg-white radius-10 padding-20">
    <div class="dashboard_posSystem__tabs">
      <ul class="tabs">
        <li @click="handleTabs($event)" data-tab="existing_customer" :class="activeTabs('existing_customer')">Existing Customer</li>
        <li @click="handleTabs($event)" data-tab="walk_customer" :class="activeTabs('walk_customer')">Walk in Customer</li>
      </ul>
      <div class="tab_content_item active mt-4" id="existing_customer">
        <div class="searchParent">
          <div class="single-input">
            <label for="findEmail" class="label_title">Find by Phone/Email</label>
            <input type="text" v-model="search_text" @keyup="handleCustomerSearch($event)" @change="handleCustomerSearch($event)" class="form--control keyupInput radius-5" placeholder="Enter Phone/Email" id="findEmail">
          </div>
          <div class="searchWrap scrollWrap d-none mt-3">
            <div class="searchWrap__inner scrollWrap__inner" v-if="customers.length > 0">
              <div v-for="customer in customers" :key="customer.id" class="searchWrap__item">
                <div class="searchWrap__item__flex">
                  <div class="searchWrap__left">
                    <div class="searchWrap__left__flex">
                      <div class="searchWrap__left__thumb">
                        <img v-bind:src="customer.image" alt="profile">
                      </div>
                      <div class="searchWrap__left__contents">
                        <h5 class="searchWrap__left__contents__title">{{ customer.mobile }}</h5>
                          <p class="searchWrap__left__contents__subtitle">{{ customer.name }} <small>({{ customer.username }})</small></p>
                          <small>{{ customer.email }}</small>
                      </div>
                    </div>
                  </div>
                  <div class="searchWrap__right">
                    <button class="searchWrap__right__loyal mt-2" @click="handleSelectCustomer(customer.id)">
                        Select User
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="scrollWrap__inner" v-if="isSearchCustomer || customers.length < 1">
              <h5 v-text="isSearchCustomer && customers.length < 1 ? `Searching customer...` : `No customer found`"></h5>
            </div>
          </div>
        </div>
      </div>
      <div class="tab_content_item mt-4" id="walk_customer">
        <div class="searchParent">
            <h5 class="text-center">
                Ordering for walk in customer
            </h5>
        </div>
      </div>
    </div>

    <div class="card mt-3" v-if="selectedCustomer">
        <div class="card-body mt-0">
            <div class="d-flex justify-content-between align-items-center py-0">
                <h6 class="m-0">Selected Customer</h6>
                <button class="btn btn-sm btn-info" @click="changeSelectedCustomer">Change</button>
            </div>
            <hr>
            <div class="searchWrap__item__flex">
                <div class="searchWrap__left">
                    <div class="searchWrap__left__flex">
                        <div class="searchWrap__left__thumb">
                            <img v-bind:src="selectedCustomer.image" alt="profile">
                        </div>
                        <div class="searchWrap__left__contents">
                            <h5 class="searchWrap__left__contents__title">{{ selectedCustomer.phone }}</h5>
                            <p class="searchWrap__left__contents__subtitle">{{ selectedCustomer.name }}</p>
                        </div>
                    </div>
                </div>

                <div class="searchWrap__right">
                    <h6 class="text-center">User from</h6>
                    <p>{{ selectedCustomer.date }}</p>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div v-show="showHideAddNewCustomer()" id="addCustomerButton" class="dashboard_posSystem__sidebar__item bg-white radius-10 padding-20">
    <a href="#1" class="addCustomer" data-bs-toggle="modal" data-bs-target="#addCustomer"><i class="las la-user-alt"></i> Add a New Customer</a>
  </div>
</template>

<script>
import axios from "axios";
import {ref} from "vue";

export default {
  name: "Customer",
  setup(props,{emit}){
      const search_text = ref('');
      const customer_tabs = ref("existing_customer");
      const customers = ref([]);
      const selectedCustomer = ref();
      const isSearchCustomer = ref(false);
     function handleCustomerSearch(event){
         let input_values = event.target.value;

         if (input_values.length > 0) {
             searchCustomer(input_values);

             event.target.closest('.searchParent').querySelector('.searchWrap').classList.add("d-block");
             event.target.closest('.searchParent').querySelector('.searchWrap').classList.remove("d-none");

             document.querySelector('.body-overlay').classList.add('show');
         } else {
             event.target.closest('.searchParent').querySelector('.searchWrap').classList.remove("d-block");
             event.target.closest('.searchParent').querySelector('.searchWrap').classList.add("d-none");

             document.querySelector('.body-overlay').classList.remove('show');
         }

         selectedCustomer.value = null;
         emit("selectedCustomer", null);
     }

     function handleSelectCustomer(id){
         axios.get(window.appUrl + "/admin-home/pos/customer/" + id).then((response) => {
             selectedCustomer.value = response.data;
             emit("selectedCustomer", response.data);

             document.querySelector("#selected_customer").value = selectedCustomer.value.id;
             document.querySelector('#existing_customer .searchParent .searchWrap').classList.remove("d-block");
             document.querySelector('#existing_customer .searchParent .searchWrap').classList.add("d-none");
             document.querySelector('#existing_customer .searchParent .single-input').classList.add("d-none");
             document.querySelector('.dashboard_posSystem__tabs .tabs').classList.add("d-none");
             document.querySelector('.body-overlay').classList.remove('show');
             document.querySelector('.dashboard_posSystem__tabs .tabs').classList.add('d-none');
         });
     }

     async function searchCustomer(searchData) {
       isSearchCustomer.value = true;

       await axios.get(window.appUrl + "/admin-home/pos/search-customer?email=" + searchData).then((response) => {
         customers.value = response.data;
         isSearchCustomer.value = false;
       });
     }

     function changeSelectedCustomer(){
         document.querySelector('#existing_customer .searchParent .single-input').classList.remove("d-none");
         document.querySelector('.dashboard_posSystem__tabs .tabs').classList.remove("d-none");
     }

     function handleTabs(event){
       customer_tabs.value = event.target.getAttribute("data-tab");

        if(customer_tabs.value === 'walk_customer'){
          document.querySelector("#walk_customer").classList.add('active');
          document.querySelector("#existing_customer").classList.remove('active');
          document.querySelector("#addCustomerButton").classList.add('d-none');

          selectedCustomer.value = null;
          emit("selectedCustomer", null);
          search_text.value = "";
        }
        else if (customer_tabs.value === 'existing_customer')
        {
            document.querySelector("#walk_customer").classList.remove('active');
            document.querySelector("#existing_customer").classList.add('active');
            document.querySelector("#addCustomerButton").classList.remove('d-none');
        }
     }

     const activeTabs = (item_class) => {
       return customer_tabs.value === item_class ? 'active' : '';
     }

     const showHideAddNewCustomer = () => {
       return search_text.value.length < 1;
     }

      return {
          handleCustomerSearch,
          handleSelectCustomer,
          changeSelectedCustomer,
          selectedCustomer,
          customers,
          handleTabs,
          isSearchCustomer,
          customer_tabs,
          activeTabs,
          showHideAddNewCustomer,
          search_text
      };
  }
}
</script>

<style scoped>

</style>
