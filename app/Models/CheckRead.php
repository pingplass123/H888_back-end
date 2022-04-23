<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckRead extends Model
{
    use HasFactory;

    protected $table = 'check_read_messages';

    protected $primaryKey = 'idCheck';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idUser',
        'idRoom',
        'latest_read',
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
