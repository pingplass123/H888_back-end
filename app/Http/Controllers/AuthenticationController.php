<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\ChatRoom;
use App\Models\CheckRead;

use Carbon\Carbon;
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
            $success['idUser'] =  $user->idUser;

            if($user->isAdmin() != []) // send id back as response
            {
                $success['id'] =  $user->isAdmin()->idAdmin;
            }
            elseif($user->isCustomer() != []) // send id back as response
            {
                $success['id'] =  $user->isCustomer()->idCustomer;
                $success['belongsTo'] =  $user->isCustomer()->created_by;

                $room = ChatRoom::where('idCustomer', '=', $success['id'])->first();
                $success['room'] =  $room;

                $admin = Admin::where('idAdmin', '=', $success['belongsTo'])->first();
                $success['nameAdmin'] = $admin->name;
            }

            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorized.', ['error'=>'Unauthorized']);
        } 
    }

    public function createAdminAccount(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
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
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->save();

        $user->assignToAdmin($request->displayName);

        $success['displayName'] = $request->displayName;

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
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->save();

        $response = $user->assignToCustomer($request->displayName, $request->created_by);

        $room = ChatRoom::where('idRoom', '=', $response["idRoom"])->first();

        // first check point for admin
        $admin = Admin::where('idAdmin', '=', $request->created_by)->first();
        
        $checkpoint_a = new CheckRead();
        $checkpoint_a->idUser = $admin->idUser;
        $checkpoint_a->idRoom = $response["idRoom"];
        $checkpoint_a->latest_read = Carbon::now()->toDateTimeString();
        $checkpoint_a->save();

        // first check point for customer
        $checkpoint_c = new CheckRead();
        $checkpoint_c->idUser = $user->idUser;
        $checkpoint_c->idRoom = $response["idRoom"];
        $checkpoint_c->latest_read = Carbon::now()->toDateTimeString();
        $checkpoint_c->save();

        $success['displayName'] = $request->displayName;
        $success['belongsTo'] = $request->created_by; // idAdmin
        $sucess['chat_id'] = $room;

        return $this->sendResponse($success, 'Customer account created successfully.');
    }
}
