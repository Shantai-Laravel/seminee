<template>

    <tr class="row100">
        <td class="column-id">{{ index + 1 }}</td>
    	<td class="column-id">
            <input type="text" :value="lang.lang.toUpperCase()" disabled v-for="lang in langs">
        </td>
        <td class="column-text">
            <input type="text" v-model="name[translation.lang_id]=translation.name" placeholder="---" v-for="(translation, index) in product.translations" @keyup="setChange">
        </td>
        <td class="column-tenumberxt">

            <div class="form-check">
                <input type="checkbox" class="form-check-input" :id="'.com' + product.id"  v-model="product.com" @click="changeProductLocationCOM">
                <label class="form-check-label" :for="'.com' + product.id">active</label>
            </div>
            <!-- <div class="form-check">
                <input type="checkbox" class="form-check-input" :id="'.md' + product.id" v-model="product.md" @click="changeProductLocationMD">
                <label class="form-check-label" :for="'.md' + product.id">.md</label>
            </div> -->
        </td>
        <td class="column-number">
            <span><i>{{ strCut(product.translations[0].name) }}</i></span>
            <input type="text" v-model="code=product.code" placeholder="---" @keyup="setChange">
        </td>
        <td class="column-select column-button" v-for="param in category.params" v-if="dependeble !== param.property.id">
            <span v-if="param.property.type === 'select'"><i>{{ strCut(product.translations[0].name) }}</i></span>
            <select @change="setChange" v-model="properties[param.property.id]" v-if="param.property.type === 'select'">
                <option value="0">---</option>
                <option :value="mutidata.id" v-for="mutidata in param.property.parameter_values">{{ mutidata.translation.name }}</option>
            </select>

            <select multiple @change="setChange" v-model="propertiesCheckbox[param.property.id]" v-if="param.property.type === 'checkbox'">
                <option value="0">---</option>
                <option :value="mutidata.id" v-for="mutidata in param.property.parameter_values">{{ mutidata.translation.name }}</option>
            </select>

            <input type="text" v-model="propertiesText[param.property.id][key]" v-for="(propertyText, key) in propertiesText[param.property.id]" v-if="param.property.type === 'text' || param.property.type === 'textarea'" @keyup="setChange">
        </td>
    	<td class="column-number">
            <span><i>{{ strCut(product.translations[0].name) }}</i></span>
            <input type="number" v-model="stoc=product.stock" placeholder="---" @keyup="setChange">
        </td>
    	<td class="column-number">
            <span><i>{{ strCut(product.translations[0].name) }}</i></span>
            <input type="number" v-model="discount=product.discount" placeholder="---" @keyup="setChange">
        </td>
        <td class="column-select">
            <span><i>{{ strCut(product.translations[0].name) }}</i></span>
            <select v-model="promotion=product.promotion_id" @change="setChange">
                <option value="0">---</option>
                <option :value="promotion.id" v-for="promotion in promotions">{{ promotion.translation.name }}</option>
            </select>
        </td>
        <td class="column-button">
            <span><i>{{ strCut(product.translations[0].name) }}</i></span>
            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" :data-target="'#prices-modal' + product.id"><i class="fa fa-image"></i> Prices <span class="badge">{{ prices.length }}</span> </button>
        </td>
        <td class="column-button">
            <span><i>{{ strCut(product.translations[0].name) }}</i></span>
            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" :data-target="'#images-modal' + product.id"><i class="fa fa-image"></i> Images <span class="badge">{{ images.length }}</span> </button>
        </td>
        <td class="column-button">
            <span><i>{{ strCut(product.translations[0].name) }}</i></span>
            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" :data-target="'#video-modal' + product.id"><i class="fa fa-film"></i> Video <span class="badge">{{ video ? 1 : 0 }}</span></button>
        </td>
        <td class="column-button">
            <span><i>{{ strCut(product.translations[0].name) }}</i></span>
            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" :data-target="'#subprod-modal' + product.id" @click="updateSubproducts"><i class="fa fa-tag"></i> Subproducts <span class="badge">{{ subproducts.length }}</span></button>
        </td>
        <td class="column-button">
            <span><i>{{ strCut(product.translations[0].name) }}</i></span>
            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" :data-target="'#material-modal' + product.id" @click="getMaterials()"><i class="fa fa-tag"></i> Focare <span class="badge">{{ materials ? materials.length : 0 }}</span></button>
        </td>
        <td class="column-button">
            <span><i>{{ strCut(product.translations[0].name) }}</i></span>
            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" :data-target="'#sets-modal' + product.id"><i class="fa fa-point"></i> Add to Sets <span class="badge">{{ product.sets.length }}</span></button>
        </td>
        <td class="column-button">
            <span><i>{{ strCut(product.translations[0].name) }}</i></span>
            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" :data-target="'#collections-modal' + product.id"><i class="fa fa-point"></i> Collections/Brands </button>
        </td>
        <td class="column-button">
            <span><i>{{ strCut(product.translations[0].name) }}</i></span>
            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" :data-target="'#categories-modal' + product.id"><i class="fa fa-point"></i> Similar Products </button>
        </td>

        <td class="column-text">
            <input type="text" v-model="description[translation.lang_id]=translation.description" placeholder="---" v-for="translation in product.translations" @keyup="setChange">
        </td>
    	<td class="column-text">
            <input type="text" v-model="body[translation.lang_id]=translation.body" placeholder="---" v-for="translation in product.translations" @keyup="setChange">
        </td>

        <div class="modal fade bd-example-modal-lg settings-modal" :id="'prices-modal' + product.id" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="loading" v-if="loading"><div class="lds-ripple"><div></div><div></div></div></div>
                <div class="modal-content">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-center"><b>Prices : </b> {{ product.translations[0].name }}</h5>
                            <hr>
                        </div>
                        <div class="col-md-8">
                            <div class="col-md-12">
                                <div class="form-group" v-for="(price, index) in prices">
                                    <label for="">{{ price.currency.abbr }}</label>
                                    <small v-if="index == 0">[main currency]</small>
                                    <small v-else>
                                        <span class="text-success" v-if="price.currency.exchange_dependable == 1">exchange dependable</span>
                                        <span class="text-danger" v-else>non exchange dependable</span>
                                    </small>
                                    <input type="number" step="0,01" v-model="currencyPrices[price.id]=price.old_price" class="form-control" :disabled="price.currency.active == 0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- <div class="form-group">
                                <label>
                                    <input type="checkbox" @change="changeDependeblePrice" v-model="product.dependable_price" disabled>
                                    <span>Exchange dependable</span>
                                </label>
                            </div> -->
                            <button type="button" class="btn btn-primary btn-block" @click="savePrices">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Materials modal -->
        <div class="modal fade bd-example-modal-lg settings-modal" :id="'material-modal' + product.id" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="loading" v-if="loading"><div class="lds-ripple"><div></div><div></div></div></div>
                <div class="modal-content">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-center"><b>Focare : </b> {{ product.translations[0].name }}</h5>
                            <hr>
                        </div>
                        <div class="col-md-12">
                            <div class="row" v-for="material in materials">
                                <div class="form-group"> <hr>
                                    <div class="col-md-12">
                                        <input type="checkbox" :id="'material' + material.id + product.id" v-model="materialsModel[material.id]" @click="addMaterialToProduct(material.id)">
                                        <label :for="'material' + material.id + product.id"><span>{{ material.translation.name }}</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subproducts modal -->
        <div class="modal fade bd-example-modal-lg settings-modal" :id="'subprod-modal' + product.id" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="loading" v-if="loading"><div class="lds-ripple"><div></div><div></div></div></div>
                <div class="modal-content">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-center"><b>Subproducts : </b> {{ product.translations[0].name }}</h5>
                            <hr>
                        </div>
                        <div class="row">
                            <div class="col-md-12 subprods">
                                <form @submit.prevent="getFormSubproducts">
                                    <div class="">
                                        <div class="col-md-12">
                                            <div class="col-md-4">
                                                <button type="button" class="btn btn-primary btn-block" @click="inheritProduct">Inherit product fields</button>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary btn-block" name="button">Save</button>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="button" class="btn btn-primary btn-block" data-dismiss="modal" aria-hidden="true">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" v-for="(subproduct, index) in subproducts">
                                        <div class="form-group">
                                            <input type="checkbox" v-model="subproductFields.active[subproduct.id]=subproduct.active" checked="subproduct.active">
                                            <span>#{{ index + 1 }} subproduct, code - <b>{{ subproduct.code }}</b> </span>
                                        </div>
                                        <div class="form-group">
                                            <label>{{ category.property.property.translation.name }}</label>
                                            <input type="text" :value="category.property.property.parameter_values[index].translation.name" class="form-control" disabled>
                                        </div>
                                        <!-- <div class="form-group">
                                            <label>Price</label>
                                            <input type="text" v-model="subproductFields.price[subproduct.id]=subproduct.price" class="form-control">
                                        </div> -->
                                        <div class="input-group" v-for="subPrice in subproduct.prices">
                                            <input type="number" step="0.01" v-model="subproductPrices[subPrice.id]=subPrice.old_price" class="form-control"  :disabled="subPrice.currency.active == 0">
                                            <span class="input-group-addon"> {{ subPrice.currency.abbr }} </span>
                                        </div>
                                        <div class="form-group">
                                            <!-- <label for="">
                                                <input type="checkbox" v-model="subproductDepndablePrice=subproduct.dependable_price" checked="subproduct.active" @change="changeDependableStatut(subproduct.id)" disabled>
                                                <span>Exchange dependable</span>
                                            </label> -->
                                        </div>

                                        <div class="form-group">
                                            <label>Discount</label>
                                            <input type="number" step="0.01" v-model="subproductFields.discount[subproduct.id]=subproduct.discount" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Stoc</label>
                                            <input type="number" v-model="subproductFields.stoc[subproduct.id]=subproduct.stoc" class="form-control">
                                        </div>
                                        <hr>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Images modal -->
        <div class="modal fade bd-example-modal-lg settings-modal" :id="'images-modal' + product.id" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="loading" v-if="loading"><div class="lds-ripple"><div></div><div></div></div></div>
                <div class="modal-content">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-center"><b>Images : </b> {{ product.translations[0].name }}</h5>
                            <hr>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <input type="file" multiple="multiple" id="attachments" @change="uploadFieldChange">
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-primary" @click="submit">Upload</button><hr>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-3 image-wrapp" v-for="image in images">
                                    <div class="image-btns">
                                        <b v-if="image.main">Main <i class="fa fa-pencil"></i></b>
                                        <b v-if="image.first">First <i class="fa fa-edit"></i></b>
                                        <span><i class="fa fa-edit"   @click="firstImage(image.id)"></i></span>
                                        <span><i class="fa fa-pencil" @click="mainImage(image.id)"></i></span>
                                        <span><i class="fa fa-remove" @click="removeImage(image.id)"></i></span>
                                    </div>
                                    <img :src="'/images/products/og/' + image.src" alt="" style="width: 100%;"> <hr>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sets Modal -->
        <div class="modal fade bd-example-modal-lg settings-modal" :id="'sets-modal' + product.id" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="loading" v-if="loading"><div class="lds-ripple"><div></div><div></div></div></div>
                <div class="modal-content">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-center"><b>Sets : </b> {{ product.translations[0].name }}</h5><hr>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-12"><div class="row">
                                        <div class="col-md-6"><b>Set</b></div>
                                        <div class="col-md-6"><b>Image</b></div>
                                </div></div>
                                <div class="col-md-12">
                                    <div class="row" v-for="set in setsAll">
                                        <div class="form-group"> <hr>
                                            <div class="col-md-5">
                                                <input type="checkbox" v-model="setsProduct[set.id]" :id="'set' + set.id + product.id" @change="saveSets(set.id)">
                                                <label :for="'set' + set.id + product.id"><span>{{ set.translation.name }}</span></label>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="file" @change="uploadFieldChange" :name="'set' + set.id" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-primary btn-sm svbtn" :id="'set' + set.id" @click="uploadSetImage(set.id)">save</button>
                                            </div>
                                            <div class="col-md-2 text-left setImage" v-if="getSetImage(set.id) != false">
                                                <button type="button" class="btn btn-primary btn-sm" v-if="getSetImage(set.id) != false" @click="removeSetImage(set.id)"><i class="fa fa-remove"></i></button>
                                                <img :src="'/images/products/set/' + getSetImage(set.id)" height="40px">
                                            </div>
                                            <div class="col-md-2 text-right setImage" v-else>
                                                <img src="/admin/img/noimage.jpg" height="40px">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collections Modal -->
        <div class="modal fade bd-example-modal-lg settings-modal" :id="'collections-modal' + product.id" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="loading" v-if="loading"><div class="lds-ripple"><div></div><div></div></div></div>
                <div class="modal-content">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-center">Collections : {{ product.translations[0].name }}</h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <small class="text-danger">Bifand una sau mai multe colectii, vor fi create seturi care vor contine produsul "<i>{{ product.translations[0].name }}</i>".</small>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <div class="row" v-for="collection in collections">
                                            <div class="form-group"> <hr>
                                                <div class="col-md-12">
                                                    <input type="checkbox" :id="'collection' + collection.id + product.id" @change="generateNewSet(collection.id)" v-model="collectionsProducts[collection.id]">
                                                    <label :for="'collection' + collection.id + product.id"><span>{{ collection.translation.name }}</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-center">Brands : {{ product.translations[0].name }}</h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <small class="text-danger">Puteti atribui unul sau mai multe branduri - "<i>{{ product.translations[0].name }}</i>".</small>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <div class="row" v-for="brand in brands">
                                            <div class="form-group"> <hr>
                                                <div class="col-md-12">
                                                    <input type="checkbox" :id="'brand' + brand.id + product.id" @change="addBrandToProduct(brand.id)" v-model="brandsProducts[brand.id]">
                                                    <label :for="'brand' + brand.id + product.id"><span>{{ brand.translation.name }}</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Products Modal-->
        <div class="modal fade bd-example-modal-lg settings-modal" :id="'categories-modal' + product.id" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="loading" v-if="loading"><div class="lds-ripple"><div></div><div></div></div></div>
                <div class="modal-content">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-center">Similar Products : {{ product.translations[0].name }}</h5>
                            <hr>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- <div class="col-md-8">
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <button type="button" @click="setSimilarAll" class="btn btn-primary" name="button">check all</button>
                                                <button type="button" @click="unsetSimilarAll" class="btn btn-primary" name="button">uncheck all</button>
                                            </div>
                                        </div> <hr>
                                    </div>
                                    <div class="row" v-for="category in categories">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <input type="checkbox" v-model="similarProducts[category.id]" :id="'category' + category.id + product.id" @change="setSimilarProducts(category.id)" class="similar-categories">
                                                <label :for="'category' + category.id + product.id"><span>{{ category.translation.name }}</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->

                                <div class="col-md-8">
                                    <div class="row" v-for="oneProd in allprods">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <input type="checkbox" v-model="similarProductsToProd[oneProd.id]" :id="'category' + oneProd.id + product.id" @change="setSimilarProductsToProd(oneProd.id)" class="similar-categories">
                                                <label :for="'category' +  oneProd.id + product.id"><span>{{ oneProd.translation.name }}</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <input type="checkbox" v-model="hit" id="hit" @change="setHit">
                                            <label for="hit"><span>Lichidare de stoc</span></label>
                                        </div><hr><br>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <input type="checkbox" v-model="recomended" id="recomended" @change="setRecomended">
                                            <label for="recomended"><span>Recomended</span></label>
                                        </div><hr> <br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Modal-->
        <div class="modal fade bd-example-modal-lg settings-modal" :id="'video-modal' + product.id" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="loading" v-if="loading"><div class="lds-ripple"><div></div><div></div></div></div>
                <div class="modal-content">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-center"><b>Video</b> : {{ product.translations[0].name }}</h5>
                            <hr>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="file" class="form-control" id="video-attachments" @change="uploadFieldChange">
                                    <label for="">Video Uploader</label>
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-block"@click="uploadVideo">Save</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div v-if="video !== false">
                                    <button type="button" class="btn btn-primary btn-sm" @click="removeVideo"><i class="fa fa-remove"></i> Delete video</button>
                                    <video :src="'/videos/' + video" height="400px" controls></video>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </tr>

