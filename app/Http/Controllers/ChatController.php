<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\ChatRoom;

use Illuminate\Support\Arr;

use App\Http\Controllers\BaseController as BaseController;

class ChatController extends BaseController
{
    public function fetchAdminRooms(Request $request)
    {
        $roomsList = ChatRoom::where('idAdmin', '=', $request->idAdmin)->get();
        $customersList = [];

        foreach($roomsList as $room){
            $customer = Customer::where('idCustomer', '=', $room->idCustomer)->first();
            $customersList = Arr::add($customersList, $room->idRoom, $customer->name);
        }

        $success['room-list'] = $roomsList;
        $success['customer-list'] = $customersList;

        return $this->sendResponse($success, 'All room records for this admin account.');
    }
}
