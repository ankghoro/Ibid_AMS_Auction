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

<div class="modal fade" id="lot_detail2" role="dialog">
  <div class="modal-dialog">
  
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" id="lot-detail-title"></h4>
      </div>
      <div class="modal-body" id="lot-detail-body">
      </div>
      <div class="modal-footer" id="lot-detail-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="submit-delete">Confirm</button>
      </div>
    </div>
    
  </div>
</div>