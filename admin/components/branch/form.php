<form id="frmBranch" onsubmit="return doSubmitForm(event,'branch/doSave','frmBranch')">
  <div class="mt-4 mb-6">
    <div id="lblMdlTitle" class="mb-2 text-lg font-semibold text-gray-700">Tambah Cabang Baru</div>
    <div class="text-sm text-gray-700">
      <p class="text-gray-400 mb-8">Mohon diisi semua field yang wajib bertanda (*) sebelum menyimpan form ini</p>
      <label class="block mt-4">
        <span>Nama Cabang *</span>
        <input
          id="txtFrmName"
          name="txtFrmName"
          type="text"
          maxlength="100"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          required
        />
      </label>

      <label class="block mt-4">
        <span>Alamat *</span>
        <textarea
          id="txtFrmAlamat"
          name="txtFrmAlamat"
          maxlength="250"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          required
        ></textarea>
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
        <span id="lblFrmPassword">No. Telepon</span>
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
        <span>No. Whatsapp</span>
        <input
          id="txtFrmWWA"
          name="txtFrmWWA"
          type="text"
          maxlength="20"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
        />
      </label>

      <label class="block mt-4">
        <span>Link Facebook</span>
        <input
          id="txtFrmFB"
          name="txtFrmFB"
          type="text"
          maxlength="250"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
        />
      </label>

      <label class="block mt-4">
        <span>Link Instagram</span>
        <input
          id="txtFrmIG"
          name="txtFrmIG"
          type="text"
          maxlength="250"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
        />
      </label>

      <label class="block mt-4">
        <span>Admin Lokasi</span>
        <select
          id="selFrmAdminBranch"
          name="selFrmAdminBranch[]"
          multiple="multiple"
          size="10"
        >
        </select>
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
  

  function fetchAllUser(ID=null) {
    doFetch('global/getAllUser','_cb=onCompleteFetchAllUser&_p='+encodeURI(JSON.stringify(ID)));
  }

  function onCompleteFetchAllUser(data,ID) {
    var rawAdmin = JSON.parse(decodeURI(ID));    
    Swal.close();
    var html = '';
    for (i=0;i<data.length;i++) {
      if (rawAdmin!=null) {
        html += '<option value="'+data[i].ID+'" '+(rawAdmin.indexOf(data[i].ID)>=0 ? 'selected' : '')+'>'+data[i].Name+'</option>';
      }
      else
      {
        html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
      }
    }
    $('#selFrmAdminBranch').html(html);
    $('#selFrmAdminBranch').DualListbox({
      infoText: ''
    });
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
  
  function onDetailForm(ID) {
    $('#lblMdlTitle').html('Ubah Cabang');
    $('#hdnFrmID').val(ID);
    $('#hdnFrmAction').val('edit');
    doFetch('branch/get','_i='+ID);
  }
  function onCompleteFetch(data) {
    Swal.close();
    $('#txtFrmName').val(data.header.Name);
    $('#txtFrmAlamat').val(data.header.Address);
    $('#txtFrmPhone').val(data.header.Phone);
    $('#txtFrmWA').val(data.header.WA);
    $('#txtFrmFB').val(data.header.FB);
    $('#txtFrmIG').val(data.header.IG);
    fetchState(data.header.StateID);
    fetchCity(data.header.StateID, data.header.CityID);
    fetchDistrict(data.header.CityID, data.header.DistrictID);
    fetchAllUser(data.selBranch);
    $('#radFrmStatus_'+data.header.Status).prop('checked', true);
  }
</script>