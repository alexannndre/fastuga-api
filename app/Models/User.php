<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use DB;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $dates = ['updated_at', 'created_at', 'deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'photo_url',
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

    protected $with = ['customer'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bloked' => 'boolean'
    ];

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // TODO: certo?
    public function orderItems()
    {
        return $this->hasManyThrough(Order::class, Product::class, 'preparation_by', 'id', 'id', 'order_id');
    }

    public function perms()
    {
        return
            DB::table('permissions')
                ->where('type', '=', $this->type)
                ->get()[0]->permissions;
    }

    public function scopes()
    {
        $perms = $this->perms();
        $scopes = [];
        for ($i = 0; $i <= DB::table('scopes')->get()->count(); $i++) {
            if ($perms & (1 << $i)) {
                $scopes[] = DB::table('scopes')->where('id', '=', $i)->get()[0]->scope_name;
            }
        }
        return $scopes;
    }
}
