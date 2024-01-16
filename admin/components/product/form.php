<form id="frmProduct" onsubmit="return doSubmitForm(event,'product/doSave','frmProduct')" enctype="multipart/form-data">
	<div class="mt-4 mb-6">
		<div id="lblMdlTitle" class="mb-2 text-lg font-semibold text-gray-700">Tambah Produk</div>
		<div class="text-sm text-gray-700">
			<p class="text-gray-400 mb-8">Mohon diisi semua field yang wajib bertanda (*) sebelum menyimpan form ini</p>
							
			<label class="block mt-4">
				<span>Cabang *</span>
				<select
					id="selFrmBranch"
					name="selFrmBranch"
					class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
					required
				></select>
			</label>

			<label class="block mt-4">
				<span>SKU *</span>
				<input
					id="txtFrmCode"
					name="txtFrmCode"
					type="text"
					maxlength="20"
					class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
					placeholder="Ketik disini"
					required
				/>
			</label>

			<label class="block mt-4">
				<span>Nama *</span>
				<input
					id="txtFrmName"
					name="txtFrmName"
					maxlength="250"
					class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
					placeholder="Ketik disini"
					required
				>
			</label>
			
			<label class="block mt-4">
				<span>Deskripsi *</span>
				<textarea
					id="txtFrmDesc"
					name="txtFrmDesc"
					maxlength="4000"
					class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
					placeholder="Ketik disini"
					required
				></textarea>
			</label>

			<label class="block mt-4">
				<span>Upload Gambar *</span>
			</label>
			<div class="grid grid-cols-4 col-auto">
				<div class="grid grid-rows-1">
					<label class="w-20 rounded-md shadow-xs border border-gray-200 cursor-pointer focus:outline-none focus:shadow-outline-gray">
						<img
							id="imgFile_1" src="../assets/img/bg/plus-icon-13062.png"
							class="ml-auto mr-auto w-auto h-20"
						/>
						<input type="file" class="hidden" id="inpFile_1" onChange="beforeUpload(this,1);" accept="image/png, image/gif, image/jpeg, image/jpg"/>
						<input type="hidden" id="inpFileID_1" value="">
					</label>
					<div id="imgModBtn_1" class="flex mt-1 pl-5 w-20" style="display:none">
						<div onclick="loadModal('product/view','onDetailForm(\''+$('#hdnFrmID').val()+'\',\''+$('#inpFileID_1').val()+'\')')" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">
							<svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" width="18px" fill="currentColor">
								<path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 6c3.79 0 7.17 2.13 8.82 5.5C19.17 14.87 15.79 17 12 17s-7.17-2.13-8.82-5.5C4.83 8.13 8.21 6 12 6m0-2C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4zm0 5c1.38 0 2.5 1.12 2.5 2.5S13.38 14 12 14s-2.5-1.12-2.5-2.5S10.62 9 12 9m0-2c-2.48 0-4.5 2.02-4.5 4.5S9.52 16 12 16s4.5-2.02 4.5-4.5S14.48 7 12 7z"/>
							</svg>
						</div>
						<div onclick="removePhoto($('#hdnFrmID').val(),$('#inpFileID_1').val())" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
							</svg>
						</div>
					</div>
				</div>

				<div class="grid grid-rows-1">
					<label class="w-20 rounded-md shadow-xs border border-gray-200 cursor-pointer focus:outline-none focus:shadow-outline-gray">
						<img
							id="imgFile_2" src="../assets/img/bg/plus-icon-13062.png"
							class="ml-auto mr-auto w-auto h-20"
						/>
						<input type="file" class="hidden" id="inpFile_2" onChange="beforeUpload(this,2);" accept="image/png, image/gif, image/jpeg, image/jpg"/>
						<input type="hidden" id="inpFileID_2" value="">
					</label>
					<div id="imgModBtn_2" class="flex mt-1 pl-5 w-20" style="display:none">
						<div onclick="loadModal('product/view','onDetailForm(\''+$('#hdnFrmID').val()+'\',\''+$('#inpFileID_2').val()+'\')')" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">
							<svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" width="18px" fill="currentColor">
								<path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 6c3.79 0 7.17 2.13 8.82 5.5C19.17 14.87 15.79 17 12 17s-7.17-2.13-8.82-5.5C4.83 8.13 8.21 6 12 6m0-2C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4zm0 5c1.38 0 2.5 1.12 2.5 2.5S13.38 14 12 14s-2.5-1.12-2.5-2.5S10.62 9 12 9m0-2c-2.48 0-4.5 2.02-4.5 4.5S9.52 16 12 16s4.5-2.02 4.5-4.5S14.48 7 12 7z"/>
							</svg>
						</div>
						<div onclick="removePhoto($('#hdnFrmID').val(),$('#inpFileID_2').val())" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
							</svg>
						</div>
					</div>
				</div>
				<div class="grid grid-rows-1">
					<label class="w-20 rounded-md shadow-xs border border-gray-200 cursor-pointer focus:outline-none focus:shadow-outline-gray">
						<img
							id="imgFile_3" src="../assets/img/bg/plus-icon-13062.png"
							class="ml-auto mr-auto w-auto h-20"
						/>
						<input type="file" class="hidden" id="inpFile_3" onChange="beforeUpload(this,3);" accept="image/png, image/gif, image/jpeg, image/jpg"/>
						<input type="hidden" id="inpFileID_3" value="">
					</label>
					<div id="imgModBtn_3" class="flex mt-1 pl-5 w-20" style="display:none">
						<div onclick="loadModal('product/view','onDetailForm(\''+$('#hdnFrmID').val()+'\',\''+$('#inpFileID_3').val()+'\')')" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">
							<svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" width="18px" fill="currentColor">
								<path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 6c3.79 0 7.17 2.13 8.82 5.5C19.17 14.87 15.79 17 12 17s-7.17-2.13-8.82-5.5C4.83 8.13 8.21 6 12 6m0-2C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4zm0 5c1.38 0 2.5 1.12 2.5 2.5S13.38 14 12 14s-2.5-1.12-2.5-2.5S10.62 9 12 9m0-2c-2.48 0-4.5 2.02-4.5 4.5S9.52 16 12 16s4.5-2.02 4.5-4.5S14.48 7 12 7z"/>
							</svg>
						</div>
						<div onclick="removePhoto($('#hdnFrmID').val(),$('#inpFileID_3').val())" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
							</svg>
						</div>
					</div>
				</div>
				<div class="grid grid-rows-1">
					<label class="w-20 rounded-md shadow-xs border border-gray-200 cursor-pointer focus:outline-none focus:shadow-outline-gray">
						<img
							id="imgFile_4" src="../assets/img/bg/plus-icon-13062.png"
							class="ml-auto mr-auto w-auto h-20"
						/>
						<input type="file" class="hidden" id="inpFile_4" onChange="beforeUpload(this,4);" accept="image/png, image/gif, image/jpeg, image/jpg"/>
						<input type="hidden" id="inpFileID_4" value="">
					</label>
					<div id="imgModBtn_4" class="flex mt-1 pl-5 w-20" style="display:none">
						<div onclick="loadModal('product/view','onDetailForm(\''+$('#hdnFrmID').val()+'\',\''+$('#inpFileID_4').val()+'\')')" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">
							<svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" width="18px" fill="currentColor">
								<path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 6c3.79 0 7.17 2.13 8.82 5.5C19.17 14.87 15.79 17 12 17s-7.17-2.13-8.82-5.5C4.83 8.13 8.21 6 12 6m0-2C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4zm0 5c1.38 0 2.5 1.12 2.5 2.5S13.38 14 12 14s-2.5-1.12-2.5-2.5S10.62 9 12 9m0-2c-2.48 0-4.5 2.02-4.5 4.5S9.52 16 12 16s4.5-2.02 4.5-4.5S14.48 7 12 7z"/>
							</svg>
						</div>
						<div onclick="removePhoto($('#hdnFrmID').val(),$('#inpFileID_4').val())" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
							</svg>
						</div>
					</div>
				</div>

			</div>

			<label class="block mt-4">
				<span>Harga *</span>
			 	<input type="hidden" id="hdnFrmPrice" value="1">
				<table 
					id="tblFrmPrice"
					name="tblFrmPrice"
					class="table-fixed border p-4 w-full mt-1"
				>
					<tr class="table-fixed border">
						<td width="35px">
							<div onclick="addRow(0,0,0)" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">
								<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><g><rect fill="none" height="24" width="24"/></g><g><g><path d="M19,13h-6v6h-2v-6H5v-2h6V5h2v6h6V13z"/></g></g></svg>
							</div>
						</td>
						<td>Min Pesanan</td>
						<td>Maks Pesanan</td>
						<td>Harga</td>
					</tr>
					<tr id="trFrmPrice_1" class="table-fixed border">
						<td width="35px" id="td_1" name="td_1"></td>
						<td id="td_minOrder_1" name="td_minOrder_1">
							<input
								id="minOrder_1"
								name="minOrder[]"
								class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
								type="number"
								min="0"
								value="0"
								required
							>
						</td>
						<td id="td_maxOrder_1" name="td_maxOrder_1">
							<input
								id="maxOrder_1"
								name="maxOrder[]"
								class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
								type="number"
								min="0"
								value="0"
								required
							>
						</td>
						<td id="td_priceOrder_1" name="td_priceOrder_1">
							<input
								id="priceOrder_1"
								name="priceOrder[]"
								class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
								type="number"
								min="0"
								value="0"
								required
							>
						</td>
					</tr>
				</table>
			</label>

			<label class="block mt-4" style="display:none">
				<span>Berat (kg) *</span>
				<input
					id="txtFrmWeight"
					name="txtFrmWeight"
					type="number"
					min="0"
					class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
					placeholder="Ketik disini"
					value="1"
					required
				/>
			</label>

			<label class="block mt-4" id="txtFrmStockWrapper" style="display:none">
				<span>Jumlah Stok</span>
				<input
					id="txtFrmStock"
					name="txtFrmStock"
					type="number"
					min="0"
					value="0"
					class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
					placeholder="Ketik disini"
				/>
			</label>

			<label class="block mt-4">
				<span>Kategori</span>
				<select
				id="selFrmProductCategory"
				name="selFrmProductCategory[]"
				multiple="multiple"
				size="10"
				>
				</select>
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
	if (getCookie(MSG['cookiePrefix']+'AUTH-ISBRANCHADMIN')=="false") $('#txtFrmStockWrapper').show();
	function fetchCategory(ID=null) {
		doFetch('global/getCategory','_cb=onCompleteFetchCategory&_p='+encodeURI(JSON.stringify(ID)));
	}

	function onCompleteFetchCategory(data,ID) {
		var rawCategory = JSON.parse(decodeURI(ID));
		Swal.close();
		var html = '';
		for (i=0;i<data.length;i++) {
			if (rawCategory!=null) {
				html += '<option value="'+data[i].ID+'" '+(rawCategory.indexOf(data[i].ID)>=0 ? 'selected' : '')+'>'+data[i].Name+'</option>';
			} else {
				html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
			}
		}
		$('#selFrmProductCategory').html(html);
		$('#selFrmProductCategory').DualListbox({
			infoText: ''
		});
	}
	
	function fetchBranch(ID='') {
		doFetch('global/getAllBranch','_cb=onCompleteFetchBranch&_p='+ID);
	}

	function onCompleteFetchBranch(data,ID) {
		Swal.close();
		var html = '';
		html += '<option value="">Please Select</option>';
		for (i=0;i<data.length;i++) {
			html += '<option value="'+data[i].ID+'">'+data[i].Name+'</option>';
		}
		$('#selFrmBranch').html(html);
		if (ID!='') { $('#selFrmBranch').val(ID); }
		if (data.length == 1) $("#selFrmBranch").prop('selectedIndex', 1);
	}

	function fetchPriceDetail(BranchID,ID='') {
		doFetch('product/getPriceDetail','_cb=onCompleteFetchPriceDetail&_p='+ID+'&branchid='+BranchID);
	}

	function onCompleteFetchPriceDetail(data,ID) {
		Swal.close();
		//$('#trFrmPrice_1').remove();
		$('#minOrder_1').val(data[0].MinOrder);
		$('#maxOrder_1').val(data[0].MaxOrder);
		$('#priceOrder_1').val(data[0].Price);
		
		for (i=1;i<data.length;i++) {
			addRow(data[i].MinOrder, data[i].MaxOrder, data[i].Price);
		}
	}

	function fetchImageProduct(ID='') {
		doFetch('product/getImageProduct','_cb=onCompleteFetchImageProduct&_p='+ID);
	}
	
	function onCompleteFetchImageProduct(data,ID) {
		Swal.close();
		for(var i=0; i<data.length; i++) {
			if (data[i].ImagePath != "") {
				//$('#imgFile_'+ (i+1)).attr('src',uploadedUrl + '/product/' + data[i].ImagePath);
				//$('#inpFileID_'+ (i+1)).val(data[i].ID);
				$('#imgFile_'+ data[i].SequenceNo).attr('src',uploadedUrl + '/product/' + data[i].ImagePath);
				$('#inpFileID_'+ data[i].SequenceNo).val(data[i].ID);
				$('#imgModBtn_'+ data[i].SequenceNo).show();
			} else {
				$('imgModBtn_'+ (i+1)).hide();
			}
		}
	}

	function onDetailForm(ID) {
		$('#lblMdlTitle').html('Ubah Produk');
		$('#hdnFrmID').val(ID);
		$('#hdnFrmAction').val('edit');
		doFetch('product/get','_i='+ID);
	}
	function onCompleteFetch(data) {
		Swal.close();
		$('#txtFrmCode').val(data.header.Code);
		$('#txtFrmName').val(data.header.Name);
		$('#txtFrmDesc').val(data.header.Description);
		$('#selFrmBranch').val(data.header.BranchID);
		$('#txtFrmWeight').val(data.header.Weight);
		$('#txtFrmStock').val(data.header.Stock);
		$('#radFrmStatus_'+data.header.Status).prop('checked', true);
		$('#txtFrmCode').prop('disabled', true);
		fetchBranch(data.header.BranchID, data.header.ID);
		fetchPriceDetail(data.header.BranchID, data.header.ID);
		fetchCategory(data.selCategory);
		fetchImageProduct(data.header.ID);
	}

	var arrImg = [];
	function beforeUpload(input,position) {
		if (input.files && input.files.length > 0) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#imgFile_'+position).attr('src', e.target.result);
				arrImg.push($('#inpFile_'+position).val());
				$('#hdnAttached').val(arrImg.join('|'));
			};
			reader.readAsDataURL(input.files[0]);
		} else {
			$('#imgFile_'+position).attr('src', '../assets/img/bg/plus-icon-13062.png');
			arrImg.splice(arrImg.indexOf($('#inpFile_'+position).val()));
			$('#hdnAttached').val(arrImg.join('|'));
		}
	}

	function execUpload(ProductID,ImgID1,ImgID2,ImgID3,ImgID4) {
		$('#inpFileID_1').val(ImgID1);
		$('#inpFileID_2').val(ImgID2);
		$('#inpFileID_3').val(ImgID3);
		$('#inpFileID_4').val(ImgID4);

		for (var i=1; i<=4; i++) {
			if ($('#inpFile_'+i).val() != '') {
				var imgID = $('#inpFileID_'+i).val();
				doUpload('product/doUpload','inpFile_'+i,ProductID,imgID,i);
			}
			if (i == 4) Swal.fire({title:'Success', text:"Data berhasil tersimpan!", icon:'success'}); doReloadTable();
		}
		/*
		for (var i=1; i<=4; i++) {
			if ($('#inpFile_'+i).val() != '') {
				var imgID = $('#inpFileID_'+i).val();
				doSubmit('product/doFinish','_i='+imgID);
				break;
			}
		}
		*/
	}

	function removePhoto(ProductID,ID) {
		var param = '_i='+ID+'&_p='+ProductID;
		doSubmit('product/doDeletePic',param);
	}

	function addRow(MinOrder,MaxOrder,Price) {
		var tblName = document.getElementById('tblFrmPrice');
		var hdncurrRow = document.getElementById('hdnFrmPrice');
		var lastIndex = tblName.children[0].rows.length;
		var tblRow = tblName.insertRow(lastIndex);
		var currRow = 1;
		currRow = parseInt(hdncurrRow.value) + currRow;
		
		tblRow.id = "trFrmPrice_"+currRow;
		tblRow.className  = "table-fixed border";
		
		var html1 =
			'<td width="35px">' +
			'	<div onclick="removeRow('+currRow+')" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 cursor-pointer">' +
			'		<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 13H5v-2h14v2z"/></svg>' +
			'	</div>' +
			'</td>';
			
		var html2 =
			'<td>' +
			'	<input id="minOrder_'+currRow+'" name="minOrder[]" class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" type="number" min="0" value="'+MinOrder+'" required>' +
			'</td>';
		
		var html3 =
			'<td>' +
			'	<input id="maxOrder_'+currRow+'" name="maxOrder[]" class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" type="number" min="0" value="'+MaxOrder+'" required>' +
			'</td>';

		var html4 =
			'<td>' +
			'	<input id="priceOrder_'+currRow+'" name="priceOrder[]" class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray" type="number" min="0" value="'+Price+'" required>' +
			'</td>';

		var tblCell1 = tblRow.insertCell(0).innerHTML = html1;
		var tblCell2 = tblRow.insertCell(1).innerHTML = html2;
		var tblCell3 = tblRow.insertCell(2).innerHTML = html3;
		var tblCell4 = tblRow.insertCell(3).innerHTML = html4;

		hdncurrRow.value = currRow;
	}

	function removeRow(Index) {
		$('#trFrmPrice_' + Index).remove();
	}
</script>