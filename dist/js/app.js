var app = {};
var timer, valQuery;

document.addEventListener('init', function (event) {
	var mainNav = document.getElementById('mainNav');
	var page = event.target;
	/*if (page.id === 'login') {
		gapi.signin2.render('my-signin2', {
			'scope': 'profile email',
			'width': 240,
			'height': 50,
			'longtitle': true,
			'theme': 'dark',
			'onsuccess': onSignIn,
			'onfailure': function(error) { 
				ons.notification.toast(error, {
					timeout: 2000
				});
			}
		  });
	}*/
	if (page.id === 'homeApp') {
		//if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN') != "" && getCookie(MSG['cookiePrefix']+'AUTH-TOKEN') != null) {
			runHome();
			doFetch('external/getCategory','_cb=onCompleteFetchCategory&_p=main-category-slider',false);
			//doFetch('external/getDiscount','BranchID='+getCookie(MSG['cookiePrefix']+'AUTH-BRANCHID')+'&_cb=onCompleteFetchDiscount&_p=main-discount-slider',false);
			if((getCookie(MSG['cookiePrefix']+'AUTH-TOKEN') != "" && getCookie(MSG['cookiePrefix']+'AUTH-TOKEN') != null) && getCookie(MSG['cookiePrefix']+'AUTH-BRANCHID') == "") {
				ons.notification.alert("Silahkan pilih cabang terlebih dahulu",{'title':'Selamat Datang di EllaFroze'});
				openBranch();
			}
		//}
	}
	if (page.id === 'product-search') {
		$('#lblSearchTitle').html(mainNav.topPage.data.title);
		doFetch('external/getAllProduct','CatID='+mainNav.topPage.data.CatID+'&BranchID='+mainNav.topPage.data.BranchID+'&Keyword='+mainNav.topPage.data.Keyword+'&_cb=onCompleteFetchAllProduct&_p=search-product-list',false);
	} 
	if (page.id === 'discount-search') {
		doFetch('external/getDiscount','BranchID='+getCookie(MSG['cookiePrefix']+'AUTH-BRANCHID')+'&_cb=onCompleteFetchAllProduct&_p=discount-list',false);
	} 
	if (page.id === 'product-description') {
		$("#btnBuy").attr("disabled", true);
		$("#btnChat").attr("disabled", true);
		doFetch('external/getProductImage','_i=' + mainNav.topPage.data.ID + '&_cb=onCompleteFetchBanner&_p=product-image-slider-wrapper',false);
		doFetch('external/getProductDetail','_i=' + mainNav.topPage.data.ID + '&_cb=onCompleteFetchProduct&_p=',false);
		doFetch('external/getUserAddress','_cb=onCompleteFetchProductDefAddress&_p=',false);
	}
	if (page.id === 'chat') {
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')=="") doHandlerNotAuthorized();
		else fetchChatList();
	}
	if (page.id === 'chat-details') {
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')=="") doHandlerNotAuthorized();
		else {
			$('#txtHdnChatBranchID').val(mainNav.topPage.data.BranchID);
			//page.querySelector('ons-toolbar .center').innerHTML = '<img class="list-item__thumbnail" style="float:left;padding-right:10px;margin-top:10px" src="dist/img/logo.png"> Admin ' + mainNav.topPage.data.title;
			page.querySelector('ons-toolbar .center').innerHTML = 'Admin ' + mainNav.topPage.data.title;
			reloadChatMessage();
		}
	}
	if (page.id === 'cart') {
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')=="") doHandlerNotAuthorized();
		else {
			//doFetch('external/getCart','_cb=onCompleteFetchCart&_p=cartItemWrapper',false);
			doFetch('external/getCart','_cb=onCompleteFetchCart_new&_p=cartItemWrapper',false);
		}
	}
	if (page.id === 'cart-payment') {
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')=="") doHandlerNotAuthorized();
		else {
			doFetch('external/getCart','_cb=onCompleteFetchCartFinal&_p=cartItemPaymentWrapper',false);
		}
	}
	if (page.id === 'cart-completed') {
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')=="") doHandlerNotAuthorized();
		else {
			$('#btnPayWithGopay').hide();
			$('#lblPaymentExpiredDate').html(mainNav.topPage.data.ExpiredDate);
			$('#lblPaymentMethod').html(mainNav.topPage.data.PaymentMethod);
			$("#imgPaymentLogo").attr("src",uploadedUrl + '/' + mainNav.topPage.data.PaymentMethodLogo);
			$('#lblPaymentCodeName').html(mainNav.topPage.data.PaymentMethodCategory == "bank_transfer" ? "Nomor Virtual Account" : (mainNav.topPage.data.PaymentMethodCategory == "cstore" ? "Payment Code" : ""));
			$('#lblPaymentCodeName').show();
			$('#lblPaymentReferenceID').show();
			$('#lblPaymentReferenceID').html(mainNav.topPage.data.ReferenceID + ' <a href="#" onclick="copyVA(\'' + mainNav.topPage.data.ReferenceID + '\')">Copy</a>');

			if (mainNav.topPage.data.PaymentMethodCategory == "gopay") {
				$('#lblPaymentCodeName').hide();
				$('#lblPaymentReferenceID').hide();
				$('#btnPayWithGopay').show();
				$("#btnPayWithGopay").click(function(){
					ons.notification.alert("Ellafroze akan mencoba membuka aplikasi Gojek, pastikan aplikasi Gojek sudah terinstall");
					doOpenURL(mainNav.topPage.data.GoPayDeepLink);
				});
			} 
			$('#lblPaymentGrossAmount').html("Rp " + doFormatNumber(mainNav.topPage.data.GrossAmount));
		}
	}

	if (page.id === 'transaction-unpaid') {
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')=="") doHandlerNotAuthorized();
		else doFetch('external/getUnpaidTransaction','_cb=onCompleteFetchUnpaidTransaction&_p=transaction-unpaid-wrapper',false);
	}
	if (page.id === 'transaction-confirmed') {
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')!="") doFetch('external/getTransaction','_cb=onCompleteFetchTransaction&Status=2&_p=transaction-confirmed-wrapper',false);
	}
	if (page.id === 'transaction-delivery') {
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')!="") doFetch('external/getTransaction','_cb=onCompleteFetchTransaction&Status=3&_p=transaction-delivery-wrapper',false);
	}
	if (page.id === 'transaction-finished') {
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')!="") doFetch('external/getTransaction','_cb=onCompleteFetchTransaction&Status=4&_p=transaction-finished-wrapper',false);
	}
	if (page.id === 'transaction-cancel') {
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')!="") doFetch('external/getTransaction','_cb=onCompleteFetchTransaction&Status=5&_p=transaction-cancel-wrapper',false);
	}
	if (page.id === 'transaction-detail') {
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')!="") doFetch('external/getTransactionDetail','_cb=onCompleteFetchTransactionDetail&ID='+mainNav.topPage.data.ID+'&_p=transactionDetailOrderList',false);
	}

	if (page.id === 'profile') {
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')=="") doHandlerNotAuthorized();
		else doFetch('external/getUser','_cb=onCompleteFetchUserDetail&_p=',false);
	}
	if (page.id === 'profile_edit') {
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')=="") doHandlerNotAuthorized();
		else doFetch('external/getUser','_cb=onCompleteFetchUserDetailEdit&_p=',false);
	}
	if (page.id === 'profile_address_list') {
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')=="") doHandlerNotAuthorized();
		else doFetch('external/getUserAddress','_cb=onCompleteFetchUserAddress&_p=profile-address-wrapper',false);
	}
	if (page.id === 'profile_address_form') {
		$('#btn-address-remove').hide();
		if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')=="") doHandlerNotAuthorized();
		else {
			page.querySelector('ons-toolbar .center').innerHTML = mainNav.topPage.data.title;
			$('#hdnAddressDetailAction').val(mainNav.topPage.data.action);
			$('#hdnAddressDetailID').val(mainNav.topPage.data.ID);
			if (mainNav.topPage.data.action == "add") {
				doFetch('global/getState','_cb=onCompleteFetchAddressState&_p=',false);
			} else {
				doFetch('external/getUserAddress','_cb=onCompleteFetchUserAddressDetail&_p=' + mainNav.topPage.data.ID,false);
				$('#btn-address-remove').show();
			}
		}
	}
	if (page.id === 'help') {
		fetchHelpList();
	}
});

function copyVA(text) {
	//cordova.plugins.clipboard.copy(text);
	navigator.clipboard.writeText(text)
	ons.notification.toast(text + ' copied!', {
		timeout: 2000
	});
}

function doLoginByGoogle() {
	window.plugins.googleplus.login(
        {},
        function (obj) {
			if (getCookie(MSG['cookiePrefix']+'IsLoading')!="true") {
				var param = "";
				param += "ID=" + obj.userId;
				param += "&Name=" + obj.displayName;
				param += "&Email=" + obj.email;
				param += "&TokenID=" + obj.idToken;
				doSubmit('external/doAuthGoogle',param);
			} else {
				ons.notification.toast(MSG['onProcess'], {
					timeout: 2000
				});
			}
        },
        function (msg) {
		  ons.notification.toast(msg, {
			timeout: 2000
		});
        }
    );
}

