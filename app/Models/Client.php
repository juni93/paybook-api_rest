<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'address', 'cap', 'city', 'country', 'email',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
