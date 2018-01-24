<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 23-Jan-18
 * Time: 21:52
 */

namespace App;


class Helper
{
    public static function sendPushNotification($toids,$message,$deep_link='',$silent=0,$title='', $group = ''){

        // bunlar config faylina dasinacag sonra
        $push_messages[1]=array("en"=>$message);
        $push_api_url="https://onesignal.com/api/v1/notifications";
        $push_api_id="530f6a6b-69e7-4220-b161-d032a15a4906";
        $push_api_auth="Basic MWQzNjQ1YjMtZTcxZC00NTQ4LTgwOTAtNWEzOGMzYjJhM2Ix";


        if(is_array($toids) && sizeof($toids)>0)
        {
            $playerIds = getPlayerIds($toids);
            // print_r($playerIds);exit;
            if($silent==0){
                $fields = array(
                    'app_id' => $push_api_id,
                    'include_player_ids' => $playerIds,
                    'data' => array("deep_link"=>$deep_link),
                    'contents' => array("en"=>$message),
                    'headings'=>array('en'=>$title),
                    'android_group' => $group,
                    'android_group_message' => array("en" => "You have $[notif_count] new notifications")
                );

                // silent olmayan ve tek adama gnderilen butun pushlarda notify ve badge gondermek.
                if (count($toids)==1) {
                    $data = getUserNotifyCounts($toids[0]);
                    $fields['data']['notify'] = array();
                    $fields['data']['notify'] = $data['data'];
//                $fields['data']['notify']['inbox']= $data['data']['inbox'];
//                $fields['data']['notify']['admin_inbox']= $data['data']['admin_inbox'];
//                $fields['data']['notify']['branch_inbox']= $data['data']['branch_inbox'];
//                $fields['data']['notify']['product_update']= $data['data']['product_update'];
//                $fields['data']['notify']['new_follower']= $data['data']['new_follower'];
                    $fields['ios_badgeType']= 'SetTo';
                    $fields['ios_badgeCount']= $data['data']['sum'];
                }

            }
            else
            {
                $fields = array(
                    'app_id' => $push_api_id,
                    'include_player_ids' => $playerIds,
                    'content_available'=>true
                );
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

            $retval["status"]=1;
            $retval["data"]["push"]=sizeof($playerIds);
            $retval["data"]["push_message"]=$message;

            // print_r($response);
            //$retval["data"]["push_player_id"]=$playerid;
            //sendBadgeCount($toids);

        }




        $response_data=array("api_curl_response"=>$response,"function_response"=>$retval);
        foreach ($toids as $userid) {
            $sql="INSERT INTO push_message_log (userid, send_date, request_data, response_data) VALUES 
	('$userid',NOW(),'".$db->real_escape_string(serialize($fields))."','".$db->real_escape_string(serialize($response_data))."')";
            $db->query($sql);
        }

        return $retval;

    }
}