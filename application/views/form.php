<form id="schedule-form" class="form-horizontal">
  <div class="row">
    <div class="col-md-6">
  		<div class="form-group date">
  			<label class="col-sm-3 col-md-3 control-label">Tanggal</label>
  			<div class="col-sm-9 col-md-9">
  				<input type="text" name="date" readonly="true" class="form-control" id="date" value="" data-provide="datepicker">
  			</div>
  		</div>

  		<div class="form-group place_id">
  			<label class="col-sm-3 control-label">Tempat</label>
  			<div class="col-sm-9">
  				<select name="place_id" data-provide="selectpicker" class="form-control" id="place_id">
              <option value="0">Bandung</option>
              <option value="1">Jakarta</option>
          </select>
  			</div>
  		</div>

      <div class="form-group waktu">
        <label class="col-sm-3 col-md-3 control-label">Waktu</label>
        <div class="col-sm-9 col-md-9">
          <input type="text" name="waktu" readonly="true" class="form-control" value="" id='waktu' data-provide="clockpicker" data-placement="top">
        </div>
      </div>

      <div class="form-group" id="input-duration">
        <label class="col-sm-3 col-md-3 control-label">Durasi</label>
        <div class="col-sm-9 col-md-9">
          <input type="hidden" name="duration">
          <div id="duration"></div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group category_id">
        <label class="col-sm-3 control-label">Jenis</label>
        <div class="col-sm-9">
          <select name="category_id" data-provide="selectpicker" class="form-control" id="category_id">
            <option value="1">Mobil</option>
            <option value="0">Motor</option>
          </select>
        </div>
      </div>

      <div class="form-group type">
        <label class="col-sm-3 control-label">Tipe</label>
        <div class="col-sm-9">
          <select name="type" data-provide="selectpicker" class="form-control" id="type">
            <option value="0">Live</option>
            <option value="1">Online</option>
          </select>
        </div>
      </div>

      <div class="form-group limit" id="input-limit">
        <label class="col-sm-3 control-label">Limit</label>
        <div class="col-sm-9">
          <input type="number" name="limit" class="form-control" id="limit">
        </div>
      </div>

      <div class="form-group interval">
        <label class="col-sm-3 control-label">Kelipatan</label>
        <div class="col-sm-9">
          <input type="text" name="interval" class="form-control" id="interval">
        </div>
      </div>
    </div>
  </div>
</form>