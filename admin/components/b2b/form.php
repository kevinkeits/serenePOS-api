<form id="frmB2B" onsubmit="return doSubmitForm(event,'b2b/doSave','frmB2B')">
  <div class="mt-4 mb-6">
    <div id="lblMdlTitle" class="mb-2 text-lg font-semibold text-gray-700">B2B</div>
    <div class="text-sm text-gray-700">

      <label class="block mt-4">
        <span>Pelanggan *</span>
          <!--<input 
            list="selFrmCustomer"  
            id="txtFrmCustomerID"
            name="txtFrmCustomerID"
            class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
            placeholder="Ketik disini"
            onkeyup="doSearchCustomer()"
            required>
          <datalist id="selFrmCustomer">
          </datalist>-->
          <!--
            <select
            id="selFrmCustomer"
            name="selFrmCustomer"
            class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
            required
          />
            <option value="">Silahkan Pilih</option>
          </select>
          -->
          <input type="hidden" name="txtFrmCustomerID" id="txtFrmCustomerID" />
          <input
            id="txtFrmCustomer"
            name="txtFrmCustomer"
            type="text"
            class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
            placeholder="Cari disini"
            onClick="loadModal('b2b/form_lookup','')"
            readonly
            required
          />
      </label>
      <label class="block mt-2" id="addNewCustomerWrapper">
        <p class="text-sm font-sm text-red-600 my-1">
          <a href="#" onClick="loadModal('b2b/form_customer','fetchState();')">+ Tambah Pelanggan</a>
        </p>
        <div id="fieldsetEdit"></div>
      </label>

      <label class="block mt-4">
          <span>Cabang *</span>
          <select
            id="selFrmBranch"
            name="selFrmBranch"
            class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
            onchange="doChangeBranch()"
            required
          />
            <option value="">Silahkan Pilih</option>
          </select>
        </label>

      <label class="block mt-4">
        <span>Pesanan *</span>
        <input type="hidden" id="hdnFrmLastRowNum" value="1">
				<table 
					id="tblFrmProduct"
					class="table-fixed border p-4 w-full mt-1"
				>
					<tr class="table-fixed border">
						<td width="35px">
							<div onclick="addRow()" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer" id="btnAddRow">
								<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><g><rect fill="none" height="24" width="24"/></g><g><g><path d="M19,13h-6v6h-2v-6H5v-2h6V5h2v6h6V13z"/></g></g></svg>
							</div>
						</td>
						<td>Produk</td>
						<td>Jumlah</td>
            <td>Harga Normal</td>
            <td>Discount</td>
            <td>Harga Akhir</td>
					</tr>
					<tr id="trFrmProduct_1" class="table-fixed border">
						<td width="35px" id="td_1" name="td_1"></td>
						<td id="td_product_1" name="td_product_1">
							<select
								id="product_1"
								name="product[]"
								class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
								type="text"
                onchange="fetchProductPrice(1)" 
								required
							>
              <option value="">Silahkan Pilih</option>
              </select>
						</td>
						<td>
							<input
								id="qty_1"
								name="qty[]"
								class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
								type="number"
								min="0"
								value="0"
                onblur="fetchProductPrice(1)" 
								required
							>
						</td>
            <td>
							<input
								id="priceOri_1"
								name="priceOri[]"
								class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
								type="number"
								min="0"
								value="0"
								readonly
							>
						</td>
            <td>
							<input
								id="discount_1"
								name="discount[]"
								class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
								type="number"
								min="0"
								value="0"
								required
                onblur="doCalculateSubTotal()"
							>
						</td>
            <td>
							<input
								id="price_1"
								name="price[]"
								class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
								type="number"
								min="0"
								value="0"
								readonly
							>
						</td>
					</tr>
				</table>
			</label>

      <label class="block mt-4">
        <span>Sub Total *</span>
        <input
          id="txtFrmSubTotal"
          name="txtFrmSubTotal"
          type="number"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          value="0"
          onblur="doCalculateFinal()"
          required
        >
      </label>

      <label class="block mt-4">
        <span>Discount *</span>
        <input
          id="txtFrmDiscount"
          name="txtFrmDiscount"
          type="number"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          value="0"
          onblur="doCalculateFinal()"
          required
        >
      </label>

      <label class="block mt-4">
        <span>Ongkos Kirim *</span>
        <input
          id="txtFrmFee"
          name="txtFrmFee"
          type="number"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          value="0"
          onblur="doCalculateFinal()"
          required
        >
      </label>

      <label class="block mt-4">
        <span>Total Pembayaran *</span>
        <input
          id="txtFrmTotal"
          name="txtFrmTotal"
          type="number"
          class="p-2 w-full mt-1 text-md"
          value="0"
          onblur="doCalculateFinal()"
          readonly
        >
      </label>

      <label class="block mt-4">
        <span>Status Pesanan *</span>
        <select
          id="selFrmOrderStatus"
          name="selFrmOrderStatus"
          type="text"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          required
          onchange="doSwitchStatus()"
        >
          <option value="">Silahkan Pilih</option>
          <option value="1">Menunggu Pembayaran</option>
          <option value="2">Dikonfirmasi</option>
          <option value="3">Dalam Pengiriman</option>
          <option value="4">Selesai</option>
          <option value="5">Batal</option>
        </select>
      </label>

      <label class="block mt-4">
        <span>Status Pembayaran *</span>
        <select
          id="selFrmStatusPayment"
          name="selFrmStatusPayment"
          type="text"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          required
        >
          <option value="">Silahkan Pilih</option>
          <option value="0">Belum Bayar</option>
          <option value="1">Sudah Dibayar</option>
        </select>
      </label>

      <label class="block mt-4" id="lblFrmCourierWrapper">
        <span id="lblFrmCourier">Kurir</span>
        <input
          id="txtFrmCourier"
          name="txtFrmCourier"
          type="text"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          maxlength="50"
          value=""
        >
      </label>
      <label class="block mt-4" id="lblFrmTrackingNumberWrapper">
        <span id="lblFrmTrackingNumber">No Resi</span>
        <input
          id="txtFrmTrackingNumber"
          name="txtFrmTrackingNumber"
          type="text"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          maxlength="50"
          value=""
        >
      </label>
      <label class="block mt-4" id="lblFrmCancelledReasonWrapper">
        <span id="lblFrmCancelledReason">Alasan Pembatalan</span>
        <input
          id="txtFrmCancelledReason"
          name="txtFrmCancelledReason"
          type="text"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          maxlength="250"
          value=""
        >
      </label>

    </div>
  </div>
  <footer class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50">
    <button
      type="submit"
      class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
      id="btnSubmit"
      >
      Simpan
    </button>
    <button
      type="button"
      id="btnPrint"
      style="display:none"
      onclick="doPrint()"
      class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
    >
      Cetak
    </button>
  </footer>
  <input type="hidden" id="hdnFrmID" name="hdnFrmID" value=""/>
  <input type="hidden" id="hdnFrmAction" name="hdnFrmAction" value="add"/>
