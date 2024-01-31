<script setup>
    import SubmitButtonLoader from "../button/SubmitButtonLoader.vue";
    import {onMounted, ref} from "vue";
    import axios from "axios";
    import Swal from "sweetalert2";

    const props = defineProps(['cartProducts','cartTotalAmount']);
    const buttonLoader = ref(false);
    const currentDateTime = ref(null);
    const emit = defineEmits(['newCartProducts']);

    const getDateTime = () => {
        const now = new Date();

        const date = now.getDate();
        const month = now.getMonth() + 1;
        const year = now.getFullYear();
        let hour = now.getHours();
        const minute = now.getMinutes();
        const second = now.getSeconds();

        const amPm = now.getHours() >= 12 ? 'PM' : 'AM';
        hour = hour % 12;
        hour = hour ? hour : 12;

        return `Cart-${date}-${month}-${year},${hour}:${minute}:${second}${amPm}`
    };

    const submitHolderOrder = () => {
        buttonLoader.value = true;
        let cartName = currentDateTime.value ? currentDateTime.value.value : null;

        axios.post(window.appUrl + '/admin-home/pos/hold-order', {
            cart_name: cartName
        }).then((response) => {
            if (response.data.type === 'success')
            {
                alertSwal(response.data.type, response.data.msg);
                buttonLoader.value = false;
                document.querySelector("#close-proceed-to-hold").dispatchEvent(new MouseEvent("click"));
                emit('newCartProducts', {});
            } else {
                alertSwal(response.data.type, response.data.msg);
            }
        }).catch((error) => {

        });
    };

    const alertSwal = (type, msg) => {
        Swal.fire({
            position: 'top-end',
            icon: type,
            title: msg,
            showConfirmButton: false,
            timer: 1000
        });
    };
</script>

<template>
    <div class="modal fade" id="proceedHold">
        <div class="modal-dialog">
            <div class="modal-content bg-white">
                <div class="modal-header">
                    <h5 class="modal-title">Hold This Order For Later</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body mt-3 mb-3">
                    <div class="dashboard_posSystem__sidebar__cart__item" v-for="product in props.cartProducts"
                         :key="product.rowId">
                        <div class="dashboard_posSystem__sidebar__cart__item__flex">
                            <p class="dashboard_posSystem__sidebar__cart__item__para">{{ product.name }} x {{ product.qty }}</p>
                            <p class="dashboard_posSystem__sidebar__cart__item__price">
                                {{ getCurrencySymbolWithAmount(product.price) }}</p>
                        </div>
                    </div>
                    <div class="dashboard_posSystem__sidebar__cart__total">
                        <div class="dashboard_posSystem__sidebar__cart__item__flex">
                            <p class="dashboard_posSystem__sidebar__cart__item__para">Subtotal:</p>
                            <p class="dashboard_posSystem__sidebar__cart__item__price">{{ getCurrencySymbolWithAmount(props.cartTotalAmount.sub_total) }}</p>
                        </div>
                    </div>


                    <div class="mt-3">
                        <label for="cartSaveName">Cart Name To Be Saved</label>
                        <input id="cartSaveName" class="form--control" type="text" :value="getDateTime()" ref="currentDateTime">
                    </div>
                </div>
                <div class="modal-footer">
                    <form>
                        <button id="close-proceed-to-hold" type="button" class="btn btn-danger mx-2" data-bs-dismiss="modal">Cancel</button>
                        <button id="submit-proceed-to-hold-btn" type="button" class="btn btn-primary mx-2" @click.prevent="submitHolderOrder">Hold <SubmitButtonLoader v-show="buttonLoader"/></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .dashboard_posSystem__sidebar__cart__item:not(:last-child){
        border-bottom: none;
        padding-bottom: 8px;
        margin-bottom: 8px;
    }
    .dashboard_posSystem__sidebar__cart__total{
        border-top: 1px solid var(--border-color);
        padding-top: 15px;
        padding-bottom: 15px;
    }
</style>
