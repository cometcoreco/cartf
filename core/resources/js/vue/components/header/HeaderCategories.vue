<style>
.dashboard_posSystem__category__menu .submenu li.active .submenu {
    background: rgba(var(--main-color-one-rgb), .1);
}

.dashboard_posSystem__category__menu ul li.active > a {
    background: var(--main-color-one);
    color: #fff;
}

.dashboard_posSystem__category__menu ul li.active ul li a {
    background-color: inherit;
    color: inherit;
}
</style>

<template>
    <div class="dashboard_posSystem__category__nav category__nav radius-10" @click="handleCategoryNavClick($event)">
        <p class="dashboard_posSystem__category__nav__main"><i class="las la-th-large"></i>
            All Categories
        </p>
    </div>

    <div class="dashboard_posSystem__category__menu category__menu radius-10 mt-3" v-show="cateSlide">
        <div class="dashboard_posSystem__category__menu__header">
            <div class="dashboard_posSystem__category__menu__header__input">
                <input v-model="search_text" @keyup="searchCategory()" type="text" class="form--control" placeholder="Find a Category">
                <button class="searchIcon"><i class="las la-search"></i></button>
            </div>
        </div>
        <div class="dashboard_posSystem__category__menu__inner">
            <ul class="pos-categories">
                <li v-for="category in AllCategories" :key="category.id"
                    v-bind:data-category="'category_id=' + category.id"
                    @click="ToggleSubCategoryMenu($event)"
                    v-bind:class="(category.sub_categories.length > 0) ? 'has-children category ' + (category.id === activeMenus.category.id ? 'active' : '') : ''"
                    v-bind:data-category-data="JSON.stringify(category)"
                    v-bind:data-category-type="'category'"
                >
                    <a href="#1">{{ category.name }}</a>
                    <!-- Load all sub categories from this category if sub category are exist on this collection -->
                    <ul class="submenu subCategoryWarp d-none" :class="'subCategoryWarp-'+category.id" v-if="category.sub_categories ?? false">
                        <li @click="ToggleSubCategoryMenu($event, 'childCategoryWrap')"
                            v-for="sub_category in category.sub_categories"
                            :key="sub_category.id" v-bind:data-category="'sub_category_id=' + sub_category.id"
                            v-bind:class="sub_category.child_categories.length > 0 ? 'has-children sub-category' : ''"
                            v-bind:data-category-data="JSON.stringify(sub_category)"
                            v-bind:data-category-type="'sub_category'"
                        >
                            <a href="#1">{{ sub_category.name }}</a>

                            <!-- Child category -->
                            <ul class="submenu childCategoryWrap d-none"
                                v-if="sub_category.child_categories.length > 0">
                                <li v-for="child_category in sub_category.child_categories"
                                    v-bind:data-category="'child_category_id=' + child_category.id"
                                    key="{{ child_category.id }}"
                                    v-bind:data-category-data="JSON.stringify(child_category)"
                                    v-bind:data-category-type="'child_category'"
                                    v-bind:class="'child-category' + category.id === activeMenus.category.id ? 'active' : ''"
                                >
                                    <a href="#1" @click="handleChildCategoryClick($event)">{{ child_category.name }}</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import {ref, inject, watch, toRef, reactive} from "vue"
import axios from "axios"

export default {
    name: "HeaderCategories",
    setup(props, {emit}) {
        const search_text = ref('');
        const cateSlide = ref(false);
        const AllCategories = ref({});
        const activeMenus = reactive({category: '', sub_category: '', child_category: ''});

        function fetchAllCategories() {
            axios.get(window.appUrl + "/api/tenant/v1/all-categories").then((response) => {
                AllCategories.value = response.data.data;
            }).catch((errors) => {
                // prepare_errors(errors)
            });
        }

        function handleCategoryNavClick(event) {
            cateSlide.value = !cateSlide.value;

            let categorySlide = event.target.closest('.category__parent').querySelector('.category__menu');
            categorySlide.classList.toggle('show');

            if (categorySlide.classList.contains('show')) {
                document.querySelector('.category_overlay').classList.add('show');
            } else {
                document.querySelector('.category_overlay').classList.remove('show');
            }
        }

        function ToggleSubCategoryMenu(event, className = 'subCategoryWarp') {
            const currentEl = event.target;
            const type = currentEl.closest('li').getAttribute("data-category-type");
            let name = JSON.parse(currentEl.closest('li').getAttribute('data-category-data'));
            const oldActiveMenus = activeMenus.value;

            currentEl.closest('li.has-children').classList.toggle('down-arrow');

            if (type === 'category') {
                activeMenus.value = {"category": name};

                removeAllActiveMenus('category', currentEl);
                removeAllActiveMenus('sub-category', currentEl);
                removeAllActiveMenus('child-category', currentEl);
            }
            else if(type === "sub_category") {
              activeMenus.value = {"category" : oldActiveMenus.category, "sub_category": name};

              removeAllActiveMenus('sub-category', currentEl);
              removeAllActiveMenus('child-category', currentEl);
            }

            emit('emitCategorySelected', activeMenus);

            let parentElement = currentEl.parentElement;
            emit("emitSearch", parentElement.getAttribute("data-category"));
            parentElement.querySelector("." + className)?.classList?.toggle("d-none");

            currentEl.closest('li').classList.add('active');

            return true;
        }

        function removeAllActiveMenus(type, currentEl) {
            let allCategories = "";

            if (type === 'category') {
                allCategories = document.querySelectorAll('.pos-categories li.category.active');
            } else if (type === 'sub-category') {
                allCategories = document.querySelectorAll('.pos-categories li.sub-category.active');
            } else if (type === 'child-category') {
                allCategories = document.querySelectorAll('.pos-categories li.child-category.active');
            }

            if (allCategories.length > 0) {
                allCategories.forEach(function (element, key) {
                    element.classList.remove('active');
                    element.classList.remove('down-arrow');
                })

                document.querySelectorAll('.subCategoryWarp').forEach(function (element, key) {
                    if (type === 'category')
                    {
                        let category_id = currentEl.closest('li.category').getAttribute("data-category").replace('category_id=','');

                        if (!element.classList.contains(`subCategoryWarp-${category_id}`))
                        {
                            element.classList.add('d-none');
                        }
                    }
                })
            }
        }

        function handleChildCategoryClick(event) {
            const currentEl = event.target;

            let name = JSON.parse(currentEl.closest('li').getAttribute('data-category-data'));
            const oldActiveMenus = activeMenus.value;

            activeMenus.value = {"category": oldActiveMenus.category, "sub_category": name, "child_category": name};

            emit('emitCategorySelected', activeMenus);

            let parentElement = currentEl.parentElement;
            emit("emitSearch", parentElement.getAttribute("data-category"));

            removeAllActiveMenus('child-category');
            currentEl.closest('li').classList.add('active');
        }

        const fetchProductData = inject('fetchProductData');
        const selectedCategories = inject('selectedCategories');

        const searchCategory = () => {
            let categories = AllCategories.value;

            AllCategories.value = categories.filter((category) => {
                return category.name.toLowerCase().includes(search_text.value.toLowerCase());
            });

            if (search_text.value.length < 1)
            {
                fetchAllCategories();
                fetchProductData();
                selectedCategories.value = {};
            }
        }

        return {
            fetchAllCategories,
            cateSlide,
            AllCategories,
            handleCategoryNavClick,
            ToggleSubCategoryMenu,
            handleChildCategoryClick,
            activeMenus,
            search_text,
            searchCategory,
            fetchProductData
        };
    },
    mounted() {
        this.fetchAllCategories()
    }
}
</script>
