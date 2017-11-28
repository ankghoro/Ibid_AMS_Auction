<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive admin dashboard and web application ui kit. ">
    <meta name="keywords" content="blank, starter">

    <title>LOT &mdash; IBID AMS</title>

    <!-- Fonts -->
    <link href="//fonts.googleapis.com/css?family=Roboto:100,300,400,500,300i" rel="stylesheet">

    <!-- Styles -->
    <link href="<?php echo base_url('assets/css/core.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo  base_url('assets/css/app.min.css')?>" rel="stylesheet">
    <link href="<?php echo  base_url('assets/datatables/dataTables.bootstrap4.min.css')?>" rel="stylesheet">
    <link href="<?php echo  base_url('assets/css/style.css')?>" rel="stylesheet">

    <!-- Favicons -->
    <link rel="apple-touch-icon" href="<?php echo $assets_url; ?>img/apple-touch-icon.png">
    <link rel="icon" href="<?php echo $assets_url; ?>img/favicon.png">
  </head>

  <body>

    <!-- Preloader -->
    <div class="preloader">
      <div class="spinner-dots">
        <span class="dot1"></span>
        <span class="dot2"></span>
        <span class="dot3"></span>
      </div>
    </div>

	<?php $this->load->view('partials/theadmin/sidebar'); ?>
	<?php $this->load->view('partials/theadmin/topbar'); ?>
	
	<!-- Main container -->
		<main>
			<div class="main-content">
				<?php $this->load->view($content); ?>
			</div>

			<?php $this->load->view('partials/theadmin/footer'); ?>
		
		</main>
	<!-- END Main container -->

    <!-- Scripts -->
    <script src="<?php echo base_url('assets/js/core.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/app.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/script.min.js'); ?>"></script>
    <?php $this->load->view($content_script); ?>
