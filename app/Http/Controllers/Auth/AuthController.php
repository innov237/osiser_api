<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\typeUser;
use App\etatCommande;
use App\etatLivraison;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            // 'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json(array('message' => 'Email ou mot de passe incorrect ', 'success' => false));
        $user = $request->user();
        $type = typeUser::where('id',$user->type_id)->select('libelleType')->first();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        // if ($request->remember_me)
        //     $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'user' => $user,
            'type' => $type->libelleType,
            'success' => true
        ]);
    }
    public function register(Request $request)
    {
        $request->validate([
            'pays_id' => 'Integer|nullable',
            'type' => 'required',
            'nom' => 'required|string',
            'telephone' => 'required',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
            'avatar' => ''
        ]);
        $type = typeUser::where('libelleType',$request->type)->select('id')->first();

        $user = new User;
        $user->type_id = $type->id;
        $user->pays_id = $request->pays_id;
        $user->nom = $request->nom;
        $user->telephone = $request->telephone;
        $user->email = $request->email;
        $user->avatar = $request->avatar;
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(array('message' => 'utilisateur crée avec succes','success'=>true));
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(array('message' => 'deconnecté','success'=>true));
    }

     public function filtrelisteclient(Request $request){
        $key = $request->input('key');
        $user = User::join('type_users','type_users.id','=','users.type_id')
        ->where([
            ['email','like','%'.$key.'%'],
            ['type_users.libelleType','client']
        ])->orderBy('users.id','des')->get();
        return response()->json($user);

    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function menbre(){
        $menbre = User::join('type_users','type_users.id','=','users.type_id')
                        ->where('type_users.libelleType','<>','client')
                        ->select('users.*')
                        ->get();
        return response()->json($menbre);
    }
    public function livreur()
    {
        $menbre = User::join('type_users', 'type_users.id', '=', 'users.type_id')
            ->where('type_users.libelleType', 'livreur')
            ->select('users.*')
            ->get();
        return response()->json($menbre);
    }
    public function client(){
        $client = User::join('type_users','type_users.id','=','users.type_id')
                        ->where('type_users.libelleType','client')
                        ->select('users.*')
                        ->get();
        return response()->json($client);
    }

     public function telechargerImage(Request $request)
    {
        if ($request->hasFile('image')) {

            $files = $request->file('image');
            $request->image->move('storage/avatar', $files->getClientOriginalName());
            return response()->json(array('message' => 'image upload success'));
        }
        return response()->json(array('message' => 'image upload error'));
    }

    public function modifPassword(Request $request)
    {
        $request->validate([
            'a_password' => 'required',
            'password' => 'required|required_with:password_confirmation|same:c_password',
            'c_password' => 'required',
            'id' => 'required'
        ]);

        $user = User::find($request->id);

        if (Hash::check($request->a_password, $user->password)) {
            $user->password = $request->password;
            $user->save();
            return response()->json(array('success' => true));
        }
        return response()->json(array('success' => false));
    }

    public function creerTypeUser(){
      $type = ['administrateur','client','livreur'];

      $count = typeUser::count();
      if($count == 0){
        for ($i=0; $i < 3 ; $i++) {
          $type_users = new typeUser;
          $type_users->libelleType = $type[$i];
          $type_users->save();
        }
        return response(['succes'=>true]);
      }
        return response(['succes'=>false]);
    }

    public function verifiparametre(){
      // $countuser = User::count
      $countEtatcom = etatCommande::count();
      $countEtatLiv = etatLivraison::count();
      if($countEtatLiv != 0 && $countEtatcom != 0){
      return response(['succes'=>true]);
      }
    return response(['succes'=>false]);
    }
}