function doRegister() {
	var status = false;
	var message = "";
	if ($('#txtRegName').val() == "") {
		message = "Nama Lengkap tidak boleh kosong";
	} else if ($('#txtRegUsername').val() == "") {
		message = "Username tidak boleh kosong";
	} else if ($('#txtRegPassword').val() == "") {
		message = "Password tidak boleh kosong";
	} else if ($('#txtRegPassword').val() != $('#txtRegPassConfirm').val()) {
		message = "Password dan Konfirmasi Password tidak sama";
	} else {
		status = true;
	}
	if (status) {
		if (getCookie(MSG['cookiePrefix']+'IsLoading')!="true") {
			var param = "";
			param += "txtName=" + $('#txtRegName').val();
			param += "&txtUsername=" + $('#txtRegUsername').val();
			param += "&txtPassword=" + $('#txtRegPassword').val();
			param += "&_cb=doHandleRegister";
			param += "&_p=";
			doSubmit('external/doRegister',param);
		} else {
			ons.notification.toast(MSG['onProcess'], {
				timeout: 2000
			});
		}
	} else {
		ons.notification.toast(message, {
			timeout: 2000
		});
	}
}
function doLogin() {
	var status = false;
	var message = "";
	if ($('#txtLoginUsername').val() == "") {
		message = "Username tidak boleh kosong";
	} else if ($('#txtLoginPassword').val() == "") {
		message = "Password tidak boleh kosong";
	} else {
		status = true;
	}
	if (status) {
		if (getCookie(MSG['cookiePrefix']+'IsLoading')!="true") {
			var param = "";
			param += "txtUsername=" + $('#txtLoginUsername').val();
			param += "&txtPassword=" + $('#txtLoginPassword').val();
			param += "&_p=";
			doSubmit('external/doLogin',param);
		} else {
			ons.notification.toast(MSG['onProcess'], {
				timeout: 2000
			});
		}
	} else {
		ons.notification.toast(message, {
			timeout: 2000
		});
	}
}
function doHandleRegister(data) {
	document.getElementById('mainNav').popPage({animation:'none'});;
}
function onSignIn(googleUser) {
	if (getCookie(MSG['cookiePrefix']+'IsLoading')!="true") {
		var param = "";
		param += "ID=" + googleUser.getBasicProfile().getId();
		param += "&Name=" + googleUser.getBasicProfile().getName();
		param += "&Email=" + googleUser.getBasicProfile().getEmail();
		param += "&TokenID=" + googleUser.getAuthResponse().id_token;
		doSubmit('external/doAuthGoogle',param);
	} else {
		ons.notification.toast(MSG['onProcess'], {
			timeout: 2000
		});
	}
}
function doHandlerLogin(data) {
	setCookie(MSG['cookiePrefix']+'AUTH-TOKEN',data.Token);
	document.querySelector('ons-navigator').resetToPage('main.html');
	ons.notification.toast("Selamat datang "+data.Name, {
		timeout: 2000
	});
}
function doResetPassword() {
	if (getCookie(MSG['cookiePrefix']+'IsLoading')!="true") {
		var param = "";
		param += "txtEmail=" + $('#txtFrmResetEmail').val();
		doSubmit('external/doReset',param);
	} else {
		ons.notification.toast(MSG['onProcess'], {
			timeout: 2000
		});
	}
}
function doLogout() {
	if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')=="") { 
		ons.notification.toast("Kamu perlu Login dulu", {
			timeout: 2000
		});
	} else {
		ons.notification.confirm({
			message: 'Logout dari aplikasi?',
			callback: function(answer) {
				if (answer==1) {
					if (getCookie(MSG['cookiePrefix']+'IsLoading')!="true") {
						doSubmit('external/doLogout');
					} else {
						ons.notification.toast(MSG['onProcess'], {
							timeout: 2000
						});
					}
				}
			}
		});
	}
}
function doHandlerLogout(data) {
	setCookie(MSG['cookiePrefix']+'AUTH-TOKEN','');
	if (data != "") {
		if (data == "google") {
			var auth2 = gapi.auth2.getAuthInstance();
			auth2.disconnect();
			auth2.signOut().then(function () {
				document.querySelector('ons-navigator').resetToPage('main.html');
				ons.notification.toast("Logout berhasil", {
					timeout: 2000
				});
			});
			/*window.plugins.googleplus.disconnect(
				function (msg) {
					ons.notification.toast("Logout berhasil", {
						timeout: 2000
					});
					document.querySelector('ons-navigator').resetToPage('main.html');
				}
			);*/
		} else {
			document.querySelector('ons-navigator').resetToPage('main.html');
			ons.notification.toast("Logout berhasil", {
				timeout: 2000
			});
		}
	}
}
function doHandlerNotAuthorized() {
	ons.notification.toast("Kamu perlu Login dulu ya", {
		timeout: 2000
	});
	setTimeout(function(){ doOpenPage('login.html'); }, 750);
}

/* =====================================================
START : HOME 
=======================================================*/
function runHome() {
	doFetch('external/getBanner','_cb=onCompleteFetchBanner&_p=main-banner-slider-wrapper',false);
	runApp();
	runNotification();
	if ($('#homeApp')[0].childNodes[0].childNodes[5] != undefined) $('#homeApp')[0].childNodes[0].childNodes[5].remove()
	if ($('#homeApp')[0].childNodes[0].childNodes[4] != undefined) $('#homeApp')[0].childNodes[0].childNodes[4].remove()
}
function runNotification() {
	doFetch('external/getNotification','_cb=onCompleteFetchNotification',false);
	doResizeListMenu();
}
function runApp() {
	$('#lblSelBranch').html(getCookie(MSG['cookiePrefix']+'AUTH-BRANCHNAME') == "" ? "Semua Cabang" : getCookie(MSG['cookiePrefix']+'AUTH-BRANCHNAME'));
	doFetch('external/getDiscount','BranchID='+getCookie(MSG['cookiePrefix']+'AUTH-BRANCHID')+'&_cb=onCompleteFetchDiscount&_p=main-discount-slider',false);
	if (getCookie(MSG['cookiePrefix']+'AUTH-BRANCHID') != "") {
		doFetch('external/getAllProduct','CatID=&BranchID='+getCookie(MSG['cookiePrefix']+'AUTH-BRANCHID')+'&Keyword=&_cb=onCompleteFetchAllProduct&_p=main-product-list',false);
	}
}
function onCompleteFetchNotification(data) {
	$('#notifChat').html(data.messageData);
	$('#notifCart').html(data.cartData);
	$('#notifTrans').html(data.orderData);
}
function doSearchProduct(type,str='') {
	if (type == 'search') {
		str = $('#txtSearchQuery').val().replace("'","");
		clearTimeout(timer);
		if (str.length > 2 && valQuery != str) {
			timer = setTimeout(function() {
				valQuery = str;
				doOpenPage('product-search.html',{'CatID':'','BranchID':getCookie(MSG['cookiePrefix']+'AUTH-BRANCHID'),'Keyword':valQuery,'title':'Produk dengan keyword "' + valQuery + '"'});
			}, 750);
		}
	} else {
		doOpenPage('product-search.html',{'CatID':'','BranchID':'','Keyword':str,'title':'Produk dengan keyword "' + str + '"'});
	}
}
function doSearchCategory(CatID, CatName) {
	doOpenPage('product-search.html',{'CatID':CatID,'BranchID':getCookie(MSG['cookiePrefix']+'AUTH-BRANCHID'),'Keyword':'','title':'Produk dalam kategori "' + CatName + '"'});
}
function openBranch() {
	doFetch('external/getBranch','_cb=onCompleteFetchBranch&_p=',false);
}
function onCompleteFetchBranch(data,page) {
	if (data.length > 0) {
		//const arrList = [{'ID':'','label':'Semua Cabang'}];
		const arrList = [];
		for (i=0;i<data.length;i++) {
			arrList.push({'ID':data[i].ID,'label':data[i].Name});
		}
		ons.openActionSheet({
			title: 'Pilih Cabang',
			cancelable: false,
			buttons: arrList
		}).then(function (index) { 
			if (index>=0) {
				setCookie(MSG['cookiePrefix']+'AUTH-BRANCHID',arrList[index].ID);
				setCookie(MSG['cookiePrefix']+'AUTH-BRANCHNAME',arrList[index].label);
				runApp();
				document.querySelector('ons-alert-dialog').remove();
			}
		});
	}
}
function onCompleteFetchBanner(data,page) {
	var html = '';
	var dirPath = (page == "main-banner-slider-wrapper" ? "banner" : 'product');
	if (data.length > 0) {
		var onclick = '';
		html = '<ons-carousel fullscreen swipeable overscrollable auto-scroll auto-scroll-ratio="0.1" id="car-' + dirPath + '" style="width: 98%; border-radius: 10px; margin: auto; height: ' + (page == "main-banner-slider-wrapper" ? "auto" : 'auto') + '">';
		for (i=0;i<data.length;i++) {
			if (dirPath == 'banner') {
				var keyword = data[i].Keyword.replace("'","");
				if (data[i].Keyword != "") onclick = 'onclick="doSearchProduct(\'banner\',\''+keyword+'\')"';
				if (data[i].URL != "") onclick = 'onclick="window.open(\'' + data[i].URL + '\')"';
			}
			var urls = data[i].ImagePath ? uploadedUrl + '/' + dirPath + '/' + data[i].ImagePath : 'dist/img/no_img_wide.png';
			html += '<ons-carousel-item style="height: 100%;" '+ onclick +'>' +
						'<img src="' + urls + '" style="width:100%">' + 
						//'<div style="text-align: center; font-size: 30px; padding-top: 100px; color: rgb(255, 255, 255);">&nbsp;</div>' +
					'</ons-carousel-item>';
		}
		html += '</ons-carousel>';
		html += '<ons-carousel-cover>' +
					'<div class="cover-label">' ;
					for (i=0;i<data.length;i++) {
						if (i == 0) html += '<span class="indicators" id="car-' + dirPath + '-indicator-' + i + '"><i class="fa fa-circle" style="font-size: 5pt; color:rgb(250, 0, 0)"></i></span>&nbsp;';
						else html += '<span class="indicators" id="car-' + dirPath + '-indicator-' + i + '"><i class="fa fa-circle-o" style="font-size: 5pt; color:rgb(250, 0, 0)"></i></span>&nbsp;';
					}
					html += '</div>' +
				'</ons-carousel-covers>';
		$('#'+page).html(html);
		document.querySelector('#car-' + dirPath).addEventListener('postchange', function(event) { 
			$('#car-' + dirPath + '-indicator-' + event.lastActiveIndex).html('<i class="fa fa-circle-o" style="font-size: 5pt; color:rgb(250, 0, 0)"></i></span>&nbsp;'); 
			$('#car-' + dirPath + '-indicator-' + event.activeIndex).html('<i class="fa fa-circle" style="font-size: 5pt; color:rgb(250, 0, 0)"></i></span>&nbsp;'); 
		})
	}
	if (data.length == 0 && dirPath != 'banner') {
		html = '<ons-carousel fullscreen swipeable auto-scroll overscrollable style="width: 100%; height: 250px;">';
		html += '<ons-carousel-item style="width: 100%; height: auto">' +
					'<img src="dist/img/no_img_wide.png" style="width:100%">' + 
				'</ons-carousel-item>';
		html += '</ons-carousel>';
		$('#'+page).html(html);
	}
}
function onCompleteFetchDiscount(data,page) {
	var html = '';
	if (data.length > 0) {
		for (i=0;i<data.length;i++) {
			if (i < 20) {
				var urls = data[i].ImagePath ? uploadedUrl + '/product/' + data[i].ImagePath : 'dist/img/no_img_square.png';
				html += '<li>' +
							'<ons-card style="padding: 6px; width: 140px; border-radius: 10px;" onclick="doOpenPage(\'product-description.html\',{\'ID\':\''+ data[i].ProductID +'\'})">' +
								'<div style="position: relative;">' +
									'<img src="' + urls + '" style="width:100%; height: 160px; border-radius: 6px;">' ;
									if (data[i].Stock == 0) html += '<span id="overlay_text" style="position: relative; left: 20px; top: -90px; font-weight: 500; color: #f1f1f1; width: 100%; background: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 50px">Habis</span>';
						html += '</div>' +	
								'<ons-row>' +
									'<span style="font-size: 10pt; font-weight: 500;">' + (data[i].Product.length > 16 ? data[i].Product.substr(0,16) + "..." : data[i].Product) + '</span>' +
								'</ons-row>' ;
								if (data[i].DiscountType == 1) {
									html +=  '<ons-row>' +
												'<span style="font-size: 9pt; margin-top: 4px; color: grey"><s> Rp ' + doFormatNumber(data[i].Price) + '</s></span>' +
											'</ons-row>' +
											'<ons-row>' +
												'<span style="font-size: 12pt; font-weight: bold; margin-top: 4px;"> Rp ' + doFormatNumber(data[i].Price - data[i].Discount) + '</span>' +
											'</ons-row>' ;
								} else if (data[i].DiscountType == 2) {
									html +=  '<ons-row>' +
												'<span style="font-size: 9pt; margin-top: 4px; color: grey"><s> Rp ' + doFormatNumber(data[i].Price) + '</s></span>' +
											'</ons-row>' +
											'<ons-row>' +
												'<span style="font-size: 12pt; font-weight: bold; margin-top: 4px;"> Rp ' + doFormatNumber(data[i].Price - ((data[i].Price * data[i].Discount)/100)) + '</span>' +
											'</ons-row>' ;
								} else {
									html += '<ons-row>' +
												'<span style="font-size: 12pt; font-weight: bold; margin-top: 4px;"> Rp ' + doFormatNumber(data[i].Price) + '</span>' +
											'</ons-row>' ;
								}
								html +=
								'<ons-row>' +
									'<span style="font-size: 9pt; color: rgb(99, 99, 99);">' +
										'<ons-icon icon="fa-map-marker"></ons-icon> ' + data[i].Branch +
									'</span>' +
								'</ons-row>' +
								'<ons-row>' +
									'<ons-col vertical-align="bottom" width="100px">' +
										'<span style="font-size: 9pt; color: rgb(126, 126, 126); margin-left: 12px;">' +
											'Terjual ' + data[i].ItemSold +
										'</span>' +
									'</ons-col>' +
								'</ons-row>' +
							'</ons-card>' +
						'</li>';
			}
			if (i == 19) {
				html += '<li>' +
							'<ons-card style="padding: 6px; height: 260px; width: 140px; border-radius: 10px;" onclick="doOpenPage(\'discount-search.html\')">' +
								'<ons-row>' +
									'<ons-col vertical-align="bottom" width="100px">' +
										'<div style="padding-top: 100px; margin-left: 12px;"><h3>Lainnya...</h3></div>' +
									'</ons-col>' +
								'</ons-row>' +
							'</ons-card>' +
						'</li>';
			}
		}
		$('#'+page).html(html);
		if ($('#'+page+'-init').val() == "F") {
			$('#'+page).lightSlider({
				controls: false,
				pager: false,
				item: 3,
				autoWidth: true,
				slideMargin: 1,
				enableTouch: data.length >= 3 ? true : false,
				enableDrag: data.length >= 3 ? true : false,
				freeMove: data.length >= 3 ? true : false
			});
			$('#'+page+'-init').val('T');
		} else {
			window.location.reload();
		}
		$('#'+page+'-wrapper').show();
	} else {
		$('#'+page+'-wrapper').hide();
	}
}
function onCompleteFetchCategory(data,page) {
	var html = '';
	if (data.length > 0) {
		for (i=0;i<data.length;i++) {
			var urls = data[i].ImagePath ? uploadedUrl + '/category/' + data[i].ImagePath : 'dist/img/no_img_square.png';
			html += '<li>' +
						'<ons-col style="margin-left: 20px; text-align: center" onclick="doSearchCategory(\''+data[i].ID+'\',\''+data[i].Name+'\')">' +
							'<img src="' + urls + '" width="50px" height="50px" style="padding: 10px;border-radius: 25px;"><br />' +
							//'<ons-row>' +
								'<span style="font-size: 10pt; text-align: center;">' + data[i].Name + '</span>' +
							//'</ons-row>' +
						'</ons-col>' +
					'</li>';
		}
		$('#'+page).html(html);
		$('#'+page).lightSlider({
			controls: false,
			pager: false,
			item: 5,
			autoWidth: true,
			slideMargin: 1,
			enableTouch: data.length >= 5 ? true : false,
			enableDrag: data.length >= 5 ? true : false,
			freeMove: data.length >= 5 ? true : false
		});
	}
}
/*======================================================
END : HOME 
=======================================================*/


