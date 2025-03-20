<?php

class Import_model extends CI_Model {
  public $gcsql_db_main;
  public function __construct(){
      parent::__construct();
      // $this->db = $this->load->database( 'gcsql_main', TRUE, TRUE );
      // $CI =& get_instance();
      // $CI->db =& $this->db;
    }

    public function import($schdeules){
      $data =[];
      for($i=1;$i<count($schdeules);$i++){
        $inst_id = isset($schdeules[$i][1]) ? $schdeules[$i][1] : '';
        $provider_id = isset($schdeules[$i][2]) ?  $schdeules[$i][2] :'';
        $course_type = isset($schdeules[$i][3]) ? $schdeules[$i][3] : '';
        $study_mode = isset($schdeules[$i][4]) ? $schdeules[$i][4] :'';
        $work_placement = isset($schdeules[$i][5]) ? $schdeules[$i][5] : '';
        $course_url = isset($schdeules[$i][6]) ? $schdeules[$i][6] :'';
        $course_name = isset($schdeules[$i][7]) ? $schdeules[$i][7] :'';
        $degree_type = isset($schdeules[$i][8]) ? $schdeules[$i][8] : '';
        $course_duration = isset($schdeules[$i][9]) ? $schdeules[$i][9] : '';
        $internation_tution_fee = isset($schdeules[$i][10]) ? $schdeules[$i][10] : '';
        $course_start_date =  isset($schdeules[$i][11]) ? $schdeules[$i][11] : '';
        $app_deadline = isset($schdeules[$i][12]) ?  $schdeules[$i][12] : '';
        $app_fees = isset($schdeules[$i][13]) ? $schdeules[$i][13] : '';
        $previous_degree_req = isset($schdeules[$i][14]) ? $schdeules[$i][14] :'';
        $percentage_req = isset($schdeules[$i][15]) ? $schdeules[$i][15] : '';
        $gpa_req = isset($schdeules[$i][16]) ? $schdeules[$i][16] : '';
        $gpa_max = isset($schdeules[$i][17]) ? $schdeules[$i][17] : '';
        $ib_score_req = isset($schdeules[$i][18]) ? $schdeules[$i][18] : '';

        $req_subject_in_pd = isset($schdeules[$i][19]) ? $schdeules[$i][19] : '';
        $work_exp = isset($schdeules[$i][20]) ? $schdeules[$i][20] : '';
        $ielts_overall = isset($schdeules[$i][21]) ? $schdeules[$i][21] : '';
        $ielts_speaking = isset($schdeules[$i][22]) ? $schdeules[$i][22] : '';
        $ielts_listening = isset($schdeules[$i][23]) ? $schdeules[$i][23] : '';
        $ielts_reading = isset($schdeules[$i][24]) ? $schdeules[$i][24] : '';
        $ielts_writing = isset($schdeules[$i][25]) ? $schdeules[$i][25] : '';
        $pte_overall = isset($schdeules[$i][26]) ? $schdeules[$i][26] : '';
        $pte_speaking = isset($schdeules[$i][27]) ? $schdeules[$i][27] :'';
        $pte_listening = isset($schdeules[$i][28]) ? $schdeules[$i][28] : '';
        $pte_reading = isset($schdeules[$i][29]) ? $schdeules[$i][29] : '';
        $pte_writing = isset($schdeules[$i][30]) ?  $schdeules[$i][30] : '';
        $toefl_overall = isset($schdeules[$i][31]) ? $schdeules[$i][31] : '';
        $toefl_speaking = isset($schdeules[$i][32]) ? $schdeules[$i][32] : '';
        $toefl_listening = isset($schdeules[$i][33]) ? $schdeules[$i][33] : '';
        $toefl_reading = isset($schdeules[$i][34]) ? $schdeules[$i][34] : '';
        $toefl_writing = isset($schdeules[$i][35]) ? $schdeules[$i][35] : '';
		//$commisionable = isset($schdeules[$i][36]) ? $schdeules[$i][36] : '';
        //$commisionable = 'Yes';
        $course_name = $this->db->escape($course_name);

        if($inst_id && $degree_type){
			
	    $crmQuery = "SELECT crm_id FROM institution_course_type WHERE course_type = '".$course_type."'";
        $crmResult = $this->db->query($crmQuery);
        $crmRow = $crmResult->row();
        $course_type_id = $crmRow->crm_id;

            $selectqry ="SELECT id FROM ".institution_courses." where institution_id='".$inst_id."' AND course_type='".$course_type."' AND link_txt=".$course_name;
            $squery = $this->db->query($selectqry);
            $checkr = $squery->num_rows();
           // if ($checkr > 0) {
              //  $query = "INSERT INTO institution_courses_dummy_lastest ( `institution_id`, `provider_id`, `course_type`, `study_mode`, `work_placement`, `link`, `link_txt`, `international_ft_fees`, `duration`, `application_fees`) VALUES ('".$inst_id."','".$provider_id."','".$course_type."','".$study_mode."','".$work_placement."','".$course_url."',".$course_name.",'".$internation_tution_fee."','".$course_duration."','".$app_fees."')";
            //$query = $this->db->query($query);
           // }else{ 

              $query = "INSERT INTO ".institution_courses." ( `institution_id`, `provider_id`, `course_type`,`coursetype_crmid`, `study_mode`, `work_placement`, `link`, `link_txt`, `international_ft_fees`, `duration`, `application_fees` ) VALUES ('".$inst_id."','".$provider_id."','".$course_type."','".$course_type_id."','".$study_mode."','".$work_placement."','".$course_url."',".$course_name.",'".$internation_tution_fee."','".$course_duration."','".$app_fees."')";
              $query = $this->db->query($query);
              $last_id = $this->db->insert_id();
              if(!empty($course_start_date)){
                if(substr_count($course_start_date, ",")==0){
                  $start_date = $course_start_date;
                  $enddate = $app_deadline;
                  //$cs_date_query = "INSERT INTO ".institution_course_start." ( `course_id`, `course_start_date`, `course_end_date`) VALUES ('".$last_id."','".$start_date."','".$enddate."')";
                  $cs_date_query = "INSERT INTO ".institution_course_start." ( `course_id`, `course_start_date`, `course_end_date`,`link`,`international_ft_fees`, `application_fees`) VALUES ('".$last_id."','".$start_date."',NULLIF('$enddate',''),'".$course_url."','".$internation_tution_fee."','".$app_fees."')";  
                  $query = $this->db->query($cs_date_query);
                }else{
                  $cs_date = explode(',',$course_start_date);
                  if($app_deadline==""){
                    $ce_date = array();  
                  }
                  else{
                    $ce_date = explode(',',$app_deadline);
                  }
                  foreach ($cs_date as $key => $value) {
                    $start_date = trim($value);
                    if (count($ce_date)>0) {
                      if(!isset($ce_date[$key]))
                        $ce_date[$key] = 0;
                      $enddate = $ce_date[$key] !=0 ? trim($ce_date[$key]) : ''  ;
                    }else{
                      $enddate = '';
                    }

                    //$cs_date_query = "INSERT INTO ".institution_course_start." ( `course_id`, `course_start_date`, `course_end_date`) VALUES ('".$last_id."','".$start_date."','".$enddate."')";
                    $cs_date_query = "INSERT INTO ".institution_course_start." ( `course_id`, `course_start_date`, `course_end_date`,`link`,`international_ft_fees`, `application_fees`) VALUES ('".$last_id."','".$start_date."',NULLIF('$enddate',''),'".$course_url."','".$internation_tution_fee."','".$app_fees."')";          
                    $query = $this->db->query($cs_date_query);
                  }
                }
                
              }

              $degree_query = "INSERT INTO ".institution_degree." ( `course_id`, `degree_type_id`) VALUES ('".$last_id."','".$degree_type."')";
              $query = $this->db->query($degree_query);

              $prev_degree_query = "INSERT INTO ".institution_entry." ( `course_id`, `previous_degree_id`, `percentage_required`, `gpa`, `gpa_max`, `ibscore_overall`, `work_experience_required`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_previous_degree WHERE previous_degree = '".$previous_degree_req."'),'".$percentage_req."','".$gpa_req."','".$gpa_max."','".$ib_score_req."','".$work_exp."')";
              $query = $this->db->query($prev_degree_query);

              if($req_subject_in_pd){
                if(substr_count($req_subject_in_pd, ",")==0){
                $req_sub_query = "INSERT INTO ".institution_subject." ( `course_id`, `subject_id`) VALUES ('".$last_id."',(SELECT id FROM institution_subject_rankings_subjects WHERE subject = '".trim($req_subject_in_pd)."'))";
              $query = $this->db->query($req_sub_query);

            }else{
              $sub = explode(',',$req_subject_in_pd);
              foreach ($sub as $key => $value) {
                  $req_subject_in_pd = trim($value);
                  $req_sub_query = "INSERT INTO ".institution_subject." ( `course_id`, `subject_id`) VALUES ('".$last_id."',(SELECT id FROM institution_subject_rankings_subjects WHERE subject = '".$req_subject_in_pd."'))";
              $query = $this->db->query($req_sub_query);
                }
              }
              
            }
              

              if($ielts_overall){
                $ielts_overall_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'IELTS'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Overall'),'".$ielts_overall."')";
                $query = $this->db->query($ielts_overall_query);
              }

              if($ielts_reading){
                $ielts_reading_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'IELTS'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Reading'),'".$ielts_reading."')";
                $query = $this->db->query($ielts_reading_query);
              }

            if($ielts_writing){
              $ielts_writing_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'IELTS'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Writing'),'".$ielts_writing."')";
              $query = $this->db->query($ielts_writing_query);
            }

            if($ielts_listening){
              $ielts_listening_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'IELTS'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Listening'),'".$ielts_listening."')";
              $query = $this->db->query($ielts_listening_query);
            }

            if($ielts_speaking){
              $ielts_speaking_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'IELTS'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Speaking'),'".$ielts_speaking."')";
              $query = $this->db->query($ielts_speaking_query);
            }
            if($pte_overall){
              $pte_overall_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'PTE'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Overall'),'".$pte_overall."')";
              $query = $this->db->query($pte_overall_query);
            }
            if($pte_reading){
              $pte_reading_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'PTE'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Reading'),'".$pte_reading."')";
              $query = $this->db->query($pte_reading_query);
            }
            if($pte_writing){
              $pte_writing_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'PTE'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Writing'),'".$pte_writing."')";
              $query = $this->db->query($pte_writing_query);
            }
            if($pte_listening){
              $pte_listening_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'PTE'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Listening'),'".$pte_listening."')";
              $query = $this->db->query($pte_listening_query);
            }
            if($pte_speaking){
              $pte_speaking_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'PTE'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Speaking'),'".$pte_speaking."')";
              $query = $this->db->query($pte_speaking_query);
          }
          if($toefl_overall){
              $toefl_overall_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'TOEFL iBT'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Overall'),'".$toefl_overall."')";
              $query = $this->db->query($toefl_overall_query);
            }
            if($toefl_reading){
              $toefl_reading_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'TOEFL iBT'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Reading'),'".$toefl_reading."')";
              $query = $this->db->query($toefl_reading_query);
            }
            if($toefl_writing){
              $toefl_writing_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'TOEFL iBT'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Writing'),'".$toefl_writing."')";
              $query = $this->db->query($toefl_writing_query);
            }
            if($toefl_listening){
              $toefl_listening_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'TOEFL iBT'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Listening'),'".$toefl_listening."')";
              $query = $this->db->query($toefl_listening_query);
            }
            if($toefl_speaking){
              $toefl_speaking_query = "INSERT INTO ".institution_courses_test." ( `course_id`, `test_id`, `category_id`, `score`) VALUES ('".$last_id."',(SELECT id FROM institution_entry_requirement_tests WHERE test = 'TOEFL iBT'),(SELECT id FROM institution_entry_requirement_categories WHERE category = 'Speaking'),'".$toefl_speaking."')";
              $query = $this->db->query($toefl_speaking_query);
            }
        //}
      }
    }
  }



