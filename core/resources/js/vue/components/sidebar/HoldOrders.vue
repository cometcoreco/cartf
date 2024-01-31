<script setup>
import {onMounted, ref, watch} from "vue";
import axios from "axios";
import Swal from "sweetalert2";

const emits = defineEmits(null)
const cartTotalAmount = defineProps(['cartTotalAmount'])
const holdOrders = ref([]);
const showOrderList = ref(false);
const holdOrderButton = ref(false);

const getHoldCartItems = () => {
    getOnlyCartItems();
    showOrderList.value = !showOrderList.value;
}

const getOnlyCartItems = () => {
    axios.get(window.appUrl + "/admin-home/pos/get-hold-order").then((response) => {
        holdOrders.value = response.data;
        holdOrderButton.value = response.data.length > 0;
    }).catch((errors) => {
        prepare_errors(errors)
    });
}

const deleteHoldOrder = (id) => {
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
            axios.get(window.appUrl + `/admin-home/pos/delete-hold-order?id=${id}`).then((response) => {
                // emit('orderCanceled', true);
                Swal.fire(
                    'Deleted!',
                    'The saved cart items has been deleted.',
                    'success'
                )

                getOnlyCartItems();
            }).catch((errors) => {
                prepare_errors(errors)
            });
        }
    })
}

const restoreHoldOrder = (id) => {
    axios.get(window.appUrl + `/admin-home/pos/restore-hold-order?id=${id}`).then((response) => {
        if (response.data.type === 'success')
        {
            getHoldCartItems();
            emits('cartItemRestored');
        } else {
            Swal.fire(
                'Warning!',
                response.data.msg,
                'error'
            );
        }
    }).catch((errors) => {

    });
}

watch(() => cartTotalAmount.cartTotalAmount, (newValue, oldValue) => {
    if (newValue === 0)
    {
        getOnlyCartItems();
    }
});

onMounted(() => {
    getOnlyCartItems();
})
</script>

<template>
    <div id="addCustomerButton" v-show="holdOrderButton" :class="['dashboard_posSystem__sidebar__item radius-10 padding-20', {'bg-white': !showOrderList ,'bg-primary text-white': showOrderList}]">
        <a href="#0" class="addCustomer" @click="getHoldCartItems"><i class="las la-list"></i> Hold Order List <i :class="['hold-icon las', {'la-angle-down': showOrderList, 'la-angle-left': !showOrderList}]"></i></a>
    </div>

    <div class="dashboard_posSystem__sidebar__item bg-white radius-10 padding-20" v-if="showOrderList && holdOrderButton">
        <div class="card mt-3">
            <div class="card-body mt-0">
                <div class="d-flex justify-content-between align-items-center py-0">
                    <h6 class="m-0">Hold Orders</h6>
                </div>
                <hr>
                <div class="searchWrap__item__flex">
                    <div class="searchWrap__left mt-3">
                        <div class="searchWrap__left__contents d-flex justify-content-between mb-3"
                             v-for="order in holdOrders" :key="order.name" v-if="holdOrders.length > 0">
                            <h5 class="searchWrap__left__contents__title">{{holdOrders.indexOf(order)+1}}. {{ order.name }}</h5>
                            <div class="d-flex gap-2">
                                <a class="tick-btn btn-info" @click.prevent="restoreHoldOrder(order.name)"><i class="las la-check"></i></a>
                                <a class="tick-btn btn-danger" @click.prevent="deleteHoldOrder(order.name)"><i class="las la-times"></i></a>
                            </div>
                        </div>
                        <div class="searchWrap__left__contents mb-3" v-else>
                            <p class="text-center">No order is saved yet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.hold-icon{
    font-size: 15px;
}
.tick-btn {
    border-radius: 5px;
    padding: 5px 10px;
    cursor: pointer;
}
.btn-info:hover {
    background: #0066ff;
}
.btn-danger:hover {
    background: red;
}
#addCustomerButton.bg-primary a{
    color: #FFFFFF;
}
#addCustomerButton.bg-primary:hover a{
    color: #FFFFFF !important;
}
</style>