/*======================================================
START : PRODUCT 
=======================================================*/
function onCompleteFetchAllProduct(data,page) {
	var maxCol = 2;
	//if (screen.width > 400) maxCol = 3;
	//if (screen.width > 600) maxCol = 4;
	var html = '';
	if (data.length > 0) {
		var x = 1;
		for (i=0;i<data.length;i++) {
			var urls = data[i].ImagePath ? uploadedUrl + '/product/' + data[i].ImagePath : 'dist/img/no_img_square.png';
			if (x == 1) html += '<ons-row>';
			html += '<ons-col>' +
						'<ons-card style="padding: 6px; border-radius: 10px;" class="item-card" onclick="doOpenPage(\'product-description.html\',{\'ID\':\''+ data[i].ProductID +'\'})">' +
							'<div style="position: relative;">' +
								'<img src="' + urls + '" style="width:100%; height: 160px; border-radius: 6px;">' ;
								if (data[i].Stock == 0) html += '<span id="overlay_text" style="position: relative; left: 50px; top: -90px; font-weight: 500; color: #f1f1f1; width: 100%; background: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 50px">Habis</span>';
					html += '</div>' +
							'<ons-row>' +
								'<span style="font-size: 10pt; font-weight: 500;">' + data[i].Product + '</span>' +
							'</ons-row>' ;
							if (data[i].DiscountType == 1) {
								html +=  '<ons-row>' +
											'<span style="font-size: 9pt; margin-top: 4px; color: grey"><s> Rp ' + doFormatNumber(data[i].Price) + '</s></span>' +
										'</ons-row>' +
										'<ons-row>' +
											'<span style="font-size: 12pt; font-weight: bold; margin-top: 4px;"> Rp ' + doFormatNumber(data[i].Price - data[i].Discount) + '</span>' +
										'</ons-row>' ;
							} else if (data[i].DiscountType == 2) {
								html +=  '<ons-row>' +
											'<span style="font-size: 9pt; margin-top: 4px; color: grey"><s> Rp ' + doFormatNumber(data[i].Price) + '</s></span>' +
										'</ons-row>' +
										'<ons-row>' +
											'<span style="font-size: 12pt; font-weight: bold; margin-top: 4px;"> Rp ' + doFormatNumber(data[i].Price - ((data[i].Price * data[i].Discount)/100)) + '</span>' +
										'</ons-row>' ;
							} else {
								html += '<ons-row>' +
											'<span style="font-size: 12pt; font-weight: bold; margin-top: 4px;"> Rp ' + doFormatNumber(data[i].Price) + '</span>' +
										'</ons-row>' ;
							}
							html +=
							'<ons-row>' +
								'<span style="font-size: 9pt; color: rgb(99, 99, 99);">' +
									'<ons-icon icon="fa-map-marker"></ons-icon> ' + data[i].Branch +
								'</span>' +
							'</ons-row>' +
							'<ons-row>' +
								'<ons-col vertical-align="bottom" width="100px">' +
									'<span style="font-size: 9pt; color: rgb(126, 126, 126); margin-left: 12px;">' +
										'Terjual ' + data[i].ItemSold +
									'</span>' +
								'</ons-col>' +
							'</ons-row>' +
						'</ons-card>' +
					'</ons-col>';
			if (x == 1 && (i+1) == data.length) html += '<ons-col></ons-col>';
			if (x == maxCol || (i+1) == data.length) html += '</ons-row>';
			x++;
			if (x == (maxCol + 1)) x = 1;
		}
	}
	html += "<br /><br /><br />";
	$('#'+page).html(html);
}
function onCompleteFetchProduct(data,ID) {
	$('#lblProductPriceBeforeWrapper').show();
	if (data.DiscountType == 1) {
		$('#lblProductPriceBefore').html("<s>Rp " + doFormatNumber(data.Price) + "</s>");
		$('#lblProductPrice').html("Rp " + doFormatNumber(data.Price - data.Discount));
	} else if (data.DiscountType == 2) {
		$('#lblProductPriceBefore').html("<s>Rp " + doFormatNumber(data.Price) + "</s>");
		$('#lblProductPrice').html("Rp " + doFormatNumber(data.Price - ((data.Price * data.Discount)/100)));
	} else {
		$('#lblProductPriceBeforeWrapper').hide();
		$('#lblProductPrice').html("Rp " + doFormatNumber(data.Price));
	}
	fetchProductPrice(data.ProductID);
	$('#lblProductName').html(data.Product);
	//$('#lblProductMinOrder').html("Harga Grosir - Minimal Pembelian " + data.MinOrder);
	$('#lblProductMinOrder').hide();
	$('#lblProductBranch').html(data.Branch);
	$('#lblProductDesc').html(data.Description.replace(/(?:\r\n|\r|\n)/g, '<br>'));
	$('#hdnProductDetailID').val(data.ProductID);
	$('#hdnProductBranchID').val(data.BranchID);
	$('#hdnProductStock').val(data.Stock);
	if (data.Stock == 0) {
		ons.notification.toast("Maaf, Stok produk ini sedang kosong", {
			timeout: 2000
		});
	} 
	$("#btnBuy").attr("disabled", false);
	$("#btnChat").attr("disabled", false);
}
function fetchProductPrice(ProductID) {
	doFetch('external/getProductPrice','_cb=onCompleteFetchProductPrice&ProductID=' + ProductID + '&_p=',false);
}
function onCompleteFetchProductPrice(data) {
	var html = '';
	for (i=0;i<data.length;i++) {
		html += '<p style="margin-top: 18px;">' +
					'<span style="margin-left: 6%;">Pembelian ' + data[i].MinOrder + ' hingga ' + data[i].MaxOrder + ' Pcs</span>' +
					'<span style="margin-right: 10%; float: right;">Rp ' + doFormatNumber(data[i].Price) + '</span>' +
				'</p>';
	}
	$('#dvProductPriceTable').html(html);
}
function onCompleteFetchProductDefAddress(data,ID) {
	if (data.length > 0) {
		$('#lblProductDefaultAddress').html(data[0].DistrictName);
	}
}
function openMainAddress(callfrom) {
	if (callfrom=="cart") {
		//$('#lblDeliveryFee').html('...');
		//$('#lblTotalPayment').html('...');
	}
	doFetch('external/getUserAddress','_cb=onCompleteFetchMainAddress&_p='+callfrom,false);
}
function onCompleteFetchMainAddress(data,callfrom) {
	if (data.length > 0) {
		const arrList = [];
		for (i=0;i<data.length;i++) {
			arrList.push({'ID':data[i].ID,'label':data[i].Name + " (" +data[i].DistrictName+ ")"});
		}
		ons.openActionSheet({
			title: 'Pilih Alamat',
			cancelable: true,
			buttons: arrList
		}).then(function (index) { 
			if (index>=0) {
				if (getCookie(MSG['cookiePrefix']+'IsLoading')!="true") {
					var param = "hdnFrmID=" + arrList[index].ID;
					param += "&_cb=" + callfrom;
					doSubmit('external/doSetPrimaryAddress',param);
				} else {
					ons.notification.toast(MSG['onProcess'], {
						timeout: 2000
					});
				}
			}
		});
	} else {
		ons.notification.toast("Tambahkan alamat dulu ya", {
			timeout: 2000
		});
		setTimeout(function(){ doOpenPage('profile_address.html'); }, 750);
	}
}
function doChatBranch() {
	doOpenPage('chat-details.html',{'title':$('#lblProductBranch').html(),'BranchID':$('#hdnProductBranchID').val()});
}
/*======================================================
END : PRODUCT 
=======================================================*/


