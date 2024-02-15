<form id="frmRequest" onsubmit="return doSubmitForm(event,'stockapproval/doSave','frmRequest')">
  <div class="mt-4 mb-6">
    <div id="lblMdlTitle" class="mb-2 text-lg font-semibold text-gray-700">Permintaan Penambahan Stok</div>
    <div class="text-sm text-gray-700">

      <label class="block mt-4">
        <span>Tanggal</span>
        <input
          id="txtFrmCreatedDate"
          name="txtFrmCreatedDate"
          class="p-2 w-full mt-1 text-md"
          type="text"
          disabled
        >
      </label>

      <label class="block mt-4">
        <span>Pembuat Permintaan</span>
        <input
          id="txtFrmCreatedBy"
          name="txtFrmCreatedBy"
          class="p-2 w-full mt-1 text-md"
          type="text"
          value=""
          disabled
        >
      </label>
      
      <label class="block mt-4">
        <span>Untuk Cabang</span>
          <select
            id="selFrmBranch"
            name="selFrmBranch"
            class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
            disabled>
          </select>
      </label>

      <label class="block mt-4">
        <span>Detail Product</span>
        <input type="hidden" id="hdnFrmLastRowNum" value="1">
				<table 
					id="tblFrmProduct"
					class="table-fixed border p-4 w-full mt-1"
				>
					<tr class="table-fixed border">
						<td width="35px">
							<div class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer" id="btnAddRow" style="display:none">
								<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><g><rect fill="none" height="24" width="24"/></g><g><g><path d="M19,13h-6v6h-2v-6H5v-2h6V5h2v6h6V13z"/></g></g></svg>
							</div>
						</td>
						<td>Produk</td>
						<td>Jumlah</td>
					</tr>
					<tr id="trFrmProduct_1" class="table-fixed border">
						<td width="35px" id="td_1" name="td_1"></td>
						<td id="td_product_1" name="td_product_1">
							<input
								id="product_1"
								name="product[]"
								class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
								type="text"
								readonly
							>
              <input
								id="hdnproduct_1"
								name="hdnproduct[]"
								class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
								type="hidden"
								required
							>
						</td>
						<td>
							<input
								id="qty_1"
								name="qty[]"
								class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
								type="number"
								min="0"
								value="0"
								required
							>
						</td>
					</tr>
				</table>
			</label>

      <label class="block mt-4">
        <span>Status Permintaan</span>
        <select
          id="selFrmOrderStatus"
          name="selFrmOrderStatus"
          type="text"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          disabled
          onchange="doSwitchStatus()"
        >
          <option value="1">Draft</option>
          <option value="2">Menunggu Persetujuan</option>
          <option value="3">Disetujui</option>
          <option value="4">Dikirim</option>
          <option value="5">Diterima</option>
        </select>
      </label>

    </div>
  </div>
  <footer class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50">
    <button
      id="btnFrmDraft"
      type="button"
      onclick="doSaveDraft()"
      class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
    >
      Simpan Draft
    </button>
    <button
      id="btnFrmSentToApprover"
      type="button"
      onclick="doSent()"
      class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
    >
      Simpan dan Setujui
    </button>
    <button
      id="btnFrmPrint"
      type="button"
      onclick="doPrint()"
      class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
    >
      Cetak
    </button>
  </footer>
  <input type="hidden" id="hdnFrmID" name="hdnFrmID" value=""/>
  <input type="hidden" id="hdnIsDraft" name="hdnIsDraft" value="T"/>
  <input type="hidden" id="hdnFrmAction" name="hdnFrmAction" value="add"/>
