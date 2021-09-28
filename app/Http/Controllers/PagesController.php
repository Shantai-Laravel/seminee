<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Set;
use App\Models\Brand;
use App\Models\Promocode;
use App\Models\UserField;
use App\Models\Promotion;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Blog;

class PagesController extends Controller
{
    /**
     *  get action
     *  render home page
     */
    public function index()
    {
        $page = Page::where('alias', 'home')->with('translation')->first();
        $categories = ProductCategory::with(['products.category.properties.property.parameterValues.translation',
                                            'products.imagesBegin',
                                            'products.imagesLast',
                                            'products.images',
                                            'products.mainImage',
                                            'products.category.translation',
                                            'products.subproducts',
                                            'products.subproducts.parameterValue.translation',
                                            'products.subproducts.parameter.translation',
                                            'products.translation'
                                        ])
                                    ->orderBy('succesion', 'asc')
                                    ->where('on_home', 1)->get();

        $promotions = Promotion::get();
        $orderCategories = ProductCategory::where('product_type', 'order')->where('parent_id', 0)->orderBy('position', 'asc')->get();
        $preOrderCategories = ProductCategory::where('product_type', 'pre-order')->where('parent_id', 0)->orderBy('position', 'asc')->get();
        $brands = Brand::orderBy('position', 'asc')->get();


        if (is_null($page)) {
            abort(404);
        }

        $seoData = $this->getSeo($page);

        return view('front.home', compact('seoData', 'page', 'categories', 'promotions', 'orderCategories', 'preOrderCategories', 'brands'));
    }

    public function getHomeSubcategories(Request $request)
    {
        $subcategories = ProductCategory::with('translation')->where('parent_id', $request->category)->paginate(3);

        return $subcategories;
    }

    public function getHomeProducts(Request $request)
    {
        $products = Product::with('translation')->where('category_id', $request->category)->paginate(3);

        return $products;
    }

    public function getHomeCollections(Request $request)
    {
        $collections = Collection::with('translation')->paginate(6);

        return $collections;
    }

    /**
     *  get action
     *  render dinamic pages by alias
     */
    public function getPages($slug)
    {
        $page = Page::where('alias', $slug)->first();
        if (is_null($page)) {
            return redirect()->route('404');
        }

        if (view()->exists('front/pages/'.$slug)) {
            $seoData = $this->getSeo($page);
            return view('front.pages.'.$slug, compact('seoData', 'page'));
        }else{
            $seoData = $this->getSeo($page);
            return view('front.pages.default', compact('seoData', 'page'));
        }
    }

    /**
     *  get action
     *  render 404 page
     */
    public function get404()
    {
        return view('front.404');
    }

    /**
     *  get action
     *  render wellcome page
     */
    public function wellcome()
    {
        $userfields = UserField::where('in_register', 1)->get();

        return view('front.pages.wellcome', compact('userfields'));
    }


    public function getPromocode($promocodeId)
    {
        $promocode = Promocode::find($promocodeId);

        if(count($promocode) > 0) {
            session(['promocode' => $promocode]);
            return redirect()->route('home');
        }
    }

    public function getNews()
    {
        $news = Blog::orderBy('position', 'asc')->get();
        return view('front.news.news', compact('news'));
    }

    public function getNewsSingle($id)
    {
        $new = Blog::where('id', $id)->first();
        return view('front.news.newsSingle', compact('new'));
    }

    public function getBrands()
    {
        $brands = Brand::orderBy('position', 'asc')->get();
        return view('front.brands.brands', compact('brands'));
    }

    /**
     *  private method
     *  get meta datas of pages
     */
    public function getSeo($page)
    {
        $seo['title'] = $page->translation($this->lang->id)->first()->meta_title ?? $page->translation($this->lang->id)->first()->title;
        $seo['keywords'] = $page->translation($this->lang->id)->first()->meta_keywords ?? $page->translation($this->lang->id)->first()->title;
        $seo['description'] = $page->translation($this->lang->id)->first()->meta_description ?? $page->translation($this->lang->id)->first()->title;

        return $seo;
    }

}
