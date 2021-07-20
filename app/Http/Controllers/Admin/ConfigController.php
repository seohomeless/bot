<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Получение данных с таблиц
     *
     */
    public function getGoogleSheets()
    {
        $id = '1-1_yVsyktQXCOXk2rSEDWUe2tUeVXcg5G2EgeE-RjY0';
        $gid = '0';

        $csv = file_get_contents('https://docs.google.com/spreadsheets/d/' . $id . '/export?format=csv&gid=' . $gid);
        $csv = explode("\r\n", $csv);
        $products = array_map('str_getcsv', $csv);

        Products::truncate();

        foreach ($products as $k => $product) {
            if ($k > 0) {
                $saveProduct = new Products();
                $saveProduct->name = $product[1];
                $saveProduct->qty = $product[2];
                $saveProduct->desc = $product[3];
                $saveProduct->price = $product[4];
                $saveProduct->img = $product[5];
                $saveProduct->availability = $product[6];
                $saveProduct->save();
            }
        }

        return redirect()->back()->with('success', 'Update');

    }
}
