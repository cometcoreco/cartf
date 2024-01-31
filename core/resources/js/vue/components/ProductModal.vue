<template>
    <div class="popup-fixed interview-popup">
        <div class="popup-contents" v-if="product">
            <span class="popup-contents-close popup-close" @click="closeModal"> <i class="las la-times"></i> </span>

            <div class="modal-body">
                <div class="editProduct">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="dashboard_posSystem__category__nav__inner">
                                <p class="dashboard_posSystem__category__nav__item">{{
                                        product.product.category.name
                                    }}</p>
                                <p class="dashboard_posSystem__category__nav__item">{{
                                        product.product.sub_category.name
                                    }}</p>
                                <p class="dashboard_posSystem__category__nav__item">
                                    {{ childCategoryString(product.product.child_category) }}</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="editProduct__thumb">
                                <div class="editProduct__thumb__main">
                                    <img v-bind:src="productImage" alt="" id="product-image-field"
                                         v-bind:data-src="product.product.image">
                                </div>
                            </div>

                            <div
                                class="shop-details-thumb-wrapper text-center d-flex gap-2 align-content-center justify-content-center">
                                <div class="shop-details-thums bg-item-five"
                                     v-for="productGallery in product.product.gallery_images">
                                    <img v-bind:src="productGallery.image" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="editProduct__contents">
                                <h5 class="editProduct__contents__title"><a href="{{ product.product_url }}">
                                    {{ product.product.name }}</a></h5>
                                <p>
                                    Brand: {{ product.product?.brand?.name }}
                                </p>

                                <p class="editProduct__contents__price mt-2">
                                    <b id="price"
                                       v-bind:data-main-price="product.product.sale_price">
                                        {{ getCurrencySymbolWithAmount(productPrice) }}
                                    </b>
                                    <s>{{ getCurrencySymbolWithAmount(product.product.price) }}</s></p>

                                <div class="text-success mt-2" v-if="product.product?.inventory?.stock_count > 0">
                                    In Stock ({{ product.product?.inventory?.stock_count }})
                                </div>
                                <div class="text-danger mt-2" v-if="product.product?.inventory?.stock_count < 1">
                                    Out of stock
                                </div>

                                <div class="valueInput value-input-area mt-4" v-if="product.productSizes">
                  <span class="valueInput__head">
                      <strong> Size: </strong>
                      <input class="form--input value-color" name="color" type="text" value="" readonly>
                      <input type="hidden" id="selected_size">
                  </span>
                                    <ul class="valueInput__list size-lists" data-type="Size">
                                        <li @click="handleSizeListClick($event)" class="text-center"
                                            v-for="size in product.productSizes"
                                            v-bind:data-value="size.id"
                                            v-bind:data-display-value="size.name"
                                        > {{ size.name }}
                                        </li>
                                    </ul>
                                </div>

                                <div class="valueInput value-input-area mt-4" v-if="product.productColors">
                  <span class="valueInput__head">
                      <strong> Color: </strong>
                      <input class="form--input value-color" name="color" type="text" value="" readonly>
                      <input type="hidden" id="selected_color">
                  </span>
                                    <ul class="valueInput__list size-lists" data-type="Color">
                                        <li @click="handleSizeListClick($event)" class="text-center"
                                            v-for="color in product.productColors"
                                            :style="{background: color.color_code}"
                                            v-bind:data-value="color.id"
                                            v-bind:data-display-value="color.name"
                                        ></li>
                                    </ul>
                                </div>

                                <div class="value-input-area margin-top-15 attribute_options_list"
                                     v-for="(attribute, attr_name) in product.available_attributes">
                  <span class="input-list attr_span">
                      <strong class="color-light">{{ attr_name }}:</strong>
                      <input class="form--input value-size" type="text" value="" readonly>
                      <input type="hidden" id="selected_attribute_option" name="selected_attribute_option">
                  </span>

                                    <ul class="size-lists" v-bind:data-type="attr_name">
                                        <li @click="handleSizeListClick" v-for="option in attribute" class=""
                                            v-bind:data-value="option"
                                            v-bind:data-display-value="option">
                                            {{ option }}
                                        </li>
                                    </ul>
                                </div>


                                <div class="btn-wrapper btn_flex justify-content-start mt-4">
                                    <div class="product__quantity m-0 radius-5">
                                        <span @click="productAmount--" class="substract">
                                            <i class="las la-minus"></i>
                                        </span>
                                        <input class="product__quantity__input radius-5" type="number"
                                               :value="productAmount">
                                        <span @click="productAmount++" class="plus">
                                            <i class="las la-plus"></i>
                                        </span>
                                    </div>
                                    <a href="#" class="posBtn btn_bg_1 radius-5"
                                       @click.prevent="variantProductAddToCart(product.product)">Add to cart</a>
                                    <div class="text-success mt-2" v-if="variantStock > 0"> In Stock ({{
                                            variantStock
                                        }})
                                    </div>
                                    <div class="text-danger mt-2" v-if="outOfStock == 1"> Out of stock</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="popup-overlay" @click="closeModal"></div>

