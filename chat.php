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

			<div class="h-screen w-full flex overflow-hidden">
			<nav class="flex flex-col bg-gray-200 dark:bg-gray-900 w-64 px-12 pt-4 pb-6">
			<!-- SideNavBar -->
				<div class="mt-8">
					<!-- User info -->
					<img
						class="h-12 w-12 rounded-full object-cover"
						src="https://inews.gtimg.com/newsapp_match/0/8693739867/0"
						alt="" />
					<h2
						class="mt-4 text-xl dark:text-gray-300 font-extrabold capitalize">
						Hello Reza
					</h2>
					<span class="text-sm dark:text-gray-300">
						<span class="font-semibold text-green-600 dark:text-green-300">
						User id
						</span>
						id789038
					</span>
				</div>

				<ul class="mt-2 text-gray-600">
					<!-- Links -->
					<li class="mt-8">
						<a href="chat.php" class="flex ">
						<svg xmlns="http://www.w3.org/2000/svg" class="fill-current h-5 w-5 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
						</svg>
							<span
								class="ml-2 capitalize font-medium text-black
								dark:text-gray-300">
								Chat
							</span>
						</a>
					</li>

					<li class="mt-8">
						<a href="complain.php" class="flex">
							<svg
								class="fill-current h-5 w-5 dark:text-gray-300"
								viewBox="0 0 24 24">
								<path
									d="M19 19H5V8h14m-3-7v2H8V1H6v2H5c-1.11 0-2 .89-2
									2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0
									00-2-2h-1V1m-1 11h-5v5h5v-5z"></path>
							</svg>
							<span
								class="ml-2 capitalize font-medium text-black
								dark:text-gray-300">
								Pesan Komplain
							</span>
						</a>
					</li>

					<li class="mt-8">
						<a href="selling.php" class="flex">
							<svg
								class="fill-current h-5 w-5 dark:text-gray-300"
								viewBox="0 0 24 24">
								<path
									d="M19 19H5V8h14m-3-7v2H8V1H6v2H5c-1.11 0-2 .89-2
									2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0
									00-2-2h-1V1m-1 11h-5v5h5v-5z"></path>
							</svg>
							<span
								class="ml-2 capitalize font-medium text-black
								dark:text-gray-300">
								Daftar Transaksi
							</span>
						</a>
					</li>

					<li class="mt-8">
						<a href="#home" class="flex">
							<svg
								class="fill-current h-5 w-5 dark:text-gray-300"
								viewBox="0 0 24 24">
								<path
									d="M19 19H5V8h14m-3-7v2H8V1H6v2H5c-1.11 0-2 .89-2
									2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0
									00-2-2h-1V1m-1 11h-5v5h5v-5z"></path>
							</svg>
							<span
								class="ml-2 capitalize font-medium text-black
								dark:text-gray-300">
								Pusat Bantuan
							</span>
						</a>
					</li>

					<li class="mt-8">
						<a href="user_profile.php" class="flex">
							<svg
								class="fill-current h-5 w-5 dark:text-gray-300"
								viewBox="0 0 24 24">
								<path
									d="M12 13H7v5h5v2H5V10h2v1h5v2M8
									4v2H4V4h4m2-2H2v6h8V2m10 9v2h-4v-2h4m2-2h-8v6h8V9m-2
									9v2h-4v-2h4m2-2h-8v6h8v-6z"></path>
							</svg>
							<span
								class="ml-2 capitalize font-medium text-black
								dark:text-gray-300">
								Pengaturan
							</span>
						</a>
					</li>

				</ul>

				<div class="mt-auto flex items-center text-red-700 dark:text-red-400">
					<!-- important action -->
					<a href="#home" class="flex items-center">
						<svg class="fill-current h-5 w-5" viewBox="0 0 24 24">
							<path
								d="M16 17v-3H9v-4h7V7l5 5-5 5M14 2a2 2 0 012
								2v2h-2V4H5v16h9v-2h2v2a2 2 0 01-2 2H5a2 2 0 01-2-2V4a2 2
								0 012-2h9z"></path>
						</svg>
						<span class="ml-2 capitalize font-medium">log out</span>
					</a>

				</div>
			</nav>

			<div class="flex-1 p:2 sm:p-6 justify-between flex flex-col h-screen">
			<div class="flex sm:items-center justify-between py-3 border-b-2 border-gray-200">
				<div class="flex items-center space-x-4">
					<img src="https://images.unsplash.com/photo-1549078642-b2ba4bda0cdb?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=3&amp;w=144&amp;h=144" alt="" class="w-10 sm:w-16 h-10 sm:h-16 rounded-full">
					<div class="flex flex-col leading-tight">
						<div class="text-2xl mt-1 flex items-center">
						<span class="text-gray-700 mr-3">Ella-Froze</span>
						<!-- <span class="text-green-500">
							<svg width="10" height="10">
								<circle cx="5" cy="5" r="5" fill="currentColor"></circle>
							</svg>
						</span> -->
						<span class="text-red-500">
							<svg width="10" height="10">
								<circle cx="5" cy="5" r="5" fill="currentColor"></circle>
							</svg>
						</span>
						</div>
						<span class="text-lg text-gray-600">Reza</span>
					</div>
				</div>
				<div class="flex items-center space-x-2">
					<button type="button" class="inline-flex items-center justify-center rounded-full h-10 w-10 transition duration-500 ease-in-out text-gray-500 hover:bg-gray-300 focus:outline-none">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
						</svg>
					</button>
				</div>
			</div>
			<div id="messages" class="flex flex-col space-y-4 p-3 overflow-y-auto scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-blue-lighter scrollbar-w-2 scrolling-touch">
				<div class="chat-message">
					<div class="flex items-end">
						<div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-2 items-start">
						<div><span class="px-4 py-2 rounded-lg inline-block rounded-bl-none bg-gray-300 text-gray-600">Can be verified on any platform using docker</span></div>
						</div>
						<img src="https://images.unsplash.com/photo-1549078642-b2ba4bda0cdb?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=3&amp;w=144&amp;h=144" alt="My profile" class="w-6 h-6 rounded-full order-1">
					</div>
				</div>
				<div class="chat-message">
					<div class="flex items-end justify-end">
						<div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-1 items-end">
						<div><span class="px-4 py-2 rounded-lg inline-block rounded-br-none bg-blue-600 text-white ">Your error message says permission denied, npm global installs must be given root privileges.</span></div>
						</div>
						<img src="https://images.unsplash.com/photo-1590031905470-a1a1feacbb0b?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=3&amp;w=144&amp;h=144" alt="My profile" class="w-6 h-6 rounded-full order-2">
					</div>
				</div>
				<div class="chat-message">
					<div class="flex items-end">
						<div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-2 items-start">
						<div><span class="px-4 py-2 rounded-lg inline-block bg-gray-300 text-gray-600">Command was run with root privileges. I'm sure about that.</span></div>
						<div><span class="px-4 py-2 rounded-lg inline-block bg-gray-300 text-gray-600">I've update the description so it's more obviously now</span></div>
						<div><span class="px-4 py-2 rounded-lg inline-block bg-gray-300 text-gray-600">FYI https://askubuntu.com/a/700266/510172</span></div>
						<div>
							<span class="px-4 py-2 rounded-lg inline-block rounded-bl-none bg-gray-300 text-gray-600">
								Check the line above (it ends with a # so, I'm running it as root )
								<pre># npm install -g @vue/devtools</pre>
							</span>
						</div>
						</div>
						<img src="https://images.unsplash.com/photo-1549078642-b2ba4bda0cdb?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=3&amp;w=144&amp;h=144" alt="My profile" class="w-6 h-6 rounded-full order-1">
					</div>
				</div>
				<div class="chat-message">
					<div class="flex items-end justify-end">
						<div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-1 items-end">
						<div><span class="px-4 py-2 rounded-lg inline-block rounded-br-none bg-blue-600 text-white ">Any updates on this issue? I'm getting the same error when trying to install devtools. Thanks</span></div>
						</div>
						<img src="https://images.unsplash.com/photo-1590031905470-a1a1feacbb0b?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=3&amp;w=144&amp;h=144" alt="My profile" class="w-6 h-6 rounded-full order-2">
					</div>
				</div>
				<div class="chat-message">
					<div class="flex items-end">
						<div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-2 items-start">
						<div><span class="px-4 py-2 rounded-lg inline-block rounded-bl-none bg-gray-300 text-gray-600">Thanks for your message David. I thought I'm alone with this issue. Please, 👍 the issue to support it :)</span></div>
						</div>
						<img src="https://images.unsplash.com/photo-1549078642-b2ba4bda0cdb?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=3&amp;w=144&amp;h=144" alt="My profile" class="w-6 h-6 rounded-full order-1">
					</div>
				</div>
				<div class="chat-message">
					<div class="flex items-end justify-end">
						<div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-1 items-end">
						<div><span class="px-4 py-2 rounded-lg inline-block bg-blue-600 text-white ">Are you using sudo?</span></div>
						<div><span class="px-4 py-2 rounded-lg inline-block rounded-br-none bg-blue-600 text-white ">Run this command sudo chown -R `whoami` /Users/{{your_user_profile}}/.npm-global/ then install the package globally without using sudo</span></div>
						</div>
						<img src="https://images.unsplash.com/photo-1590031905470-a1a1feacbb0b?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=3&amp;w=144&amp;h=144" alt="My profile" class="w-6 h-6 rounded-full order-2">
					</div>
				</div>
				<div class="chat-message">
					<div class="flex items-end">
						<div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-2 items-start">
						<div><span class="px-4 py-2 rounded-lg inline-block bg-gray-300 text-gray-600">It seems like you are from Mac OS world. There is no /Users/ folder on linux 😄</span></div>
						<div><span class="px-4 py-2 rounded-lg inline-block rounded-bl-none bg-gray-300 text-gray-600">I have no issue with any other packages installed with root permission globally.</span></div>
						</div>
						<img src="https://images.unsplash.com/photo-1549078642-b2ba4bda0cdb?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=3&amp;w=144&amp;h=144" alt="My profile" class="w-6 h-6 rounded-full order-1">
					</div>
				</div>
				<div class="chat-message">
					<div class="flex items-end justify-end">
						<div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-1 items-end">
						<div><span class="px-4 py-2 rounded-lg inline-block rounded-br-none bg-blue-600 text-white ">yes, I have a mac. I never had issues with root permission as well, but this helped me to solve the problem</span></div>
						</div>
						<img src="https://images.unsplash.com/photo-1590031905470-a1a1feacbb0b?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=3&amp;w=144&amp;h=144" alt="My profile" class="w-6 h-6 rounded-full order-2">
					</div>
				</div>
				<div class="chat-message">
					<div class="flex items-end">
						<div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-2 items-start">
						<div><span class="px-4 py-2 rounded-lg inline-block bg-gray-300 text-gray-600">I get the same error on Arch Linux (also with sudo)</span></div>
						<div><span class="px-4 py-2 rounded-lg inline-block bg-gray-300 text-gray-600">I also have this issue, Here is what I was doing until now: #1076</span></div>
						<div><span class="px-4 py-2 rounded-lg inline-block rounded-bl-none bg-gray-300 text-gray-600">even i am facing</span></div>
						</div>
						<img src="https://images.unsplash.com/photo-1549078642-b2ba4bda0cdb?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=3&amp;w=144&amp;h=144" alt="My profile" class="w-6 h-6 rounded-full order-1">
					</div>
				</div>
			</div>
			<div class="border-t-2 border-gray-200 px-4 pt-4 mb-2 sm:mb-0">
				<div class="relative flex">
					<span class="absolute inset-y-0 flex items-center">
						<button type="button" class="inline-flex items-center justify-center rounded-full h-12 w-12 transition duration-500 ease-in-out text-gray-500 hover:bg-gray-300 focus:outline-none">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6 text-gray-600">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
						</svg>
						</button>
					</span>
					<input type="text" placeholder="Write Something" class="w-full focus:outline-none focus:placeholder-gray-400 text-gray-600 placeholder-gray-600 pl-12 bg-gray-200 rounded-full py-3">
					<div class="absolute right-0 items-center inset-y-0 hidden sm:flex">
						<button type="button" class="inline-flex items-center justify-center rounded-full h-10 w-10 transition duration-500 ease-in-out text-gray-500 hover:bg-gray-300 focus:outline-none">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6 text-gray-600">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
						</svg>
						</button>
						<button type="button" class="inline-flex items-center justify-center rounded-full h-10 w-10 transition duration-500 ease-in-out text-gray-500 hover:bg-gray-300 focus:outline-none">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6 text-gray-600">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
						</svg>
						</button>
						<button type="button" class="inline-flex items-center justify-center rounded-full h-10 w-10 transition duration-500 ease-in-out text-gray-500 hover:bg-gray-300 focus:outline-none">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6 text-gray-600">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
						</svg>
						</button>
						<button type="button" class="inline-flex items-center justify-center rounded-full h-12 w-12 transition duration-500 ease-in-out text-white bg-blue-500 hover:bg-blue-400 focus:outline-none">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-6 w-6 transform rotate-90">
							<path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
						</svg>
						</button>
					</div>
				</div>
			</div>
			</div>
			</div>

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

			<script>
				const el = document.getElementById('messages')
				el.scrollTop = el.scrollHeight
			</script>

		</body>
	</html>