<!DOCTYPE html>
<html lang="en">
	
	<head>
		<meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Ella Froze</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <link href="assets/favicon.png" rel="shortcut icon" />
        <link href="assets/css/tailwind.css" rel="stylesheet" />
        <link href="assets/css/pace-flash.css" rel="stylesheet" />
        <link href="assets/plugin/fontawesome-5.15.3/css/all.css" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css?family=Work+Sans:200,400&display=swap" rel="stylesheet" />
		<link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet" />
        
		<script src="assets/js/popper-2.9.1.min.js"></script>
        <script src="assets/js/alpine-2.8.2.min.js"></script>
        <script src="assets/js/jquery-3.6.0.min.js"></script>
        <script src="assets/js/sweetalert2.all.min.js"></script>
        <script src="assets/js/polyfill.js"></script>
        <script src="assets/js/pace.min.js"></script>
        <script src="assets/js/helper.js?ts=<?=time()?>"></script>
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
		</style>
	</head>

	<body class="bg-white work-sans leading-normal text-base tracking-normal">
		<!--Nav-->
        <nav id="header" class="sticky bg-red-600 top-0 w-full z-30 top-0 py-1 text-white">
            <div class="w-full container mx-auto flex flex-wrap items-center justify-between mt-0 py-1">
                <a href="index.php"><img class="hidden sm:block flex items-center tracking-wide rounded-md" width="150px" src="assets/img/logo.png" href="index.php"></a>
                <div class="grid grid-rows-1 w-3/4 sm:w-1/2">
                    <div class="shadow flex sm:mt-8">
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
                    <a class="pl-6 inline-block no-underline hover:text-white" href="cart.php" onclick="openDropdown(event,'dropdown-id-1')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="white">
                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                        </svg>
                    </a>
                    <div x-data="{ dropdownOpen: false }">
                        <button @click="dropdownOpen = !dropdownOpen" class="relative inline-block rounded-md bg-transparant p-2 focus:outline-none ml-6 hidden sm:block">
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
                        <svg viewBox="0 0 512 512" class="h-6 w-6" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="m437.019531 74.980469c-48.351562-48.351563-112.640625-74.980469-181.019531-74.980469-68.382812 0-132.667969 26.628906-181.019531 74.980469-48.351563 48.351562-74.980469 112.636719-74.980469 181.019531 0 68.378906 26.628906 132.667969 74.980469 181.019531 48.351562 48.351563 112.636719 74.980469 181.019531 74.980469 68.378906 0 132.667969-26.628906 181.019531-74.980469 48.351563-48.351562 74.980469-112.640625 74.980469-181.019531 0-68.382812-26.628906-132.667969-74.980469-181.019531zm-308.679687 367.40625c10.707031-61.648438 64.128906-107.121094 127.660156-107.121094 63.535156 0 116.953125 45.472656 127.660156 107.121094-36.347656 24.972656-80.324218 39.613281-127.660156 39.613281s-91.3125-14.640625-127.660156-39.613281zm46.261718-218.519531c0-44.886719 36.515626-81.398438 81.398438-81.398438s81.398438 36.515625 81.398438 81.398438c0 44.882812-36.515626 81.398437-81.398438 81.398437s-81.398438-36.515625-81.398438-81.398437zm235.042969 197.710937c-8.074219-28.699219-24.109375-54.738281-46.585937-75.078125-13.789063-12.480469-29.484375-22.328125-46.359375-29.269531 30.5-19.894531 50.703125-54.3125 50.703125-93.363281 0-61.425782-49.976563-111.398438-111.402344-111.398438s-111.398438 49.972656-111.398438 111.398438c0 39.050781 20.203126 73.46875 50.699219 93.363281-16.871093 6.941406-32.570312 16.785156-46.359375 29.265625-22.472656 20.339844-38.511718 46.378906-46.585937 75.078125-44.472657-41.300781-72.355469-100.238281-72.355469-165.574219 0-124.617188 101.382812-226 226-226s226 101.382812 226 226c0 65.339844-27.882812 124.277344-72.355469 165.578125zm0 0"/>
                        </svg>
                    </button>
                    <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" id="dropdown-id-2">
                        <a href="selling.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Pembelian</a>
                        <a href="user_profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Profile</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-3">Sign out</a>
                    </div>
                </div>
            </div>
        </nav>

		<body x-data="{ showModal2: false }" :class="{'overflow-y-hidden':showModal2}" class="bg-gray-100">
			<div class="container mx-auto mt-10">
				<div class="flex shadow-md my-10">
					<div class="w-3/4 bg-white px-10 py-10">
						<div class="flex justify-between border-b pb-8">
							<h1 class="font-semibold text-2xl">Keranjang Belanja</h1>
							<h2 class="font-semibold text-2xl">3 Items</h2>
						</div>
						<div class="flex mt-10 mb-5">
							<h3 class="font-semibold text-gray-600 text-xs uppercase w-2/5">Detail Produk</h3>
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
							<span class="text-center w-1/5 font-semibold text-sm">Rp150.000</span>
							<span class="text-center w-1/5 font-semibold text-sm">Rp150.000</span>
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
							<span class="text-center w-1/5 font-semibold text-sm">Rp100.000</span>
							<span class="text-center w-1/5 font-semibold text-sm">Rp100.000</span>
						</div>
						<div class="flex items-center hover:bg-gray-100 -mx-8 px-6 py-5">
							<div class="flex w-2/5"> <!-- product -->
								<div class="w-20">
									<img class="h-24 rounded-md" src="https://images.unsplash.com/photo-1611506168759-1e69a83b5a53?ixid=MnwxMjA3fDB8MHxzZWFyY2h8NjZ8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="">
								</div>
								<div class="flex flex-col justify-between ml-4 flex-grow">
									<span class="font-bold text-sm">Kakap Hitam Fillet 500g</span>
									<span class="text-red-500 text-xs">Ikan Kakap</span>
									<a href="#" class="font-semibold hover:text-red-500 text-gray-500 text-xs">Remove</a>
								</div>
							</div>
							<div class="flex justify-center w-1/5">
								<div class="flex flex-row h-8 w-1/2 rounded-lg relative bg-transparent">
									<button data-action="decrement" class="bg-gray-300 text-gray-600 hover:text-gray-700 hover:bg-gray-400 h-full w-10 rounded-l cursor-pointer outline-none">
										<span class="m-auto text-2xl font-thin">−</span>
									</button>
									<input type="number" min="0" class="outline-none focus:outline-none text-center w-1/2 bg-gray-200 font-semibold text-md hover:text-black focus:text-black md:text-basecursor-default flex items-center text-gray-700 outline-none" name="custom-input-number" value="0"></input>
									<button data-action="increment" class="bg-gray-300 text-gray-600 hover:text-gray-700 hover:bg-gray-400 h-full w-10 rounded-r cursor-pointer">
										<span class="m-auto text-2xl font-thin">+</span>
									</button>
								</div>
							</div>
							<span class="text-center w-1/5 font-semibold text-sm">Rp120.000</span>
							<span class="text-center w-1/5 font-semibold text-sm">Rp120.000</span>
						</div>

						<a href="index.php" class="flex font-semibold text-indigo-600 text-sm mt-10">
							<svg class="fill-current mr-2 text-indigo-600 w-4" viewBox="0 0 448 512">
								<path d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z"/>
							</svg>Belanja Lagi
						</a>
					</div>

					<div id="summary" class="w-1/4 px-8 py-10">
						<h1 class="font-semibold text-2xl border-b pb-8">Ringkasan Belanja</h1>
						<div class="flex justify-between mt-10 mb-5">
							<span class="font-semibold text-sm uppercase">3 Items</span>
							<span class="font-semibold text-sm">Rp370.000</span>
						</div>
						<div>
							<label class="font-medium inline-block mb-3 text-sm uppercase">Pengiriman</label>
							<select class="block p-2 text-gray-600 w-full text-sm">
								<option>Pengiriman Standar - Rp10.000</option>
								<option>Pengiriman Cepat - Rp16.000</option>
								<option>Pengiriman NextDay - Rp20.000</option>
							</select>
						</div>
						<div class="py-10">
							<label for="promo" class="font-semibold inline-block mb-3 text-sm uppercase">Kode Promo</label>
							<input type="text" id="promo" placeholder="Masukan Kode Promo" class="p-2 text-sm w-full">
						</div>
						<button class="bg-red-500 hover:bg-red-600 px-5 py-2 text-sm text-white uppercase rounded-md">Pakai</button>
						<div class="border-t mt-8">
							<div class="flex font-semibold justify-between py-6 text-sm uppercase">
								<span>Total Harga</span>
								<span>Rp380.000</span>
							</div>
							<button class="bg-indigo-500 font-semibold hover:bg-indigo-600 py-3 text-sm text-white uppercase w-full transition-all duration-300 hover:shadow-none" @click="showModal2 = true">Checkout</button>
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
										@click="showModal2 = false"
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