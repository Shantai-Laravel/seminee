<?php

namespace Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SubProduct;
use App\Models\FeedBack;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class FrisboController extends Controller
{
    public $client;

    public $token;

    public function __construct()
    {
        $this->client = new Client();

        $this->login();

        // $this->token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6IlFqRTROa00zTnprNFJVSTROalU0UVVSRFFUUkNOak13UVRBd1JrVXhRamxDT1RRNU1rWTJNdyJ9.eyJpc3MiOiJodHRwczovL2ZyaXNiby1yby5ldS5hdXRoMC5jb20vIiwic3ViIjoiYXV0aDB8Mjc2IiwiYXVkIjpbImh0dHBzOi8vYXBpLmZyaXNiby5ybyIsImh0dHBzOi8vZnJpc2JvLXJvLmV1LmF1dGgwLmNvbS91c2VyaW5mbyJdLCJpYXQiOjE1NzQwNjc5NzYsImV4cCI6MTU3NDE1NDM3NiwiYXpwIjoiMkNoeUgyeXNRYThOdHdEV0tFem1oekpfa2p5cHlkMk0iLCJzY29wZSI6Im9wZW5pZCBwcm9maWxlIGVtYWlsIiwiZ3R5IjoicGFzc3dvcmQifQ.gFxzikSw_FArUwhVhQsMDFoIF685IroIAZC5D8Yz92pcvF1CpLnI8_idONBqaV24Mstxj5qtsyJEzs0hbK9QCNsgQtPD7DSTdsVy_VwN2CWKlvyPZr7xs2nBDo4yTuhmYMxUaoY_waeVYQ-at04Cr8EjrXq8scypyIgwfw5NEV9Z1nyNlNdl2-EOIfMRvXt4iasriB0gCyiHo5uCO_pBp5noHFBDo1tV6A6EDV6ljHn8MTGFoywJKT1Y1oBLk6ZiUDqgOpnJ4l5RyYdGCbAso2DXe4b7lA64mkXPmFeoP7TUZ1nn0Z2nR7mULdRoTxNkKtLNJgxJaq269A9k8tnyVQ";
    }

    public function login()
    {
        $loginUrl = "https://api.frisbo1.ro/v1/auth/login";

        $request = $this->client->post($loginUrl, [
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
        $getProductsUrl = "https://api.frisbo1.ro/v1/organizations/183/products";

        $request = $this->client->get($getProductsUrl, [
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
            FeedBack::create([
                'form' => 'update stocks',
                'first_name' => 'cron schedule '. date('Y-m-d h:i:s'),
                'status' => 'new',
            ]);
        }

        echo "Was updated <b>". $i ."</b> product stocks.";
    }

    public function addProduct($subproduct)
    {
        $postProductUrl = "https://api.frisbo1.ro/v1/organizations/183/products";

        $request = $this->client->post($postProductUrl, [
            'headers' => [
                    'Authorization' =>  "Bearer {$this->token}"
            ],
            'form_params' => [
                    "name" => $subproduct->product->translation->name.' '.$subproduct->parameterValue->translation->name,
                    "sku" => $subproduct->code,
                    "upc" => "1020304050607080",
                    "external_code" => "1020304050607080",
                    "ean" => "somestring",
                    "vat" => 0,
                    "dimensions" =>  [
                        "width" => 0,
                        "height" => 0,
                        "length" => 0,
                        "weight" => 0
                    ]
                ]
            ]);
    }
}
