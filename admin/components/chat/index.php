<div class="relative text-gray-600 w-52 mb-6 float-left">
  <input 
    id="txtSearch_tblchat" 
    placeholder="Cari" 
    class="bg-white h-10 px-5 pr-10 rounded-lg text-sm focus:outline-none border"
    onkeyup="doSearchTable()">
  <button type="submit" class="absolute right-0 top-0 mt-3 mr-4">
  <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 56.966 56.966" xml:space="preserve" width="512px" height="512px">
    <path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  s-17-7.626-17-17S14.61,6,23.984,6z"/>
  </svg>
  </button>
</div>

<!-- <button
  onclick="loadModal('chat/form','fetchState();fetchAllUser();')"
  class="w-32 float-right px-4 py-2 text-sm text-white bg-red-600 border border-transparent rounded-lg active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
>
  Tambah Baru
</button> -->

<table id="tblchat" class="w-full whitespace-nowrap">
  <thead>
    <tr class="font-semibold tracking-wide text-left text-gray-500 bg-gray-100 uppercase border-b">
      <th class="px-4 py-3">Pelanggan</th>
      <th class="px-4 py-3">Cabang</th>
      <th class="px-4 py-3">Pesan</th>
      <th class="px-4 py-3">Tanggal</th>
      <th class="px-4 py-3">Status</th>
      <th class="px-4 py-3">&nbsp;</th>
    </tr>
  </thead>
</table>

<script>
  Pace.restart();
  $('#tblchat').DataTable( {
    ajax: {
      url: apiUrl+'/chat/getListing?_s='+getCookie(MSG['cookiePrefix']+'AUTH-TOKEN'),
    },
    "ordering": false,
    columns: [
      { data:'Name', className:'px-4 py-3 text-sm' },
      { data:'Branch', className:'px-4 py-3 text-sm' },
      {
        data:'Message',
        className:'px-4 py-3 text-sm',
        render: function (data, type, full, meta) {
          var html = data.length > 50 ? data.substring(0,50) + "..." : data;
          return html
        },
      },
      { data:'CreatedDate', className:'px-4 py-3 text-sm' },
      {
        data:'IsReadByBranch',
        className:'px-4 py-3 text-sm',
        render: function (data, type, full, meta) {
          var html = data==1 ? '<span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Sudah Dibaca</span>' : '<span class="px-2 py-1 font-semibold leading-tight text-white bg-red-400 rounded-full">Pesan Baru</span>';
          return html
        },
      },
      {
        data:'CustomerID',

        render: function (data, type, full, meta) {
          var html = '<div class="flex item-center justify-center">' +
                        '<div onclick="showDetailForm(\''+full['CustomerID']+'\',\''+full['BranchID']+'\')" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">' +
                          '<svg xmlns="http://www.w3.org/2000/svg" fill="none" height="15px" viewBox="0 0 24 24" stroke="currentColor">' +
                            '<path  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/>' +
                          '</svg>' +
                        '</div>';
          return html
        },
      },
    ],
    'dom': '<"w-full overflow-x-auto rounded-lg"t<"grid px-4 py-3 text-xs tracking-wide text-gray-500 border-t bg-gray-50 sm:grid-cols-9"<"flex items-center col-span-3"i><"col-span-2"><"flex col-span-4 mt-2 sm:mt-auto sm:justify-end"<"inline-flex items-center"p>>>>'
  });

  function doReloadTable() {
    Pace.restart();
    $('#tblchat').DataTable().ajax.reload();
    modal.close();
  }

  function doSearchTable() {
    $('#tblchat').DataTable().search( $('#txtSearch_tblchat').val() ).draw();
  }

  function showDetailForm(CustomerID,BranchID) {
    loadModal('chat/chatbox','onDetailForm(\''+CustomerID+'\',\''+BranchID+'\')');
	  /* $.ajax({
      url: 'components/chat/chatbox.php?_i'+ID,
      type: 'get',
      beforeSend: function(e, t, i) { doBeforeSend(); },
      success: function(e) { 
        $('.tagMenu').removeClass('bg-blue-50 rounded-full');
        $('.tagMenu_chatbox').addClass('bg-blue-50 rounded-full');
        //$('#lblBreadcrumb').show();
        $('#bgWrapper').show();
        $('#lblHero').html('ChatBox');
        //$('#lblBreadcrumb').html('<a href="#" onclick="loadMenu(\''+url+'\',\''+name+'\')">'+name+'</a>');
        $('#contentWrapper').html(e);
        Swal.close();
      },
      error: function(e, t, i) { doHandleError(e, t, i) },
      timeout: maxTimeout
    }); */
  }

  function showDeleteConfirm(ID,Label) {
    Swal.fire({html:'Apakah anda yakin akan menghapus <strong>'+Label+'</strong> ?', icon:'warning', showCancelButton:true, confirmButtonText: 'Ya', cancelButtonText: 'Tidak'})
    .then((result) => {
      if (result.isConfirmed) {
        var param = '_i='+ID;
        doSubmit('chat/doDelete',param);
      }
    });
  }
</script>