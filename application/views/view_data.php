<!DOCTYPE html>
<?php
$cnt = 1;
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Export | Course Intake Details</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="https://siuk-europe.s3.amazonaws.com/static/original_images/latest-favicon.png">
        <style type="text/css">
            .custab{
                border: 1px solid #ccc;
                padding: 5px;
                box-shadow: 3px 3px 2px #ccc;
                transition: 0.5s;
            }
            .custab:hover{
                box-shadow: 3px 3px 0px transparent;
                transition: 0.5s;
            }
        </style>
        
    <link rel="stylesheet" href="/importdata/assets/css/chosen.css">
    <link rel="stylesheet" href="/importdata/assets/css/style.css">
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
            <div class="col-2 my-4"><img src="https://siuk-europe.s3.amazonaws.com/static/original_images/si-global-by-si-uk-logo.webp" width="100%"></div>
                <div class="col text-end"><a href="<?=base_url()?>export/?id=<?=$inst_id?>" class="text-right btn btn-primary mt-5"><span style="font-size:18px;font-weight:bold">+</span> Genrate Excel</a></div>
                <div class="table-responsive">
                    <table class="table table-bordered custab">
                        <thead>
                            <tr>
                            <?php
                            foreach($headerColumn as $key => $column){
                                echo "<th style='padding: 8px; background-color: #f2f2f2; white-space: nowrap;'>" . trim($column) . "</th>";
                            } 
                            ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($exportDatas as $data) { 
                                echo "<tr>";
                                echo "<td>".$data["institution_id"]."</td>";
                                echo "<td>".$data["Course_id"]."</td>";
                                echo "<td style='padding: 8px; word-wrap: break-word; word-break: break-word;'>".$data["Course_Name"]."</td>";
                                echo "<td style='padding: 8px; word-wrap: break-word; word-break: break-word;'>".$data["Course_Intake_ids"]."</td>";
                                echo "<td style='padding: 8px; word-wrap: break-word; word-break: break-word;'>".$data["Course_Start_Date"]."</td>";
                                echo "<td style='padding: 8px; word-wrap: break-word; word-break: break-word;'>".$data["Course_Level_Tuition_Fees"]."</td>";
                                echo "<td style='padding: 8px; word-wrap: break-word; word-break: break-word;' >".$data["Course_Level_application_fees"]."</td>";
                                echo "<td style='padding: 8px; word-wrap: break-word; word-break: break-word;'>".$data["Course_Application_Deadline_Date"]."</td>";
                                echo "<td>".implode(',', json_decode($data["Course_level_Url"], true))."</td>";
                                echo "<td style='padding: 8px; word-wrap: break-word; word-break: break-word;'>".$data["application_status"]."</td>"; 
                                echo "<td>".$data["Show"]."</td>";
                                echo "</tr>";
                            }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- <section class="cst-today">
            <div class="container cnt-sec">
                <div class="row txt">
                    <div class="col-lg-10 col-md-9 col-12 mt-5">
                        <p class="content-full text-left">SI-Global is a trusted international education consultant with over 17 years of experience. We have successfully processed over a million applications and operate in more than 40 countries with 92 offices worldwide.</p>
                    </div>
                    <div class="col-lg-2 col-md-3 col-12 mt-5 mb-5">
                        <img src="https://siuk-europe.s3.amazonaws.com/static/original_images/17-years.webp" alt="SI-Global" class="img-fluid f-logo " loading="lazy">
                    </div>
                </div>
            </div>
        </section> -->
        <div class="footer-bottom ">
            <div class="container">
                <div class="footer-bottom-wrapper"> 
                <div class="footer-bottom-list copyright">
                    <ul>
                    <li><strong>&copy;<script>document.write(new Date().getFullYear());</script>, SI-Global</strong></li>
                    <li>All rights reserved</li>
                    </ul>
                </div>
                </div>
            </div>
        </div>
        <?php /*
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- fordemos -->
        <ins class="adsbygoogle"
            style="display:block"
            data-ad-client="ca-pub-8906663933481361"
            data-ad-slot="9236995934"
            data-ad-format="auto"
            data-full-width-responsive="true"></ins>
        <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        */?>
    </body>
</html>