</template>

<script>
import {reactive, ref, toRef, watch} from "vue";
import MD5 from "../lib/md5";
import Swal from "sweetalert2";

export default {
    name: "product-modal",
    props: {
        productDetails: false
    }, mounted() {
        // this.$refs.sizeLists
    },
    setup: (props, {emit}) => {
        const product = toRef(props, 'productDetails');
        const productPrice = ref(product.value.product?.sale_price);
        const productImage = ref(product.value.product?.image);
        const variantStock = ref(0);
        const outOfStock = ref(0);
        const productAmount = ref(1);

        const selectedAttributes = reactive({
            color: "",
            size: ""
        });

        watch(() => productAmount.value, (newValue, oldValue) => {
            productAmount.value = newValue < 1 ? 1 : newValue
        });

        watch(() => props.productDetails, (newValue, oldValue) => {
            productPrice.value = newValue.product?.sale_price;
            productImage.value = newValue.product?.image;
        })

        function cart_view_selected_options() {
            let available_variant_types = [];
            let selected_options = {};
            let available_options = document.querySelectorAll('.value-input-area')

            // get all selected attributes in {key:value} format
            available_options.forEach(function (element, key) {
                let selected_option = element.querySelector('li.active');
                let type = selected_option?.closest('.size-lists')?.getAttribute('data-type');
                let value = selected_option?.getAttribute('data-display-value');

                if (type) {
                    available_variant_types.push(type);
                }

                if (type && value) {
                    selected_options[type] = value;
                }
            });

            let ordered_data = {};
            let selected_options_keys = Object.keys(selected_options).sort();
            selected_options_keys.map(function (e) {
                ordered_data[e] = selected_options[e];
            });

            return ordered_data;
        }

        function getAttributesForCart() {
            let selected_options = cart_view_selected_options();
            let hashed_key = getSelectionHash(selected_options);

            // if selected attribute set is available
            if (product.value.additional_info_store[hashed_key]) {
                return product.value.additional_info_store[hashed_key]['pid_id'];
            }

            // if selected attribute set is not available
            if (Object.keys(selected_options).length) {
                toastr.error("Attribute not available")
            }

            return '';
        }

        function variantProductAddToCart(product) {
            let selected_size = selectedAttributes.size;
            let selected_color = selectedAttributes.color;

            let pid_id = getAttributesForCart();

            // let product_id = product.prd_id;
            let quantity = Number(document.querySelector('.product__quantity__input').value);

            // if selected attribute is a valid product item
            if (validateSelectedAttributes()) {
                let csrf_token = document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content");
                let data = new FormData();

                data.append("product_id", product.id);
                data.append("_token", csrf_token);
                data.append("quantity", quantity);
                data.append("pid_id", pid_id);
                data.append("product_variant", pid_id);
                data.append("selected_size", selected_size);
                data.append("selected_color", selected_color);
                data.append("attribute", null);

                send_ajax_request("post", data, window.appUrl + "/shop/cart/ajax/add-to-cart", () => {
                }, (data) => {
                    //todo:: emit an event
                    emit('cartAdded');
                    closeModal();

                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Item added successfully',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }, (errors) => {
                    prepare_errors(errors)
                })
            } else {
                toastr.error('Select all attribute to proceed');
            }
        }

        function addToCartButton(product) {
            let csrf_token = document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content");
            let data = new FormData();

            data.append("product_id", product.prd_id);
            data.append("_token", csrf_token);
            data.append("quantity", 1);
            data.append("pid_id", null);
            data.append("product_variant", null);
            data.append("selected_size", null);
            data.append("selected_color", null);
            data.append("attribute", null);

            send_ajax_request("post", data, window.appUrl + "/shop/product/cart/add", () => {
            }, (data) => {
                //todo:: emit an event
                emit('cartAdded');
                closeModal();

                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Item added successfully',
                    showConfirmButton: false,
                    timer: 1500
                });
            }, (errors) => {
                prepare_errors(errors)
            })
        }

        function childCategoryString(childCategories) {
            let categories = "";

            for (let childCat of childCategories) {
                categories += childCat.name + ' , ';
            }

            categories = categories.substring(0, categories.length - 2)

            return categories;
        }

        function closeModal() {
            document.querySelector('.popup-overlay').classList.remove('popup-active');
            document.querySelector('.interview-popup').classList.remove('popup-active');
        }

        function handleSizeListClick(event) {
            // initially, disable all buttons

            // this line will take current element
            let el = event.target;
            // this line take display value like-> small, big
            let value = el.getAttribute('data-display-value');

            // this line is refer parent element
            let parentWrap = el.parentElement.parentElement;

            // get all previous element from current sibling and remove all active class
            el.previousElementSibling?.classList.remove('active');
            // add active class to selected element
            el.classList.add('active');
            // get all next sibling elements and remove all active class
            el.nextElementSibling?.classList.remove('active');

            // add value to input for display frontend value like -> Small , Big
            parentWrap.querySelector('input[type=text]').value = value;
            // this will store value on hidden input and this value will work with selecting correct variant
            parentWrap.querySelector('input[type=hidden]').value = el.getAttribute('data-value');

            productDetailSelectedAttributeSearch(el);

            if (el.parentElement.getAttribute('data-type').toLowerCase() === 'size') {
                selectedAttributes.size = el.getAttribute('data-value');
            } else if (el.parentElement.getAttribute('data-type').toLowerCase() === 'color') {
                selectedAttributes.color = el.getAttribute('data-value');
            }
        }

        function validateSelectedAttributes() {
            let selected_options = cart_view_selected_options();
            let hashed_key = getSelectionHash(selected_options);

            // validate if product has any attribute
            if (product.value.product_inventory_set.length) {
                if (!Object.keys(selected_options).length) {
                    return false;
                }

                if (!product.value.additional_info_store[hashed_key]) {
                    return false;
                }

                return !!product.value.additional_info_store[hashed_key]['pid_id'];
            }

            return true;
        }

        function productDetailSelectedAttributeSearch(selected_item) {

            let availableOptions = document.querySelectorAll('.size-lists li');

            availableOptions.forEach(function (value, key) {
                availableOptions[key].classList.add('disabled-option');
            })

            /*
            * search based on all selected attributes
            *
            * 1. get all selected attributes in {key:value} format
            * 2. search in attribute_store for all available matches
            * 3. display available matches (keep available matches selectable, and rest as disabled)
            * */

            let available_variant_types = [];
            let selected_options = {};
            let available_options = document.querySelectorAll('.value-input-area')

            // get all selected attributes in {key:value} format
            available_options.forEach(function (element, key) {
                let selected_option = element.querySelector('li.active');
                let type = selected_option?.closest('.size-lists')?.getAttribute('data-type');
                let value = selected_option?.getAttribute('data-display-value');


                if (type) {
                    available_variant_types.push(type);
                }

                if (type && value) {
                    selected_options[type] = value;
                }
            });

            syncImage(view_selected_options(selected_options));
            syncPrice(view_selected_options(selected_options));
            syncStock(view_selected_options(selected_options));

            // search in attribute_store for all available matches
            let available_variants_selection = [];
            let selected_attributes_by_type = {};

            product.value.product_inventory_set.map(function (arr) {
                let matched = true;

                Object.keys(selected_options).map(function (type) {
                    if (arr[type] !== selected_options[type]) {
                        matched = false;
                    }
                })

                if (matched) {
                    available_variants_selection.push(arr);

                    // insert as {key: [value, value...]}
                    Object.keys(arr).map(function (type) {
                        // not array available for the given key
                        if (!selected_attributes_by_type[type]) {
                            selected_attributes_by_type[type] = []
                        }

                        // insert value if not inserted yet
                        if (selected_attributes_by_type[type].indexOf(arr[type]) <= -1) {
                            selected_attributes_by_type[type].push(arr[type]);
                        }
                    })
                }
            });


            // selected item not contain product then de-select all selected option hare
            if (Object.keys(selected_attributes_by_type).length == 0) {
                // get all active elements
                let activeElements = document.querySelectorAll('.size-lists li.active');
                let disabledOptions = document.querySelectorAll('.size-lists li.disabled-option');
                activeElements.forEach(function (value, key) {
                    activeElements[key].classList.remove('active');

                    let sizeItem = activeElements[key].parentElement.parentElement;

                    sizeItem.querySelector('input[type=hidden]').value = '';
                    sizeItem.querySelector('input[type=text]').value = '';
                });

                disabledOptions.forEach(function (value, key) {
                    disabledOptions[key].classList.remove('disabled-option');
                });

                let el = selected_item;
                let value = el.getAttribute('data-displayValue');

                el.classList.add("active");

                document.querySelector(this).querySelector('input[type=hidden]').val(value);
                document.querySelector(this).querySelector('input[type=text]').val(el.getAttribute('data-value'));

                productDetailSelectedAttributeSearch();
            }

            // keep only available matches selectable
            Object.keys(selected_attributes_by_type).map(function (type) {
                // let availableOptionsForDeselect = document.querySelector('.size-lists[data-type="' + type + '"] li');
                // availableOptionsForDeselect.forEach(function (value, key){
                //   availableOptionsForDeselect[key].classList.add('disabled-option');
                // });
                $('.size-lists[data-type="' + type + '"] li').addClass('disabled-option');

                // make buttons selectable for the available options
                selected_attributes_by_type[type].map(function (value) {
                    let available_buttons = $('.size-lists[data-type="' + type + '"] li[data-display-value="' + value + '"]');
                    available_buttons.map(function (key, el) {
                        $(el).removeClass('disabled-option');
                    })
                });
            });
            // todo check is empty object
            // selected_attributes_by_type
        }

        function syncImage(selected_options) {
            //todo fire when attribute changed
            let hashed_key = getSelectionHash(selected_options);


            let product_image_el = document.querySelector("#product-image-field");
            let img_original_src = product_image_el.getAttribute('data-src');

            // if selection has any image to it
            if (product.value.additional_info_store[hashed_key]) {
                let attribute_image = product.value.additional_info_store[hashed_key].image;
                if (attribute_image) {
                    productImage.value = attribute_image;
                }
            } else {
                productImage.value = img_original_src;
            }
        }

        function syncPrice(selected_options) {
            let hashed_key = getSelectionHash(selected_options);

            let product_price_el = document.querySelector('#price');
            let product_main_price = Number(product_price_el.getAttribute('data-main-price')).toFixed(2);

            // if selection has any additional price to it
            if (product.value.additional_info_store[hashed_key]) {
                let attribute_price = product.value.additional_info_store[hashed_key]['additional_price'];
                if (attribute_price) {
                    productPrice.value = Number(product_main_price) + Number(attribute_price);
                }
            } else {
                productPrice.value = product_main_price;
            }
        }

        function syncStock(selected_options) {
            let hashed_key = getSelectionHash(selected_options);

            // if selection has any size and color to it

            if (product.value.additional_info_store[hashed_key]) {
                let stock_count = product.value.additional_info_store[hashed_key]['stock_count'];

                if (Number(stock_count) > 0) {
                    variantStock.value = stock_count;
                    outOfStock.value = 2;
                } else {
                    outOfStock.value = 1;
                }
            } else {
            }
        }

        function attributeSelected() {
            let total_options_count = $('.size-lists').length;
            let selected_options_count = $('.size-lists li.active').length;
            return total_options_count === selected_options_count;
        }

        function view_selected_options(selected_options) {
            let ordered_data = {};
            let selected_options_keys = Object.keys(selected_options).sort();

            selected_options_keys.map(function (e) {
                ordered_data[e] = selected_options[e];
            });

            return ordered_data;
        }

        function getSelectionHash(selected_options) {
            return MD5(JSON.stringify(selected_options));
        }

        return {
            closeModal,
            variantProductAddToCart,
            product,
            childCategoryString,
            handleSizeListClick,
            productPrice,
            productImage,
            variantStock,
            outOfStock,
            selectedAttributes,
            productAmount
        };
    }
}
</script>

