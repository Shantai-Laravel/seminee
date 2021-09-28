<template>
    <div>
        <div class="inputSearch">
            <input type="text" :placeholder="trans.vars.Search.searchTitle" v-model="search" @keyup="findProducts" />
        </div>
        <div class="menuOpenResults">
            <div class="resultItemOne" v-for="product in products">
                <img :src="'/images/products/og/' + product.main_image.src" v-if="product.main_image">
                <a :href="'/' + $lang + '/catalog/' + product.category.alias + '/' + product.alias" class="nameProduct">{{ product.translation.name }}</a>
            </div>
        </div>
        <div class="menuFooter">
            <a :href="'/' + $lang + '/search?search=' + search" class="butt">{{ trans.vars.TehButtons.btnLoadMore }}</a>
        </div>
    </div>

</template>

<script>
    import { bus } from '../app';
    export default{
        data() {
            return {
                search : '',
                products : [],
                sets : [],
            }
        },
        methods: {
            // find products method
            findProducts(){
                if (this.search.length > 2) {
                    axios.post('/'+ this.$lang +'/search-product', {search : this.search})
                        .then(response => {
                            this.products = response.data.products;
                            this.sets = response.data.sets;
                        })
                        .catch(e => {
                            console.log('loading search products error.');
                        })
                }
            }
        }
    }
</script>
