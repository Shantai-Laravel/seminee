<template>

    <div class="row" v-if="ready">
        <div class="col-12">
            <h3>{{ category.translation.name }}</h3>
        </div>
        <div class="col-12">
            <div class="row">
                <a :href="'/' + $lang + '/catalog/' +  product.category.alias + '/' + product.alias" class="col-md-4 oneProduct" v-for="product in products">
                    <img v-if="product.main_image" :src="'/images/products/og/' + product.main_image.src" alt=""/>
                    <div class="itemDescr">
                        <div class="name">
                            <div>{{ product.translation.name }}</div>
                            <div></div>
                        </div>
                        <div class="price" v-if="product.main_price.price > 0">
                            {{ product.main_price.price }} {{ $currency }}
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-12" v-if="page <= last_page">
            <a href="#" class="butt" @click.prevent="handleScroll"><span>{{ trans.vars.TehButtons.viewMore }}</span></a>
        </div>
    </div>

</template>

<script>
import { bus } from '../app';

export default {
    props: ['category', 'product'],
    data() {
        return {
            products: [],
            defaultFilter: [],
            page: 0,
            last_page: 0,
            loading: false,
            categoryFiltredId: this.category.id,
            ready: false,
        };
    },
    mounted() {
        bus.$on('sendInitProducts', data => {
            this.products = this.products.concat(data.data);
        })

        bus.$on('categoryFilter', data => {
            this.categoryFiltredId = data.id;
            this.page = 0;
            this.filterCategory();
        })

        bus.$on('filterProducts', data => {
            this.products = data;
        })

        bus.$on('loadMoreFilterProducts', data => {
            this.products = this.products.concat(data.data);
        })

        this.ready = true;
    },
    methods: {
        handleScroll(e) {
            bus.$emit('handleScroll', {
                last_page: this.last_page
            });
        },
        // load cart products method
        load() {
            this.loading = true;
            axios.post('/ro/categories?page=' + this.page, {
                    mainProductId: this.product,
                    category_id: this.categoryFiltredId
                })
                .then(response => {
                    this.last_page = response.data.products.last_page;
                    this.page = response.data.products.current_page + 1;
                    this.products = this.products.concat(response.data.products.data);
                    this.defaultFilter = response.data.filter;
                    this.loading = false;

                    bus.$emit('setFilterDefault', response.data.filter);
                })
                .catch(e => {
                    console.log('error load products');
                })
        },
        filterCategory() {
            this.loading = true;
            axios.post('/ro/categories?page=' + this.page, {
                    mainProductId: this.product,
                    category_id: this.categoryFiltredId
                })
                .then(response => {
                    this.last_page = response.data.last_page;
                    this.page = response.data.current_page + 1;
                    this.loading = false;
                    this.products = response.data.data;
                })
                .catch(e => {
                    console.log('error load products');
                })
        },

    },
}
</script>
