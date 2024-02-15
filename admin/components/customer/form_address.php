<form id="frmAddress" onsubmit="return doSubmitForm(event,'customer/doSaveAddress','frmAddress')">
  <div class="mt-4 mb-6">
    <div id="lblMdlTitle" class="mb-2 text-lg font-semibold text-gray-700">Tambah Address Baru</div>
    <div class="text-sm text-gray-700">
      <p class="text-gray-400 mb-8">Mohon diisi semua field yang wajib bertanda (*) sebelum menyimpan form ini</p>
      
        <label class="block mt-4">
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
  </div>
  <footer class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50">
    <button
      type="submit"
      class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
    >
      Simpan
    </button>
    <button
      type="button"
      class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
      onClick="loadModal('customer/form',onDetailForm($('#hdnFrmCustID').val()))"
      >
        Batal
      </button>
  </footer>
  <input type="hidden" id="hdnFrmID" name="hdnFrmID" value=""/>
  <input type="hidden" id="hdnFrmCustID" name="hdnFrmCustID" value=""/>
  <input type="hidden" id="hdnFrmAction" name="hdnFrmAction" value="add"/>
</form>

<script>
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

	function onAddFormAddress(CustomerID) {
		$('#hdnFrmCustID').val(CustomerID);
		fetchState();
	}

	function onEditFormAddress(AddressID) {
		Swal.close();
		doFetch('customer/getAddressDetail','_cb=onCompleteFetchAddress&_addressID='+AddressID);
		$('#lblMdlTitle').html('Ubah Alamat');
		$('#hdnFrmAction').val('edit');
		$('#hdnFrmID').val(AddressID);
	}

	function onCompleteFetchAddress(data) {
    $('#hdnFrmCustID').val(data.CustomerID);
		$('#txtAddressName').val(data.Name);
		$('#txtFrmPhoneDetail').val(data.Phone);
		$('#SelFrmState').val(data.StateID);
		$('#SelFrmCity').val(data.City);
		$('#SelFrmDistrict').val(data.DistrictID);
		$('#txtPostalCode').val(data.PostalCode);
		$('#txtAddressDetail').val(data.Address);
		fetchState(data.StateID);
		fetchCity(data.StateID, data.CityID);
		fetchDistrict(data.CityID, data.DistrictID);
	}
</script>