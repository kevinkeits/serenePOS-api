<form id="frmArticle" onsubmit="return saveForms(event)" enctype="multipart/form-data">
  <div class="mt-4 mb-6">
    <div id="lblMdlTitle" class="mb-2 text-lg font-semibold text-gray-700">Tambah Referensi Data</div>
    <div class="text-sm text-gray-700">
      <p class="text-gray-400 mb-8">Mohon diisi semua field yang wajib bertanda (*) sebelum menyimpan form ini</p>
      <div class="block mt-4">
        <span>Tipe *</span>
        <div class="mt-2">
          <label class="inline-flex items-center">
            <input
              id="radFrmType_1"
              name="radFrmType"
              type="radio"
              class="text-red-600 form-radio focus:border-red-400 focus:outline-none focus:shadow-outline-gray"
              value="1"
              checked
            />
            <span class="ml-2">Resep</span>
          </label>
          <label class="inline-flex items-center ml-6">
            <input
              id="radFrmType_2"
              name="radFrmType"
              type="radio"
              class="text-red-600 form-radio focus:border-red-400 focus:outline-none focus:shadow-outline-gray" 
              value="2"
            />
            <span class="ml-2">Artikel</span>
          </label>
        </div>
      </div>

      <label class="block mt-4">
        <span>Judul *</span>
        <input
          id="txtFrmTitle"
          name="txtFrmTitle"
          type="text"
          maxlength="250"
          class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
          placeholder="Ketik disini"
          required
        />
      </label>

	    <label class="block mt-4">
				<span>Upload Gambar *</span>
			</label>
			<div class="grid grid-rows-1">
				<label class="w-20 rounded-md shadow-xs border border-gray-200 cursor-pointer focus:outline-none focus:shadow-outline-gray">
					<img
						id="imgFile" src="../assets/img/bg/plus-icon-13062.png"
						class="ml-auto mr-auto w-auto h-20"
					/>
					<input type="file" class="hidden" name="inpFile" id="inpFile" onChange="beforeUpload(this);" accept="image/png, image/gif, image/jpeg, image/jpg"/>
				</label>
				<div id="imgModBtn" class="flex mt-1 pl-5 w-20" style="display:none">
					<div onclick="loadModal('banner/view','onDetailForm(\''+$('#hdnFrmID').val()+'\')')" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">
						<svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" width="18px" fill="currentColor">
							<path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 6c3.79 0 7.17 2.13 8.82 5.5C19.17 14.87 15.79 17 12 17s-7.17-2.13-8.82-5.5C4.83 8.13 8.21 6 12 6m0-2C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4zm0 5c1.38 0 2.5 1.12 2.5 2.5S13.38 14 12 14s-2.5-1.12-2.5-2.5S10.62 9 12 9m0-2c-2.48 0-4.5 2.02-4.5 4.5S9.52 16 12 16s4.5-2.02 4.5-4.5S14.48 7 12 7z"/>
						</svg>
					</div>
					<div onclick="removePhoto($('#hdnFrmID').val())" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
						</svg>
					</div>
				</div>
			</div>

      <label class="block mt-4">
				<span>Deskripsi *</span>

<div id="editor">
</div>

			</label>

      <div class="block mt-4">
        <span>Status *</span>
        <div class="mt-2">
          <label class="inline-flex items-center">
            <input
              id="radFrmStatus_1"
              name="radFrmStatus"
              type="radio"
              class="text-red-600 form-radio focus:border-red-400 focus:outline-none focus:shadow-outline-gray"
              value="1"
              checked
            />
            <span class="ml-2">Aktif</span>
          </label>
          <label class="inline-flex items-center ml-6">
            <input
              id="radFrmStatus_0"
              name="radFrmStatus"
              type="radio"
              class="text-red-600 form-radio focus:border-red-400 focus:outline-none focus:shadow-outline-gray"
              value="0"
            />
            <span class="ml-2">Tidak Aktif</span>F
          </label>
        </div>
      </div>
    </div>
  </div>
  <footer class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50">
    <button
      type="submit"
      class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg sm:w-32 sm:px-4 sm:py-2 active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
    >
      Simpan
    </button>
  </footer>
  <input type="hidden" id="hdnFrmID" name="hdnFrmID" value=""/>
  <input type="hidden" id="hdnFrmAction" name="hdnFrmAction" value="add"/>
  <input type="hidden" id="hdnAttached" name="hdnAttached" value=""/>
  <input type="hidden" id="txtFrmContent" name="txtFrmContent" value=""/>
