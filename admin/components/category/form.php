<form id="frmCategory" onsubmit="return doSubmitForm(event,'category/doSave','frmCategory')" enctype="multipart/form-data">
  	<div class="mt-4 mb-6">
    	<div id="lblMdlTitle" class="mb-2 text-lg font-semibold text-gray-700">Tambah Kategori Baru</div>
		<div class="text-sm text-gray-700">
			<p class="text-gray-400 mb-8">Mohon diisi semua field yang wajib bertanda (*) sebelum menyimpan form ini</p>
			<label class="block mt-4">
				<span>Nama *</span>
				<input
					id="txtFrmName"
					name="txtFrmName"
					type="text"
					maxlength="50"
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
					<div onclick="loadModal('category/view','onDetailForm(\''+$('#hdnFrmID').val()+'\')')" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">
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
					<span class="ml-2">Tidak Aktif</span>
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
</form>

<script>
	function onDetailForm(ID) {
		$('#lblMdlTitle').html('Ubah Kategori');
		$('#hdnFrmID').val(ID);
		$('#hdnFrmAction').val('edit');
		doFetch('category/get','_i='+ID);
	}
	function onCompleteFetch(data) {
		Swal.close();
		$('#txtFrmName').val(data.Name);
		if (data.ImagePath != null && data.ImagePath != '') {
			$('#imgFile').attr('src',uploadedUrl + '/category/' + data.ImagePath);
			$('#imgModBtn').show();
		} else {
			$('#hdnAttached').val("");
			$('imgModBtn').hide();
		}
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
		doUpload('category/doUpload','inpFile',ID);
	}

	function removePhoto(ID) {
		var param = '_i='+ID;
		doSubmit('category/doDeletePic',param);
	}
</script>