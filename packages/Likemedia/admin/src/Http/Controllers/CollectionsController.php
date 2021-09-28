<?php

namespace Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Set;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\SetProducts;
use App\Models\SetGallery;
use App\Models\Currency;
use App\Models\SetProductImage;


class CollectionsController extends Controller
{
    public function index()
    {
        $collections = Collection::with(['translation', 'sets.translation', 'sets.products.translation', 'sets.products.mainImage', 'sets.products.setImages'])
                                ->orderBy('position', 'asc')
                                ->orderBy('created_at', 'desc')
                                ->get();


        return view('admin::admin.collections.home', compact('collections'));
    }



    public function getCollections()
    {
        $collections = Collection::with(['translation', 'sets.translation', 'sets.products.translation', 'sets.products.mainImage', 'sets.products.setImages'])
                                ->orderBy('position', 'asc')
                                ->orderBy('created_at', 'desc')
                                ->get();

        return $collections;
    }

    public function getSets(Request $request)
    {
        $set = Set::with(['translation', 'products.translation', 'products.mainImage', 'products.setImages'])
                ->where('collection_id', $request->get('collection_id'))
                ->orderBy('position', 'asc')
                ->get();

        return $set;
    }

    public function changeCollections(Request $request)
    {
        $collections = $request->get('collections');
        $position = 1;

        if (count($collections) > 0) {
            foreach ($collections as $key => $collection) {
                $position++;
                Collection::where('id', $collection['id'])->update(['position' => $position]);
            }
        }

        return  $this->getCollections();
    }

    public function changeSets(Request $request)
    {
        $sets = $request->get('sets');
        $position = 1;

        if (count($sets) > 0) {
            foreach ($sets as $key => $set) {
                $position++;
                Set::where('id', $set['id'])->update(['position' => $position]);
            }
        }

        return Set::with(['translation', 'products.translation', 'products.mainImage', 'products.setImages'])
                ->where('collection_id', $request->get('collection_id'))
                ->orderBy('position', 'asc')
                ->get();
    }

    public function changeProducts(Request $request)
    {
        $products = $request->get('products');
        $position = 1;

        if (count($products) > 0) {
            foreach ($products as $key => $product) {
                $position++;
                Product::where('id', $product['id'])->update(['succesion' => $position]);
                $setProd = SetProducts::where('product_id', $product['id'])
                                    ->where('set_id', $request->get('set_id'))
                                    ->update(['position' => $position]);
            }
        }

        return Set::with(['translation', 'products.translation', 'products.mainImage', 'products.setImages'])
                        ->where('id', $request->get('set_id'))
                        ->first();

    }

    public function removeCollection(Request $request)
    {
        $collection = Collection::find($request->get('collection_id'));

        @unlink(public_path('/images/collections/' . $collection->banner));

        foreach ($this->langs as $lang):
            @unlink(public_path('/images/collections/' . $collection->translationByLang($lang->id)->first()->image));
        endforeach;

        $collection->delete();

        return $this->getCollections();
    }

    public function removeSet(Request $request)
    {
        $set = Set::find($request->get('set_id'));
        $images = SetGallery::where('set_id', $set->id)->get();

        SetProducts::where('set_id', $set->id)->delete();

        if (count($images) > 0) {
            foreach ($images as $key => $image) {
                if (file_exists(public_path('images/sets/og/'.$image->src))) {
                    unlink(public_path('images/sets/og/'.$image->src));
                }
                if (file_exists(public_path('images/sets/sm/'.$image->src))) {
                    unlink(public_path('images/sets/sm/'.$image->src));
                }

                SetGallery::where('id', $image->id)->delete();
            }
        }

        $set->delete();

        return Set::with(['translation', 'products.translation', 'products.mainImage', 'products.setImages'])
                ->where('collection_id', $request->get('collection_id'))
                ->orderBy('position', 'asc')
                ->get();
    }

    public function removeProduct(Request $request)
    {
        $setProduct = SetProducts::where('set_id', $request->get('set_id'))
                                ->where('product_id', $request->get('product_id'))
                                ->delete();

        return Set::with(['translation', 'products.translation', 'products.mainImage', 'products.setImages'])
                        ->where('id', $request->get('set_id'))
                        ->first();
    }

    public function removeSetProductImage(Request $request)
    {
        $image = SetProductImage::find($request->get('id'));

        if (!is_null($image)) {
            @unlink(public_path('images/products/set/'.$image->image));
            $image->delete();
        }

        return Product::with(['translation', 'mainImage', 'setImages'])->where('id', $image->product_id)->first();
    }

    public function addNewCollection(Request $request)
    {
        $titles = array_filter($request->get('titles'), function($var){return !is_null($var);} );

        $alias = str_slug($titles[$this->lang->id]);

        $findSlug = Collection::where('alias', $alias)->first();

        if (!is_null($findSlug))  $alias = $alias . rand(0, 100);

        $collection = Collection::create([
            'alias' => $alias,
            'position' => 1,
        ]);

        foreach ($titles as $key => $title) {
            $collection->translations()->create([
                'lang_id' => $key,
                'name' => $title,
            ]);
        }

        $data['collections'] = $this->getCollections();
        $data['collection'] = $collection;

        return $data;
    }

