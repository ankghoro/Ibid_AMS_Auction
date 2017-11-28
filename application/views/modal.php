<div class="modal fade" id="lot_detail" role="dialog">
  <div class="modal-dialog modal-lg">
  
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modal-title"></h4>
      </div>
      <div class="modal-body" id="modal-body">
        <?php $this->load->view('form'); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      	<button type="button" class="btn btn-success btn-submit" id="submit">Submit</button>
      </div>
    </div>
    
  </div>
</div>

<div class="modal fade" id="auction_modal" role="dialog">
  <div class="modal-dialog">
  
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modal-title"></h4>
      </div>
      <div class="modal-body" id="modal-body">
        
      </div>
      <div class="modal-footer" id="lot-detail-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="submit-delete">Yes</button>
      </div>
    </div>
    
  </div>
</div>