<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\CheckRead;

use Illuminate\Support\Arr;
use Validator;
use Carbon\Carbon;

use App\Http\Controllers\BaseController as BaseController;

class ChatController extends BaseController
{
    public function fetchAdminRooms(Request $request)
    {
        $admin = Admin::where('idAdmin', '=', $request->idAdmin)->first();
        $roomsList = ChatRoom::where('idAdmin', '=', $request->idAdmin)->get();
        $response = [];

        foreach($roomsList as $room){
            $checkpoint = CheckRead::where('idRoom', '=', $room->idRoom)
                                    ->where('idUser', '=', $admin->idUser)->first();
                                    
            $unread_messages = $room->unreadMessages($admin->idUser, $checkpoint->updated_at);
        
            $customer = Customer::where('idCustomer', '=', $room->idCustomer)->first();
            $data = [
                "room" => $room,
                "customer_name" => $customer->name,
                "unread" => $unread_messages
            ];

            $last_message = $room->lastMessage();
            if($last_message != null){
                $last_message_time = $last_message->created_at;
                $date = Carbon::createFromFormat('Y-m-d H:i:s', $last_message_time, 'UTC');
                $date->setTimezone('Asia/Bangkok');

                $data["display_time"] = $date;
            }
            else {
                $data["display_time"] = 0;
            }

            array_push($response, $data);
        }

        $success['room_list'] = $response;

        return $this->sendResponse($success, 'All room records for this admin account.');
    }

    public function storeMessage(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'idRoom' => 'bail|required|exists:chat_rooms,idRoom',
            'from' => 'bail|required|exists:users,idUser',
            'message' => 'bail|required|string',
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
        $chat->image = $request->message;
        $chat->save();

        $success['idMessage'] = $chat->idMessage;

        return $this->sendResponse($success, 'Stored message successfully.');
    }

    public function fetchChatHistory(Request $request)
    {
        // Mark as read
        $checkpoint_record = CheckRead::where('idRoom', '=', $request->idRoom)
                                    ->where('idUser', '=', $request->idUser)->first();

        $checkpoint_record->latest_read = Carbon::now()->toDateTimeString();
        $checkpoint_record->save();
        
        $chatHistory = ChatMessage::where('idRoom', '=', $request->idRoom)->get();

        foreach($chatHistory as $chat){
            $user = User::where('idUser', '=', $chat->sentFrom)->first();

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

            $chat->user_role = $role;
            $chat->user_record = $record;

            $utc_time = $chat->created_at;
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $utc_time, 'UTC');
            $date->setTimezone('Asia/Bangkok');

            $chat->show_time = $date;
        }

        $success['chat_history'] = $chatHistory;

        return $this->sendResponse($success, 'Chat history of this room.');
    }

    public function deleteMessage(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'idMessage' => 'bail|required|exists:chat_messages,idMessage',
        ],[
            'idMessage.required'      => 'ID message must not be empty',
            'idMessage.exists'    => 'ID message is invalid, not found record',
        ]);

        if ($validatedData->fails()) {
            $failedRules = $validatedData->failed(); 
            return $this->sendError('Invalid Data.', ['error'=>$failedRules]);
        }

        $chat = ChatMessage::where('idMessage', '=', $request->idMessage)->first();
        $chat->delete();

        $success['idMessage'] = $request->idMessage;
        $success['action'] = "delete";

        return $this->sendResponse($success, 'Deleted message successfully.');
    }
}
