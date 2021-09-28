require('./bootstrap');

window.Vue = require('vue');
Vue.prototype.$lang = document.documentElement.getAttribute('lang');
Vue.prototype.$currency = document.documentElement.getAttribute('currency');
Vue.prototype.$currencyRate = document.documentElement.getAttribute('currency-rate');
Vue.prototype.$mainCurrency = document.documentElement.getAttribute('main-currency');
Vue.prototype.$device = document.documentElement.getAttribute('device');

export const bus = new Vue();
import VeeValidate from 'vee-validate';
import BootstrapVue from 'bootstrap-vue';
import VueNestable from 'vue-nestable';
Vue.use(VueNestable)

import Slick from 'vue-slick';              //https://github.com/staskjs/vue-slick
Vue.prototype.trans = trans;

import vSelect from 'vue-select';
Vue.component('v-select', vSelect)

let pathname = window.location.pathname;
let segments = pathname.split('/');


Vue.component('promotion-products', require('./components/PromotionProductsComponent.vue'));
Vue.component('recomanded-products',require('./components/RecomandedProductsComponent.vue'));
Vue.component('category',           require('./components/CategoryComponent.vue'));
Vue.component('sale',               require('./components/SaleComponent.vue'));
Vue.component('parameters-filter',  require('./components/ParametersFilterComponent.vue'));
Vue.component('product',            require('./components/ProductComponent.vue'));
Vue.component('new',                require('./components/NewComponent.vue'));
Vue.component('search-box',         require('./components/SearchBoxComponent.vue'));


if (segments.includes('back')) {
    // admin Components - CRM
    // CRMAddProductsToSet
    Vue.component('crm-payment',    require('./components/admin/CRM/CRMPayment.vue'));
    Vue.component('crm-shipping',   require('./components/admin/CRM/CRMShipping.vue'));
    Vue.component('order-search',   require('./components/admin/CRM/OrderSearch.vue'));
    Vue.component('crm-cart',       require('./components/admin/CRM/CRMCart.vue'));

    // admin Components - Collections
    Vue.component('collections',    require('./components/admin/collections/Collections.vue'));
    Vue.component('sets',           require('./components/admin/collections/Sets.vue'));
    Vue.component('products-depth', require('./components/admin/collections/Products.vue'));

    // admin Components - Categories
    Vue.component('categories',     require('./components/admin/Categories.vue'));
    Vue.component('categories-add-new', require('./components/admin/CategoriesAddNew.vue'));

    // admin Components - Blog Categories
    Vue.component('blog-categories', require('./components/admin/blogCategories/Categories.vue'));
    Vue.component('blog-categories-add-new', require('./components/admin/blogCategories/CategoriesAddNew.vue'));

    // admin Components - Autoupload
    Vue.component('autoupload', require('./components/admin/Autoupload.vue'));
    Vue.component('top-bar-autoupload', require('./components/admin/AutouploadTopBar.vue'));
    Vue.component('edit-autoupload', require('./components/admin/AutouploadEdit.vue'));
    Vue.component('create-autoupload', require('./components/admin/AutouploadCreate.vue'));

    // admin Components - Parameters
    Vue.component('create-parameter', require('./components/admin/ParameterCreate.vue'));
    Vue.component('edit-parameter', require('./components/admin/ParameterEdit.vue'));

    // admin Components - Orders
    Vue.component('order-filter', require('./components/admin/OrderFilter.vue'));
    Vue.component('orders', require('./components/admin/Orders.vue'));
    Vue.component('order-admin', require('./components/admin/Order.vue'));
    Vue.component('order-create', require('./components/admin/OrderCreate.vue'));
    Vue.component('order-edit', require('./components/admin/OrderEdit.vue'));

    // admin Components - Returns
    Vue.component('return-filter', require('./components/admin/ReturnFilter.vue'));
    Vue.component('returns', require('./components/admin/Returns.vue'));
    Vue.component('return-create', require('./components/admin/ReturnCreate.vue'));
    Vue.component('return-edit', require('./components/admin/ReturnEdit.vue'));
    Vue.component('return', require('./components/admin/Return.vue'));

    // admin Components - Translations
    Vue.component('top-bar-translations', require('./components/admin/TranslationTopBar.vue'));
    Vue.component('group-translations', require('./components/admin/TranslationGroup.vue'));
    Vue.component('item-translations', require('./components/admin/TranslationItem.vue'));
}

Vue.use(VeeValidate, {
    classes: true,
    classNames: {
        valid: "is-valid",
        invalid: "is-invalid"
    }
});

Vue.directive('select2', {
    inserted(el) {
        $(el).on('select2:select', () => {
            const event = new Event('change', { bubbles: true, cancelable: true });
            el.dispatchEvent(event);
        });

        $(el).on('select2:unselect', () => {
            const event = new Event('change', {bubbles: true, cancelable: true})
            el.dispatchEvent(event)
        })
    },
});

const app = new Vue({
    el: '#cover'
});
