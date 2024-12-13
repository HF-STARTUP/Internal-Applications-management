<?php

namespace App\Imports;

use App\Http\Controllers\adminController;
use App\Http\Controllers\userController;
use App\Models\roleModel;
use App\Models\User;
use App\Notifications\userNotification;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $existingUser = User::where("email", $row['email'])->first();
        if ($existingUser) 
        {
            return response()->json([
                "message"=>$row['email']."Cette adresse email est deja prise ! ",
                "users"=> $existingUser->name,
                "code"=>401,
                ],0);
        }
        $password = adminController::randomPassword();
        $role = roleModel::firstOrCreate(['roleTitle'=>$row['role']]);
        $user = new User([
            'userID'=>userController::randomString(),
            'name'=> $row['nom'],
            'email'=> $row['email'],    
            'password'=> bcrypt($password),
        ]
        );
        $user->role()->associate($role);
        $user->save();
        $user->notify(new userNotification($password));
        return $user;
    }
}
