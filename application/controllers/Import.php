<?php
// require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
ini_set('max_execution_time', '0');



if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Import extends CI_Controller
{


  public function __construct()
  {
    parent::__construct();
    $this->load->model('import_model');
  }

  public function import()
  {
   
	 if(!$this->session->userdata('importdata')){
	
   // echo"You are here" ; exit();
    $inputFileName = "importfiles/Copy of Bowling Green State University.xlsx";

    $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);

    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
    $reader->setReadEmptyCells(false);
    $spreadsheet = $reader->load($inputFileName);
    $sheetCount = $spreadsheet->getSheetCount(); 
    // echo "<pre>";
    for ($i = 0; $i < $sheetCount; $i++) 
    {
      $sheet = $spreadsheet->getSheet($i);
      $sheetData = $sheet->toArray();
      
      //echo ($spreadsheet->getSheet($i)->getTabColor()->getRGB());
      $this->import_model->import($sheetData);
      // echo "<br>";
    }
    echo "Data Inserted successfully";
	$submit = $_POST["import"];
	  $this->session->set_userdata('importdata',$submit);
    
  }else{
	  
     echo "Data already imported";
	 
	 
  }
  }


  public function deletecoursedata($uniId){
      $coursedata = $this->import_model->getCourseId($uniId);
      foreach ($coursedata as $key => $value) {
            $this->import_model->deletedatawithcourseid(institution_courses_test,array('course_id' => $value['id']));
            $this->import_model->deletedatawithcourseid(institution_subject,array('course_id' => $value['id']));
            $this->import_model->deletedatawithcourseid(institution_entry,array('course_id' => $value['id']));
            $this->import_model->deletedatawithcourseid(institution_degree,array('course_id' => $value['id']));
            $this->import_model->deletedatawithcourseid(institution_course_start,array('course_id' => $value['id']));
           
      }
      $this->import_model->deletedatawithcourseid(institution_courses,array('institution_id' => $uniId));
      echo "Data deleted successfully with institution id => ".$uniId;
  }
}