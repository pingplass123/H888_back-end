<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $primaryKey = 'idMessage';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idRoom',
        'sentFrom',
        'image'
    ];

    public function user()
    {
        return $this->belongsTo(User::Class);
    }

    public function room()
    {
        return $this->belongsTo(ChatRoom::Class);
    }
}
