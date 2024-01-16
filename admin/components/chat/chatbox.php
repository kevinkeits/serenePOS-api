<style>
	.scrollbar-w-2::-webkit-scrollbar {
		width: 0.25rem;
		height: 0.25rem;
	}

	.scrollbar-track-blue-lighter::-webkit-scrollbar-track {
		--bg-opacity: 1;
		background-color: #f7fafc;
		background-color: rgba(247, 250, 252, var(--bg-opacity));
	}

	.scrollbar-thumb-blue::-webkit-scrollbar-thumb {
		--bg-opacity: 1;
		background-color: #edf2f7;
		background-color: rgba(237, 242, 247, var(--bg-opacity));
	}

	.scrollbar-thumb-rounded::-webkit-scrollbar-thumb {
		border-radius: 0.25rem;
	}
</style>

<div class="flex-1 flex flex-col" style="height: 80vh">
	<div class="flex sm:items-center justify-between py-3 border-b-2 border-gray-200">
		<div class="flex items-center space-x-4">
			<div id="userPhoto">
				<img src="../assets/img/avatar.png" alt="" class="w-10 sm:w-16 h-10 sm:h-16 rounded-full">
			</div>
			<div class="flex flex-col leading-tight">
				<div class="text-2xl mt-1 flex items-center">
					<span class="text-lg text-gray-600" id="custName"></span>
				</div>
				<div class="text-lg text-gray-600" id="custDetail">
					<div class="grid-rows-1">
						<div class="grid" style="grid-template-columns: repeat(2, minmax(0, 0.25fr)); align-items: center;">
							<svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 0 24 24" width="15px" fill="#000000">
								<path d="M6.54 5c.06.89.21 1.76.45 2.59l-1.2 1.2c-.41-1.2-.67-2.47-.76-3.79h1.51m9.86 12.02c.85.24 1.72.39 2.6.45v1.49c-1.32-.09-2.59-.35-3.8-.75l1.2-1.19M7.5 3H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.49c0-.55-.45-1-1-1-1.24 0-2.45-.2-3.57-.57-.1-.04-.21-.05-.31-.05-.26 0-.51.1-.71.29l-2.2 2.2c-2.83-1.45-5.15-3.76-6.59-6.59l2.2-2.2c.28-.28.36-.67.25-1.02C8.7 6.45 8.5 5.25 8.5 4c0-.55-.45-1-1-1z"/>
							</svg>
							<div id="custPhone" style="font-size:12px"></div>
							<svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 0 24 24" width="15px" fill="#000000">
								<path d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6zm-2 0l-8 5-8-5h16zm0 12H4V8l8 5 8-5v10z"/>
							</svg>
							<div id="custMail" style="font-size:12px"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- <div class="flex items-center space-x-2">
			<button type="button" class="inline-flex items-center justify-center rounded-full h-10 w-10 transition duration-500 ease-in-out text-gray-500 hover:bg-gray-300 focus:outline-none">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
				</svg>
			</button>
		</div> -->
	</div>


	<div id="messages" class="flex flex-col space-y-4 p-3 overflow-y-auto scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-blue-lighter scrollbar-w-2 scrolling-touch" style="height: 75%;">
	</div>

	
	<div class="border-t-2 border-gray-200 px-4 pt-4 mb-2 sm:mb-0">
		<form id="frmChat" onsubmit="return doSubmitForm(event,'chat/doPost','frmChat')">

			<div class="relative flex">
				<textarea id="txtChat" name="txtChat" required placeholder="Tulis sesuatu" class="w-full focus:outline-none focus:placeholder-gray-400 text-gray-600 placeholder-gray-600 pl-12 bg-gray-200 rounded-full py-3" style="max-width:440px"></textarea>
				<div class="absolute right-0 items-center inset-y-0 hidden sm:flex">
					<button type="submit" class="inline-flex items-center justify-center rounded-full h-12 w-12 transition duration-500 ease-in-out text-white bg-blue-500 hover:bg-blue-400 focus:outline-none">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-6 w-6 transform rotate-90">
						<path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
					</svg>
					</button>
					<input type="hidden" id="hdnFrmCustomerID" name="CustomerID" value=""/>
					<input type="hidden" id="hdnFrmBranchID" name="BranchID" value=""/>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	/* $.ajax({
		url: apiUrl+'/chat/getMessageDetail?_s='+getCookie(MSG['cookiePrefix']+'AUTH-TOKEN'),
		type: 'get',
		beforeSend: function(e, t, i) { doBeforeSend(); },
		success: function(e) { 
			
		},
		error: function(e, t, i) { doHandleError(e, t, i) },
		timeout: maxTimeout
	}); */

	function onDetailForm(CustomerID,BranchID) {
		doFetch('chat/getMessageDetail','CustomerID='+CustomerID+'&BranchID='+BranchID, false);
		$('#hdnFrmCustomerID').val(CustomerID);
		$('#hdnFrmBranchID').val(BranchID);
	}

	function onCompleteFetch(data) {
		Swal.close();

		$("#custName").html(data[0].CustomerName);
		$("#custPhone").html(data[0].CustomerPhone);
		$("#custMail").html(data[0].CustomerEmail);

		if(data[0].CustomerPhoto !== '' && data[0].CustomerPhoto !== null){
			$("##userPhoto").attr(data[0].CustomerPhoto);
		}
		
		var html = '';
		for(i=0; i<data.length; i++) {
			if(data[i].AdminID !== '' && data[i].AdminID !== null){
				//if(data[i].AdminID == data[i].LoginUserID) {
				if(data[i].AdminID != data[i].UserID) {
					// Right
					html +=
					'<div class="flex items-end justify-end">' +
					'	<div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-1 items-end">' +
					'		<div>' +
					'			<span class="px-4 py-2 rounded-lg inline-block rounded-bl-none bg-blue-600 text-white" style="max-width:485px">' +
					'				<div style="color:white; font-weight:bold;">' + data[i].AdminName + '</div>' +
					'				<div style="overflow-wrap: break-word">' + data[i].Message + '</div>' +
					'				<div style="text-align:right; color: #D3D3D3; font-size:9px">' + data[i].CreatedDate + '</div>' +
					'			<span>' +
					'		</div>' +
					'	</div>' +
					'</div>';
				} else {
					// Left
					html +=
					'<div class="flex items-end">' +
					'	<div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-2 items-start">' +
					'		<div>' +
					'			<span class="px-4 py-2 rounded-lg inline-block rounded-bl-none bg-gray-300 text-gray-600" style="max-width:485px">' +
					'				<div style="color:black; font-weight:bold;">' + data[i].AdminName + '</div>' +
					'				<div style="overflow-wrap: break-word">' + data[i].Message + '</div>' +
					'				<div style="text-align:right; color: #fff; font-size:9px">' + data[i].CreatedDate + '</div>' +
					'			<span>' +
					'		</div>' +
					'	</div>' +
					'</div>';
				}
				
			} else {
				// Left
				html +=
				'<div class="flex items-end">' +
				'	<div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-2 items-start">' +
				'		<div>' +
				'			<span class="px-4 py-2 rounded-lg inline-block rounded-bl-none bg-gray-300 text-gray-600" style="max-width:485px">' +
				'				<div style="color:black; font-weight:bold;">' + data[i].CustomerName + '</div>' +
				'				<div style="overflow-wrap: break-word">' + data[i].Message + '</div>' +
				'				<div style="text-align:right; color: #fff; font-size:9px">' + data[i].CreatedDate + '</div>' +
				'			<span>' +
				'		</div>' +
				'	</div>' +
				'</div>';
			}
			
		}
		$('#messages').html(html);
	}

	function sendChat() {
		var strChat = $("#txtChat").val();
		if(strChat.trim() !== '') {
			var param = "";
			param += "CustomerID=" + $('#hdnFrmCustomerID').val();
			param += "&BranchID=" + $('#hdnFrmBranchID').val();
			param += "&txtChat=" + strChat.trim(); 
			doSubmit('chat/doPost', param, false);
		}
	}

</script>