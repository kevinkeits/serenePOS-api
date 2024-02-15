<form id="frmDiskon" onsubmit="return doSubmitForm(event,'discount/doSave','frmDiskon')">
  <div class="mt-4 mb-6">
    <div id="lblMdlTitle" class="mb-2 text-lg font-semibold text-gray-700">Tambah Diskon Baru</div>
    <div class="text-sm text-gray-700">
      <p class="text-gray-400 mb-8">Mohon diisi semua field yang wajib bertanda (*) sebelum menyimpan form ini</p>
      <label class="block mt-4">
        <span>Cabang *</span>
        <select
          id="selFrmBranch"
          name="selFrmBranch"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          onChange="changeProduct();"
          required
        />
          <option value="">Silahkan pilih</option>
        </select>
      </label>

      <label class="block mt-4">
        <span>Produk *</span>
        <select
          id="selFrmProductID"
          name="selFrmProductID"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          required
        >
          <option value="">Silahkan pilih</option>
        </select>
      </label>
      
      <label class="block mt-4">
        <span id="lblFrmProvinsi">Dari Tanggal *</span>
        <input
          id="txtFrmStartDate"
          name="txtFrmStartDate"
          type="date"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          required
        >
      </label>

      <label class="block mt-4">
        <span id="lblFrmDistrict">Sampai Tanggal *</span>
        <input
          id="txtFrmEndDate"
          name="txtFrmEndDate"
          type="date"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          required
        >
      </label>

      <label class="block mt-4">
        <span>Tipe Diskon *</span>
        <select
          id="selFrmDiscountType"
          name="selFrmDiscountType"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          required
        >
          <option value="">Silahkan pilih</option>
          <option value="1">Nominal</option>
          <option value="2">Persentase (%)</option>
        </select>
        <input
          id="txtFrmDiscount"
          name="txtFrmDiscount"
          type="number"
          min="0"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
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
  function fetchBranch(ID='') {
    doFetch('global/getAllBranch','_cb=onCompleteFetchBranch&_p='+ID);
  }

  function onCompleteFetchBranch(data,ID) {
    Swal.close();
    var html = '';
    html += '<option value="">Silahkan pilih</option>';
    for (i=0;i<data.length;i++) {
      html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
    }
    $('#selFrmBranch').html(html);
    if (ID!='') { $('#selFrmBranch').val(ID); }
    if (data.length == 1) { $("#selFrmBranch").prop('selectedIndex', 1); changeProduct(); };
  }

  function changeProduct() {
    fetchProduct($('#selFrmBranch').val(),'');
    $('#selFrmProductID').val('');
  }

  function fetchProduct(BranchID,ID='') {
    if (BranchID != '') doFetch('discount/getProduct','_cb=onCompleteFetchProduct&_p='+ID+'&BranchID='+BranchID);
  }

  function onCompleteFetchProduct(data,ID) {
    Swal.close();
    var html = '';
    html += '<option value="">Silahkan pilih</option>';
    for (i=0;i<data.length;i++) {
      html += '<option value="'+data[i].ID+'">['+data[i].Code+'] '+data[i].Name+'</option>';
    }
    $('#selFrmProductID').html(html);
    if (ID!='') { $('#selFrmProductID').val(ID); }
  }

  function onDetailForm(ID) {
    $('#lblMdlTitle').html('Ubah Data Diskon');
    $('#hdnFrmID').val(ID);
    $('#hdnFrmAction').val('edit');
    doFetch('discount/get','_i='+ID);
  }
  
  function onCompleteFetch(data) {
    Swal.close();
    $('#txtFrmStartDate').val(data.StartDate);
    $('#txtFrmEndDate').val(data.EndDate);
    $('#txtFrmDiscount').val(data.Discount);
    fetchBranch(data.BranchID, data.ID);
    fetchProduct(data.BranchID, data.ProductID);
    $('#selFrmDiscountType').val(data.DiscountType);
    $('#radFrmStatus_'+data.Status).prop('checked', true);
  }
</script>