<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <title>AMS | Auction Session</title>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('auction/assets/css/bootstrap.min.css'); ?>">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('auction/assets/css/font-awesome.min.css'); ?>">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('auction/assets/css/style.css'); ?>">
  
</head>
<body class="fixed-left">
    <div id="wrapper">  
        <div class="content-page">
            <div class="content">
                <div class="container-fluid" id="body">
                    <div class="text-center" id="loader"></div>
                    <div class="row" id="content">
                        <div class="col-md-4 col-lg-4">
                            <div class="card">
                                <div class="bid-name">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="side-lot">
                                                <h6 class="sub-title">LOT</h6>
                                                <h4 class="lot-number data-lot" id="item_lot">-</h4>
                                            </div>
                                        </div>
                                        <div class="col-9">
                                            <h6 class="main-title data-lot" id="item_name">Tidak ada data</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="car-grade center-align">
                                                <span class="grade-text">Grade</span> <br>
                                                <span class="grade-alpha data-lot" id="item_grade">-</span>
                                            </div>
                                            <div class="card-img-top" id="image" style="background-image: url(<?php echo base_url('auction/assets/')?>img/ford.jpg);"></div>
                                        </div>
                                    </div>
                                    <div class="border-bottom">
                                        <div class="row margin0 padding12 orange-bg">
                                            <div class="col-6 separator1 center-align">
                                                <h6>Tahun</h6>
                                                <h5 class="bold data-lot" id="item_tahun">-</h5>
                                            </div>
                                            <div class="col-6 center-align">
                                                <h6>Harga Awal</h6>
                                                <h5 class="bold data-lot" id="item_startprice">-</h5>
                                            </div>
                                        </div>
                                        <div class="row">  
                                            <div class="col-md-12">
                                                <div class="rounded-iden center-align">
                                                    Nomor Polisi
                                                    <h6><b class="data-lot" id="item_nopol">-</b></h6>
                                                </div>
                                                <div class="dot-separator">.</div>
                                                <div class="rounded-iden center-align">
                                                    Kilometer
                                                    <h6><b class="data-lot" id="item_km">-</b></h6>
                                                    </div>
                                            </div>
                                            <div class="col-md-12">
                                                    <div class="rounded-iden center-align">
                                                    Warna
                                                    <h6><b class="data-lot" id="item_color">-</b></h6>
                                                    </div>
                                                    <div class="dot-separator">.</div>
                                                    <div class="rounded-iden center-align">
                                                        Transmisi
                                                        <h6><b class="data-lot" id="item_transmisi">-</b></h6>
                                                        </div>
                                                </div>
                                              
                                        </div>
                                    </div>             
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="card">
                                <div class="card-block block-content">
                                    <div class="row margin11">
                                       <!-- <div class="col-md-4">
                                            <center>
                                                <div class="bid-count">
                                                    <h6 class="count-right">COUNT</h6>  
                                                    1
                                                </div> 
                                            </center> 
                                        </div> -->
                                        <div class="col-md-12  padding11 color-right">
                                            <div class="young-purple">
                                                <div class="center-content">
                                                    <img src="<?php echo base_url('auction/assets/img/star.png');?>" class="img-top">&nbsp <span class="top-bidder">TOP BIDDER</span>
                                                </div>
                                                <div class="bid-topbid" id="top_bid"></div>
                                                <div class="pull-right openbold white" id="top_bid_state"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stock-detail padding13">
                                                <div class="row padding10">
                                                    <div class="col-6 col-md-12 item center-align">
                                                        <h6 class="title-caption">Exterior</h6>
                                                        <h5 class="main-caption weight data-lot" id="item_exterior">-</h5>
                                                    </div> 
                                                    <div class="col-6 col-md-12 item center-align">
                                                        <h6 class="title-caption">Interior</h6>
                                                        <h5 class="main-caption weight data-lot" id="item_interior">-</h5>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6 col-md-12 item center-align">
                                                        <h6 class="title-caption">Mechanical</h6>
                                                        <h5 class="main-caption weight data-lot" id="item_mechanical">-</h5>
                                                    </div>
                                                    <div class="col-6 col-md-12 item center-align">
                                                        <h6 class="title-caption">Frame</h6>
                                                        <h5 class="main-caption weight data-lot" id="item_frame">-</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8 padding10 center-align">
                                            <div class="fold-price ">Bidding Log</div>
                                            <div class="bidding-log" style="border: 1px solid #cccccc !important">
                                                    <div class="row line-height" id="bid-log">
                                                    </div>
                                            </div>
                                            
                                        </div>
                                        <div class="col-12 ">
                                            <div class="fold-price center-align" id="harga_kelipatan">
                                                Harga Kelipatan: -
                                            </div>
                                        </div>
                                    </div>                   
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="card">
                                <div class="card-block block-content">
                                    <div class="fold-price center-align">
                                            Control
                                    </div> 
                                        <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group clearfix">
                                                        <label class="control-label">Nomor Lot:</label>
                                                        <input type="text" class="form-control" id="skip">
                                                        <button class="btn btn-warning btn-sm btn-block" id="btn_skip">SKIP</button>
                                                    </div>
                                                    <div class="form-group clearfix">
                                                            <label class="control-label">Bidding Control</label>
                                                            <button class="btn btn-success btn-sm btn-block" style="height:100px;" id="floor-bid" disabled="disabled">+</button>
                                            
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                        <div class="form-group clearfix">
                                                                <button class="btn btn-info btn-sm btn-block" id="start">START</button>
                                                            </div>
                                                            <div class="form-group clearfix">
                                                                    <label class="control-label">Count</label>
                                                                    <input type="text" class="form-control" value="(-)" style="height: 100px; background-color: #a965af; color: white; text-align: center;" id="count" disabled>
                                                                    <button class="btn btn-success btn-sm btn-block" id="btn_count" disabled="disabled">COUNT</button>
                                                                    <button class="btn btn-warning btn-sm btn-block" id="btn_next">NEXT</button>
                                                                </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 center-align">
                                                        <div class="fold-price">
                                                                Detail Lelang
                                                        </div>
                                                            <div class="stock-detail">
                                                                    <div class="row spec">
                                                                        <div class="col-md-6 col-lg-6 item">
                                                                            <h6 class="title-caption2">Tempat</h6>
                                                                            <h5 class="main-caption2 weight" id="schedule_company"></h5>
                                                                        </div>
                                                                        <div class=" col-md-6 col-lg-6 item item2">
                                                                                <h6 class="title-caption2">Tanggal</h6>
                                                                                <h5 class="main-caption2 weight" id="schedule_date"></h5>
                                                                            </div>
                                                                            <div class="col-md-6 col-lg-6 item">
                                                                                    <h6 class="title-caption2">Waktu</h6>
                                                                                    <h5 class="main-caption2 weight" id="schedule_time"></h5>
                                                                            </div>
                                                                            <div class="col-md-6 col-lg-6 item">
                                                                                    <h6 class="title-caption2">Jenis</h6>
                                                                                    <h5 class="main-caption2 weight" id="schedule_type"></h5>
                                                                            </div>
                                                                            <div class="col-md-12 col-lg-12 item">
                                                                                <h6 class="title-caption2">Total LOT</h6>
                                                                                <h5 class="main-caption2 weight" id="lot_total"></h5>
                                                                            </div>
                                                                            </div>
                                                                 </div>
                                                </div>
                                            </div>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $this->load->view($content_script); ?>