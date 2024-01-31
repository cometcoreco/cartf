<template>
    <div class="dashboard_posSystem__sidebar__item bg-white radius-10 padding-20" v-if="cartItemsLength > 0">
        <div class="dashboard_posSystem__sidebar__cart">
            <div class="dashboard_posSystem__sidebar__cart__header">
                <div class="dashboard_posSystem__sidebar__cart__header__flex">
                    <p class="dashboard_posSystem__sidebar__cart__header__para">Items in Cart</p>
                    <a href="#1" @click="clearCartItems" class="dashboard_posSystem__sidebar__cart__clear"
                       data-bs-toggle="modal" data-bs-target="#clearCart">
                        <i class="las la-times"></i> Clear Cart
                    </a>
                </div>
            </div>

            <div class="dashboard_posSystem__sidebar__cart__inner scrollWrap" :style="[isExpanded.expand ? {maxHeight: isExpanded.maxHeight} : '']">
                <div class="scrollWrap__inner">
                    <div class="dashboard_posSystem__sidebar__cart__item" v-for="product in products"
                         :key="product.rowId">
                        <div class="dashboard_posSystem__sidebar__cart__item__flex">
                            <div class="dashboard_posSystem__sidebar__cart__item__left">
                                <p class="dashboard_posSystem__sidebar__cart__item__para">{{ product.name }}</p>
                            </div>
                            <div>
                                {{ product.qty }}
                            </div>
                            <div class="dashboard_posSystem__sidebar__cart__item__icon">
                                <a href="#0" @click.prevent="toggleModalClass(product)" class="icon"
                                   data-bs-toggle="modal" data-bs-target="#productEdit"><i
                                    class="las la-pen-alt"></i></a>
                                <a href="#0" @click.prevent="removeCartItem(product.rowId)" class="icon remove"
                                   data-bs-toggle="modal" data-bs-target="#removeCart"><i class="las la-times"></i></a>
                            </div>
                            <p class="dashboard_posSystem__sidebar__cart__item__price">
                                {{ getCurrencySymbolWithAmount(product.price) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dashboard_posSystem__sidebar__cart__inner scrollWrap" v-if="cartItemsLength < 1">
                Product cart is empty
            </div>
            <div class="dashboard_posSystem__sidebar__cart__footer">
                <a href="#1" class="expand_btn" @click="expandAll()">{{isExpanded.expand ? 'Collapse All' : 'Expand All'}} <i :class="isExpanded.expand ? 'las la-angle-up' : 'las la-angle-down'"></i></a>
            </div>
        </div>
    </div>

    <!-- Modal popup start -->
    <div :class="['popup_overlay', {popup_active: openModal}]" @click.prevent="removeModalClass"></div>
    <div :class="['popup_fixed','custom_modal_popup', {popup_active: openModal}]">
        <div class="popup_contents">
            <div class="popup_contents_header">
                <div class="popup_contents_header__flex">
                    <span class="popup_contents_close popup_close" @click.prevent="removeModalClass"> <i
                        class="las la-times"></i> </span>
                    <h5 class="popup_contents_title">Update cart item quantity</h5>
                </div>
            </div>
            <hr/>
            <div class="popup-contents-interview">
                <div class="myJob-wrapper-single-contents">
                    <h6 class="d-flex justify-content-between">
                        <span class="popup_cart_left__title">
                            {{ cartItem.name }}
                        </span>
                        <span>{{ cartItem.qty }}</span>
                        <span>{{ cartItem.price }}</span>
                    </h6>

                    <h4 class="myJob-wrapper-single-title mt-3">
                        <input id="modal-cart-item-quantity" type="number" class="form-control"
                               v-bind:value="cartItem.qty"/>
                    </h4>
                </div>
            </div>
            <div class="popup_contents_btn">
                <div class="btn-wrapper d-flex gap-3 justify-content-end">
                    <a href="#1" class="btn btn-danger popup_close" @click.prevent="removeModalClass"> Cancel </a>
                    <a href="#1" class="btn btn-primary" @click.prevent="updateQuantity(cartItem.rowId)"> Update item </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal popup end -->
    <!-- Custom Modal end -->
</template>

<script>
import axios from "axios";
import {ref, defineEmits, toRef, watch, reactive, onUpdated} from "vue";
import Swal from "sweetalert2";

export default {
    name: "CartItems",
    data() {
        return {
            log: console.log
        };
    },
    props: {
        loadCartItems: null,
        clearCart: null
    },
    emits: [],
    mounted() {
        this.getCartItems();
    },
    setup: (props, {emit}) => {
        const LoadCardStatus = toRef(props, 'loadCartItems');
        const clearCartItem = toRef(props, 'clearCart');
        let emits = defineEmits(['changeCartLoadState']);
        const products = ref({});
        const testInput = ref(0);
        const cartItem = ref(0);
        const totalAmountWithTax = ref(0);
        const tax = ref(0);
        const cartItemsLength = ref(0);
        const openModal = ref(false);
        const isExpanded = reactive({expand: false, maxHeight: '300px'});

        function taxAmount() {
            axios.get(window.appUrl + "/shop/cart/ajax/tax-amount").then((response) => {
                tax.value = 0;
            });
        }

        function getCartItems() {
            axios.get(window.appUrl + "/shop/cart/ajax/get-cart-items").then((response) => {
                products.value = response.data;
                cartItemsLength.value = Object.keys(products.value).length;

                getTotalAmountWithTax(response.data);
                emit('cartProducts', products.value);
            });
        }

        function getTotalAmountWithTax(response) {
            let subTotal = 0;
            let tax = 0;

            let tax_percent = 0;
            let total = 0;
            let total_taxed_amount = 0;

            Object.keys(response).forEach(function (key) {
                subTotal += response[key].price * response[key].qty;
                tax += response[key].options.tax_options_sum_rate;

                let product_tax = response[key].options.tax_options_sum_rate;
                let productWithQuantity = response[key].price * response[key].qty;
                let tax_amount = (productWithQuantity / 100) * product_tax;

                tax_percent += product_tax;
                total_taxed_amount += tax_amount;
                total += productWithQuantity + tax_amount;
            });


            // axios.get(window.appUrl + "/shop/cart/ajax/tax-amount").then((response) => {
                emit('cartAmountWithTaxAmount', {
                    tax: tax,
                    sub_total: subTotal,
                    total: total,
                    taxed_amount: total_taxed_amount
                });
            // });
        }

        function updateQuantity(rowId) {
            let quantity = document.querySelector("#modal-cart-item-quantity").value;
            axios.post(window.appUrl + "/shop/cart/ajax/update-quantity", {
                rowId: rowId,
                qty: quantity
            }).then((response) => {
                getCartItems();

                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Updated successfully',
                    showConfirmButton: false,
                    timer: 1000
                });

                removeModalClass()
            }).catch((errors) => {
                prepare_errors(errors)
            });
        }

        /*
        ========================================
            Popup Custom Modal
        ========================================
        */

        function removeModalClass() {
            openModal.value = false;
        }

        function toggleModalClass(product) {
            cartItem.value = product;
            openModal.value = !openModal.value;
        }

        watch(LoadCardStatus, function (newValue, oldValue) {
            if (newValue) {
                emit('cartAdded');
                getCartItems();
            }
        }, {flush: 'post'});

        function changeInput() {
            getCartItems();
        }

        function clearCartItems() {
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
                        getCartItems();
                        Swal.fire(
                            'Removed!',
                            'All cart items has been cleared.',
                            'success'
                        )
                    }).catch((errors) => {
                        prepare_errors(errors)
                    });
                }
            })
        }

        function removeCartItem(rowId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(window.appUrl + "/shop/cart/ajax/remove", {
                        rowId: rowId
                    }).then((response) => {
                        getCartItems();
                        Swal.fire(
                            'Removed!',
                            'Selected item has been deleted.',
                            'success'
                        )
                    }).catch((errors) => {
                        prepare_errors(errors)
                    });
                }
            })
        }

        const expandAll = () => {
            isExpanded.expand = !isExpanded.expand;
            isExpanded.maxHeight = 'unset';
        }

        onUpdated(() => {
            if (clearCartItem.value)
            {
                products.value = {}
                cartItemsLength.value = 0;
                emit('cartCleared');
            }
        });

        return {
            products,
            cartItem,
            getCartItems,
            clearCartItems,
            testInput,
            LoadCardStatus,
            removeCartItem,
            removeModalClass,
            toggleModalClass,
            updateQuantity,
            cartItemsLength,
            openModal,
            isExpanded,
            expandAll
        }
    }
}
</script>