/*======================================================
START : CART 
=======================================================*/
function doUpdateStepper(ID,Type) {
	var min = parseInt($('#qty-'+ID).attr("min"));
	var max = parseInt($('#qty-'+ID).attr("max"));
	var step = parseInt($('#qty-'+ID).attr("step"));
	var value = parseInt($('#qty-'+ID).val());
	if (Type == "up") {
		if ((value + step) > max) $('#qty-'+ID).val(value);
		else $('#qty-'+ID).val((value + step));
	}
	if (Type == "down") {
		if (value == 1) doClearProduct(ID);
		else $('#qty-'+ID).val((value - step));
	}
}

function doHandlerSaveCartNew(isvalid) {
	if (isvalid) {
		doOpenPage('cart-payment.html');
	}
}
function doConfirmCart() {
	if (getCookie(MSG['cookiePrefix']+'IsLoading')=="true") {
		setCookie(MSG['cookiePrefix']+'IsLoading',"false");
	}
	if (getCookie(MSG['cookiePrefix']+'IsLoading')!="true") {
		var param = "Source=cart";
		$('.cartItems').each(function(i, obj) {
			var ID = $(this).data("id");
			param += "&ProductID[]=" + ID;
			param += "&Qty[]=" + $('#qty-' + ID).val();
			param += "&Notes[]=" + $('#notes-' + ID).val();
		});	
		if (param != "Source=cart") doSubmit('external/doUpdateCart',param);
	} else {
		ons.notification.toast(MSG['onProcess'], {
			timeout: 2000
		});
	}
}
function onCompleteFetchCart_new(data,page) {
	var html = '';
	if (data.length > 0) {
		for (i=0;i<data.length;i++) {
			var urls = data[i].ImagePath ? uploadedUrl + '/product/' + data[i].ImagePath : 'dist/img/no_img_square.png';
			var price = data[i].Price;
			if (data[i].DiscountType == 1) {
				price = data[i].Price - data[i].Discount;
			} else if (data[i].DiscountType == 2) {
				price = data[i].Price - ((data[i].Price * data[i].Discount)/100);
			}
			html += '<div id="wrapper-'+ data[i].ProductID +'">';
			html += '<ons-list-item modifier="nodivider">' +
						'<ons-row>' +
							'<div class="left" style="display: flex; margin-right: 4%;">' +
								'<img src="' + urls + '" style="width: 80px; height: 80px; border-radius: 20%; margin-left: 6px;">' +
							'</div>' +
							'<ons-col style="margin-top: 10px; margin-right: 10%; align-items: center;">' +
								'<span>' + data[i].Product + '</span><br />';
						if (data[i].DiscountType > 0) html += '<span style="font-size: 9pt; color: grey;"><s>Rp. ' + doFormatNumber(data[i].Price) + '</s></span><br />';
						html += '<span style="font-size: 12pt; color: rgb(3, 0, 0); font-weight: bold;">Rp. ' + doFormatNumber(price) + '</span><br />' +
								'<span style="font-size: 9pt; color: rgb(99, 99, 99);"><ons-icon icon="fa-map-marker"></ons-icon> ' + data[i].Branch + '</span>' +
							'</ons-col>' +
						'</ons-row>' +
					'</ons-list-item>' +
					'<ons-list-item modifier="nodivider">' +
						'<div class="left"><span style="color:grey">Jumlah item</span></div>' +
						'<div class="right">' +
							'<span>' +
								'<button onclick="doClearProduct(\''+ data[i].ProductID +'\')"><ons-icon icon="md-delete"></ons-icon></button>' +
								'<button onclick="doUpdateStepper(\''+ data[i].ProductID +'\',\'down\')">–</button>' +
								'<input type="number" class="cartItems" id="qty-'+ data[i].ProductID +'" value="'+ data[i].Qty +'" data-id="'+ data[i].ProductID +'" min="0" max="1000" step="1" >' +
								'<button onclick="doUpdateStepper(\''+ data[i].ProductID +'\',\'up\')">+</button>' +
							'</span>' +
						'</div>' +
					'</ons-list-item>' +
					'<ons-list-item modifier="nodivider">' +
						'<ons-input style="width: 95%;" id="notes-'+ data[i].ProductID +'" modifier="underbar" type="text" placeholder="Catatan Pesanan" float value="'+ data[i].Notes +'"></ons-input>' +
					'</ons-list-item>' ;
			html += '</div>';
		}
		html += '<ons-button style="text-align:center; margin-top:50px; width: 100%; height: 50px; padding: 8px; background-color: tomato; font-weight: 500; border-radius: 0px;" onclick="doConfirmCart()" id="btnConfirmCart">Selanjutnya</ons-button>';
	} else {
		html = '<p style="text-align:center;padding-top:200px;color:tomato"><ons-icon icon="fa-shopping-cart" style="font-size:36px"></ons-icon><br /><br />Keranjang belanja Anda kosong</p>';
	}
	$('#'+page).html(html);
}
function onCompleteFetchCartFinal(data,page) {
	if (getCookie(MSG['cookiePrefix']+'IsLoading')=="true") {
		setCookie(MSG['cookiePrefix']+'IsLoading',"false");
	}
	if (getCookie(MSG['cookiePrefix']+'IsCalculateQty')=="true") {
		setCookie(MSG['cookiePrefix']+'IsCalculateQty',"false");
	}
	var html = '';
	if (data.length > 0) {
		doFetch('external/getUserAddress','_cb=onCompleteFetchCartDefAddress&_p=',false);
		var subTotal = 0;
		for (i=0;i<data.length;i++) {
			var urls = data[i].ImagePath ? uploadedUrl + '/product/' + data[i].ImagePath : 'dist/img/no_img_square.png';
			var price = data[i].Price;
			if (data[i].DiscountType == 1) {
				price = data[i].Price - data[i].Discount;
			} else if (data[i].DiscountType == 2) {
				price = data[i].Price - ((data[i].Price * data[i].Discount)/100);
			}
			subTotal += parseInt(price) * parseInt(data[i].Qty);
			html += '<ons-list-item modifier="nodivider">' +
						'<ons-row>' +
							'<div class="left" style="display: flex; margin-right: 4%;">' +
								'<img src="' + urls + '" style="width: 80px; height: 80px; border-radius: 20%; margin-left: 6px;">' +
							'</div>' +
							'<ons-col style="margin-top: 10px; margin-right: 10%; align-items: center;">' +
								'<span>' + data[i].Product + '</span><br />';
						if (data[i].DiscountType > 0) html += '<span style="font-size: 9pt; color: grey;"><s>Rp. ' + doFormatNumber(data[i].Price) + '</s></span><br />';
						html += '<span style="font-size: 12pt; color: rgb(3, 0, 0); font-weight: bold;">Rp. ' + doFormatNumber(price) + '</span> x ' + data[i].Qty + '<br />' +
								'<span style="font-size: 9pt; color: rgb(99, 99, 99);"><ons-icon icon="fa-map-marker"></ons-icon> ' + data[i].Branch + '</span><br />' +
								'<span style="font-size: 9pt; color: rgb(99, 99, 99);">Catatan:<br /><i> ' + data[i].Notes + '</i></span>' +
							'</ons-col>' +
						'</ons-row>' +
					'</ons-list-item>';
		}
		html += '<div style="border: 5px solid rgb(238, 238, 238); margin-top: 10px;"></div>' +
				'<p style="margin-top: 18px;">' +
					'<span style="font-size: 12pt;font-weight: bold; margin-left: 6%;">Pengiriman</span>' +
				'</p>' +
				'<p style="margin-top: 18px; margin-bottom: 20px;">' +
					'<span style="margin-left: 6%; color:grey">Dikirim ke alamat:</span><br />' +
					'<div style="margin-left: 6%;" id="lblCartDefaultAddress"></div>' +
					'<span style="margin-right: 10%; float: right; color:tomato; margin-top: -10px"; onclick="openMainAddress(\'cart\')">&nbsp;(Ubah)</span>' +
				'</p>' +

				'<div style="border: 5px solid rgb(238, 238, 238); margin-top: 10px;"></div>' +
				'<p style="margin-top: 18px;">' +
					'<span style="font-size: 12pt;font-weight: bold; margin-left: 6%;">Pembayaran</span>' +
				'</p>' +
				'<p style="margin-top: 18px;">' +
					'<span style="margin-left: 6%;">Total harga Produk</span>' +
					'<span style="float: right; margin-right: 6%;" id="lblSubTotal">Rp ' + doFormatNumber(subTotal) + '</span>' +
				'</p>' +

				'<div id="lblDeliveryFeeWrapper">' +
					'<p style="margin-top: 18px;">' +
						'<span style="margin-left: 6%;">Biaya kirim</span>' +
						'<span style="float: right; margin-right: 6%;" id="lblDeliveryFee"> ... </span>' +
					'</p>' +
				'</div>' +

				'<div style="margin-left: 6%; border: 1px solid tomato; margin-top: 12px; width: 90%;"></div>' +
				'<p style="margin-top: 12px;">' +
					'<span style="margin-left: 6%;">Total Bayar</span>' +
					'<span style="font-size: 16pt; font-weight: bolder; float: right; margin-right: 6%; color: tomato" id="lblTotalPayment"> ... </span>' +
				'</p>' +

				'<div style="border: 5px solid rgb(238, 238, 238); margin-top: 10px;"></div>' +
				'<p style="margin-top: 20px;">' +
					'<span style="margin-left: 6%;">Metode Pembayaran</span>' +
					'<span id="lblCartPaymentMethod" style="margin-right: 5%; float: right;">-</span><br />' +
					'<span style="margin-right: 5%; float: right; color:tomato;" onclick="doFetchPaymentMethod()">&nbsp;(Pilih)</span>' +
				'</p>' +
				
				'<input type="hidden" id="hdnTotalPayment" value="0">' +
				'<input type="hidden" id="hdnPaymentMethod" value="-">' +
				'<ons-button style="text-align:center; margin-top:50px; width: 100%; height: 50px; padding: 8px; background-color: tomato; font-weight: 500; border-radius: 0px;" onclick="doPay()" id="btnPay">Bayar</ons-button>';
			$('#lblTotalPayment').html('-');
			doFetch('external/doCalculateDelivery','_cb=onCompleteFetchCartCalculateDelivery&_p='+subTotal,false);
	} else {
		html = '<p style="text-align:center;padding-top:200px;color:tomato"><ons-icon icon="fa-shopping-cart" style="font-size:36px"></ons-icon><br /><br />Keranjang belanja Anda kosong</p>';
	}
	$('#'+page).html(html);
}




