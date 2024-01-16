<form id="frmDeliverycost" onsubmit="return doSubmitForm(event,'deliverycost/doSave','frmDeliverycost')">
	<div class="mt-4 mb-6">
		<div id="lblMdlTitle" class="mb-2 text-lg font-semibold text-gray-700">Tambah Biaya Kirim</div>
		<div class="text-sm text-gray-700">
			<p class="text-gray-400 mb-8">Mohon diisi semua field yang wajib bertanda (*) sebelum menyimpan form ini</p>
			<label class="block mt-4">
				<span>Dari *</span>
				<select
					id="SelFrmStateFrom"
					name="SelFrmStateFrom"
					class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
					onChange="changeCityFrom();"
					required
				>
					<option value="">Silahkan Pilih</option>
				</select>
				<select
					id="SelFrmCityFrom"
					name="SelFrmCityFrom"
					class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
					placeholder="Ketik disini"
					onChange="changeDistrictFrom();"
					required
				>
					<option value="">Silahkan Pilih</option>
				</select>
				<select
					id="SelFrmDistrictFrom"
					name="SelFrmDistrictFrom"
					class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
					placeholder="Ketik disini"
					required
				>
					<option value="">Silahkan Pilih</option>
				</select>
			</label>

			<label class="block mt-4">
				<span>Ke *</span>
				<select
					id="SelFrmStateTo"
					name="SelFrmStateTo"
					class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
					onChange="changeCityTo();"
					required
				>
					<option value="">Silahkan Pilih</option>
				</select>
				<select
					id="SelFrmCityTo"
					name="SelFrmCityTo"
					class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
					onChange="changeDistrictTo();"
					required
				>
					<option value="">Silahkan Pilih</option>
				</select>
				<select
					id="SelFrmDistrictTo"
					name="SelFrmDistrictTo"
					class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
					placeholder="Ketik disini"
					required
				>
					<option value="">Silahkan Pilih</option>
				</select>
			</label>
			
			<label class="block mt-4">
				<span>Biaya *</span>
				<input
					id="txtFrmCost"
					name="txtFrmCost"
					class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
					placeholder="Ketik disini"
					required
				>
			</label>

			<div class="block mt-4">
				<span>Status *</span>
				<div class="mt-2">
					<label class="inline-flex items-center">
						<input
							id="radFrmStatus_1"
							name="radFrmStatus"
							type="radio"
							class="text-red-600 form-radio focus:border-red-400 focus:outline-none focus:shadow-outline-gray"
							value="1"
							checked
						/>
						<span class="ml-2">Aktif</span>
					</label>
					<label class="inline-flex items-center ml-6">
						<input
							id="radFrmStatus_0"
							name="radFrmStatus"
							type="radio"
							class="text-red-600 form-radio focus:border-red-400 focus:outline-none focus:shadow-outline-gray"
							value="0"
						/>
						<span class="ml-2">Tidak Aktif</span>
					</label>
				</div>
			</div>

		</div>
	</div>
	<footer class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50">
		<button
			type="submit"
			class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
		>
			Simpan
		</button>
	</footer>
	<input type="hidden" id="hdnFrmID" name="hdnFrmID" value=""/>
	<input type="hidden" id="hdnFrmAction" name="hdnFrmAction" value="add"/>