</template>

<script>
    import { bus } from '../../app';

    export default {
        props: ['category', 'index', 'prod', 'langs', 'promotions', 'sets', 'collections', 'brands', 'categories', 'allprods'],
        data(){
            return {
                product: this.prod,
                name : [],
                description : [],
                body : [],
                code : '',
                price : 0,
                stoc : 0,
                discount : 0,
                promotion : 0,
                currencyPrices: [],
                video: this.prod.video ? this.prod.video : false,
                hit: this.prod.hit,
                recomended: this.prod.recomended,
                properties : [],
                propertiesCheckbox : [],
                propertiesText : [],
                attachments : [],
                images : this.prod.images,
                prices: this.prod.prices,
                subproducts : this.prod.subproducts,
                setsAll: this.sets,
                data : new FormData(),
                percentCompleted: 0,
                loading : false,
                setsProduct: [],
                previewImage: [],
                similarProducts: [],
                similarProductsToProd: [],
                collectionsProducts: [],
                brandsProducts: [],
                subproductFields : {
                    'id' : [],
                    'active' : [],
                    'price' : [],
                    'discount' : [],
                    'stoc' : [],
                },
                subproductPrices: [],
                subproductDepndablePrice: [],
                dependeble: this.category.property ? this.category.property.parameter_id : 0,
                materials: [],
                checkedMaterials: [],
                materialsModel: [],
            }
        },
        mounted(){
            // console.log(this.brands);
            bus.$on('save' + this.index, data => {
                this.save();
            })
            bus.$on('refresh', data => {
                this.properties = [];
                this.getPropValue();
                this.getPropValueText();
            })
            this.getPropValue();
            this.getPropValueCheckbox();
            this.getPropValueText();
            this.getProductSets();
            this.getSimilarProduct();
            this.getSimilarProductToProds();
            this.getCollectionProduct();
            this.getBrandsProduct();
        },
        methods: {
            setChange(){
                bus.$emit('documentChange', this.index);
            },
            save(){
                // console.log(this.propertiesCheckbox);
                bus.$emit('startLoading');
                axios.post('/back/auto-upload-edit', {product: this.product, properties: this.properties, propertiesText: this.propertiesText, propertiesCheckbox: this.propertiesCheckbox})
                    .then(response => {
                        bus.$emit('endLoading');
                    })
                    .catch(e => {
                        console.log('error load products');
                    })
            },
            remove(){
                if(confirm("Do you really want to delete?")){
                    axios.post('/back/auto-upload-remove', {product: this.product})
                        .then(response => {
                            bus.$emit('removeProduct', this.index);
                        })
                        .catch(e => {
                            console.log('error load products');
                        })
                }
            },
            getProductSets(){
                let defaultVal = [];
                let defaultModel = [];

                this.product.sets.forEach(function(entry) {
                    defaultVal[ entry.id] = entry.id;
                });

                this.sets.forEach(function(entry){
                    defaultModel[entry.id] = defaultVal.includes(entry.id);
                });

                this.setsProduct = defaultModel;
            },
            getSimilarProduct(){
                let defaultVal = [];
                this.product.similar.forEach(function(entry){
                    defaultVal[entry.category_id] = true;
                });

                this.similarProducts = defaultVal;
            },
            getSimilarProductToProds(){
                let defaultVal = [];
                this.product.similar_prods.forEach(function(entry){
                    defaultVal[entry.prod_id] = true;
                });

                this.similarProductsToProd = defaultVal;
            },
            // similarProductsToProd
            getCollectionProduct(){
                let defaultVal = [];
                this.product.collections.forEach(function(entry){
                    defaultVal[entry.collection_id] = true;
                });

                this.collectionsProducts = defaultVal;
            },
            getBrandsProduct(){
                let defaultVal = [];
                this.product.brands.forEach(function(entry){
                    defaultVal[entry.brand_id] = true;
                });

                this.brandsProducts = defaultVal;
            },
            getPropValue(){
                let vm = this;
                let defaultVal = [];
                let defaultValCheckbox= [];

                 this.category.params.forEach(function (entry) {
                     if (vm.dependeble !== entry.property.id) {
                         if (entry.property.type == 'select') {
                             defaultVal[entry.parameter_id] = vm.getProductValueSelect(entry.parameter_id);
                         }else if(entry.property.type == 'checkbox'){
                             defaultValCheckbox[entry.parameter_id] = vm.getProductValueCheckbox(entry.parameter_id);
                         }
                     }
                 })
                 // console.log(defaultValCheckbox);
                 this.propertiesCheckbox = defaultValCheckbox;
                 this.properties = defaultVal;
            },
            getProductValueSelect(propId){ //get product value
                let ret = 0;
                this.product.property_values.forEach(function(entry) {
                    if (propId == entry.parameter_id) {
                        ret = entry.parameter_value_id;
                        return ret;
                    }
                })
                return ret;
            },
            getProductValueCheckbox(propId){
                let ret = [];
                this.product.property_values.forEach(function(entry) {
                    if (propId == entry.parameter_id) {
                        ret.push(entry.parameter_value_id);
                        return ret;
                    }
                })
                return ret;
            },
            getPropValueCheckbox(){

            },
            getPropValueText(){
                let vm = this;
                let defaultVal = [];
                let multilingual = 1;

                 this.category.params.forEach(function (entry, key) {
                     if (entry.property.type == 'text' || entry.property.type == 'textarea') {
                        multilingual = entry.property.multilingual;
                        defaultVal[entry.parameter_id] = vm.getProductValueText(entry.parameter_id, multilingual);
                     }
                 })
                 this.propertiesText = defaultVal;
            },
            getProductValueText(propId, multilingual){ //get product text value
                let vm = this;
                let ret = [''];
                let retInfo = [];

                this.product.property_values.forEach(function(entry, index) {
                    if (propId === entry.parameter_id) {
                        if (multilingual == 1) {
                            vm.langs.forEach(function(item, key){
                                if (entry.translations[key] != undefined) {
                                    if (item.id == entry.translations[key].lang_id) {
                                        retInfo[key] = entry.translations[key].value;
                                    }else{
                                        retInfo[key] = '';
                                    }
                                }else{
                                    retInfo[key] = '';
                                }
                            })
                        }else{
                            if (entry.translations[0] != undefined) {
                                retInfo[0] = entry.translations[0].value;
                            }else {
                                retInfo[0] = '';
                            }
                        }
                        return retInfo;
                    }else{
                        if (multilingual == 1) {
                            vm.langs.forEach(function(item, key){
                                ret[key] = '';
                            })
                        }else{
                            ret[0] = '';
                        }
                        return ret;
                    }
                })

                if (retInfo.length > 0) {
                    return retInfo;
                }
                return ret;
            },

            strCut(text){
                return text.length > 15 ? text.substring(0, 15) + '...' : text;
            },
            prepareFields() { //image prepare field
                if (this.attachments.length > 0) {
                    for (var i = 0; i < this.attachments.length; i++) {
                        let attachment = this.attachments[i];
                        this.data.append('attachments[]', attachment);
                    }
                    this.data.append('product_id', this.product.id);
                }
            },
            uploadFieldChange(e) {
                var name = e.target.name;
                var files = e.target.files || e.dataTransfer.files;

                if (name.length > 0) {
                    $('.svbtn').removeClass('btn-warning');
                    $('.svbtn').addClass('btn-primary');
                    $('#' + name).removeClass('btn-primary');
                    $('#' + name).addClass('btn-warning');
                }

                if (!files.length)
                    return;
                for (var i = files.length - 1; i >= 0; i--) {
                    this.attachments.push(files[i]);
                }

                document.getElementById("video-attachments").value = [];
                document.getElementById("attachments").value = [];
            },
            submit() {
                this.loading = true;
                this.prepareFields();
                var config = {
                    headers: { 'Content-Type': 'multipart/form-data' } ,
                    onUploadProgress: function(progressEvent) {
                        this.percentCompleted = Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                        this.$forceUpdate();
                    }.bind(this)
                };
                axios.post('/back/auto-upload-upload-images', this.data, config)
                .then(function (response) {
                    this.updateImagesList();
                    if (response.data.success) {
                        console.log('Successfull Upload');
                        this.resetData();
                    } else {
                        console.log('Unsuccessful Upload');
                    }
                    this.resetData();

                }.bind(this))
                .catch(function (error) {
                    console.log(error);
                });
            },
            saveSets(setId){
                this.loading = true;
                axios.post('/back/auto-upload-save-sets', {sets: this.setsProduct, product_id: this.product.id, set_id: setId})
                .then(function (response) {
                    this.loading = false;
                }.bind(this))
                .catch(function (error) {
                    console.log(error);
                    this.loading = false;
                });
            },
            uploadSetImage(setId){
                $('.svbtn').removeClass('btn-warning');
                $('.svbtn').addClass('btn-primary');

                this.loading = true;
                this.prepareFields();
                this.data.append('set_id', setId);

                var config = {
                    headers: { 'Content-Type': 'multipart/form-data' } ,
                    onUploadProgress: function(progressEvent) {
                        this.percentCompleted = Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                        this.$forceUpdate();
                    }.bind(this)
                };
                axios.post('/back/auto-upload-set-image-upload', this.data, config)
                    .then(response => {
                        this.setsAll = response.data.sets;
                        this.product = response.data.product;
                        this.resetData();
                        this.loading = false;
                    })
                    .catch(e => {
                        this.loading = false;
                        console.log('error load products');
                    })
            },
            removeSetImage(setId){
                if (confirm("Do you really want to delete image?")) {
                    $('.svbtn').removeClass('btn-warning');
                    $('.svbtn').addClass('btn-primary');

                    this.loading = true;

                    axios.post('/back/auto-upload-set-image-remove', {set_id: setId, product_id: this.product.id})
                        .then(response => {
                            this.setsAll = response.data.sets;
                            this.product = response.data.product;
                            this.loading = false;
                        })
                        .catch(e => {
                            this.loading = false;
                            console.log('error load remove set image');
                        })
                }
            },
            uploadVideo(){
                this.prepareFields();
                var config = {
                    headers: { 'Content-Type': 'multipart/form-data' } ,
                    onUploadProgress: function(progressEvent) {
                        this.percentCompleted = Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                        this.$forceUpdate();
                    }.bind(this)
                };
                this.loading = true;
                axios.post('/back/auto-upload-upload-video', this.data, config)
                    .then(response => {
                        this.video = response.data;
                        this.resetData();
                        this.loading = false;
                    })
                    .catch(e => {
                        this.loading = false;
                        console.log('error load products');
                    })
                    this.loading = false;
            },
            getSetImage(setId){
                let ret = false;
                this.product.set_images.forEach(function(entry){
                    if(setId == entry.set_id){
                        ret = entry.image;
                        return ret;
                    }
                })
                return ret;
            },
            resetData() {
                this.data = new FormData();
                this.attachments = [];
            },
            updateImagesList(){
                axios.post('/back/auto-upload-get-images', {product_id: this.product.id})
                    .then(response => {
                        this.images = response.data;
                        this.loading = false;
                    })
                    .catch(e => {
                        this.loading = false;
                        console.log('error load products');
                    })
            },
            removeImage(id){
                if (confirm("Do you really want to delete this image?")) {
                    this.loading = true;
                    axios.post('/back/auto-upload-remove-image', {product_id: this.product.id, id : id})
                        .then(response => {
                            this.images = response.data;
                            this.loading = false;
                        }).catch(e => {
                            this.loading = false;
                        })
                }
            },
            mainImage(id){
                this.loading = true;
                axios.post('/back/auto-upload-main-image', {product_id: this.product.id, id : id})
                    .then(response => {
                        this.images = response.data;
                        this.loading = false;
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            firstImage(id){
                this.loading = true;
                axios.post('/back/auto-upload-first-image', {product_id: this.product.id, id : id})
                    .then(response => {
                        this.images = response.data;
                        this.loading = false;
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            getFormSubproducts(submitEvent){
                 this.loading = true;
                 axios.post('/back/auto-upload-edit-subproducts', {product_id: this.product.id, subproducts : this.subproductFields, subproductPrices: this.subproductPrices})
                     .then(response => {
                         this.subproducts = response.data;
                         this.loading = false;
                     }).catch(e => {
                         this.loading = false;
                     })
            },
            inheritProduct(){
                if (confirm("Do you really want to inherit product fields?")) {
                    this.loading = true;
                    axios.post('/back/auto-upload-inherit-subproducts', {product_id: this.product.id})
                        .then(response => {
                            this.subproducts = response.data;
                            this.loading = false;
                        }).catch(e => {
                            this.loading = false;
                        })
                }
            },
            generateNewSet(collectionId){
                this.loading = true;
                axios.post('/back/auto-upload-generate-new-set', {product_id: this.product.id, collection_id: collectionId})
                    .then(response => {
                        this.loading = false;
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            addBrandToProduct(brandId){
                this.loading = true;
                axios.post('/back/auto-upload-add-brand-to-product', {product_id: this.product.id, brand_id: brandId})
                    .then(response => {
                        this.loading = false;
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            setSimilarProducts(categoryId){
                this.loading = true;
                axios.post('/back/auto-upload-set-similar-products', {product_id: this.product.id, category_id: categoryId})
                    .then(response => {
                        this.loading = false;
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            setSimilarProductsToProd(prodId){
                this.loading = true;
                axios.post('/back/auto-upload-set-similar-products-to-prod', {product_id: this.product.id, prod_id: prodId})
                    .then(response => {
                        this.loading = false;
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            //
            setSimilarAll(e){
                let categories = this.categories;
                $('.similar-categories').prop('checked', true);

                this.loading = true;
                axios.post('/back/auto-upload-set-similar-all-products', {product_id: this.product.id, categories_id: categories})
                    .then(response => {
                        this.loading = false;
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            unsetSimilarAll(e){
                let categories = [];
                $('.similar-categories').prop('checked', false);

                this.loading = true;
                axios.post('/back/auto-upload-set-similar-all-products', {product_id: this.product.id, categories_id: categories})
                    .then(response => {
                        this.loading = false;
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            setHit(){
                this.loading = true;
                axios.post('/back/auto-upload-set-hit-products', {product_id: this.product.id})
                    .then(response => {
                        this.loading = false;
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            setRecomended(){
                this.loading = true;
                axios.post('/back/auto-upload-set-recomended-products', {product_id: this.product.id})
                    .then(response => {
                        this.loading = false;
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            removeVideo(){
                if (confirm("Do you really want to delete?")) {
                this.loading = true;
                axios.post('/back/auto-upload-remove-video', {product_id: this.product.id})
                    .then(response => {
                        this.loading = false;
                        this.video = false;
                    }).catch(e => {
                        this.loading = false;
                    })

                }
            },
            changeDependeblePrice(){
                if (confirm("Do you really want to change dependable status?")) {
                    this.loading = true;
                    axios.post('/back/auto-upload-change-dependable-price', {product_id: this.product.id, prices: this.currencyPrices})
                        .then(response => {
                            this.loading = false;
                            this.prices = response.data.prices;
                        }).catch(e => {
                            this.loading = false;
                        })
                }
            },
            savePrices(){
                this.loading = true;
                axios.post('/back/auto-upload-save-prices', {product_id: this.product.id, prices: this.currencyPrices})
                    .then(response => {
                        this.loading = false;
                        this.prices = response.data.prices;
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            changeDependableStatut(subproductId){
                if (confirm("Do you really want to change dependable status?")) {
                    this.loading = true;
                    axios.post('/back/auto-upload-change-dependable-status', {subproduct_id: subproductId, product_id: this.product.id, subproductPrices: this.subproductPrices})
                        .then(response => {
                            this.loading = false;
                            this.subproducts = response.data;
                        }).catch(e => {
                            this.loading = false;
                        })
                }
            },
            getMaterials(){
                this.materials = [];
                this.loading = true;
                axios.post('/back/auto-upload-get-materials', {product_id: this.product.id})
                    .then(response => {
                        this.loading = false;
                        this.materials = response.data.materials;
                        this.checkedMaterials = response.data.checkedMaterials;
                        this.setCheckedMaterials();
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            setCheckedMaterials(){
                let arr = [];
                let wm = this;

                this.materials.forEach(function(entry){
                    if (wm.checkedMaterials.includes(entry.id)) {
                        arr[entry.id] = 1;
                    }else{
                        arr[entry.id] = 0;
                    }
                });

                this.materialsModel = arr;
            },
            addMaterialToProduct(id){
                this.loading = true;
                axios.post('/back/auto-upload-add-materials', {product_id: this.product.id, material_id: id})
                    .then(response => {
                        this.loading = false;
                        // this.materials = response.data.materials;
                        // this.checkedMaterials = response.data.checkedMaterials;
                        // this.setCheckedMaterials();
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            changeProductLocationCOM(){
                this.loading = true;
                axios.post('/back/auto-upload-change-com-status', {product_id: this.product.id, product_com: this.product.com})
                    .then(response => {
                        this.loading = false;
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            changeProductLocationMD(){
                this.loading = true;
                axios.post('/back/auto-upload-change-md-status', {product_id: this.product.id, product_md: this.product.md})
                    .then(response => {
                        this.loading = false;
                    }).catch(e => {
                        this.loading = false;
                    })
            },
            updateSubproducts(){
                this.loading = true;
                axios.post('/back/auto-upload-update-subproducts', {product_id: this.product.id})
                    .then(response => {
                        this.subproducts = response.data.subproducts,
                        this.loading = false;
                    }).catch(e => {
                        this.loading = false;
                    })
            }
        },
    }
</script>

<style>
    .setImage{
        position: relative;
    }
    .setImage img{
        position: absolute;
        height: 80px;
        right: 15px;
        top: -15px;
    }
</style>
