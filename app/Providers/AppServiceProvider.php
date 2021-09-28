<?php

namespace App\Providers;

use App\Base as Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\FrontUserUnlogged;
use App\Models\Lang;
use App\Models\Module;
use App\Models\Cart;
use App\Models\Page;
use App\Models\WishList;
use App\Models\WishListSet;
use App\Models\ProductCategory;
use App\Models\Collection;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Set;
use App\Models\Product;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (@$_COOKIE['lang_id']) {
            $lang = Lang::where('lang', @$_COOKIE['lang_id'])->first();
            if (is_null($lang)) {
                $lang = Lang::where('default', '1')->first();
            }
        }else{
            $lang = Lang::where('default', '1')->first();
        }

        if (!is_null($lang)) {
            $currentLang = Lang::where('lang', \Request::segment(1))->first()->lang ?? $lang->lang;
            session(['applocale' => $currentLang]);
            \App::setLocale($currentLang);

            $lang = Lang::where('lang', session('applocale'))->first();
            $country = Country::where('id', @$_COOKIE['country_id'] ?? Country::first()->id)->first();
            $mainCurrency = Currency::where('type', 1)->first();
            // $currency = Currency::where('id', @$_COOKIE['currency_id'])->first() ?? $mainCurrency;
            $currency = Currency::where('id', 5)->first() ?? $mainCurrency;
            $unloggedUser = FrontUserUnlogged::where('user_id', @$_COOKIE['user_id'])->first();

            $seo['title'] = env('SEO_TITLE');
            $seo['keywords'] = env('SEO_KEYWORDS');
            $seo['description'] = env('SEO_DESCRIPTION');

            $this->setUserId();
            // $this->setStocks();

            if(\Request::segment(1) == 'back'){
                View::share('menu', Module::where('parent_id', 0)->orderBy('position')->get());
            }else{
                $categoriesMenu = ProductCategory::where('parent_id', 0)->where('active', 1)->orderBy('position', 'asc')->get();
                $collectionsMenu = Collection::where('active', 1)->orderBy('position', 'asc')->get();
                $this->shareCarts();

                View::share('categoriesMenu', $categoriesMenu);
                View::share('collectionsMenu', $collectionsMenu);
            }

            View::share('langs', Lang::orderBy('id', 'asc')->get());
            View::share('lang', $lang);
            View::share('countries', Country::orderBy('id', 'asc')->get());
            View::share('country', $country);
            View::share('currencies', Currency::where('id', 5)->orderBy('id', 'asc')->get());
            View::share('currency', !is_null($currency) ? $currency : $mainCurrency);
            View::share('mainCurrency', $mainCurrency);
            View::share('seoData', $seo);
            View::share('unloggedUser', $unloggedUser);

            Model::$lang = $lang->id;
            Model::$currency = $currency->id;
            Model::$mainCurrency = $mainCurrency->id;

        }else{
            exit('language is not exists!');
        }
    }

    public function shareCarts()
    {
        View::composer('*', function ($view)
        {
            if(auth('persons')->guest() && isset($_COOKIE['user_id'])){
                $cartProducts = Cart::with(['product.translation','product.mainImage'])
                              ->where('user_id', $_COOKIE['user_id'])->where('parent_id', null)->orderBy('id', 'desc')->get();

                $wishProducts = WishList::with(['product.translation','product.mainImage', 'subproduct'])
                                      ->where('user_id', $_COOKIE['user_id'])->get();

                $wishSets = WishListSet::with(['set.translation','set.mainPhoto'])
                                    ->where('user_id', $_COOKIE['user_id'])->get();

                $wishListIds = $wishProducts->pluck('product_id')->toArray();
            }else{
                $cartProducts = Cart::with(['product.translation','product.mainImage', 'subproduct'])
                                        ->where('user_id', auth('persons')->id())->where('parent_id', null)->orderBy('id', 'desc')->get();

                $wishProducts = WishList::with(['product.translation','product.mainImage', 'subproduct'])
                                    ->where('user_id', auth('persons')->id())->get();

                $wishSets = WishListSet::with(['set.translation','set.mainPhoto'])
                                    ->where('user_id', auth('persons')->id())->get();

                $wishListIds = $wishProducts->pluck('product_id')->toArray();
            }

            View::share('cartProducts', json_encode($cartProducts));
            View::share('wishProducts', json_encode($wishSets));
            View::share('wishSets', json_encode($wishProducts));
            View::share('wishListIds', $wishListIds);
        });
    }

    public function setUserId()
    {
        $user_id = md5(rand(0, 9999999).date('Ysmsd'));

        if (\Cookie::has('user_id')) {
            $value = \Cookie::get('user_id');
        }else{
            setcookie('user_id', $user_id, time() + 10000000, '/');
            $value = \Cookie::get('user_id');
        }
    }

    protected function setStocks()
    {
        $sets = Set::select('id')->get();

        foreach ($sets as $key => $set) {
            $stock = false;
            if ($set->products->count() > 0) {
                foreach ($set->products as $key => $product) {
                    if ($product->subproducts->count() > 0) {
                        foreach ($product->subproducts as $key => $subproduct) {
                            if (($stock == false || $subproduct->stoc < $stock) && $subproduct->stoc !== 0) {
                                $stock = $subproduct->stoc;
                            }
                        }
                    }else{
                        if ($stock == false || $product->stock < $stock) {
                            $stock = $product->stock;
                        }
                    }
                }
            }
            $set->update(['stock' => $stock]);
        }

        $this->setStocksOuts($sets);
    }

    protected function setStocksOuts($sets)
    {
        foreach ($sets as $key => $set) {
            $stock = false;
            if ($set->products->count() > 0) {
                foreach ($set->products as $key => $product) {
                    if ($product->subproducts->count() > 0) {
                        $subproductStock = 0;
                        foreach ($product->subproducts as $key => $subproduct) {
                            $subproductStock += $subproduct->stoc;
                        }
                        if ($subproductStock == 0) {
                            $set->update(['stock' => $stock]);
                            break;
                        }
                    }else{
                        if ($product->stock == 0) {
                            $set->update(['stock' => $product->stock]);
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
