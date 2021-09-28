<?php

$prefix = session('applocale');

Route::get('/', 'PagesController@index')->name('index');

// Front routes
Route::group(['prefix' => $prefix], function() {
    //guest user settings
    Route::post('set-user-settings', 'Controller@setUserSettings');

    Route::get('api-frisbo', 'Controller@apiFrisbo');

    Route::post('/generate-promoceode', 'FeedBackController@generatePromocode');
    Route::post('/contact-feed-back', 'FeedBackController@contactFeedBack');

    // Bootsrap Modal get countries list
    Route::post('/bootsrap-get-countries-list', 'Controller@getCountriesList');
    Route::post('/save-country-user', 'Controller@saveCountryUser');

    Route::get('/',     'PagesController@index')->name('home');
    Route::get('/home', 'PagesController@index');

    // product routes
    Route::get('/search',  'SearchController@search');
    Route::get('/new',  'ProductsController@newRender');
    Route::get('/sale', 'ProductsController@saleRender');
    Route::get('/catalog/{category}', 'ProductsController@categoryRender');
    Route::get('/catalog/{category}/{product}', 'ProductsController@productRender');
    Route::get('/promotions', 'ProductsController@renderPromotions');
    Route::get('/promotions/{id}', 'ProductsController@renderPromotionSingle');

    Route::get('/testOrederFrisbo', 'OrderController@testOrederFrisbo');
    Route::post('/product/pre-order', 'FeedBackController@productPreOrder');

    // collections routes
    Route::get('/collection/{collection}', 'CollectionsController@collectionRender');
    Route::get('/collection/{collection}/{set}', 'CollectionsController@setRender');
    // Route::post('/set/pre-order', 'FeedBackController@setPreOrder');

    Route::get('/cart',  'CartController@index')->name('cart');
    Route::get('/wish',  'WishListController@index');

    Route::get('/logout', 'Auth\AuthController@logout');
    Route::get('/login', 'Auth\AuthController@renderLogin');
    Route::get('/login/{provider}', 'Auth\AuthController@redirectToProvider');
    Route::get('/login/{provider}/callback', 'Auth\AuthController@handleProviderCallback');

    Route::get('/thanks', 'OrderController@thanks')->name('thanks');
    Route::get('/promocode/{promocodeId}', 'PagesController@getPromocode');

    // order
    Route::get('/order', 'OrderController@order')->name('order');
    Route::get('/order/payment/{orderId}', 'OrderController@orderPayment')->name('order-payment');

    Route::get('/brands', 'PagesController@getBrands')->name('brands');
    Route::get('/news', 'PagesController@getNews')->name('news');
    Route::get('/news/{id}', 'PagesController@getNewsSingle')->name('news-single');

    // Pages
    Route::get('/{pages}', 'PagesController@getPages')->name('pages');


    // Localization
    Cache::forget('country.js');
    Route::get('/js/country.js', 'Controller@setCountry')->name('assets.countries');

    // Localization
    Cache::forget('lang.js');
    Route::get('/js/lang.js', 'LanguagesController@changeLangScript')->name('assets.lang');
});

// Personal Account routes
Route::group(['prefix' => $prefix, 'middleware' => 'auth_front'], function() {
    Route::get('/account/personal-data', 'AccountController@index')->name('account');
    Route::get('/account/promocodes', 'AccountController@getPromocodes')->name('account-promocodes');
    Route::get('/account/cart', 'AccountController@getCart')->name('account-cart');
    Route::get('/account/wishlist', 'AccountController@getWishlist')->name('account-wishlist');
    Route::post('/account/savePersonalData', 'AccountController@savePersonalData')->name('account.savePersonalData');
    Route::post('/account/changePass', 'AccountController@savePass')->name('account.savePass');
    Route::post('/account/addAddress', 'AccountController@addAddress')->name('account.addAddress');
    Route::post('/account/saveAddress/{id}', 'AccountController@saveAddress')->name('account.saveAddress');

    Route::get('/account/history', 'AccountController@history')->name('account.history');
    Route::post('/account/historyCart/{id}', 'AccountController@historyCart')->name('account.historyCart');
    Route::get('/account/history/order/{order}', 'AccountController@historyOrder')->name('account.historyOrder');
    Route::post('/account/historyCartProduct/{id}', 'AccountController@historyCartProduct')->name('account.historyCartProduct');

    Route::get('/account/returns', 'AccountController@returns')->name('account.returns');
    Route::get('/account/returns/create', 'AccountController@createReturn');
    Route::get('/account/returns/create/{id}', 'AccountController@createReturnFromOrder');
    Route::post('/account/returns/store', 'AccountController@storeReturn');
    Route::get('/account/returns/{id}', 'AccountController@showReturn');

    Route::get('/account/return', 'AccountController@return')->name('account.return');
    Route::get('/account/return/order/{order}', 'AccountController@returnOrder')->name('account.returnOrder');
    Route::post('/account/return/addProductsToReturn/{order}', 'AccountController@addProductsToReturn')->name('account.addProductsToReturn');
    Route::post('/account/return/saveReturn/{return}', 'AccountController@saveReturn')->name('account.saveReturn');
});
