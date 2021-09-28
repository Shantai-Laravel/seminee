<template>
    <div class="productDescr">
        <a :href="'/' + $lang + '/catalog/' + product.category.alias + '/' + product.alias">
            {{ product.translation.name }}
        </a>
        <div class="price">
            <span>{{ product.personal_price.price }} {{ $currency }}</span>
            <span v-if="product.discount > 0">{{ product.personal_price.old_price }} {{ $currency }}</span>
        </div>
        <div class="productOptions">


            <wish-button :product="product"></wish-button>

            <div class="selectBox">
                <div class="optionSelectedBox">{{ trans.vars.DetailsProductSet.size }}</div>
                <div class="options">
                    <div class="sizeGide" data-toggle="modal" data-target="#modalSize">
                        {{ trans.vars.DetailsProductSet.sizeGuide }}
                    </div>
                    <div :class="['containerSize', subproduct.stoc == 0 ? 'soldOut' : '']" v-for="subproduct in product.subproducts">
                        <input type="radio" name="size1" :value="subproduct.parameter_value.translation.name" @change="chooseSubproduct(subproduct.id)" />
                        <span class="option">
                            <span class="optionSize">{{ subproduct.parameter_value.translation.name }}</span>
                            <span v-if="subproduct.stoc >= 5">{{ trans.vars.DetailsProductSet.inStock }}</span>
                            <span v-if="subproduct.stoc < 5 && subproduct.stoc > 0">{{ trans.vars.DetailsProductSet.inStockFewItems }}</span>
                            <span v-if="subproduct.stoc == 0">{{ trans.vars.DetailsProductSet.notInStock }}</span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="iconCart buttAddToCart" v-if="mode" @click="addToCartWish"></div>
            <div class="iconCart buttAddToCart" v-else @click="addToCart"></div>
        </div>
    </div>
</template>

<script>
    import { bus } from '../app';

    export default {
        props: ['id', 'product', 'mode'],
        data() {
            return {
                successCartModal : false,
                successWishModal : false,
                subproduct: [],
                checkProperty: false,
                isSubproduct: false,
            };
        },
        mounted(){

        },
        methods: {
            // add product to favorites method
            addToFavorites(){
                axios.post('/'+ this.$lang +'/add-to-favorites', { product_id : this.product.id })
                    .then(response => {
                        bus.$emit('updateWishBox', response.data.data);
                        if (response.data.status == 'false') {
                            this.successWishModal = 'Produsul a fost adaugat in favorite cu succes.';
                        }else{
                            this.successWishModal = 'Produsul a fost sters din favorite.';
                        }
                    })
                    .catch(e => {  console.log('add favorites error') });
            },
            chooseSubproduct(subproductId){
                this.subproduct = subproductId;
            },
            addToCart(){
                axios.post('/'+ this.$lang +'/add-product-to-cart', {
                        productId   : this.product.id,
                        subproductId : this.subproduct,
                    })
                    .then(response => {
                        bus.$emit('updateCartBox', {data : response.data});
                        bus.$emit('updateCart', this.subproduct.code);
                        $('.buttCart').addClass('flash');
                        setTimeout(function(){
                            $('.buttCart').removeClass('flash');
                        }, 500);
                    })
                    .catch(e => {
                      this.errors.push(e)
                  });
            },
            addToCartWish(){
                axios.post('/'+ this.$lang +'/add-product-to-cart-from-wish', {
                        productId   : this.product.id,
                        subproductId : this.subproduct,
                    })
                    .then(response => {
                        bus.$emit('updateCartBox', {data : response.data.carts});
                        bus.$emit('updateWishBox', response.data.wish);
                        bus.$emit('updateWishList', response.data.wish);
                    })
                    .catch(e => {
                      this.errors.push(e)
                  });
            }
        }
    }
</script>
