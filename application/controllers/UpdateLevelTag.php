<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
ini_set('max_execution_time', '0');

class UpdateLevelTag extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('upload');
        $this->load->model('import_model');
    }

    public function index() {
        $this->load->view('upload_course_tag_data_view');
    }
    
    public function updateCourseTag(){
        try {
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'xls|xlsx';
            $config['encrypt_name'] = FALSE;
            $config['file_name'] = 'course_tag_data_' . time();

            $this->upload->initialize($config);
            // Check if file was uploaded
            if (!$this->upload->do_upload('file')) {
                $error = $this->upload->display_errors();
                if (strpos($error, 'The filetype you are attempting to upload is not allowed.') !== false) {
                    $error = 'The file type you selected is not allowed. Please upload a valid Excel file (xls or xlsx).';
                }
                $this->session->set_flashdata('error', $error);
            } else {
                // If upload is successful, retrieve file data
                $uploadedFile = $this->upload->data();
                $filePath = $uploadedFile['full_path'];
                $spreadsheet = $this->loadSpreadsheet($filePath);
                unlink("uploads/".$uploadedFile["file_name"]); // delete file
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();
                $allowedColumns = [
                    'course_id', 'level_3_tagging_id'
                ];
                if ($highestRow < 2) {
                    throw new Exception("No data available in the worksheet for processing.");
                }

                $headers = $this->extractHeaders($worksheet);
                $dataToUpdate = $this->extractData($worksheet, $headers, $allowedColumns);

                $bulkUpdateData = [];
                
                foreach ($dataToUpdate as $rowIndex => $row) {
                    $bulkUpdateData[] = [
                        'id' => $row['course_id'],
                        'level_three_id'  => $row['level_3_tagging_id']
                    ];
                }
                $message = '';
                $summery = '';
                if (!empty($bulkUpdateData)) {
                    $updatedRecords = $this->import_model->bulk_update_courses_tag($bulkUpdateData);
                    if (count($updatedRecords) > 0) {
                        $message .= "Total Course level three tag updated : ".count($updatedRecords).".<br>";
                        $summery .= "These course ids: ".implode(",", $updatedRecords).".<br>";
                    }
                }
                if(!empty($message) && !empty($summery)){
                    $this->session->set_flashdata('success', $message . "<br>" . $summery);
                }else{
                    $this->session->set_flashdata('error', 'No changes in sheet.');
                }
                redirect('update-level-tag');
            }
        }catch (Exception $e) { // eception handling
            $this->session->set_flashdata('error', 'Error processing the file: ' . $e->getMessage());
        }
        redirect('update-level-tag');
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

}