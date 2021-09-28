<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\FeedBack;
use App\Models\PromocodeType;
use App\Models\Promocode;
use App\Models\Product;
use Session;


class FeedBackController extends Controller
{
    public function index()
    {
        return view('front.pages.thanks');
    }

    public function productPreOrder(Request $request)
    {
        $data['name'] = $request->get('name');
        $data['email'] = $request->get('email');
        $data['phone'] = $request->get('phone');
        $data['productId'] = $request->get('product_id');
        $data['contact_message'] = $request->get('message');

        $to =  trans('vars.Contacts.email');
        $to = 'info@seminee.md';

        $productDetails = '';
        $product = Product::find($request->get('product_id'));
        if (!is_null($product)) {
            $productDetails .= '<p>'. $product->translation->name  .'</p>';
            $productDetails .= '<p>'. $product->code  .'</p>';
        }

        $feedback = new FeedBack();
        $feedback->form = 'Order';
        $feedback->first_name = request('name');
        $feedback->email = request('email');
        $feedback->phone = request('phone');
        $feedback->subject = 'Contact Form.';
        $feedback->message = request('message');
        $feedback->additional_1 = $productDetails;
        $feedback->status = 'new';

        $feedback->save();

        $data['productDetails'] = $productDetails;

        Mail::send('mails.order.admin', $data, function($message) use ($to){
            $message->to($to, 'Order seminee.md')->from('seninee.md@gmail.com')->subject('Order seminee.md');
        });

        $email = $request->get('email');

        Mail::send('mails.order.user', $data, function($message) use ($email){
            $message->to($email, 'Order succesefully placed on seninee.md')->from('seninee.md@gmail.com')->subject('Order succesefully placed on seninee.md');
        });

        Session::flash('message', 'Va multumim, in scrut timp managerii nostri va vor contacta.');
        return redirect()->back();
    }

    public function contactFeedBack(Request $request)
    {
        $data['name'] = $request->get('name');
        $data['email'] = $request->get('email');
        $data['phone'] = $request->get('phone');
        $data['contact_message'] = $request->get('message');

        $to =  trans('vars.Contacts.email');
        $to = 'info@seminee.md';

        $feedback = new FeedBack();
        $feedback->form = 'Contact';
        $feedback->first_name = request('name');
        $feedback->email = request('email');
        $feedback->phone = request('phone');
        $feedback->subject = 'Contact Form.';
        $feedback->message = request('message');
        $feedback->status = 'new';

        $feedback->save();

        Mail::send('mails.contactForm.admin', $data, function($message) use ($to){
            $message->to($to, 'Contactează-ne')->from('seninee.md@gmail.com')->subject('Contact Us.');
        });

        $email = $request->get('email');

        Mail::send('mails.contactForm.user', $data, function($message) use ($email){
            $message->to($email, 'You left a message on the online shop seninee.md')->from('seninee.md@gmail.com')->subject('You left a message on the online shop seninee.md');
        });

        Session::flash('message', 'Va multumim, in scrut timp managerii nostri va vor contacta.');
        return redirect()->back();
    }

}
