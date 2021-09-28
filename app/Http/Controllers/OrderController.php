<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Mollie\Laravel\Facades\Mollie;
use App\FrontUser;
use App\Models\Order;
use App\Models\Cart;
use App\Models\WishList;
use App\Models\CartSet;
use App\Models\Contact;
use App\Models\FrontUserUnlogged;
use App\Models\FrontUserAddress;
use App\Models\Promocode;
use App\Models\PromocodeType;
use App\Models\Collection;
use App\Models\Set;
use App\Models\Product;
use App\Models\SubProduct;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\Delivery;
use App\Models\CRMOrders;
use App\Models\CRMOrderItem;


class OrderController extends Controller
{
    public $token;

    /**
     *   Order Shiping Page render
     */
    public function order()
    {
        $message = false;
        $user = $this->checkIfLogged();
        $cartSubprods = Cart::where('user_id', $user['user_id'])
                            ->where('parent_id', null)
                            ->where('subproduct_id', '!=', null)
                            ->get();

        $cartSets = Cart::where('user_id', $user['user_id'])
                          ->where('parent_id', null)
                          ->where('set_id', '!=', 0)
                          ->get();

        foreach ($cartSubprods as $key => $cartSubprod) {
            if ($cartSubprod->stock_qty == 0) {
                Cart::where('id', $cartSubprod->id)->delete();
                $message = "Unu sau mai multe produse din cos au fost deja cumparate";
            }
        }

        foreach ($cartSets as $key => $cartSet) {
            if ($cartSet->stock_qty == 0) {
                Cart::where('id', $cartSubprod->id)->delete();
                $message = "Unu sau mai multe seturi din cos au fost deja cumparate";
            }
        }
        $message = "Unu sau mai multe seturi din cos au fost deja cumparate";


        return view('front.order.order', compact('message'));
    }

    /**
     *  Order Payment Page render
     */
    public function orderPayment($orderId)
    {
        $user = $this->checkIfLogged();

        if ($user['status'] == 'guest') {
            $order = CRMOrders::where('id', $orderId)->where('guest_user_id',  $user['guest_id'])->where('step', 1)->first();
        }else{
            $order = CRMOrders::where('id', $orderId)->where('user_id', $user['user_id'])->where('step', 1)->first();
        }

        if (!is_null($order)) {
            return view('front.order.order-payment', compact('order'));
        }else{
            abort(404);
        }
    }

    /**
     *   Post:: get user full info
     */
    public function getUser(Request $request)
    {
        $data['payment_id'] = 0;

        if (\Auth::guard('persons')->user()){
            $country = Country::find(@$_COOKIE['country_id']);
            $data['mode'] = "auth";
            $data['frontUser'] = FrontUser::with([
                                'address.getCountryById',
                                'address.getCountryById.translation',
                                'address.getCountryById.deliveries',
                                'address.getCountryById.mainDelivery',
                                'address.getCountryById.payments',
                                'address.getCountryById.payments.payment.translation'
                            ])
                            ->find(Auth::guard('persons')->id());

            if (!is_null($data['frontUser']->address)) {
                $data['phone_code'] = $data['frontUser']->address->phone_code;
                $data['phone'] = $data['frontUser']->address->phone;
            }else{
                $data['phone_code'] = $country->phone_code;
                $data['phone'] = $data['frontUser']->phone;
            }
            $data['payment_id'] = $data['frontUser']->payment_id;
        }else{
            $country = Country::find(@$_COOKIE['country_id']);
            $data['mode'] = "guest";
            $data['country'] = $country->id;
            $data['frontUser'] = FrontUserUnlogged::where('user_id', @$_COOKIE['user_id'])->first();
            $data['phone_code'] = $country->phone_code;
            $data['phone'] = $data['frontUser']->phone;
        }

        if (@$_COOKIE['country_delivery_id']) {
            $data['country'] = @$_COOKIE['country_delivery_id'];
        }

        return $data;
    }

    /**
     *  Post:: event on change country
     */
    public function changeCountry(Request $request)
    {
        return Country::with([
                        'translation',
                        'deliveries.delivery.translation',
                        'mainDelivery',
                        'payments.payment.translation'
                    ])
                    ->where('id', $request->get('countryId'))
                    ->where('active', 1)
                    ->first();
    }


    /**
     *  Post:: get order payment methods
     */
    public function orderGetPayments(Request $request)
    {
        return Mollie::api()->methods()->allActive(['include' => 'pricing']);
    }

