<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// File name
$filename = "Courses-". date("Y-m-d") .".xls";

// Fetching data from database
$cnt = 1;

// Headers for the file download
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename);
//header("Pragma: no-cache");
//header("Expires: 0");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Export</title>
</head>
<body>
    <table border="1">
        <thead>
           <tr>
    <th>Sr.</th>
	<th>Course ID</th>
	<th>Show</th>
    <th>Institution Id</th>
    <th>Provider Id</th>
    <th>Course Type</th>
    <th>Study Mode</th>
    <th>Work Placement</th>
    <th>Course URL</th>
	<th>Course Name</th>
    <th>Degree Type</th>
    <th>Course Duartion</th>
    <th>International Tuition Fees</th>
    <th>Course Intake Ids</th>
    <th>Course Level Tuition Fees</th>
    <th>Course Level URL</th>
    <th>Course start date</th>
    <th>Application Deadline Date</th>
    <th>Application Fee</th>
    <th>Previous Degree Required</th>
    <th>Percentage Required</th>
    <th>GPA Required</th>
    <th>GPA Max</th>
    <th>IB Score Required</th>
    <th>Required Subjects in Previous Degree</th>
    <th>Work Experience required</th>
    <th>IELTS Overall</th>
    <th>IELTS Speaking</th>
    <th>IELTS Listening</th>
    <th>IELTS Reading</th>
    <th>IELTS Writing</th>
    <th>PTE Overall</th>
    <th>PTE Speaking</th>
    <th>PTE Listening</th>
    <th>PTE Reading</th>
    <th>PTE Writing</th>
    <th>TOEFL Overall</th>
    <th>TOEFL Speaking</th>
    <th>TOEFL Listening</th>
    <th>TOEFL Reading</th>
    <th>TOEFL Writing</th>
    <th>Duolingo Overall</th>
    <th>Duolingo Speaking</th>
    <th>Duolingo Listening</th>
    <th>Duolingo Reading</th>
    <th>Duolingo Writing</th>
    <th>GRE Overall</th>
    <th>GRE Writing</th>
    <th>GRE Verbal</th>
    <th>GRE Quantitative</th>
    <th>GMAT Overall</th>
    <th>GMAT Verbal</th>
    <th>GMAT Quantitative</th>
    <th>GMAT Writing</th>
    <th>GMAT Integrated Reasoning</th>
    <th>SAT Overall</th>
    <th>SAT Math</th>
    <th>SAT Reading & Writing</th>
    <th>ACT Overall</th>
    <th>ACT English</th>
    <th>ACT Math</th>
    <th>ACT Reading</th>
    <th>ACT Science</th>
    <th>LSAT Overall</th>
    <th>UKCAT Overall</th>
    <th>UKCAT Verbal Reasoning</th>
    <th>UKCAT Quantitative Reasoning</th>
    <th>UKCAT Abstract Reasoning</th>
    <th>UKCAT Decision Making</th>
    <th>STEP Overall</th>
    <th>Category L3</th>
    
