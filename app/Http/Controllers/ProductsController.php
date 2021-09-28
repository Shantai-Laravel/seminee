<?php

namespace App\Http\Controllers;

use App\Http\Controllers\PagesController as PageItem;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\SubProduct;
use App\Models\Page;
use App\Models\ParameterValueProduct;
use App\Models\ParameterValue;
use App\Models\ProductSimilar;
use App\Models\ProductMaterial;
use App\Models\ProductPrice;
use App\Models\SetProducts;
use App\Models\Set;
use App\Models\SetTranslation;
use App\Models\Promotion;
use App\Models\SimilarProdToProds;

class ProductsController extends Controller
{
    /**
     *  get action
     *  Render Category page
     */
    public function categoryRender($category)
    {
        $category = ProductCategory::with([ 'children.translation',
                                            'translation',
                                            'params.property.translation',
                                            'params.property.transData',
                                            'params.property.parameterValues.translation',
                                            'params.property.parameterValues.transData',
                                        ])
                                    ->where('alias', $category)->first();


        if (is_null($category)) {
            abort(404);
        }

        $seoData = $this->_getSeo($category);

        return view('front..products.category', compact('seoData', 'category'));
    }

    /**
     *  get action
     *  Render Product page
     */
    public function productRender($category, $product)
    {
        $product = Product::with([
                            'category.properties.property.parameterValues.translation',
                            'category.translation',
                            'images',
                            'mainImage',
                            'mainPrice',
                            'personalPrice',
                            'subproducts.parameterValue.translation',
                            'subproducts.parameter.translation',
                            'translation'
                          ])->where('alias', $product)
                          ->where(env('APP_SLUG'), 1)
                          ->orderBy('position', 'asc')
                          ->first();

        $category = ProductCategory::where('alias', $category)->first();

        if (is_null($product) || is_null($category)) {
            abort(404);
        }

        $productMaterial = ProductMaterial::where('material_id', $product->id)->pluck('product_id')->toArray();
        if (count($productMaterial) == 0) {
            $productMaterial = ProductMaterial::where('product_id', $product->id)->limit(4)->orderBy('id', 'desc')->pluck('material_id')->toArray();
        }

        if (count($productMaterial) > 0) {

            $similarProducts = Product::whereIn('id', $productMaterial)->limit(4)->orderBy('id', 'desc')->get();
        }else{

            // $similarProductsArray = ProductSimilar::where('product_id', $product->id)->pluck('category_id')->toArray();
            $similarProductsArray = SimilarProdToProds::where('prod_main', $product->id)->pluck('prod_id')->toArray();
            // dd($similarProductsArray);
            $similarProducts = Product::whereIn('id', $similarProductsArray)->where('id', '!=', $product->id)->limit(4)->orderBy('id', 'desc')->get();

            if (count($similarProducts) == 0) {
                $similarProducts = Product::where('category_id', $category->id)->where('id', '!=', $product->id)->limit(4)->orderBy('id', 'desc')->get();
            }
        }

        $seoData = $this->_getSeo($product);

        return view('front.products.product', compact('seoData', 'category', 'product', 'similarProducts', 'wearWith'));
    }

    /**
     *  get action
     *  Render new page
     */
    public function newRender()
    {
        $page = Page::where('alias', 'new')->first();

        if (is_null($page)) {
            abort(404);
        }

        $pageItem = new PageItem;
        $seoData = $pageItem->getSeo($page);

        return view('front.products.new', compact('seoData'));
    }

    /**
     *  post action (vuejs)
     *  return new products collection
     */
    public function getNewProducts(Request $request)
    {
        $products = Product::with(['category.properties.property.parameterValues.translation',
                                    'imagesBegin',
                                    'imagesLast',
                                    'images',
                                    'mainImage',
                                    'mainPrice',
                                    'personalPrice',
                                    'category.translation',
                                    'subproducts.parameterValue.translation',
                                    'subproducts.parameter.translation',
                                    'translation'])
                            // ->where('created_at', '>=', date('Y-m-d', strtotime('-15 days')))
                            ->where('discount', 0)
                            ->orderBy('created_at', 'desc')
                            ->where(env('APP_SLUG'), 1)
                            ->paginate(12);

        return $products;
    }

