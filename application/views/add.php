<div class="card">
	<h1 class="card-title">Lot Management
		<small class="subtitle"><?php echo $title;?></small>
	</h1>
	<div class="card-body">
    <form id="form_body">
        <div class="col-sm-12 col-md-12">
          <div class="dataTables_schedule" id="schedule-table_schedule">
            <label>Schedule 
              <select class="form-control form-control-sm" id="schedule-filter" aria-controls="schedule-table" data-provide="selectpicker" data-live-search="true">
              
              </select>
            </label>
          </div>
        </div>
        <div class="col-sm-12 col-md-12">
          <div class="dataTables_schedule" id="schedule-table_schedule" style="margin-left: 25px;">
            <label>Type 
              <select class="form-control form-control-sm" id="type-filter" data-provide="selectpicker">
                <option value="">-Select-</option>
                <option value="0">Online</option>
                <option value="1">Live</option>
              </select>
            </label>
          </div>
        </div>

       	<div class="row">
       		<div class="col-md-6">
	       		<div class="card-bordered">
	       			<h5 class="card-title">Stock</h5>
	       			<div class="card-body">
		       			<select name="from[]" id="multiselect" class="form-control" size="8" multiple="multiple">
								</select>
		            </div>
	            </div>
            </div>
            <div class="col-md-6">
	       		<div class="card-bordered">
	       			<h5 class="card-title">Lot</h5>
	       			<div class="card-body">
		       			<select name="multiselect_data[]" id="multiselect_to" class="form-control" size="8" multiple="multiple">
		       			</select>
		            </div>
	            </div>
            </div>
       	</div>
       	<div class="row" style="margin-top: 15px;">
       	<div class="col-md-4"></div>
	       	<div class="col-md-4 text-center">
	       		<button id="multiselect_leftSelected" class="btn btn-square btn-outline btn-info" style="margin-right: 10px;"><i class="fa fa-angle-double-left"></i></button>
	       		<button id="multiselect_rightSelected" class="btn btn-square btn-outline btn-info" style="margin-left: 15px;"><i class="fa fa-angle-double-right"></i></button>
	       	</div>
      		<div class="col-md-4">
      			<button type="submit" id="btn-submit" class="btn btn-success pull-right">Simpan</button>
      			<a href="<?php echo base_url();?>" class="btn btn-default pull-right" style="margin-right: 15px;">Kembali</a>
      		</div>
       	</div>
      </form>
       	

       <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?>
       </p>
    </div>	
	</div>