<style scoped>
/* Popup Modal */
.popup-fixed {
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

.popup-fixed.popup-active {
    visibility: visible;
    opacity: 1;
    -webkit-transform: translateY(-50%) scale(1);
    transform: translateY(-50%) scale(1);
}

.popup-overlay {
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

.popup-overlay.popup-active {
    visibility: visible;
    opacity: 1;
}

.popup-contents {
    max-width: 900px;
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

.popup-contents::-webkit-scrollbar {
    width: 5px;
    height: 8px;
    background-color: #d3d3d3;
    border-radius: 10px;
}

.popup-contents::-webkit-scrollbar-thumb {
    background-color: var(--main-color-one);
    border-radius: 10px;
}

.popup-contents-close {
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

.popup-contents-close:hover {
    background-color: #ff0000;
    color: #fff;
}


/* Quantity Css */
.product__quantity {
    position: relative;
    z-index: 2;
    margin: 0 auto;
    width: 100px;
    text-align: center;
}

.product__quantity .substract,
.product__quantity .plus {
    color: var(--light-color);
    z-index: 9;
    cursor: pointer;
    position: absolute;
    left: 5px;
    top: 50%;
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
    background: #fff;
    -webkit-transition: all 0.3s;
    transition: all 0.3s;
    color: var(--heading-color);
}

.product__quantity .plus {
    left: auto;
    right: 5px;
}

.product__quantity__input {
    width: 100px;
    height: 35px;
    border: 1px solid var(--border-color);
    color: var(--heading-color);
    font-size: 16px;
    font-weight: 500;
    line-height: 18px;
    text-align: center;
    -moz-appearance: textfield;
    padding: 0 20px;
}

.product__quantity__input::-webkit-outer-spin-button, .product__quantity__input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    -moz-appearance: textfield;
}

.size-lists {
    padding: 0;
}

.size-lists li {
    cursor: pointer;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    font-size: 16px;
    height: 35px;
    width: 45px;
    border: 1px solid #999;
    border-radius: 5px;
}

.size-lists li:is(:hover, .active) {
    border-color: var(--main-color-one);
    color: var(--main-color-one);
}

.value-color,.value-size{
    border: none;
}
.attr_span{
    display: flex;
    gap: 10px;
}
</style>
