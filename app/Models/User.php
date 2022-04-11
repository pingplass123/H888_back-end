<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'idUser';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isOwner()
    {
        return Owner::where('idUser', '=', $this->idUser)->first();
    }

    public function isAdmin()
    {
        return Admin::where('idUser', '=', $this->idUser)->first();
    }

    public function isCustomer()
    {
        return Customer::where('idUser', '=', $this->idUser)->first();
    }

    public function assignToAdmin(String $name)
    {
        $admin = new Admin();
        $admin->idUser = $this->idUser;
        $admin->name = $name;
        $admin->save();
    }

    public function assignToCustomer(String $name, int $idAdmin)
    {
        $customer = new Customer();
        $customer->idUser = $this->idUser;
        $customer->name = $name;
        $customer->created_by = $idAdmin;
        $customer->save();
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::Class);
    }
}
