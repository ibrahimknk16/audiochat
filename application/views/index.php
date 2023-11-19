<!doctype html>
<html lang="tr">

<head>
    <meta charset="utf-8" />
    <title>CNS Chat App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="CNS Chat App" name="description" />
    <meta content="CNS Games" name="author" />

    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/images/favicon.ico">
    <link href="<?php echo base_url(); ?>assets/libs/magnific-popup/magnific-popup.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/libs/owl.carousel/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/libs/owl.carousel/assets/owl.theme.default.min.css">

    <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

</head>

<body>

    <div class="layout-wrapper d-lg-flex">

        <?php $this->load->view('layouts/sidebar') ?>
        <?php $this->load->view('layouts/rooms') ?>
        <?php $this->load->view('layouts/chat') ?>

        <script src="<?php echo base_url(); ?>assets/libs/jquery/jquery.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/libs/simplebar/simplebar.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/libs/node-waves/waves.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/libs/magnific-popup/jquery.magnific-popup.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/libs/owl.carousel/owl.carousel.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/pages/index.init.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/app.js"></script>

</body>

</html>