</tr>
        </thead>
        <tbody>
            <?php foreach($exportData as $exportDatas) { ?>
               <tr>
    <td><?php echo $cnt; ?></td>
	<td><?php echo $exportDatas['id']; ?></td>
    <td><?php echo $exportDatas['Show']; ?></td>
    <td><?php echo $exportDatas['institution_id']; ?></td>
    <td><?php echo $exportDatas['provider_id']; ?></td>
    <td><?php echo $exportDatas['course_type']; ?></td>
    <td><?php echo $exportDatas['study_mode']; ?></td>
    <td><?php echo $exportDatas['work_placement']; ?></td>
    <td><?php echo $exportDatas['Link']; ?></td>
    <td><?php echo $exportDatas['Course_Name']; ?></td>
    <td><?php echo $exportDatas['Degree_Type']; ?></td>
    <td><?php echo $exportDatas['duration']; ?></td>
    <td><?php echo $exportDatas['Tuition_Fees']; ?></td>
    <td><?php echo $exportDatas['Course_Intake_ids']; ?></td>
    <td><?php echo $exportDatas['Course_Level_Tuition_Fees']; ?></td>
    <td><?php echo $exportDatas['Course_level_url']; ?></td>
    <td><?php echo $exportDatas['Course_Start_Date']; ?></td>
    <td><?php echo $exportDatas['Course_Application_Deadline_Date']; ?></td>
    <td><?php echo $exportDatas['application_fees']; ?></td>
    <td><?php echo $exportDatas['Previous_Degree_Required']; ?></td>
    <td><?php echo $exportDatas['Percentage_Required']; ?></td>
    <td><?php echo $exportDatas['GPA_Required']; ?></td>
    <td><?php echo $exportDatas['GPA_Max']; ?></td>
    <td><?php echo $exportDatas['IBScore_Overall']; ?></td>
    <td><?php echo $exportDatas['Subject_Required_Previous_Degree']; ?></td>
    <td><?php echo $exportDatas['Work_Experience_Required']; ?></td>
    <td><?php echo $exportDatas['IELTS_Overall']; ?></td>
    <td><?php echo $exportDatas['IELTS_Speaking']; ?></td>
    <td><?php echo $exportDatas['IELTS_Listening']; ?></td>
    <td><?php echo $exportDatas['IELTS_Reading']; ?></td>
    <td><?php echo $exportDatas['IELTS_Writing']; ?></td>
    <td><?php echo $exportDatas['PTE_Overall']; ?></td>
    <td><?php echo $exportDatas['PTE_Speaking']; ?></td>
    <td><?php echo $exportDatas['PTE_Listening']; ?></td>
    <td><?php echo $exportDatas['PTE_Reading']; ?></td>
    <td><?php echo $exportDatas['PTE_Writing']; ?></td>
    <td><?php echo $exportDatas['TOEFL_Overall']; ?></td>
    <td><?php echo $exportDatas['TOEFL_Speaking']; ?></td>
    <td><?php echo $exportDatas['TOEFL_Listening']; ?></td>
    <td><?php echo $exportDatas['TOEFL_Reading']; ?></td>
    <td><?php echo $exportDatas['TOEFL_Writing']; ?></td>
    <td><?php echo $exportDatas['Duolingo_Overall']; ?></td>
    <td><?php echo $exportDatas['Duolingo_Literacy']; ?></td>
    <td><?php echo $exportDatas['Duolingo_Comprehension']; ?></td>
    <td><?php echo $exportDatas['Duolingo_Conversation']; ?></td>
    <td><?php echo $exportDatas['Duolingo_Production']; ?></td>
    <td><?php echo $exportDatas['GRE_Overall']; ?></td>
    <td><?php echo $exportDatas['GRE_Writing']; ?></td>
    <td><?php echo $exportDatas['GRE_Verbal']; ?></td>
    <td><?php echo $exportDatas['GRE_Quantitative']; ?></td>
    <td><?php echo $exportDatas['GMAT_Overall']; ?></td>
    <td><?php echo $exportDatas['GMAT_Verbal']; ?></td>
    <td><?php echo $exportDatas['GMAT_Quantitative']; ?></td>
    <td><?php echo $exportDatas['GMAT_Writing']; ?></td>
    <td><?php echo $exportDatas['GMAT_Integrated_Reasoning']; ?></td>
    <td><?php echo $exportDatas['SAT_Overall']; ?></td>
    <td><?php echo $exportDatas['SAT_Math']; ?></td>
    <td><?php echo $exportDatas['SAT_Reading_Writing']; ?></td>
    <td><?php echo $exportDatas['ACT_Overall']; ?></td>
    <td><?php echo $exportDatas['ACT_English']; ?></td>
    <td><?php echo $exportDatas['ACT_Math']; ?></td>
    <td><?php echo $exportDatas['ACT_Reading']; ?></td>
    <td><?php echo $exportDatas['ACT_Science']; ?></td>
    <td><?php echo $exportDatas['LSAT_Overall']; ?></td>
    <td><?php echo $exportDatas['UKCAT_Overall']; ?></td>
    <td><?php echo $exportDatas['UKCAT_Verbal_Reasoning']; ?></td>
    <td><?php echo $exportDatas['UKCAT_Quantitative_Reasoning']; ?></td>
    <td><?php echo $exportDatas['UKCAT_Abstract_Reasoning']; ?></td>
    <td><?php echo $exportDatas['UKCAT_Decision_Making']; ?></td>
    <td><?php echo $exportDatas['STEP_Overall']; ?></td>
    <td><?php echo $exportDatas['Category_L3']; ?></td>
                <?php $cnt++; ?>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>