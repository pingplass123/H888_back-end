<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Http\Controllers\BaseController as BaseController;

class AdminAccountController extends BaseController
{
    public function getAdminList()
    {
        $success['admins_list'] = Admin::all();

        return $this->sendResponse($success, 'Get all admin account records.');
    }
}