  public function getCourseId($uniId){
    $query =  $this->db->select('*')->where('institution_id',$uniId)->get(institution_courses)->result_array();
    return $query;
  }

  public function deletedatawithcourseid($tablename,$coursecon){
    $this->db->where($coursecon);
    $this->db->delete($tablename);
    return true;
  }

  public function bulk_update_courses_intake($bulkUpdateData) {
    $updatedRecords = [];
    foreach ($bulkUpdateData as $data) {
        // Check if the course already exists using the 'id'
        $this->db->where('id', $data['id']);
        $this->db->where('course_id', $data['course_id']);
        $existingData  = $this->db->get(institution_course_start)->row_array();
        
        if ($existingData) {
          // Track fields that need to be updated
          $updateFields = [];
          // Compare course_start_date
          if ($existingData['course_start_date'] !== $data['course_start_date']) {
            $updateFields['course_start_date'] = $data['course_start_date'];
            $up_course_start_date[] = $data['id'];
          }
          //Compare course_end_date
          if ($existingData['course_end_date'] !== $data['course_end_date']) {
            $updateFields['course_end_date'] = $data['course_end_date'];
            $up_course_end_date[] = $data['id'];
          }
          
          // Compare international_ft_fees
          if ($existingData['international_ft_fees'] != $data['international_ft_fees']) {
            $updateFields['international_ft_fees'] = $data['international_ft_fees'];
            $up_international_ft_fees[] = $data['id'];
          }

          // Compare application fees
          if ($existingData['application_fees'] != $data['application_fees']) {
            $updateFields['application_fees'] = $data['application_fees'];
            $up_application_fees[] = $data['id'];
          }

          // Compare link
          if ($existingData['link'] !== $data['link']) {
            $updateFields['link'] = $data['link'];
            $up_link[] = $data['id'];
          }

          if (!empty($updateFields)) {
              $this->db->where('id', $data['id']);
              $this->db->where('course_id', $data['course_id']);
              $this->db->update(institution_course_start, $updateFields);
          }
        }
    }
    return $updatedRecords=[
      'up_course_start_date' => $up_course_start_date ?? [],
      'up_course_end_date' => $up_course_end_date ?? [],
      'up_international_ft_fees' => $up_international_ft_fees ?? [],
      'up_application_fees' => $up_application_fees ?? [],
      'up_link' => $up_link ?? []
    ];
  }
  public function insert_courses_intake($bulkInsertData)
  {
      $inserted = [];

      foreach ($bulkInsertData as $insertRow) {
        // Insert into the database (if course_id does not exist)
        $this->db->insert(institution_course_start, $insertRow);
        $inserted[] = $insertRow['course_id'];
      }

      return $inserted; // Return the number of inserted/updated rows
  }
  //Start
  public function update_course_status($bulkCourseData){
    $updatedRecords = [];
    foreach ($bulkCourseData as $data) {
      $this->db->where('id', $data['course_id']);
      $existingData  = $this->db->get(institution_courses)->row_array();
      if ($existingData) {
        $updateCourseFields = [];
        if (strtolower($existingData['show']) != strtolower($data['show'])) {
          $updateCourseFields['show'] = ucwords(strtolower($data['show']));
        }
        if (!empty($updateCourseFields)) {
          $this->db->where('id', $data['course_id']);
          $this->db->update(institution_courses, $updateCourseFields);
          $updatedRecords[] = $data['course_id'];
        }
      }
    }
    return $updatedRecords;
  }
  public function update_course_name($bulkCourseData){
    $updatedRecords = [];
    foreach ($bulkCourseData as $data) {
      $this->db->where('id', $data['course_id']);
      $existingData  = $this->db->get(institution_courses)->row_array();
      if ($existingData) {
        $updateCourseFields = [];

        if (strtolower($existingData['link_txt']) != strtolower($data['course_name'])) {
          $updateCourseFields['link_txt'] = ucwords(strtolower($data['course_name']));
        }
        if (!empty($updateCourseFields)) {
          $this->db->where('id', $data['course_id']);
          $this->db->update(institution_courses, $updateCourseFields);
          $updatedRecords[] = $data['course_id'];
        }
      }
    }
    return $updatedRecords;
  }
  //End
  public function check_existing_course($data)
  {
      $this->db->where('course_id', $data['course_id']);
      $this->db->where('course_start_date', $data['course_start_date']);
      $this->db->where('course_end_date', $data['course_end_date']);
      $this->db->where('international_ft_fees', $data['international_ft_fees']);
      $this->db->where('link', $data['link']);
      $this->db->where('application_fees', $data['application_fees']);

      $query = $this->db->get(institution_course_start);

      return $query->num_rows() > 0;
  }

  // Function for the update Level tag three for course
  public function bulk_update_courses_tag($bulkUpdateData) {
    $updatedRecords = [];
    $updateData = [];
    foreach ($bulkUpdateData as $data) {
      $this->db->where('id', $data['id']);
      $existingData  = $this->db->get(institution_courses)->row_array();
      if ($existingData) {
        $updateData[] = [
          'id' => $data['id'],
          'level_three_id' => $data['level_three_id']
        ];
        $updatedRecords[] = $data['id'];
      }
    }
    // Perform batch update if there are records to update
    if (!empty($updateData)) {
      $this->db->update_batch(institution_courses, $updateData, 'id');
    }
    // Return updated records count
    return $updatedRecords;
  }
}
