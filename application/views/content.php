<div class="card">
	<h1 class="card-title">Auction Session</h1>

	<div id="body" class="card-body">
	<?php if ($this->session->flashdata('message')) { ?>
		<div class="alert alert-success alert-dismissable alert_content"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close"><i class="fa fa-times"></i></a><strong>Success!</strong> <?php echo $this->session->flashdata('message');?>
		</div>
	<?php } ?>
		
		<div class="row gap-2">
			<div class="col-md-3">
				<div class="card">
					<h4 class="card-title text-center"><u class="data-lot" id="item_name">Mitsubishi Pajero Sport 2,5</u>
						<small class="subtitle data-lot" id="item_lot">LOT 8</small>
					</h4>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card card-outline-secondary">
					<h5 class="card-title">Detail Stock
					</h5>
					<div class="card-body" style="padding: 5px;">
						<div class="row no-gutters">
							<div class="col-md-6 text-center detail_stock">
								<b>Warna</b><br>
								<span class="data-lot" id="item_color">Abu Tua</span>
							</div>
							<div class="col-md-6 text-center detail_stock">
								<b>Transmisi</b><br>
								<span class="data-lot" id="item_transmisi">AT</span>
							</div>
							<div class="col-md-6 text-center detail_stock">
								<b>Kilometer</b><br>
								<span class="data-lot" id="item_km">47,822</span>
							</div>
							<div class="col-md-6 text-center detail_stock">
								<b>Bahan Bakar</b><br>
								<span class="data-lot" id="item_bahanbakar">Bensin</span>
							</div>
							<div class="col-md-6 text-center detail_stock">
								<b>Exterior</b><br>
								<span class="data-lot" id="item_exterior">A</span>
							</div>
							<div class="col-md-6 text-center detail_stock">
								<b>Interior</b><br>
								<span class="data-lot" id="item_interior">A</span>
							</div>
							<div class="col-md-6 text-center detail_stock">
								<b>Mechanical</b><br>
								<span class="data-lot" id="item_mechanical">A</span>
							</div>
							<div class="col-md-6 text-center detail_stock">
								<b>Frame</b><br>
								<span class="data-lot" id="item_frame">A</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">	
				<div class="card card-outline-secondary">
					<h5 class="card-title">Bidding Log
					</h5>
					<div class="card-body no-gutters" style="padding:0;">
						<select name="" id="" class="form-control" size="11" multiple="multiple">
							<option value="">206.000.000 Floor Bid ....</option>
							<option value="">206.000.000 Floor Bid ....</option>
							<option value="">206.000.000 Floor Bid ....</option>
							<option value="">206.000.000 Floor Bid ....</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card card-outline-secondary">
					<h5 class="card-title">Control
					</h5>
					<div class="card-body">
						<div class="row gap-2">
							<div class="col-md-12">
								<button class="btn btn-sm btn-success pull-right">Start</button>
							</div>
							<div class="col-md-6">
								<div class="form-group date">
						  			<label class="control-label">Nomor Lot : </label>
						  			<input type="text" name="lot" class="form-control" id="skip" value="">

						  		</div>
						  		<button class="btn btn-sm btn-default" id="btn_skip">Skip</button>
							</div>
							<div class="col-md-6">
						  		<div class="form-group date">
						  			<label class="control-label text-center">Count : </label>
						  			<input type="text" name="count" class="form-control" id="count" value="0" readonly>
						  		</div>
						  		<button class="btn btn-sm btn-default" id="btn_count">Count</button>
							</div>
							<div class="col-md-6" style="margin-top: 10px;">
								<button class="btn btn-sm btn-default">+500000</button>
							</div>
							<div class="col-md-6" style="margin-top: 10px;">
								<button class="btn btn-sm btn-default" id="btn_next">Next</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
