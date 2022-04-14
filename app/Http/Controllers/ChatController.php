<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\ChatRoom;

use App\Http\Controllers\BaseController as BaseController;

class ChatController extends BaseController
{
    public function fetchAdminRooms(Request $request)
    {
        $success['room-list'] = ChatRoom::where('idAdmin', '=', $request->idAdmin)->get();

        return $this->sendResponse($success, 'All room records for this admin account.');
    }
}
