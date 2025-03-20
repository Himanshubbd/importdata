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
        // Display the upload form
        $this->load->view('upload_course_data_view');
    }
    public function updateCourseDetails()
    {
        die("==");
        $inputFileName = 'updatefiles/course_data_2024-11-22.xlsx';
        $allowedColumns = [
            'course_id', 'course_intake_ids', 'course_level_tuition_fees',
            'course_level_url', 'course_start_date', 'application_deadline_date',
            'application_fee', 'course_status', 'show'
        ];

        try {
            $spreadsheet = $this->loadSpreadsheet($inputFileName);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            if ($highestRow < 2) {
                echo "No data available in the worksheet for processing.";
                return;
            }
            $headers = $this->extractHeaders($worksheet);

            $bulkInsertData = [];
            $bulkUpdateData = [];
            $mismatchedRows = [];
            
            $dataToUpdate = $this->extractData($worksheet, $headers, $allowedColumns);

            foreach ($dataToUpdate as $rowIndex => $row) {
                // Break down fields with `|` separators
                $primaryIds = explode('|', $row['course_intake_ids']);
                $startDates = isset($row['course_start_date']) ? explode(',', $row['course_start_date']) : [];
                $startDates = array_map(function ($date) {
                    // Check if the value is numeric (Excel date format)
                    if (is_numeric($date)) {
                        return Date::excelToDateTimeObject($date)->format('Y-m-d');
                    }
                    // Otherwise, attempt to format it as a standard date
                    return date('Y-m-d', strtotime($date));
                }, $startDates);
                $tuitionFees = isset($row['course_level_tuition_fees']) ? explode('|', $row['course_level_tuition_fees']) : [];
                $urls = isset($row['course_level_url']) ? explode(',', $row['course_level_url']) : [];
                $deadLineDates = isset($row['application_deadline_date']) ? explode(',', $row['application_deadline_date']) : [];
                $deadLineDates = array_map(function ($date) {
                    // Check if the value is numeric (Excel date format)
                    if (is_numeric($date)) {
                        return Date::excelToDateTimeObject($date)->format('Y-m-d');
                    }
                    // Otherwise, attempt to format it as a standard date
                    return $date;
                }, $deadLineDates);
                $applicationFees = isset($row['application_fee']) ? explode('|', $row['application_fee']) : [];
                $courseStatus = isset($row['course_status']) ? explode(',', $row['course_status']) : [];
                
                // Validate that all arrays have the same count as primaryIds
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
                foreach ($primaryIds as $i => $coursePrimaryId) {
                    $startDate = isset($startDates[$i]) ? date("Y-m-d", strtotime($startDates[$i])) : null;
                    $endDate = isset($deadLineDates[$i]) && $deadLineDates[$i] !== 'NULL' 
                               ? date("Y-m-d", strtotime($deadLineDates[$i])) 
                               : null;
                    $tuitionFee = $tuitionFees[$i] ?? null;
                    $url = $urls[$i] ?? null;
                    $applicationFee = $applicationFees[$i] ?? null;
            
                    // Check if the Intake ID is zero, then perform the insert
                    if ($coursePrimaryId === '0') {
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
                        // for update
                        $bulkUpdateData[] = [
                            'course_start_date' => $startDate,
                            'course_end_date' => $endDate,
                            'international_ft_fees' => (int)$tuitionFee,
                            'link' => $url,
                            'application_fees' => (int)$applicationFee,
                            'course_id' => $row['course_id'],
                            'id' => $coursePrimaryId
                        ];
                    }
                }
            }
            if (!empty($mismatchedRows)) {
                echo "Error: Data count mismatch detected in the following rows:<br>";
                foreach ($mismatchedRows as $mismatch) {
                    echo "Row " . $mismatch['row_number'] . " - Course ID: " . $mismatch['course_id'] . "<br>";
                    echo "Counts: Intake Primary IDs = " . $mismatch['primary_ids_count'] .
                         ", Cousre Start Dates = " . $mismatch['start_dates_count'] .
                         ", Tuition Fees = " . $mismatch['tuition_fees_count'] .
                         ", URLs = " . $mismatch['urls_count'] .
                         ", Application Deadlines = " . $mismatch['deadlines_count'] .
                         ", Application Fees = " . $mismatch['application_fees_count'] . "<br><br>";
                }
               die("Execution stopped due to a data inconsistency."); // Stop execution
            }
            $message = '';
            $summery = '';

            if(!empty($bulkInsertData)){
                $insertRecords = $this->import_model->insert_courses_intake($bulkInsertData);
                if (count($insertRecords) > 0) {
                    $message .= "Total intake inserted : ".count($insertRecords).".<br>";
                    $summery .= "Intake added for these course ids: ".implode(",",$insertRecords).".<br>";
                }
            }

            if(!empty($dataToUpdate)){
                $updatedCourseRecords = $this->import_model->update_course_status($dataToUpdate);
                if (count($updatedCourseRecords) > 0) {
                    $message .= "Total Course status updated : ".count($updatedCourseRecords).".<br>";
                    $summery .= "Course status updated for these course ids: ".implode(",", $updatedCourseRecords).".<br>";
                }
            }
            if (!empty($bulkUpdateData)) {
                $updatedRecords = $this->import_model->bulk_update_courses_intake($bulkUpdateData);
                if (count($updatedRecords) > 0) {
                    $message .= "Total Deadline updated : ".count($updatedRecords).".<br>";
                    $summery .= "Deadline updated for these course ids : ".implode(",", $updatedRecords).".<br>";
                } else {
                    echo "All course intake records have been processed, but no updates were needed for the provided data.";
                }
            } else {
                echo "No data found to update.";
            }

            if(!empty($message) && !empty($summery)){
                echo $message."<br><br>".$summery;
            }

        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            echo 'Error loading file: ' . $e->getMessage();
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
}