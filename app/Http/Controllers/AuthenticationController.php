<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController as BaseController;
use Validator;

class AuthenticationController extends BaseController
{
    public function login(Request $request)
    {
        if(auth()->attempt(array('username' => $request->username, 'password' => $request->password)))
        {
            $user = Auth::user(); 
            $role = "";

            if($user->isOwner() != [])
            {
                $role = "owner";
            }
            elseif($user->isAdmin() != [])
            {
                $role = "admin";
            }
            elseif($user->isCustomer() != [])
            {
                $role = "customer";
            }

            $success['token'] =  $user->createToken('H888')->plainTextToken; 
            $success['name'] =  $user->username;
            $success['role'] =  $role;

            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorized.', ['error'=>'Unauthorized']);
        } 
    }

    public function createAdminAccount(Request $request)
    {
        $validatedData = $request->validate([
            'displayName' => 'bail|required|string|max:50',
            'username' => 'bail|required|string',
            'password' => 'bail|required|string',
        ],[
            'displayName.required'      => 'Display name must not be empty',
            'displayName.max'    => 'Display name length must not exceed 50 characters',
            'username.required'    => 'Username must not be empty',
            'password.required'      => 'Password must not be empty',
        ]);

        if ($validatedData->fails()) {
            $failedRules = $validatedData->failed();
            return $this->sendError('Invalid Data.', ['error'=>$failedRules]);
        }
        
        $user = new User();
        $user->name = $request->username;
        $user->password = Hash::make($request->password);
        $user->save();

        $user->assignToAdmin($request->displayName);

        return $this->sendResponse($success, 'Admin account created successfully.');
    }

    public function createCustomerAccount(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'displayName' => 'bail|required|string|max:50',
            'username' => 'bail|required|string',
            'password' => 'bail|required|string',
            'created_by' => 'bail|required|exists:admins,idAdmin',
        ],[
            'displayName.required'      => 'Display name must not be empty',
            'displayName.max'    => 'Display name length must not exceed 50 characters',
            'username.required'    => 'Username must not be empty',
            'password.required'      => 'Password must not be empty',
            'created_by.required'      => 'Creator of this account must be assigned',
            'created_by.exists'      => 'This creator is invalid, not found record',
        ]);

        if ($validatedData->fails()) {
            $failedRules = $validatedData->failed();
            return $this->sendError('Invalid Data.', ['error'=>$failedRules]);
        }
        
        $user = new User();
        $user->name = $request->username;
        $user->password = Hash::make($request->password);
        $user->save();

        $user->assignToCustomer($request->displayName, $request->created_by);

        return $this->sendResponse($success, 'Customer account created successfully.');
    }
}