<style scoped>
/* Custom Modal css start */
.popup_fixed {
    position: fixed;
    top: 50%;
    left: 0%;
    right: 0;
    margin-inline: auto;
    padding: 0 5px;
    -webkit-transform: translateY(-50%) scale(0.6);
    transform: translateY(-50%) scale(0.6);
    z-index: 9992;
    visibility: hidden;
    opacity: 0;
    -webkit-transition: 0.4s;
    transition: 0.4s;
    max-width: -webkit-fit-content;
    max-width: -moz-fit-content;
    max-width: fit-content;
}

.popup_fixed.popup_active {
    visibility: visible;
    opacity: 1;
    -webkit-transform: translateY(-50%) scale(1);
    transform: translateY(-50%) scale(1);
}

.popup_overlay {
    position: fixed;
    height: 100%;
    width: 100%;
    left: 0;
    top: 0;
    background-color: rgba(0, 0, 0, 0.6);
    z-index: 9991;
    visibility: hidden;
    opacity: 0;
    -webkit-transition: 0.4s;
    transition: 0.4s;
}

.popup_overlay.popup_active {
    visibility: visible;
    opacity: 1;
}

.popup_contents {
    max-width: 600px;
    width: -webkit-fit-content;
    width: -moz-fit-content;
    width: fit-content;
    background-color: #fff;
    padding: 30px;
    margin: auto;
    border-radius: 10px;
    position: relative;
    overflow: hidden;
    max-height: calc(100vh - 50px);
    overflow-y: auto;
    scrollbar-color: var(--main-color-one) #e6e6e6;
    scrollbar-width: thin;
}

