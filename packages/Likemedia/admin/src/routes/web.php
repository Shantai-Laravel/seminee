<?php
Route::group(['middleware' => ['web']], function ()
{
    $namespace = 'Admin\Http\Controllers';

    Route::group(['namespace' => $namespace, 'prefix' => 'back', 'middleware' => 'auth'], function ()
    {
        // Dashboard
        Route::get('/', 'AdminController@index')->name('back');

        // CRM Orders
        Route::get('/crm-orders', 'OrdersController@index');
        Route::get('/crm-orders-list', 'OrdersController@getOrdersList');
        Route::get('/crm-orders-list/guests', 'OrdersController@getOrdersListGuests');
        Route::get('/crm-orders-detail/{id}', 'OrdersController@orderDetail');
        Route::get('/crm-orders-delete', 'OrdersController@orderDelete');
        Route::post('/crm-orders-change-order-status', 'OrdersController@changeOrderStatus');
        Route::get('/crm-order-delete/{id}', 'OrdersController@orderDelete');
        Route::post('/crm-orders-search-users', 'OrdersController@searchUsers');
        Route::post('/crm-orders-search-show-all-users', 'OrdersController@searchShowAllUsers');
        Route::post('/crm-orders-search-products', 'OrdersController@searchProducts');
        Route::post('/crm-orders-get-user-cart', 'OrdersController@getUserCart');
        Route::post('/crm-orders-add-set-to-cart', 'OrdersController@addSetToCart');
        Route::post('/crm-orders-add-product-to-cart', 'OrdersController@addProductToCart');
        Route::post('/crm-orders-add-subproduct-to-cart', 'OrdersController@addSubproductToCart');
        Route::post('/crm-orders-remove-cart', 'OrdersController@removeCart');
        Route::post('/crm-orders-favorite-cart', 'OrdersController@moveToFavoriteCart');
        Route::post('/crm-orders-change-qty', 'OrdersController@changeQty');
        Route::post('/crm-apply-promocode', 'OrdersController@applyPromocode');
        Route::post('/crm-change-set-subproduct', 'OrdersController@changeSetSubproduct');
        Route::post('/crm-get-countries-list', 'OrdersController@getCountriesList');
        Route::post('/crm-get-country', 'OrdersController@getCurrentCountry');
        Route::post('/crm-get-delivery', 'OrdersController@getCurrentDelivery');
        Route::post('/crm-get-payments-list', 'OrdersController@getPaymentsList');
        Route::post('/crm-user-order', 'OrdersController@userOrder');
        Route::post('/crm-user-preorder', 'OrdersController@userPreOrder');
        Route::post('/crm-orders-deformate-set-cart', 'OrdersController@deformateSetCart');


        Route::get('/returns', 'ReturnsController@index');
        Route::get('/returns-select-order-to-return', 'ReturnsController@selectOrderToReturn');
        Route::get('/returns/{id}/order', 'ReturnsController@returnOrder');
        Route::post('/returns/store', 'ReturnsController@store');
        Route::get('/returns/{id}/show', 'ReturnsController@show');

        // Country Module
        Route::resource('/countries', 'CountriesController');
        Route::get('/countries-refresh', 'CountriesController@refresh');
        Route::resource('/currencies', 'CurrenciesController');
        Route::get('/currencies-regenerate', 'CurrenciesController@regeneratePrices');
        Route::post('/currencies-deactivate/{id}', 'CurrenciesController@deactivateCurrency');
        Route::resource('/delivery', 'DeliveriesController');
        Route::resource('/payments', 'PaymentsController');

        // Translations
        Route::get('/translations', 'TranslationsController@index');
        Route::get('/translations/generate', 'TranslationsController@createTraslation');
        Route::post('/translations-get-groups', 'TranslationsController@getGroups');
        Route::post('/translations-create-group', 'TranslationsController@createGroup');
        Route::post('/translations-save-new-line', 'TranslationsController@saveNewLine');
        Route::post('/translations-update', 'TranslationsController@updateTranslations');
        Route::post('/translations-remove', 'TranslationsController@removeTranslations');
        Route::post('/translations-search', 'TranslationsController@searchTranslations');
        Route::post('/translations-cancel-search', 'TranslationsController@cancelSearchTranslations');
        Route::post('/translations-set-active', 'TranslationsController@setActive');

        // AutoUpload
        Route::any('auto-upload', 'AutoUploadController@index')->name('auto-upload.index');
        Route::post('auto-upload-get-products', 'AutoUploadController@getProducts');
        Route::post('auto-upload-edit', 'AutoUploadController@edit');
        Route::post('auto-upload-create', 'AutoUploadController@create');
        Route::post('auto-upload-remove', 'AutoUploadController@remove');
        Route::post('auto-upload-upload-images', 'AutoUploadController@uploadImages');
        Route::post('auto-upload-get-images', 'AutoUploadController@getImages');
        Route::post('auto-upload-remove-image', 'AutoUploadController@removeImage');
        Route::post('auto-upload-main-image', 'AutoUploadController@mainImage');
        Route::post('auto-upload-first-image', 'AutoUploadController@firstImage');
        Route::post('auto-upload-edit-subproducts', 'AutoUploadController@editSubproducts');
        Route::post('auto-upload-inherit-subproducts', 'AutoUploadController@inheritSubproducts');
        Route::post('auto-upload-search', 'AutoUploadController@search');
        Route::post('auto-upload-save-sets', 'AutoUploadController@saveSets');
        Route::post('auto-upload-set-image-upload', 'AutoUploadController@uploadSetProductImage');
        Route::post('auto-upload-set-image-remove', 'AutoUploadController@removeSetProductImage');
        Route::post('auto-upload-generate-new-set', 'AutoUploadController@generateNewSet');
        Route::post('auto-upload-set-similar-products', 'AutoUploadController@setSimilarProducts');
        Route::post('auto-upload-set-similar-products-to-prod', 'AutoUploadController@setSimilarProductsToProd');

        Route::post('auto-upload-set-similar-all-products', 'AutoUploadController@setSimilarAllProducts');
        Route::post('auto-upload-upload-video', 'AutoUploadController@uploadVideo');
        Route::post('auto-upload-set-hit-products', 'AutoUploadController@setHitProduct');
        Route::post('auto-upload-set-recomended-products', 'AutoUploadController@setRecomandedProduct');
        Route::post('auto-upload-remove-video', 'AutoUploadController@removeVideo');
        Route::post('auto-upload-change-dependable-price', 'AutoUploadController@changeDependeblePrice');
        Route::post('auto-upload-save-prices', 'AutoUploadController@savePrices');
        Route::post('auto-upload-change-dependable-status', 'AutoUploadController@changeDependeblePriceSubproduct');
        Route::post('auto-upload-add-brand-to-product', 'AutoUploadController@addBrandToProduct');
        Route::post('auto-upload-update-subproducts', 'AutoUploadController@updateSubproducts');

        // Temp...
        Route::post('auto-upload-get-materials', 'AutoUploadController@getMaterials');
        Route::post('auto-upload-add-materials', 'AutoUploadController@addMaterials');
        Route::post('auto-upload-change-com-status', 'AutoUploadController@changeComStatus');
        Route::post('auto-upload-change-md-status', 'AutoUploadController@changeMdStatus');

        // Admin Users
        Route::resource('/users', 'AdminUserController');

        // Brands
        Route::resource('/brands', 'BrandsController');
        Route::post('/brands/changePosition', 'BrandsController@changePosition');
        Route::patch('/brands/{id}/change-status', 'BrandsController@status')->name('brands.change.status');

        // Promotions
        Route::get('/promotions/set-all-stauts', 'PromotionsController@setAllStatus');

        Route::resource('/promotions', 'PromotionsController');
        Route::resource('/promotions/changePosition', 'PromotionsController@changePosition');
        Route::patch('/promotions/{id}/change-status', 'PromotionsController@status')->name('promotions.change.status');

        // Promocodes
        Route::resource('/promocodes', 'PromocodesController');
        Route::resource('/promocodesType', 'PromocodeTypesController');
        Route::post('promocode/setType', 'PromocodeTypesController@getPromocodeTypes');

        // Product Collections
        Route::resource('/product-collections', 'CollectionsController');
        Route::post('/product-collections/getSets', 'CollectionsController@getSets');
        Route::post('/product-collections/changeCollections', 'CollectionsController@changeCollections');
        Route::post('/product-collections/removeCollection', 'CollectionsController@removeCollection');
        Route::post('/product-collections/add-new-collection', 'CollectionsController@addNewCollection');
        Route::post('/product-collections/add-new-set', 'CollectionsController@addNewSet');
        Route::post('/product-collections/removeSet', 'CollectionsController@removeSet');
        Route::post('/product-collections/changeSets', 'CollectionsController@changeSets');
        Route::post('/product-collections/changeProducts', 'CollectionsController@changeProducts');
        Route::post('/product-collections/removeProduct', 'CollectionsController@removeProduct');
        Route::post('/product-collections/search-product', 'CollectionsController@searchProduct');
        Route::post('/product-collections/add-product-to-set', 'CollectionsController@addProductToSet');
        Route::post('/product-collections/remove-set-product-image', 'CollectionsController@removeSetProductImage');

        // Route::resource('/collections', 'CollectionsController_old');
        // Route::post('/collections/changePosition', 'CollectionsController@changePosition');

        // Products Sets
        Route::resource('/sets', 'SetsController');
        Route::get('/sets/collection/{collectionId}', 'SetsController@getByCollection');
        Route::get('/sets/delete/gallery-item/{id}', 'SetsController@deleteGalleryItem');
        Route::get('/sets/setmain/gallery-item/{id}', 'SetsController@setMainGalleryItem');
        Route::post('/sets/collection/{id}/changePosition', 'SetsController@changePosition');

        // Galleries
        Route::resource('/galleries', 'GalleriesController');
        Route::post('/gallery/images/delete', 'GalleriesController@deleteGalleryImages')->name('gallery.images.delete');

        // Lead Mangment
        Route::resource('/feedback', 'FeedBackController');
        Route::get('/feedback/clooseStatus/{id}/{status}', 'FeedBackController@changeStatus');
        Route::get('/feedback-emmit', 'FeedBackController@emitPreorder');

        // Pages
        Route::resource('/pages', 'PagesController');
        Route::patch('/pages/save/traductions', 'PagesController@saveTraductions')->name('pages.save.traductions');
        Route::post('/pages/changePosition', 'PagesController@changePosition');
        Route::patch('/pages/{id}/change-status', 'PagesController@status')->name('pages.change.status');

        // Modules
        Route::resource('/modules', 'ModulesController');
        Route::post('/modules/changePosition', 'ModulesController@changePosition');

        // Product Categories
        Route::resource('/product-categories', 'ProductCategoryController');
        Route::post('/product-categories/get-categories', 'ProductCategoryController@getCategories');
        Route::post('/product-categories/change-position', 'ProductCategoryController@changePosition');
        Route::post('/product-categories/add-new', 'ProductCategoryController@store');
        Route::post('/product-categories/remove', 'ProductCategoryController@remove');
        Route::post('/product-categories/get-all-categories', 'ProductCategoryController@getAllCategories');
        Route::post('/product-categories/remove-moving-category', 'ProductCategoryController@removeWithMovingCategory');
        Route::post('/product-categories/move-products', 'ProductCategoryController@moveProducts');
        Route::get('/product-categories/remove-dependable-parameter/{id}', 'ProductCategoryController@removeDepedableParameter');

        // Blog Categories
        Route::resource('/blog-categories', 'BlogCategoryController');
        Route::post('/blog-categories/get-categories', 'BlogCategoryController@getCategories');
        Route::post('/blog-categories/change-position', 'BlogCategoryController@changePosition');
        Route::post('/blog-categories/add-new', 'BlogCategoryController@store');
        Route::post('/blog-categories/remove', 'BlogCategoryController@remove');
        Route::post('/blog-categories/get-all-categories', 'BlogCategoryController@getAllCategories');
        Route::post('/blog-categories/remove-moving-category', 'BlogCategoryController@removeWithMovingCategory');
        Route::post('/blog-categories/move-blogs', 'BlogCategoryController@moveBlogs');

        // Blogs
        Route::resource('/blogs', 'BlogController');
        Route::patch('/blogs/{id}/change-status', 'BlogController@status')->name('blogs.change.status');
        Route::get('/blogs/category/{id}', 'BlogController@getByCategory');

        // Parameters
        Route::resource('/parameter-groups', 'ParameterGroupsController');

        Route::resource('/parameters', 'ParametersController');
        Route::post('/parameter-store', 'ParametersController@store');
        Route::post('/parameter-update/{id}', 'ParametersController@update');
        Route::post('/remove-old-value', 'ParametersController@removeOldValue');
        Route::post('/parameter-check-dependable', 'ParametersController@checkParameterDependable');
        Route::post('/parameters/addImages', 'ParametersController@addImages');

        Route::post('/parameters/changePosition', 'ParametersController@changePosition');

        // Products
        Route::resource('/products', 'ProductsController');
        Route::get('/products/category/{category}', 'ProductsController@getProductsByCategory')->name('products.category');
        Route::get('/products/sets/{set}', 'ProductsController@getProductsBySet')->name('products.set');
        Route::post('/products/category/{category}/changePosition', 'ProductsController@changePosition')->name('products.changePosition');
        Route::post('/products/gallery/add/{product}', 'ProductsController@addProductImages')->name('products.images.add');
        Route::post('/products/gallery/edit/{product}', 'ProductsController@editProductImages')->name('products.images.edit');
        Route::get('/products/gallery/first/{id}', 'ProductsController@addFirstProductImages')->name('products.images.add.first');
        Route::post('/products/gallery/main', 'ProductsController@addMainProductImages')->name('products.images.add.main');
        Route::post('/products/gallery/delete', 'ProductsController@deleteProductImages')->name('products.images.add.delete');

        // Autometa
        Route::resource('/autometa', 'AutoMetasController');
        Route::post('/autometa/changeCategory', 'AutoMetasController@changeCategory');
        Route::post('/autometa/changeCategoryEdit', 'AutoMetasController@changeCategoryEdit');
        Route::post('/autometa/checkAutometasCategory', 'AutoMetasController@checkAutometasCategory');

        // AutoAlt
        Route::resource('/autoalt', 'AutoAltController');
        Route::post('/autoalt/exportCategories', 'AutoAltController@exportCategories')->name('autoalt.exportCategories');

        Route::resource('/guest-users', 'GuestUserController');

        // Front Users
        Route::resource('/frontusers', 'FrontUserController');
        Route::get('/frontusers/{id}/editPassword', 'FrontUserController@editPassword')->name('frontusers.editPassword');
        Route::patch('/frontusers/{id}/updatePassword', 'FrontUserController@updatePassword')->name('frontusers.updatePassword');
        Route::post('/frontusers/{user_id}/addAddress', 'FrontUserController@addAddress')->name('frontusers.addAddress');
        Route::post('/frontusers/{user_id}/updateAddress/{address_id}', 'FrontUserController@updateAddress')->name('frontusers.updateAddress');
        Route::delete('frontusers/{user_id}/deleteAddress/{address_id}', 'FrontUserController@deleteAddress')->name('frontusers.deleteAddress');

        //frisbo
        Route::get('/frisbo/synchronize-stocks', 'FrisboController@synchronizeStocks');

        Route::post('ckeditor/upload', 'AdminController@upload')->name('ckeditor.upload');

        // Settings
        Route::group(['prefix' => 'settings'], function ()
        {
            Route::resource('/languages', 'LanguagesController');
            Route::patch('/languages/set-default/{id}', 'LanguagesController@setDefault')->name('languages.default');
            Route::patch('/languages/set-active/{id}', 'LanguagesController@setActive')->name('languages.active');

            Route::get('/contacts', 'ContactController@index')->name('contacts.index');
            Route::post('/contacts', 'ContactController@store')->name('contacts.store');
            Route::post('/contacts/storeMultilang', 'ContactController@storeMultilang')->name('contacts.storeMultilang');

            Route::get('/crop', 'CropController@index')->name('crop.index');
            Route::post('/crop', 'CropController@update')->name('crop.update');
        });

    });

});
