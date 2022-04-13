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

    public function editCustomerAccount(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'displayName' => 'bail|required|string|max:50',
            'created_by' => 'bail|required|exists:admins,idAdmin'
        ],[
            'displayName.required'      => 'Display name must not be empty',
            'displayName.max'    => 'Display name length must not exceed 50 characters',
            'created_by.required'      => 'Creator of this account must be assigned',
            'created_by.exists'      => 'This creator is invalid, not found record'
        ]);

        if ($validatedData->fails()) {
            $failedRules = $validatedData->failed(); 
            return $this->sendError('Invalid Data.', ['error'=>$failedRules]);
        }

        $customer = Customer::where('idCustomer', '=', $request->idCustomer)->first();
        $customer->name = $request->displayName;
        $customer->save();

        $success['displayName'] = $customer->name;
        return $this->sendResponse($success, 'Customer account updated successfully.');
    }

    public function deleteCustomerAccount(Request $request)
    {
        $customer = Customer::where('idCustomer', '=', $request->idCustomer)->first();
        $user = User::where('idUser', '=', $customer->idUser)->first();

        $customer->delete();
        $user->delete();

        $success['message'] = 'This account has been deleted!';
        return $this->sendResponse($success, 'Deleted successfully.');
    }
}
