<?php

ini_set('max_execution_time', '0');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Export extends CI_Controller
{
  private $headerColumns = ["Institution Id","Course Id","Course Name","Course Intake Ids", "Course start date","Course Level Tuition Fees","Application Fees","Application Deadline Date","Course Level URL","Course Status","Show"];
  public function __construct()
  {
    parent::__construct();
    $this->load->model('export_model');

  }
  
   public function index() {
        $this->export();
    }

  public function view_courses()
  {
    ini_set('max_execution_time', '0'); // for infinite time of execution 
    $com_values = implode(",", $this->input->post('exportID'));
	  $Id=$com_values;
    $exportDatas = $this->export_model->exportCourseIntakeData($Id);
    $inst_id=$Id;
    $this->load->view('view_data', array(
      'inst_id' =>$inst_id,
      'exportDatas' => $exportDatas,
      'headerColumn'=> $this->headerColumns
    ));
  
 
  }
  
  public function export()
  {
    $Id = $this->input->get('id');
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $headerColumns = $this->headerColumns;
    $sheet->setTitle("Course Data"); // Set the sheet name
    $sheet->fromArray($headerColumns, NULL, 'A1');
    $headerStyle = [
      'font' => [
          'bold' => true,
          'size' => 12,
      ],
      'alignment' => [
          'horizontal' => Alignment::HORIZONTAL_LEFT,
          'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ];
    $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($headerStyle);
    $exportData = $this->export_model->exportCourseIntakeData($Id);
    $rowNumber = 2; // from the second row
    $serialNumber = 1; // Initialize serial number
    foreach ($exportData as $row) {
        if (!empty($row['Course_level_Url'])) {
          $row['Course_level_Url'] = implode(',', json_decode($row['Course_level_Url'], true));
        }
        $sheet->fromArray(array_values($row), NULL, "A{$rowNumber}"); // Populate each row
        $rowNumber++;
    }
    $dataRange = 'A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow();
    $sheet->getStyle($dataRange)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
          ],
          'alignment' => [
              'horizontal' => Alignment::HORIZONTAL_LEFT,
              'vertical' => Alignment::VERTICAL_CENTER,
          ]
    ]);

    $fileName = "course_data_" . date('Y-m-d') . ".xlsx";

    // Output the file
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$fileName}\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output'); // Stream the file directly to the browser
    exit;
  }
  
  public function export_input()
  {
    $exportuniversities = $this->export_model->getunis();
    $this->load->view('exportinput', array(
      'exportuniversities' => $exportuniversities
    ));
  }
 
}