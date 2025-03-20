<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="https://siuk-europe.s3.amazonaws.com/static/original_images/latest-favicon.png">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Source+Serif+Pro:400,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/importdata/assets/css/chosen.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/importdata/assets/css/bootstrap.min.css">
    <!-- Style -->
    <link rel="stylesheet" href="/importdata/assets/css/style.css">
    <title>Course export</title>
  </head>
  <body>
    <div class="content">
      <div class="col-md-2 mx-auto mb-5">
        <img src="https://siuk-europe.s3.amazonaws.com/static/original_images/si-global-by-si-uk-logo.webp" width="100%">
      </div>
      <form action="<?=base_url()?>view-courses/" method="post">
        <div class="container text-left">
          <div class="row justify-content-center mb-4 pt-5 mx-auto">
            <div class="col-md-8 pr-0 pt-2">
              <div class="color-1">
                <select data-placeholder="Choose Institution" multiple class="chosen-select" id="multiple-label-example" name="exportID[]" tabindex="8"> <?php foreach($exportuniversities as $value ):
                                echo '
																			<option value="'.$value['id'].'">'.$value['header_name'].'</option>';
                           endforeach;
						   ?> </select>
              </div>
            </div>
            <div class="col-md-1 pl-0 pt-2">
              <input type="submit" value="Submit" class="sub-mit" id="">
            </div>
          </div>
        </div>
      </form>
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
              <li>
                <strong>&copy; <script>
                    document.write(new Date().getFullYear());
                  </script>, SI-Global </strong>
              </li>
              <li>All rights reserved</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <script src="/importdata/assets/js/jquery-3.3.1.min.js"></script>
    <script src="/importdata/assets/js/popper.min.js"></script>
    <script src="/importdata/assets/js/bootstrap.min.js"></script>
    <script src="/importdata/assets/js/chosen.jquery.min.js"></script>
    <script src="/importdata/assets/js/main.js"></script>
  </body>
</html>