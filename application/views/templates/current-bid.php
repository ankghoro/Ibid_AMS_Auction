<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <title>AMS | Current Bidding</title>
  <link href="//fonts.googleapis.com/css?family=Roboto:100,300,400,500,300i" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/current-bidding.css'); ?>">
</head>
<body>
    <section>
        <?php $this->load->view($content); ?>
    </section>
    <?php $this->load->view($content_script); ?>
</body>
</html>