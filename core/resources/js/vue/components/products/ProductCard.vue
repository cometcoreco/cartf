<template>
    <div class="dashboard_posSystem__item__thumb radius-10">
      <div class="dashboard_posSystem__item__thumb__main">
        <img :src="product.img_url" alt="">
      </div>
    </div>
    <div class="dashboard_posSystem__item__contents mt-2">
      <h5 class="dashboard_posSystem__item__title">{{ product.title }} </h5>
      <span class="dashboard_posSystem__item__price mt-1">{{ getCurrencySymbolWithAmount(product.discount_price ?? product.price) }}</span>
      <div class="dashboard_posSystem__item__btn btn_flex mt-2">
        <button class="posBtn_details posBtn radius-5" @click="loadProductDetails($event, product)" v-if="!product.is_cart_able">View Details</button>
        <button class="posBtn_cart posBtn radius-5"
           v-bind:data-product-id="product.prd_id"
           @click="addToCartButton($event, product)"
           v-if="product.is_cart_able">
          Add to Cart
        </button>
      </div>
    </div>
</template>

<script>
  import {ref, defineEmits, toRef} from "vue";
  import Swal from "sweetalert2";
  import axios from "axios";

  export default {
    name: "ProductCard",
    data(){
      return {
        symbol: this.currencySymbol.symbol,
        position: this.currencySymbol.position
      }
    },
    props: {
      loadCartItems : null,
      product: {},
    },
    setup(props,{emit}){
      const emits = defineEmits(["cartAdded"]);
      const cartItems = ref({});
      const CartStatus = toRef(props,'loadCartItems');

      function addToCartButton(event, product){
        let currentEl = event.target;
        let csrf_token = document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content");
        let data = new FormData();

        data.append("product_id", product.prd_id);
        data.append("_token", csrf_token);
        data.append("quantity", 1);
        data.append("pid_id", "");
        data.append("product_variant", "");
        data.append("selected_size", "");
        data.append("selected_color", "");
        data.append("attribute","");

        send_ajax_request("post", data ,window.appUrl + "/shop/cart/ajax/add-to-cart", ()=> {
          currentEl.innerHTML = "Processing...";
          currentEl.setAttribute("disabled",true);
          currentEl.classList.add("disabled");
        },(data) => {
          //todo:: emit an event
          emit('cartAdded');

          Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Item added successfully',
            showConfirmButton: false,
            timer: 1500
          });
          currentEl.innerHTML = "Add to cart";
          currentEl.removeAttribute("disabled");
          currentEl.classList.remove("disabled");
        }, (errors) => {
          prepare_errors(errors)
          currentEl.innerHTML = "Add to cart";
          currentEl.removeAttribute("disabled");
          currentEl.classList.remove("disabled");
        })
      }

      function loadProductDetails(event,product){
        let currentEl = event.target;
        // first change button text view details to opening and disabled attribute true
        currentEl.innerText = "Opening...";
        currentEl.setAttribute("disabled", true)

        // todo:: load product data from api
        axios.get(window.appUrl + '/api/tenant/v1/product/' + product.prd_id).then((response) => {
          currentEl.innerText = "View Details";
          currentEl.removeAttribute("disabled")

          document.querySelector('.interview-popup').classList.toggle('popup-active')
          document.querySelector('.popup-overlay').classList.toggle('popup-active')
          emit('loadProductView', response.data);

          // clear old product details page
          let available_options = document.querySelectorAll('.value-input-area')

          // get all selected attributes in {key:value} format
          available_options.forEach(function (element, key) {
              let selected_option = element.querySelector('li.active').classList.remove('active');

              // add value to input for display frontend value like -> Small , Big
              element.parentElement.parentElement.querySelector('input[type=text]').value = '';
              // this will store value on hidden input and this value will work with selecting correct variant
              element.parentElement.parentElement.querySelector('input[type=hidden]').value = '';
          });
        }).catch((errors) => {
          currentEl.innerText = "View Details";
          currentEl.removeAttribute("disabled")

          prepare_errors(errors)
        });
      }

      return {
        addToCartButton,
        loadProductDetails
      }
    },
  }
</script>

<style scoped>

</style>
