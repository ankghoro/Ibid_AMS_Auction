<div class="card">
	<h1 class="card-title">Lot Management</h1>

	<div id="body" class="card-body">
	<?php if ($this->session->flashdata('message')) { ?>
		<div class="alert alert-success alert-dismissable alert_content"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close"><i class="fa fa-times"></i></a><strong>Success!</strong> <?php echo $this->session->flashdata('message');?>
		</div>
		<?php } ?>
		<table id="schedule-table" class="table table-striped table-bordered text-center" data-tables="true" width="100%" cellspacing="0">
			<thead>
				<tr>
					<th class="text-center"> Lot </th>
					<th class="text-center"> Unit </th>
					<th class="text-center" width="15%"> Action </th>
				</tr>
			</thead>
		</table>
  <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
	</div>
</div>