.popup_contents::-webkit-scrollbar {
    width: 5px;
    height: 8px;
    background-color: #d3d3d3;
    border-radius: 10px;
}

.popup_contents::-webkit-scrollbar-thumb {
    background-color: var(--main-color-one);
    border-radius: 10px;
}

.popup_contents_header {
    padding-right: 20px;
}

.popup_contents_close {
    position: absolute;
    right: 0;
    top: 0;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    height: 40px;
    width: 40px;
    background-color: #f3f3f3;
    color: #ff0000;
    font-size: 18px;
    -webkit-box-shadow: 0 0 10px #f3f3f3;
    box-shadow: 0 0 10px #f3f3f3;
    cursor: pointer;
    -webkit-transition: 0.3s;
    transition: 0.3s;
}

.popup_contents_close:hover {
    background-color: #ff0000;
    color: #fff;
}

.popup_contents_title {
    font-size: 24px;
    line-height: 28px;
    font-weight: 600;
    color: var(--heading-color);
    margin: -4px 0 10px;
}

.popup_contents_para {
    color: var(--paragraph-color);
    font-size: 16px;
    line-height: 24px;
    margin-bottom: 30px;
}

.popup_contents_btn {
    margin-top: 25px;
}

.popup_cart_left__title {
    max-width: 170px;
    font-size: 16px;
    font-weight: 500;
    line-height: 24px;
}

/* Custom Modal css end */
</style>
