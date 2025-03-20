<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
ini_set('max_execution_time', '0');

defined('BASEPATH') OR exit('No direct script access allowed');

class UpdateCourse extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('import_model');
        $this->load->helper('form');
        $this->load->library('upload');
    }

    public function index() {
        $this->load->view('upload_course_data_view');
    }

    public function updateCourseDetails() {
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xls|xlsx';
        $config['encrypt_name'] = FALSE;
        $config['file_name'] = 'course_data_' . time();

        $this->upload->initialize($config);
        // Check if file was uploaded
        if (!$this->upload->do_upload('file')) {
            $error = $this->upload->display_errors();
            if (strpos($error, 'The filetype you are attempting to upload is not allowed.') !== false) {
                $error = 'The file type you selected is not allowed. Please upload a valid Excel file (xls or xlsx).';
            }
            $this->session->set_flashdata('error', $error);
            redirect('upload-course-data');
        } else {
            // If upload is successful, retrieve file data
            $uploadedFile = $this->upload->data();
            $filePath = $uploadedFile['full_path'];
            $spreadsheet = $this->loadSpreadsheet($filePath);
            unlink("uploads/".$uploadedFile["file_name"]); // delete file
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $allowedColumns = [
                'course_id', 'course_intake_ids', 'course_level_tuition_fees',
                'course_level_url', 'course_start_date', 'application_deadline_date',
                'application_fees', 'course_status', 'show','course_name'
            ];
            try {
                if ($highestRow < 2) {
                    throw new Exception("No data available in the worksheet for processing.");
                }

                $headers = $this->extractHeaders($worksheet);
                $dataToUpdate = $this->extractData($worksheet, $headers, $allowedColumns);
                $validationErrors = [];

                foreach ($dataToUpdate as $rowIndex => $row) {
                    // Perform validation for Course Start Date
                    $startDates = isset($row['course_start_date']) ? explode(',', $row['course_start_date']) : [];
                    foreach ($startDates as $date) {
                        if (is_numeric($date)) { // check excel data format
                            $date = Date::excelToDateTimeObject($date)->format('Y-m-d');
                        }
                        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                            $validationErrors[] = [
                                'row_number' => $rowIndex + 2,
                                'course_id' => $row['course_id'],
                                'error' => "Invalid date format in 'Course Start Date'. Expected format: YYYY-MM-DD. Found: {$date}."
                            ];
                        }
                    }

                    // Perform validation for Course Level Tuition Fees
                    $tuitionFees = isset($row['course_level_tuition_fees']) ? explode('|', $row['course_level_tuition_fees']) : [];
                    foreach ($tuitionFees as $fee) {
                        if (!is_numeric($fee)) {
                            $validationErrors[] = [
                                'row_number' => $rowIndex + 2,
                                'course_id' => $row['course_id'],
                                'error' => "Non-numeric value found in 'Course Level Tuition Fees'. Found: {$fee}."
                            ];
                        }
                    }

                    // Perform validation for Application Fee
                    $applicationFees = isset($row['application_fees']) ? explode('|', $row['application_fees']) : [];
                    foreach ($applicationFees as $fee) {
                        if (!is_numeric($fee)) {
                            $validationErrors[] = [
                                'row_number' => $rowIndex + 2,
                                'course_id' => $row['course_id'],
                                'error' => "Non-numeric value found in 'Application Fee'. Found: {$fee}."
                            ];
                        }
                    }
                }
                // Stop execution if errors exist
                if (!empty($validationErrors)) {
                    $errorMessages = "Validation Errors Found:<br>";
                    foreach ($validationErrors as $error) {
                        $errorMessages .= "Row: {$error['row_number']}, Course ID: {$error['course_id']} - {$error['error']}<br>";
                    }
                    throw new Exception($errorMessages);
                }
                
                $bulkInsertData = [];
                $bulkUpdateData = [];
                $mismatchedRows = [];
                
                foreach ($dataToUpdate as $rowIndex => $row) {
                    $this->processCourseData($row, $rowIndex, $bulkInsertData, $bulkUpdateData, $mismatchedRows);
                }

                if (!empty($mismatchedRows)) {
                    $mismatchError = "Error: Data count mismatch detected in the following rows:<br>";
                    foreach ($mismatchedRows as $mismatch) {
                        $mismatchError.= "Row " . $mismatch['row_number'] . " - Course ID: " . $mismatch['course_id'] . "<br>";
                        $mismatchError.=  "Counts: Intake Primary IDs = " . $mismatch['primary_ids_count'] .
                            ", Cousre Start Dates = " . $mismatch['start_dates_count'] .
                            ", Tuition Fees = " . $mismatch['tuition_fees_count'] .
                            ", URLs = " . $mismatch['urls_count'] .
                            ", Application Deadlines = " . $mismatch['deadlines_count'] .
                            ", Application Fees = " . $mismatch['application_fees_count'] . "<br><br>";
                    }
                    throw new Exception($mismatchError);
                }

                $message = '';
                $summery = '';

                if (!empty($bulkInsertData)) {
                    $insertRecords = $this->import_model->insert_courses_intake($bulkInsertData);
                    if (count($insertRecords) > 0) {
                        $message .= "Total intake inserted: " . count($insertRecords) . ".<br>";
                        $summery .= "Intake added for these course ids: " . implode(",", $insertRecords) . ".<br>";
                    }
                }
                if(!empty($dataToUpdate)){ // for udpate course status
                    $updatedCourseRecords = $this->import_model->update_course_status($dataToUpdate);
                    if (count($updatedCourseRecords) > 0) {
                        $message .= "Total Course status updated : ".count($updatedCourseRecords).".<br>";
                        $summery .= "Course status updated for these course ids: ".implode(",", $updatedCourseRecords).".<br>";
                    }
                }
                if(!empty($dataToUpdate)){ // for udpate course name
                    $updatedCourseNameRecords = $this->import_model->update_course_name($dataToUpdate);
                    if (count($updatedCourseNameRecords) > 0) {
                        $message .= "Total Course name updated : ".count($updatedCourseNameRecords).".<br>";
                        $summery .= "Course name updated for these course ids: ".implode(",", $updatedCourseNameRecords).".<br>";
                    }
                }
                if (!empty($bulkUpdateData)) {
                    $updatedRecords = $this->import_model->bulk_update_courses_intake($bulkUpdateData);

                    if (count($updatedRecords['up_course_end_date']) > 0) {
                        $message .= "Total Deadline updated: " . count($updatedRecords['up_course_end_date']) . ".<br>";
                        $summery .= "Deadline updated for these course intake ids: " . implode(",", $updatedRecords['up_course_end_date']) . ".<br>";
                    }
                    if (count($updatedRecords['up_course_start_date']) > 0) {
                        $message .= "Total Course Start date updated: " . count($updatedRecords['up_course_start_date']) . ".<br>";
                        $summery .= "Course start date updated for these course intake ids: " . implode(",", $updatedRecords['up_course_start_date']) . ".<br>";
                    }
                    if (count($updatedRecords['up_international_ft_fees']) > 0) {
                        $message .= "Total Fees updated: " . count($updatedRecords['up_international_ft_fees']) . ".<br>";
                        $summery .= "Fees updated for these course intake ids: " . implode(",", $updatedRecords['up_international_ft_fees']) . ".<br>";
                    }
                    if (count($updatedRecords['up_application_fees']) > 0) {
                        $message .= "Total Application Fees updated: " . count($updatedRecords['up_application_fees']) . ".<br>";
                        $summery .= "Application Fees updated for these course intake ids: " . implode(",", $updatedRecords['up_application_fees']) . ".<br>";
                    }
                    if (count($updatedRecords['up_link']) > 0) {
                        $message .= "Total Link updated: " . count($updatedRecords['up_link']) . ".<br>";
                        $summery .= "Link updated for these course intake ids: " . implode(",", $updatedRecords['up_link']) . ".<br>";
                    }
                }
                if(!empty($message) && !empty($summery)){
                    $this->session->set_flashdata('success', $message . "<br>" . $summery);
                }else{
                    $this->session->set_flashdata('error', 'No changes in sheet.');
                }
            } catch (Exception $e) { // eception handling
                $this->session->set_flashdata('error', 'Error processing the file: ' . $e->getMessage());
            }
            redirect('upload-course-data');
        }
    }

    private function loadSpreadsheet($fileName) {
        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($fileName);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $reader->setReadEmptyCells(false);
        return $reader->load($fileName);
    }

    private function extractHeaders($worksheet) {
        $headers = [];
        $headerRow = $worksheet->getRowIterator(1)->current();
        foreach ($headerRow->getCellIterator() as $cell) {
            $headers[] = str_replace(' ', '_', strtolower(trim($cell->getValue())));
        }
        return $headers;
    }

    private function extractData($worksheet, $headers, $allowedColumns) {
        $data = [];
        foreach ($worksheet->getRowIterator(2) as $row) {
            $rowData = [];
            $cellIndex = 0;

            foreach ($row->getCellIterator() as $cell) {
                if (isset($headers[$cellIndex])) {
                    $rowData[$headers[$cellIndex]] = $cell->getValue();
                }
                $cellIndex++;
            }

            $filteredData = array_intersect_key($rowData, array_flip($allowedColumns));
            if (!empty($filteredData)) {
                $data[] = $filteredData;
            }
        }
        return $data;
    }

    private function processCourseData($row, $rowIndex, &$bulkInsertData, &$bulkUpdateData, &$mismatchedRows)
    {
        
        $primaryIds = explode('|', $row['course_intake_ids']);
        
        $startDates = isset($row['course_start_date']) ? explode(',', $row['course_start_date']) : [];
        $startDates = array_map(function ($date) {
            if (is_numeric($date)) { // check excel data format
                return Date::excelToDateTimeObject($date)->format('Y-m-d');
            }
           return date('Y-m-d', strtotime($date)); // return standard format
        }, $startDates);

        $tuitionFees = isset($row['course_level_tuition_fees']) ? explode('|', $row['course_level_tuition_fees']) : [];
        $urls = isset($row['course_level_url']) ? explode(',', $row['course_level_url']) : [];
        
        $deadLineDates = isset($row['application_deadline_date']) ? explode(',', $row['application_deadline_date']) : [];
        $deadLineDates = array_map(function ($date) {
            if (is_numeric($date)) {
                return Date::excelToDateTimeObject($date)->format('Y-m-d');
            }
            return $date; // return NULL
        }, $deadLineDates);

        $applicationFees = isset($row['application_fees']) ? explode('|', $row['application_fees']) : [];
        $courseStatus = isset($row['course_status']) ? explode(',', $row['course_status']) : [];

        // Validate data consistency
        $expectedCount = count($primaryIds);
        if (
            count($startDates) !== $expectedCount ||
            count($tuitionFees) !== $expectedCount ||
            count($urls) !== $expectedCount ||
            count($deadLineDates) !== $expectedCount ||
            count($applicationFees) !== $expectedCount
        ) {
            $mismatchedRows[] = [
                'row_number' => $rowIndex + 1,
                'course_id' => $row['course_id'],
                'primary_ids_count' => $expectedCount,
                'start_dates_count' => count($startDates),
                'tuition_fees_count' => count($tuitionFees),
                'urls_count' => count($urls),
                'deadlines_count' => count($deadLineDates),
                'application_fees_count' => count($applicationFees),
            ];
        }

        // Process each primary ID
        foreach ($primaryIds as $i => $coursePrimaryId) {
            $startDate = isset($startDates[$i]) ? date("Y-m-d", strtotime($startDates[$i])) : null;
            $endDate = isset($deadLineDates[$i]) && $deadLineDates[$i] !== 'NULL' 
                       ? date("Y-m-d", strtotime($deadLineDates[$i])) 
                       : null;
            $tuitionFee = $tuitionFees[$i] ?? null;
            $url = $urls[$i] ?? null;
            $applicationFee = $applicationFees[$i] ?? null;

            if ($coursePrimaryId === '0') {
                // Insert new course data
                $exists = $this->import_model->check_existing_course([
                    'course_id' => $row['course_id'],
                    'course_start_date' => $startDate,
                    'course_end_date' => $endDate,
                    'international_ft_fees' => (int)$tuitionFee,
                    'link' => $url,
                    'application_fees' => (int)$applicationFee,
                ]);

                if (!$exists) {
                    $bulkInsertData[] = [
                        'course_start_date' => $startDate,
                        'course_end_date' => $endDate,
                        'international_ft_fees' => (int)$tuitionFee,
                        'link' => $url,
                        'application_fees' => (int)$applicationFee,
                        'course_id' => $row['course_id'],
                    ];
                }
            } else {
                // Update existing course data
                $bulkUpdateData[] = [
                    'course_start_date' => $startDate,
                    'course_end_date' => $endDate,
                    'international_ft_fees' => (int)$tuitionFee,
                    'link' => $url,
                    'application_fees' => (int)$applicationFee,
                    'course_id' => $row['course_id'],
                    'id' => $coursePrimaryId,
                ];
            }
        }
    }
}