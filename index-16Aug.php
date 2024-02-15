<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Ella Froze</title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="google-signin-client_id" content="208326548212-4de6gs8sv9lcu67ulpe0cubp3l7evdfs.apps.googleusercontent.com">
	<meta http-equiv="Content-Security-Policy" content="default-src * data: gap: content: https://ssl.gstatic.com; style-src * 'unsafe-inline'; script-src * 'unsafe-inline' 'unsafe-eval'">
	<link href="assets/favicon.png" rel="shortcut icon" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
	<link rel="stylesheet" href="dist/css/onsenui.min.css">
	<link rel="stylesheet" href="dist/css/onsen-css-components.min.css">
	<link rel="stylesheet" href="dist/css/lightslider.min.css">
	<link rel="stylesheet" href="dist/css/pace-flash.css"/>

	<script src="dist/js/jquery-3.6.0.min.js"></script>
	<script src="dist/js/pace.min.js"></script>
	<script src="dist/js/sweetalert2.all.min.js"></script>
	<script src="dist/js/lightslider-1.1.6.min.js"></script>
	<script src="dist/js/onsenui.min.js"></script>
	<script src="dist/js/helper.js?ts=100000"></script>
	<script src="dist/js/app.js?ts=1000011"></script>
</head>

<style>
	body {
		background-color: rgb(145, 145, 145);
	}
	.page__background {
		background-color: rgb(145, 145, 145);
	}
	.action-sheet, .toast__message {
		max-width:500px;
		margin: auto;
	}
	.page__content {
		background-color: #fff !important;
		max-width:500px;
		margin: auto;
		overflow-y: scroll
	}
	.page__content::-webkit-scrollbar {
		display: none;
	}
	.tabbar--top {
		overflow-x: hidden !important;
	}
	.back-button__icon {
		fill: #fff !important
	}
	.toolbar__title {
		color: #fff !important
	}
	.text-input--material__label--active {
		color: red !important
	}
	.cover-label {
		text-align: center;
		left: 0px;
		width: 100%;
		bottom: 10px;
	}
	.box {
		width: 300px;
		margin: 20px auto;
		overflow-wrap: break-word !important;
		word-wrap: break-word !important;
		border-radius: 20px;
		padding: 20px;
		font-weight: 200;
		position: relative;
	}
	.sb1:before {
		content: "";
		width: 0px;
		height: 0px;
		position: absolute;
		border-left: 14px solid rgba(37,99,235);
		border-right: 14px solid transparent;
		border-top: 10px solid rgba(37,99,235);
		border-bottom: 10px solid transparent;
		right: -19px;
		top: 6px;
	}
	.sb2:before {
		content: "";
		width: 0px;
		height: 0px;
		position: absolute;
		border-left: 14px solid transparent;
		border-right: 14px solid rgba(209,213,219);
		border-top: 10px solid rgba(209,213,219);
		border-bottom: 10px solid transparent;
		left: -19px;
		top: 6px;
	}
	.tabbar {
		display: block;
		overflow-x: auto;
		overflow-y: hidden;
	}

	.tabbar__item {
		display: inline-block;
		width: 20%;
	}

</style>

