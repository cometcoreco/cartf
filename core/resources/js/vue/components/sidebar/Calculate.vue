<style scoped>
    .orderBtn_hold_order{
        background-color: unset;
        border: 1px solid var(--border-color);
    }
    .orderBtn_hold_order:is(:hover){
        background-color: var(--main-color-one);
        border-color: var(--main-color-one);
        color: #ffffff;
    }
</style>

<template>
  <div class="dashboard_posSystem__sidebar__item bg-white radius-10 padding-20" v-if="data.sub_total > 0">
    <div class="dashboard_posSystem__sidebar__estimate">
      <div class="dashboard_posSystem__sidebar__estimate__discount">
        <input type="text" v-model="couponCode" class="discountCoupon radius-5 discountCouponInput" placeholder="Enter a Coupon">
        <button @click="handleCouponSubmission" class="btn btn-primary">
            <i class="las la-paper-plane"></i>
        </button>
      </div>
      <div class="dashboard_posSystem__sidebar__estimate__count mt-4">
        <div class="dashboard_posSystem__sidebar__estimate__list">
          <div class="dashboard_posSystem__sidebar__estimate__list__item">
            <p class="title">Subtotal</p>
            <p class="price">{{ getCurrencySymbolWithAmount(data.sub_total) }}</p>
          </div>
          <div class="dashboard_posSystem__sidebar__estimate__list__item">
            <p class="title">Discount</p>
            <p class="price">{{ getCurrencySymbolWithAmount(couponAmount) }}</p>
          </div>
          <div class="dashboard_posSystem__sidebar__estimate__list__item">
            <p class="title">Vat</p>
            <p class="price">(+{{ data.tax }}%) {{ getCurrencySymbolWithAmount(taxAmount.toFixed(2)) }}</p>
          </div>
        </div>
        <div class="dashboard_posSystem__sidebar__estimate__list">
          <div class="dashboard_posSystem__sidebar__estimate__list__item">
            <p class="title"><strong>Total</strong></p>
            <p class="price"><strong>{{ getCurrencySymbolWithAmount(customSubTotal) }}</strong></p>
          </div>
        </div>
      </div>
      <div class="dashboard_posSystem__sidebar__estimate__footer mt-4">
        <div class="dashboard_posSystem__sidebar__estimate__footer__btn btn_flex">
            <a href="#1" class="orderBtn_hold_order orderBtn radius-5" data-bs-toggle="modal" data-bs-target="#proceedHold" v-if="customSubTotal > 0">Hold Order</a>
            <a href="#1" class="orderBtn_cancel orderBtn radius-5" @click.prevent="handleCancelOrder">Cancel Order</a>
            <a href="#1" class="orderBtn_proceed orderBtn radius-5" data-bs-toggle="modal" data-bs-target="#proceedCart">Proceed to Order</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {ref, toRef, watch} from "vue";
import axios from "axios";
import Swal from "sweetalert2";

export default {
  name: "Calculate",
  props: {
      data : null,
  },
  setup(props,{emit}){
      const taxAmount = ref(0);
      const totalAmount = ref(0);
      const tax = ref(props.data.tax);
      const sub_total = ref(props.data.sub_total);
      const customSubTotal = ref(props.data.total);
      const customTaxedAmount = ref(props.data.taxed_amount)
      const onlyTaxAmount = ref(customTaxedAmount.value);
      const couponAmount = ref(0);
      const couponCode = ref("");

      taxAmount.value = onlyTaxAmount.value;
      totalAmount.value = customSubTotal.value;
      emit("totalAmount", totalAmount);

      function handleCouponSubmission() {
          document.querySelector("#form_coupon").value = couponCode.value;

          axios.get(window.appUrl + "/shop/checkout/coupon?coupon=" + couponCode.value).then((response) => {
              if(response.data.type === 'success'){
                  couponAmount.value = response.data.coupon_amount;
                  let subTotal = sub_total.value - response.data.coupon_amount;

                  onlyTaxAmount.value = (subTotal / 100) * tax.value;
                  taxAmount.value = (onlyTaxAmount.value).toFixed(2);
                  totalAmount.value = (subTotal + onlyTaxAmount.value).toFixed(2);

                  emit("totalAmount", totalAmount.value);

                  toastr.success("Coupon applied.");
              }else{
                  couponAmount.value = 0;
                  let subTotal = sub_total.value - 0;

                  onlyTaxAmount.value = (subTotal / 100) * tax.value;
                  taxAmount.value = (onlyTaxAmount.value).toFixed(2);
                  totalAmount.value = (subTotal + onlyTaxAmount.value).toFixed(2);

                  emit("totalAmount", totalAmount.value);

                  toastr.error(response.data.msg);
              }
          });
      }


      function handleCancelOrder(){
          //todo:: send a request for clearing cart items
          Swal.fire({
              title: 'Are you sure?',
              text: "You won't be able to revert those!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, remove it!'
          }).then((result) => {
              if (result.isConfirmed) {
                  axios.get(window.appUrl + "/shop/cart/ajax/clear-all-cart-items").then((response) => {
                      emit('orderCanceled', true);
                      Swal.fire(
                          'Canceled!',
                          'All cart items has been cleared.',
                          'success'
                      )
                  }).catch((errors) => {
                      prepare_errors(errors)
                  });
              }
          })
      }


      watch(() => props.data, (newValue, oldValue) => {
          tax.value = newValue.tax;
          sub_total.value = newValue.sub_total;
          customSubTotal.value = newValue.total;
          customTaxedAmount.value = newValue.taxed_amount;

          if(customSubTotal.value > 0){
              let subTotal = (customSubTotal.value - couponAmount.value);
              onlyTaxAmount.value = customTaxedAmount.value;
              taxAmount.value = onlyTaxAmount.value;
              totalAmount.value = subTotal;
          }else{
              if (document.querySelector('.discountCoupon')){
                document.querySelector('.discountCoupon').value = '';
              }

              couponAmount.value = 0
              onlyTaxAmount.value = 0;
              taxAmount.value = 0;
              totalAmount.value = 0;
          }

          emit("totalAmount", totalAmount.value);
      })

      return {
          customSubTotal,
          customTaxedAmount,
          taxAmount,
          totalAmount,
          couponAmount,
          handleCouponSubmission,
          handleCancelOrder,
          couponCode,
      };
  }
}
</script>