    /**
     *  get action
     *  Render Sale page
     */
    public function saleRender()
    {
        $page = Page::where('alias', 'sale')->first();

        if (is_null($page)) {
            abort(404);
        }

        $pageItem = new PageItem;
        $seoData = $pageItem->getSeo($page);

        return view('front.products.sale', compact('seoData'));
    }

    /**
     *  post action (vuejs)
     *  return sale products collection
     */
    public function getSaleProducts(Request $request)
    {
        $products = Product::with(['category.properties.property.parameterValues.translation',
                                    'imagesBegin',
                                    'imagesLast',
                                    'images',
                                    'mainImage',
                                    'category.translation',
                                    'subproducts.parameterValue.translation',
                                    'subproducts.parameter.translation',
                                    'translation',
                                    'mainPrice',
                                    'personalPrice'])
                            ->where('discount', '>', '0')
                            ->orderBy('discount_update', 'desc')
                            ->where(env('APP_SLUG'), 1)
                            ->paginate(15);

        return $products;
    }

    public function getRecentlyProducts(Request $request)
    {
        $recently = [];
        if (@$_COOKIE['view_recently']) {
            $recently = json_decode(@$_COOKIE['view_recently']);
        }

        $products = Product::with(['category.properties.property.parameterValues.translation',
                                    'imagesBegin',
                                    'imagesLast',
                                    'images',
                                    'mainImage',
                                    'category.translation',
                                    'subproducts.parameterValue.translation',
                                    'subproducts.parameter.translation',
                                    'translation',
                                    'mainPrice',
                                    'personalPrice'])
                            ->whereIn('id', $recently)
                            ->limit(4)
                            ->where(env('APP_SLUG'), 1)
                            ->get();

        return $products;
    }

    /**
     *  post action (vuejs)
     *  return search products collection
     */
    public function searchProducts(Request $request)
    {
        $findProducts = ProductTranslation::where('name', 'like',  '%'.$request->get('search').'%')
                                    ->orWhere('body', 'like',  '%'.$request->get('search').'%')
                                    ->pluck('product_id')->toArray();

        $data['products'] = Product::with(['category.properties.property.parameterValues.translation', 'imagesBegin', 'imagesLast', 'images', 'mainImage', 'category.translation', 'subproducts', 'translation'])
                                    ->whereIn('id', $findProducts)
                                    ->where(env('APP_SLUG'), 1)
                                    ->get();

        $findSets = SetTranslation::where('name', 'like',  '%'.$request->get('search').'%')
                                    ->pluck('set_id')->toArray();

        $data['sets'] = Set::with(['collection', 'mainPhoto', 'translation'])
                                    ->whereIn('id', $findSets)
                                    ->where(env('APP_SLUG'), 1)
                                    ->get();

        return $data;
    }

    /**
     *  post action (vuejs)
     *  return category products
     */
    public function getProductsAll(Request $request)
    {
        $id = 0;
        $data = [];
        $product = Product::find($request->get('mainProductId'));
        $category = ProductCategory::find($request->get('category_id'));
        $categoryChilds = ProductCategory::where('parent_id', $category->id)->pluck('id')->toArray();
        $categoryChilds2 = ProductCategory::whereIn('parent_id', $categoryChilds)->pluck('id')->toArray();

        $categoriesId = array_merge($categoryChilds, $categoryChilds2);

        array_push($categoryChilds, $category->id);

        if (!is_null($product)) {
            $id = $product->id;
        }

        $allProducts = Product::whereIn('category_id', $categoryChilds)->get(); // without pagination

        $products = Product::with(['category.properties.property.parameterValues.translation',
                                    'imagesBegin',
                                    'imagesLast',
                                    'images',
                                    'mainImage',
                                    'mainPrice', 'personalPrice',
                                    'category.translation',
                                    'subproducts.parameterValue.translation',
                                    'subproducts.parameter.translation',
                                    'translation'])
                           ->orderByRaw("id = $id DESC")
                           ->where('category_id', $request->get('category_id'))
                           ->where(env('APP_SLUG'), 1)
                           ->paginate(15);

       $maxPrice = ProductPrice::where('currency_id', $this->currency->id)
                               ->whereIn('product_id', $products->pluck('id')->toArray())
                               ->max('price');

       $data['prices']['min'] = 0;
       $data['prices']['max'] = $maxPrice;
       $data['products'] = $products;

       return json_encode($data);
    }

