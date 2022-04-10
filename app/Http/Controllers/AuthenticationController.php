<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController as BaseController;

class AuthenticationController extends Controller
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
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
}
