<?php

namespace App\Http\Controllers;

use App\Attendant;
use App\AttendantExports;
use Validator;
use Illuminate\Http\Request;
use Mockery\Exception;
use Excel;

/**
 * Class AttendantController
 * @package App\Http\Controllers
 */
class AttendantController extends MainController
{

    /**
     * AttendantController constructor.
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

            $users = Attendant::orderBy('id', 'desc')
                ->paginate($per_page);

            return response()->json(['success' => true, 'result' => $users], 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => 'Invalid credential used!!'], 401);
        }
    }

    public function search(){
        try {
            $pages = request()->only('len');
            $per_page = $pages != null ? (int)$pages['len'] : 10;

            if ($per_page > 50) {
                return response()->json(['success' => false, 'error' => 'Maximum page length is 50.'], 401);
            }


            $credentials = request()->only(
                'searchText'
            );

            $searchText = $credentials['searchText'];

            $attendants = Attendant::where('full_name', 'LIKE', "%$searchText%")
                        ->orWhere('email', 'LIKE', "%$searchText%")
                        ->orWhere('phone', 'LIKE', "%$searchText%")
                        ->orWhere('city', 'LIKE', "%$searchText%")
                ->orderBy('id', 'desc')
                ->paginate($per_page);

            return response()->json(['success' => true, 'result' => $attendants], 200);


        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => 'Invalid credential used!!'], 401);
        }

    }

    /**
     * Register new Attendant
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $rules = [
            'full_name' => 'max:255 |required',
            'phone' => 'max:13 |required |min:9',
            'sex' => 'max:1',
        ];

        $credentials = $request->only(
            'full_name', 'phone', 'email', 'age', 'sex', 'region', 'city', 'profession', 'interest', 'conference_year', 'conference_place'
        );

        $validator = Validator::make($credentials, $rules);

        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['status' => false, 'error' => $error], 400);
        }

        $phone = $credentials['phone'];
        $phone = substr($phone,strlen($phone)-9,9);
        $phone = "251" . $phone;

        $new_attendant = new Attendant();
        $new_attendant->full_name =  isset($credentials['full_name']) ? $credentials['full_name'] : "";
        $new_attendant->phone =  $phone;
        $new_attendant->email =  isset($credentials['email']) ? $credentials['email'] : null;
        $new_attendant->age =  isset($credentials['age']) ? $credentials['age'] : null;
        $new_attendant->sex =  isset($credentials['sex']) ? $credentials['sex'] : null;
        $new_attendant->region =  isset($credentials['region']) ? $credentials['region'] : null;
        $new_attendant->city =  isset($credentials['city']) ? $credentials['city'] : null;
        $new_attendant->profession =  isset($credentials['profession']) ? $credentials['profession'] : null;
        $new_attendant->interest =  isset($credentials['interest']) ? $credentials['interest'] : null;
        $new_attendant->conference_year =  isset($credentials['conference_year']) ? $credentials['conference_year'] : null;
        $new_attendant->conference_place =  isset($credentials['conference_place']) ? $credentials['conference_place'] : null;

        $state = $new_attendant->save();
        if($state){
            $message = "Dear " . $new_attendant->full_name .
                ", Thank you for registering for the 2018 Indigitous #Hack which takes place in Addis Ababa, Gurid Shola, Holy City center from October 19 - 21, 2018."
                . " \n \n See Holy City Center ( https://maps.app.goo.gl/i/dUpo8 ) in Google Maps.";
            $this->addToGroup($new_attendant->full_name, $new_attendant->phone, $new_attendant->email);
            $this->sendMessage($message, $new_attendant->phone);

            return response()->json(["success" => true, "result"=>$new_attendant]);
        }
        else{
            return response()->json(["success" => false, "error"=>"Something went wrong. Please try again"]);
        }
    }



    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = Attendant::where('id', '=', $id)->first();
        if(!$user){
            return response()->json(["success" => false, "error"=>"User not found"], 400);
        }
        return response()->json(["success" => true, "result"=>$user]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $attendant = Attendant::find($id);
        if(! $attendant){
            return response()->json(['status'=>false,'error'=> "Attendant not found"], 400);
        }

        $attendant->delete();
        return response()->json(["success" => true, "result"=>$attendant]);
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $id){

        $user = Attendant::find($id);

        if(!$user){
            return response()->json(['status'=>false,'error'=> "User not found"]);
        }

        $rules = [
            'full_name' => 'max:255 |required',
            'phone' => 'max:13 |required |min:9',
            'sex' => 'max:1',
        ];

        $credentials = $request->only(
            'full_name', 'phone', 'email', 'age', 'sex', 'region', 'city', 'profession', 'academic_status'
        );

        $validator = Validator::make($credentials, $rules);

        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['status' => false, 'error' => $error], 400);
        }

        $user->full_name =  isset($credentials['full_name']) ? $credentials['full_name'] : "";
        $user->phone =  isset($credentials['phone']) ? $credentials['phone'] : "";
        $user->email =  isset($credentials['email']) ? $credentials['email'] : "";
        $user->age =  isset($credentials['age']) ? $credentials['age'] : "";
        $user->sex =  isset($credentials['sex']) ? $credentials['sex'] : "";
        $user->region =  isset($credentials['region']) ? $credentials['region'] : "";
        $user->city =  isset($credentials['city']) ? $credentials['city'] : "";
        $user->profession =  isset($credentials['profession']) ? $credentials['profession'] : "";
        $user->academic_status =  isset($credentials['academic_status']) ? $credentials['academic_status'] : "";

        $user->update();
        return response()->json(["success" => true, "result"=>$user]);
    }



    public function exportAll()
    {
        return Excel::download(new AttendantExports(), 'attendant.xlsx');
    }



}
