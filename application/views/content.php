<div id="wrapper">  
    <div class="content-page">
        <div class="content">
            <div class="container" id="body">
                <div class="text-center" id="loader">
    
                </div>
                <div class="row gap-2" id="content">
                    <div class="col-md-6 col-lg-3">
                        <div class="card">
	                        <div class="card-header bid-name">
	                        	<div class="row">
	                        		<div class="col-md-12">
	                            <h6 class="main-title data-lot" id="item_name">Tidak ada data</h6>
	                        </div>
	                        <div class="col-md-12">
	                            <h6 class="top-border sub-title data-lot" id="item_lot">Tidak ada data</h6>
	                        </div>
	                            </div>
	                        </div>
                        	<div class="card-block">
                            	<img class="card-img-top" src="assets/img/ford.jpg" id="image" style="max-width: 100%;">
                            	<div class="row">
                                	<div class=" col-sm-4 col-lg-4 center-align">
                                		<div class="car-grade">
                                        <span class="grade-text">Grade</span> <br>
                                        <span class="grade-alpha data-lot" id="item_grade">-</span>
                                    </div>
                                    </div>
	                            	<div class=" col-sm-4 col-lg-8 center-align">
	                            	<h6 class="first-price">Harga Awal</h6>
	                            	<h5 class="data-lot" id="item_startprice" style="font-weight: bold; color: #777777;">-</h5>
	                            </div>
	                        </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card">
                            <div class="card-block block-content center-align">
                                <div class="fold-price ">
                                    Detail Stock
                                </div>
                                <div class="stock-detail">
                                    <div class="row spec">
                                        <div class="col-md-6 col-lg-6 item item1">
                                            <h6 class="title-caption">Warna</h6>
                                            <h5 class="main-caption weight data-lot" id="item_color">-</h5>
                                        </div>
                                        <div class=" col-md-6 col-lg-6 item item2">
                                            <h6 class="title-caption">Transmisi</h6>
                                            <h5 class="main-caption weight data-lot" id="item_transmisi">-</h5>
                                        </div>
                                    	<div class="col-md-6 col-lg-6 item item3 item2">
                                            <h6 class="title-caption">Kilometer</h6>
                                            <h5 class="main-caption weight data-lot" id="item_km">-</h5>
                                        </div>
                                        <div class="col-md-6 col-lg-6 item item2">
                                            <h6 class="title-caption">Bahan Bakar</h6>
                                            <h5 class="main-caption weight data-lot" id="item_bahanbakar">-</h5>
                                        </div>
                                        <div class="col-md-6 col-lg-6 item item1">
                                            <h6 class="title-caption">Exterior</h6>
                                            <h5 class="main-caption weight data-lot" id="item_exterior">-</h5>
                                        </div>
                                        <div class=" col-md-6 col-lg-6 item item2">
                                            <h6 class="title-caption">Interior</h6>
                                            <h5 class="main-caption weight data-lot" id="item_interior">-</h5>
                                        </div>
                                        <div class="col-md-6 col-lg-6 item item3">
                                            <h6 class="title-caption">Mechanical</h6>
                                            <h5 class="main-caption weight data-lot" id="item_mechanical">-</h5>
                                        </div>
                                        <div class="col-md-6 col-lg-6 item">
                                            <h6 class="title-caption">Frame</h6>
                                            <h5 class="main-caption weight data-lot" id="item_frame">-</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            </div>
                                <div class="col-md-6 col-lg-3 center-align">
                                    <div class="card">
                                        <div class="card-block block-content">
                                            <div class="fold-price">
                                                Bidding Log
                                             </div>
                                        <div class="bidding-log" style="border: 1px solid #cccccc !important">
                                            <div class="row no-gutters line-height" id="bid-log">
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="card">
                                         <div class="card-block block-content center-align">
                                            <div class="fold-price">
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
                                                            <button class="btn btn-success btn-sm btn-block" style="height:100px;" id="floor-bid" disabled="disabled">+
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group clearfix">
                                                            <button class="btn btn-info btn-sm btn-block" id="start">START</button>
                                                        </div>
	                                                    <div class="form-group clearfix">
	                                                        <label class="control-label">Count</label>
	                                                        <input type="text" class="form-control" style="height: 100px;" id="count" value="0" readonly>
	                                                        <button class="btn btn-success btn-sm btn-block" id="btn_count" disabled="disabled">COUNT</button>
	                                                        <button class="btn btn-warning btn-sm btn-block" id="btn_next">NEXT</button>
	                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <a target="_blank" href="<?php echo base_url('current/bidding');?>" class="btn btn-primary btn-sm btn-block">Current Bidding</a>
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