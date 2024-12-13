<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class appModel extends Model
{
    protected $primaryKey = 'appID';
    protected $casts = [ 
        'appID' =>'string',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'allow_app_models', 'appID', 'userID');
    }

    protected $fillable = [ 
        'appName',
        'appID',
        'appImage',
        'appLink',
    ];

    use HasFactory;
}
