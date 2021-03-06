<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

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

    public function accounting()
    {
        return $this->hasMany(Accounting::class);
    }
}
