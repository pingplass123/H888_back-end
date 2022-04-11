<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $primaryKey = 'idAdmin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idUser',
        'name',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::Class);
    }

    public function rooms()
    {
        return $this->hasMany(ChatRoom::Class);
    }
}
