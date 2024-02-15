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
  function onDetailForm(ID) {
    doFetch('category/get','_i='+ID);
	if (data.ImagePath != null && data.ImagePath != '') {
		$('#imgFile').attr('src',uploadedUrl + '/category/' + data.ImagePath);
	}
	$('#btnBack').attr('onClick','showDetailForm(\''+ ID +'\')');
  }
</script>