    public function getRecomandedProducts(Request $request)
    {
        return Product::with(['category.properties.property.parameterValues.translation',
                                    'imagesBegin',
                                    'imagesLast',
                                    'images',
                                    'mainImage',
                                    'mainPrice', 'personalPrice',
                                    'category.translation',
                                    'subproducts.parameterValue.translation',
                                    'subproducts.parameter.translation',
                                    'translation'])
                           ->where('recomended', 1)
                           ->where(env('APP_SLUG'), 1)
                           ->paginate(15);

    }

    public function getPromotionProducts(Request $request)
    {
        return Product::with(['category.properties.property.parameterValues.translation',
                                    'imagesBegin',
                                    'imagesLast',
                                    'images',
                                    'mainImage',
                                    'mainPrice', 'personalPrice',
                                    'category.translation',
                                    'subproducts.parameterValue.translation',
                                    'subproducts.parameter.translation',
                                    'translation'])
                           ->where('promotion_id', $request->get('promotion_id'))
                           ->where(env('APP_SLUG'), 1)
                           ->paginate(15);

    }

    public function setDefaultFilter(Request $request)
    {
        $categoryChilds = ProductCategory::where('parent_id', $request->get('category'))->pluck('id')->toArray();
        $categoryChilds = array_merge($categoryChilds, [$request->get('category')]);
        $allProducts = Product::whereIn('category_id', $categoryChilds)->get(); // without pagination

        $data['parameters'] = $this->getPropertiesList($allProducts, $request->get('category'));

        $maxPrice = ProductPrice::where('currency_id', $this->currency->id)
                                ->whereIn('product_id', $allProducts->pluck('id')->toArray())
                                ->max('price');

        $data['prices']['min'] = 0;
        $data['prices']['max'] = $maxPrice;

        return $data;
    }

    protected function getPropertiesList($allProducts, $categoryId)
    {
        $dependable = 0;
        $parametersId = ParameterValueProduct::whereIn('product_id', array_filter($allProducts->pluck('id')->toArray()))->pluck('parameter_value_id')->toArray();
        $dependableCategory = ProductCategory::where('id', $categoryId)->first();

        if (!is_null($dependableCategory)) {
            if (!is_null($dependableCategory->subproductParameter)) {
                $dependable = $dependableCategory->subproductParameter->parameter_id;
            }
        }

        $dependebleValues = ParameterValue::where('parameter_id', $dependable)->pluck('id')->toArray();

        $parametersId = array_merge($parametersId, $dependebleValues);
        return json_encode(array_filter($parametersId));
    }

    /**
     *  post action (vuejs)
     *  return subproduct on change size
     */
    public function getSubproductVue(Request $request)
    {
        $subproduct = SubProduct::where('product_id', $request->get('productId'))
                                ->where('parameter_id', $request->get('propertyId'))
                                ->where('value_id', $request->get('valueId'))
                                ->where('active', 1)
                                ->where('stoc', '>', 0)
                                ->first();
       return $subproduct;
    }

