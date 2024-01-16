<form id="frmOrder" onsubmit="return doSubmitForm(event,'selling/doSave','frmOrder')">
  <div class="mt-4 mb-6">
    <div id="lblMdlTitle" class="mb-2 text-lg font-semibold text-gray-700">Detail Transaksi</div>
    <div class="text-sm text-gray-700">
      <label class="block mt-4">
        <span>No. Pesanan</span>
        <input
          id="txtFrmInvoiceID"
          name="txtFrmInvoiceID"
          class="p-2 w-full mt-1 text-md"
          type="text"
          value=""
          disabled
        />
      </label>

      <label class="block mt-4">
        <span>Tanggal</span>
        <input
          id="txtFrmTransactionDate"
          name="txtFrmTransactionDate"
          class="p-2 w-full mt-1 text-md"
          value="2"
          disabled
        >
      </label>
      
      <label class="block mt-4">
        <span>Pelanggan</span>
        <input
          id="txtFrmCustomer"
          name="txtFrmCustomer"
          class="p-2 w-full mt-1 text-md"
          value=""
          disabled
        >
      </label>

      <label class="block mt-4">
        <span>Alamat Pengiriman</span>
        <textarea id="txtFrmAddress" class="p-2 w-full mt-1 text-md" disabled rows="5"></textarea>
      </label>

      <label class="block mt-4">
        <span>Detail Pesanan</span>
				<table 
					id="tblFrmOrderItem"
					class="table-fixed border p-4 w-full mt-1"
				>
					<tr class="table-fixed border">
						<td width="50%">Produk</td>
						<td width="10%">Qty</td>
            <td width="40%">Catatan</td>
					</tr>
          <tbody id="tblFrmOrderItemBody"></tbody>
				</table>
      </label>

      <label class="block mt-4">
        <span>Ongkos Kirim</span>
        <input
          id="txtFrmDeliveryFee"
          name="txtFrmDeliveryFee"
          type="text"
          class="p-2 w-full mt-1 text-md"
          value=""
          disabled
        >
      </label>

      <label class="block mt-4">
        <span>Total Pembayaran</span>
        <input
          id="txtFrmTotal"
          name="txtFrmTotal"
          type="text"
          class="p-2 w-full mt-1 text-md"
          value=""
          disabled
        >
      </label>

      <label class="block mt-4">
        <span>Metode Pembayaran</span>
        <input
          id="txtFrmPaymentMethod"
          name="txtFrmPaymentMethod"
          type="text"
          class="p-2 w-full mt-1 text-md"
          value=""
          disabled
        >
      </label>

      <label class="block mt-4">
        <span>Status Pesanan</span>
        <input
          id="txtFrmStatus"
          name="txtFrmStatus"
          type="text"
          class="p-2 w-full mt-1 text-md"
          value=""
          disabled
        >
        <input type="hidden" id="hdnFrmStatus" name="hdnFrmStatus" />
        <!--
        <select
          id="selFrmOrderStatus"
          name="selFrmOrderStatus"
          type="text"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          onchange="doSwitchStatus()"
          required
        >
          <option value="1">Menunggu Pembayaran</option>
          <option value="2">Dibayar</option>
          <option value="3">Dalam Pengiriman</option>
          <option value="4">Selesai</option>
          <option value="5">Batal</option>
        </select>
        -->
      </label>
      <div id="btnAction" style="display:none" class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50">
      <button
          type="button"
          onclick="$('#hdnFrmStatus').val('3'); doSwitchStatus();"
          class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-green-400 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 focus:bg-green-800 hover:bg-green-700 focus:outline-none focus:shadow-outline-green"
        >
          Kirim
        </button>
        <button
          type="button"
          onclick="$('#hdnFrmStatus').val('5'); doSwitchStatus();"
          class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-400 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 focus:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
        >
          Batalkan
        </button>
      </div>
      <div id="btnAction2" style="display:none" class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50">
      <button
          type="button"
          onclick="$('#hdnFrmStatus').val('4'); doSwitchStatus();"
          class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-green-400 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 focus:bg-green-800 hover:bg-green-700 focus:outline-none focus:shadow-outline-green"
        >
          Selesaikan
        </button>
        <button
          type="button"
          onclick="$('#hdnFrmStatus').val('5'); doSwitchStatus();"
          class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-400 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 focus:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
        >
          Batalkan
        </button>
      </div>

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
          maxlength="100"
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
    >
      Simpan
    </button>
    <button
      type="button"
      onclick="doPrint()"
      class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
    >
      Cetak PL
    </button>
    <button
      type="button"
      onclick="doPrintInvoice()"
      class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
    >
      Invoice
    </button>
  </footer>
  <input type="hidden" id="hdnFrmID" name="hdnFrmID" value=""/>
  <input type="hidden" id="hdnFrmAction" name="hdnFrmAction" value="edit"/>
