<script setup>
    import {ref} from "vue";
    import SubmitButtonLoader from "../button/SubmitButtonLoader.vue";
    const emits = defineEmits(null);
    const props = defineProps(['orderDetails']);

    const invoiceDataStatus = ref(false);
    const loaderButton = ref(false);

    const printReceipt = () => {
        loaderButton.value = true;
        invoiceDataStatus.value = true;
        emits('invoiceDataStatus', invoiceDataStatus.value);
        emits('directPrint');

        setTimeout(()=>{
            loaderButton.value = false;
        }, 500)
    }

    const showReceipt = () => {
        loaderButton.value = true;
        invoiceDataStatus.value = true;
        emits('invoiceDataStatus', invoiceDataStatus.value);

        setTimeout(()=>{
            loaderButton.value = false;
        }, 500)
    }

    const readOrderDetails = () => {
        let invoice_number = props.orderDetails.invoice_number;
        let url = window.appUrl + `/admin-home/order-manage/view/${invoice_number}`;

        window.open(url, '_blank', 'noreferrer');
    }
</script>

<template>
    <div class="modal fade" id="invoiceModal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content bg-white">
                <div class="modal-header">
                    <h5 class="modal-title">Order Complete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body my-3 mb-5 text-center">
                    <p class="mb-3">
                        <strong>Order successfully completed! You can view the order details or print the receipt using the options below.</strong>
                    </p>

                    <div class="button-wrappers d-flex justify-content-center gap-3 mt-5">
                        <button id="submit-proceed-to-hold-btn" type="button" class="btn btn-primary" @click.prevent="readOrderDetails">Order Details</button>
                        <button id="submit-proceed-to-hold" type="button" class="btn btn-info" @click.prevent="showReceipt">Show Receipt <SubmitButtonLoader v-if="loaderButton"/></button>
                        <button id="submit-proceed-to-hold" type="button" class="btn btn-success" @click.prevent="printReceipt">Print Receipt <SubmitButtonLoader v-if="loaderButton"/></button>
                    </div>
                </div>
                <div class="modal-footer d-flex">
                    <button id="close-proceed-to-hold" type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Cancel</button>
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
