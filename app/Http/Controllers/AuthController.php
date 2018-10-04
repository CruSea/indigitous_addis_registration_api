<?php

namespace App\Http\Controllers;

use App\User;
use App\UserQR;
use Illuminate\Http\Request;
use Mockery\Exception;
use Validator;
use JWTAuth;
use App\UserRole;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends MainController
{

    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $pages = request()->only('len');
            $per_page = $pages != null ? (int)$pages['len'] : 10;

            if ($per_page > 50) {
                return response()->json(['success' => false, 'error' => 'Maximum page length is 50.'], 401);
            }

            $users = User::where('role_id', '!=', '4')->orderBy('id', 'desc')->with('qrImage')->with('roles')->paginate($per_page);
            return response()->json(['success' => true, 'result' => $users], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => 'Invalid credential used!!'], 401);
        }
    }


    /**
     * Register new user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $rules = [
            'full_name' => 'required|max:255',
            'phone' => 'max:255',
            'role_id' => 'required',
        ];

        $credentials = $request->only(
            'full_name', 'phone', 'password', 'email','role_id'
        );

        $validator = Validator::make($credentials, $rules);

        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['status' => false, 'error' => $error], 400);
        }

        try {
            $user_role = UserRole::where('id', '=', $credentials['role_id'])->first();
            $old_user_phone = User::where('phone', '=', $credentials['phone'])->first();

            if(isset($credentials['email'])){
                if (($credentials['email'] != null) && ($credentials['email'] != " ") && ($credentials['email'] != "")) {
                    $old_user_email = User::where('email', '=', $credentials['email'])->first();

                    if ($old_user_email instanceof User) {
                        return response()->json(['status' => false, 'error' => "Email is already taken"], 400);
                    }
                }
            }


            if (!$user_role instanceof UserRole) {
                return response()->json(['status' => false, 'error' => "Role not found"], 400);
            }
            if ($old_user_phone instanceof User) {
                return response()->json(['status' => false, 'error' => "Phone is already taken"], 400);
            }


            $new_user = new User();
            $new_user->full_name = isset($credentials['full_name']) ? $credentials['full_name']: "";
            $new_user->phone = isset($credentials['phone']) ? $credentials['phone']: null;
            $new_user->email = isset($credentials['email']) ? $credentials['email']: null;
            $new_user->password = isset($credentials['password']) ? bcrypt($credentials['password']):  bcrypt($credentials["123456"]);
            $new_user->role_id = $user_role->id;
            $new_user->status = isset($credentials['status']) ? $credentials['status']: 0;

            $state = $new_user->save();

            if ($state) {
                return response()->json([
                    'status' => true,
                    'user' => $new_user,
                    'user id' => $new_user->id,
                    'message' => 'User Created'
                ], 200);
            }

        } catch (Exception $exception) {
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 400);
        }
        return response()->json(['status' => false, 'error' => "Problem occurred. Please try again."], 400);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUser(){
        $credentials = request()->only(
            'phone'
        );

        $user = User::where('phone', '=', $credentials['phone'])->first();

        if($user instanceof User){
            return response()->json(['status' => true, 'data' =>$user], 400);
        } else{
            return response()->json(['status' => false, 'error' => "User Not found"], 400);
        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(){
        $rule = ['phone' => 'required|max:255', 'password' => 'required|min:4'];

        $credential = request()->only('phone','password');

       /* $validator = Validator::make($credential, $rule);

        if($validator->fails() ) {
            return response()->json(['error'=>'Invalid credential used!! '],401);
        }*/

        try{
            $token = JWTAuth::attempt($credential);
            if(!$token){
                return response()->json(['error'=>'Invalid credential used!!'],401);
            } else{
                $user = $this->getUserFromToken($token);
                if($user instanceof User){
                    return response()->json(['success'=>true,'token'=>$token, 'user'=>$user]);
                }else{
                    return response()->json(['success'=>false,'error'=>'User Not Found'],500);
                }
            }
        }catch (JWTException $exception){
            return response()->json(['error'=>$exception->getMessage()],500);
        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(){
        $token = JWTAuth::getToken();
        $user = $this->getUserFromToken($token);

        if($user instanceof User){
            return response()->json(['token'=>'', 'user'=>$user, 'status'=>$user->status]);
        }else{
            return response()->json(['error'=>'User not found!!!'],500);
        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(){
        $token = JWTAuth::getToken();
        $user = $this->getUserFromToken($token);
        if($user instanceof User){
            return response()->json(['status'=>$token,'user'=>$user],200);
        }else{
            return response()->json(['status'=>false,'error'=>'User notdd found!!!'],401);
        }
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::find($id);
        if(!$user){
            return response()->json(["success" => false, "error"=>"User not found"]);
        }
        return response()->json(["success" => true, "result"=>$user]);
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['status'=>false,'error'=> "User not found"]);
        }
        $user->delete();
        return response()->json(["success" => true, "result"=>$user]);
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $id){

        $user = User::find($id);
        if(!$user){
            return response()->json(['status'=>false,'error'=> "User not found"]);
        }

        $rules = [
            'full_name' => 'required|max:255',
            'phone' => 'required',
        ];

        $credentials = $request->only('full_name', 'email','phone','password', 'role_id', 'status');

        $validator = Validator::make($credentials, $rules);

        if($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json(['success'=> false, 'error'=> $error]);
        }

        try{

            $user->full_name = $credentials['full_name'];
            $user->phone = $credentials['phone'];
            $user->email = $credentials['email'];
            $user->role_id = $credentials['role_id'];
            $user->status = $credentials['status'];
            $user->update();
            return response()->json(["success" => true, "result"=>$user]);

        } catch (Exception $exception) {
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 400);
        }
    }


    /**
     * @return User|\Illuminate\Http\JsonResponse
     */
    public function getUser(){
        try{
            $token = JWTAuth::getToken();
            return $this->getUserFromToken($token);
        } catch (\Exception $exception){
            return response()->json(['error'=>$exception->getMessage()],500);
        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserToken(){

        try{
            return JWTAuth::getToken();
        }catch (\Exception $exception){
            return response()->json(['error'=>$exception->getMessage()],500);
        }
    }





}
