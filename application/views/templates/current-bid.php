<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <title>Current Bidding - IBID AMS</title>
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('current/assets/css/bootstrap.min.css')?>">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('current/assets/css/style.css')?>">
</head>
<body class="fixed-left">
    <?php $this->load->view($content); ?>

    <?php isset($content_script) ?  $this->load->view($content_script) : ''; ?>
</body>
</html>