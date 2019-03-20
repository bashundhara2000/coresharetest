<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDriverAuth extends Model 
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_drive_auth';

    protected $fillable = ['user_id'];

}
