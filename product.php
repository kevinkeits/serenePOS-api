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
			.row:after {
				content: "";
				display: table;
				clear: both;
			}
			.column {
				float: left;
				width: 33%;
			}

			/* The Modal (background) */
			.modal {
				display: none;
				position: fixed;
				padding-top: 140px;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
				overflow: auto;
				background-color: black;
			}

			/* Modal Content */
			.modal-content {
				position: relative;
				background-color: #fefefe;
				margin: auto;
				width: 100%;
				max-width: 1000px;
			}

			/* The Close Button */
			.close {
				color: white;
				position: absolute;
				top: 10px;
				right: 25px;
				font-size: 35px;
				font-weight: bold;
			}

			.close:hover,
			.close:focus {
				color: #999;
				text-decoration: none;
				cursor: pointer;
			}

			.mySlides {
				display: none;
			}

			.cursor {
				cursor: pointer;
			}

			/* Next & previous buttons */
			.prev,
			.next {
				cursor: pointer;
				position: absolute;
				top: 50%;
				width: auto;
				padding: 16px;
				margin-top: -50px;
				color: black;
				font-weight: bold;
				font-size: 20px;
				transition: 0.6s ease;
				border-radius: 0 3px 3px 0;
				user-select: none;
				-webkit-user-select: none;
			}

			/* Position the "next button" to the right */
			.next {
				right: 0;
				border-radius: 3px 0 0 3px;
			}

			/* On hover, add a black background color with a little bit see-through */
			.prev:hover,
			.next:hover {
				background-color: rgba(0, 0, 0, 0.8);
			}

			/* Number text (1/3 etc) */
			.numbertext {
				color: #f2f2f2;
				font-size: 12px;
				padding: 8px 12px;
				position: absolute;
				top: 0;
			}

			img {
				margin-bottom: -4px;
			}

			.demo {
				opacity: 0.6;
			}

			.active,
			.demo:hover {
				opacity: 1;
			}

			img.hover-shadow {
				transition: 0.3s;
			}

			.hover-shadow:hover {
				box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
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
		
		<div class="bg-white shadow-sm sticky top-0">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-1 md:py-4">
				<div class="flex items-center justify-between md:justify-start">
				<!-- Menu Trigger -->
					<button type="button" class="md:hidden w-10 h-10 rounded-lg -ml-2 flex justify-center items-center">
						<svg class="text-gray-500 w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
						</svg>
					</button>
					<!-- ./ Menu Trigger -->
					<a href="category.php" class="font-bold text-gray-700 text-2xl">Kategori</a>

					<div class="hidden md:flex space-x-3 flex-1 lg:ml-8 mt-2">
						<a href="category.php" class="py-2 hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600">Ikan Tenggiri</a>
						<a href="category.php" class="px-2 py-2 hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600">Ikan Bawal</a>
						<a href="category.php" class="px-2 py-2 hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600">Ikan Pihi</a>
						<a href="category.php" class="px-2 py-2 hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600">Ikan Gabus</a>
						<a href="category.php" class="px-2 py-2 hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600">Ikan Marlin</a>
						<a href="category.php" class="px-2 py-2 hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600">Ikan Kakap</a>
						<a href="category.php" class="px-2 py-2 hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600">Cumi</a>
						<a href="category.php" class="px-2 py-2 hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600">Bakso</a>
					</div>
				</div>
					<!-- Search Mobile -->
				<div class="relative md:hidden">
					<input type="search" class="mt-1 w-full pl-10 pr-2 h-10 py-1 rounded-lg border border-gray-200 focus:border-gray-300 focus:outline-none focus:shadow-inner leading-none" placeholder="Search">
					<svg class="h-6 w-6 text-gray-300 ml-2 mt-3 stroke-current absolute top-0 left-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
					</svg>
				</div>
				<!-- ./ Search Mobile -->
			</div>
		</div>

		<div class="py-6">
		<!-- Breadcrumbs -->
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<div class="flex items-center space-x-2 text-gray-400 text-sm">
					<a href="index.php" class="hover:underline hover:text-gray-600">Home</a>
				<span>
					<svg class="h-5 w-5 leading-none text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
				</span>
				<a href="category.php" class="hover:underline hover:text-gray-600">Ikan Gabus</a>
				<span>
					<svg class="h-5 w-5 leading-none text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
				</span>
				<span>Ikan Kakap</span>
				</div>
			</div>
			<!-- ./ Breadcrumbs -->

			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
				<div class="flex flex-col md:flex-row -mx-4">
					<div class="md:flex-1 px-4">
						<div x-data="{ image: 1 }" x-cloak>
							<div class="h-64 md:h-80 rounded-lg mb-4">
								<div x-show="image === 1" class="h-64 md:h-80 rounded-lg mb-4 flex items-center justify-center">
								<img src="https://images.unsplash.com/photo-1612939675110-fe3a0129a024?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MXx8bWFrYW5hbnxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="h-64 md:h-80 rounded-lg">
								</div>

								<div x-show="image === 2" class="h-64 md:h-80 rounded-lg mb-4 flex items-center justify-center">
								<img src="https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?ixid=MnwxMjA3fDB8MHxzZWFyY2h8Mnx8ZmlzaCUyMGZvb2R8ZW58MHx8MHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="h-64 md:h-80 rounded-lg">
								</div>

								<div x-show="image === 3" class="h-64 md:h-80 rounded-lg mb-4 flex items-center justify-center">
								<img src="https://images.unsplash.com/photo-1534948216015-843149f72be3?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MjR8fGZpc2glMjBmb29kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="h-64 md:h-80 rounded-lg">
								</div>

								<div x-show="image === 4" class="h-64 md:h-80 rounded-lg mb-4 flex items-center justify-center">
								<img src="https://images.unsplash.com/photo-1563557908-b7787229f123?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MzN8fGZpc2glMjBmb29kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="h-64 md:h-80 rounded-lg">
								</div>
							</div>
							<div class="flex -mx-2 mb-4">
								<template x-for="i in 4">
									<div class="flex-1 px-2">
										<button x-on:click="image = i" :class="{ 'ring-2 ring-indigo-300 ring-inset': image === i }" class="focus:outline-none w-full rounded-lg h-24 md:h-32 bg-gray-100 flex items-center justify-center">
											<span x-text="i" class="text-2xl"></span>
										</button>
									</div>
								</template>
							</div>
						</div>
					</div>
					<div class="md:flex-1 px-4">
						<h2 class="mb-2 leading-tight tracking-tight font-bold text-gray-800 text-2xl md:text-3xl">Ikan Salmon Trout Fresh Grade Sashimi 500 gram</h2>
						<p class="text-gray-400 text-sm">Terjual 1.000</p>
						<div class="flex items-center space-x-4 my-4">
							<div class="rounded-lg bg-gray-100 flex py-2 px-3">
								<span class="text-indigo-400 mr-1 mt-1">Rp</span>
								<span class="font-bold text-indigo-600 text-3xl">135.000</span>
							</div>
							<div class="flex-1">
								<p class="text-green-500 text-xl font-semibold">Diskon 10%</p>
								<s class="text-gray-400 text-sm">150.000</s>
							</div>
						</div>

						<p class="text-gray-500">
						Minimal pembelian 500 Gram Salmon Trout Fresh Sashimi Grade

						Stock ready selama masih ada di etalase 🙏🏻😊

						Ikan Salmon Trout Fresh Sashimi Grade cocok untuk dibuat sushi dan dimakan mentah (Sashimi)

						- Rekomendasi penyajian salmon sashimi grade maksimal 3 hari setelah salmon sampai di tangan pembeli

						- cara penyimpanan salmon fresh adalah di chiller / dikulkas</p>

						<div class="">
							<h1 class="flex text-xl mb-2 mt-4 font-semibold">Pengiriman</h1>
							<div class="md:flex-nowrap">
								<span class="text-gray-600">Dikirim Dari Cabang:</span>
								<select class="float-right mr-36">
									<option>Grogol</option>
									<option>Jaksel</option>
									<option>Bogor</option>
								</select>
							</div>
							<div class="md:flex-nowrap mt-1">
								<span class="text-gray-600">Dikirim ke Alamat:</span>
								<select class="float-right mr-36">
									<option>Kab. Bandung</option>
									<option>Kota Bandung</option>
									<option>Jakarta</option>
								</select>
							</div>
							<div class="md:flex-nowrap mt-1">
								<span class="text-gray-600">Ongkos Kirim:</span>
								<span class="float-right mr-40">Rp10.000</span>
							</div>
						</div>

						<div class="">
							<h1 class="flex text-xl mb-2 mt-4 font-semibold">Detail Produk</h1>
							<div class="md:flex-nowrap">
								<span class="text-gray-600">Kondisi:</span>
								<span class="float-right mr-36">Baru</span>
							</div>
							<div class="md:flex-nowrap mt-1">
								<span class="text-gray-600">Minimal Pemesanan:</span>
								<span class="float-right mr-36">1 Buah</span>
							</div>
							<div class="md:flex-nowrap mt-1">
								<span class="text-gray-600">Stok:</span>
								<span class="float-right mr-36">10000</span>
							</div>
							<div class="md:flex-nowrap mt-1">
								<span class="text-gray-600">Harga Grosir:</span>
								<span class="float-right mr-36">Rp130.000</span>
							</div>
						</div>

						<div class="flex py-4 space-x-4">
							<div class="relative">
								<div class="text-center left-0 pt-2 right-0 absolute block text-xs uppercase text-gray-400 tracking-wide font-semibold">Qty</div>
								<select class="cursor-pointer appearance-none rounded-xl border border-gray-200 pl-4 pr-8 h-14 flex items-end pb-1">
									<option>1</option>
									<option>2</option>
									<option>3</option>
									<option>4</option>
									<option>5</option>
								</select>

								<svg class="w-5 h-5 text-gray-400 absolute right-0 bottom-0 mb-2 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
								</svg>
							</div>

							<a href="cart.php" class="hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600">
								<button type="button" class="h-14 px-6 py-2 font-semibold rounded-xl bg-red-600 hover:bg-red-700 text-white">
									<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="white">
										<path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
									</svg>
								</button>
							</a>

							<a href="payment.php" class="hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-600">
								<button type="button" class="h-14 px-6 py-2 font-semibold rounded-xl bg-red-600 hover:bg-red-700 text-white">
									Beli Langsung
								</button>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script src='https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.min.js'></script>
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
		</script>

		<script>
			function openModal() {
			document.getElementById("myModal").style.display = "block";
			}
			
			function closeModal() {
			document.getElementById("myModal").style.display = "none";
			}
			
			var slideIndex = 1;
			showSlides(slideIndex);
			
			function plusSlides(n) {
			showSlides(slideIndex += n);
			}
			
			function currentSlide(n) {
			showSlides(slideIndex = n);
			}
			
			function showSlides(n) {
			var i;
			var slides = document.getElementsByClassName("mySlides");
			var dots = document.getElementsByClassName("demo");
			var captionText = document.getElementById("caption");
			if (n > slides.length) {slideIndex = 1}
			if (n < 1) {slideIndex = slides.length}
			for (i = 0; i < slides.length; i++) {
				slides[i].style.display = "none";
			}
			for (i = 0; i < dots.length; i++) {
				dots[i].className = dots[i].className.replace(" active", "");
			}
			slides[slideIndex-1].style.display = "block";
			dots[slideIndex-1].className += " active";
			captionText.innerHTML = dots[slideIndex-1].alt;
			}
			</script>
	</body>
</html>