</form>

<script>

  function saveForms(event) {
	event.preventDefault();
	$('#txtFrmContent').val($('.ql-editor').html());
	doSubmitForm(event,'article/doSave','frmArticle') 
}  

  function initQuill() {
	var toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
		[{ 'color': [] }],          // dropdown with defaults from theme
                [{ 'align': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                [ 'link', 'image', 'video'],          // add's image support
            ];
	var quill = new Quill('#editor', {
	modules: {
                toolbar: toolbarOptions
            },
    	theme: 'snow'
  	});
	quill.getModule("toolbar").addHandler("video", videoHandler);
function videoHandler() {
    let url = prompt("Enter Video URL: ");
    url = getVideoUrl(url);
    let range = quill.getSelection();
    if (url != null) {
        quill.insertEmbed(range, 'video', url);
    }
}

function getVideoUrl(url) {
    let match = url.match(/^(?:(https?):\/\/)?(?:(?:www|m)\.)?youtube\.com\/watch.*v=([a-zA-Z0-9_-]+)/) ||
        url.match(/^(?:(https?):\/\/)?(?:(?:www|m)\.)?youtu\.be\/([a-zA-Z0-9_-]+)/) ||
        url.match(/^.*(youtu.be\/|v\/|e\/|u\/\w+\/|embed\/|v=)([^#\&\?]*).*/);
    console.log(match[2]);
    if (match && match[2].length === 11) {
        return ('https') + '://www.youtube.com/embed/' + match[2] + '?showinfo=0';
    }
    if (match = url.match(/^(?:(https?):\/\/)?(?:www\.)?vimeo\.com\/(\d+)/)) { // eslint-disable-line no-cond-assign
        return (match[1] || 'https') + '://player.vimeo.com/video/' + match[2] + '/';
    }
    return null;
}

  }

  function onDetailForm(ID) {
    $('#lblMdlTitle').html('Ubah Artikel'); 
    $('#hdnFrmID').val(ID);
    $('#hdnFrmAction').val('edit');
    doFetch('article/get','_i='+ID);
  }
  function onCompleteFetch(data) {
    Swal.close();
   $('#radFrmType_'+data.Type).prop('checked', true);
    $('#txtFrmTitle').val(data.Title);
    if (data.ImageUrl!= null && data.ImageUrl!= '') {
			$('#imgFile').attr('src',uploadedUrl + '/article/' + data.ImageUrl);
			$('#imgModBtn').show();
		} else {
			$('#hdnAttached').val("");
			$('imgModBtn').hide();
		}

    $('#editor').html(data.Contents);
    initQuill()
    $('#radFrmStatus_'+data.Status).prop('checked', true);
  }

function beforeUpload(input) {
		$('#hdnAttached').val($('#inpFile').val());
		if (input.files && input.files.length > 0) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#imgFile').attr('src', e.target.result);
			};
			reader.readAsDataURL(input.files[0]);
		} else {
			$('#imgFile').attr('src', '../assets/img/bg/plus-icon-13062.png');
		}
	}

	function execUpload(ID) {
		doUpload('article/doUpload','inpFile',ID);
	}

	function removePhoto(ID) {
		var param = '_i='+ID;
		doSubmit('article/doDeletePic',param);
	}

</script>