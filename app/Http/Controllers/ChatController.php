<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\ChatRoom;
use App\Models\ChatMessage;

use Illuminate\Support\Arr;
use Validator;

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

        $success['room_list'] = $roomsList;
        $success['customer_list'] = $customersList;

        return $this->sendResponse($success, 'All room records for this admin account.');
    }

    public function storeMessage(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'idRoom' => 'bail|required|exists:chat_rooms,idRoom',
            'from' => 'bail|required|exists:users,idUser',
            'message' => 'bail|required|mimes:jpg,jpeg',
        ],[
            'idRoom.required'      => 'ID Room must not be empty',
            'idRoom.exists'    => 'ID Room is invalid, not found record',
            'from.required'      => 'ID User must not be empty',
            'from.exists'    => 'ID User is invalid, not found record',
            'message.required'      => 'Message must not be empty',
        ]);

        if ($validatedData->fails()) {
            $failedRules = $validatedData->failed(); 
            return $this->sendError('Invalid Data.', ['error'=>$failedRules]);
        }

        $chat = new ChatMessage();
        $chat->idRoom = $request->idRoom;
        $chat->sentFrom = $request->from;

        // Prepare image and upload to folder
        $fileNameExt = $request->file('message')->getClientOriginalName();
        $fileName = pathinfo($fileNameExt, PATHINFO_FILENAME);
        $fileExt = $request->file('message')->getClientOriginalExtension();
        $fileNameToStore = $fileName.'_'.time().'.'.$fileExt;
        $pathToStore = $request->file('message')->move('uploaded_images',$fileNameToStore);

        $chat->message = file_get_contents($pathToStore); // save image
        $chat->save();

        $sucess['idMessage'] = $chat->idMessage;

        return $this->sendResponse($success, 'Stored message successfully.');
    }

    public function fetchChatHistory(Request $request)
    {
        $chatHistory = ChatMessage::where('idRoom', '=', $request->idRoom)->get();
        $usersList = [];

        foreach($chatHistory as $chat){
            $user = User::where('idUser', '=', $chat->sentFrom)->first();

            $role = "";
            $record = "";
            if($user->isAdmin() != [])
            {
                $role = "admin";
                $record = Admin::where('idUser', '=', $chat->sentFrom)->first();
            }
            elseif($user->isCustomer() != [])
            {
                $role = "customer";
                $record = Customer::where('idUser', '=', $chat->sentFrom)->first();
            }

            $arr = [
                "from" => $record,
                "role" => $role
            ];

            $usersList = Arr::add($usersList, $chat->idMessage, $arr);
        }

        $success['chat_history'] = $chatHistory;
        $success['user_list'] = $usersList;

        return $this->sendResponse($success, 'Chat history of this room.');
    }
}
