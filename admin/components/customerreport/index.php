  
<label class="block mt-4 hidden">
  <span>Cabang *</span>
    <select
      id="selFrmBranch"
      name="selFrmBranch"
      class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
      required
    >
      <option value="">Semua</option>
    </select>
</label>

  <button
    type="button"
    class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
    onclick="doDownload()"
    >Download
  </button>

<script>
  doFetch('global/getAllBranch','_cb=onCompleteFetchBranch&_p=');
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

  function doDownload() {
    window.open(apiUrl + "/customerreport/get?_s=" + getCookie(MSG['cookiePrefix']+'AUTH-TOKEN'));
  }
</script>