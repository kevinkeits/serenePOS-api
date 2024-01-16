<form id="frmB2B" onsubmit="return doSubmitForm(event,'b2b/doSearchCustomer','frmB2B')">
  <div class="mt-4 mb-6">
    <div id="lblMdlTitle" class="mb-2 text-lg font-semibold text-gray-700">Cari Pelanggan</div>
    <div class="text-sm text-gray-700">
      <span>Nama Pelanggan </span>
      <input
        id="txtFrmCustomer"
        name="qName"
        type="text"
        class="border p-2 rounded w-full mt-1 mb-4 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
        placeholder="Ketik disini"
      />
      <span>No. Telepon </span>
      <input
        id="txtFrmPhone"
        name="qPhone"
        type="text"
        class="border p-2 rounded w-full mt-1 mb-4 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
        placeholder="Ketik disini"
      />
      <button
        type="submit"
        class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
        id="btnSubmit"
        >
        Cari
      </button>
      <br /><br />
      <div id="wraperCustomerSearchResult" style="display:none">
        <hr />
        <table 
          id="tblFrmCustomer"
          class="table-fixed border p-4 w-full mt-4"
        >
          <tr class="table-fixed border">
            <td class="p-2" style="width:50%">Pelanggan</td>
            <td class="p-2" style="width:30%">No. Telp</td>
            <td class="p-2" style="width:20%">Action</td>
          </tr>
          <tbody id="tblFrmCustomer_Body"></tbody>
        </table>
      </div>
      
    </div>
  </div>
  
  <footer class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50">
  <button
      type="button"
      class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
      onclick="setBackValue('','')"
    >
      Kembali
    </button>
  </footer>
</form>
<script>
  function setBackValue(ID, Name) {
    loadModal("b2b/form","fetchBranch();setCustomerValue('" +ID+ "','" +Name+ "')");
  }
  function onCompleteFetchCustomer(data) {
    Swal.close();
    var html = '';
    if (data.length > 0) {
      for (i=0;i<data.length;i++) {
        html += '<tr>';
        html += '<td class="p-2">' + data[i].Name + '</td>';
        html += '<td class="p-2">' + (data[i].Phone != null ? data[i].Phone : "") + '</td>';
        html += '<td class="p-2"><a href="#" class="text-red-600" onclick="setBackValue(\'' + data[i].ID + '\',\'' + data[i].Name + '\')">Pilih</a></td>';
        html += '</tr>';
      }
      $('#wraperCustomerSearchResult').show();
    } else {
      $('#wraperCustomerSearchResult').hide();
    }
    $('#tblFrmCustomer_Body').html(html);
  }
</script>