    public function addNewSet(Request $request)
    {
        $titles = array_filter($request->get('titles'), function($var){return !is_null($var);} );
        $alias = str_slug($titles[$this->lang->id]);

        $findSlug = Set::where('alias', $alias)->first();

        if (!is_null($findSlug))  $alias = $alias . rand(0, 100);

        $set = Set::create([
            'alias' => $alias,
            'collection_id' => $request->get('collection_id'),
            'position' => 1,
            'active' => 1,
            'code' => $request->get('code'),
            'price' => $request->get('price'),
            'discount' => $request->get('discount'),
        ]);

        foreach ($titles as $key => $title) {
            $set->translations()->create([
                'lang_id' => $key,
                'name' => $title,
            ]);
        }

        $this->generatePrices($set);

        $data['collections'] = $this->getCollections();
        $data['set'] = $set;

        return $data;
    }

    public function searchProduct(Request $request)
    {
        $search = $request->get('search');

        $findsByName = ProductTranslation::where('name', 'like', '%' . $search . '%')->pluck('product_id')->toArray();
        $findsByCode = Product::where('code', 'like', '%' . $search . '%')->pluck('id')->toArray();

        $finds = array_merge($findsByName, $findsByCode);

        $inSet = SetProducts::where('set_id',  $request->get('set_id'))
                            ->pluck('product_id')->toArray();

        $products = Product::with(['translation', 'mainImage', 'setImages'])->whereIn('id', array_unique($finds))->whereNotIn('id', $inSet)->get();

        return $products;
    }

    public function addProductToSet(Request $request)
    {
        $setProduct = SetProducts::where('set_id', $request->get('set_id'))
                                ->where('product_id', $request->get('product_id'))
                                ->first();

        if (is_null($setProduct)) {
            SetProducts::create([
                'set_id' => $request->get('set_id'),
                'product_id' => $request->get('product_id'),
            ]);
        }

        return Set::with(['translation', 'products.translation', 'products.mainImage', 'products.setImages'])
                        ->where('id', $request->get('set_id'))
                        ->first();
    }

    // Restfull methods
    public function create()
    {
        return view('admin::admin.collections.create');
    }

    public function edit($id)
    {
        $collection = Collection::findOrFail($id);

        return view('admin::admin.collections.edit', compact('collection'));
    }

    public function update(Request $request, $id)
    {
        $collection = Collection::findOrFail($id);
        $toValidate['title_'.$this->lang->lang] = 'required|max:255';
        $validator = $this->validate($request, $toValidate);
        $banner = $request->old_banner;
        $active = 0;

        if ($request->banner) {
            $banner = uniqid() . '-' . $request->banner->getClientOriginalName();
            $request->banner->move('images/collections', $banner);
            if ($collection->image) {
                @unlink(public_path('images/collections/'.$collection->banner));
            }
        }

        foreach ($this->langs as $lang){
            $image[$lang->lang] = '';
            if ($request->file('image_'. $lang->lang)) {
              $image[$lang->lang] = uniqid() . '-' . $request->file('image_'. $lang->lang)->getClientOriginalName();
              $request->file('image_'. $lang->lang)->move('images/collections', $image[$lang->lang]);
            }else{
                if ($request->get('old_image_'. $lang->lang)) {
                    $image[$lang->lang] = $request->get('old_image_'. $lang->lang);
                }
            }
        }

        if ($request->active == 'on') { $active = 1; }

        $collection->alias = str_slug(request('title_'.$this->lang->lang));
        $collection->banner = $banner;
        $collection->active = $active;
        $collection->save();

        $collection->translations()->delete();

        foreach ($this->langs as $lang):
            $collection->translations()->create([
                'lang_id' => $lang->id,
                'name' => request('title_' . $lang->lang),
                'description' => request('description_' . $lang->lang),
                'body' => request('body_' . $lang->lang),
                'image' => $image[$lang->lang],
                'seo_title' => request('seo_title_' . $lang->lang),
                'seo_descr' => request('seo_descr_' . $lang->lang),
                'seo_keywords' => request('seo_keywords_' . $lang->lang)
            ]);
        endforeach;

        return redirect()->back();
    }

    public function store(Request $request)
    {
        return redirect()->url('/back/product-collections');
    }

    public function show($id)
    {
        return redirect()->url('/back/product-collections');
    }

    public function destroy($id)
    {
        return redirect()->url('/back/product-collections');
    }

    public function generatePrices($set)
    {
        $currencies = Currency::orderBy('type', 'desc')->get();
        $mainCurrency = Currency::where('type', 1)->first();

        if ($currencies->count() > 0) {
            foreach ($currencies as $key => $currency) {
                $set->prices()->create([
                    'currency_id' => $currency->id,
                    'dependable' => $mainCurrency->id == $currency->id ? 1 : 0,
                ]);
            }
        }
    }

}