</form>
<script>
  doSwitchStatus();
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
    if (data.length == 1) { $("#selFrmBranch").prop('selectedIndex', 1); doChangeBranch(); }
  }

  function setCustomerValue(ID, Name) {
    $('#txtFrmCustomerID').val(ID);
    $('#txtFrmCustomer').val(Name);
  }
  /*function doFetchCustomer(keyword='',ID='') {
    doFetch('b2b/getCustomer','_cb=onCompleteFetchCustomer&_p='+ID+'&_search='+keyword);
  }
  */

  /*var timer, valQuery;
  function doSearchCustomer() {
    var str = $('#txtFrmCustomerID').val();
    clearTimeout(timer);
		if (str.length > 2 && valQuery != str) {
			timer = setTimeout(function() {
				valQuery = str;
				doFetchCustomer(str);
			}, 750);
		}
  }*/
  /*function onCompleteFetchCustomer(data,ID) {
    Swal.close();
    var html = '<option value="">Silahkan Pilih</option>';
    for (i=0;i<data.length;i++) {
      html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
    }
    $('#selFrmCustomer').html(html);
    if (ID!='') { $('#selFrmCustomer').val(ID); }
  }*/

  function doChangeBranch() {
    $('#tblFrmProduct > tbody > tr').each(function(index, tr) {
      if (tr.id != "") {
        var id = tr.id.replace("trFrmProduct_","");
        fetchProduct($('#selFrmBranch').val(),'product_'+id);
      }
    });
  }

  function fetchProduct(branchID,page='',ID='') {
    doFetch('b2b/getProduct','_cb=onCompleteFetchProduct&retailID='+branchID+'&_p='+page+'&_i='+ID);
  }
  function onCompleteFetchProduct(data,page,ID) {
    Swal.close();
    var html = '<option value="">Silahkan Pilih</option>';
    for (i=0;i<data.length;i++) {
      html += '<option value="'+data[i].ID+'">' + data[i].Name + '</option>';
    }
    $('#'+page).html(html);
    if (ID!='') { $('#'+page).val(ID); }
  }


  function fetchProductPrice(rowNum) {
    doFetch('b2b/getProduct','_cb=onCompleteFetchProductPrice&retailID='+$('#selFrmBranch').val()+'&_p='+rowNum+'&_i='+$('#product_'+rowNum).val()+'&qty='+$('#qty_'+rowNum).val());
  }
  function onCompleteFetchProductPrice(data,rowNum,ID){
    Swal.close();
    $('#btnSubmit').hide();
    if (data.length > 0) {
      if (parseInt($('#qty_'+rowNum).val()) > data[0].Stock) {
        Swal.fire({title:'Error', text:"Sisa produk untuk " + data[0].Name + " adalah " + data[0].Stock, icon:'error'});
        $('#txtFrmSubTotal').val(0);
        $('#txtFrmTotal').val(0);
      } else {
        var price = parseInt(data[0].Price);
        var qty = parseInt($('#qty_'+rowNum).val());
        var total = price * qty;
        console.log(rowNum, price, qty, total);
        $('#priceOri_'+rowNum).val(price);
        $('#price_'+rowNum).val(total);
        $('#btnSubmit').show();

        doCalculateSubTotal();
      }
    } else {
      Swal.fire({title:'Error', text:"Qty yang diinput diluar matrix harga", icon:'error'});
      $('#txtFrmSubTotal').val(0);
      $('#txtFrmTotal').val(0);
    }
  }

  function doCalculateSubTotal() {
    var subtotal = 0;
    $('#tblFrmProduct > tbody > tr').each(function(index, tr) {
      if (tr.id != "") {
        var id = tr.id.replace("trFrmProduct_","");

        var price = parseInt($('#priceOri_'+id).val());
        var qty = parseInt($('#qty_'+id).val());
        var discount = parseInt($('#discount_'+id).val());
        $('#price_'+id).val((price * qty) - discount);
        subtotal += parseInt($('#price_'+id).val());
      }
      $('#txtFrmSubTotal').val(subtotal);
      doCalculateFinal();
    });
  }
  function doCalculateFinal() {
    var subtotal = parseInt($('#txtFrmSubTotal').val());
    var discount = parseInt($('#txtFrmDiscount').val());
    var ongkir = parseInt($('#txtFrmFee').val());
    $('#txtFrmTotal').val((subtotal - discount) + ongkir);
  }

  function onDetailForm(ID) {
    $('#lblMdlTitle').html('Detail Transaksi');
    $('#hdnFrmID').val(ID);
    $('#hdnFrmAction').val('edit');
    doFetch('b2b/get','_i='+ID);
  }
  function onCompleteFetch(data) {
    Swal.close();
    $('#btnPrint').show();
    fetchBranch(data.orderData.BranchID);
    //doFetchCustomer('',data.orderData.CustomerID);
    $('#txtFrmSubTotal').val(data.orderData.SubTotal);
    $('#txtFrmFee').val(data.orderData.DeliveryFee);
    $('#txtFrmTotal').val(data.orderData.Total);
    $('#selFrmOrderStatus').val(data.orderData.Status);
    $('#selFrmStatusPayment').val(data.orderData.IsPaid);
    if (data.orderData.Status == 5) $('#selFrmOrderStatus').prop('disabled', true);
    if (data.orderData.Status == 5) $('#selFrmStatusPayment').prop('disabled', true);
    doSwitchStatus();
    $('#txtFrmCourier').val(data.orderData.ShippingMethod);
    $('#txtFrmTrackingNumber').val(data.orderData.TrackingNumber);
    $('#txtFrmCancelledReason').val(data.orderData.CancelledReason);
    $("#selFrmBranch").prop('disabled', true);
    //$("#selFrmCustomer").prop('disabled', true);
    $("#txtFrmCustomerID").val(data.orderData.CustomerID);
    $("#txtFrmCustomer").val(data.orderData.Customer);
    $("#txtFrmCustomer").prop("onclick", null).off("click");
    $('#addNewCustomerWrapper').hide();
    $("#txtFrmSubTotal").prop('disabled', true);
    $("#txtFrmFee").prop('disabled', true);
    $("#txtFrmTotal").prop('disabled', true);
    $("#txtFrmDiscount").prop('disabled', true);

    $('#btnAddRow').hide();
    for (i=0;i<data.orderItem.length;i++) {
      if (i==0) {
        fetchProduct(data.orderData.BranchID,'product_1',data.orderItem[i].ProductID);
        $('#qty_1').val(data.orderItem[i].Qty);
        $('#priceOri_1').val(data.orderItem[i].SourcePrice);
        $('#discount_1').val(data.orderItem[i].DiscountPrice);
        $('#price_1').val(data.orderItem[i].ItemPrice);
        $('#product_1').prop('disabled',true);
        $('#qty_1').prop('disabled',true);
        $('#priceOri_1').prop('disabled',true);
        $('#discount_1').prop('disabled',true);
        $('#price_1').prop('disabled',true);
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
          //'	<div onclick="removeRow('+currRow+')" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">' +
          //'		<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 13H5v-2h14v2z"/></svg>' +
          //'	</div>' +
          '&nbsp;</td>';
        var html2 =
          '<td>' +
          '	<select id="product_'+currRow+'" name="product[]" onchange="fetchProductPrice('+currRow+')" disabled class="listProducts border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" required><option value="">Silahkan Pilih</option></select>' +
          '</td>';
        var html3 =
          '<td>' +
          '	<input id="qty_'+currRow+'" name="qty[]"  onblur="fetchProductPrice('+currRow+')" disabled class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" type="number" min="0" value="'+data.orderItem[i].Qty+'" required>' +
          '</td>';
        var html4 =
          '<td>' +
          ' <input id="priceOri_'+currRow+'" disabled name="priceOri[]" class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" type="number" min="0" value="'+parseInt(data.orderItem[i].SourcePrice)+'" readonly />' +
          '</td>';
        var html5 =
          '<td>' +
          ' <input id="discount_'+currRow+'" disabled name="discount[]" class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" type="number" min="0" value="'+parseInt(data.orderItem[i].DiscountPrice)+'" readonly />' +
          '</td>';
        var html6 =
          '<td>' +
          ' <input id="price_'+currRow+'" disabled name="price[]" class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" type="number" min="0" value="'+parseInt(data.orderItem[i].ItemPrice)+'" readonly />' +
          '</td>';
        var tblCell1 = tblRow.insertCell(0).innerHTML = html1;
        var tblCell2 = tblRow.insertCell(1).innerHTML = html2;
        var tblCell3 = tblRow.insertCell(2).innerHTML = html3;
        var tblCell4 = tblRow.insertCell(3).innerHTML = html4;
        var tblCell5 = tblRow.insertCell(4).innerHTML = html5;
        var tblCell6 = tblRow.insertCell(5).innerHTML = html6;
        fetchProduct(data.orderData.BranchID,'product_'+currRow,data.orderItem[i].ProductID);
        hdncurrRow.value = currRow;
      }
    }
  }

  function addRow() {
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
			'	<div onclick="removeRow('+currRow+')" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">' +
			'		<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 13H5v-2h14v2z"/></svg>' +
			'	</div>' +
			'</td>';
		var html2 =
			'<td>' +
			'	<select id="product_'+currRow+'" name="product[]" onchange="fetchProductPrice('+currRow+')" class="listProducts border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" required><option value="">Silahkan Pilih</option></select>' +
			'</td>';
		var html3 =
			'<td>' +
			'	<input id="qty_'+currRow+'" name="qty[]"  onblur="fetchProductPrice('+currRow+')" class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" type="number" min="0" value="0" required>' +
      '</td>';
    var html4 =
			'<td>' +
			' <input id="priceOri_'+currRow+'" name="priceOri[]" class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" type="number" min="0" value="0" readonly />' +
      '</td>';
    var html5 =
			'<td>' +
			' <input id="discount_'+currRow+'" name="discount[]" class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" type="number" min="0" value="0" required onblur="doCalculateSubTotal()" />' +
      '</td>';
    var html6 =
			'<td>' +
			' <input id="price_'+currRow+'" name="price[]" class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" type="number" min="0" value="0" readonly />' +
      '</td>';
		var tblCell1 = tblRow.insertCell(0).innerHTML = html1;
		var tblCell2 = tblRow.insertCell(1).innerHTML = html2;
		var tblCell3 = tblRow.insertCell(2).innerHTML = html3;
    var tblCell4 = tblRow.insertCell(3).innerHTML = html4;
    var tblCell5 = tblRow.insertCell(4).innerHTML = html5;
    var tblCell6 = tblRow.insertCell(5).innerHTML = html6;
    fetchProduct($('#selFrmBranch').val(),'product_'+currRow);
		hdncurrRow.value = currRow;
	}

	function removeRow(Index) {
		$('#trFrmProduct_' + Index).remove();
	}

  function doSwitchStatus() {
    $('#lblFrmCourierWrapper').hide();
    $('#txtFrmCourier').prop('disabled', true);
    $('#txtFrmCourier').prop('required', false);
    $('#lblFrmCourier').html('Kurir');
    $('#lblFrmTrackingNumberWrapper').hide();
    $('#txtFrmTrackingNumber').prop('disabled', true);
    $('#txtFrmTrackingNumber').prop('required', false);
    $('#lblFrmTrackingNumber').html('No Resi');
    $('#lblFrmCancelledReasonWrapper').hide();
    $('#txtFrmCancelledReason').prop('disabled', true);
    $('#txtFrmCancelledReason').prop('required', false);
    $('#lblFrmCancelledReason').html('Alasan Pembatalan');
    var Status = $('#selFrmOrderStatus').val();
    if (Status == 3) {
      $('#lblFrmCourierWrapper').show(300);
      $('#txtFrmCourier').prop('disabled', false);
      $('#txtFrmCourier').prop('required', true);
      $('#lblFrmTrackingNumberWrapper').show(300);
      $('#txtFrmTrackingNumber').prop('disabled', false);
      $('#txtFrmTrackingNumber').prop('required', true);
      $('#lblFrmCourier').html('Kurir *');
      $('#lblFrmTrackingNumber').html('No Resi *');
    } else if (Status == 4 || Status == 5) {
      $('#lblFrmCourierWrapper').show(300);
      $('#txtFrmCourier').prop('disabled', true);
      $('#txtFrmCourier').prop('required', false);
      $('#lblFrmTrackingNumberWrapper').show(300);
      $('#txtFrmTrackingNumber').prop('disabled', true);
      $('#txtFrmTrackingNumber').prop('required', false);
      if (Status == 5) {
        $('#lblFrmCancelledReasonWrapper').show();
        $('#txtFrmCancelledReason').prop('disabled', false);
        $('#txtFrmCancelledReason').prop('required', true);
        $('#lblFrmCancelledReason').html('Alasan Pembatalan *');
      }
    }
  }
  function doPrint() {
    window.open(apiUrl + "/b2b/printOrder?_i=" + $('#hdnFrmID').val() + "&_s=" + getCookie(MSG['cookiePrefix']+'AUTH-TOKEN'));
  }
</script>