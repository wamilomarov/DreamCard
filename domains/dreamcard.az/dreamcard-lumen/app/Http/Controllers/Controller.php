<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
    function resize($imagepath, $new_height, $new_width){
        $src = imagecreatefromjpeg($imagepath);
        list($width, $height) = getimagesize($imagepath);
        $tmp = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($tmp, $src, 0,0,0,0,$new_width, $new_height, $width,$height);
        imagejpeg($tmp, "uploads/done.jpg", 100);
        imagedestroy($src);
        imagedestroy($tmp);
    }

    public function sendPushNotification($instanceIds,$message,$deep_link='',$silent=0,$title='', $group = ''){

        $push_api_url="https://onesignal.com/api/v1/notifications";
        $push_api_id="530f6a6b-69e7-4220-b161-d032a15a4906";
        $push_api_auth="Basic MWQzNjQ1YjMtZTcxZC00NTQ4LTgwOTAtNWEzOGMzYjJhM2Ix";


        if(is_array($instanceIds) && sizeof($instanceIds)>0)
        {
            if($silent==0){
                $fields = [
                    'app_id' => $push_api_id,
                    'include_player_ids' => $instanceIds,
                    'data' => array("deep_link"=>$deep_link),
                    'contents' => array("en"=>$message),
                    'headings'=>array('en'=>$title),
                    'android_group' => $group,
                    'android_group_message' => array("en" => "$[notif_count] yeni xəbəriniz var.")
                ];
            }
            else
            {
                $fields = [
                    'app_id' => $push_api_id,
                    'include_player_ids' => $instanceIds,
                    'content_available'=>true
                ];
            }
            $fields = json_encode($fields);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $push_api_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                'Authorization: '.$push_api_auth));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = curl_exec($ch);
            curl_close($ch);

            return $response;

        }
        else
        {
            return false;
        }

    }
}
