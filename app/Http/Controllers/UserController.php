<?php

namespace App\Http\Controllers;

use Log;
use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function __construct()
    {

        $this->middleware('can:users.index')->only('indexC');
        $this->middleware('can:users.info')->only('getAuthenticatedUser');
        $this->middleware('can:users.show')->only('showC');
        $this->middleware('can:users.update-auth')->only('updateAuth');
        $this->middleware('can:users.delete')->only('destroy');
        $this->middleware('can:users.store')->only('storeC');
    }

    public function authenticate(Request $request)
    {
      $credentials = $request->only('email', 'password');
      try {
          if (! $token = JWTAuth::attempt($credentials)) {
              return response()->json(['error' => 'invalid_credentials'], 400);
          }
      } catch (JWTException $e) {
          return response()->json(['error' => 'could_not_create_token'], 500);
      }
      return response()->json(compact('token'));
    }

    public function getAuthenticatedUser()
    {
        try {
          if (!$user = JWTAuth::parseToken()->authenticate()) {
                  return response()->json(['user_not_found'], 404);
          }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
                return response()->json(['token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }


    public function register(Request $request)
    {

        Log::info($request);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
                return response()->json($validator->errors()->toJson(),400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }

    public function indexC(){
        $users = User::all();
        return response()->json(["users" => $users], 200);
    }

    public function showC($id)
    {
        $user = User::find($id);

        if ($user != '') {
            return response()->json(["user" => $user], 200);
        } else {
            return response()->json(["messages" => "User not found"], 500);
        }
    }

    public function updateAuth(Request $request)
    {
        $rules = array(
            'name' => 'nullable|unique:users,name,' . Auth::user()->id,
            'email' => 'nullable|email',
            'password'=>'nullable|password'
        );

        $messages = array();

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {

            $messages = $validator->messages();

            return response()->json(["messages" => $messages], 500);
        }

        $user = User::find(Auth::user()->id);
        if($request->name!=''){
            $user->name = $request->name;
        }
        if($request->email!=''){
            $user->email = $request->email;
        }
        if($request->password!=''){
            $user->password = Hash::make($request->password);
        }

        $user->update();

        return response()->json(["user" => $user, "message" => "User has been updated successfully"], 200);

    }

    public function destroy($id)
    {
        $user = User::find($id);

        if ($user != '') {

        User::destroy($id);
        return response()->json(["message" => "User has been deleted successfully"], 200);

        } else {
            return response()->json(["messages" => "User not found"], 500);
        }
    }

    public function storeC(Request $request){
        $rules = array(
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role'=>'required'
        );

        $messages = array();

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {

            $messages = $validator->messages();

            return response()->json(["messages" => $messages], 500);
        }
        $user= new User();
        $user->name = $request->name;
        $user->email=$request->email;
        $user->password=Hash::make($request->password);
        $user->save();

        $user->assignRole($request->role);

        return response()->json(["user" => $user, "message" => "Post has been created successfully"], 200);
    }

}