    // Filter products
    public function filter(Request $request)
    {
        $propsProducts =    [];
        $propsSubprods =    [];
        $params =           [];
        $subproducts =      [];
        $dependable = 0;
        $categoriesId = array_filter($request->get('categories'));
        $dependableCategory = ProductCategory::where('id', $request->get('category'))->first();

        $childCategories = ProductCategory::whereIn('parent_id', $categoriesId)->pluck('id')->toArray();

        $allCategoriesId = array_merge($categoriesId, $childCategories);

        if (!is_null($dependableCategory)) {
            if (!is_null($dependableCategory->subproductParameter)) {
                $dependable = $dependableCategory->subproductParameter->parameter_id;
            }
        }

        foreach ($request->get('properties') as $key => $param) {
            if ($param['name'] != $dependable) {
                $params[$param['name']][] = $param['value'];
            }else{
                $subproducts[] = $param;
            }
        }

        foreach ($params as $param => $values) {
            $propIds = [];
            foreach ($values as $key => $value) {
                $row = ParameterValueProduct::select('product_id')
                                ->where('parameter_value_id', $value)
                                ->where('parameter_id', $param)
                                ->when(count($propsProducts) > 0, function($query) use ($propsProducts){
                                    return $query->whereIn('product_id', $propsProducts);
                                })
                                ->pluck('product_id')->toArray();

                $propIds = array_merge($propIds, $row);
            }
            $propsProducts = $propIds;
        }

        foreach ($subproducts as $key => $value) {
            $row = Subproduct::select('product_id')
                                ->whereRaw('json_contains(combination, \'{"'.$value['name'].'": '.$value['value'].'}\')')
                                ->where('active', 1)
                                ->where('stoc', '>', 0)
                                ->when(count($propsProducts) > 0, function($query) use ($propsProducts){
                                   return $query->whereIn('product_id', $propsProducts);
                                })
                                ->pluck('product_id')->toArray();

            $propsSubprods = array_merge($propsSubprods, $row);
        }


        if ((count($request->get('properties')) > 0) && (count($propsProducts) == 0) && (count($propsSubprods) == 0)) {
            $propsProducts = [0];
        }

        $priceMax = $request->get('priceMax') ??  1000000;
        $priceMin = $request->get('priceMin') ?? 0;

        $products = Product::with(['category.properties.property.parameterValues.translation',
                                    'imagesBegin',
                                    'imagesLast',
                                    'images',
                                    'mainImage',
                                    'category.translation',
                                    'subproducts.parameterValue.translation',
                                    'subproducts.parameter.translation',
                                    'translation'])
                           ->orderBy('position', 'asc')
                           ->when(count($allCategoriesId) > 0, function($query) use ($allCategoriesId){
                               return $query->whereIn('category_id', $allCategoriesId);
                           })
                           ->when(count($propsProducts) > 0, function($query) use ($propsProducts){
                              return $query->whereIn('id', $propsProducts);
                          })
                          // ->when(count($propsSubprods) > 0, function($query) use ($propsSubprods){
                          //    return $query->whereIn('id', $propsSubprods);
                          //  })
                           ->whereHas('personalPrice', function($query) use ($priceMin, $priceMax){
                               $query->where('price', '>=', $priceMin);
                               $query->where('price', '<=', $priceMax);
                           })
                           ->with(['category.properties.property.parameterValues.translation', 'imagesBegin', 'imagesLast', 'images', 'mainImage', 'mainPrice', 'personalPrice', 'category.translation', 'subproducts', 'translation'])
                           ->where(env('APP_SLUG'), 1)
                           ->paginate(15);

        return $products;
    }

    public function renderPromotions()
    {
        $promotions = Promotion::with(['products.category.properties.property.parameterValues.translation',
                                        'products.images',
                                        'products.mainImage',
                                        'products.category.translation',
                                        'products.subproducts.parameterValue.translation',
                                        'products.subproducts.parameter.translation',
                                        'products.translation',
                                        'products.mainPrice',
                                        'products.personalPrice'])
                                    ->get();

        return view('front.products.promo', compact('promotions'));
    }

    public function renderPromotionSingle($id)
    {
        $promotion = Promotion::with(['products.category.properties.property.parameterValues.translation',
                                    'products.images',
                                    'products.mainImage',
                                    'products.category.translation',
                                    'products.subproducts.parameterValue.translation',
                                    'products.subproducts.parameter.translation',
                                    'products.translation',
                                    'products.mainPrice',
                                    'products.personalPrice'])
                                ->find($id);

        return view('front.products.promo', compact('promotion'));
    }

    /**
     *  private method
     *  return meta datas of categories and products
     */
    private function _getSeo($item)
    {
        $seo['title']       = $item->translation->seo_title ?? $item->translation->name;
        $seo['keywords']    = $item->translation->seo_keywords ?? $item->translation->name;
        $seo['description'] = $item->translation->seo_description ?? $item->translation->name;

        return $seo;
    }

}
