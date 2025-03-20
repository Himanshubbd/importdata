<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Upload Course Level Three Tag</title>
      <link rel="icon" href="https://siuk-europe.s3.amazonaws.com/static/original_images/latest-favicon.png">
      <!-- Bootstrap 5 CDN -->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="/importdata/assets/css/chosen.css">
      <link rel="stylesheet" href="/importdata/assets/css/style.css">
   </head>
   <body>
        <div class="container-fluid my-5">
            <div class="col-2 mx-auto"><img src="https://siuk-europe.s3.amazonaws.com/static/original_images/si-global-by-si-uk-logo.webp" width="100%"></div>
            <!-- File Upload Form -->
            <form action="<?php echo base_url('process-course-data'); ?>" method="post" enctype="multipart/form-data" class="m_height">
            <div class="row">
                <div class="d-flex justify-content-center mt-5">
                    <div class="col-8 mb-3 color-1">
                        <input type="file" name="file" id="file" class="form-control" style="line-height:34px" required>
                    </div>
                    <div class="col-1">
                        <button type="submit" class="sub-mit">Upload</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
        <!-- <section class="cst-today">
            <div class="container cnt-sec">
                <div class="row txt">
                <div class="col-lg-10 col-md-9 col-12 mt-5">
                    <p class="content-full text-left">SI-Global is a trusted international education consultant with over 17 years of experience. We have successfully processed over a million applications and operate in more than 40 countries with 92 offices worldwide.</p>
                </div>
                <div class="col-lg-2 col-md-3 col-12 mt-5 mb-5"><img src="https://siuk-europe.s3.amazonaws.com/static/original_images/17-years.webp" alt="SI-Global" class="img-fluid f-logo " loading="lazy"></div>
                </div>
            </div>
        </section> -->
      <!-- Modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" style="display:none" id="clickBtn">
            Click
        </button>
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="word-wrap: break-word;">
                    <!-- Error Message -->
                    <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                    <?php endif; ?>
                    <!-- Success Message -->
                    <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success">
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom ">
            <div class="container">
            <div class="footer-bottom-wrapper">
                <div class="footer-bottom-list copyright">
                    <ul>
                        <li>
                        <strong>
                            &copy;<script>document.write(new Date().getFullYear());</script>, SI-Global
                        </strong>
                        </li>
                        <li>All rights reserved</li>
                    </ul>
                </div>
            </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <?php 
        if ($this->session->flashdata('error') || $this->session->flashdata('success')) {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    document.querySelector("#clickBtn").click();
                });
            </script>';
        }
        ?>
   </body>
</html>