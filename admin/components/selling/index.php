
<div class="mb-6">
  <div class="relative text-gray-600 w-50 float-right mt-4 mr-2">
    <input 
      id="txtSearch_tblselling" 
      placeholder="Cari" 
      class="bg-white h-10 px-5 pr-10 rounded-lg text-sm focus:outline-none border"
      onkeyup="doSearchTable()">
    <button type="submit" class="absolute right-0 top-0 mt-3 mr-4">
    <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 56.966 56.966" xml:space="preserve" width="512px" height="512px">
      <path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  s-17-7.626-17-17S14.61,6,23.984,6z"/>
    </svg>
    </button>
  </div>

  <div class="pl-25">
    <span class="text-xs text-gray-500 block">Tanggal Transaksi: </span>
    <input
      id="txtFilterStartDate"
      name="txtFilterStartDate"
      type="date"
      maxlength="50"
      class="border p-2 rounded w-30 text-sm text-gray-500 form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
      placeholder="Type here"
      onchange="doReloadTable()"
    />
    <input
      id="txtFilterEndDate"
      name="txtFilterEndDate"
      type="date"
      maxlength="50"
      class="border p-2 rounded w-30 sm:mr-2 text-sm text-gray-500 form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
      placeholder="Type here"
      onchange="doReloadTable()"
    />
  </div>
  
</div>

<table id="tblselling" class="w-full whitespace-nowrap">
  <thead>
    <tr class="font-semibold tracking-wide text-left text-gray-500 bg-gray-100 uppercase border-b">
      <th class="px-4 py-3">No. Pesanan</th>
      <th class="px-4 py-3">Tanggal</th>
      <th class="px-4 py-3">Pelanggan</th>
      <th class="px-4 py-3">Cabang</th>
      <th class="px-4 py-3">Bayar</th>
      <th class="px-4 py-3">Metode</th>
      <th class="px-4 py-3">Status<br />Pemesanan</th>
      <th class="px-4 py-3">&nbsp;</th>
    </tr>
  </thead>
</table>

<script>
  Pace.restart();

  var now = new Date();
  var day = ("0" + now.getDate()).slice(-2);
  var month = ("0" + (now.getMonth() + 1)).slice(-2);
  var today = now.getFullYear()+"-"+(month)+"-"+(day);
  $('#txtFilterEndDate').val(today);

  lw = new Date();
  lw.setDate(now.getDate()-7);
  day = ("0" + lw.getDate()).slice(-2);
  month = ("0" + (lw.getMonth() + 1)).slice(-2);
  lastweek = lw.getFullYear()+"-"+(month)+"-"+(day);
  $('#txtFilterStartDate').val(lastweek);

  $('#tblselling').DataTable( {
    ajax: {
      url: apiUrl+'/selling/get',
      data: function(d) {
        d.startDate = $('#txtFilterStartDate').val();
        d.endDate = $('#txtFilterEndDate').val();
        d._s = getCookie(MSG['cookiePrefix']+'AUTH-TOKEN');
      },
    },
    "ordering": false,
    columns: [
      { data:'InvoiceID', className:'px-4 py-3 text-sm' },
      { data:'TransactionDate', className:'px-4 py-3 text-sm' },
      { data:'Customer', className:'px-4 py-3 text-sm' },
      { data:'Branch', className:'px-4 py-3 text-sm' },
      {
        data:'Total',
        className:'px-4 py-3 text-sm',
        render: function (data, type, full, meta) {
          var html = doFormatNumber(data);
          return html
        },
      },
      { data:'PaymentMethod', className:'px-4 py-3 text-sm' },
      {
        data:'Status',
        className:'px-4 py-3 text-sm',
        render: function (data, type, full, meta) {
          var status = "";
          if (data == 1) status = "Menunggu Pembayaran";
          if (data == 2) status = "Dibayar";
          if (data == 3) status = "Dalam Pengiriman";
          if (data == 4) status = "Selesai";
          if (data == 5) status = "Batal";
          var html = (data==1 || data==5) ? '<span class="px-2 py-1 font-semibold leading-tight text-white bg-red-400 rounded-full">'+status+'</span>' : '<span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">'+status+'</span>';
          return html
        },
      },
      {
        data:'ID',
        render: function (data, type, full, meta) {
          var html = '<div class="flex item-center justify-center">' +
                        '<div onclick="showDetailForm(\''+full['ID']+'\')" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">' +
                          '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />' +
                          '</svg>' +
                        '</div>' +
                      '</div>';
          return html
        },
      },
    ],
    'dom': '<"w-full overflow-x-auto rounded-lg"t<"grid px-4 py-3 text-xs tracking-wide text-gray-500 border-t bg-gray-50 sm:grid-cols-9"<"flex items-center col-span-3"i><"col-span-2"><"flex col-span-4 mt-2 sm:mt-auto sm:justify-end"<"inline-flex items-center"p>>>>'
  });

  function doReloadTable() {
    Pace.restart();
    $('#tblselling').DataTable().ajax.reload();
    modal.close();
  }

  function doSearchTable() {
    $('#tblselling').DataTable().search( $('#txtSearch_tblselling').val() ).draw();
  }

  function showDetailForm(ID) {
    loadModal('selling/form','onDetailForm(\''+ID+'\')');
  }
</script>