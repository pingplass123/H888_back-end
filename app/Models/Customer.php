<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'idCustomer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idUser',
        'name',
        'created_by'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::Class);
    }

    public function room()
    {
        return $this->hasOne(ChatRoom::Class);
    }
}