<body>
	<ons-navigator animation="slide" swipeable id="mainNav" page="main.html"></ons-navigator>

	<!-- Start: Login -->
	<template id="login.html">
		<ons-page id="login">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'});">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Login</div>
			</ons-toolbar>
			<div style="margin-top: -200px; width: 100%; height: 100%;">
				<div style="text-align: center; margin-top: 100px;">
					<img src="dist/img/logo.png" style="margin-top: 150px; height: 150px;">
				</div>
				<div style="text-align: center; margin-top: 40px;">
					<p>
						<ons-icon icon="fa-user">&nbsp;&nbsp;</ons-icon><ons-input style="width: 70%;" id="txtLoginUsername" modifier="underbar" placeholder="Email/Nomor HP" float></ons-input>
					</p>
					<p>
						<ons-icon icon="fa-lock">&nbsp;&nbsp;</ons-icon><ons-input style="width: 70%;" id="txtLoginPassword" modifier="underbar" type="password" placeholder="Password" float></ons-input>
					</p>
					<p style="margin-top: 30px;">
						<ons-button onclick="doLogin()" style="width: 70%; background-color: tomato; font-weight: bold; border-radius: 100px;">Masuk</ons-button>
					</p>
					<p>
						<div id="my-signin2" style="width:250px;margin:auto"></div>
					</p>
					<!--<p>
						<ons-button onclick="doLoginByGoogle()" style="width: 70%; background-color: #fff; color: tomato; border: tomato 1px solid; font-weight: bold; border-radius: 100px;">Login by Google</ons-button>
					</p>-->
				</div>
					<div style="text-align: left; margin-left: 15%; color: tomato;">
					<span style="float: left;" onclick="doOpenPage('forgot-password.html')">Lupa Password</span>
					<span style="float: right; margin-right: 20%;" onclick="doOpenPage('registration.html')">Daftar Disini</span>
				</div>
			</div>
		</ons-page>
	</template>
	<!-- End: Login -->


	<!-- Start: Forgot Password-->
	<template id="forgot-password.html">
		<ons-page id="forgot-password">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'});">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title"></div>
			</ons-toolbar>
			<p style="margin-bottom: 10px; margin-top: 20px;">
				<span style="font-weight: bolder; margin-left: 20px; display: flex; font-size: 14pt;">Lupa Password</span>
			</p>
			<p style="margin-bottom: 10px; margin-top: 20px;">
				<span style="margin-left: 20px; display: flex; color:gray;">Masukkan alamat email kamu. Kami akan mengirimkan instruksi untuk atur ulang kata sandi.</span>
			</p>
			<div style="width: 100%; height: 100%;">
				<div style="text-align: center; margin-top: 40px;">
					<p>
						</ons-icon><ons-input style="width: 70%;" id="txtFrmResetEmail" modifier="underbar" placeholder="Email" float></ons-input>
					</p>
					<div>
						<ons-button onclick="doResetPassword()" style="width: 70%; background-color: tomato; font-weight: bold; border-radius: 100px;">Lanjut</ons-button>
					</div>
				</div>
			</div>
		</ons-page>
	</template>
	<!-- End: Forgot Password-->


	<!-- Start: Form Registration -->
	<template id="registration.html">
		<ons-page id="registration">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'});">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Daftar Akun</div>
			</ons-toolbar>
			<div style="margin-top: -200px; width: 100%; height: 100%;">
				<div style="text-align: center; margin-top: 100px;">
					<img src="dist/img/logo.png" style="margin-top: 150px; height: 150px;">
				</div>
				<div style="text-align: center; margin-top: 40px;">
					<p>
						<ons-input style="width: 70%;" id="txtRegName" modifier="underbar" placeholder="Nama Lengkap" float></ons-input>
					</p>
					<p style="margin-top: 10px">
						<ons-input style="width: 70%;" id="txtRegUsername" modifier="underbar" placeholder="Email/No Handphone" float></ons-input>
					</p>
					<p style="margin-top: 10px">
						<ons-input style="width: 70%;" id="txtRegPassword" modifier="underbar" type="password" placeholder="Password" float></ons-input>
					</p>
					<p style="margin-top: 10px">
						<ons-input style="width: 70%;" id="txtRegPassConfirm" modifier="underbar" type="password" placeholder="Konfirmasi Password" float></ons-input>
					</p>
					<p style="margin-top: 30px;">
						<ons-button onclick="doRegister()" style="width: 70%; background-color: tomato; font-weight: bold; border-radius: 100px;">Daftar</ons-button>
					</p>
					<p>
						<ons-button onclick="document.getElementById('mainNav').popPage({animation:'none'})" style="width: 70%; background-color: #fff; color: tomato; border: tomato 1px solid; font-weight: bold; border-radius: 100px;">Kembali</ons-button>
					</p>
				</div>
			</div>
		</ons-page>
	</template>
	<!-- End: Form Registration -->


	<!-- Start: Home -->
	<template id="main.html">
		<ons-page id="homeApp">
			<ons-toolbar style="padding-bottom: 80px; background-color: rgb(250, 0, 0); max-width: 500px;
			margin: auto;">
				<div style="text-align: center; padding-left: 24px;" id="imgLogo">
					<img src="dist/img/logo.png" style="height: 80px;">
				</div>
				<ons-row style="margin-top: 8px">
					<ons-col-1>
						<div class="left">
							<input id="txtSearchQuery" onkeyup="doSearchProduct('search')" type="search" value="" placeholder="Cari disini" class="search-input" style="background-color: aliceblue; width: 90%; margin-top: 12px; margin-left: 20px;">
						</div>
						<div style="margin-top: 10px; margin-left: 20px; min-width: 200px;">
							<span style="display: flex; flex: 1; white-space: nowrap; font-size: 10pt; font-weight: normal; color: white;" onclick="openBranch()">
								<ons-icon icon="fa-map-marker"></ons-icon>&nbsp;<span id="lblSelBranch"></span>
							</span>
						</div>
					</ons-col-1>
				</ons-row>
				<div style="display: flex; flex: 1; float: right; margin-top: 15px; margin-left: 10px;" id="btnMenu">
					<ons-icon icon="fa-book" style="margin-right: 25px; color: white; margin-top: 8px; font-size: 15pt;" onclick="doOpenURL(apiUrl + '/redirect/blog')"></ons-icon>
					<div style="margin-right: 25px">
						<span id="notifChat" class="notification" style="float: right; position: absolute; margin-left: 10px; background-color: rgba(196, 0, 0)">0</span>
						<ons-icon icon="fa-envelope" style="color: white; margin-top: 8px; font-size: 15pt" onclick="doOpenPage('chat.html')"></ons-icon>
					</div>
					<div>
						<span id="notifCart" class="notification" style="float: right; position: absolute; margin-left: 10px; background-color: rgba(196, 0, 0)">0</span>
						<ons-icon icon="fa-shopping-cart" style="color: white; margin-top: 8px; font-size: 15pt" onclick="doOpenPage('cart.html')"></ons-icon>
					</div>
				</div>
			</ons-toolbar>
			<div id="main-banner-slider-wrapper" style="padding-top: 38px;"></div>
			<div id="main-discount-slider-wrapper">
				<div style="margin-top: 10px; padding-bottom: 10px;">
					<span style="font-weight: bold; margin-left: 4%; font-size: 12pt;">Diskon Hari Ini!</span>
				</div>
				<div style="background-position-y: 150px; background-color: rgb(250, 250, 250); margin-bottom: 10px; border-radius: 10px;">
					<ul id="main-discount-slider"></ul>
				</div>
				<input type="hidden" id="main-discount-slider-init" value="F" />
			</div>
			<div style="margin-top: 20px; padding-bottom: 10px;">
				<span style="font-weight: bold; margin-left: 4%; font-size: 12pt;">Kategori untuk Anda</span>
			</div>
			<div style="background-color: rgb(250, 250, 250);">
				<ul id="main-category-slider" style="margin-bottom: 8px;"></ul>
			</div>
			<div style="margin-top: 20px; padding-bottom: 10px;">
				<span style="font-weight: bold; margin-left: 4%; font-size: 12pt;">Semua Produk</span>
			</div>
			<div id="main-product-list"></div>
			<div class="tabbar" style="position: fixed; display: flex; max-width:500px; margin: auto;">
				<label class="tabbar__item" onclick="runHome()">
					<button class="tabbar__button">
						<i class="tabbar__icon ion-ios-home"></i>
						<div class="tabbar__label">Home</div>
					</button>
				</label>
				<label class="tabbar__item" onclick="doOpenPage('transaction.html')">
					<button class="tabbar__button">
						<span id="notifTrans" class="notification" style="float: left; margin-left: 16px; position: absolute; background-color: rgba(196, 0, 0)">0</span>
						<i class="tabbar__icon ion-ios-shuffle"></i>
						<div class="tabbar__label">Transaksi</div>
					</button>
				</label>
				<label class="tabbar__item" onclick="doOpenPage('help.html')">
					<button class="tabbar__button">
						<i class="tabbar__icon ion-ios-help-circle"></i>
						<div class="tabbar__label">Bantuan</div>
					</button>
				</label>
				<label class="tabbar__item" onclick="doOpenPage('profile.html')">
					<button class="tabbar__button">
						<i class="tabbar__icon ion-ios-person"></i>
						<div class="tabbar__label">Akun</div>
					</button>
				</label>
			</div>
		</ons-page>
	</template>
	<!-- End: Home -->


	<!-- Start: Product List (Search/Category) -->
	<template id="product-search.html">
		<ons-page id="product-search">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="window.location.reload()">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Pencarian</div>
			</ons-toolbar>								
			<div style="margin-top: 10px; padding-bottom: 5px;">
				<span id="lblSearchTitle" style="font-weight: bold; font-size: 12pt;margin-left: 4%; "></span>
			</div>
			<div id="search-product-list"></div>
		</ons-page>
	</template>
	<!-- End: Product List (Search/Category) -->


	<!-- Start: Product Discount List -->
	<template id="discount-search.html">
		<ons-page id="discount-search">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="window.location.reload()">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Discount Hari Ini</div>
			</ons-toolbar>
			<div id="discount-list"></div>
		</ons-page>
	</template>
	<!-- End: Product Discount List -->


	<!-- Start: Product Detail -->
	<template id="product-description.html">
		<ons-page id="product-description" style="background-color: white;">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'});runHome()">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Detail Produk</div>
			</ons-toolbar>
			<div style="padding-bottom:20px" id="product-image-slider-wrapper"></div>
			<p id="lblProductPriceBeforeWrapper">
				<span id="lblProductPriceBefore" style="font-size: 10pt; color: grey; margin-left: 6%;"></span>
			</p>
			<p>
				<span id="lblProductPrice" style="font-size: 16pt; font-weight: bolder; margin-left: 6%;"></span>
			</p>
			<p style="margin-top: 10px;">
				<span id="lblProductName" style="font-size: 12pt; font-weight: 500; margin-left: 6%; display: flex;"></span>
			</p>
			<p style="margin-top: 10px;">
				<span id="lblProductMinOrder" style="font-size: 9pt; font-weight: 500; margin-left: 6%; color: gray;"></span>
			</p><br>
			<div style="height: 6px; background: rgb(237, 237, 237);"></div>
			<p style="margin-top: 18px;">
				<span style="font-weight: bold; margin-left: 6%;">Pengiriman</span>
			</p>
			<p style="margin-top: 18px;">
				<span style="margin-left: 6%;">Dikirim dari cabang:</span>
				<span id="lblProductBranch" style="margin-right: 10%; float: right;"></span>
			</p>
			<p style="margin-top: 18px; margin-bottom: 20px;">
				<span style="margin-left: 6%;">Dikirim ke alamat:</span>
				<span id="lblProductDefaultAddress" style="margin-right: 10%; float: right;"></span>
				<span style="margin-right: 10%; float: right; color:tomato;" onclick="openMainAddress('product-detail')">&nbsp;(Ubah)</span>
			</p>
			<div style="height: 8px; background: rgb(237, 237, 237);"></div>
			<p style="margin-top: 18px;">
				<span style="font-weight: bold; margin-left: 6%;">Harga Grosir</span>
			</p>
			<div id="dvProductPriceTable" style="margin-bottom: 18px;"></div>
			<div style="height: 8px; background: rgb(237, 237, 237);"></div>
			<p style="margin-top: 18px;">
				<span style="font-weight: bold; margin-left: 6%;">Deskripsi Produk</span>
			</p>
			<p style="margin-top: 18px; margin-bottom: 100px;">
				<span id="lblProductDesc" style="margin-left: 6%; margin-right: 6%; display: flex; justify-items: left; text-align: justify; "></span>
			</p>
			<input type="hidden" id="hdnProductDetailID" value="">
			<input type="hidden" id="hdnProductBranchID" value="">
			<input type="hidden" id="hdnProductStock" value="">
			<div class="tabbar" style="align-items: center; text-align: center; position: fixed; max-width: 490px; margin: auto; padding: 5px;">
				<ons-row style="padding-right: 15px;">
					<ons-col width="20%" style="padding-left: 5px; padding-right: 5px;">
						<ons-button id="btnChat" style="text-align:center; background-color: tomato; width: 100%;" onclick="doChatBranch()">
							<div class="tabbar__label"><i class="far fa-comment"></i></div>
						</ons-button>
					</ons-col>
					<ons-col width="80%" style="padding-left: 10px;">
						<ons-button id="btnBuy" style="background-color: tomato; width: 100%;" onclick="doAddCart()">
							<div class="tabbar__label">Beli</div>
						</ons-button>
					</ons-col>
				</ons-row>
			</div>
		</ons-page>
	</template>
	<!-- End: Product Detail -->


	<!-- Start: Profile -->
	<template id="profile.html">
		<ons-page id="profile">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'});">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Akun Saya</div>
			</ons-toolbar>
			<ons-row style="background-color: rgb(238, 238, 238); border-radius: 25px;">
				<ons-col width="100%">
					<p
						style="margin-top: 12px; margin-bottom: 12px; display: flex; justify-content: center; background-color: rgb(238, 238, 238);">
						<span id="lblProfileName" style="font-weight: 600; "></span>
					</p>
					<p
						style="margin-top: 12px; margin-bottom: 12px; display: flex; justify-content: center; background-color: rgb(238, 238, 238);">
						<span id="lblProfilePhone" style="font-weight: 500;"></span>
					</p>
					<p
						style="margin-top: 12px; margin-bottom: 12px; display: flex; justify-content: center; background-color: rgb(238, 238, 238);">
						<span id="lblProfileEmail" style="font-weight: 500;"></span>
					</p>
				</ons-col>
			</ons-row>
			<div style="font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;">
				<p style="margin-bottom: 10px; margin-top: 20px;">
					<span style="font-weight: bolder; margin-left: 20px; display: flex;">Pengaturan Akun</span>
				</p>
				<ons-list-item modifier="nodivider" onclick="doOpenPage('profile_edit.html')">
					<div class="left" style="width: 50px;">
						<ons-icon icon="fa-user-circle" class="list-item__icon"></ons-icon>
					</div>
					<div class="center"><span>Data Diri</span></div>
				</ons-list-item>
				<ons-list-item modifier="nodivider" onclick="doOpenPage('profile_address.html')">
					<div class="left" style="width: 50px;">
						<ons-icon icon="fa-address-card" class="list-item__icon"></ons-icon>
					</div>
					<div class="center"><span>Daftar Alamat</span></div>
				</ons-list-item>
				<ons-list-item modifier="nodivider" onclick="doLogout()">
					<div class="left" style="width: 50px;">
						<ons-icon icon="fa-sign-out-alt" class="list-item__icon"></ons-icon>
					</div>
					<div class="center"><span>Keluar Akun</span></div>
				</ons-list-item>
			</div>
		</ons-page>
	</template>
	<template id="profile_edit.html">
		<ons-page id="profile_edit">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'});">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Akun Saya</div>
			</ons-toolbar>
			<p style="padding-top: 20px;">
				<ons-list-item modifier="nodivider">
					<ons-input id="txtAccountName" modifier="underbar" placeholder="Nama Lengkap" float style="width:90%"></ons-input>
				</ons-list-item>
				<ons-list-item modifier="nodivider">
					<ons-input id="txtAccountPhone" modifier="underbar" placeholder="No. Telepon" float style="width:90%"></ons-input>
				</ons-list-item>
				<ons-list-item modifier="nodivider">
					<ons-input id="txtAccountEmail" modifier="underbar" placeholder="Email" float style="width:90%"></ons-input>
				</ons-list-item>
				<ons-list-item modifier="nodivider">
					<ons-button style="width: 90%; background-color: tomato; font-weight: bold; border-radius: 100px;" onclick="doUpdateAccount();">
						<span>Simpan</span>
					</ons-button>
				</ons-list-item>
			</p>
			<p></p>
		</ons-page>
	</template>
	<template id="profile_address.html">
		<ons-page id="profile_address_list">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'});">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Daftar Alamat</div>
			</ons-toolbar>
			<div id="profile-address-wrapper"></div>
			<div style="padding-top: 40px; padding-left: 10%;">
				<ons-button style="background-color: tomato; border-radius: 100px; width: 90%;" modifier="large" onclick="doOpenPage('profile_address_form.html',{'title':'Tambah Alamat','action':'add','ID':''})">
					<span>Tambah Alamat</span>
				</ons-button>
			</div>
		</ons-page>
	</template>
	<template id="profile_address_form.html">
		<ons-page id="profile_address_form">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'});">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
			</ons-toolbar>
			<ons-list-header style="margin-top: 10px; border-radius: 5px;">Detail Alamat</ons-list-header>
			<ons-list-item modifier="nodivider">
				<ons-card
					style="height: auto; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
					<ons-list-item modifier="nodivider">
						<ons-list-item modifier="nodivider">
							<ons-input id="txtFrmAddressLabel" modifier="underbar" placeholder="Label Alamat cth. Rumah/Kantor" float style="width:90%"></ons-input>
						</ons-list-item>
						<ons-list-item modifier="nodivider">
							<ons-input id="txtFrmAddressPhone" modifier="underbar" placeholder="No. Telepon" float style="width:90%"></ons-input>
						</ons-list-item>
						<ons-list-item modifier="nodivider">
							<div class="left">Provinsi</div>
							<div class="right">
								<select class="select-input" id="selFrmAddressState" style="width: 120px; margin-left: 12%;" onchange="$('#selFrmAddressCity').html('');$('#selFrmAddressDistrict').html('');doFetchAddressCity($('#selFrmAddressState').val(),'onCompleteFetchAddressCity');">
								</select>
							</div>
						</ons-list-item>
						<ons-list-item modifier="nodivider">
							<div class="left">Kota/Kabupaten</div>
							<div class="right">
								<select class="select-input" id="selFrmAddressCity" style="width: 120px; margin-left: 12%;" onchange="$('#selFrmAddressDistrict').html('');doFetchAddressDistrict($('#selFrmAddressCity').val(),'onCompleteFetchAddressDistrict');">
								</select>
							</div>
						</ons-list-item>
						<ons-list-item modifier="nodivider">
							<div class="left">Kecamatan</div>
							<div class="right">
								<select class="select-input" id="selFrmAddressDistrict" style="width: 120px; margin-left: 12%;">
								</select>
							</div>
						</ons-list-item>
						<ons-list-item modifier="nodivider">
							<ons-input id="txtFrmAddressPostalCode" modifier="underbar" placeholder="Kode Pos" float style="width:90%"></ons-input>
						</ons-list-item>
						<ons-list-item modifier="nodivider">
							<ons-input id="txtFrmAddressDetail" modifier="underbar" placeholder="Detail Alamat" float style="width:90%"></ons-input>
						</ons-list-item>
					</ons-list-item>
				</ons-card>
			</ons-list-item>
			<ons-list-header style="margin-top: 10px; border-radius: 5px;">Pengaturan</ons-list-header>
			<ons-list>
				<ons-card style="background-color: white;">
					<ons-list-item modifier="nodivider">
						<ons-list-item modifier="nodivider">
							<div class="center">
								Atur sebagai Alamat Utama
							</div>
							<div class="right">
								<ons-switch id="chkDefaultAddress"></ons-switch>
							</div>
						</ons-list-item>
					</ons-list-item>
				</ons-card>
			</ons-list>
			<input type="hidden" id="hdnAddressDetailAction" value="">
			<input type="hidden" id="hdnAddressDetailID" value="">
			<ons-button style="text-align: center; width: 100%; height: 50px; padding: 8px; background-color: tomato; font-weight: bold; border-radius: 0px;" onclick="doSaveAddress()">Simpan</ons-button>
			<ons-button id="btn-address-remove" style="text-align: center; width: 100%; height: 50px; padding: 8px; background-color: red; font-weight: bold; border-radius: 0px;" onclick="doRemoveAddress()">Hapus</ons-button>
		</ons-page>
	</template>
	<!-- End: Profile -->


	<!-- Start: Chat -->
	<template id="chat.html">
		<ons-page id="chat">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'});runHome()">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Pesan</div>
			</ons-toolbar>
			<div id="chatListWrapper"></div>
		</ons-page>
	</template>
	<template id="chat-details.html">
		<ons-page id="chat-details">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'});fetchChatList()">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Pesan</div>
			</ons-toolbar>
			<div style="border-top: 1px solid rgb(226, 226, 226); "></div>
			<div id="chatMessagesWrapper" style="max-height:85%;overflow:scroll;overflow-x:hidden;"></div>
			<div class="tabbar" style="position: fixed; min-height: 15%; display:flex; max-width: 500px; margin: auto;">
				<input type="hidden" id="txtHdnChatBranchID">
				<label class="tabbar__item" style="width:90%">
					<button class="tabbar__button" style="height: 90px;">
						<textarea maxlength="250" id="txtChatMessage" class="textarea" placeholder="Tulis Pesan"
							style="border-radius: 10px; width: 90%; margin-left: 8pt; margin-top:8px" rows="3"></textarea>
					</button>
				</label>
				<label class="tabbar__item" style="max-width: 10%;">
					<button class="tabbar__button" onclick="doSendMessage()">
						<ons-icon icon="fa-paper-plane" style="color:tomato; margin-top: 10px; font-size: 25px;"></ons-icon>
					</button>
				</label>
			</div>
		</ons-page>
	</template>
	<!-- End: Chat -->
	

	<!-- Start: Checkout -->
	<template id="cart.html">
		<ons-page id="cart">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'});runHome()">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Keranjang</div>
			</ons-toolbar>
			<div id="cartItemWrapper"></div>
		</ons-page>
	</template>
	<template id="cart-payment.html">
		<ons-page id="cart-payment">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'})">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Pembayaran</div>
			</ons-toolbar>
			<div id="cartItemPaymentWrapper"></div>
		</ons-page>
	</template>
	<template id="cart-completed.html">
		<ons-page id="cart-completed">
			<!--<ons-toolbar style="background-color: rgb(250, 0, 0);">
				<div class="left" style="margin-right: -20px;"><ons-back-button></ons-toolbar-button></div>
				<div class="center toolbar__center toolbar__title">Pesanan</div>
			</ons-toolbar>-->
			<p style="text-align:center;padding-top:50px;">
				<ons-icon icon="fa-check-circle" style="font-size:64px;color:green"></ons-icon>
				<br /><br /><b>Pesanan berhasil dibuat</b><br />Mohon selesaikan pembayaran sebelum Batas Akhir Pembayaran
			</p>
			<ons-list-item modified="nodivider">
				<ons-row>
					<ons-col>
						<span style="color: gray;">Batas Akhir Pembayaran</span>
					</ons-col>
				</ons-row>
				<ons-col>
					<span style="font-weight: 600;" id="lblPaymentExpiredDate">-</span>
				</ons-col>
			</ons-list-item>
			<div style="height: 8px; background: rgb(237, 237, 237);"></div>
			<ons-row style="margin-top: 20px;">
				<ons-col>
					<span style="font-weight: 600; margin-left: 20px;" id="lblPaymentMethod">-</span>
					<span style="float: right; margin-right: 20px; margin-top: -10px;"><img id="imgPaymentLogo" class="list-item__thumbnail" src=""></span>
				</ons-col>
			</ons-row>
			<ons-row style="margin-top: 10px;">
				<ons-col>
					<span style="color: gray; margin-left: 20px;" id="lblPaymentCodeName">-</span>
				</ons-col>
			</ons-row>
			<ons-row style="margin-top: 10px;">
				<ons-col>
					<span style="font-weight: 600; margin-left: 20px; color: rgb(2, 0, 0);" id="lblPaymentReferenceID">-</span>
				</ons-col>
			</ons-row>
			<ons-row style="margin-top: 10px; margin-bottom: 10px;">
				<ons-col>
					<span style="margin-left: 20px; color: gray;">Total Pembayaran</span>
				</ons-col>
			</ons-row>
			<ons-col>
				<span style="font-weight: 600; margin-left: 20px; color: rgb(0, 0, 0);" id="lblPaymentGrossAmount">-</span>
			</ons-col>
			<div style="text-align: center; margin-top: 10px;margin-bottom: 10px;">
				<a style="text-align: center; margin-top: 10px;" id="btnPayWithGopay">
					<ons-button style="background-color: #00aed6; border-radius: 100px; width: 90%;">
						<span>Bayar Pakai Gopay</span>
					</ons-button>
				</a>
			</div>
			
			<div style="height: 8px; background: rgb(237, 237, 237);"></div>

			<div style="text-align: center; margin-top: 10px;"><span style="align-items: center; text-align: center; color: rgb(156, 156, 156)">Pesananmu baru diteruskan ke penjual setelah pembayaran terverfikasi</span></div>

			<div style="text-align: center; margin-top: 10px;">
				<ons-button style="background-color: tomato; border-radius: 100px; width: 90%;" onclick="document.getElementById('mainNav').popPage({animation:'none'});setTimeout(function(){ doOpenPage('transaction.html'); }, 750);">
					<span>Belanja Lagi</span>
				</ons-button>
			</div>
		</ons-page>
	</template>
	<!-- End: Checkout -->


	<!-- Start: Transaction -->
	<template id="transaction.html">
		<ons-page id="transaction">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'});runHome()">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Transaksi</div>
			</ons-toolbar>
			
			<ons-tabbar swipeable position="top">
				<ons-tab page="transaction-unpaid.html" badge="0" id="transaction-unpaid-wrapper-badge"><span style="font-size: 7pt;" >Bayar</span></ons-tab>
				<ons-tab page="transaction-confirmed.html" badge="0" id="transaction-confirmed-wrapper-badge"><span style="font-size: 7pt;">Diproses</span></ons-tab>
				<ons-tab page="transaction-delivery.html" badge="0" id="transaction-delivery-wrapper-badge"><span style="font-size: 7pt;">Dikirim</span></ons-tab>
				<ons-tab page="transaction-finished.html"><span style="font-size: 7pt;">Selesai</span></ons-tab>
				<ons-tab page="transaction-cancel.html"><span style="font-size: 7pt;">Batal</span></ons-tab>
				<!--<ons-tab page="transaction-complain.html"><span>Komplain</span></ons-tab>-->
			</ons-tabbar>

			<template id="transaction-unpaid.html">
				<ons-page id="transaction-unpaid">
					<div id="transaction-unpaid-wrapper"></div>
				</ons-page>
			</template>
			<template id="transaction-confirmed.html">
				<ons-page id="transaction-confirmed">
					<div id="transaction-confirmed-wrapper"></div>
				</ons-page>
			</template>
			<template id="transaction-delivery.html">
				<ons-page id="transaction-delivery">
					<div id="transaction-delivery-wrapper"></div>
				</ons-page>
			</template>
			<template id="transaction-finished.html">
				<ons-page id="transaction-finished">
					<div id="transaction-finished-wrapper"></div>
				</ons-page>
			</template>
			<template id="transaction-cancel.html">
				<ons-page id="transaction-cancel">
					<div id="transaction-cancel-wrapper"></div>
				</ons-page>
			</template>
			<!--<template id="transaction-complain.html">
				<ons-page id="transaction-complain">
				</ons-page>
			</template>-->

		</ons-page>
	</template>
	<template id="transaction-detail.html">
		<ons-page id="transaction-detail">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'});">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Detail Pesanan</div>
			</ons-toolbar>
			<ons-card style="padding:6px">
				<ons-list-item modifier="nodivider">
					<ons-col>
						<span style="font-size: 10pt">No. Pesanan</span>
						<span style="float: right; margin-right: 6px; margin-top: 2px; font-size: 8pt" id="lblTransInvoiceNo">-</span>
						<span style="display: none;" id="lblTransInvoiceNoEncrypt"></span>
					</ons-col>
				</ons-list-item>
				<ons-list-item id="btnDownloadInvoice"  modifier="nodivider" style="margin-top: -20px;display:none; ">
					<ons-col>
						<button style="background-color: tomato; color: white; padding: 6px; cursor: pointer; border: none; float: right; width: 20%; border-radius: 10px" onclick="doOpenInvoice()">Invoice</button>
					</ons-col>
				</ons-list-item>
				<ons-list-item modifier="nodivider">
					<ons-col>
						<span style="font-size: 10pt">Tanggal Pesanan</span>
						<span style="float: right; margin-right: 6px; margin-top: 2px; font-size: 10pt" id="lblTransDate">-</span>
					</ons-col>
				</ons-list-item>
				<ons-list-item modifier="nodivider">
					<ons-col>
						<span style="font-size: 10pt">Tanggal Pembayaran</span>
						<span style="float: right; margin-right: 6px; margin-top: 2px; font-size: 10pt" id="lblPaymentDate">-</span>
					</ons-col>
				</ons-list-item>
			</ons-card>
			<ons-card style="padding:6px">
				<ons-list-item modifier="nodivider">
					<span style="font-size: 10pt"><b>Alamat Pengiriman</b></span><br />
				</ons-list-item>
				<ons-list-item modifier="nodivider">
					<span style="margin-top: -20px; font-weight: 500; font-size: 10pt" id="lblSelectedAddress">-</span>
				</ons-list-item>
			</ons-card>
			<div id="transactionDetailOrderList"></div>
			<ons-card style="padding:6px">
				<ons-list-item modifier="nodivider">
					<span style="font-size: 10pt"><b>Rincian Pembayaran</b></span><br />
				</ons-list-item>
				<ons-list-item modifier="nodivider">
					<ons-col>
						<span style="font-size: 10pt">Metode Pembayaran</span>
						<span style="float: right; margin-right: 6px; margin-top: 2px; font-size: 10pt" id="lblTransPaymentMethod">Rp 0</span>
					</ons-col>
				</ons-list-item>
				<ons-list-item modifier="nodivider">
					<ons-col>
						<span style="font-size: 10pt">Sub Total</span>
						<span style="float: right; margin-right: 6px; margin-top: 2px; font-size: 10pt" id="lblTransSubTotal">Rp 0</span>
					</ons-col>
				</ons-list-item>
				<ons-list-item modifier="nodivider">
					<ons-col>
						<span style="font-size: 10pt">Ongkos Kirim</span>
						<span style="float: right; margin-right: 6px; margin-top: 2px; font-size: 10pt" id="lblTransDeliveryFee">Rp 0</span>
					</ons-col>
				</ons-list-item>
				<ons-list-item modifier="nodivider">
					<ons-col>
						<span style="font-size: 10pt">Total Diskon</span>
						<span style="float: right; margin-right: 6px; margin-top: 2px; font-size: 10pt" id="lblTransDiscount">Rp 0</span>
					</ons-col>
				</ons-list-item>
				<ons-list-item modifier="nodivider">
					<ons-col>
						<span style="font-size: 10pt"><b>Total</b></span>
						<span style="float: right; margin-right: 6px; margin-top: 2px; font-size: 10pt" id="lblTransTotal"><b>Rp 0</b></span>
					</ons-col>
				</ons-list-item>
			</ons-card>
		</ons-page>
	</template>
	<!-- End: Transaction -->


	<!-- Start: Help -->
	<template id="help.html">
		<ons-page id="help">
			<ons-toolbar style="background-color: rgb(250, 0, 0); max-width: 500px; margin: auto;">
				<div class="left" style="margin-right: -20px;">
					<ons-toolbar-button class="toolbar-button--quiet" onclick="document.getElementById('mainNav').popPage({animation:'none'});runHome();">
						<button class="toolbar-button">
							<ons-icon icon="md-arrow-back" style="color:#fff;font-size:17pt"></ons-icon>
						</button>
					</ons-toolbar-button>
				</div>
				<div class="center toolbar__center toolbar__title">Bantuan</div>
			</ons-toolbar>
			<div id="helpListWrapper"></div>
		</ons-page>
	</template>
	<!-- End: Help -->
	
