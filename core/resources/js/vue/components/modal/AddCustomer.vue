<template>
    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomer">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add a Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="custom-form">
                        <form method="post" @submit.prevent.self="addCustomer($event)">
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <div class="single-input">
                                        <label for="firstName" class="label_title">Full Name</label>
                                        <input type="text" name="name" class="form--control radius-5"
                                               placeholder="Enter First Name" id="firstName">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="single-input">
                                        <label for="PhoneNumber" class="label_title">Phone Number</label>
                                        <input name="phone" type="tel" class="form--control radius-5"
                                               placeholder="Enter Phone Number" id="PhoneNumber">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="single-input">
                                        <label for="emailAddress" class="label_title">Email Address</label>
                                        <input name="email" type="email" class="form--control radius-5"
                                               placeholder="Enter Email Address" id="emailAddress">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="single-input">
                                        <label for="emailAddress" class="label_title">Username</label>
                                        <input name="username" @change="checkUsernameIsAvailableOrNot($event)"
                                               type="text" class="form--control radius-5" placeholder="Enter username."
                                               id="username">
                                        <p v-if="isAvailableUsername"
                                           v-bind:class="'info text-' + isAvailableUsername.type">
                                            {{ isAvailableUsername.msg }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="single-input">
                                        <label for="emailAddress" class="label_title">Country</label>
                                        <select name="country" @change="handleCountryState($event)"
                                                class="form-control">
                                            <option value="">Select a county</option>
                                            <option v-for="country in countries" v-bind:value="country.id"
                                                    :key="country.id">
                                                {{ country.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="single-input">
                                        <label for="firstName" class="label_title">State</label>
                                        <select name="state" @change="handleStateCity($event)" class="form-control">
                                            <option value="">Select a state</option>
                                            <option v-for="state in states" v-bind:value="state.id" :key="state.id">
                                                {{ state.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="single-input">
                                        <label for="lastName" class="label_title">City</label>
                                        <select name="city" class="form-control">
                                            <option value="">Select a city</option>
                                            <option v-for="city in cities" v-bind:value="city.id" :key="city.id">
                                                {{ city.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="single-input">
                                        <label for="customerAddress" class="label_title">Customer Address</label>
                                        <input name="address" type="tel" class="form--control radius-5"
                                               placeholder="Enter Customer Address" id="customerAddress">
                                    </div>
                                </div>
                            </div>

                            <hr class="mb-0"/>

                            <div class="modal-footer border-0">
                                <button id="add-customer-close-modal-button" type="button"
                                        class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel
                                </button>
                                <button id="add-customer-submit-modal-button" type="submit" class="btn btn-primary">
                                    Add Customer

                                    <SubmitButtonLoader v-show="status"/>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import {ref} from "vue";
import Swal from "sweetalert2";
import SubmitButtonLoader from "../button/SubmitButtonLoader.vue";

const status = ref(false);
const countries = ref({});
const states = ref({});
const cities = ref({});
const isAvailableUsername = ref({});

axios.get(window.appUrl + "/api/tenant/v1/get-countries").then((response) => {
    countries.value = response.data.countries;
});

function addCustomer(event) {
    let submitButton = document.querySelector("#add-customer-submit-modal-button");

    submitButton.setAttribute("disabled", true);
    status.value = !status.value

    axios.post(window.appUrl + "/admin-home/pos/customer/add", new FormData(event.target)).then((response) => {
        submitButton.removeAttribute("disabled")

        Swal.fire({
            position: 'top-end',
            icon: response.data.type,
            title: response.data.msg,
            showConfirmButton: false,
            timer: 1500
        });

        document.querySelector("#add-customer-close-modal-button").dispatchEvent(new MouseEvent('click'));
        event.target.reset();
    }).catch((error) => {
        submitButton.removeAttribute("disabled")

        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;

            Object.keys(errors).forEach((field) => {
                errors[field].forEach((message) => {
                    toastr.error(message);
                });
            });
        } else {
            // Other types of errors
        }

        status.value = !status.value;
    });
}

function handleCountryState(event) {
    let country_id = event.target.value;

    if (country_id !== '') {
        axios.get(window.appUrl + "/api/tenant/v1/get-states/" + country_id).then((response) => {
            states.value = response.data.state;
        });
    } else {
        states.value = {};
    }
}

function handleStateCity(event) {
    let state_id = event.target.value;

    if (state_id !== '') {
        axios.get(window.appUrl + "/api/tenant/v1/get-cities/" + state_id).then((response) => {
            cities.value = response.data.cities;
        });
    } else {
        cities.value = {};
    }
}

let timer = null;

function checkUsernameIsAvailableOrNot(event) {
    let username = event.target.value;
    if (username === '') {
        isAvailableUsername.value = {};
        return;
    }

    axios.post(window.appUrl + "/api/tenant/v1/username", {
        username: username
    }).then((response) => {
        isAvailableUsername.value = response.data;
    }).catch((errors) => {
    });
}
</script>