function onCompleteFetchCart(data,page) {
	if (getCookie(MSG['cookiePrefix']+'IsLoading')=="true") {
		setCookie(MSG['cookiePrefix']+'IsLoading',"false");
	}
	if (getCookie(MSG['cookiePrefix']+'IsCalculateQty')=="true") {
		setCookie(MSG['cookiePrefix']+'IsCalculateQty',"false");
	}
	var html = '';
	if (data.length > 0) {
		doFetch('external/getUserAddress','_cb=onCompleteFetchCartDefAddress&_p=',false);
		var subTotal = 0;
		for (i=0;i<data.length;i++) {
			var urls = data[i].ImagePath ? uploadedUrl + '/product/' + data[i].ImagePath : 'dist/img/no_img_square.png';
			var price = data[i].Price;
			if (data[i].DiscountType == 1) {
				price = data[i].Price - data[i].Discount;
			} else if (data[i].DiscountType == 2) {
				price = data[i].Price - ((data[i].Price * data[i].Discount)/100);
			}
			subTotal += parseInt(price) * parseInt(data[i].Qty);
			html += '<div id="wrapper-'+ data[i].ProductID +'">';
			html += '<ons-list-item modifier="nodivider">' +
						'<ons-row>' +
							'<div class="left" style="display: flex; margin-right: 4%;">' +
								'<img src="' + urls + '" style="width: 80px; height: 80px; border-radius: 20%; margin-left: 6px;">' +
							'</div>' +
							'<ons-col style="margin-top: 10px; margin-right: 10%; align-items: center;">' +
								'<span>' + data[i].Product + '</span><br />';
						if (data[i].DiscountType > 0) html += '<span style="font-size: 9pt; color: grey;"><s>Rp. ' + doFormatNumber(data[i].Price) + '</s></span><br />';
						html += '<span style="font-size: 12pt; color: rgb(3, 0, 0); font-weight: bold;">Rp. ' + doFormatNumber(price) + '</span><br />' +
								'<span style="font-size: 9pt; color: rgb(99, 99, 99);"><ons-icon icon="fa-map-marker"></ons-icon> ' + data[i].Branch + '</span>' +
							'</ons-col>' +
						'</ons-row>' +
					'</ons-list-item>' +
					'<ons-list-item modifier="nodivider">' +
						'<div class="left"><span style="color:grey">Jumlah item</span></div>' +
						'<div class="right">' +
							'<span>' +
								'<button onclick="doClearProduct(\''+ data[i].ProductID +'\');"><ons-icon icon="md-delete"></ons-icon></button>' +
								'<button onclick="doUpdateStepper(\''+ data[i].ProductID +'\',\'down\');doCartUpdateQty(\''+ data[i].ProductID +'\')">–</button>' +
								'<input type="number" id="qty-'+ data[i].ProductID +'" value="'+ data[i].Qty +'" min="0" max="1000" step="1" onblur="doCartUpdateQty(\''+ data[i].ProductID +'\')">' +
								'<button onclick="doUpdateStepper(\''+ data[i].ProductID +'\',\'up\');doCartUpdateQty(\''+ data[i].ProductID +'\')">+</button>' +
							'</span>' +
						'</div>' +
					'</ons-list-item>' +
					'<ons-list-item modifier="nodivider">' +
						'<ons-input style="width: 95%;" id="notes-'+ data[i].ProductID +'" modifier="underbar" type="text" placeholder="Catatan Pesanan" float value="'+ data[i].Notes +'" onkeyup="doCartUpdateNotes(\''+ data[i].ProductID +'\')"></ons-input>' +
					'</ons-list-item>' ;
			html += '</div>';
		}
		html += '<div style="border: 5px solid rgb(238, 238, 238); margin-top: 10px;"></div>' +
				'<p style="margin-top: 18px;">' +
					'<span style="font-size: 12pt;font-weight: bold; margin-left: 6%;">Pengiriman</span>' +
				'</p>' +
				'<p style="margin-top: 18px; margin-bottom: 20px;">' +
					'<span style="margin-left: 6%; color:grey">Dikirim ke alamat:</span><br />' +
					'<div style="margin-left: 6%;" id="lblCartDefaultAddress"></div>' +
					'<span style="margin-right: 10%; float: right; color:tomato; margin-top: -10px"; onclick="openMainAddress(\'cart\')">&nbsp;(Ubah)</span>' +
				'</p>' +

				'<div style="border: 5px solid rgb(238, 238, 238); margin-top: 10px;"></div>' +
				'<p style="margin-top: 18px;">' +
					'<span style="font-size: 12pt;font-weight: bold; margin-left: 6%;">Pembayaran</span>' +
				'</p>' +
				'<p style="margin-top: 18px;">' +
					'<span style="margin-left: 6%;">Total harga Produk</span>' +
					'<span style="float: right; margin-right: 6%;" id="lblSubTotal">Rp ' + doFormatNumber(subTotal) + '</span>' +
				'</p>' +

				'<div id="lblDeliveryFeeWrapper">' +
					'<p style="margin-top: 18px;">' +
						'<span style="margin-left: 6%;">Biaya kirim</span>' +
						'<span style="float: right; margin-right: 6%;" id="lblDeliveryFee"> ... </span>' +
					'</p>' +
				'</div>' +

				'<div style="margin-left: 6%; border: 1px solid tomato; margin-top: 12px; width: 90%;"></div>' +
				'<p style="margin-top: 12px;">' +
					'<span style="margin-left: 6%;">Total Bayar</span>' +
					'<span style="font-size: 16pt; font-weight: bolder; float: right; margin-right: 6%; color: tomato" id="lblTotalPayment"> ... </span>' +
				'</p>' +

				'<div style="border: 5px solid rgb(238, 238, 238); margin-top: 10px;"></div>' +
				'<p style="margin-top: 20px;">' +
					'<span style="margin-left: 6%;">Metode Pembayaran</span>' +
					'<span id="lblCartPaymentMethod" style="margin-right: 5%; float: right;">-</span><br />' +
					'<span style="margin-right: 5%; float: right; color:tomato;" onclick="doFetchPaymentMethod()">&nbsp;(Pilih)</span>' +
				'</p>' +
				
				'<input type="hidden" id="hdnTotalPayment" value="0">' +
				'<input type="hidden" id="hdnPaymentMethod" value="-">' +
				'<ons-button style="text-align:center; margin-top:50px; width: 100%; height: 50px; padding: 8px; background-color: tomato; font-weight: 500; border-radius: 0px;" onclick="doPay()" id="btnPay">Bayar</ons-button>';
			$('#lblTotalPayment').html('-');
			doFetch('external/doCalculateDelivery','_cb=onCompleteFetchCartCalculateDelivery&_p='+subTotal,false);
	} else {
		html = '<p style="text-align:center;padding-top:200px;color:tomato"><ons-icon icon="fa-shopping-cart" style="font-size:36px"></ons-icon><br /><br />Keranjang belanja Anda kosong</p>';
	}
	$('#'+page).html(html);
}
function onCompleteFetchCartDefAddress(data,ID) {
	if (data.length > 0) {
		$('#lblCartDefaultAddress').html(data[0].Name + "<br />" + data[0].Address + "<br />" + data[0].StateName + ", " + data[0].CityName + ", " + data[0].DistrictName + "<br />" + data[0].PostalCode);
	}
}
function doHandlerDeliveryFeeIsNull() {
	ons.notification.toast("Maaf, tidak bisa menghitung Biaya kirim ke alamat kamu, silahkan kontak admin", {
		timeout: 5000
	});
	$('#hdnTotalPayment').val(0);
}
function onCompleteFetchCartCalculateDelivery(data,subtotal) {
	if (data.length > 0) {
		var html = '';
		var subtotal = parseInt(subtotal);
		var deliveryFee = 0;
		var isValid = true;
		for (i=0;i<data.length;i++) {
			deliveryFee += parseInt(data[i].Fee);
			html += '<p style="margin-top: 18px;">' +
						'<span style="margin-left: 6%;">Biaya kirim (' + data[i].Branch + ')</span>' +
						'<span style="float: right; margin-right: 6%;">'+(data[i].IsFound == 0 ? '-' : (data[i].Fee == 0 ? 'Bebas Ongkir' : 'Rp ' + doFormatNumber(data[i].Fee)))+'</span>' +
					'</p>';
			if (data[i].IsFound == 0) isValid = false;
		}
		$('#lblDeliveryFeeWrapper').html(html);
		$('#lblTotalPayment').html('Rp ' + doFormatNumber(subtotal + deliveryFee));
		//if (!isValid) doHandlerDeliveryFeeIsNull();
		if (!isValid) {
			$('#hdnTotalPayment').val(0);
		} else {
			$('#hdnTotalPayment').val(subtotal + deliveryFee);
		}
	} else doHandlerDeliveryFeeIsNull();
}
function doAddCart() {
	if ($('#hdnProductStock').val() == "0") {
		ons.notification.toast("Maaf, Stok produk ini sedang kosong", {
			timeout: 2000
		});
	} else {
		if (getCookie(MSG['cookiePrefix']+'IsLoading')!="true") {
			var param = "";
			param += "ProductID=" + $('#hdnProductDetailID').val();
			param += "&Qty=1";
			param += "&Notes=";
			param += "&Source=product";
			doSubmit('external/doSaveCart',param);
		} else {
			ons.notification.toast(MSG['onProcess'], {
				timeout: 2000
			});
		}
	}
}
function doUpdateCart(ID) {
	$('#lblSubTotal').html('...');
	$('#lblTotalPayment').html('...');
	if (getCookie(MSG['cookiePrefix']+'IsLoading')!="true") {
		var param = "";
		param += "ProductID=" + ID;
		param += "&Qty=" + $('#qty-' + ID).val();
		param += "&Notes=" + $('#notes-' + ID).val();
		param += "&Source=cart";
		doSubmit('external/doSaveCart',param);
	} else {
		ons.notification.toast(MSG['onProcess'], {
			timeout: 2000
		});
	}
}
function doClearProduct(ID) {
	ons.notification.confirm({
		message: 'Hapus produk ini dari keranjang?',
		callback: function(answer) {
			if (answer==1) {
				$('#qty-' + ID).val('0');
				$('#wrapper-' + ID).remove();
				doUpdateCart(ID)
				if ($('.cartItems').length == 0) {
					$('#cartItemWrapper').html('<p style="text-align:center;padding-top:200px;color:tomato"><ons-icon icon="fa-shopping-cart" style="font-size:36px"></ons-icon><br /><br />Keranjang belanja Anda kosong</p>');
				}
			}
		}
	});
}
function doCartUpdateQty(ID) {
	if (getCookie(MSG['cookiePrefix']+'IsCalculateQty')!="true") {
		setCookie(MSG['cookiePrefix']+'IsCalculateQty',"true");
	}
	var str = $('#qty-' + ID).val();
	clearTimeout(timer);
	if (valQuery != str) {
		timer = setTimeout(function() {
			valQuery = str;
			doUpdateCart(ID)
		}, 750);
	}
}
function doCartUpdateNotes(ID) {
	var str = $('#notes-' + ID).val().replace("'","");
	clearTimeout(timer);
	if (str.length > 2 && valQuery != str) {
		timer = setTimeout(function() {
			valQuery = str;
			doUpdateCart(ID)
		}, 750);
	}
}
function doHandlerSaveCart(source,isvalid) {
	if (isvalid) {
		if(source=="product") {
			document.querySelector('ons-navigator').resetToPage('main.html');setTimeout(function(){ doOpenPage('cart.html') }, 750);
		}
	}
	if(source=="cart") {
		//doFetch('external/getCart','_cb=onCompleteFetchCart&_p=cartItemWrapper',false);
	}
}
function doFetchPaymentMethod() {
	doFetch('external/getPaymentMethod','_cb=onCompleteFetchPaymentMethod&_p=',false);
}
function onCompleteFetchPaymentMethod(data,subtotal) {
	if (data.length > 0) {
		const arrList = [];
		for (i=0;i<data.length;i++) {
			var label = '<img class="list-item__thumbnail" style="float:left" src="' + uploadedUrl + '/' + data[i].ImagePath + '"><span style="margin-left:10px">' + data[i].ID.split("|")[1] + '</span>';
			arrList.push({'ID':data[i].ID.split("|")[0],'label':label,'name':data[i].ID.split("|")[1] });
		}
		ons.openActionSheet({
			title: 'Pilih Metode Pembayaran',
			cancelable: true,
			buttons: arrList
		}).then(function (index) { 
			if (index>=0) {
				$('#hdnPaymentMethod').val(arrList[index].ID);
				$('#lblCartPaymentMethod').html(arrList[index].name);
			}
		});
	}
}
function doPay() {
	if ($('#hdnTotalPayment').val() == "0") {
		if (document.querySelector('ons-toast') == null) {
			ons.notification.toast("Maaf, tidak bisa menghitung Biaya kirim ke alamat kamu, silahkan kontak admin", {
				timeout: 5000
			});
		}
	} else if ($('#hdnPaymentMethod').val() == "-") {
		ons.notification.toast("Silahkan pilih Metode Pembayaran terlebih dahulu", {
			timeout: 5000
		});
	} else {
		if (getCookie(MSG['cookiePrefix']+'IsCalculateQty')=="true") {
			ons.notification.toast(MSG['onProcess'], {
				timeout: 2000
			});
		} else {
			if (getCookie(MSG['cookiePrefix']+'IsLoading')!="true") {
				setCookie(MSG['cookiePrefix']+'IsLoading',"true");
				var param = "paymentMethod=" + $('#hdnPaymentMethod').val();
				doSubmit('external/doPay',param);
			} else {
				ons.notification.toast(MSG['onProcess'], {
					timeout: 2000
				});
			}
		}
	}
}
function onCompleteDoPay(data) {
	document.querySelector('ons-navigator').resetToPage('main.html');
	setTimeout(function(){ doOpenPage('cart-completed.html',data) }, 750);
}
/*======================================================
END : CART 
=======================================================*/


