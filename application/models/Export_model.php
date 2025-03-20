<?php

class Export_model extends CI_Model {
  public $gcsql_db_main;
  public function __construct(){
      parent::__construct();
      // $this->db = $this->load->database( 'gcsql_main', TRUE, TRUE );
      // $CI =& get_instance();
      // $CI->db =& $this->db;
    }

    public function export($ids){
     
      $qry = "SELECT 
      ic.id,
       ic.Show,
       ic.institution_id,
       ic.provider_id,
       ic.course_type,
       ic.study_mode,
       ic.work_placement,
       ic.Link,
       ic.link_txt AS Course_Name,
       (SELECT indt.degree_type 
        FROM institution_degree_types_list indtl 
        JOIN institution_degree_types indt 
        ON indtl.degree_type_id = indt.id 
        WHERE indtl.course_id = ic.id) AS Degree_Type,
       ic.duration,
       -- ic.international_ft_fees AS Tuition_Fees,
       (SELECT GROUP_CONCAT(incsd.id SEPARATOR '|') FROM institution_course_start_date incsd WHERE incsd.course_id = ic.id) AS Course_Intake_ids,
        (SELECT GROUP_CONCAT(incsd.international_ft_fees SEPARATOR '|') FROM institution_course_start_date incsd  WHERE incsd.course_id = ic.id) AS Course_Level_Tuition_Fees,
        (SELECT GROUP_CONCAT(incsd.link) FROM institution_course_start_date incsd WHERE incsd.course_id = ic.id) AS Course_level_url,
        (SELECT GROUP_CONCAT(DATE_FORMAT(incsd.course_start_date, '%Y-%m-%d')) FROM institution_course_start_date incsd WHERE incsd.course_id = ic.id) AS Course_Start_Date,
        (SELECT GROUP_CONCAT(IFNULL(DATE_FORMAT(incsd.course_end_date, '%Y-%m-%d'), 'NULL')) FROM institution_course_start_date incsd 
        WHERE incsd.course_id = ic.id) AS Course_Application_Deadline_Date,
        (SELECT GROUP_CONCAT(incsd.application_fees SEPARATOR '|') FROM institution_course_start_date incsd WHERE incsd.course_id = ic.id) as application_fees,
        (SELECT GROUP_CONCAT(incsd.international_ft_fees SEPARATOR '|') FROM institution_course_start_date incsd WHERE incsd.course_id = ic.id) as Tuition_Fees,
       (SELECT inerpd.previous_degree 
        FROM institution_entry_requirement_previous_degree_score inerpds 
        JOIN institution_entry_requirement_previous_degree inerpd 
        ON inerpds.previous_degree_id = inerpd.id 
        WHERE inerpds.course_id = ic.id) AS Previous_Degree_Required,
   (SELECT inerpds.percentage_required FROM
   institution_entry_requirement_previous_degree_score inerpds WHERE
   inerpds.course_id = ic.id) AS Percentage_Required,
   (SELECT inerpds.gpa FROM
   institution_entry_requirement_previous_degree_score inerpds WHERE
   inerpds.course_id = ic.id) AS GPA_Required,
   (SELECT inerpds.gpa_max FROM
   institution_entry_requirement_previous_degree_score inerpds WHERE
   inerpds.course_id = ic.id) AS GPA_Max,
   (SELECT inerpds.ibscore_overall FROM
   institution_entry_requirement_previous_degree_score inerpds WHERE
   inerpds.course_id = ic.id) AS IBScore_Overall,
   
   (SELECT group_concat(insrs.subject) from institution_subject_rankings_subjects
   insrs JOIN institution_subject_list insl ON insl.subject_id=insrs.id WHERE
   insl.course_id = ic.id) AS Subject_Required_Previous_Degree,
   (SELECT inerpds.work_experience_required FROM
   institution_entry_requirement_previous_degree_score inerpds WHERE
   inerpds.course_id = ic.id) AS Work_Experience_Required,
   
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 1 AND category_id = 1) AND incter.course_id=ic.id) AS
   IELTS_Overall,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 1 AND category_id = 2) AND incter.course_id=ic.id) AS
   IELTS_Speaking,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 1 AND category_id = 3) AND incter.course_id=ic.id) AS
   IELTS_Listening,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 1 AND category_id = 4) AND incter.course_id=ic.id) AS
   IELTS_Reading,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 1 AND category_id = 5) AND incter.course_id=ic.id) AS
   IELTS_Writing,
   
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 3 AND category_id = 1) AND incter.course_id=ic.id) AS
   PTE_Overall,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 3 AND category_id = 2) AND incter.course_id=ic.id) AS
   PTE_Speaking,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 3 AND category_id = 3) AND incter.course_id=ic.id) AS
   PTE_Listening,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 3 AND category_id = 4) AND incter.course_id=ic.id) AS
   PTE_Reading,
   
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 3 AND category_id = 5) AND incter.course_id=ic.id) AS
   PTE_Writing,
   
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 2 AND category_id = 1) AND incter.course_id=ic.id) AS
   TOEFL_Overall,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 2 AND category_id = 2) AND incter.course_id=ic.id) AS
   TOEFL_Speaking,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 2 AND category_id = 3) AND incter.course_id=ic.id) AS
   TOEFL_Listening,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 2 AND category_id = 4) AND incter.course_id=ic.id) AS
   TOEFL_Reading,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 2 AND category_id = 5) AND incter.course_id=ic.id) AS
   TOEFL_Writing,
   
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 4 AND category_id = 1) AND incter.course_id=ic.id) AS
   Duolingo_Overall,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 4 AND category_id = 17) AND incter.course_id=ic.id) AS
   Duolingo_Literacy,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 4 AND category_id = 18) AND incter.course_id=ic.id) AS
   Duolingo_Comprehension,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 4 AND category_id = 19) AND incter.course_id=ic.id) AS
   Duolingo_Conversation,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 4 AND category_id = 20) AND incter.course_id=ic.id) AS
   Duolingo_Production,
   
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 5 AND category_id = 1) AND incter.course_id=ic.id) AS
   GRE_Overall,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 5 AND category_id = 5) AND incter.course_id=ic.id) AS
   GRE_Writing,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 5 AND category_id = 6) AND incter.course_id=ic.id) AS
   GRE_Verbal,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 5 AND category_id = 7) AND incter.course_id=ic.id) AS
   GRE_Quantitative,
   
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 6 AND category_id = 1) AND incter.course_id=ic.id) AS
   GMAT_Overall,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 6 AND category_id = 6) AND incter.course_id=ic.id) AS
   GMAT_Verbal,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 6 AND category_id = 7) AND incter.course_id=ic.id) AS
   GMAT_Quantitative,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 6 AND category_id = 5) AND incter.course_id=ic.id) AS
   GMAT_Writing,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 6 AND category_id = 8) AND incter.course_id=ic.id) AS
   GMAT_Integrated_Reasoning,
   
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 7 AND category_id = 1) AND incter.course_id=ic.id) AS
   SAT_Overall,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 7 AND category_id = 9) AND incter.course_id=ic.id) AS
   SAT_Math,
   
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 7 AND category_id = 10) AND incter.course_id=ic.id) AS
   SAT_Reading_Writing,
   
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 8 AND category_id = 1) AND incter.course_id=ic.id) AS
   ACT_Overall,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 8 AND category_id = 11) AND incter.course_id=ic.id) AS
   ACT_English,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 8 AND category_id = 9) AND incter.course_id=ic.id) AS
   ACT_Math,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 8 AND category_id = 4) AND incter.course_id=ic.id) AS
   ACT_Reading,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 8 AND category_id = 12) AND incter.course_id=ic.id) AS
   ACT_Science,
   
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 9 AND category_id = 1) AND incter.course_id=ic.id) AS
   LSAT_Overall,
   
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 10 AND category_id = 1) AND incter.course_id=ic.id) AS
   UKCAT_Overall,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 10 AND category_id = 13) AND incter.course_id=ic.id) AS
   UKCAT_Verbal_Reasoning,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 10 AND category_id = 14) AND incter.course_id=ic.id) AS
   UKCAT_Quantitative_Reasoning,
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 10 AND category_id = 15) AND incter.course_id=ic.id) AS
   UKCAT_Abstract_Reasoning,
   
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 10 AND category_id = 16) AND incter.course_id=ic.id) AS
   UKCAT_Decision_Making,
   
   (SELECT incter.score FROM institution_courses_test_entry_requirement incter
   WHERE (test_id = 11 AND category_id = 1) AND incter.course_id=ic.id) AS
   STEP_Overall,
   (select l3.name from institution_course_category_level_three l3 where
   l3.id=ic.level_three_id) as Category_L3
   FROM institution_courses ic WHERE ic.institution_id IN ($ids)";
   $query = $this->db->query($qry);
           
    return $query->result_array();
  }
  
  
  public function getunis(){
    

      $this->db->select('`t1`.`id`, `t1`.`header_name`', FALSE);
      $this->db->from('institution_institution AS t1');
      $this->db->where('t1.show', 'Yes');
      $this->db->order_by('`t1`.`header_name`');
      $this->db->group_by('id');
      $query = $this->db->get();
      return $query->result_array();
    }
    public function exportCourseIntakeData($ids){
      $sqlQuery = "SELECT 
	      ic.institution_id,
        ic.id as Course_id,
        ic.link_txt AS Course_Name,
        GROUP_CONCAT(incsd.id SEPARATOR '|') AS Course_Intake_ids,
        GROUP_CONCAT(DATE_FORMAT(incsd.course_start_date, '%Y-%m-%d')) AS Course_Start_Date,
        GROUP_CONCAT(incsd.international_ft_fees SEPARATOR '|') AS Course_Level_Tuition_Fees,
        GROUP_CONCAT(incsd.application_fees SEPARATOR '|') AS  Course_Level_application_fees,
        GROUP_CONCAT(IFNULL(DATE_FORMAT(incsd.course_end_date, '%Y-%m-%d'), 'NULL')) AS Course_Application_Deadline_Date,
        JSON_ARRAYAGG(incsd.link) AS Course_level_Url,
        GROUP_CONCAT(incsd.application_status) AS application_status,
        ic.Show
        FROM institution_courses ic
        LEFT JOIN institution_course_start_date as incsd ON incsd.course_id = ic.id
        WHERE ic.institution_id IN ($ids) GROUP BY ic.id";
        $query = $this->db->query($sqlQuery);
        return $query->result_array();
  }


}
