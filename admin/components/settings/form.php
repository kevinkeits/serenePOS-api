<form id="frmSettings" onsubmit="return doSubmitForm(event,'settings/doSave','frmSettings')" enctype="multipart/form-data">
  <div class="mt-4 mb-6">
    <div id="lblMdlTitle" class="mb-2 text-lg font-semibold text-gray-700">Tambah Referensi Data</div>
    <div class="text-sm text-gray-700">
      <p class="text-gray-400 mb-8">Mohon diisi semua field yang wajib bertanda (*) sebelum menyimpan form ini</p>
      <label class="block mt-4">
        <span>Tipe *</span>
        <input
          id="txtFrmType"
          name="txtFrmType"
          type="text"
          maxlength="50"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          required
        />
      </label>

      <label class="block mt-4">
        <span>Field 1 *</span>
        <input
          id="txtFrmField1"
          name="txtFrmField1"
          type="text"
          maxlength="50"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          required
        />
      </label>

      <label class="block mt-4">
        <span>Field 2 *</span>
        <input
          id="txtFrmField2"
          name="txtFrmField2"
          type="text"
          maxlength="100"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          required
        />
      </label>

      <label class="block mt-4">
        <span>Field 3</span>
        <input
          id="txtFrmField3"
          name="txtFrmField3"
          type="text"
          maxlength="250"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
        />
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
  function onDetailForm(ID) {
    $('#lblMdlTitle').html('Ubah Referensi Data');
    $('#hdnFrmID').val(ID);
    $('#hdnFrmAction').val('edit');
    doFetch('settings/get','_i='+ID);
  }
  function onCompleteFetch(data) {
    Swal.close();
    $('#txtFrmType').val(data.Type);
    $('#txtFrmField1').val(data.Field1);
    $('#txtFrmField2').val(data.Field2);
    $('#txtFrmField3').val(data.Field3);
    $('#radFrmStatus_'+data.Status).prop('checked', true);
    if (data.IsEditable && getCookie(MSG['cookiePrefix']+'GLOBAL-ACCOUNTTYPE')!='1') {
      $('#txtFrmType').prop('readonly',true);
      $('#txtFrmField1').prop('readonly',true);
    }
  }
</script>