/*======================================================
START : PROFILE 
=======================================================*/
function onCompleteFetchUserDetail(data,ID) {
	$('#lblProfileName').html(data.Name);
	$('#lblProfilePhone').html(data.Phone);
	$('#lblProfileEmail').html(data.Email);
}
function onCompleteFetchUserDetailEdit(data,ID) {
	$('#txtAccountName').val(data.Name);
	$('#txtAccountPhone').val(data.Phone);
	$('#txtAccountEmail').val(data.Email);
}
function doUpdateAccount() {
	if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')=="") doHandlerNotAuthorized();
	else {
		var status = false;
		var message = "";
		if ($('#txtAccountName').val() == "") {
			message = "Nama Lengkap tidak boleh kosong";
		} else if ($('#txtFrmPhone').val() == "") {
			message = "No. Telepon tidak boleh kosong";
		} else if ($('#txtAccountEmail').val() == "") {
			message = "Email tidak boleh kosong";
		} else {
			status = true;
		}
		if (status) {
			if (getCookie(MSG['cookiePrefix']+'IsLoading')!="true") {
				var param = "";
				param += "txtFrmName=" + $('#txtAccountName').val();
				param += "&txtFrmPhone=" + $('#txtAccountPhone').val();
				param += "&txtFrmEmail=" + $('#txtAccountEmail').val();
				doSubmit('external/doUpdateUser',param);
			} else {
				ons.notification.toast(MSG['onProcess'], {
					timeout: 2000
				});
			}
		} else {
			ons.notification.toast(message, {
				timeout: 2000
			});
		}
	}
}
function doHandlerUpdateUser() {
	doFetch('external/getUser','_cb=onCompleteFetchUserDetail&_p=',false);document.getElementById('mainNav').popPage({animation:'none'});;
}


