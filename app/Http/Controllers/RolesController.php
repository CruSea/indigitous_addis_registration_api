<?php

namespace App\Http\Controllers;

use App\UserRole;
use Illuminate\Http\Request;
use Mockery\Exception;

class RolesController extends MainController
{
    //


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        try {
            $roles = UserRole::all();
            return response()->json(['success' => true, 'result' => $roles], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => 'Invalid credential used!!'], 401);
        }
    }

}
