<div class="mt-4 mb-6">
    <div id="lblMdlTitle" class="mb-2 text-lg font-semibold text-gray-700">Lihat Foto</div>
    <div class="text-sm text-gray-700">
      <p class="text-gray-400 mb-8"></p>
      <label class="block mt-4">
	  	<img
			id="imgFile" src=""
			class="ml-auto mr-auto w-auto h-auto"
		/>
      </label>

    </div>
  </div>
  <footer class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50">
    <button
      type="button"
	    id="btnBack"
      class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
    >
      Kembali
    </button>
  </footer>
  <script>
  function onDetailForm(ProductID,ID) {
    doFetch('product/getImageProduct','_cb=onFetchComplete&_i='+ID);

    $('#btnBack').attr('onClick','showDetailForm(\''+ ProductID +'\')');
  }

  function onFetchComplete(data){
    Swal.close();
    if (data[0].ImagePath != null && data[0].ImagePath != '') {
      $('#imgFile').attr('src',uploadedUrl + '/product/' + data[0].ImagePath);
    }
    
	  
  }
</script>