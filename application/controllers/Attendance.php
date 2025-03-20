<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends CI_Controller{
    const API_URL = 'https://webapi.studyin-uk.com/api/WebSiteForms/';
    //const API_URL = 'https://xeroapi.studyin-uk.com/api/WebSiteForms/';
    public function __construct(){
        parent::__construct();
    }
    public function index() {
        // Retrieve GET parameters
        $eventId = isset($_GET['eventId']) ? $_GET['eventId'] : null;
        $email = isset($_GET['email']) ? $_GET['email'] : null;
        if($eventId!=null &&  $email!= null){
            // Convert comma-separated emails to an array
            $emailArray = $email ? explode(',', $email) : [];
        
            // Get authentication token
            $authCode = $this->authatoken();
            // var_dump($authCode);
        
            foreach ($emailArray as $emailItem) {
                $newformdata = [
                    'data' => [
                        'AccelEventID' => $eventId,
                        'Email'        => trim($emailItem), // Ensure no spaces
                    ]
                ];
                
                // Encode each JSON object
                $data_string = json_encode($newformdata);
                echo '<pre>';
                print_r($data_string);
                echo '</pre>';
                $checkAtt = $this->markAtt($authCode,$data_string);
                echo '<pre>';
                print_r($checkAtt);
                echo '</pre>';
            }
        }else{
            echo 'Please provide the eventId and email.';
        }
    }
    
    private function authatoken(){
        $api_url = SELF::API_URL;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url."Certify");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: 23-93-63-D7-E2-15-5C-12-C1-FD-FC-A9-B9-75-3A-B6-59-97-5B-97-1E-49-A8-A9-BC-65-FF-59-6B-45-F6-5E'
        ));
        $html = json_decode(curl_exec($curl));
        curl_close($curl);
        return $html->Data;
    }
    private function markAtt($acode,$data_string){
        $api_url = SELF::API_URL;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url."MarkStudentAttendanceByAccelEvent");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization:'. $acode,
            'Content-Type: application/json')
        );
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }
}