<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\appModel;
use App\Models\launchAppModel;
use App\Models\User;
use App\Notifications\passwordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class userController extends Controller
{
 static function randomString() {
        $length = rand(15, 20);
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    static function randomPassword() {
        $length = rand(8, 12);
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomPassword = '';

        for ($i = 0; $i < $length; $i++) {
            $randomPassword .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomPassword;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json(['message' => 'Les informations d\'identification sont incorrectes', 'code'=>401 ]);
        }

        $user = Auth::user();
         if($user->status == 1)
        {
             $token = $user->createToken('RH_OpenApps')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => $user,
            'code' =>200
        ]);
        }else{
            return response()->json([
                'message'=> 'Ce compte n\est plus actif. Veuillez rencontrer l\'administrateur',
                'code'=> 10
                ]);
        }

    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Déconnexion réussie'], 200);
    }

    public function launchApp($appID)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $userId = $user->userID;
            $record = appModel::find($appID);

            if ($record) {
                $launch = launchAppModel::create([
                    'userID' => $userId,
                    'appID' => $record->appID,
                    'dateTime'=>date('d/m/Y H:i')
                ]);
            } else {
                return response()->json(['message' => 'Application introuvable'], 404);
            }
        } else {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }
    }

    public function getAppsOfUser($id)
    {
        if (Auth::check()) {
     $user = User::find($id);
     if (!$user) {
        return response()->json(['message'=> 'Utilisateur non trouver'],404);
     }
     $apps = $user ->apps;

     return response()->json([
         'apps'=>$apps,
         'code'=>200,
         'message'=>'Applications recuperees',
     ]);
    } else {
        return response()->json(['message'=> 'Utilisateur non authentifier'], 404);
    }
}

public function resetPwd($userEmail)
{
    $user = User::where('email',$userEmail)->first();
    if($user)
    {
        $password = $this->randomPassword();
        $user->update(['password' =>Hash::make($password) ]);
        $user->notify(new passwordReset($password));

    return response()->json([
        'code'=>200
        ],200);
    }else{
        return response()->json(['message'=> 'Utilisateur introuvable'], 404);
    }
}

public function updatePassword(Request $request)
{
   $validation =  $request->validate([
        'password'=>'required|confirmed',
    ]);
   if(Auth::check() && $validation){
    $user = Auth::user();
    $user->update(['password'=>Hash::make($request->password) ]);
    return response()->json([
        'code'=> 200,
        'message'=> 'Votre mot de passe a ete modifier'
        ]);
   }
   return response()->json([
    'message'=> 'Votre requette ne peut aboutir',
    'code'=> 404
    ], 404);
}
}
