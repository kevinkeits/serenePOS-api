<form id="frmcustomer" onsubmit="return doSubmitForm(event,'customer/doSave','frmcustomer')">
  <div class="mt-4 mb-6">
    <div id="lblMdlTitle" class="mb-2 text-lg font-semibold text-gray-700">Tambah Pelanggan Baru</div>
    <div class="text-sm text-gray-700">
      <p class="text-gray-400 mb-8">Mohon diisi semua field yang wajib bertanda (*) sebelum menyimpan form ini</p>
      <label class="block mt-4 hidden">
        <span>Kode *</span>
        <input
          id="txtFrmCode"
          name="txtFrmCode"
          type="text"
          maxlength="50"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
        />
      </label>

      <label class="block mt-4">
        <span>Source Data</span><br />
        <span id="txtSourceData" class="mt-1">&nbsp;</span>
      </label>

      <label class="block mt-4">
        <span>Nama *</span>
        <input
          id="txtFrmName"
          name="txtFrmName"
          type="text"
          maxlength="250"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          required
        />
      </label>

      <label class="block mt-4">
        <span>No. Telepon</span>
        <input
          id="txtFrmPhone"
          name="txtFrmPhone"
          type="text"
          maxlength="20"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
        />
      </label>

      <label class="block mt-4">
        <span>Email</span>
        <input
          id="txtFrmEmail"
          name="txtFrmEmail"
          type="email"
          maxlength="250"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
        />
      </label>

      <label class="block mt-4" id="dvFrmPassword">
        <span id="lblFrmPassword">Kata Sandi</span>
        <input
          id="txtFrmPassword"
          name="txtFrmPassword"
          type="password"
          maxlength="250"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          
        />
      </label>

      <label class="block mt-8">
        <span class="text-lg font-semibold">Alamat</span>
      </label>

      <label class="block mt-4" id="fieldAddMore" style="display:none">
        <p class="text-sm font-sm text-red-600 my-1">
          <a href="#" onClick="addMoreAddress($('#hdnFrmID').val())">+ Tambah Alamat Baru</a>
        </p>
        <div id="fieldsetEdit"></div>
      </label>

      <div id="fieldAdd">
        <label class="block mt-2">
          <span>Label Alamat *</span>
          <input
            id="txtAddressName"
            name="txtAddressName"
            type="text"
            maxlength="250"
            class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
            placeholder="Ketik disini"
            required
          />
        </label>

        <label class="block mt-4">
          <span>No. Telepon *</span>
          <input
          id="txtFrmPhoneDetail"
          name="txtFrmPhoneDetail"
          type="text"
          maxlength="20"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          required
          />
        </label>

        <label class="block mt-4">
          <span id="lblFrmProvinsi">Provinsi *</span>
          <select
            id="SelFrmState"
            name="SelFrmState"
            class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
            placeholder="Ketik disini"
            onChange="changeCity();"
            required
          >
            <option value="">Silahkan Pilih</option>
          </select>
        </label>

        <label class="block mt-4">
          <span id="lblFrmDistrict">Kota *</span>
          <select
            id="SelFrmCity"
            name="SelFrmCity"
            type="text"
            class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
            placeholder="Ketik disini"
            onChange="changeDistrict();"
            required
          >
            <option value="">Silahkan Pilih</option>
          </select>
        </label>

        <label class="block mt-4">
          <span id="lblFrmDistrict">Kecamatan *</span>
          <select
            id="SelFrmDistrict"
            name="SelFrmDistrict"
            type="text"
            class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
            placeholder="Ketik disini"
            required
          >
            <option value="">Silahkan Pilih</option>
          </select>
        </label>

        <label class="block mt-4">
          <span>Kode Pos *</span>
          <input
            id="txtPostalCode"
            name="txtPostalCode"
            type="text"
            maxlength="250"
            class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
            placeholder="Ketik disini"
            required
          />
        </label>

        <label class="block mt-4">
          <span>Alamat Lengkap *</span>
          <textarea
            id="txtAddressDetail"
            name="txtAddressDetail"
            class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
            placeholder="Ketik disini"
            required
          /></textarea>
        </label>
      </div>

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
  <input type="hidden" id="hdnIsB2B" name="hdnIsB2B" value="0" />