</form>
<script>
  function fetchBranch(ID='') {
    doFetch('global/getAllBranch','_cb=onCompleteFetchBranch&_p='+ID);
  }
  function onCompleteFetchBranch(data,ID) {
    Swal.close();
    var html = '';
    html += '<option value="">Silahkan Pilih</option>';
    for (i=0;i<data.length;i++) {
      html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
    }
    $('#selFrmBranch').html(html);
    if (ID!='') { $('#selFrmBranch').val(ID); }
    if (data.length == 1) $("#selFrmBranch").prop('selectedIndex', 1);
  }
  function doSaveDraft() {
    $('#hdnIsDraft').val('T');
    $('#frmRequest').submit();
  }
  function doSent() {
    Swal.fire({html:'Pastikan data sudah benar. Lanjutkan setujui Permintaan?', icon:'warning', showCancelButton:true, confirmButtonText: 'Ya', cancelButtonText: 'Tidak'})
    .then((result) => {
      if (result.isConfirmed) {
        $('#hdnIsDraft').val('F');
        $('#frmRequest').submit();
      }
    });
  }
  function doPrint() {
    window.open(apiUrl + "/stockrequest/printOrder?_i=" + $('#hdnFrmID').val() + "&_s=" + getCookie(MSG['cookiePrefix']+'AUTH-TOKEN'));
  }
  function onDetailForm(ID) {
    $('#lblMdlTitle').html('Detail Permintaan');
    $('#hdnFrmID').val(ID);
    $('#hdnFrmAction').val('edit');
    doFetch('stockapproval/get','_i='+ID);
  }
  function onCompleteFetch(data) {
    Swal.close();
    $('#btnFrmDraft').hide();
    $('#btnFrmSentToApprover').hide();
    fetchBranch(data.orderData.BranchID);
    $('#selFrmOrderStatus').val(data.orderData.RequestStatus);
    $('#txtFrmCreatedDate').val(data.orderData.CreatedDate);
    $('#txtFrmCreatedBy').val(data.orderData.CreatedBy);
    if (data.orderData.RequestStatus == 2) {
      $('#btnFrmDraft').show();
      $('#btnFrmSentToApprover').show();
    }
    for (i=0;i<data.orderItem.length;i++) {
      if (i==0) {
        $('#product_1').val(data.orderItem[i].Product);
        $('#hdnproduct_1').val(data.orderItem[i].ProductID);
        $('#qty_1').val(data.orderItem[i].Qty);
        if (data.orderData.RequestStatus != 2) {
          $('#product_1').prop('disabled',true);
          $('#qty_1').prop('disabled',true);
        }
      } else {
        var tblName = document.getElementById('tblFrmProduct');
        var hdncurrRow = document.getElementById('hdnFrmLastRowNum');
        var lastIndex = tblName.children[0].rows.length;
        var tblRow = tblName.insertRow(lastIndex);
        var currRow = 1;
        currRow = parseInt(hdncurrRow.value) + currRow;
        tblRow.id = "trFrmProduct_"+currRow;
        tblRow.className  = "table-fixed border";
        var html1 =
          '<td width="35px">' +
          '&nbsp;</td>';
        var html2 =
          '<td>' +
          '	<input id="product_'+currRow+'" name="product[]" ' + (data.orderData.RequestStatus == 2 ? "readonly" : "disabled") + ' class="listProducts border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" value="'+data.orderItem[i].Product+'" required>' +
          '	<input id="hdnproduct_'+currRow+'" name="hdnproduct[]" class="listProducts border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" type="hidden" value="'+data.orderItem[i].ProductID+'" required>' +
          '</td>';
        var html3 =
          '<td>' +
          '	<input id="qty_'+currRow+'" name="qty[]" ' + (data.orderData.RequestStatus == 2 ? "" : "disabled") + ' class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" type="number" min="0" value="'+data.orderItem[i].Qty+'" required>' +
          '</td>';
        
        var tblCell1 = tblRow.insertCell(0).innerHTML = html1;
        var tblCell2 = tblRow.insertCell(1).innerHTML = html2;
        var tblCell3 = tblRow.insertCell(2).innerHTML = html3;
        hdncurrRow.value = currRow;
      }
    }
  }
</script>