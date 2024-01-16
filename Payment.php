<!DOCTYPE html>
<html lang="en">
	
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Homepage</title>
		<meta name="description" content="Free open source Tailwind CSS Store template">
		<meta name="keywords" content="tailwind,tailwindcss,tailwind css,css,starter template,free template,store template, shop layout, minimal, monochrome, minimalistic, theme, nordic">

		<link href="https://unpkg.com/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
		<!--Replace with your tailwind.css once created-->
		<link href="https://fonts.googleapis.com/css?family=Work+Sans:200,400&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">
		<link rel="stylesheet" href="assets/plugin/fontawesome-5.15.3/css/all.css">

		<script src="https://unpkg.com/@popperjs/core@2.9.1/dist/umd/popper.min.js" charset="utf-8"></script>
		<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
		<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.1.min.js"></script>
		<script>
			function openDropdown(event,dropdownID){
				let element = event.target;
				while(element.nodeName !== "BUTTON"){
				element = element.parentNode;
				}
				var popper = Popper.createPopper(element, document.getElementById(dropdownID), {
				placement: 'bottom-start'
				});
				document.getElementById(dropdownID).classList.toggle("hidden");
				document.getElementById(dropdownID).classList.toggle("block");
			}

			function openDropdown(event,dropdownID){
				let element = event.target;
				while(element.nodeName !== "BUTTON"){
				element = element.parentNode;
				}
				var popper = Popper.createPopper(element, document.getElementById(dropdownID), {
				placement: 'bottom-start'
				});
				document.getElementById(dropdownID).classList.toggle("hidden");
				document.getElementById(dropdownID).classList.toggle("block");
			}
		</script>
		<style>
			input[type='number']::-webkit-inner-spin-button,
			input[type='number']::-webkit-outer-spin-button {
			-webkit-appearance: none;
			margin: 0;
			}
			.custom-number-input input:focus {
			outline: none !important;
			}
			.custom-number-input button:focus {
			outline: none !important;
			}
			.table {
			border-spacing: 0 15px;
			}
			i {
			font-size: 1rem !important;
			}
			.table tr {
			border-radius: 20px;
			}
			tr td:nth-child(n+5),
			tr th:nth-child(n+5) {
			border-radius: 0 .625rem .625rem 0;
			}
			tr td:nth-child(1),
			tr th:nth-child(1) {
			border-radius: .625rem 0 0 .625rem;
			}
			#summary {
			background-color: #f6f6f6;
			}
			input:checked + svg {
			display: block;
			}
		</style>
	</head>

	<body class="bg-white work-sans leading-normal text-base tracking-normal">
		<nav id="header" class="sticky bg-red-600 top-0 w-full z-30 top-0 py-1 text-white">
			<div class="w-full container mx-auto flex flex-wrap items-center justify-between mt-0 py-1">
				<label for="menu-toggle" class="cursor-pointer sm:hidden block">
					<svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
						<title>menu</title>
						<path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"></path>
					</svg>
				</label>
				<input class="hidden" type="checkbox" id="menu-toggle" />
				<a href="index.php"><img class="hidden sm:block flex items-center tracking-wide rounded-md" width="150px" src="assets\img\picture\froze.png" href="index.php"></a>
				<div class="grid grid-rows-1 sm:w-3/4">
					<div class="shadow flex mt-8">
						<input class="w-full rounded sm:w-full hover:text-black" type="text" placeholder=" Cari item...">
						<button class="bg-red-700 w-auto flex justify-end items-center text-blue-500 p-2 hover:text-blue-400">
							<svg class="text-white h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 56.966 56.966" style="enable-background:new 0 0 56.966 56.966;" xml:space="preserve" width="512px" height="512px">
								<path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  s-17-7.626-17-17S14.61,6,23.984,6z"/>
							</svg>
						</button>
					</div>
					<div class="flex items-center">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
						</svg>
						<span class="inline-block px-2 text-md font-semibold text-white mr-2 pt-2 hidden sm:block">
							Dikirim dari cabang Grogol ke Andi
						</span>
					</div>
				</div>
				
				<div class="flex items-center " id="nav-content">
					<a class="pl-6 inline-block no-underline hover:text-white hidden sm:block" href="cart.php" onclick="openDropdown(event,'dropdown-id-1')">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="white">
							<path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
						</svg>
					</a>
					<div x-data="{ dropdownOpen: false }">
						<button @click="dropdownOpen = !dropdownOpen" class="relative inline-block rounded-md bg-transparant p-2 focus:outline-none ml-6">
							<svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
								<path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
							</svg>
						</button>
						<div x-show="dropdownOpen" @click="dropdownOpen = false" class="fixed inset-0 h-full w-full z-10"></div>
						<div x-show="dropdownOpen" class="absolute right-0 mt-2 bg-white rounded-md shadow-lg overflow-hidden z-20" style="width:20rem;">
							<div class="py-2">
								<a href="#" class="flex items-center px-4 py-3 border-b hover:bg-gray-100 -mx-2">
									<img class="h-8 w-8 rounded-full object-cover mx-1" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=334&q=80" alt="avatar">
									<p class="text-gray-600 text-sm mx-2">
										<span class="font-bold" href="#">Sara Salah</span> replied on the <span class="font-bold text-blue-500" href="#">Upload Image</span> artical . 2m
									</p>
								</a>
								<a href="#" class="flex items-center px-4 py-3 border-b hover:bg-gray-100 -mx-2">
									<img class="h-8 w-8 rounded-full object-cover mx-1" src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=634&q=80" alt="avatar">
									<p class="text-gray-600 text-sm mx-2">
										<span class="font-bold" href="#">Slick Net</span> start following you . 45m
									</p>
								</a>
								<a href="#" class="flex items-center px-4 py-3 border-b hover:bg-gray-100 -mx-2">
									<img class="h-8 w-8 rounded-full object-cover mx-1" src="https://images.unsplash.com/photo-1450297350677-623de575f31c?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=334&q=80" alt="avatar">
									<p class="text-gray-600 text-sm mx-2">
										<span class="font-bold" href="#">Jane Doe</span> Like Your reply on <span class="font-bold text-blue-500" href="#">Test with TDD</span> artical . 1h
									</p>
								</a>
								<a href="#" class="flex items-center px-4 py-3 hover:bg-gray-100 -mx-2">
									<img class="h-8 w-8 rounded-full object-cover mx-1" src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=398&q=80" alt="avatar">
									<p class="text-gray-600 text-sm mx-2">
										<span class="font-bold" href="#">Abigail Bennett</span> start following you . 3h
									</p>
								</a>
							</div>
							<a href="#" class="block bg-gray-800 text-white text-center font-bold py-2">See all notifications</a>
						</div>
					</div>
					<button type="button" class="ml-4 sm:block w-8 h-8 overflow-hidden rounded-full border-1 border-gray-600 focus:outline-none focus:border-white" id="user-menu-button" aria-expanded="false" aria-haspopup="true" onclick="openDropdown(event,'dropdown-id-2')" >
						<span class="sr-only">Open user menu</span>
						<img class="w-full h-full rounded-full object-cover" src="assets/img/picture/Dfrank.jpg">
						<!--
						<svg class="fill-current hover:text-blue-900" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
							<circle fill="white" cx="12" cy="7" r="3" />
							<path d="M12 2C9.243 2 7 4.243 7 7s2.243 5 5 5 5-2.243 5-5S14.757 2 12 2zM12 10c-1.654 0-3-1.346-3-3s1.346-3 3-3 3 1.346 3 3S13.654 10 12 10zM21 21v-1c0-3.859-3.141-7-7-7h-4c-3.86 0-7 3.141-7 7v1h2v-1c0-2.757 2.243-5 5-5h4c2.757 0 5 2.243 5 5v1H21z" />
						</svg>-->
					</button>
					<div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" id="dropdown-id-2">
						<a href="selling.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Pembelian</a>
						<a href="user_profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Profile</a>
						<a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-3">Sign out</a>
					</div>
				</div>
			</div>
		</nav>

		<body x-data="{ showModal1: false, showModal2: false, showModal3: false }" :class="{'overflow-y-hidden': showModal1 || showModal2 || showModal3}" class="bg-gray-100">
			<div class="container mx-auto mt-10">
				<div class="flex my-10">
					<div class="w-3/4 bg-white px-10 py-10">
						<div class="flex justify-between border-b pb-8">
							<h1 class="font-semibold text-2xl">Beli Langsung</h1>
							<h2 class="font-semibold text-2xl">2 Items</h2>
						</div>
						<div class="flex mt-10 mb-5">
							<h3 class="font-semibold text-gray-600 text-xs uppercase w-2/5">Produk dipesan</h3>
							<h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5 text-center">Jumlah</h3>
							<h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5 text-center">Harga</h3>
							<h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5 text-center">Total</h3>
						</div>
						<div class="flex items-center hover:bg-gray-100 -mx-8 px-6 py-5">
							<div class="flex w-2/5"> <!-- product -->
								<div class="w-20">
									<img class="h-24 rounded-md" src="https://images.unsplash.com/photo-1611506168759-1e69a83b5a53?ixid=MnwxMjA3fDB8MHxzZWFyY2h8NjZ8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="">
								</div>
								<div class="flex flex-col justify-between ml-4 flex-grow">
									<span class="font-bold text-sm">Kepala Kakap Merah 500g</span>
									<span class="text-red-500 text-xs">Ikan Kakap</span>
									<a href="#" class="font-semibold hover:text-red-500 text-gray-500 text-xs">Remove</a>
								</div>
							</div>
							<div class="flex justify-center w-1/5">
								<div class="flex flex-row h-8 w-1/2 rounded-lg relative bg-transparent">
									<button data-action="decrement" class="bg-gray-300 text-gray-600 hover:text-gray-700 hover:bg-gray-400 h-full w-10 rounded-l cursor-pointer outline-none">
										<span class="m-auto text-2xl font-thin">−</span>
									</button>
									<input type="number" class="outline-none focus:outline-none text-center w-1/2 bg-gray-200 font-semibold text-md hover:text-black focus:text-black md:text-basecursor-default flex items-center text-gray-700 outline-none" name="custom-input-number" value="0"></input>
									<button data-action="increment" class="bg-gray-300 text-gray-600 hover:text-gray-700 hover:bg-gray-400 h-full w-10 rounded-r cursor-pointer">
										<span class="m-auto text-2xl font-thin">+</span>
									</button>
								</div>
							</div>
							<span class="text-center w-1/5 font-semibold text-sm">Rp</span>
							<span class="text-center w-1/5 font-semibold text-sm">Rp</span>
						</div>
						<div class="flex items-center hover:bg-gray-100 -mx-8 px-6 py-5">
							<div class="flex w-2/5"> <!-- product -->
								<div class="w-20">
									<img class="h-24 rounded-md" src="https://images.unsplash.com/photo-1611506168759-1e69a83b5a53?ixid=MnwxMjA3fDB8MHxzZWFyY2h8NjZ8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="">
								</div>
								<div class="flex flex-col justify-between ml-4 flex-grow">
									<span class="font-bold text-sm">Marlin Fillet 500g</span>
									<span class="text-red-500 text-xs">Ikan Marlin</span>
									<a href="#" class="font-semibold hover:text-red-500 text-gray-500 text-xs">Remove</a>
								</div>
							</div>
							<div class="flex justify-center w-1/5">
								<div class="flex flex-row h-8 w-1/2 rounded-lg relative bg-transparent">
									<button data-action="decrement" class="bg-gray-300 text-gray-600 hover:text-gray-700 hover:bg-gray-400 h-full w-10 rounded-l cursor-pointer outline-none">
										<span class="m-auto text-2xl font-thin">−</span>
									</button>
									<input type="number" class="outline-none focus:outline-none text-center w-1/2 bg-gray-200 font-semibold text-md hover:text-black focus:text-black md:text-basecursor-default flex items-center text-gray-700 outline-none" name="custom-input-number" value="0"></input>
									<button data-action="increment" class="bg-gray-300 text-gray-600 hover:text-gray-700 hover:bg-gray-400 h-full w-10 rounded-r cursor-pointer">
										<span class="m-auto text-2xl font-thin">+</span>
									</button>
								</div>
							</div>
							<span class="text-center w-1/5 font-semibold text-sm">Rp</span>
							<span class="text-center w-1/5 font-semibold text-sm">Rp</span>
						</div>
					</div>

					<div id="summary" class="w-1/4 px-8 py-10">
						<h1 class="font-semibold text-2xl border-b pb-8">Ringkasan Belanja</h1>
						<div class="flex justify-between mt-6">
							<span class="font-normal block mb-3 text-sm uppercase text-gray-400">Total Harga (2 produk)</span>
							<span class="font-semibold text-sm">Rp</span>
						</div>
						<div class="flex justify-between">
							<span class="font-normal block mb-3 text-sm uppercase text-gray-400">Total Ongkos Kirim</span>
							<span class="font-semibold text-sm">Rp</span>
						</div>
						<div class="flex justify-between">
							<span class="font-normal block mb-3 text-sm uppercase text-gray-400">Ansuransi</span>
							<span class="font-semibold text-sm">Rp</span>
						</div>
						<div class="border-t">
							<div class="flex font-semibold justify-between py-6 text-sm uppercase">
								<span>Total Tagihan</span>
								<span>Rp</span>
							</div>
							<button class="bg-indigo-500 font-semibold hover:bg-indigo-600 py-3 text-sm text-white uppercase w-full transition-all duration-300 hover:shadow-none">Bayar</button>
							<div
								class="fixed inset-0 w-full h-full bg-black bg-opacity-50 duration-300 overflow-y-auto mt-28"
								x-show="showModal1"
								x-transition:enter="transition duration-300"
								x-transition:enter-start="opacity-0"
								x-transition:enter-end="opacity-100"
								x-transition:leave="transition duration-300"
								x-transition:leave-start="opacity-100"
								x-transition:leave-end="opacity-0"
								>
								<div class="relative sm:w-3/4 md:w-1/2 lg:w-1/3 mx-2 sm:mx-auto my-10 opacity-100">
									<div
									class="relative bg-white shadow-lg rounded-lg text-gray-900 z-20"
									@click.away="showModal1 = false"
									x-show="showModal1"
									x-transition:enter="transition transform duration-300"
									x-transition:enter-start="scale-0"
									x-transition:enter-end="scale-100"
									x-transition:leave="transition transform duration-300"
									x-transition:leave-start="scale-100"
									x-transition:leave-end="scale-0"
									>
									<header class="flex flex-col justify-center items-center p-3 text-green-600">
										<div class="flex justify-center w-28 h-28 rounded-full mb-4">
										<svg id="bold" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="10.5" cy="22.5" r="1.5"/><circle cx="18.5" cy="22.5" r="1.5"/><path d="m24 6.5c0 3.584-2.916 6.5-6.5 6.5s-6.5-2.916-6.5-6.5 2.916-6.5 6.5-6.5 6.5 2.916 6.5 6.5zm-3 0c0-.552-.448-1-1-1h-1.5v-1.5c0-.552-.448-1-1-1s-1 .448-1 1v1.5h-1.5c-.552 0-1 .448-1 1s.448 1 1 1h1.5v1.5c0 .552.448 1 1 1s1-.448 1-1v-1.5h1.5c.552 0 1-.448 1-1z"/>
											<path d="m9 6.5c0-.169.015-.334.025-.5h-2.666l-.38-1.806c-.266-1.26-1.392-2.178-2.679-2.183l-2.547-.011c-.001 0-.002 0-.003 0-.413 0-.748.333-.75.747s.333.751.747.753l2.546.011c.585.002 1.097.42 1.218.992l.505 2.401 1.81 8.596h-.576c-1.241 0-2.25 1.009-2.25 2.25s1.009 2.25 2.25 2.25h15c.414 0 .75-.336.75-.75s-.336-.75-.75-.75h-15c-.414 0-.75-.336-.75-.75s.336-.75.75-.75h1.499.001 13.5c.354 0 .661-.249.734-.596l.665-3.157c-1.431 1.095-3.213 1.753-5.149 1.753-4.687 0-8.5-3.813-8.5-8.5z"/>
										</svg>
										</div>
										<h2 class="font-semibold text-2xl"></h2>
									</header>
									<main class="p-3 text-center">
										<p>
											Anda belum memilih produk untuk checkout
										</p>
									</main>
									<footer class="flex justify-center bg-transparent">
										<button
										class="bg-green-600 font-semibold text-white py-3 w-full rounded-b-md hover:bg-green-700 focus:outline-none focus:ring shadow-lg hover:shadow-none transition-all duration-300"
										@click="showModal1 = false"
										>
										Confirm
										</button>
									</footer>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="container mx-auto mt-2 mb-8">
				<div class="w-3/4 bg-white px-10">
					<div class="border-t-8">
						<div class="flex justify-between pb-4 mt-2">
							<h1 class="font-semibold text-2xl">Pengiriman dan Pembayaran</h1>
						</div>
						<div class="shadow-md border-1 rounded-lg px-6 py-5 mt-2">
							<div class="sm:flex-wrap items-center -mx-6 px-6 border-b" @click="showModal2 = true">
								<span class="text-sm transition-all duration-300 hover:shadow-none cursor-pointer">
									<button class="bg-gray-200 px-1 py-1 text-sm" disabled>Utama</button> 
									Rumah - Arip/Ulul (6285156644378)
								</span>
								<div
									class="fixed inset-0 w-full h-full bg-black bg-opacity-50 duration-300 overflow-y-auto mt-28"
									x-show="showModal2"
									x-transition:enter="transition duration-300"
									x-transition:enter-start="opacity-0"
									x-transition:enter-end="opacity-100"
									x-transition:leave="transition duration-300"
									x-transition:leave-start="opacity-100"
									x-transition:leave-end="opacity-0"
									>
									<div class="relative sm:w-3/4 md:w-1/2 lg:w-1/3 mx-2 sm:mx-auto my-10 opacity-100">
									<div
									class="relative bg-white shadow-lg rounded-lg text-gray-900 z-20"
									@click.away="showModal2 = false"
									x-show="showModal2"
									x-transition:enter="transition transform duration-300"
									x-transition:enter-start="scale-0"
									x-transition:enter-end="scale-100"
									x-transition:leave="transition transform duration-300"
									x-transition:leave-start="scale-100"
									x-transition:leave-end="scale-0"
									>
									<header class="flex flex-col justify-center items-center p-3">
										<div class="px-5 py-5 bg-white sm:p-6 rounded-md border-2 shadow-md">
											<div class="grid grid-cols-1 gap-6">
												<div id="lblMdlTitle" class="mb-2 text-2xl font-semibold text-gray-700">
													<span>Alamat kamu</span>
													<button class="bg-red-600 hover:bg-red-700 rounded-md text-white p-2 text-lg transition-all duration-300 hover:shadow-none" style="float: right" @click="showModal3 = true">Tambah Alamat</button>
												</div>
												<div
													class="fixed inset-0 w-full h-full bg-black bg-opacity-50 duration-300 overflow-y-auto mt-28"
													x-show="showModal3"
													x-transition:enter="transition duration-300"
													x-transition:enter-start="opacity-0"
													x-transition:enter-end="opacity-100"
													x-transition:leave="transition duration-300"
													x-transition:leave-start="opacity-100"
													x-transition:leave-end="opacity-0"
													>
													<div class="relative sm:w-3/4 md:w-1/2 lg:w-1/3 mx-2 sm:mx-auto my-10 opacity-100">
														<div
															class="relative bg-white shadow-lg rounded-lg text-gray-900 z-20"
															@click.away="showModal3 = false"
															x-show="showModal3"
															x-transition:enter="transition transform duration-300"
															x-transition:enter-start="scale-0"
															x-transition:enter-end="scale-100"
															x-transition:leave="transition transform duration-300"
															x-transition:leave-start="scale-100"
															x-transition:leave-end="scale-0"
															>
															<header class="flex flex-col justify-center items-center p-3">
																<div class="leading-loose">
																	<form class="max-w-xl m-4 p-10 bg-white rounded shadow-xl">
																		<p class="text-gray-800 font-semibold text-xl mb-4">Tambah Alamat Baru</p>
																		<div class="inline-block mt-2 w-1/2 pr-1">
																			<label class="hidden block text-sm text-gray-600" for="cus_email"></label>
																			<input class="w-full px-2 py-2 text-gray-700 bg-gray-200 rounded" id="cus_email" name="cus_email" type="text" required="" placeholder="Nama Penerima" aria-label="Email">
																		</div>
																		<div class="inline-block mt-2 -mx-1 pl-1 w-1/2">
																			<label class="hidden block text-sm text-gray-600" for="cus_email"></label>
																			<input class="w-full px-2 py-2 text-gray-700 bg-gray-200 rounded" id="cus_email"  name="cus_email" type="number" required="" placeholder="Nomor Telepon" aria-label="Email">
																		</div>
																		<div class="mt-2">
																			<label class="block text-sm text-gray-600 w-1/2" for="cus_email"></label>
																			<input class="w-full px-2 py-2 text-gray-700 bg-gray-200 rounded" id="cus_email" name="cus_email" type="text" required="" placeholder="Label Alamat" aria-label="Email">
																		</div>
																		<div class="inline-block mt-2 w-1/2 pr-1">
																			<label class="hidden text-sm block text-gray-600" for="cus_email"></label>
																				<select class="w-full px-2 py-2 text-gray-700 bg-gray-200 rounded" id="cus_email" name="cus_email" type="text" required="" placeholder="Provinsi" aria-label="Email">
																					<option value="">Pilih Provinsi</option>
																				</select>
																		</div>
																		<div class="inline-block mt-2 w-1/2 pr-1">
																			<label class="hidden block text-sm text-gray-600" for="cus_email"></label>
																				<select class="w-full px-2 py-2 text-gray-700 bg-gray-200 rounded" id="cus_email" name="cus_email" type="text" required="" placeholder="Kota" aria-label="Email">
																					<option value="">Pilih Kota</option>
																				</select>
																		</div>
																		<div class="inline-block mt-2 w-1/2 pr-1">
																			<label class="hidden block text-sm text-gray-600" for="cus_email"></label>
																			<select class="w-full px-2 py-2 text-gray-700 bg-gray-200 rounded" id="cus_email" name="cus_email" type="text" required="" placeholder="Kecamatan" aria-label="Email">
																				<option value="">Pilih Kecamatan</option>
																			</select>
																		</div>
																		<div class="mt-2 -mx-1 pl-1 w-1/2">
																			<label class="hidden block text-sm text-gray-600" for="cus_email"></label>
																			<input class="w-full px-2 py-2 text-gray-700 bg-gray-200 rounded" id="cus_email"  name="cus_email" type="text" required="" placeholder="Kode Pos" aria-label="Email">
																		</div>
																		<div class="mt-2">
																			<label class="block text-sm text-gray-600" for="cus_email"></label>
																			<textarea class="w-full px-2 py-2 text-gray-700 bg-gray-200 rounded" id="cus_email" name="cus_email" type="text" required="" placeholder="Alamat" aria-label="Email"></textarea>
																		</div>
																		<label class="flex justify-start items-start mt-2">
																			<div class="bg-white border-2 rounded border-gray-400 w-6 h-6 flex flex-shrink-0 justify-center items-center mr-2 focus-within:border-blue-500">
																				<input type="checkbox" class="opacity-0 absolute">
																				<svg class="fill-current hidden w-4 h-4 text-green-500 pointer-events-none" viewBox="0 0 20 20"><path d="M0 11l2-2 5 5L18 3l2 2L7 18z"/></svg>
																			</div>
																			<div class="select-none">Jadikan Alamat Utama</div>
																		</label>
																		<button
																		class="bg-red-600 px-6 rounded-md font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring shadow-lg hover:shadow-none"
																		>
																		Tambah
																		</button>
																		<button
																		class="bg-red-600 px-6 rounded-md font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring shadow-lg hover:shadow-none" @click="showModal3 = false"
																		>
																		Batal
																		</button>
																	</form>
																</div>
															</header>
														</div>
													</div>
												</div>
												
												<div class="flex flex-row border-2 shadow-sm rounded-md">
													<div class="flex w-full items-center justify-between bg-white
														dark:bg-gray-800 px-8 py-6">
														<!-- card -->
														<div class="flex">
															<div class="flex flex-col ml-6">
																<span class="text-lg font-medium italic">Reza Eka Alfarisi</span>
																<div class="mt-4 flex">
																	<div class="flex">
																		<span
																			class="text-sm
																			dark:text-gray-300 capitalize">
																			Jl. A. Yani No.9A, RT.02/RW.02, Tanah Sareal, Kec. Tanah Sereal, Kota Bogor, Jawa Barat 16161
																		</span>
																	</div>
																</div>
																<span
																	class="text-sm text-gray-400
																	dark:text-gray-100 capitalize">
																	085156644378
																</span>
																<div class="mt-4 flex">
																	<button
																		class="flex items-center
																		focus:outline-none border rounded-md
																		py-2 px-6 leading-none bg-red-600 border-white
																		dark:border-white select-none
																		hover:bg-red-800 hover:text-black
																		dark-hover:text-gray-200">
																		<span class="font-semibold text-white">Jadikan Alamat Utama</span>
																	</button>
																</div>
															</div>
														</div>
													</div>
												</div>
			
												<div class="flex flex-row border-2 shadow-sm rounded-md">
													<div class="flex w-full items-center justify-between bg-white
														dark:bg-gray-800 px-8 py-6">
														<!-- card -->
														<div class="flex">
															<div class="flex flex-col ml-6">
															<div class="mt-2 flex">
																<button
																	class="flex items-center
																	focus:outline-none border rounded-md
																	py-1 px-4 leading-none bg-black border-white
																	dark:border-white select-none
																	hover:bg-red-800 hover:text-black
																	dark-hover:text-gray-200">
																	<span class="font-semibold text-white text-sm">Alamat Utama</span>
																</button>
															</div>
																<span class="text-lg font-medium italic">Reza Eka Alfarisi</span>
																<div class="mt-4 flex">
																	<div class="flex">
																		<span
																			class="text-sm
																			dark:text-gray-300 capitalize">
																			Jl. A. Yani No.9A, RT.02/RW.02, Tanah Sareal, Kec. Tanah Sereal, Kota Bogor, Jawa Barat 16161
																		</span>
																	</div>
																</div>
																<span
																	class="text-sm text-gray-400
																	dark:text-gray-100 capitalize">
																	085156644378
																</span>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</header>
									<main class="p-3 text-center">
										<p>
										
										</p>
									</main>
									<footer class="flex justify-center bg-transparent">
										<button
										class="bg-red-600 font-semibold text-white py-3 w-full rounded-b-md hover:bg-red-700 focus:outline-none focus:ring shadow-lg hover:shadow-none transition-all duration-300"
										@click="showModal2 = false"
										>
										Confirm
										</button>
									</footer>
									</div>
								</div>
							</div>
								<span class="float-right">
									<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
									width="20px" height="20px" viewBox="0 0 306 306" style="enable-background:new 0 0 306 306;" xml:space="preserve"><g>
										<g id="keyboard-arrow-right">
											<polygon points="58.65,267.75 175.95,153 58.65,35.7 94.35,0 247.35,153 94.35,306 "/>
										</g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g> </g><g> </g><g></g><g> </g><g></g>
									</svg>
								</span>
								<span class="sm:flex-wrap block text-sm mb-2 cursor-pointer">Jl. Masjid Al-Baliyah No.32, RT.8/RW.11, Pabuaran, Cibinong, Bogor, Jawa Barat 16916 (Kontrakan Mama Adam)
									Cibinong, Kab. Bogor, 16916
								</span>
							</div>
							<div class="flex items-center -mx-6 px-6 py-5 border-b">
								<div class="justify-items-center md:content-center md:items-center md:place-items-center">
									<span class="flex block font-semibold">Pilih Pengiriman</span>
									<select class="w-32 sm:w-64 sm:h-10 rounded-md pl-4 pr-4 bg-gray-200 focus:border-indigo-600" type="select"
									placeholder="Semua Produk">
										<option>Pilih Pengiriman</option>
										<option>Esok Hari</option>
										<option>Instant</option>
									</select>
								</div>	
								<div class="justify-items-center md:content-center md:items-center md:place-items-center pl-8">
									<span class="flex block font-semibold">Pilih Kurir</span>
									<select class="w-32 sm:w-64 sm:h-10 rounded-md pl-4 pr-4 bg-gray-200 focus:border-indigo-600" type="select"
									placeholder="Semua Produk">
										<option>Pilih Kurir</option>
										<option>Kurir 1</option>
										<option>Kurir 2</option>
									</select>
								</div>
							</div>
							<a href="#"><div class="mt-2">
								<div class="sm:flex items-center -mx-6 px-6">
									<svg height="64px" version="1.1" viewBox="0 0 100 72" width="100px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><title/><desc/><defs><rect height="71.4285714" id="path-1" rx="6.63157895" width="100" x="0" y="0"/></defs><g fill="none" fill-rule="evenodd" id="Content" stroke="none" stroke-width="1"><g id="Indonesian-Bank-Icons" transform="translate(-690.000000, -623.000000)"><g id="BNI" transform="translate(690.000000, 623.000000)"><mask fill="white" id="mask-2"><use xlink:href="#path-1"/></mask><use fill="#FFFFFF" id="Background" xlink:href="#path-1"/><g id="Logo-Bank-BNI" mask="url(#mask-2)"><g transform="translate(8.571429, 24.285714)"><path d="M7.64567209,18.129353 L11.0393948,22.3319602 L2.63714862,22.3319602 L7.64567209,18.129353 Z M6.57848065,16.8311823 L0.00582104422,22.3447975 L0.00312611634,8.78460968 L6.57848065,16.8311823 Z M22.3928564,3.17685437 C22.3928564,3.17685437 20.949992,2.49380367 19.2737469,2.59356009 C17.3573837,2.70642125 15.7541711,3.44135599 14.8325057,4.41110612 C14.2525573,5.02034241 13.6114339,5.7635679 13.28939,6.57071239 C12.822359,7.73837071 12.7226467,9.28125226 12.7226467,9.28125226 C12.7226467,9.28125226 12.0524182,8.71239994 11.2126786,7.05478512 C10.2363063,5.12678493 9.9692389,3.95885916 9.83691794,2.62645564 C9.71914959,1.45719266 9.90402165,0.301034404 9.90402165,0.301034404 L22.3928564,0.301034404 L22.3928564,3.17685437 Z M22.3928564,7.02108724 L22.3928564,15.571256 C22.3928564,15.571256 21.6164477,15.8694555 20.9831396,15.8694555 C20.3498316,15.8694555 19.2713215,15.7755828 18.018719,15.0869158 C16.7752793,14.4038651 16.0869947,13.5774647 15.553399,12.8286229 C15.0122574,12.0669438 14.5654384,11.0110768 14.4325785,10.2502001 C14.3258593,9.64149866 14.2107859,8.12322191 14.8325057,6.8892376 C15.1699107,6.21955907 16.0754065,5.07169157 17.8187553,5.00643535 C19.2397908,4.95187688 20.0277877,5.32549545 20.7500284,5.72184672 C22.1244416,6.47764205 22.3928564,7.02108724 22.3928564,7.02108724 L22.3928564,7.02108724 Z M8.79398085,14.9708453 L11.0466711,13.0931245 C10.3287423,12.1760607 10.1390194,11.8305237 9.33646984,10.6465513 C8.51478633,9.43476483 7.82650175,8.10155898 7.2379295,6.53728195 C6.60084855,4.84275993 6.44696817,3.8914634 6.51730578,2.62645564 C6.60542992,1.01698066 6.97140113,0.301034404 6.97140113,0.301034404 L-0.000107797115,0.301034404 L0.00312611634,4.05834814 C0.00312611634,4.05834814 5.3396223,10.7460403 8.79398085,14.9708453 L8.79398085,14.9708453 Z M15.2305466,22.325809 L22.3928564,22.325809 L22.3928564,17.1034398 C22.3928564,17.1034398 20.61636,17.9183401 19.4615834,17.9841312 C18.7741073,18.0242477 18.1068431,18.0507246 17.0302194,17.7642927 C15.833402,17.4463023 15.1534717,17.1363353 14.0442393,16.2214111 C13.1532962,15.4875461 12.1346135,14.4038651 12.1346135,14.4038651 L9.86871809,16.293086 C10.9200095,17.5789543 11.6964182,18.5150066 11.9341108,18.7669383 C12.9560275,19.8468752 13.3891024,20.2437614 14.077387,20.6286125 C14.765402,21.0142661 17.0641755,20.9934055 17.0641755,20.9934055 L15.2305466,22.325809 Z" fill="#E94E0F" id="46"/><path d="M76.561338,17.6803956 C76.561338,19.193591 76.765344,21.6808155 75.0707733,22.2863076 L75.0707733,22.3871338 L81.5068001,22.3871338 L81.5068001,22.2863076 C79.8133074,21.6476525 80.016505,19.193591 80.016505,17.6803956 L80.016505,5.00705047 C80.016505,3.49465748 79.8133074,1.00689804 81.5068001,0.368242956 L81.5068001,0.267416756 L75.0707733,0.267416756 L75.0707733,0.368242956 C76.7984916,1.00689804 76.561338,3.4609596 76.561338,5.00705047 L76.561338,17.6803956 Z M52.961585,5.00705047 L64.5807666,19.6305936 C66.0707922,21.5462914 67.1215446,22.656182 69.5264982,23.4625241 L69.5264982,4.60374567 C69.5264982,2.72147832 69.5264982,1.37650495 71.2884421,0.368242956 L71.2884421,0.267416756 L65.3259141,0.267416756 L65.3259141,0.368242956 C67.1215446,1.37650495 67.0870495,2.72147832 67.0870495,4.60374567 L67.0870495,17.3444866 L54.6895728,1.81350763 L54.2147265,1.14115467 C53.9781118,0.872373954 53.8428264,0.704419436 53.7072716,0.301114637 L53.7072716,0.267416756 L48.7607315,0.267416756 L48.7607315,0.368242956 C50.5224058,1.37650495 50.5224058,2.72147832 50.5224058,4.60374567 L50.5224058,18.0173744 C50.5224058,19.8993744 50.5224058,21.2438128 48.7275838,22.2863076 L48.7275838,22.3871338 L54.7235289,22.3871338 L54.7235289,22.2863076 C52.9278984,21.2438128 52.961585,19.8993744 52.961585,18.0173744 L52.961585,5.00705047 Z M34.0219014,9.17595976 L34.0219014,2.58722168 L36.2910307,2.35133651 C38.3238148,2.14968411 40.4913453,3.3601334 40.4913453,5.67993832 C40.4913453,8.43647848 37.3409746,9.17595976 35.1397575,9.17595976 L34.0219014,9.17595976 Z M34.0219014,19.697722 L34.0219014,11.3941362 L36.3926295,11.2598795 C39.3737587,11.0916576 42.557816,12.5040267 42.557816,15.7646978 C42.557816,18.9582407 39.7470062,20.2355508 36.9685355,19.9667701 L34.0219014,19.697722 Z M30.5661954,17.6469652 C30.5661954,19.193591 30.7696624,21.6476525 29.0753613,22.2863076 L29.0753613,22.3871338 L34.6649112,22.3871338 C37.2054197,22.3871338 39.8822916,22.4882274 42.2530196,21.3451739 C44.5226879,20.2692487 46.3183183,18.05027 46.3183183,15.4616844 C46.3183183,12.1678502 43.8449135,10.1505239 40.7961416,9.51133391 C42.7273269,8.67129387 44.1833965,7.39398371 44.1833965,5.14157455 C44.1833965,2.14968411 41.5757842,0.267416756 37.1046294,0.267416756 L29.0753613,0.267416756 L29.0753613,0.368242956 C30.803888,1.00689804 30.5661954,3.4609596 30.5661954,4.97335259 L30.5661954,17.6469652 Z" fill="#005B6A" id="BNI"/></g></g></g></g></g>
									</svg>
									<span>BNI Virtual Account</span>
									<div class="float-right">
										<span>
											<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
											width="20px" height="20px" viewBox="0 0 306 306" style="enable-background:new 0 0 306 306;" xml:space="preserve"><g>
												<g id="keyboard-arrow-right">
													<polygon points="58.65,267.75 175.95,153 58.65,35.7 94.35,0 247.35,153 94.35,306 "/>
												</g>
											</svg>
										</span>
									</div>
								</div>
							</a>
						</div>
					</div>
				</div>
			</div>
		</body>
		<script src="https://unpkg.com/@popperjs/core@2.9.1/dist/umd/popper.min.js" charset="utf-8"></script>
		<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.1.min.js"></script>
		<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
		<script>
			function openDropdown(event,dropdownID){
				let element = event.target;
				while(element.nodeName !== "BUTTON"){
				element = element.parentNode;
				}
				var popper = Popper.createPopper(element, document.getElementById(dropdownID), {
				placement: 'bottom-start'
				});
				document.getElementById(dropdownID).classList.toggle("hidden");
				document.getElementById(dropdownID).classList.toggle("block");
				}
			function openDropdown(event,dropdownID){
				let element = event.target;
				while(element.nodeName !== "BUTTON"){
				element = element.parentNode;
				}
				var popper = Popper.createPopper(element, document.getElementById(dropdownID), {
				placement: 'bottom-start'
				});
				document.getElementById(dropdownID).classList.toggle("hidden");
				document.getElementById(dropdownID).classList.toggle("block");
			}
			function decrement(e) {
				const btn = e.target.parentNode.parentElement.querySelector(
				'button[data-action="decrement"]'
				);
				const target = btn.nextElementSibling;
				let value = Number(target.value);
				value--;
				console.log(value);
				if(value < 0) { value = 0 }; // Ini untuk minimal nilai menjadi 0
				target.value = value;
			}
			function increment(e) {
				const btn = e.target.parentNode.parentElement.querySelector(
				'button[data-action="decrement"]'
				);
				const target = btn.nextElementSibling;
				let value = Number(target.value);
				value++;
				target.value = value;
			}
			const decrementButtons = document.querySelectorAll(
				`button[data-action="decrement"]`
			);
			const incrementButtons = document.querySelectorAll(
				`button[data-action="increment"]`
			);
			decrementButtons.forEach(btn => {
				btn.addEventListener("click", decrement);
			});
			incrementButtons.forEach(btn => {
				btn.addEventListener("click", increment);
			});
		</script>
	</body>
</html>