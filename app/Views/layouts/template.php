<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?= $title; ?></title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url() ?>public/assets/img/favicon/favicon.ico" />
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />
    <link 
      href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" 
      rel="stylesheet"/>

    <link rel="stylesheet" href="<?= base_url() ?>public/assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>public/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?= base_url() ?>public/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?= base_url() ?>public/assets/css/demo.css" />

    <!-- Select2 -->
    <link rel="stylesheet" href="<?= base_url() ?>public/assets/vendor/css/select2.css" />
    <link rel="stylesheet" href="<?= base_url() ?>public/assets/vendor/css/bootstrap-select.css" />

    <!-- Vendors CSS -->
    <!-- <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/apex-charts/apex-charts.css" /> -->

    <!-- Datatables -->
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.7/b-3.0.2/b-html5-3.0.2/b-print-3.0.2/r-3.0.2/datatables.min.css" rel="stylesheet">

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="<?= base_url() ?>public/assets/vendor/js/helpers.js"></script>
    <script src="<?= base_url() ?>public/assets/js/config.js"></script>
  </head>
  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <?= $this->include('layouts/navbar'); ?>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Header -->
          <?= $this->include('layouts/header'); ?>
          <!-- / Header -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="col-md-12">
                
                <?= $this->include('layouts/alert'); ?>    
                
                <?= $this->renderSection('content'); ?>
              </div>
            </div>
            <!-- / Content -->

            <!-- Footer -->
            <?= $this->include('layouts/footer'); ?>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>
    </div>
  
    <div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Hapus Data</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Apakah Anda yakin ingin menghapus data ini?</p>
        </div>
        <div class="modal-footer">
          <form action="<?= base_url(@$route.'/onDelete') ?>" method="POST" class="d-inline">
              <?= csrf_field() ?>
              <input type="hidden" name="_method" value="DELETE">
              <input type="hidden" id="formDelete" name="id" value="">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-danger">Hapus</button>
          </form>
        </div>
      </div>
      </div>
    </div>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="<?= base_url() ?>public/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="<?= base_url() ?>public/assets/vendor/libs/popper/popper.js"></script>
    <script src="<?= base_url() ?>public/assets/vendor/js/bootstrap.js"></script>
    <script src="<?= base_url() ?>public/assets/vendor/js/menu.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.7/b-3.0.2/b-html5-3.0.2/b-print-3.0.2/r-3.0.2/datatables.min.js"></script>
    
    <!-- endbuild -->
    
    <!-- Main JS -->
    <script src="<?= base_url() ?>public/assets/js/main.js"></script>
    
    <!-- Additional JS -->
    <!-- <script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script> -->
    <!-- <script src="/assets/js/dashboards-analytics.js"></script>
    <script src="/assets/vendor/libs/apex-charts/apexcharts.js"></script> -->

    <!-- Select2 -->
    <script src="<?= base_url() ?>public/assets/js/select2.js"></script>
    <script src="<?= base_url() ?>public/assets/js/bootstrap-select.js"></script>
    <script src="<?= base_url() ?>public/assets/js/forms-selects.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    
    <!-- JS for preview data -->
    <script>
      function previewImage() {
        const image = document.querySelector('#image');
        const imagePreview = document.querySelector('.img-preview');
        const imageFile = new FileReader();
  
        imageFile.readAsDataURL(image.files[0]);
        imageFile.onload = function(e) {
          imagePreview.src = e.target.result;
        }
      }
    </script>

    <!-- JS for delete data -->
    <script>
      function doDelete(val){
        console.log(val);
        document.getElementById('formDelete').value = val;
      }
    </script>

    <!-- JS for runtime date and time -->
    <script>
      setInterval(generateDate, 1000);

      function generateDate() {
        var dayNames = ["Minggu","Senin","Selasa","Rabu","Kamis","Jum'at","Sabtu"];
        var monthNames = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
        var fullDate = new Date();
        var dayName = dayNames[fullDate.getDay()];
        
        var twoDigitDate = fullDate.getDate();
        var monthName = monthNames[fullDate.getMonth()];
        var currentDate = twoDigitDate + " " + monthName + " " + fullDate.getFullYear();
        
        var twoDigitHours = fullDate.getHours()+"";if(twoDigitHours.length==1) twoDigitHours="0"+twoDigitHours;
        var twoDigitMinutes = fullDate.getMinutes()+"";if(twoDigitMinutes.length==1) twoDigitMinutes="0"+twoDigitMinutes;
        var twoDigitSeconds = fullDate.getSeconds()+"";if(twoDigitSeconds.length==1) twoDigitSeconds="0"+twoDigitSeconds;
        var currentTime = twoDigitHours + ":" + twoDigitMinutes + ":" + twoDigitSeconds;
        document.getElementById("showDate").innerHTML = dayName + ", " + currentDate + " " + currentTime;
      }
    </script>

    <!-- JS for hide flash data when showed -->
    <script>
      <?php if(session()->getFlashData()) : ?>
        setTimeout(function () {
          $('.toast-placement-ex').removeClass('show');
        }, 3000);
      <?php endif; ?>
    </script>
    
    <?= $this->renderSection('script'); ?>

  </body>
</html>