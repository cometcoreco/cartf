<template>
  <!-- Payment Method Modal -->
    <div class="modal fade" id="proceedCart">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Methods</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="paymentMethod">
                        <ul class="paymentMethod__card tabs">
                            <li @click="handleTabs('cash'); handleGateway('cash')" v-if="credentials.pos_payment_gateway_enable == 1" class="paymentMethod__card__item cash" :class="activeInactiveTab('cash')">
                                <p class="paymentMethod__card__name" data-tab="cash"><span class="icon"><i class="las la-money-bill"></i></span> Cash</p>
                            </li>
                            <li @click="handleTabs('cards'); handleGateway('cards')" v-if="credentials.pos_card_payment_gateway_enable == 1" class="paymentMethod__card__item card" :class="activeInactiveTab('cards')">
                                <p class="paymentMethod__card__name" data-tab="cards"><span class="icon"><i class="las la-credit-card"></i></span> Cards</p>
                            </li>
                        </ul>

                        <div class="tab_content_item" :class="activeInactiveTab('cash')" id="cash">
                            <div class="paymentMethod__wrap">
                                <SelectedCustomer :customer="customer"/>
                                <div class="paymentMethod__price mt-4">
                                    <p class="paymentMethod__price__para">Total</p>
                                    <p class="paymentMethod__price__title">{{ getCurrencySymbolWithAmount(totalAmount) }}</p>
                                </div>
                                <div class="paymentMethod__cash mt-4">
                                    <div class="paymentMethod__cash__paid">
                                        <p class="paymentMethod__cash__para">Enter amount customer paid</p>
                                        <div class="paymentMethod__cash__input">
                                            <input type="text" class="customer-paid form--control" value="0" @keyup="handleCustomerPaid($event)">
                                            <span class="paymentMethod__cash__input__sign"><i class="material-symbols-outlined"></i></span>
                                        </div>
                                    </div>
                                    <div class="paymentMethod__cash__return mt-4">
                                        <p class="paymentMethod__cash__return__para">Change amount</p>
                                        <h4 class="paymentMethod__cash__return__price">{{ getCurrencySymbolWithAmount(changeAmount.toFixed(2)) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab_content_item" :class="activeInactiveTab('cards')" id="cards">
                            <div class="paymentMethod__wrap">
                                <SelectedCustomer :customer="customer" />
                                <div class="paymentMethod__price mt-4">
                                    <p class="paymentMethod__price__para">Total</p>
                                    <p class="paymentMethod__price__title">{{ getCurrencySymbolWithAmount(totalAmount) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group d-flex gap-3 align-items-center">
                            <label for="send_customer_email">Send customer email</label>
                            <input type="checkbox" @change="sendCustomerEmail($event)" id="send_customer_email" class="form-check" />
                            <p class="info">If you want to send an e-mail by selecting this then you should select an customer first. </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <form @submit="handleSubmit($event)">
                        <input type="hidden" name="selected_gateway" :value="activePaymentTab" id="selected_gateway"/>
                        <input type="hidden" name="selected_customer" value="" id="selected_customer"/>
                        <input type="hidden" name="coupon" value="" id="form_coupon" />
                        <input type="hidden" name="send_email" value="" id="form_send_email" />

                        <button id="close-proceed-to-cart" type="button" class="btn btn-danger mx-2" data-bs-dismiss="modal">Cancel</button>
                        <button id="submit-proceed-to-cart-btn mx-2" type="submit" class="btn btn-primary" v-bind:disabled="disableSubmit">Pay Now <SubmitButtonLoader v-show="buttonLoader"/></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {onUpdated, reactive, ref, watch} from "vue";
import SelectedCustomer from "../customer/SelectedCustomer.vue";
import SubmitButtonLoader from "../button/SubmitButtonLoader.vue";
import InvoiceComponent from "../InvoiceComponent.vue";

export default {
    name: "ProceedToCart",
    components: {InvoiceComponent, SubmitButtonLoader, SelectedCustomer},
    props: {
        totalAmount: 0,
        customer: null
    },
    setup(props, {emit}){
        const activePaymentTab = ref('cash');
        const invoiceData = reactive({});
        const buttonLoader = ref(false);
        const customerPaidAmount = ref(0);
        const disableSubmit = ref(false);
        const totalAmount = ref();
        const changeAmount = ref(0);
        totalAmount.value = props.totalAmount;

        const credentials = ref({
            pos_payment_gateway_enable: false,
            pos_card_payment_gateway_enable: false,
            pos_e_wallet_payment_gateway_enable: false,
        });

        watch(() => props.totalAmount, (newValue, oldValue) => {
            totalAmount.value = newValue;
        });

        axios.get(window.appUrl + "/admin-home/pos/gateway-settings").then((response) => {
            credentials.value = response.data;
        }).catch((errors)=>{

        });

        function handleGateway(val){
            // first need to remove all active class
            let paymentGateway = document.querySelectorAll('.single_click');
            paymentGateway.forEach(function (element, key){
              element.classList.remove("active");
            })

            document.querySelector('.single_click[data-name='+ val +']').classList.add('active');

            document.querySelector("#selected_gateway").value = val;
        }

        function handleTabs(selector){
            document.querySelector("#cash").classList.remove("active");
            document.querySelector("#cards").classList.remove("active");

            document.querySelector("#" + selector).classList.add('active');

            activePaymentTab.value = selector;
            customerPaidAmount.value = 0;
            changeAmount.value = 0;
            document.querySelector('.customer-paid').value = 0;
        }

        function sendCustomerEmail(event){
            if(event.currentTarget.checked){
                document.querySelector("#form_send_email").value = "on";
            }else {
                document.querySelector("#form_send_email").value = "off";
            }
        }

        function handleCustomerPaid(event){
            changeAmount.value = event.target.value - totalAmount.value;
            customerPaidAmount.value = event.target.value;
        }

        function handleSubmit(event){
            event.preventDefault();

            if (activePaymentTab.value === 'cash' && customerPaidAmount.value < 1)
            {
                toastr.error("You must enter a amount");
                return;
            }

            if((Math.round(totalAmount.value) > 0) == false){
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Please add some product in to carts for purchase',
                    showConfirmButton: false,
                    timer: 1500
                });

                return ;
            }

            if (activePaymentTab.value === 'cards')
            {
                customerPaidAmount.value = Math.round(totalAmount.value);
                changeAmount.value = 0;
            }

            disableSubmit.value = true;
            buttonLoader.value = true;

            axios.post(window.appUrl + "/admin-home/pos/order/submit", new FormData(event.target)).then((response) => {
                if (response.data.type === 'success')
                {
                    //todo:: emit an event
                    emit('cartAdded');

                    invoiceData.info = response.data.order_details;
                    invoiceData.transaction = {
                        customerPaidAmount: customerPaidAmount.value,
                        changeAmount: changeAmount.value
                    }
                    emit('invoiceData', invoiceData);

                    document.querySelector('.paymentMethod__cash__input input').value = 0;
                    changeAmount.value = 0;
                    customerPaidAmount.value = 0;
                    activePaymentTab.value = 'cash';
                }

                Swal.fire({
                    position: 'top-end',
                    icon: response.data.type,
                    title: response.data.msg,
                    showConfirmButton: false,
                    timer: response.data.timer ?? 1000
                });

                document.querySelector("#close-proceed-to-cart").dispatchEvent(new MouseEvent("click"));
                disableSubmit.value = false;
                buttonLoader.value = false;
            });


        }

        const activeInactiveTab = (type) => {
            return activePaymentTab.value === type ? 'active' : '';
        }

        return {
            credentials,
            handleTabs,
            handleGateway,
            handleCustomerPaid,
            handleSubmit,
            changeAmount,
            sendCustomerEmail,
            disableSubmit,
            customerPaidAmount,
            buttonLoader,
            invoiceData,
            activePaymentTab,
            activeInactiveTab
        };
    }
}
</script>

<style scoped>
    #close-proceed-to-cart:hover {
        background: var(--delete-color);
        border-color: var(--delete-color);
        color: #ffffff;
    }
</style>
