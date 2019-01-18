<?php
namespace ERP;

use Illuminate\Foundation\Auth\User as Authenticatable;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
