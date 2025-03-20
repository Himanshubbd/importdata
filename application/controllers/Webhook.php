<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webhook extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }
    public function index(){
        $response = [
            'status' => 'error',
            'message' => 'NULL'
        ]; 
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response['message'] = 'Invalid request.Method not allowed.';
            return $this->json_response($response);
        }
        $content_type = $this->input->get_request_header('Content-Type', true);
        if ($content_type !== 'application/json') {
            $response['message'] = 'Invalid Content-Type. Only application/json is allowed.';
            return $this->json_response($response);
        }
        $input = file_get_contents("php://input");
        var_dump($input);
        $jsonDecodedData =  json_decode($input,true);
        $topic = $jsonDecodedData['topic']??NULL;
        if($topic){
            if($topic == 'user_check_in'){
                echo '<pre>';
                print_r($jsonDecodedData);
                echo '</pre>';
            }else{
                $response['message'] = 'Invalid webhook request.';
                return $this->json_response($response); 
            }
        }else {
            $response['message'] = 'Invalid webhook action.';
            return $this->json_response($response);
        }
    }
    private function json_response($data, $status_code = 200) {
        $this->output
            ->set_content_type('application/json') // Explicitly set JSON header
            ->set_status_header($status_code)
            ->set_output(json_encode($data));
    }
}