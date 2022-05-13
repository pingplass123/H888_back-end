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

                $original_date = $date->toDateTimeString();
                $date = explode(' ', $original_date)[0];
                $time = explode(' ', $original_date)[1];

                $day = explode('-', $date)[2];
                $month = explode('-', $date)[1];
                $year = explode('-', $date)[0];

                $hour = explode(':', $time)[0];
                $minute = explode(':', $time)[1];
                
                $data["display_time"] = $hour.".".$minute." ".$day."/".$month."/".$year;
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
        dd($_POST);

        /* $idRoom = $_POST['idRoom'];
        $from = $_POST['from'];

        $response = File::ensureDirectoryExists('./photos/');
        $path_move_to = './photos/' . $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], $path_move_to);

        $chat = new ChatMessage();
        $chat->idRoom = $idRoom;
        $chat->sentFrom = $from;
        $chat->image = $path_move_to;
        $chat->save();

        $success['message'] = $chat; */

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

            $original_date = $date->toDateTimeString();
            $date = explode(' ', $original_date)[0];
            $time = explode(' ', $original_date)[1];

            $day = explode('-', $date)[2];
            $month = explode('-', $date)[1];
            $year = explode('-', $date)[0];

            $hour = explode(':', $time)[0];
            $minute = explode(':', $time)[1];

            $chat->show_time = $hour.".".$minute." ".$day."/".$month."/".$year;
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
