<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Http\Controllers\BaseController as BaseController;

class CustomerAccountController extends BaseController
{
    public function getCustomerList(Request $request)
    {
        $success['customers_list'] = Customer::where('created_by', '=', $request->idAdmin)->get();

        return $this->sendResponse($success, 'Get all customer account records.');
    }
}
