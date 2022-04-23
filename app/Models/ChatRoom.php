<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Admin;
use App\Models\ChatMessage;

class ChatRoom extends Model
{
    use HasFactory;

    protected $table = 'chat_rooms';

    protected $primaryKey = 'idRoom';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idAdmin',
        'idCustomer'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::Class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::Class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::Class);
    }

    public function unreadMessages($user, $latest_read)
    {
        $unread = ChatMessage::where('idRoom', '=', $this->idRoom)
                                ->where('sentFrom', '<>', $user)
                                ->where('created_at', '>', $latest_read)->get();
        return $unread->count();
    }

    public function lastMessage()
    {
        $lastMessages = ChatMessage::where('idRoom', '=', $this->idRoom)->get();

        if($lastMessages->isEmpty())
        {
            return null;
        }

        $last_index = $lastMessages->count() - 1;
        return $lastMessages[$last_index];
    }
}
