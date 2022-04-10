<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