function onCompleteFetchAddressState(data,ID) {
	var html = '<option value="">Silahkan Pilih</option>';
	if (data.length > 0) {
		for (i=0;i<data.length;i++) {
			html += '<option value="' + data[i].ID + '" ' + (data[i].ID == ID ? "selected" : "") + ' >' + data[i].Name + '</option>';
		}
		$('#selFrmAddressState').html(html);
	}
}
function doFetchAddressCity(stateID,callback,selectedCity="") {
	doFetch('global/getCity','stateID=' + stateID + '&_cb=' + callback + '&_p=' + selectedCity,false);
}
function onCompleteFetchAddressCity(data,ID) {
	var html = '<option value="">Silahkan Pilih</option>';
	if (data.length > 0) {
		for (i=0;i<data.length;i++) {
			html += '<option value="' + data[i].ID + '" ' + (data[i].ID == ID ? "selected" : "") + ' >' + data[i].Name + '</option>';
		}
		$('#selFrmAddressCity').html(html);
	}
}
function doFetchAddressDistrict(cityID,callback,selectedDistrict="") {
	doFetch('global/getDistrict','cityID=' + cityID + '&_cb=' + callback + '&_p=' + selectedDistrict,false);
}
function onCompleteFetchAddressDistrict(data,ID) {
	var html = '<option value="">Silahkan Pilih</option>';
	if (data.length > 0) {
		for (i=0;i<data.length;i++) {
			html += '<option value="' + data[i].ID + '" ' + (data[i].ID == ID ? "selected" : "") + ' >' + data[i].Name + '</option>';
		}
		$('#selFrmAddressDistrict').html(html);
	}
}
function onCompleteFetchUserAddress(data,page) {
	var html = '';
	if (data.length > 0) {
		for (i=0;i<data.length;i++) {
			html += '<ons-card onclick="doOpenPage(\'profile_address_form.html\',{\'title\':\'Ubah Alamat\',\'action\':\'edit\',\'ID\':\''+ data[i].ID +'\'})">' +
					'<p><b>' + data[i].Name + '</b></p>' +
					'<p>' + data[i].Phone + '</p>' +
					'<p>' + data[i].Address + '<br />' + data[i].CityName + ', ' + data[i].DistrictName + '<br />' + data[i].PostalCode + '</p>' + 
					'</ons-card>';
		}
	}
	$('#'+page).html(html);
}
function onCompleteFetchUserAddressDetail(data,ID) {
	var html = '';
	if (data.length > 0) {
		for (i=0;i<data.length;i++) {
			if (data[i].ID == ID) {
				$('#txtFrmAddressLabel').val(data[i].Name);
				$('#txtFrmAddressPhone').val(data[i].Phone);
				doFetch('global/getState','_cb=onCompleteFetchAddressState&_p=' + data[i].StateID,false);
				doFetchAddressCity(data[i].StateID,'onCompleteFetchAddressCity',data[i].CityID)
				doFetchAddressDistrict(data[i].CityID,'onCompleteFetchAddressDistrict',data[i].DistrictID)
				$('#txtFrmAddressPostalCode').val(data[i].PostalCode);
				$('#txtFrmAddressDetail').val(data[i].Address);
				$('#chkDefaultAddress').prop('checked', (data[i].IsDefault == "1" ? true : false));
				$('#hdnAddressDetailID').val(ID);
			}
		}
	}
}
function doSaveAddress() {
	if (getCookie(MSG['cookiePrefix']+'AUTH-TOKEN')=="") doHandlerNotAuthorized();
	else {
		var status = false;
		var message = "";
		if ($('#txtFrmAddressLabel').val() == "") {
			message = "Label Alamat tidak boleh kosong";
		} else if ($('#txtFrmAddressPhone').val() == "") {
			message = "No. Telepon tidak boleh kosong";
		} else if ($('#selFrmAddressState').val() == "") {
			message = "Provinsi harus dipilih";
		} else if ($('#selFrmAddressCity').val() == "") {
			message = "Kota/Kabupaten harus dipilih";
		} else if ($('#selFrmAddressDistrict').val() == "") {
			message = "Kecamatan harus dipilih";
		//} else if ($('#txtFrmAddressPostalCode').val() == "") {
		//	message = "Kode Pos tidak boleh kosong";
		} else if ($('#txtFrmAddressDetail').val() == "") {
			message = "Detail Alamat tidak boleh kosong";
		} else {
			status = true;
		}
		if (status) {
			if (getCookie(MSG['cookiePrefix']+'IsLoading')!="true") {
				var param = "";
				param += "txtAddressName=" + $('#txtFrmAddressLabel').val();
				param += "&txtFrmPhone=" + $('#txtFrmAddressPhone').val();
				param += "&SelFrmState=" + $('#selFrmAddressState').val();
				param += "&SelFrmCity=" + $('#selFrmAddressCity').val();
				param += "&SelFrmDistrict=" + $('#selFrmAddressDistrict').val();
				param += "&txtPostalCode=" + $('#txtFrmAddressPostalCode').val();
				param += "&txtAddressDetail=" + $('#txtFrmAddressDetail').val();
				param += "&chkDefaultAddress=" + (document.getElementById('chkDefaultAddress').checked ? "1" : "0");
				param += "&hdnFrmID=" + $('#hdnAddressDetailID').val();
				param += "&hdnAction=" + $('#hdnAddressDetailAction').val();
				doSubmit('external/doSaveAddress',param);
			} else {
				ons.notification.toast(MSG['onProcess'], {
					timeout: 2000
				});
			}
		} else {
			ons.notification.toast(message, {
				timeout: 2000
			});
		}
	}
}
function doHandlerSaveAddress() {
	doFetch('external/getUserAddress','_cb=onCompleteFetchUserAddress&_p=profile-address-wrapper',false);document.getElementById('mainNav').popPage({animation:'none'});;
}
function doRemoveAddress() {
	ons.notification.confirm({
		message: 'Hapus alamat ini?',
		callback: function(answer) {
			if (answer==1) {
				if (getCookie(MSG['cookiePrefix']+'IsLoading')!="true") {
					var param = "hdnFrmID=" + $('#hdnAddressDetailID').val();
					doSubmit('external/doRemoveAddress',param);
				} else {
					ons.notification.toast(MSG['onProcess'], {
						timeout: 2000
					});
				}
			}
		}
	});
}
function doHandleSetPrimaryAddress(source) {
	if(source=="product-detail") {
		doFetch('external/getUserAddress','_cb=onCompleteFetchProductDefAddress&_p=',false);runApp();
	}
	if(source=="cart") {
		//doFetch('external/getCart','_cb=onCompleteFetchCart&_p=cartItemWrapper',false);
		doFetch('external/getCart','_cb=onCompleteFetchCartFinal&_p=cartItemPaymentWrapper',false);
	}
}
function doHandlerRemoveAddress() {
	doFetch('external/getUserAddress','_cb=onCompleteFetchUserAddress&_p=profile-address-wrapper',false);document.getElementById('mainNav').popPage({animation:'none'});;
}
/*======================================================
END : PROFILE 
=======================================================*/


/*======================================================
START : CHAT 
=======================================================*/
function fetchChatList() {
	doFetch('external/getChatList','_cb=onCompleteFetchChat&_p=chatListWrapper',false);
}
function onCompleteFetchChat(data,page) {
	var html = '';
	if (data.length > 0) {
		html = '<ons-list>';
		for (i=0;i<data.length;i++) {
			html += '<ons-list-item modifier="longdivider" onclick="doOpenPage(\'chat-details.html\',{\'title\':\''+data[i].Name+'\',\'BranchID\':\''+ data[i].ID +'\'})">' +
						'<div class="left">' +
							'<img class="list-item__thumbnail" src="dist/img/logo.png">' +
						'</div>' +
						'<div class="center">' +
							'<span class="list-item__title">Admin '+data[i].Name+'</span><span class="list-item__subtitle">'+data[i].LastMessage+'</span>' +
						'</div>' +
						'<div class="right">' +
							(data[i].UnreadMessage != "0" ? '<span class="notification">'+data[i].UnreadMessage+'</span>' : '') +
						'</div>' +
					'</ons-list-item>';
		}
		html += '</ons-list>';
	}
	$('#'+page).html(html);
}
function onCompleteFetchChatDetail(data,page) {
	var html = '';
	if (data.length > 0) {
		for (i=0;i<data.length;i++) {
			if (data[i].IsReply == 1) {
				html += '<div class="box sb2" style="background: rgba(209,213,219)">' +
							'<div><span>'+data[i].Message.replace("'","")+'</span></div>' +
						'</div>';
			} else {
				console.log(i);
				html += '<div class="box sb1" style="background: rgba(37,99,235)">' +
							'<div><span style="color: #fff">'+data[i].Message.replace("'","")+'</span></div>' +
						'</div>';
				if (i == (data.length-1)) {
					html += '<div class="box" style="background: rgb(236, 229, 226); font-size: 8pt">' +
								'<div><span style="color: grey">Mohon menunggu, Kami akan segera membalas pesan anda</span></div>' +
							'</div>';
				}
			}
			
		}
	}
	$('#'+page).html(html);
	$('#'+page).animate({ scrollTop: $('#'+page).prop("scrollHeight")}, 1000);
}
function doSendMessage() {
	if ($('#txtChatMessage').val().replace("'","").trim() != "") {
		var param = "";
		param += "BranchID=" + $('#txtHdnChatBranchID').val();
		param += "&Message=" + $('#txtChatMessage').val().replace("'","").trim();
		param += "&_cb=";
		param += "&_p=";
		doSubmit('external/doSaveMessage',param);
		$('#txtChatMessage').val('');
	}
}
function reloadChatMessage() {
	doFetch('external/getChatDetail','BranchID='+$('#txtHdnChatBranchID').val()+'&_cb=onCompleteFetchChatDetail&_p=chatMessagesWrapper',false);
}
/*======================================================
END : CHAT 
=======================================================*/


