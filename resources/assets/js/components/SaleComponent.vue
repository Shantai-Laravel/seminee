<template>

    <div class="row" v-if="ready">
        <div class="col-12">
            <h3>Sale</h3>
        </div>
        <div class="col-12">
            <div class="row">
                <a :href="'/' + $lang + '/catalog/' +  product.category.alias + '/' + product.alias" class="col-md-4 oneProduct" v-for="product in products">
                    <div class="salesReduce" v-if="product.discount">-{{ product.discount }}%</div>
                    <img v-if="product.main_image" :src="'/images/products/og/' + product.main_image.src" alt=""/>
                    <div class="itemDescr">
                        <div class="name">
                            <div>{{ product.translation.name }}</div>
                            <div></div>
                        </div>
                        <div class="price">
                            {{ product.main_price.price }} {{ $currency }}
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-12" v-if="page <= last_page">
            <a href="#" class="butt" @click.prevent="showMore"><span>mai multe</span></a>
        </div>
    </div>

</template>

<script>
import { bus } from '../app';

export default {
    data() {
        return {
            ready: false,
            products: [],
            page: 0,
            last_page: 0,
            loading: false,
        };
    },
    mounted() {
        this.load();
    },
    methods: {
        // load cart products method
        load() {
            axios.post('/ro/get-sale-products?page=' + this.page,)
                .then(response => {
                    this.last_page = response.last_page;
                    this.page = response.current_page + 1;
                    this.products = this.products.concat(response.data.data);
                    this.loading = false;
                    this.ready = true;
                })
                .catch(e => {
                    console.log('error load products');
                })
        },
        showMore(){
            if (this.loading == false) {
                if (this.page <= this.last_page) {
                    this.load();
                }
            }
            return;
        },

    },
}
</script>
