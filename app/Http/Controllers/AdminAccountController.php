<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;

class AdminAccountController extends Controller
{
    public function getAdminList()
    {
        $success['admins_list'] = Admin::all();

        return $this->sendResponse($success, 'Get all admin account records.');
    }
    
}
