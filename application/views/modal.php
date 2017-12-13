<div class="modal fade" id="modal" role="dialog">
  <div class="modal-dialog">
  
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header background-alert" id="modal-header">
        <h3 class="modal-title" id="modal-title" style="color: #f7f7f7;"></h3>
      </div>
      <div class="modal-body" id="modal-body">
      </div>
      <div class="modal-footer" id="modal-footer">
      	<button type="button" class="btn btn-outline btn-primary" id="proceed-winner">Lanjutkan</button>
        <button type="button" class="btn btn-outline btn-secondary" data-dismiss="modal" id="close">Close</button>
      </div>
    </div>
    
  </div>
</div>

<div class="modal fade" id="auction_modal" role="dialog">
  <div class="modal-dialog">
  
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header background-alert"  id="modal-auction-header">
        <h3 class="modal-title" id="modal-auction-title" style="color: #f7f7f7;"></h3>
      </div>
      <div class="modal-body" id="modal-auction-body">
        
      </div>
      <div class="modal-footer" id="modal-auction-footer">
        <button type="button" class="btn btn-outline btn-secondary" data-dismiss="modal">Tidak</button>
        <button type="button" class="btn btn-success" id="confirm-start">Ya</button>
        <button type="button" class="btn btn-success" id="confirm-skip">Simpan</button>
        <button type="button" class="btn btn-success" id="confirm-next">Yes</button>
      </div>
    </div>
    
  </div>
</div>

<div class="modal fade" id="logout-modal" role="dialog">
  <div class="modal-dialog modal-md">
  
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Confirm Logout</h3>
      </div>
      <div class="modal-body" id="logout-modal-body">
      Are you sure to logout ?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <a id="submit-logout" class="btn btn-success" href="<?php echo $this->config->item('ibid_auth').'/logout';?>">Confirm</a>
      </div>
    </div>
    
  </div>
</div>