/*======================================================
START : TRANSACTION 
=======================================================*/
function onCompleteFetchUnpaidTransaction(data,page) {
	var html = '';
	if (data.length > 0) {
		for (i=0;i<data.length;i++) {
			html += '<ons-card onclick="doOpenPage(\'transaction-detail.html\',{\'ID\':\''+ data[i].ID +'\'})">' +
					'<ons-col>' +
						'<div class="center" style="margin-left: 10px;">' +
							'<span class="notification" style="margin-top: 2px; float: right;">&nbsp;Belum Bayar&nbsp;</span>' +
						'</div>' +
					'</ons-col>' +
					'<ons-row vertical-align="center" style="margin-bottom: 8px;">' +
						'<ons-col>' +
							'<img class="list-item__thumbnail"  src="' + uploadedUrl + '/' + data[i].PaymentLogo + '">' +
						'</ons-col>' +
						'<ons-col style="margin-top: 6px;">' +
							'<span style="font-weight: bold; font-size: 10pt">' + data[i].PaymentMethod.split("|")[1] + '</span><br />' +
							'<span style="color: grey; font-size: 10pt">' + (data[i].PaymentMethodCategory == "gopay" ? "" : data[i].ReferenceID + ' <a href=\"#\" onclick=\"copyVA(\'' + data[i].ReferenceID + '\')\">Copy</a></span>') +
						'</ons-col>' +
					'</ons-row>' +
					'<ons-row vertical-align="center" style="margin-bottom: 8px;">' +
						'<ons-col>' +
							'<span style="margin-left: 2%;font-size: 10pt">Jumlah Dibayar</span><br />' +
							'<span style="font-weight: bold; margin-left: 2%;font-size: 10pt">Rp ' + doFormatNumber(data[i].GrossAmount) + '</span>' +
						'</ons-col>' +
						'<ons-col style="margin-top: 6px;">' +
							'<span style="font-size: 10pt">Bayar Sebelum</span><br />' +
							'<span style="font-weight: bold; font-size: 10pt">' + data[i].ExpiredDate + '</span>' +
						'</ons-col>' +
					'</ons-row>' +
					(data[i].PaymentMethodCategory == "gopay" ? '<div style="padding-bottom: 24px;"><button onclick="doOpenURL(\''+ data[i].GopayDeepLink +'\')" style="background-color: #00aed6; color: white; padding: 6px 6px; cursor: pointer; border: none; float: right; width: 40%; border-radius: 10px;">Bayar Pakai Gopay</button></div>' : '') +
				'</ons-card>';
		}
	}
	$('#'+page).html(html);
	$('#'+page+'-badge .notification').html(data.length);
}
function onCompleteFetchTransaction(data,page) {
	var html = '';
	if (data.length > 0) {
		for (i=0;i<data.length;i++) {
			var status = "";
			if (data[i].Status == 2) status = "Dikonfirmasi";
			if (data[i].Status == 3) status = "Dikirim";
			if (data[i].Status == 4) status = "Selesai";
			if (data[i].Status == 5) status = "Batal";

			var urls = data[i].ImagePath ? uploadedUrl + '/product/' + data[i].ImagePath : 'dist/img/no_img_square.png';
			html += '<ons-card onclick="doOpenPage(\'transaction-detail.html\',{\'ID\':\''+ data[i].ID +'\'})">' +
					'<ons-col>' +
						'<div class="center" style="margin-left: 10px;">' +
							'<span class="notification" style="margin-top: 2px; float: right;">&nbsp;'+status+'&nbsp;</span>' +
						'</div>' +
					'</ons-col>' +
					'<ons-row vertical-align="center" style="margin-bottom: 8px;">' +
						'<ons-col style="padding-left:16px">' +
							'<img class="list-item__thumbnail" src="' + urls + '" style="float:left">' +
							'<span style="font-weight: bold; padding-left:16px; font-size: 10pt">' + data[i].Product + '</span><br />' +
							'<span style="color: grey; padding-left:16px; font-size: 10pt">' + (data[i].TotalItem > 1 ? "dan " + data[i].TotalItem + " barang lagi" : "1 barang") + '</span>' +
						'</ons-col>' +
					'</ons-row>' +
					'<ons-row vertical-align="center" style="margin-bottom: 8px;">' +
						'<ons-col>' +
							'<span style="margin-left: 2%;font-size: 10pt">Jumlah Dibayar</span><br />' +
							'<span style="font-weight: bold; margin-left: 2%;font-size: 10pt">Rp ' + doFormatNumber(data[i].GrossAmount) + '</span>' +
						'</ons-col>' +
					'</ons-row>' +
				'</ons-card>';
		}
	}
	$('#'+page).html(html);
	$('#'+page+'-badge .notification').html(data.length);
}
function doOpenInvoice() {
	doOpenURL(apiUrl + '/invoice?i=' + $('#lblTransInvoiceNoEncrypt').html());
}
function onCompleteFetchTransactionDetail(data,page) {
	$('#lblTransInvoiceNo').html('<b>' + data['paymentData'].ID + '</b>');
	$('#lblTransInvoiceNoEncrypt').html(data['ID']);
	$('#lblTransDate').html(data['paymentData'].CreatedDate);
	$('#lblPaymentDate').html(data['paymentData'].PaidDate);
	$('#lblSelectedAddress').html(data['paymentData'].Name+'<br />'+data['paymentData'].Phone+'<br />'+data['paymentData'].StateName+', '+data['paymentData'].CityName+', '+data['paymentData'].DistrictName+'<br />'+data['paymentData'].Address+'<br />'+data['paymentData'].PostalCode);
	var html = '';
	if (data['orderData'].length > 0) {
		$('#lblTransPaymentMethod').html(data['paymentData'].PaymentMethod.split("|")[1]);
		$('#lblTransTotal').html('<b>Rp ' + doFormatNumber(data['paymentData'].GrossAmount) + '</b>');
		html += '<ons-card>';
		var subTotalValue = 0;
		var subDiscountValue = 0;
		for (i=0;i<data['orderData'].length;i++) {
			if (data['orderData'][i].Status != 1) $('#btnDownloadInvoice').show();
			var urls = data['orderData'][i].ImagePath ? uploadedUrl + '/product/' + data['orderData'][i].ImagePath : 'dist/img/no_img_square.png';
			html += '<ons-list-item modifier="nodivider">' +
						'<div class="left">' +
							'<img class="list-item__thumbnail" src="' + urls + '">' +
						'</div>' +
						'<div class="center">' +
							'<span class="list-item__title" style="font-size:10pt">' + data['orderData'][i].Product + '</span><span class="list-item__subtitle" style="font-size:10pt">' + data['orderData'][i].Qty + ' x ' + (data['orderData'][i].ItemPrice != data['orderData'][i].SourcePrice ? '<s> Rp ' + doFormatNumber(data['orderData'][i].SourcePrice) + '</s> Rp ' + doFormatNumber(data['orderData'][i].ItemPrice) : 'Rp ' + doFormatNumber(data['orderData'][i].ItemPrice)) + '<br />' + data['orderData'][i].Notes + '</span>' +
						'</div>' +
					'</ons-list-item>';
					subTotalValue += parseFloat(data['orderData'][i].SubTotal);
					subDiscountValue += parseFloat(data['orderData'][i].SubDiscount);
					$('#lblTransDeliveryFee').html('Rp ' + doFormatNumber(data['orderData'][i].DeliveryFee));
					
		}
		$('#lblTransSubTotal').html('Rp ' + doFormatNumber(subTotalValue));
		$('#lblTransDiscount').html('- Rp ' + doFormatNumber(subDiscountValue));
		html += '<ons-list-item modifier="nodivider">' +
					'<ons-col>' +
						'<span style="font-size: 10pt">Kurir Pengiriman</span>' +
						'<span style="float: right; margin-top: 2px; font-weight: 500; font-size: 10pt">' + (data['orderData'][0].ShippingMethod != null ? data['orderData'][0].ShippingMethod : "-") + '</span>' +
					'</ons-col>' +
				'</ons-list-item>' +
				'<ons-list-item modifier="nodivider">' +
					'<ons-col>' +
						'<span style="font-size: 10pt">No. Resi</span>' +
						'<span style="float: right; margin-top: 2px; font-weight: 500; font-size: 10pt">' + (data['orderData'][0].TrackingNumber != null ? data['orderData'][0].TrackingNumber : "-") + '</span>' +
					'</ons-col>' +
				'</ons-list-item>';
		if (data['orderData'][0].Status == 5) {
			html += '<ons-list-item modifier="nodivider">' +
					'<ons-col>' +
						'<span style="font-size: 10pt">Alasan Pembatalan</span>' +
						'<span style="float: right; margin-top: 2px; font-weight: 500; font-size: 10pt">' + (data['orderData'][0].CancelledReason != null ? data['orderData'][0].CancelledReason : "-") + '</span>' +
					'</ons-col>' +
				'</ons-list-item>';
		}
		html += '<div style="padding-bottom: 25px;">' +
					'<button style="background-color: tomato; color: white; padding: 6px; cursor: pointer; border: none; float: right; width: 36%; border-radius: 10px" onclick="doOpenPage(\'chat-details.html\',{\'title\':\'' + data['orderData'][0].Branch +'\',\'BranchID\':\'' + data['orderData'][0].BranchID + '\'});">Hubungi Penjual</button>' +
					//'<button style="background-color: tomato; color: white; padding: 6px; cursor: pointer; border: none; float: right; width: 36%; margin-right: 1%; border-radius: 10px">Batal Pesanan</button>' +
				'</div>' +
			'</ons-card>';
	}
	$('#'+page).html(html);
}
/*======================================================
END : TRANSACTION 
=======================================================*/
/*======================================================
START : HELP 
=======================================================*/
function fetchHelpList() {
	doFetch('external/getHelpList','_cb=onCompleteFetchHelp&_p=helpListWrapper',false);
}
function onCompleteFetchHelp(data,page) {
	var html = '';
	if (data.length > 0) {
		html = '<ons-list>';
		for (i=0;i<data.length;i++) {
			html += '<ons-list-item expandable style="border-bottom: 1px solid #c7c7cc">' +
						data[i].Title +
						'<div class="expandable-content">' + data[i].Content + '</div>' +
					'</ons-list-item>';
		}
		html += '<ons-list>';
	}
	$('#'+page).html(html);
}
/*======================================================
END : HELP 
=======================================================*/