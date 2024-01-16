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
</head>

<body class="bg-white work-sans leading-normal text-base tracking-normal">
	<nav id="header" class="sticky bg-white top-0 w-full z-30 top-0 py-1">
        <div class="w-full container mx-auto flex flex-wrap items-center justify-between mt-0 px-6 py-3">

            <label for="menu-toggle" class="cursor-pointer sm:hidden block">
                <svg class="fill-current text-gray-900" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                    <title>menu</title>
                    <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"></path>
                </svg>
            </label>
            <input class="hidden" type="checkbox" id="menu-toggle" />

            <a class="hidden sm:block flex items-center tracking-wide no-underline hover:no-underline font-bold text-black text-2xl" href="index.php">
                Ella Froze
            </a>

            <div class="shadow flex sm:w-2/4">
                <input class="w-full rounded p-2 sm:w-full" type="text" placeholder="Cari item...">
                <button class="bg-white w-auto flex justify-end items-center text-blue-500 p-2 hover:text-blue-400">
                    <svg class="text-gray-600 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 56.966 56.966" style="enable-background:new 0 0 56.966 56.966;" xml:space="preserve" width="512px" height="512px">
                        <path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  s-17-7.626-17-17S14.61,6,23.984,6z"/>
                    </svg>
                </button>
            </div>

            <div class="order-2 md:order-3 flex items-center" id="nav-content">

                <a class="pl-5 inline-block no-underline hover:text-blue-900 font-semibold hidden sm:block" href="Login.php"
                >Masuk</a>

                <a class="pl-6 inline-block no-underline hover:text-black hidden sm:block" href="cart_page.php" onclick="openDropdown(event,'dropdown-id-1')">
                    <svg class="fill-current hover:text-blue-800" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M21,7H7.462L5.91,3.586C5.748,3.229,5.392,3,5,3H2v2h2.356L9.09,15.414C9.252,15.771,9.608,16,10,16h8 c0.4,0,0.762-0.238,0.919-0.606l3-7c0.133-0.309,0.101-0.663-0.084-0.944C21.649,7.169,21.336,7,21,7z M17.341,14h-6.697L8.371,9 h11.112L17.341,14z" />
                        <circle cx="10.5" cy="18.5" r="1.5" />
                        <circle cx="17.5" cy="18.5" r="1.5" />
                    </svg>

                    <div class=" hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" id="dropdown-id-1">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Item 1</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Item 2</a>
                        <a href="user_profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Item 3</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-3">Item 4</a>
                    </div>
                </a>

                <button type="button" class="rounded-full focus:outline-none p-3" id="user-menu-button" aria-expanded="false" aria-haspopup="true" onclick="openDropdown(event,'dropdown-id-2')" >
                    <span class="sr-only">Open user menu</span>
                    <svg class="fill-current hover:text-blue-900" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <circle fill="none" cx="12" cy="7" r="3" />
                        <path d="M12 2C9.243 2 7 4.243 7 7s2.243 5 5 5 5-2.243 5-5S14.757 2 12 2zM12 10c-1.654 0-3-1.346-3-3s1.346-3 3-3 3 1.346 3 3S13.654 10 12 10zM21 21v-1c0-3.859-3.141-7-7-7h-4c-3.86 0-7 3.141-7 7v1h2v-1c0-2.757 2.243-5 5-5h4c2.757 0 5 2.243 5 5v1H21z" />
                    </svg>
                </button>

                <div class=" hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" id="dropdown-id-2">
                    <a href="halaman_pembelian.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Pembelian</a>
                    <a href="halaman_wishlist.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Wishlist</a>
                    <a href="user_profile.php" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-3">Sign out</a>
                </div>
            </div>
        </div>
    </nav>

	<div class="lg:flex justify-center xl:max-w-2/4-lg pt-10">
		<!--- Halaman Login --->
		<div id="test1" style="">
			<div class="py-8 lg:bg-white flex justify-center lg:justify-start lg:px-60 text-red-700 tracking-wide ml-4 font-bold text-2xl flex items-center">
				Masuk Disini
			</div>

			<div class="mt-10 px-12 sm:px-24 md:px-48 lg:px-12 lg:mt-16 xl:px-24 xl:max-w-2xl">
				<div class="mt-4">
					<form>
						<div class="text-sm font-bold text-gray-700 tracking-wide">
							Alamat Email
						</div>
						<input class="w-full text-lg py-2 border-b border-gray-300 focus:outline-none focus:border-red-500" type="" placeholder="Masukan Alamat Email">
						
						<div class="mt-8 flex justify-between items-center">
							<div class="text-sm font-bold text-gray-700 tracking-wide">
								Kata Sandi
							</div>
							<a class="text-xs font-display font-semibold text-red-600 hover:text-indigo-800 cursor-pointer" onClick="testRegist(3)">Lupa Kata Sandi?</a>
						</div>
						<input class="w-full text-lg py-2 border-b border-gray-300 focus:outline-none focus:border-red-500" type="" placeholder="Masukan Kata Sandi">
						<button class="mt-10 bg-red-500 text-gray-100 p-4 w-full rounded-lg tracking-wide font-semibold font-display focus:outline-none focus:shadow-outline hover:bg-red-600 shadow-lg">
							Masuk
						</button>
					</form>
					<div class="mt-12 text-sm font-display font-semibold text-gray-700 text-center">
						Belum punya akun ? <a class="cursor-pointer text-red-600 hover:text-indigo-800" onClick="testRegist(2)">Daftar disini</a>
					</div>
					<button class="mt-10 bg-red-600 text-gray-100 p-4 w-full rounded-lg tracking-wide font-semibold font-display focus:outline-none focus:shadow-outline hover:bg-red-700 shadow-lg">
						Google	
					</button>
					<button class="mt-4 bg-indigo-600 text-gray-100 p-4 w-full rounded-lg tracking-wide font-semibold font-display focus:outline-none focus:shadow-outline hover:bg-indigo-800 shadow-lg">
						Facebook
					</button>
				</div>
			</div>
		</div>

		<!--- Halaman Daftar --->
		<div id="test2" style="display:none">
			<div class="py-14 lg:bg-white flex justify-center lg:justify-start lg:px-60 text-indigo-900 tracking-wide ml-4 font-bold lg:text-4xl flex items-center">
				Daftar Disini
			</div>

			<div class="mt-10 px-12 sm:px-24 md:px-48 lg:px-12 lg:mt-16 xl:px-24 xl:max-w-2xl mt-12">
				<form>
					<div class="text-sm font-bold text-gray-700 tracking-wide">
						Alamat Email
					</div>
					<input class="w-full text-lg py-2 border-b border-gray-300 focus:outline-none focus:border-indigo-500" type="" placeholder="Masukan Alamat Email">
					
					<div class="mt-8 text-sm font-bold text-gray-700 tracking-wide">
						No. Ponsel
					</div>
					<input class="w-full text-lg py-2 border-b border-gray-300 focus:outline-none focus:border-indigo-500" type="" placeholder="Masukan Nomor Ponsel">
					
					<div class="mt-8 text-sm font-bold text-gray-700 tracking-wide">
						Kata Sandi
					</div>
					<input class="w-full text-lg py-2 border-b border-gray-300 focus:outline-none focus:border-indigo-500" type="" placeholder="Masukkan Kata Sandi">

					<button class="mt-10 bg-indigo-500 text-gray-100 p-4 w-full rounded-full tracking-wide font-semibold font-display focus:outline-none focus:shadow-outline hover:bg-indigo-600 shadow-lg">
						Daftar
					</button>
				</form>
				<div class="mt-12 text-sm font-display font-semibold text-gray-700 text-center">
					Sudah punya akun ? <a class="cursor-pointer text-indigo-600 hover:text-indigo-800" onClick="testRegist(1)">Masuk disini</a>
				</div>
			</div>
		</div>

		<!--- Halaman Lupa Password -->
		<div id="test3" style="display:none">
			<div class="py-14 lg:bg-white flex justify-center lg:justify-start lg:px-60 text-indigo-900 tracking-wide ml-4 font-bold lg:text-4xl flex items-center">
				Lupa Password
			</div>

			<div class="mt-10 px-12 sm:px-24 md:px-48 lg:px-12 lg:mt-16 xl:px-24 xl:max-w-2xl mt-12">
				<form>
					<div class="text-sm font-bold text-gray-700 tracking-wide">
						Alamat Email
					</div>
					<input class="w-full text-lg py-2 border-b border-gray-300 focus:outline-none focus:border-indigo-500" type="" placeholder="Masukan Alamat Email">

					<button class="mt-10 bg-indigo-500 text-gray-100 p-4 w-full rounded-full tracking-wide font-semibold font-display focus:outline-none focus:shadow-outline hover:bg-indigo-600 shadow-lg">
						Kirim
					</button>
				</form>
				<div class="mt-12 text-sm font-display font-semibold text-gray-700 text-center">
					Sudah punya akun ? <a class="cursor-pointer text-indigo-600 hover:text-indigo-800" onClick="testRegist(1)">Masuk disini</a>
				</div>
			</div>
		</div>
	</div>

	

	<script src="https://unpkg.com/@popperjs/core@2.9.1/dist/umd/popper.min.js" charset="utf-8"></script>
	<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.1.min.js"></script>
	<script>
		function testRegist(flag) {
			if(flag==2) {
				document.getElementById("test3").style.display = 'none';
				document.getElementById("test2").style.display = '';
				document.getElementById("test1").style.display = 'none';
			} else if(flag==3) {
				document.getElementById("test3").style.display = '';
				document.getElementById("test2").style.display = 'none';
				document.getElementById("test1").style.display = 'none';
			} else {
				document.getElementById("test3").style.display = 'none';
				document.getElementById("test2").style.display = 'none';
				document.getElementById("test1").style.display = '';
			}
		}

		function openDropdown(event,dropdownID){
			let element = event.target;
			while(element.nodeName !== "BUTTON") {
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

</body>
</html>