</form>
<script>
  function onDetailForm(ID) {
    doFetch('selling/get','_i='+ID);
  }
  function onCompleteFetch(data) {
    Swal.close();
    $('#hdnFrmID').val(data['orderData'].ID);
    $('#txtFrmInvoiceID').val(data['orderData'].InvoiceID);
    $('#txtFrmTransactionDate').val(data['orderData'].TransactionDate);
    $('#txtFrmCustomer').val(data['orderData'].Customer);
    $('#txtFrmTransactionDate').val(data['orderData'].TransactionDate);
    $('#txtFrmAddress').val(data['orderData'].Customer + "\n" + data['orderData'].Phone + "\n" + data['orderData'].StateName + ", " + data['orderData'].CityName + ", " + data['orderData'].DistrictName + "\n" + data['orderData'].Address + "\n" + data['orderData'].PostalCode);
    $('#txtFrmDeliveryFee').val("Rp. " + doFormatNumber(data['orderData'].DeliveryFee));
    $('#txtFrmTotal').val("Rp. " + doFormatNumber(data['orderData'].Total));
    //$('#selFrmOrderStatus').val(data['orderData'].Status);
    //if (data['orderData'].Status == 5) $('#selFrmOrderStatus').prop('disabled',true);
    var html = '';
    for (let i = 0; i < data['orderItem'].length; i++) {
      html += '<tr class="table-fixed border">' +
						    '<td><input class="p-2 w-full mt-1 text-md" value="' + data['orderItem'][i].Product + '" disabled /></td>' +
                '<td><input class="p-2 w-full mt-1 text-md" value="' + data['orderItem'][i].Qty + '" disabled /></td>' +
                '<td><input class="p-2 w-full mt-1 text-md" value="' + data['orderItem'][i].Notes + '" disabled /></td>' +
					  '</tr>';
    }
    $('#txtFrmCourier').val(data['orderData'].ShippingMethod);
    $('#txtFrmTrackingNumber').val(data['orderData'].TrackingNumber);
    $('#txtFrmCancelledReason').val(data['orderData'].CancelledReason);
    $('#tblFrmOrderItemBody').html(html);
    $('#txtFrmPaymentMethod').val(data['orderData'].PaymentMethod);

    var status = "";
    if (data['orderData'].Status == 1) status = "Menunggu Pembayaran";
    if (data['orderData'].Status == 2) status = "Dibayar";
    if (data['orderData'].Status == 3) status = "Dalam Pengiriman";
    if (data['orderData'].Status == 4) status = "Selesai";
    if (data['orderData'].Status == 5) status = "Batal";
    
    if (data['orderData'].Status == 2) $('#btnAction').show();
    if (data['orderData'].Status == 3) $('#btnAction2').show();
    $('#txtFrmStatus').val(status);
    $('#hdnFrmStatus').val(data['orderData'].Status);
    doSwitchStatus();
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
    //var Status = $('#selFrmOrderStatus').val();
    var Status = $('#hdnFrmStatus').val();
    if (Status == 3) {
      $('#lblFrmCourierWrapper').show(300);
      $('#txtFrmCourier').prop('disabled', false);
      $('#txtFrmCourier').prop('required', true);
      $('#lblFrmTrackingNumberWrapper').show(300);
      $('#txtFrmTrackingNumber').prop('disabled', false);
      $('#txtFrmTrackingNumber').prop('required', true);
      $('#lblFrmCourier').html('Kurir *');
      $('#lblFrmTrackingNumber').html('No Resi *');
    }
    if (Status == 4) {
      $('#lblFrmCourierWrapper').show(300);
      $('#txtFrmCourier').prop('disabled', true);
      $('#txtFrmCourier').prop('required', false);
      $('#lblFrmTrackingNumberWrapper').show(300);
      $('#txtFrmTrackingNumber').prop('disabled', true);
      $('#txtFrmTrackingNumber').prop('required', false);
    }
    if (Status == 5) {
      $('#lblFrmCancelledReasonWrapper').show(300);
      $('#txtFrmCancelledReason').prop('disabled', false);
      $('#txtFrmCancelledReason').prop('required', true);
      $('#lblFrmCancelledReason').html('Alasan Pembatalan *');
    }
  }
  function doPrint() {
    window.open(apiUrl + "/selling/printOrder?_i=" + $('#hdnFrmID').val() + "&_s=" + getCookie(MSG['cookiePrefix']+'AUTH-TOKEN'));
  }
  function doPrintInvoice() {
    window.open(apiUrl + "/invoice?i=" + encodeURIComponent($('#txtFrmInvoiceID').val()));
  }
</script>