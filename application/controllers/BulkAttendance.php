<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BulkAttendance extends CI_Controller{
    const API_URL = 'https://webapi.studyin-uk.com/api/WebSiteForms/';
    //const API_URL = 'https://xeroapi.studyin-uk.com/api/WebSiteForms/';
    public function __construct(){
        parent::__construct();
    }
    public function index() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            ini_set('memory_limit', '256M');
            ini_set('max_execution_time', 0);
            
            // Start output buffering
            if (ob_get_level() == 0) ob_start();
    
            // Retrieve Event ID from POST request
            $eventId = $this->input->post('eventId');
    
            // Check if file is uploaded
            if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
                $fileTmpPath = $_FILES['csv_file']['tmp_name'];
    
                // Read CSV file and extract emails
                $emailArray = $this->readCSV($fileTmpPath);
    
                if (empty($emailArray)) {
                    echo "❌ No valid emails found in the CSV.<br>";
                    ob_flush(); flush();
                    return;
                }
    
                // Get authentication token
                $authCode = $this->authatoken();
    
                // Counters for results
                $marked = [];
                $already = [];
                $notReg = [];
    
                // Process each email from CSV
                foreach ($emailArray as $emailItem) {
                    // Get authentication token
                    $authCode = $this->authatoken();
                    $emailItem = trim($emailItem); // Ensure no spaces
                    $newformdata = [
                        'data' => [
                            'AccelEventID' => $eventId,
                            'Email'        => $emailItem,
                        ]
                    ];
    
                    // Encode JSON object
                    $data_string = json_encode($newformdata);
                    echo "🔄 Processing: <b>$emailItem</b><br>";
                    ob_flush(); flush();
    
                    // Call API
                    $checkAtt = $this->markAtt($authCode, $data_string);
    
                    // Print response immediately
                    //echo "📩 API Response: <pre>" . ($checkAtt). "</pre><br>";
    
                    // Categorize result
                    if (isset($checkAtt['Data']['Result'])) {
                        if ($checkAtt['Data']['Result'] == "1") {
                            $marked[] = $emailItem;
                            echo "✅ Marked Present: <b>$emailItem</b><br>";
                        } elseif ($checkAtt['Data']['Result'] == "2") {
                            $already[] = $emailItem;
                            echo "⚠️ Already Marked: <b>$emailItem</b><br>";
                        } else {
                            $notReg[] = $emailItem;
                            echo "❌ Not Registered: <b>$emailItem</b><br>";
                        }
                    } elseif (isset($checkAtt['Message']) && $checkAtt['Message'] == 'Invalid student.') {
                        $notReg[] = $emailItem;
                        echo "❌ Invalid Student: <b>$emailItem</b><br>";
                    }
    
                    // Flush output to browser after each iteration
                    ob_flush(); flush();
                    sleep(1); // Optional delay for better visibility
                }
    
                // Final Summary
                echo "<hr>";
                echo "<h3>📊 Final Summary</h3>";
                echo "✅ Total Marked Present: " . count($marked) . "<br>";
                echo "⚠️ Total Already Marked: " . count($already) . "<br>";
                echo "❌ Total Not Registered: " . count($notReg) . "<br>";
                echo '<pre>';
                print_r($notReg);
                echo '</pre>';
    
                // End output buffering and flush everything
                ob_end_flush();
            } else {
                echo "❌ Please upload a valid CSV file.";
                ob_flush(); flush();
            }
        }
    
        // Display the HTML form inside the CI function
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Upload CSV File</title>
        </head>
        <body>
            <h2>Upload CSV File for Attendance</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <label for="eventId">Event ID:</label>
                <input type="text" name="eventId" id="eventId" required>
                <br><br>
    
                <label for="csv_file">Upload CSV File:</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                <br><br>
    
                <button type="submit">Submit</button>
            </form>
        </body>
        </html>';
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
        return json_decode($result,true);
    }
    private function readCSV($filePath) {
        $emails = [];
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Assuming email is in the first column (index 0)
                if (!empty($data[0]) && filter_var($data[0], FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $data[0];
                }
            }
            fclose($handle);
        }
        return $emails;
    }
}