<?php

namespace App\Http\Controllers;

use App\Setting;
use App\SMSLog;
use App\SMSPorts;
use App\User;
use App\UserQR;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mockery\Exception;
use Validator;
use JWTAuth;
use App\UserRole;
use Monolog\Logger;

/**
 * Class MainController
 * @package App\Http\Controllers
 */
class MainController extends Controller
{
    public static $SETTING_SMS_SERVER= "http://api.negarit.net/api/api_request";
    public static $SETTING_NEGARIT_API_KEY= "HTvA2us8zawfPZgEFflT9jbUDcIfz1j0";
    public static $SETTING_CAMPAIGN_ID= "55";
    public static $SETTING_PORT_ID= "1";
    public static $SETTING_CONTACT_GROUP_ID= "64";

    public static $SMS_SERVER_ACTION_SEND_MESSAGE = "sent_message";
    public static $SMS_SERVER_ACTION_GET_CAMPAIGNS = "campaigns";

    /**
     * MainController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $token
     * @return User
     */
    public function getUserFromToken($token){
        $user = JWTAuth::toUser($token);
        if($user instanceof User){
            return $user;
        }else{
            return response()->json(['error' => "Whoops invalid user token" ],500);
        }
    }

    /**
     * @param $user
     * @return null
     */
    public function getTokenFromUser($user) {
        if($user instanceof User){
            return JWTAuth::fromUser($user);
        }else{
            return null;
        }
    }

    public function addSMSLog($message, $phone ){
        $smsLog = new SMSLog();
        $smsLog->phone = $phone;
        $smsLog->message = $message;
        $state = $smsLog->save();
    }

    public function sendMessage($message, $phone){
        $api_key  = MainController::$SETTING_NEGARIT_API_KEY;
        $campaign_id = MainController::$SETTING_CAMPAIGN_ID;

        $header = array("API_KEY" => $api_key,
            "campaign_id" => $campaign_id,
            "message" => $message,
            "sent_to" => $phone);

        $url        =  MainController::$SETTING_SMS_SERVER . "/sent_message";
        $response = HTTPRequester::HTTPPost($url, $header, $api_key);
        $this->addSMSLog($message, $phone);

        try{
            $response_data = json_decode($response);
            if(isset($response_data->status)){

            }
        } catch (\Exception $exception){

        }
    }

    public function addToGroup($name, $phone, $email){
        $logger = new Logger("MessageActionTaskCtrl");

        $api_key  = MainController::$SETTING_NEGARIT_API_KEY;
        $group_id = MainController::$SETTING_CONTACT_GROUP_ID;
        $header = array("API_KEY" => $api_key,
            "group_id" => $group_id,
            "full_name" => $name,
            "phone" => $phone,
            "email" => $email);

        $url        =  MainController::$SETTING_SMS_SERVER . "/grouped_contact";
        $response = HTTPRequester::HTTPPost($url, $header, $api_key);

        try{
//            $response_data = json_decode($response);
//            $logger->log(Logger::INFO, "Executing Action TASKs", $response_data);
        } catch (\Exception $exception){

        }
    }

}
