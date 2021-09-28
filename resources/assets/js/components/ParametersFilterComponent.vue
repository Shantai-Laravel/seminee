<template>
    <div class="filterOpen">
        <div class="row justify-content-between align-items-center">
            <div class="col-auto">
                <div class="filter2">
                    {{ trans.vars.DetailsProductSet.filter }}
                </div>
            </div>
            <div class="col-auto">
                <div class="closeFilter"></div>
            </div>
        </div>

        <div class="row heightFiltr"  v-for="parameter in category.params" v-if="parameter.property.in_filter == 1">

            <!-- <div class="col-12 optionFiltr blocOne">
                <div class="opt submenuBcgMinus">
                    {{ trans.vars.DetailsProductSet.price }} ({{ $currency }})
                </div>
                <div class="optionFiltrOpen inputsFilter row">
                    <input type="number" name="curentPrice" v-model="priceMin" placeholder="min">
                    <input type="number" name="curentPrice" v-model="priceMax" placeholder="max">
                    <input type="submit" value="ok" v-on:click="filterPrice()">
                </div>
            </div> -->

            <input type="hidden" class="category-id" :value="category.id" />

            <div class="col-12 optionFiltr" v-if="children.length > 0">
                <div class="opt submenuBcgMinus">{{ trans.vars.DetailsProductSet.subcategories }}</div>
                <div class="optionFiltrOpen size1" :style="'display: block'">
                    <div class="row">
                        <div class="col-12">
                            <label class="containerRadio" v-for="child in children">
                                {{ child.translation.name }}
                                <input type="checkbox" class="filter-checkbox-category" @change="filterCategory(child.id)">
                                <span class="checkmark color"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 optionFiltr" v-if="parameter.property.multilingual == 1">
                <div class="opt submenuBcgMinus">{{ parameter.property.translation.name }}</div>
                <div class="optionFiltrOpen size1" :style="'display: block'">
                    <div class="row">
                        <div class="col-12">
                            <label class="containerRadio" v-for="value in parameter.property.parameter_values" v-if="parameter.property.multilingual == 1 && filter.includes(value.id)">
                                {{ value.translation.name }}
                                <input type="checkbox" class="filter-checkbox-category" @change="setProperty" :name="parameter.property.id" :value="value.id">
                                <span class="checkmark color"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 optionFiltr" v-if="parameter.property.multilingual == 0">
                <div class="opt">{{ parameter.property.trans_data.name }}</div>
                <div class="optionFiltrOpen size1">
                    <div class="row">
                        <div class="col-12">
                            <label class="containerRadio" v-for="value in parameter.property.parameter_values" v-if="parameter.property.multilingual == 0 && filter.includes(value.id)">
                                {{ value.trans_data.name }}
                                <input type="checkbox" class="filter-checkbox-category" @change="setProperty" :name="parameter.property.id" :value="value.id">
                                <span class="checkmark color"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-center">
            <div class="col-6">
                <div class="butt btnTransparent" @click="removeFilter">
                    {{ trans.vars.TehButtons.clearFilter }}
                </div>
            </div>
            <div class="col-6">
                <div class="butt closeThis">
                    {{ trans.vars.TehButtons.btnClose }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { bus } from '../app';

    export default {
        props: ['category'],
        data() {
            return {
                children: [],
                filter: [],
                priceMin:   0,
                priceMax:   1000000,
                categories: [],
                properties: [],
                page: 0,
                last_page: 0,
                loading: false,
                products: false,
                repeted: false,
            };
        },
        mounted(){
            this.setDefaultCategories();
            this.filterProducts();
            this.setDefaultFilter();

            bus.$on('handleScroll', data => {
                this.handleScroll(data)
            })

            this.children = this.category.children;
        },
        methods: {
            setDefaultCategories(){
                let ret = [];
                if (this.category.children.length > 0) {
                    this.category.children.forEach(function(entry, key){
                        ret[key] = entry.id;
                    })
                    this.categories = ret;
                }else {
                    this.categories = [this.category.id];
                }
            },
            filterCategory(id){
                this.categories = [id];
                this.page = 0;
                this.filterProducts();
            },
            filterPrice(){
                this.page = 0;
                this.filterProducts();
            },
            filterProducts(loadMore = 0){
                this.loading = true;
                axios.post('/'+ this.$lang +'/filter?page=' + this.page, {
                        priceMin    :  this.priceMin,
                        priceMax    :  this.priceMax,
                        categories  : this.categories,
                        category    :   this.category,
                        properties  : this.properties,
                    })
                    .then(response => {
                        this.last_page = response.data.last_page;
                        this.page = response.data.current_page + 1;
                        if (this.products === false) {
                            this.products = true;
                            bus.$emit('sendInitProducts', response.data);
                        }else{
                            if (loadMore == 1) {
                                bus.$emit('loadMoreFilterProducts', response.data);
                            }else{
                                bus.$emit('filterProducts', response.data.data);
                            }
                        }
                        this.loading = false;
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            },
            setProperty(e){
                if (e.target.checked == true) {
                    this.properties.push({'name': e.target.name, 'value': e.target.value,});
                }else{
                    this.properties = this.removeFromObject(this.properties, e.target.name, e.target.value);
                }
                this.page = 0;
                this.filterProducts();
            },
            setDefaultFilter(){
                axios.post('/'+ this.$lang +'/setDefaultFilter', {
                        category: this.category,
                    })
                    .then(response => {
                        this.filter = response.data.parameters;
                        this.priceMin = response.data.prices.min;
                        this.priceMax = response.data.prices.max;
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            },
            removeFromObject(object, name, value){
                object.filter(function(element, key){
                    if ((element.name == name) && (element.value == value)){
                        object.splice(key, 1);
                    }
                });
                return object;
            },
            handleScroll(data){
                let lastPage;

                if (this.last_page === false) {
                    lastPage = data.last_page;
                }else{
                    lastPage = this.last_page;
                }

                if (this.page <= lastPage) {
                    if (!this.loading ) {
                        if (this.page == 0) {
                            this.filterProducts();
                        }else{
                            this.filterProducts(1);
                        }
                    }
                }
            },
            removeFilter(){
                location.reload();
            }
        },
    }
</script>
