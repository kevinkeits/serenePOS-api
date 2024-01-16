  <label class="block mt-4">
    <span>Cabang *</span>
    <select
      id="selFrmBranch"
      name="selFrmBranch"
      class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
      required
    >
      <option value="">Silahkan Pilih</option>
    </select>
  </label>
  <footer class="flex flex-col items-center px-4 py-1 -mx-4 -mb-1 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row pt-4">
    <button
      type="button"
      class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
      onclick="doDownload()"  
    >Download
    </button>
  </footer>
  
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
    if ($('#selFrmBranch').val()=="") {
      Swal.fire({title:'Error', text:'Silahkan pilih Cabang terlebih dahulu', icon:'error'});
    } else {
      window.open(apiUrl + "/stockreport/get?branchID="+$('#selFrmBranch').val()+"&_s=" + getCookie(MSG['cookiePrefix']+'AUTH-TOKEN'));
    }
  }
</script>