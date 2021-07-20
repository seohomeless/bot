<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Products;
use http\Env\Response;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\PseudoTypes\True_;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $newOrder = new Orders();
        $newOrder->phone = $request->phone;
        $newOrder->name = $request->name;

        // update qty
        foreach ($request->cart as $product) {
            $product = json_decode($product,1);
            $updateProduct = Products::find($product['id']);
            $updateProduct->qty = $updateProduct->qty - 1;
            $updateProduct->save();

            $productId[] = $product['id'];
        }

        $newOrder->products = json_encode($productId);
        $newOrder->save();

        return \response(true, 200);
    }


}