    /**
     *  Get:: finish order and redirect to checkout api url
     */
    public function orderPaymentByMethod($methodId, $amount, $orderId)
    {
        $user = $this->checkIfLogged();

        $order = CRMOrders::where('id', $orderId)->where('step', 1)->first();
        $amount = number_format((float)$amount, 2, '.', '');

        $this->finishOrder($order->id, $user);

        if (!is_null($order)) {
            $payment = Mollie::api()->payments()->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => $amount,
                ],
                'method' => $methodId,
                'description' => 'Test payment',
                'webhookUrl' => route('webhooks.mollie', ['orderId', $order->id]),
                'redirectUrl' => route('thanks'),
            ]);

            $payment = Mollie::api()->payments()->get($payment->id);

            return redirect($payment->getCheckoutUrl(), 303);
        }else{
            abort(404);
        }
    }

    /**
     *   Post:: Mollie webhook
     */
    public function webhookMollie(Request $request, $orderId)
    {
        $order = CRMOrders::where('id', $orderId)->first();

        if (! $request->has('id')) {
           return;
       }

       $payment = Mollie::api()->payments()->get($request->id);

       if ($payment->isPaid()) {
           $order->update([
               'label' => 'Payed',
           ]);
       }else{
           $order->update([
               'label' => 'Expired',
           ]);
           return;
       }
    }

    /**
     *   Private:: finish order
     */
    public function finishOrder($orderId, $user)
    {
        $carts = $this->getAllCarts($user['user_id']);
        $order = CRMOrders::find($orderId);
        $currency = Currency::where('abbr', 'EUR')->first();

        if (count($carts['subproducts']) > 0) {
            foreach ($carts['subproducts'] as $key => $product) {
                if ($product->stock_qty > 0) {
                    CRMOrderItem::create([
                        'order_id'      => $order->id,
                        'subproduct_id' => $product->subproduct_id,
                        'product_id'    => 0,
                        'qty'           => $product->qty,
                        'discount'      => $product->subproduct->discount,
                        'code'          => $product->subproduct->code,
                        'old_price'     => $product->subproduct->product->mainPrice->old_price,
                        'price'         => $product->subproduct->product->mainPrice->price,
                        'currency'      => $currency->abbr,
                    ]);
                    SubProduct::where('id', $product->subproduct_id)->update(['stoc' => $product->subproduct->stoc - $product->qty]);
                }
            }
        }

        if (count($carts['sets']) > 0) {
            foreach ($carts['sets'] as $key => $set) {
                if ($set->stock_qty > 0) {
                    $list = CRMOrderItem::create([
                                'order_id'  => $order->id,
                                'set_id'    => $set->set_id,
                                'product_id'=> 0,
                                'qty'       => $set->qty,
                                'code'      => $set->set->code,
                                'old_price' => $set->set->mainPrice->old_price,
                                'price'     => $set->set->mainPrice->price,
                                'currency'  => $currency->abbr,
                            ]);

                    Set::where('id', $set->set_id)->update(['stock' => $set->set->stock - $set->qty]);

                    if ($set->children()->get()) {
                        foreach ($set->children()->get() as $key => $chid) {
                            CRMOrderItem::create([
                                'order_id'  => $order->id,
                                'parent_id' => $list->id,
                                'set_id'    => $chid->set_id,
                                'product_id' => 0,
                                'subproduct_id' => $chid->subproduct_id,
                                'qty'       => $chid->qty,
                                'currency'  => $currency->abbr,
                            ]);
                            if (!is_null($chid->subproduct)) {
                                SubProduct::where('id', $chid->subproduct_id)->update(['stoc' => $chid->subproduct->stoc - $chid->qty]);
                            }else{
                                Product::where('id', $chid->product_id)->update(['stock' => $chid->product->stock - $chid->qty]);
                            }
                        }
                    }
                }
            }
        }

        // set promocode
        $promocode = Promocode::where('name', @$_COOKIE['promocode'])->first();

        $this->checkPromocode($promocode, $user);
        setcookie('promocode', '', time() + 10000000, '/');

        if(Auth::guard('persons')->user()) {
            $user = FrontUser::find(Auth::guard('persons')->id());
            $user_id = $user->id;
            $promoType = PromocodeType::where('name', 'user')->first();
        } else {
            $user_id = 0;
            $promoType = PromocodeType::where('name', 'repeated')->first();
        }

        $this->createPromocode($promoType, $user_id);

        $user = $this->checkIfLogged();
        Cart::where('user_id', $user['user_id'])->delete();

        //  send emails
        $email = $order->details->email;
        if($user['status'] == 'guest') {
            $data['user'] = FrontUserUnlogged::where('user_id', @$_COOKIE['user_id'])->first();
            $data['promocode'] = Promocode::where('user_id', 0)
                                        ->whereRaw('to_use < times')
                                        ->where('valid_to', '>', date('Y-m-d'))
                                        ->orderBy('id', 'desc')->first();

            Mail::send('mails.order.guest', $data, function($message) use ($email){
                $message->to($email, 'Order succesefully placed on juliaallert.com.')->from('julia.allert.fashion@gmail.com')->subject('Order succesefully placed on juliaallert.com.');
            });
        }else{
            $data['user'] = FrontUser::find(Auth::guard('persons')->id());
            $data['promocode'] = Promocode::where('user_id', $data['user']->id)
                                        ->whereRaw('to_use < times')
                                        ->where('valid_to', '>', date('Y-m-d'))
                                        ->orderBy('id', 'desc')->first();

            Mail::send('mails.order.user', $data, function($message) use ($email){
                $message->to($email, 'Order succesefully placed on juliaallert.com.')->from('julia.allert.fashion@gmail.com')->subject('Order succesefully placed on juliaallert.com.');
            });
        }

        $order->update([
            'step' => 2,
            'label' => 'With payment details',
        ]);

        // frisbo sinchornizate
        // $this->sentOrderToFrisbo($order);

        return $order->id;
    }

    /**
     *  Order shipping render page
     */
     public function orderShipping(Request $request)
     {
         $user = $this->checkIfLogged();
         $userAddress = 0;

         $country    = Country::find($request->get('country'));
         $currency   = Currency::where('abbr', $request->get('cartData')['currency'])->first();
         $delivery   = Delivery::find($request->get('cartData')['delivery']);
         $payment    = Payment::find($request->get('payment'));
         $promocode  = Promocode::where('name', @$request->get('cartData')['promocode'])->first();

         // set empty data of users
         if ($user['status'] == 'auth') {
             $frontUser = FrontUser::find($user['user_id']);
             FrontUser::where('id', $frontUser->id)->update([
                 'name' => $frontUser->name ? $frontUser->name : $request->get('name'),
                 'email' => $frontUser->email ? $frontUser->email : $request->get('email'),
                 'phone' => $frontUser->phone ? $frontUser->phone : $request->get('phone'),
                 'code' => $frontUser->code ? $frontUser->code : $request->get('code'),
             ]);
         }else{
             $frontGuest = FrontUserUnlogged::where('user_id', $user['user_id'])->first();
             FrontUserUnlogged::where('id', $frontGuest->id)->update([
                 'name' => $frontGuest->name ? $frontGuest->name : $request->get('name'),
                 'email' => $frontGuest->email ? $frontGuest->email : $request->get('email'),
                 'phone' => $frontGuest->phone ? $frontGuest->phone : $request->get('phone'),
                 'code' => $frontGuest->code ? $frontGuest->code : $request->get('code'),
             ]);
         }

         // set default address
         if ($request->get('saveAddress') && $user['status'] !== 'guest'){
             if ($request->get('defaultPayment')){
                 FrontUser::where('id', $user['user_id'])->update([
                     'payment_id' => $request->get('payment')
                 ]);
             }else{
                 FrontUser::where('id', $user['user_id'])->update([
                     'payment_id' => 0
                 ]);
             }

             FrontUserAddress::where('front_user_id', $user['user_id'])->delete();
             $userAddress = FrontUserAddress::create([
                 'front_user_id' => $user['user_id'],
                 'country'       => $request->get('country'),
                 'region'        => $request->get('region'),
                 'location'      => $request->get('city'),
                 'address'       => $request->get('address'),
                 'code'          => $request->get('zip'),
                 'homenumber'    => $request->get('apartment'),
                 'phone_code'    => $request->get('phone_code'),
                 'phone'         => $request->get('phone'),
             ]);
         }

         // create order
         $ordersCount = CRMOrders::get();
         $order = CRMOrders::create([
             'order_hash'        => 1000 + $ordersCount->count(),
             'user_id'           => $user['status'] == 'auth' ? $user['user_id'] : 0,
             'guest_user_id'     => $user['status'] == 'guest' ? $user['guest_id'] : 0,
             'address_id'        => $userAddress ? $userAddress->id : 0,
             'promocode_id'      => !is_null($promocode) ? $promocode->id : null,
             'currency_id'       => !is_null($currency) ? $currency->id : null,
             'payment_id'        => $request->get('payment'),
             'delivery_id'       => $request->get('cartData')['delivery'],
             'country_id'        => $request->get('country'),
             'amount'            => $request->get('cartData')['amount'],
             'main_status'       => 'pendding',
             'change_status_at'  => date('Y-m-d'),
             'step'              => 1,
             'label'             => 'With shipping details',
         ]);

         // create order details
         $order->details()->create([
             'contact_name'      => $request->get('name'),
             'email'             => $request->get('email'),
             'promocode'         => !is_null($promocode) ? $promocode->name : null,
             'code'              => $request->get('code'),
             'phone'             => $request->get('phone'),
             'currency'          => @$currency->abbr,
             'payment'           => @$payment->translation->name,
             'delivery'          => @$delivery->translation->name,
             'country'           => @$country->translation->name,
             'region'            => $request->get('region'),
             'city'              => $request->get('city'),
             'address'           => $request->get('address'),
             'apartment'         => $request->get('apartment'),
             'zip'               => $request->get('zip'),
             'delivery_price'    => @$delivery->price,
             'tax_price'         => $request->get('cartData')['tax'],
         ]);

         return $order->id;
     }

    // to delete
    public function makeOrder(Request $request)
    {
        $user = $this->checkIfLogged();

        $userAddress = 0;
        $country    = Country::find($request->get('country'));
        $currency   = Currency::where('abbr', $request->get('cartData')['currency'])->first();
        $delivery   = Delivery::find($request->get('cartData')['delivery']);
        $payment    = Payment::find($request->get('payment'));
        $promocode  = Promocode::where('name', @$request->get('cartData')['promocode'])->first();

        // set empty data of users
        if ($user['status'] == 'auth') {
            $frontUser = FrontUser::find($user['user_id']);
            FrontUser::where('id', $frontUser->id)->update([
                'name' => $frontUser->name ? $frontUser->name : $request->get('name'),
                'email' => $frontUser->email ? $frontUser->email : $request->get('email'),
                'phone' => $frontUser->phone ? $frontUser->phone : $request->get('phone'),
                'code' => $frontUser->code ? $frontUser->code : $request->get('code'),
            ]);
        }else{
            $frontGuest = FrontUserUnlogged::where('user_id', $user['user_id'])->first();
            FrontUserUnlogged::where('id', $frontGuest->id)->update([
                'name' => $frontGuest->name ? $frontGuest->name : $request->get('name'),
                'email' => $frontGuest->email ? $frontGuest->email : $request->get('email'),
                'phone' => $frontGuest->phone ? $frontGuest->phone : $request->get('phone'),
                'code' => $frontGuest->code ? $frontGuest->code : $request->get('code'),
            ]);
        }

        // set default address
        if ($request->get('saveAddress') && $user['status'] !== 'guest'){
            if ($request->get('defaultPayment')){
                FrontUser::where('id', $user['user_id'])->update([
                    'payment_id' => $request->get('payment')
                ]);
            }else{
                FrontUser::where('id', $user['user_id'])->update([
                    'payment_id' => 0
                ]);
            }

            FrontUserAddress::where('front_user_id', $user['user_id'])->delete();
            $userAddress = FrontUserAddress::create([
                            'front_user_id' => $user['user_id'],
                            'country'       => $request->get('country'),
                            'region'        => $request->get('region'),
                            'location'      => $request->get('city'),
                            'address'       => $request->get('address'),
                            'code'          => $request->get('zip'),
                            'homenumber'    => $request->get('apartment'),
                            'phone_code'    => $request->get('phone_code'),
                            'phone'         => $request->get('phone'),
                        ]);
        }

        // create order
        $ordersCount = CRMOrders::get();
        $order = CRMOrders::create([
            'order_hash'        => 1000 + $ordersCount->count(),
            'user_id'           => $user['status'] == 'auth' ? $user['user_id'] : 0,
            'guest_user_id'     => $user['status'] == 'guest' ? $user['guest_id'] : 0,
            'address_id'        => $userAddress ? $userAddress->id : 0,
            'promocode_id'      => !is_null($promocode) ? $promocode->id : null,
            'currency_id'       => !is_null($currency) ? $currency->id : null,
            'payment_id'        => $request->get('payment'),
            'delivery_id'       => $request->get('cartData')['delivery'],
            'country_id'        => $request->get('country'),
            'amount'            => $request->get('cartData')['amount'],
            'main_status'       => 'pendding',
            'change_status_at'  => date('Y-m-d'),
        ]);

        $order->details()->create([
            'contact_name'      => $request->get('name'),
            'email'             => $request->get('email'),
            'promocode'         => !is_null($promocode) ? $promocode->name : null,
            'code'              => $request->get('code'),
            'phone'             => $request->get('phone'),
            'currency'          => @$currency->abbr,
            'payment'           => @$payment->translation->name,
            'delivery'          => @$delivery->translation->name,
            'country'           => @$country->translation->name,
            'region'            => $request->get('region'),
            'city'              => $request->get('city'),
            'address'           => $request->get('address'),
            'apartment'         => $request->get('apartment'),
            'zip'               => $request->get('zip'),
            'delivery_price'    => @$delivery->price,
            'tax_price'         => $request->get('cartData')['tax'],
        ]);

        $carts = $this->getAllCarts($user['user_id']);

        if (count($carts['subproducts']) > 0) {
            foreach ($carts['subproducts'] as $key => $product) {
                if ($product->stock_qty > 0) {
                    CRMOrderItem::create([
                        'order_id'      => $order->id,
                        'subproduct_id' => $product->subproduct_id,
                        'product_id'    => 0,
                        'qty'           => $product->qty,
                        'discount'      => $product->subproduct->discount,
                        'code'          => $product->subproduct->code,
                        'old_price'     => $product->subproduct->product->mainPrice->old_price,
                        'price'         => $product->subproduct->product->mainPrice->price,
                        'currency'      => $currency->abbr,
                    ]);
                    SubProduct::where('id', $product->subproduct_id)->update(['stoc' => $product->subproduct->stoc - $product->qty]);
                }
            }
        }

        if (count($carts['sets']) > 0) {
            foreach ($carts['sets'] as $key => $set) {
                if ($set->stock_qty > 0) {
                    $list = CRMOrderItem::create([
                                'order_id'  => $order->id,
                                'set_id'    => $set->set_id,
                                'product_id'=> 0,
                                'qty'       => $set->qty,
                                'code'      => $set->set->code,
                                'old_price' => $set->set->mainPrice->old_price,
                                'price'     => $set->set->mainPrice->price,
                                'currency'  => $currency->abbr,
                            ]);

                    Set::where('id', $set->set_id)->update(['stock' => $set->set->stock - $set->qty]);

                    if ($set->children()->get()) {
                        foreach ($set->children()->get() as $key => $chid) {
                            CRMOrderItem::create([
                                'order_id'  => $order->id,
                                'parent_id' => $list->id,
                                'set_id'    => $chid->set_id,
                                'product_id' => 0,
                                'subproduct_id' => $chid->subproduct_id,
                                'qty'       => $chid->qty,
                                'currency'  => $currency->abbr,
                            ]);
                            if (!is_null($chid->subproduct)) {
                                SubProduct::where('id', $chid->subproduct_id)->update(['stoc' => $chid->subproduct->stoc - $chid->qty]);
                            }else{
                                Product::where('id', $chid->product_id)->update(['stock' => $chid->product->stock - $chid->qty]);
                            }
                        }
                    }
                }
            }
        }

        // set promocode
        $this->checkPromocode($promocode, $user);
        setcookie('promocode', '', time() + 10000000, '/');

        if(Auth::guard('persons')->user()) {
            $user = FrontUser::find(Auth::guard('persons')->id());
            $user_id = $user->id;
            $promoType = PromocodeType::where('name', 'user')->first();
        } else {
            $user_id = 0;
            $promoType = PromocodeType::where('name', 'repeated')->first();
        }

        $this->createPromocode($promoType, $user_id);

        $user = $this->checkIfLogged();

        Cart::where('user_id', $user['user_id'])->delete();

        //  send emails
        $email = $request->get('email');
        if($user['status'] == 'guest') {
            $data['user'] = FrontUserUnlogged::where('user_id', @$_COOKIE['user_id'])->first();
            $data['promocode'] = Promocode::where('user_id', 0)
                                        ->whereRaw('to_use < times')
                                        ->where('valid_to', '>', date('Y-m-d'))
                                        ->orderBy('id', 'desc')->first();

            Mail::send('mails.order.guest', $data, function($message) use ($email){
                $message->to($email, 'Order succesefully placed on juliaallert.com.')->from('julia.allert.fashion@gmail.com')->subject('Order succesefully placed on juliaallert.com.');
            });
        }else{
            $data['user'] = FrontUser::find(Auth::guard('persons')->id());
            $data['promocode'] = Promocode::where('user_id', $data['user']->id)
                                        ->whereRaw('to_use < times')
                                        ->where('valid_to', '>', date('Y-m-d'))
                                        ->orderBy('id', 'desc')->first();

            Mail::send('mails.order.user', $data, function($message) use ($email){
                $message->to($email, 'Order succesefully placed on juliaallert.com.')->from('julia.allert.fashion@gmail.com')->subject('Order succesefully placed on juliaallert.com.');
            });
        }

        // frisbo sinchornizate
        // $this->sentOrderToFrisbo($order);

        return $order->id;
    }

    /**
     *   Private:: check promocode
     */
    private function checkPromocode($promocode, $user)
    {
        if (!is_null($promocode)) {
            if ($promocode->status == 'valid') {
                $promocode->update([
                    'status' => 'used',
                    'to_use' => $promocode->to_use + 1,
                ]);
            }
        }

        setcookie('promocode', '', time() + 10000000, '/');
    }

    /**
     *  private method
     *  Create promocode
     */
    private function createPromocode($promoType, $userId) {
        if (!is_null($promoType)) {
            $promocode = Promocode::create([
              'user_id' => $userId,
              'name' => $promoType->name.''.str_random(5),
              'type_id' => $promoType->id,
              'discount' => $promoType->discount,
              'valid_from' => date('Y-m-d'),
              'valid_to' => date('Y-m-d', strtotime(' + '.$promoType->period.' days')),
              'period' => $promoType->period,
              'treshold' => $promoType->treshold,
              'to_use' => 0,
              'times' => $promoType->times,
              'status' => 'valid',
              'user_id' => $userId
            ]);

            return $promocode;
        }
    }

    // get updated carts of user
    private function getAllCarts($userId)
    {
        $this->validateStocks($userId);

        $data['products'] = Cart::with(['product.mainPrice', 'product.translation', 'product.mainImage'])
                                      ->where('user_id', $userId)
                                      ->where('parent_id', null)
                                      ->where('product_id', '!=', null)
                                      ->orderBy('id', 'desc')
                                      ->get();

        $data['sets'] = Cart::with(['children', 'set.price', 'set.translation', 'set.mainPhoto', 'set.products.translation', 'set.products.mainImage', 'set.products.firstSubproduct', 'set.products.subproducts.parameterValue.translation'])
                                      ->where('user_id', $userId)
                                      ->where('parent_id', null)
                                      ->where('set_id', '!=', 0)
                                      ->orderBy('id', 'desc')
                                      ->get();

        $data['subproducts'] = Cart::with(['subproduct.price', 'subproduct.product.translation', 'subproduct.product.mainImage', 'subproduct.parameterValue.translation'])
                                    ->where('user_id', $userId)
                                    ->where('parent_id', null)
                                    ->where('subproduct_id', '!=', null)
                                    ->orderBy('id', 'desc')
                                    ->get();

        return $data;
    }

    private function checkIfLogged()
    {
        if(auth('persons')->guest()) {
            $guest = FrontUserUnlogged::where('user_id', @$_COOKIE['user_id'])->first();
            if (!is_null($guest)) {
                return array('is_logged' => 1, 'user_id' => @$_COOKIE['user_id'], 'status' => 'guest', 'guest_id' => $guest->id);
            }else{
                return array('is_logged' => 0, 'user_id' => @$_COOKIE['user_id'], 'status' => 'user');
            }
        }else{
            return array('is_logged' => 1, 'user_id' => auth('persons')->id(), 'status' => 'auth');
        }
    }

    /**
     * VALIDATE METHODS
    **/

     // post: validate stocks
     public function validateStocks($userId)
     {
         $data['sets'] = Cart::where('user_id', $userId)
                           ->where('parent_id', null)
                           ->where('set_id', '!=', 0)
                           ->orderBy('id', 'desc')
                           ->get();

         $data['products'] = Cart::where('user_id', $userId)
                               ->where('parent_id', null)
                               ->where('product_id', '!=', null)
                               ->orderBy('id', 'desc')
                               ->get();

         $data['subproducts'] = Cart::where('user_id', $userId)
                                 ->where('parent_id', null)
                                 ->where('subproduct_id', '!=', null)
                                 ->orderBy('id', 'desc')
                                 ->get();


         foreach ($data['products'] as $key => $product) {
             $this->validateProductStock($product);
         }
         // foreach ($data['subproducts'] as $key => $subproduct) {
         //     $this->validateSubproductStock($subproduct);
         // }
         // foreach ($data['sets'] as $key => $set) {
         //     $this->validateSetStock($set);
         // }
     }

     // post: validate sets stocks
     public function validateSetStock($setCart)
     {
         $setStock = $setCart->qty;

         foreach ($setCart->children as $key => $child) {
             if ($child->subproduct_id !== null) {
                 $subCartsSum = Cart::where('user_id', $setCart->user_id)
                                 ->where('id', '!=', $child->id)
                                 ->where('subproduct_id', $child->subproduct_id)
                                 ->get()->sum('qty');

                 $subStock = SubProduct::find($child->subproduct_id)->stoc;
                 $stock_qty = ($subStock - $subCartsSum) > 0 ? $subStock - $subCartsSum : 0;
                 $qty = ($child->qty > $stock_qty) || ($child->qty === 0) ? $stock_qty : $child->qty;

                 $child->update(['stock_qty' => $stock_qty, 'qty' => $qty]);
             }else{
                 $prodCartsSum = Cart::where('user_id', $setCart->user_id)
                                 ->where('id', '!=', $child->id)
                                 ->where('product_id', $child->product_id)
                                 ->get()->sum('qty');


                 $prodStock = Product::find($child->product_id)->stock;
                 $stock_qty = ($prodStock - $prodCartsSum) > 0 ? $prodStock - $prodCartsSum : 0;
                 $qty =($child->qty > $stock_qty) || ($child->qty === 0) ? $stock_qty : $child->qty;

                 $child->update(['stock_qty' => $stock_qty, 'qty' => $qty]);
             }
         }

         $stock_qty = $setCart->children->min('stock_qty');
         $qty = ($setCart->qty > $stock_qty) || ($setCart->qty === 0)  ? $stock_qty : $setCart->qty;

         $setCart->update(['stock_qty' => $stock_qty, 'qty' => $qty]);
     }

     // post: validate products stocks
     public function validateProductStock($productCart)
     {
         $productStock = $productCart->qty;

         $prodCartsSum = Cart::where('user_id', $productCart->user_id)
                             ->where('id', '!=', $productCart->id)
                             ->where('product_id', $productCart->product_id)
                             ->get()->sum('qty');

         $prodStock = Product::find($productCart->product_id)->stock;
         $stock_qty = ($prodStock - $prodCartsSum) > 0 ? $prodStock - $prodCartsSum : 0;
         $qty       = $productCart->qty >= $stock_qty ? $stock_qty : $productCart->qty;

         $productCart->update(['stock_qty' => $stock_qty, 'qty' => $qty]);
     }

     /**
      *  get action
      *  Render thank you page
      */
     public function thanks() {
        if(Auth::guard('persons')->guest()) {
            $user = FrontUserUnlogged::where('user_id', @$_COOKIE['user_id'])->first();
            $order = CRMOrders::where('guest_user_id', $user->id)->orderBy('id', 'desc')->first();
            $user_id = 0;
        }else{
            $user = FrontUser::find(Auth::guard('persons')->id());
            $order = CRMOrders::where('user_id', $user->id)->orderBy('id', 'desc')->first();
            $user_id = $user->id;
        }

        $promocode = Promocode::where('user_id', $user_id)
               ->whereRaw('to_use < times')
               ->where('valid_to', '>', date('Y-m-d'))
               ->orderBy('id', 'desc')->first();

        if(count($promocode) > 0) {
            $products = Product::where('created_at', '>=', date('Y-m-d', strtotime('-15 days')))
                       ->orderBy('created_at', 'desc')
                       ->limit(5)
                       ->get();

            return view('front.pages.thanks', compact('user', 'promocode', 'order'));
        }else{
            return redirect()->route('404')->send();
        }
     }

     // post: validate subproducts stocks
     public function validateSubproductStock($subproductCart)
     {
         $productStock = $subproductCart->qty;

         $prodCartsSum = Cart::where('user_id', $subproductCart->user_id)
                             ->where('id', '!=', $subproductCart->id)
                             ->where('subproduct_id', $subproductCart->subproduct_id)
                             ->get()->sum('qty');

         $subprodStock = SubProduct::find($subproductCart->subproduct_id)->stoc;
         $stock_qty = ($subprodStock - $prodCartsSum) > 0 ? $subprodStock - $prodCartsSum : 0;
         $qty = $subproductCart->qty >= $stock_qty ? $stock_qty : $subproductCart->qty;

         $subproductCart->update(['stock_qty' => $stock_qty, 'qty' => $qty]);
     }


     /*****************************************
      *
      *  Frisbo conections
      *
      ****************************************/
     public function sentOrderToFrisbo($order)
     {
         $this->frisboLogin();

         $orderProducts = [];

         if ($order->orderSubproducts()->count() > 0) {
             foreach ($order->orderSubproducts as $key => $subproductItem) {
                 $orderProducts[] = [
                     "sku" => $subproductItem->code,
                     "name" => $subproductItem->subproduct->product->translation->name,
                     "price" => $subproductItem->subproduct->product->mainPrice->price,
                     "quantity" => $subproductItem->qty,
                     "vat" => 19,
                     "discount" => $subproductItem->subproduct->product->discount
                 ];
             }
         }

         if ($order->orderSets()->count() > 0) {
             foreach ($order->orderSets as $key => $setItem) {
                 if ($setItem->set->subproducts()->count() > 0) {
                     foreach ($setItem->set->subproducts as $key => $subproductSet) {
                         $orderProducts[] = [
                             "sku" => $subproductSet->code,
                             "name" => $subproductSet->subproduct->product->translation->name,
                             "price" => $subproductSet->subproduct->product->mainPrice->price,
                             "quantity" => 1,
                             "vat" => 19,
                             "discount" => $subproductSet->subproduct->product->discount
                         ];
                     }
                 }
             }
         }

         $client = new Client();
         $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6IlFqRTROa00zTnprNFJVSTROalU0UVVSRFFUUkNOak13UVRBd1JrVXhRamxDT1RRNU1rWTJNdyJ9.eyJpc3MiOiJodHRwczovL2ZyaXNiby1yby5ldS5hdXRoMC5jb20vIiwic3ViIjoiYXV0aDB8Mjc2IiwiYXVkIjpbImh0dHBzOi8vYXBpLmZyaXNiby5ybyIsImh0dHBzOi8vZnJpc2JvLXJvLmV1LmF1dGgwLmNvbS91c2VyaW5mbyJdLCJpYXQiOjE1NzQwNjc5NzYsImV4cCI6MTU3NDE1NDM3NiwiYXpwIjoiMkNoeUgyeXNRYThOdHdEV0tFem1oekpfa2p5cHlkMk0iLCJzY29wZSI6Im9wZW5pZCBwcm9maWxlIGVtYWlsIiwiZ3R5IjoicGFzc3dvcmQifQ.gFxzikSw_FArUwhVhQsMDFoIF685IroIAZC5D8Yz92pcvF1CpLnI8_idONBqaV24Mstxj5qtsyJEzs0hbK9QCNsgQtPD7DSTdsVy_VwN2CWKlvyPZr7xs2nBDo4yTuhmYMxUaoY_waeVYQ-at04Cr8EjrXq8scypyIgwfw5NEV9Z1nyNlNdl2-EOIfMRvXt4iasriB0gCyiHo5uCO_pBp5noHFBDo1tV6A6EDV6ljHn8MTGFoywJKT1Y1oBLk6ZiUDqgOpnJ4l5RyYdGCbAso2DXe4b7lA64mkXPmFeoP7TUZ1nn0Z2nR7mULdRoTxNkKtLNJgxJaq269A9k8tnyVQ";

         $url = "https://api.frisbo1.ro/v1/organizations/183/orders";

         $request = $client->post($url,[
             'headers' => [
                     'Authorization' =>  "Bearer {$this->token}",
                     'Content-Type' => 'application/json'
             ],
            'json' => [
                     "order_reference"     => $order->order_hash.uniqid(),
                     "organization_id"     => 183,
                     "channel_id"          => 315,
                     "warehouse_id"        => 282,
                     "status"              => "new",
                     "reason_status"       => null,
                     "ordered_date"        => null,
                     "delivery_date"       => null,
                     "returned_date"       => null,
                     "canceled_date"       => null,
                     "notes"               => "",
                     "shipped_with"        => "Unknown",
                     "shipped_date"        => null,
                     "preferred_delivery_time" => null,
                     "shipping_customer"   => [
                       "email"         => $order->details->email,
                       "first_name"    => $order->details->contact_name,
                       "last_name"     => $order->details->contact_name,
                       "phone"         => $order->details->phone
                     ],
                     "shipping_address" => [
                       "street"    => $order->details->address,
                       "city"      => $order->details->city,
                       "county"    => $order->details->region,
                       "country"   => $order->details->country,
                       "zip"       => $order->details->zip
                    ],
                     "billing_customer"   => [
                       "email"     => $order->details->email,
                       "first_name" => $order->details->contact_name,
                       "last_name" => $order->details->contact_name,
                       "phone"     => $order->details->phone,
                       "trade_register_registration_number" =>"2063080",
                       "vat_registration_number" =>"J27/1037/1991"
                   ],
                     "billing_address" => [
                       "street"    => $order->details->address,
                       "city"      => $order->details->city,
                       "county"    => $order->details->region,
                       "country"   => $order->details->country,
                       "zip"       => $order->details->zip
                   ],
                     "discount" => "0",
                     "transport_tax" => "0",
                     "cash_on_delivery" => 1,
                     "products" => $orderProducts
                 ]
            ]);

        $this->synchronizeStocks();
     }

     public function frisboLogin()
     {
         $loginUrl = "https://api.frisbo1.ro/v1/auth/login";

         $client = new Client();

         $request = $client->post($loginUrl, [
             'form_params' => [
                     'email' =>  "itmalles@gmail.com",
                     'password' =>  "ItMallFrisbo2019",
                 ]
             ]);

         $response = json_decode($request->getBody()->getContents());

         $this->token = $response->access_token;
     }

     public function synchronizeStocks()
     {
         $client = new Client();

         $getProductsUrl = "https://api.frisbo1.ro/v1/organizations/183/products";

         $request = $client->get($getProductsUrl, [
             'headers' => [
                     'Authorization' =>  "Bearer {$this->token}"
                 ]
             ]);

         $response = json_decode($request->getBody()->getContents());

         $i = 0;
         if ($response->data) {
             foreach ($response->data as $key => $responseProduct) {
                 if (count($responseProduct->storage) > 0) {
                     Subproduct::where('code', $responseProduct->sku)->update(['stoc' => $responseProduct->storage[0]->available_stock]);
                     $i++;
                 }else{
                     Subproduct::where('code', $responseProduct->sku)->update(['stoc' => 0]);
                 }
             }
         }
     }
}