</form>
<script>
	//function fetchCustomer(ID='') {
	//	doFetch('customer/get','_cb=onCompleteFetchCustomer&_p='+ID);
	//}
	
	//function onCompleteFetchCustomer(data,ID) {
	//	Swal.close();
	//}

	function fetchAddressDetail(ID='') {
		doFetch('customer/getAddress','_cb=onCompleteFetchAddressDetail&_p='+ID);
	}

	function fetchState(ID='') {
		doFetch('global/getState','_cb=onCompleteFetchState&_p='+ID);
	}

	function onCompleteFetchState(data,ID) {
		Swal.close();
		var html = '';
		html += '<option value="">Silahkan Pilih</option>';
		for (i=0;i<data.length;i++) {
		html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
		}
		$('#SelFrmState').html(html);
		if (ID!='') { $('#SelFrmState').val(ID); }
	}

	function changeCity() {
		fetchCity($('#SelFrmState').val(),'');
		$('#SelFrmDistrict').val('');
	}

	function fetchCity(StateID,ID='') {
		if (StateID != '') doFetch('global/getCity','_cb=onCompleteFetchCity&_p='+ID+'&stateID='+StateID);
	}

	function onCompleteFetchCity(data,ID) {
		Swal.close();
		var html = '';
		html += '<option value="">Silahkan Pilih</option>';
		for (i=0;i<data.length;i++) {
		html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
		}
		$('#SelFrmCity').html(html);
		if (ID!='') { $('#SelFrmCity').val(ID); }
	}

	function changeDistrict() {
		fetchDistrict($('#SelFrmCity').val(),'')
	}

	function fetchDistrict(CityID,ID='') {
		if (CityID != '') doFetch('global/getDistrict','_cb=onCompleteFetchDistrict&_p='+ID+'&cityID='+CityID);
	}

	function onCompleteFetchDistrict(data,ID) {
		Swal.close();
		var html = '';
		html += '<option value="">Silahkan Pilih</option>';
		for (i=0;i<data.length;i++) {
		html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
		}
		$('#SelFrmDistrict').html(html);
		if (ID!='') { $('#SelFrmDistrict').val(ID); }
	}

	function onCompleteFetchAddressDetail(data,ID) {
		Swal.close();
		var html = '';
		$('#fieldsetEdit').html(html);
		for (i=0;i<data.length;i++) {

			if(data[i].IsDefault==1) { 
				lblDefault = '<span class="flex w-16 bg-red-400 text-white text-sm px-3 rounded-sm">Utama</span>';
				lblEdit = '<a href="#" onClick="editAddress(\''+data[i].ID+'\')" class="text-xs font-sm text-red-600">Ubah Alamat</a>';
			} else {
				lblDefault = '';
				lblEdit =
				'<a href="#" onClick="editAddress(\''+data[i].ID+'\')" class="text-xs font-sm text-red-600">Ubah Alamat</a>' +
				'<span class="text-gray-200 text-xs font-sm pl-1 pr-1">|</span>' +
				'<a href="#" onClick="changePrimaryAddress(\''+data[i].ID+'\',\''+data[i].Name+'\');" class="text-xs font-sm text-red-600">Jadikan Alamat Utama</a>' +
				'<span class="text-gray-200 text-xs font-sm pl-1 pr-1">|</span>' +
				'<a href="#" onClick="removeAddress(\''+data[i].ID+'\',\''+data[i].Name+'\');" class="text-xs font-sm text-red-600">Hapus</a>';
			};

			html +=
			'<div class="py-2 px-5 bg-white shadow-sm rounded-sm my-3">' +
			'	<div id="cardAddressDetail_'+i+'">' + lblDefault +
			'		<span id="txtAddressName_'+i+'" class="text-gray-800 text-xl font-semibold">'+ data[i].Name +'</span>' +
			'		<p id="txtAddressDetail_"'+i+'>' +
			'			'+ data[i].Address +
			'		</p>' +
			'	</div>' +
			'	<div class="flex justify-end mt-4">' +
			lblEdit +
			'	</div>' +
			'</div>';
		}

		$('#fieldAddMore').show();
		$('#fieldsetEdit').html(html);
	}

	function changePrimaryAddress(ID,Label) {
		Swal.fire({html:'Apakah anda yakin menjadikan <strong>'+Label+'</strong> sebagai alamat utama ?', icon:'warning', showCancelButton:true, confirmButtonText: 'Ya', cancelButtonText: 'Tidak'})
			.then((result) => {
			if (result.isConfirmed) {
				var param = '_addressID='+ID+'&_customerID='+$('#hdnFrmID').val();
				doSubmit('customer/doChangePrimary',param);
			}
		});
	}

	function removeAddress(ID,Label) {
		Swal.fire({html:'Apakah anda yakin akan menghapus <strong>'+Label+'</strong> ?', icon:'warning', showCancelButton:true, confirmButtonText: 'Ya', cancelButtonText: 'Tidak'})
			.then((result) => {
			if (result.isConfirmed) {
				var param = '_addressID='+ID+'&_customerID='+$('#hdnFrmID').val();
				doSubmit('customer/doRemoveAddress',param);
			}
		});
	}

	function addMoreAddress(CustomerID) {
		loadModal('customer/form_address','onAddFormAddress(\''+CustomerID+'\')');
	}

	function editAddress(AddressID) {
		loadModal('customer/form_address','onEditFormAddress(\''+AddressID+'\')');
	}

	function onDetailForm(ID) {
		$('#lblMdlTitle').html('Ubah Data Pelanggan');
		$('#hdnFrmAction').val('edit');
		doFetch('customer/get','_i='+ID);
	}

	function onCompleteFetch(data) {
		Swal.close();
    $('#hdnFrmID').val(data.ID);
		$('#txtFrmCode').val(data.Code);
		$('#txtFrmName').val(data.Name);
		$('#txtFrmPhone').val(data.Phone);
		$('#txtFrmEmail').val(data.Email);
		$('#radFrmStatus_'+data.Status).prop('checked', true);
		fetchAddressDetail(data.ID);
    $('#fieldAdd').hide();
		$('#txtAddressName').attr('required', false);
		$('#SelFrmState').attr('required', false);
		$('#txtFrmPhoneDetail').attr('required', false)
		$('#SelFrmCity').attr('required', false);
		$('#SelFrmDistrict').attr('required', false);
		$('#txtPostalCode').attr('required', false);
		$('#txtAddressDetail').attr('required', false);

    if (data.RegisterFrom == "manual" || data.RegisterFrom == "google") {
      $('#dvFrmPassword').hide();
    }
    var ref = "";
    if (data.RegisterFrom == "app") ref = "Daftar Sendiri";
    if (data.RegisterFrom == "manual") ref = "Admin - B2B";
    if (data.RegisterFrom == "google") ref = "Daftar menggunakan Google";
    $('#txtSourceData').html(ref);
	}
</script>