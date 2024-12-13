<?php

namespace App\Http\Controllers;

use App\Exports\TemplateModel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Notifications\userNotification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\roleModel;
use App\Models\appModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class adminController extends userController
{
   public function addUser(Request $request)
   {
    do {
        $userID = $this->randomString();
        $find = User::where('userID',$userID)->exists();
    } while ($find);
    $request->validate([
        'name'=>'required|string|max:255',
        'role'=>'string|max:20',
        'email' => 'required|string|email|max:255|unique:users',
    ]);
    $admin=false;
    $role = "";
    $is_admin = roleModel::where('roleTitle',$request->role)->first();
    if ($is_admin && $is_admin->roleTitle == 'Admin')
    {
        $admin = true;
        $role = $is_admin->roleID;
    }
    $password=$this->randomPassword();
    $role = $is_admin->roleID;
    $user = User::create([
        'userID'=>$userID,
        'roleID' => $role,
        'email' => $request->email,
        'name' => $request->name,
        'is_admin'=>$admin,
        'password' => Hash::make($password),
    ]);
    if ($user) {
        $user->notify(new userNotification($password));
    return response()->json([
        'message'=>'Inscription reussite',
        'code'=>200
    ]);
   }
   }
   public function removeUser($id)
   {
    $user=User::find($id);
    if ($user) {
       $user->delete();
       return response()->json([
        'message'=>'Utilisateur supprimer',
        'code'=>200
       ]);
    }else {
        return response()->json([
            'message'=>'Utilisateur introuvable',
            'code'=>404
        ]);
    }
   }

   public function editUser($id, Request $request)
   {
    $user = User::find( $id );
 if ($request->has('password')) {
    $request->validate([
        'password'=> 'string|confirmed',
    ]);
    $user -> password = Hash::make($request->password);
 }
    $admin=false;
    $is_admin = roleModel::where('roleTitle',$request->role)->first();
    if ($is_admin && $is_admin->roleTitle == 'Admin')
    {
        $admin = true;
    }
    $role = $is_admin->roleID;
    $user->name = $request->name;
    $user->roleID = $role;
    $user->email = $request->email;
    $user->is_admin = $admin;
    $user->update();

    return response()->json([
        'message'=>'Vos modifications ont ete pris en compte',
        'code'=>200
    ]);
   }

   public function addApp(Request $request)
   {
    do {
        $appID = $this->randomString();
        $find = appModel::find($appID);
    } while ($find);

    if($request->file('appImage'))
        {
            $file = $request->file('appImage');
            $image = $file->storeAs('public/appsCover',$request->appName);
            $imageUrl = Storage::url($image);
        }else{
            $imageUrl = '/storage/appsCover/defaultImg';
        }
    $app = appModel::where('appName',$request->appName)->orWhere('appLink',$request->appLink)->first();
    if ($app){
    return response()->json([
        'message'=> 'Une application ayant ce lien ou ce nom existe deja',
        'code'=> 400
        ]);
    }else{
    appModel::create([
        'appName' => $request->appName,
        'appLink' => $request->appLink,
        'appImage' => $imageUrl,
        'appID' => $appID,
    ]);
      return response()->json([
        'message'=>'Application creer avec succes',
        'code'=>200
      ]);
    }
   }

   public function editApp($id, Request $request)
   {
    $app = appModel:: find($id);
    if (!$app) {
       return response()->json([
        'message'=> 'Application introuvable',
        'code'=>404
       ]);
    }
    if ($request->has('appImage')) {
        if ($app->appImage && $app->appImage != '/storage/appsCover/defaultImg') {
            Storage::delete(str_replace('/storage', 'public', $app->appImage));
         }
        $file = $request->file('appImage');
        $image = $file->storeAs('public/appsCover',$request->appName);
        $imageUrl = Storage::url($image);
        $app->appImage = $imageUrl;
    }
    $app->appName = $request->appName;
    $app->appLink = $request->appLink;
    $app->update();

    return response()->json([
        'message'=>'Vos modifications ont ete pris en compte',
        'code'=>200
    ]);
   }

   public function removeApp($id)
   {
    $app = appModel::find($id);

    if ($app) {
        if ($app->appImage && $app->appImage != '/storage/appsCover/defaultImg') {
            Storage::delete(str_replace('/storage', 'public', $app->appImage));
        }
        $app->delete();
        return response()->json([
            'message'=>'Vous avez supprimer une application',
            'code'=>'200'
        ]);
    } else {
        return response()->json([
            'message'=>'Vous essayer de supprimer une application qui n\'existe pas',
            'code'=>404
        ]);
    }
   }

   public function allowApp(Request $request,$appID)
   {
    $app = appModel::find($appID);
    $user = User::find($appID);

            if ($app)
            {
               $users = $request->selectedUsers;
               $app->users()->attach($users);
               return response()->json([
                'message'=> 'Utilisateurs affectes avec succes',
                'code'=> 200,
               ]);
            }else if($user){
                $apps = $request->selectedApps;

                $user->apps()->attach($apps);
                return response()->json([
                 'message'=> 'Applications affectes avec succes',
                 'code'=> 200,
                ]);
            } else{
                return response()->json([
                    'message'=> 'ni l\'application, ni l\'utilisateur n\'a ete trouve',
                    'code'=> 404
                    ]);
                }
   }

   public function getNonUsers($appID)
   {
    $app = appModel::find($appID);
    $user = User::find($appID);
    if ($app){
    $user= User::whereDoesntHave('apps',
    function ($query) use ($appID){
        $query->where('app_models.appID',$appID);
    }
    )->get();

    return response()->json([
        'message'=> 'Utilisateur recuperer',
        'code'=> 200,
        'users'=> $user
    ]);
   }else if( $user ){
    $app= appModel::whereDoesntHave('users',
    function ($query) use ($appID){
        $query->where('users.userID',$appID);
    }
    )->get();

    return response()->json([
        'message'=> 'Utilisateur recuperer',
        'code'=> 200,
        'apps'=> $app
    ]);
   }else{
    return response()->json([
        'message'=> 'Aucune application ou utilisateur trouve !',
        'code'=> 404
    ]);
   }
}
   public function preventApp($appID,$userID)
   {
   $user = User::find($userID);

    if ($user) {
        $user ->apps()->detach($appID);
        return response()->json([
            'message'=>'Acces revoquer avec succes',
            'code'=>200
        ]);
    }else {
        return response()->json([
            'message'=>'Cet utilisateur n\'a pas acces a cette application',
            'code'=>401
        ]);
    }
   }

   public function addRole(Request $request)
   {
        do {
            $roleID = $this->randomString();
            $find = roleModel::find($roleID);
        } while ($find);

        $roleTitle = roleModel::where('roleTitle',$request->roleTitle)->count();
        if( $roleTitle > 0) {
            return response()->json([
                'message'=> 'Ce role existe deja',
                'code'=> 200,
            ]);
        }else if($request->has('roleTitle')) {
        roleModel::create([
            'roleID' => $roleID,
            'roleTitle' => $request->roleTitle
        ]);
        return response()->json([
            'message'=> 'Role crree avec succes',
            'code'=> 200
        ]);
        }else{
            return response()->json([
                'message'=> 'Entree invalide',
                'code'=> 0,
                ]);
        }

   }

   public function editRole($id,Request $request)
   {
    $role = roleModel::find($id);
    if ($role && $role->roleTitle != 'Admin' && $request->has('roleTitle')) {
        $find = roleModel::where('roleTitle',$request->roleTitle)->count();
        if($find > 0) {
            return response()->json([
                'message'=> 'Ce role existe deja',
                'code'=> 200,
                ]);
        }
        $role->roleTitle = $request->roleTitle;
        $role->save();
        return response()->json([
            'message'=>'Vos modifications ont ete pris en compte',
            'code'=> 200
        ]);
    }else{
        return response()->json([
            'message'=>'Impossible de modifier ce role ou entree invalide',
            'code'=> 401
        ]);
    }
   }


   public function deleteRole($id)
   {
    $role = roleModel::find($id);

    if ($role && $role->roleTitle != 'Admin') {
        $role->delete();

        return response()->json([
            'message'=>'Suppression effectuer avec succes',
            'code'=> 200
        ]);
    }else {
        return response()->json([
            'message'=>'Impossible de supprimer',
            'code'=> 401
        ]);
    }
   }

   public function getUsers()
   {
    $users = User::with('role','apps')->get();
    return response()->json([
        'users'=>$users,
        'message'=>'Utilisateurs recuperes',
        'code'=>200
    ]);
   }

   public function getUserByID($id)
   {
    $user = User::find($id);

    return response()->json([
        'users'=>$user,
        'message'=>'Utilisateur recupere',
        'code'=>200
    ]);
   }

   public function getApps()
   {
    $apps = appModel::with('users')->get();

    return response()->json([
        'apps'=>$apps,
        'message'=>'Applications recuperes',
        'code'=>200
    ]);
   }

   public function getAppByID($id)
   {
    $app = appModel::find($id);

    return response()->json([
        'app'=>$app,
        'message'=>'Application recupere',
        'code'=>200
    ]);
   }

   public function getRoles()
   {
    $roles=roleModel::with('users')->get();
    return response()->json([
        'roles'=>$roles,
        'message'=>'Roles recuperes',
        'code'=>200
    ]);
   }

   public function getRoleById($id)
   {
    $role = roleModel::find($id);

    return response()->json([
        'role'=>$role,
        'message'=>'Role recuperes',
        'code'=>200
    ]);
   }
   public function getUsersOfApp($appID)
   {
     $app = appModel:: with('users')->where('appID',$appID)->first();
     if (!$app) {
        return response()->json(['users'=>'Aucune application trouver']);
     }
     $users = $app -> users;
     return response()->json([
         'users'=>$users,
         'code'=>200
     ]);
   }

   public function getUsersByRole($id)
   {
    $users = User::where('roleID',$id);
    return response()->json([
        'users'=>$users,
        'code'=>200
    ]);
   }

   public function enableOrDesableUser($id)
   {
    $user = User::find($id);
    if (!$user) {
        return response()->json([
                'message'=> 'Utilisateur introuvable',
                'code'=> 404
                ]);
    }else if($user->is_admin == 1)
    {
        return response()->json([
            'message'=> 'Impossible de deactiver un Admin',
            'code'=> 200,
            ]);
    }else{
     $user->status= $user->status==1?0:1;
     $user->update();
    }
   }
   public function getIdOfRole($roleTitle)
   {
    $role = roleModel::where('roleTitle',$roleTitle);
    return response()->json([
        'id'=>$role->roleID,
        'code'=>200
    ]);
   }

   public function export()
   {
    return Excel::download(new UsersExport,'userRhopenLabs.xlsx');
   }

   public function exportTemplate()
   {
    return Excel::download(new TemplateModel,'users_document_model.xlsx');
   }

   public function import(Request $request)
   {
    Excel::import(new UsersImport,$request->file('file'));
    return response()->json([
        "message"=>"Utilisateurs importee avec succes",
        "code"=> 200
        ]);
   }
}


