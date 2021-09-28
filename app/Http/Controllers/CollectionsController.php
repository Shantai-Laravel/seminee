<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\SubProduct;
use App\Models\Page;
use App\Models\Collection;
use App\Models\Set;

class CollectionsController extends Controller
{

    public function collectionRender(Request $request, $alias)
    {
        $mainSet = Set::with([
                            'translation',
                            'personalPrice',
                            'products.category',
                            'products.translation',
                            'products.personalPrice',
                            'products.mainImage',
                            'products.setImages',
                            'products.subproducts.parameterValue.translation',
                            'products.subproducts.parameter',
                        ])
                        ->where(env('APP_SLUG'), 1)
                        ->where('id', $request->get('order'))->first();

        $collection = Collection::with([
                            'sets.translation',
                            'sets.personalPrice',
                            'sets.products.category',
                            'sets.products.translation',
                            'sets.products.personalPrice',
                            'sets.products.mainImage',
                            'sets.products.setImages',
                            'sets.products.subproducts.parameterValue.translation',
                            'sets.products.subproducts.parameter',
                        ])
                        ->where('alias', $alias)->first();

        if (is_null($collection)) {
            abort(404);
        }

        $seoData = $this->_getSeo($collection);

        return view('front.collections.collections', compact('seoData', 'collection', 'mainSet'));
    }

    public function getSets(Request $request)
    {
        $sets = Set::with(['translation', 'products.translation', 'collection', 'mainPhoto'])
                    ->where('collection_id', $request->get('collection_id'))
                    ->where(env('APP_SLUG'), 1)
                    ->paginate(3);

        return $sets;
    }

    public function setRender($collectionAlias, $setAlias)
    {
        $collection = Collection::where('alias', $collectionAlias)->first();

        if (is_null($collection)) {
            abort(404);
        }

        $set = Set::where('alias', $setAlias)->first();

        if (is_null($set)) {
            abort(404);
        }

        $similarSets = Set::where('id', '!=', $set->id)->limit(3)->inRandomOrder()->get();

        return view('front.collections.set', compact('collection', 'set', 'similarSets'));
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
