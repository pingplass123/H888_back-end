<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;
use App\Http\Controllers\BaseController as BaseController;

use Validator;

class AdminAccountController extends BaseController
{
    public function getAdminList()
    {
        $success['admins_list'] = Admin::all();

        return $this->sendResponse($success, 'Get all admin account records.');
    }
    
    public function editAdminAccount(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'displayName' => 'bail|required|string|max:50'
        ],[
            'displayName.required'      => 'Display name must not be empty',
            'displayName.max'    => 'Display name length must not exceed 50 characters'
        ]);

        if ($validatedData->fails()) {
            $failedRules = $validatedData->failed(); 
            return $this->sendError('Invalid Data.', ['error'=>$failedRules]);
        }

        $admin = Admin::where('idAdmin', '=', $request->idAdmin)->first();
        $admin->name = $request->displayName;
        $admin->save();

        $success['displayName'] = $admin->name;
        return $this->sendResponse($success, 'Admin account updated successfully.');
    }

    public function deleteAdminAccount(Request $request)
    {
        $customersList = Customer::where('created_by', '=', $request->idAdmin)->get();

        $admin = Admin::where('idAdmin', '=', $request->idAdmin)->first();
        $user = User::where('idUser', '=', $admin->idUser)->first();

        foreach($customersList as $customer){
            $customer->delete();
        }

        $admin->delete();
        $user->delete();

        $success['message'] = 'This account has been deleted!';
        return $this->sendResponse($success, 'Deleted successfully.');
    }
}