</form>
<script>
	function fetchStateFrom(ID='') {
    	doFetch('global/getState','_cb=onCompleteFetchStateFrom&_p='+ID);
	}

	function onCompleteFetchStateFrom(data,ID) {
		Swal.close();
		var html = '';
		html += '<option value="">Silahkan Pilih</option>';
		for (i=0;i<data.length;i++) {
		html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
		}
		$('#SelFrmStateFrom').html(html);
		if (ID!='') {
			$('#SelFrmStateFrom').val(ID);
		}
	}

	function changeCityFrom() {
		fetchCityFrom($('#SelFrmStateFrom').val(),'');
		$('#SelFrmDistrictFrom').val('');
	}

	function fetchCityFrom(StateID,ID='') {
		if (StateID != '') doFetch('global/getCity','_cb=onCompleteFetchCityFrom&_p='+ID+'&stateID='+StateID);
	}

	function onCompleteFetchCityFrom(data,ID) {
		Swal.close();
		var html = '';
		html += '<option value="">Silahkan Pilih</option>';
		for (i=0;i<data.length;i++) {
		html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
		}
		$('#SelFrmCityFrom').html(html);
		if (ID!='') { $('#SelFrmCityFrom').val(ID); }
	}

	function changeDistrictFrom() {
		fetchDistrictFrom($('#SelFrmCityFrom').val(),'')
	}

	function fetchDistrictFrom(CityID,ID='') {
		if (CityID != '') doFetch('global/getDistrict','_cb=onCompleteFetchDistrictFrom&_p='+ID+'&cityID='+CityID);
	}

	function onCompleteFetchDistrictFrom(data,ID) {
		Swal.close();
		var html = '';
		html += '<option value="">Silahkan Pilih</option>';
		for (i=0;i<data.length;i++) {
		html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
		}
		$('#SelFrmDistrictFrom').html(html);
		if (ID!='') { $('#SelFrmDistrictFrom').val(ID); }
	}

	function fetchStateTo(ID='') {
    	doFetch('global/getState','_cb=onCompleteFetchStateTo&_p='+ID);
	}

	function onCompleteFetchStateTo(data,ID) {
		Swal.close();
		var html = '';
		html += '<option value="">Silahkan Pilih</option>';
		for (i=0;i<data.length;i++) {
		html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
		}
		$('#SelFrmStateTo').html(html);
		if (ID!='') {
			$('#SelFrmStateTo').val(ID);
		}
	}

	function changeCityTo() {
		fetchCityTo($('#SelFrmStateTo').val(),'');
		$('#SelFrmDistrictTo').val('');
	}

	function fetchCityTo(StateID,ID='') {
		if (StateID != '') doFetch('global/getCity','_cb=onCompleteFetchCityTo&_p='+ID+'&stateID='+StateID);
	}

	function onCompleteFetchCityTo(data,ID) {
		Swal.close();
		var html = '';
		html += '<option value="">Silahkan Pilih</option>';
		for (i=0;i<data.length;i++) {
		html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
		}
		$('#SelFrmCityTo').html(html);
		if (ID!='') { $('#SelFrmCityTo').val(ID); }
	}

	function changeDistrictTo() {
		fetchDistrictTo($('#SelFrmCityTo').val(),'')
	}

	function fetchDistrictTo(CityID,ID='') {
		if (CityID != '') doFetch('global/getDistrict','_cb=onCompleteFetchDistrictTo&_p='+ID+'&cityID='+CityID);
	}

	function onCompleteFetchDistrictTo(data,ID) {
		Swal.close();
		var html = '';
		html += '<option value="">Silahkan Pilih</option>';
		for (i=0;i<data.length;i++) {
		html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
		}
		$('#SelFrmDistrictTo').html(html);
		if (ID!='') { $('#SelFrmDistrictTo').val(ID); }
	}
	
	function onDetailForm(ID) {
		$('#lblMdlTitle').html('Ubah Biaya Kirim');
		$('#hdnFrmID').val(ID);
		$('#hdnFrmAction').val('edit');
		doFetch('deliverycost/get','_i='+ID);
	}
	function onCompleteFetch(data) {
		Swal.close();
		$('#txtFrmCost').val(data.Fee);
		fetchStateFrom(data.FromStateID);
		fetchCityFrom(data.FromStateID, data.FromCityID);
		fetchDistrictFrom(data.FromCityID, data.FromDistrictID);
		fetchStateTo(data.ToStateID);
		fetchCityTo(data.ToStateID, data.ToCityID);
		fetchDistrictTo(data.ToCityID, data.ToDistrictID);
		$('#radFrmStatus_'+data.Status).prop('checked', true);
	}
</script>