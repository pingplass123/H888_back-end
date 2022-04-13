<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerAccountController extends Controller
{
    public function getCustomerList(Request $request)
    {
        $success['customers_list'] = Customer::where('created_by', '=', $request->idAdmin)->get();

        return $this->sendResponse($success, 'Get all customer account records.');
    }
}