</body>

<script src="https://apis.google.com/js/platform.js?onload=initGAPI" async defer></script>
<script>
	function initGAPI() {
      	gapi.load('auth2', function() {
        	gapi.auth2.init();
      	});
    }
	(function ($) {
		$(document).ready(function () {
			if (location.protocol !== 'https:') {
				//location.replace(`https:${location.href.substring(location.protocol.length)}`);
			}
			ons.platform.select('android');
			$(window).resize(function () {
				doResizeListMenu()
			});
		});
	})(jQuery);
	function doResizeListMenu() {
		$('#imgLogo').css('padding-left','24px');
		$('#btnMenu').css('padding-right','0px');
		if (screen.width < 470) {
			$('#imgLogo').hide(300);
		} else {
			$('#imgLogo').show(300);
		}
		if (screen.width < 370) {
			$('#txtSearchQuery').css('width','80%');
		} else if (screen.width < 500) {
			$('#txtSearchQuery').css('width','90%');
		}
		// } else if (screen.width < 600) {
		// 	$('#txtSearchQuery').css('width','100%');
		// } else if (screen.width < 710) {
		// 	$('#txtSearchQuery').css('width','150%');
		// } else if (screen.width < 900) {
		// 	$('#txtSearchQuery').css('width','200%');
		// } else if (screen.width < 1000) {
		// 	$('#txtSearchQuery').css('width','250%');
		// } else if (screen.width < 1299) {
		// 	$('#txtSearchQuery').css('width','300%');
		// } else if (screen.width < 1599) {
		// 	$('#imgLogo').css('padding-left','104px');
		// 	$('#txtSearchQuery').css('width','300%');
		// } else if (screen.width < 1599) {
		// 	$('#imgLogo').css('padding-left','104px');
		// 	$('#txtSearchQuery').css('width','400%');
		// 	$('#btnMenu').css('padding-right','104px');
		// } else if (screen.width < 1700) {
		// 	$('#imgLogo').css('padding-left','104px');
		// 	$('#txtSearchQuery').css('width','450%');
		// 	$('#btnMenu').css('padding-right','54px');
		// } else if (screen.width < 2048) {
		// 	$('#imgLogo').css('padding-left','224px');
		// 	$('#txtSearchQuery').css('width','450%');
		// 	$('#btnMenu').css('padding-right','54px');
		// } else {
		// 	$('#txtSearchQuery').css('width','200%');
		// }
		//console.log(screen.width);
	}
</script>
</html>