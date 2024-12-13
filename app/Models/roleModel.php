<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class roleModel extends Model
{
    use HasFactory; 
    protected $primaryKey = 'roleID';
    protected $fillable = ['roleTitle','roleID']; 
    public function users() { 
        return $this->hasMany(User::class,'roleID'); 
    }
    protected $casts = [
        'roleID' => 'string',
    ];
    use HasFactory;
}
