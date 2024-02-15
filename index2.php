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
            .carousel-open:checked + .carousel-item {
                position: static;
                opacity: 100;
            }
            .carousel-item {
                -webkit-transition: opacity 0.6s ease-out;
                transition: opacity 0.6s ease-out;
            }
            #carousel-1:checked ~ .control-1,
            #carousel-2:checked ~ .control-2,
            #carousel-3:checked ~ .control-3 {
                display: block;
            }
            .carousel-indicators {
                list-style: none;
                margin: 0;
                padding: 0;
                position: absolute;
                bottom: 2%;
                left: 0;
                right: 0;
                text-align: center;
                z-index: 10;
            }
            #carousel-1:checked ~ .control-1 ~ .carousel-indicators li:nth-child(1) .carousel-bullet,
            #carousel-2:checked ~ .control-2 ~ .carousel-indicators li:nth-child(2) .carousel-bullet,
            #carousel-3:checked ~ .control-3 ~ .carousel-indicators li:nth-child(3) .carousel-bullet {
                color: #000; /*Set to match the Tailwind colour you want the active one to be */
            }
            /* Ini carousel item untuk diskon */
            #carouselitem-1:checked ~ .control-1,
            #carouselitem-2:checked ~ .control-2,
            #carouselitem-3:checked ~ .control-3 {
                display: block;
            }
            #carouselitem-1:checked ~ .control-1 ~ .carousel-indicators li:nth-child(1) .carousel-bullet,
            #carouselitem-2:checked ~ .control-2 ~ .carousel-indicators li:nth-child(2) .carousel-bullet,
            #carouselitem-3:checked ~ .control-3 ~ .carousel-indicators li:nth-child(3) .carousel-bullet {
                color: #000;
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
        
        <div class="bg-gray-50 w-full pb-4">
            <div class="carousel relative container mx-auto pt-2" style="max-width:1600px;">
                <div class="carousel-inner relative overflow-hidden w-full">
                    <!--Slide 1-->
                    <input class="carousel-open" type="radio" id="carousel-1" name="carousel" aria-hidden="true" hidden="" checked="checked">
                    <div class="carousel-item absolute opacity-0" style="height:50vh;">
                        <div class="block h-full w-full mx-auto flex pt-6 md:pt-0 md:items-center bg-cover bg-right" style="background-image: url('https://images.unsplash.com/photo-1573999388457-ff750cc4a875?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');">
        
                            <div class="container mx-auto">
                                <div class="flex flex-col w-full lg:w-1/2 md:ml-16 items-center md:items-start px-6 tracking-wide">
                                    <p class="text-white text-2xl my-4">Stripy Zig Zag Jigsaw Pillow and Duvet Set</p>
                                    <a class="text-xl inline-block no-underline border-b border-white leading-relaxed  text-white hover:text-black hover:border-black" href="product.php">view product</a>
                                </div>
                            </div>
        
                        </div>
                    </div>
                    <label for="carousel-3" class="prev control-1 w-10 h-10 ml-2 md:ml-10 absolute cursor-pointer hidden text-3xl font-bold text-black hover:text-white rounded-full bg-white hover:bg-gray-900 leading-tight text-center z-10 inset-y-0 left-0 md:my- my-auto">‹</label>
                    <label for="carousel-2" class="next control-1 w-10 h-10 mr-2 md:mr-10 absolute cursor-pointer hidden text-3xl font-bold text-black hover:text-white rounded-full bg-white hover:bg-gray-900 leading-tight text-center z-10 inset-y-0 right-0 my-auto">›</label>
        
                    <!--Slide 2-->
                    <input class="carousel-open" type="radio" id="carousel-2" name="carousel" aria-hidden="true" hidden="">
                    <div class="carousel-item absolute opacity-0 bg-cover bg-right" style="height:50vh;">
                        <div class="block h-full w-full mx-auto flex pt-6 md:pt-0 md:items-center bg-cover bg-right" style="background-image: url('https://images.unsplash.com/photo-1476224203421-9ac39bcb3327?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTJ8fGZvb2R8ZW58MHx8MHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60');">
        
                            <div class="container mx-auto">
                                <div class="flex flex-col w-full lg:w-1/2 md:ml-16 items-center md:items-start px-6 tracking-wide">
                                    <p class="text-white text-2xl my-4">Real Bamboo Wall Clock</p>
                                    <a class="text-xl inline-block no-underline border-b border-white leading-relaxed text-white hover:text-white hover:border-black" href="product.php">view product</a>
                                </div>
                            </div>
        
                        </div>
                    </div>
                    <label for="carousel-1" class="prev control-2 w-10 h-10 ml-2 md:ml-10 absolute cursor-pointer hidden text-3xl font-bold text-black hover:text-white rounded-full bg-white hover:bg-gray-900  leading-tight text-center z-10 inset-y-0 left-0 my-auto">‹</label>
                    <label for="carousel-3" class="next control-2 w-10 h-10 mr-2 md:mr-10 absolute cursor-pointer hidden text-3xl font-bold text-black hover:text-white rounded-full bg-white hover:bg-gray-900  leading-tight text-center z-10 inset-y-0 right-0 my-auto">›</label>
        
                    <!--Slide 3-->
                    <input class="carousel-open" type="radio" id="carousel-3" name="carousel" aria-hidden="true" hidden="">
                    <div class="carousel-item absolute opacity-0" style="height:50vh;">
                        <div class="block h-full w-full mx-auto flex pt-6 md:pt-0 md:items-center bg-cover bg-bottom" style="background-image: url('https://images.unsplash.com/photo-1594397107714-d610739647fa?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');">
        
                            <div class="container mx-auto">
                                <div class="flex flex-col w-full lg:w-1/2 md:ml-16 items-center md:items-start px-6 tracking-wide">
                                    <p class="text-black text-2xl my-4">Brown and blue hardbound book</p>
                                    <a class="text-xl inline-block no-underline border-b border-gray-600 leading-relaxed hover:text-black hover:border-black" href="product.php">view product</a>
                                </div>
                            </div>
        
                        </div>
                    </div>
                    <label for="carousel-2" class="prev control-3 w-10 h-10 ml-2 md:ml-10 absolute cursor-pointer hidden text-3xl font-bold text-black hover:text-white rounded-full bg-white hover:bg-gray-900  leading-tight text-center z-10 inset-y-0 left-0 my-auto">‹</label>
                    <label for="carousel-1" class="next control-3 w-10 h-10 mr-2 md:mr-10 absolute cursor-pointer hidden text-3xl font-bold text-black hover:text-white rounded-full bg-white hover:bg-gray-900  leading-tight text-center z-10 inset-y-0 right-0 my-auto">›</label>
        
                    <!-- Add additional indicators for each slide-->
                    <ol class="carousel-indicators">
                        <li class="inline-block mr-3">
                            <label for="carousel-1" class="carousel-bullet cursor-pointer block text-4xl text-gray-400 hover:text-gray-900">•</label>
                        </li>
                        <li class="inline-block mr-3">
                            <label for="carousel-2" class="carousel-bullet cursor-pointer block text-4xl text-gray-400 hover:text-gray-900">•</label>
                        </li>
                        <li class="inline-block mr-3">
                            <label for="carousel-3" class="carousel-bullet cursor-pointer block text-4xl text-gray-400 hover:text-gray-900">•</label>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        
        <section class="bg-gray-50 py-8">
            <div class="container mx-auto flex items-center flex-wrap pt-4">
                <nav id="store" class="w-full top-0 px-6 py-1">
                    <div class="w-full container mx-auto flex flex-wrap items-center justify-between mt-0 px-2 py-3">

                        <a class="font-bold text-red-600 text-2xl" href="#">
                            Diskon Menarik
                        </a>

                    </div>
                </nav>
            </div>
            
            <div class="carousel relative container mx-auto">
                <div class="carousel-inner relative overflow-hidden w-full">
                    <!--Slide 1-->
                    <input class="carousel-open" type="radio" id="carouselitem-1" name="carouselitem" aria-hidden="true" hidden="" checked="checked">
                    <div class="carousel-item absolute opacity-0">

                        <div class="container mx-auto flex items-center flex-wrap">
                            <div class="w-full sm:w-1/2 sm:w-1/3 md:w-1/3 xl:w-1/4 p-6 flex flex-col bg-white">
                                <a href="product.php">
                                    <img class="w-full h-80 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1616431575978-ad28681d658e?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80">
                                    <div class="pt-3 flex items-center justify-between">
                                        <p class="">Kepala Kakap Merah 1</p>
                                    </div>

                                    <p class="pt-1 text-gray-900 font-semibold">IDR 40.000</p>

                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>

                                        <span class="inline-block px-2 text-sm font-semibold text-gray-600 mr-2 mb-2 pt-2">
                                            Bogor
                                        </span>
                                    </div>

                                    <div class="pt-1 flex items-center">
                                        <svg class="w-4 h-4 fill-current text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <p class="pl-1 text-sm text-gray-500">4.7 | Terjual 1 rb</p>
                                    </div>
                                </a>
                            </div>

                            <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-6 flex flex-col bg-white">
                                <a href="product.php">
                                    <img class="w-full h-80 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1611506168759-1e69a83b5a53?ixid=MnwxMjA3fDB8MHxzZWFyY2h8NjZ8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                                    <div class="pt-3 flex items-center justify-between">
                                        <p class="">Marlin Fillet 1 500g</p>
                                    </div>

                                    <p class="pt-1 text-gray-900 font-semibold">IDR 60.000</p>

                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>

                                        <span class="inline-block px-2 text-sm font-semibold text-gray-600 mr-2 mb-2 pt-2">
                                            Jakarta
                                        </span>
                                    </div>

                                    <div class="pt-1 flex items-center">
                                        <svg class="w-4 h-4 fill-current text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <p class="pl-1 text-sm text-gray-500">5 | Terjual 1 rb</p>
                                    </div>
                                </a>
                            </div>

                            <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-6 flex flex-col bg-white">
                                <a href="product.php">
                                    <img class="w-full h-80 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1615653633266-1ee0c84500b2?ixid=MnwxMjA3fDB8MHxzZWFyY2h8NzN8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                                    <div class="pt-3 flex items-center justify-between">
                                        <p class="">Kakap Hitam Fillet 1 500g</p>
                                    </div>

                                    <p class="pt-1 text-gray-900 font-semibold">IDR 80.000</p>

                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>

                                        <span class="inline-block px-2 text-sm font-semibold text-gray-600 mr-2 mb-2 pt-2">
                                            Bandung
                                        </span>
                                    </div>

                                    <div class="pt-1 flex items-center">
                                        <svg class="w-4 h-4 fill-current text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <p class="pl-1 text-sm text-gray-500">4.8 | Terjual 1 rb</p>
                                    </div>
                                </a>
                            </div>

                            <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-6 flex flex-col bg-white">
                                <a href="product.php">
                                    <img class="w-full h-80 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1614277786539-abd7a3cd46bf?ixid=MnwxMjA3fDB8MHxzZWFyY2h8ODR8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                                    <div class="pt-3 flex items-center justify-between">
                                        <p class="">Kakap Merah Fillet 1 500g</p>
                                    </div>

                                    <p class="pt-1 text-gray-900 font-semibold">IDR 90.000</p>

                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>

                                        <span class="inline-block px-2 text-sm font-semibold text-gray-600 mr-2 mb-2 pt-2">
                                            Tangerang
                                        </span>
                                    </div>

                                    <div class="pt-1 flex items-center">
                                        <svg class="w-4 h-4 fill-current text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <p class="pl-1 text-sm text-gray-500">4.7 | Terjual 1 rb</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>
                    <label for="carouselitem-3" class="prev control-1 w-10 h-10 absolute cursor-pointer hidden text-3xl font-bold text-white hover:text-black rounded-full bg-black hover:bg-white leading-tight text-center z-10 inset-y-0 left-0 my-auto">‹</label>
                    <label for="carouselitem-2" class="next control-1 w-10 h-10 absolute cursor-pointer hidden text-3xl font-bold text-white hover:text-black rounded-full bg-black hover:bg-white leading-tight text-center z-10 inset-y-0 right-0 my-auto">›</label>

                    <!--Slide 2-->
                    <input class="carousel-open" type="radio" id="carouselitem-2" name="carouselitem" aria-hidden="true" hidden="">
                    <div class="carousel-item absolute opacity-0">

                        <div class="container mx-auto flex items-center flex-wrap">
                            <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-6 flex flex-col bg-white">
                                <a href="product.php">
                                    <img class="w-full h-80 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1598515211932-b130a728a769?ixid=MnwxMjA3fDB8MHxzZWFyY2h8ODF8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                                    <div class="pt-3 flex items-center justify-between">
                                        <p class="">Bakso Tenggiri 500g</p>
                                    </div>
                
                                    <p class="pt-1 text-gray-900 font-semibold">IDR 60.000</p>
                
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                
                                        <span class="inline-block px-2 text-sm font-semibold text-gray-600 mr-2 mb-2 pt-2">
                                            Tangerang
                                        </span>
                                    </div>
                
                                    <div class="pt-1 flex items-center">
                                        <svg class="w-4 h-4 fill-current text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <p class="pl-1 text-sm text-gray-500">4.7 | Terjual 1 rb</p>
                                    </div>
                                </a>
                            </div>
                
                            <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-6 flex flex-col bg-white">
                                <a href="product.php">
                                    <img class="w-full h-80 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1556471013-0001958d2f12?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTE3fHxmb29kJTIwYmFja2dyb3VuZHxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                                    <div class="pt-3 flex items-center justify-between">
                                        <p class="">Bakso Tenggiri 250g</p>
                                    </div>
                
                                    <p class="pt-1 text-gray-900 font-semibold">IDR 40.000</p>
                
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                
                                        <span class="inline-block px-2 text-sm font-semibold text-gray-600 mr-2 mb-2 pt-2">
                                            Tangerang
                                        </span>
                                    </div>
                
                                    <div class="pt-1 flex items-center">
                                        <svg class="w-4 h-4 fill-current text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <p class="pl-1 text-sm text-gray-500">4.7 | Terjual 1 rb</p>
                                    </div>
                                </a>
                            </div>
                
                            <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-6 flex flex-col bg-white">
                                <a href="product.php">
                                    <img class="w-full h-80 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1611506168454-656b2c960868?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTI0fHxmb29kJTIwYmFja2dyb3VuZHxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                                    <div class="pt-3 flex items-center justify-between">
                                        <p class="">Cumi-cumi 1kg</p>
                                    </div>
                
                                    <p class="pt-1 text-gray-900 font-semibold">IDR 95.000</p>
                
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                
                                        <span class="inline-block px-2 text-sm font-semibold text-gray-600 mr-2 mb-2 pt-2">
                                            Tangerang
                                        </span>
                                    </div>
                
                                    <div class="pt-1 flex items-center">
                                        <svg class="w-4 h-4 fill-current text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <p class="pl-1 text-sm text-gray-500">4.7 | Terjual 1 rb</p>
                                    </div>
                                </a>
                            </div>
                
                            <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-6 flex flex-col bg-white">
                                <a href="product.php">
                                    <img class="w-full h-80 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1619714604882-db1396d4a718?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTQ2fHxmb29kJTIwYmFja2dyb3VuZHxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                                    <div class="pt-3 flex items-center justify-between">
                                        <p class="">Cumi-cumi 500g</p>
                                    </div>
                
                                    <p class="pt-1 text-gray-900 font-semibold">IDR 50.000</p>
                
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                
                                        <span class="inline-block px-2 text-sm font-semibold text-gray-600 mr-2 mb-2 pt-2">
                                            Tangerang
                                        </span>
                                    </div>
                
                                    <div class="pt-1 flex items-center">
                                        <svg class="w-4 h-4 fill-current text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <p class="pl-1 text-sm text-gray-500">4.7 | Terjual 1 rb</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>
                    <label for="carouselitem-1" class="prev control-2 w-10 h-10 absolute cursor-pointer hidden text-3xl font-bold text-white hover:text-black rounded-full bg-black hover:bg-white leading-tight text-center z-10 inset-y-0 left-0 my-auto">‹</label>
                    <label for="carouselitem-3" class="next control-2 w-10 h-10 absolute cursor-pointer hidden text-3xl font-bold text-white hover:text-black rounded-full bg-black hover:bg-white leading-tight text-center z-10 inset-y-0 right-0 my-auto">›</label>

                    <!--Slide 3-->
                    <input class="carousel-open" type="radio" id="carouselitem-3" name="carouselitem" aria-hidden="true" hidden="">
                    <div class="carousel-item absolute opacity-0">

                        <div class="container mx-auto flex items-center flex-wrap">
                            <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-6 flex flex-col bg-white">
                                <a href="product.php">
                                    <img class="w-full h-80 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1616431575978-ad28681d658e?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80">
                                    <div class="pt-3 flex items-center justify-between">
                                        <p class="">Kepala Kakap Merah 2</p>
                                    </div>

                                    <p class="pt-1 text-gray-900 font-semibold">IDR 40.000</p>

                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>

                                        <span class="inline-block px-2 text-sm font-semibold text-gray-600 mr-2 mb-2 pt-2">
                                            Bogor
                                        </span>
                                    </div>

                                    <div class="pt-1 flex items-center">
                                        <svg class="w-4 h-4 fill-current text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <p class="pl-1 text-sm text-gray-500">4.7 | Terjual 1 rb</p>
                                    </div>
                                </a>
                            </div>

                            <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-6 flex flex-col bg-white">
                                <a href="product.php">
                                    <img class="w-full h-80 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1611506168759-1e69a83b5a53?ixid=MnwxMjA3fDB8MHxzZWFyY2h8NjZ8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                                    <div class="pt-3 flex items-center justify-between">
                                        <p class="">Marlin Fillet 2 500g</p>
                                    </div>

                                    <p class="pt-1 text-gray-900 font-semibold">IDR 60.000</p>

                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>

                                        <span class="inline-block px-2 text-sm font-semibold text-gray-600 mr-2 mb-2 pt-2">
                                            Jakarta
                                        </span>
                                    </div>

                                    <div class="pt-1 flex items-center">
                                        <svg class="w-4 h-4 fill-current text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <p class="pl-1 text-sm text-gray-500">5 | Terjual 1 rb</p>
                                    </div>
                                </a>
                            </div>

                            <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-6 flex flex-col bg-white">
                                <a href="product.php">
                                    <img class="w-full h-80 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1615653633266-1ee0c84500b2?ixid=MnwxMjA3fDB8MHxzZWFyY2h8NzN8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                                    <div class="pt-3 flex items-center justify-between">
                                        <p class="">Kakap Hitam Fillet 2 500g</p>
                                    </div>

                                    <p class="pt-1 text-gray-900 font-semibold">IDR 80.000</p>

                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>

                                        <span class="inline-block px-2 text-sm font-semibold text-gray-600 mr-2 mb-2 pt-2">
                                            Bandung
                                        </span>
                                    </div>

                                    <div class="pt-1 flex items-center">
                                        <svg class="w-4 h-4 fill-current text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <p class="pl-1 text-sm text-gray-500">4.8 | Terjual 1 rb</p>
                                    </div>
                                </a>
                            </div>

                            <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-6 flex flex-col bg-white">
                                <a href="product.php">
                                    <img class="w-full h-80 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1614277786539-abd7a3cd46bf?ixid=MnwxMjA3fDB8MHxzZWFyY2h8ODR8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                                    <div class="pt-3 flex items-center justify-between">
                                        <p class="">Kakap Merah Fillet 2 500g</p>
                                    </div>

                                    <p class="pt-1 text-gray-900 font-semibold">IDR 90.000</p>

                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>

                                        <span class="inline-block px-2 text-sm font-semibold text-gray-600 mr-2 mb-2 pt-2">
                                            Tangerang
                                        </span>
                                    </div>

                                    <div class="pt-1 flex items-center">
                                        <svg class="w-4 h-4 fill-current text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        <p class="pl-1 text-sm text-gray-500">4.7 | Terjual 1 rb</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>
                    <label for="carouselitem-2" class="prev control-3 w-10 h-10 absolute cursor-pointer hidden text-3xl font-bold text-white hover:text-black rounded-full bg-black hover:bg-white leading-tight text-center z-10 inset-y-0 left-0 my-auto">‹</label>
                    <label for="carouselitem-1" class="next control-3 w-10 h-10 absolute cursor-pointer hidden text-3xl font-bold text-white hover:text-black rounded-full bg-black hover:bg-white leading-tight text-center z-10 inset-y-0 right-0 my-auto">›</label>

                    <ol class="carousel-indicators md:hidden block">
                        <li class="inline-block mr-3">
                            <label for="carouselitem-1" class="carousel-bullet cursor-pointer block text-4xl text-gray-400 hover:text-gray-900">•</label>
                        </li>
                        <li class="inline-block mr-3">
                            <label for="carouselitem-2" class="carousel-bullet cursor-pointer block text-4xl text-gray-400 hover:text-gray-900">•</label>
                        </li>
                        <li class="inline-block mr-3">
                            <label for="carouselitem-3" class="carousel-bullet cursor-pointer block text-4xl text-gray-400 hover:text-gray-900">•</label>
                        </li>
                    </ol>
                </div>
            </div>
        </section>

        <section class="bg-gray-50">
            <div class="container mx-auto flex items-center flex-wrap pb-12">

                <div class="container mx-auto flex items-center flex-wrap pt-4">
                    <nav id="store" class="w-full top-0 px-6 py-1">
                        <div class="w-full container mx-auto flex md-flex-wrap items-center justify-between mt-0 px-2 py-3">
        
                            <a class="font-bold text-red-600 text-2xl " href="#">
                                Kategori
                            </a>
        
                        </div>
                    </nav>
                </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/5 p-6 flex flex-col bg-white">
                    <a href="category.php">
                        <img class="w-full h-56 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1616431575978-ad28681d658e?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80">
                        <p class="pt-1 text-gray-900 font-semibold text-center">Ikan Air Tawar</p>
                    </a>
                </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/5 p-6 flex flex-col bg-white">
                    <a href="category.php">
                        <img class="w-full h-56 over:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1611506168759-1e69a83b5a53?ixid=MnwxMjA3fDB8MHxzZWFyY2h8NjZ8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <p class="pt-1 text-gray-900 font-semibold text-center">Ikan Air Payau</p>
                    </a>
                </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/5 p-6 flex flex-col bg-white">
                    <a href="category.php">
                        <img class="w-full h-56 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1615653633266-1ee0c84500b2?ixid=MnwxMjA3fDB8MHxzZWFyY2h8NzN8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <p class="pt-1 text-gray-900 font-semibold text-center">Ikan Air Laut</p>
                    </a>
                </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/5 p-6 flex flex-col bg-white">
                    <a href="category.php">
                        <img class="w-full h-56 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1614277786539-abd7a3cd46bf?ixid=MnwxMjA3fDB8MHxzZWFyY2h8ODR8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <p class="pt-1 text-gray-900 font-semibold text-center">Bakso</p>
                    </a>
                </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/5 p-6 flex flex-col bg-white">
                    <a href="category.php">
                        <img class="w-full h-56 hover:grow hover:shadow-lg rounded-2xl" src="https://images.unsplash.com/photo-1598515211932-b130a728a769?ixid=MnwxMjA3fDB8MHxzZWFyY2h8ODF8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <p class="pt-1 text-gray-900 font-semibold text-center">Cumi</p>
                    </a>
                </div>
            </div>
        </section>

        <section class="bg-white">
            <div class="container mx-auto flex items-center flex-wrap pb-12">
                <div class="container mx-auto flex items-center flex-wrap pb-12">
                    <div class="container mx-auto flex items-center flex-wrap pt-4">
                        <nav id="store" class="w-full top-0 px-6 py-1">
                            <div class="w-full container mx-auto flex md-flex-wrap items-center justify-between mt-0 px-2 py-3">
            
                                <a class="font-bold text-red-600 text-2xl" href="#">
                                    Rekomendasi Untukmu
                                </a>
                            </div>
                        </nav>
                    </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-8 flex flex-col bg-white">
                    <a href="product.php">
                        <img class="w-full h-80 hover:grow hover:shadow-lg rounded-lg" src="https://images.unsplash.com/photo-1616431575978-ad28681d658e?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80">
                        <div class="pt-3 flex items-center justify-between ml-2">
                            <p class="">Kepala Kakap Merah</p>
                        </div>
                        <s class="pt-1 text-gray-900 font-semibold ml-2">IDR 60.000</s>
                        <p class="pt-1 text-gray-900 font-semibold text-lg ml-2">IDR 40.000</p>
                    </a>
                </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-8 flex flex-col bg-white">
                    <a href="product.php">
                        <img class="w-full h-80 hover:grow hover:shadow-lg rounded-lg" src="https://images.unsplash.com/photo-1611506168759-1e69a83b5a53?ixid=MnwxMjA3fDB8MHxzZWFyY2h8NjZ8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <div class="pt-3 flex items-center justify-between ml-2">
                            <p class="">Marlin Fillet 500g</p>
                        </div>

                        <s class="pt-1 text-gray-900 font-semibold ml-2">IDR 60.000</s>
                        <p class="pt-1 text-gray-900 font-semibold text-lg ml-2">IDR 40.000</p>
                    </a>
                </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-8 flex flex-col bg-white">
                    <a href="product.php">
                        <img class="w-full h-80 hover:grow hover:shadow-lg rounded-lg" src="https://images.unsplash.com/photo-1615653633266-1ee0c84500b2?ixid=MnwxMjA3fDB8MHxzZWFyY2h8NzN8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <div class="pt-3 flex items-center justify-between ml-2">
                            <p class="">Kakap Hitam Fillet 500g</p>
                        </div>
                        <s class="pt-1 text-gray-900 font-semibold ml-2">IDR 90.000</s>
                        <p class="pt-1 text-gray-900 font-semibold text-lg ml-2">IDR 80.000</p>
                    </a>
                </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-8 flex flex-col bg-white">
                    <a href="product.php">
                        <img class="w-full h-80 hover:grow hover:shadow-lg rounded-lg" src="https://images.unsplash.com/photo-1614277786539-abd7a3cd46bf?ixid=MnwxMjA3fDB8MHxzZWFyY2h8ODR8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <div class="pt-3 flex items-center justify-between ml-2">
                            <p class="">Kakap Merah Fillet 500g</p>
                        </div>

                        <s class="pt-1 text-gray-900 font-semibold ml-2">IDR 90.000</s>
                        <p class="pt-1 text-gray-900 font-semibold text-lg ml-2">IDR 80.000</p>
                    </a>
                </div>
                <!--End produk 1-4 -->
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-8 flex flex-col bg-white">
                    <a href="product.php">
                        <img class="w-full h-80 hover:grow hover:shadow-lg rounded-lg" src="https://images.unsplash.com/photo-1598515211932-b130a728a769?ixid=MnwxMjA3fDB8MHxzZWFyY2h8ODF8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <div class="pt-3 flex items-center justify-between ml-2">
                        <p class="">Bakso Tenggiri 500g</p>
                        </div>

                        <s class="pt-1 text-gray-900 font-semibold ml-2">IDR 90.000</s>
                        <p class="pt-1 text-gray-900 font-semibold text-lg ml-2">IDR 80.000</p>
                    </a>
                </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-8 flex flex-col bg-white">
                    <a href="product.php">
                        <img class="w-full h-80 hover:grow hover:shadow-lg rounded-lg" src="https://images.unsplash.com/photo-1556471013-0001958d2f12?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTE3fHxmb29kJTIwYmFja2dyb3VuZHxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <div class="pt-3 flex items-center justify-between ml-2">
                            <p class="">Bakso Tenggiri 250g</p>
                        </div>

                        <s class="pt-1 text-gray-900 font-semibold ml-2">IDR 90.000</s>
                        <p class="pt-1 text-gray-900 font-semibold text-lg ml-2">IDR 80.000</p>
                    </a>
                </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-8 flex flex-col bg-white">
                    <a href="product.php">
                        <img class="w-full h-80 hover:grow hover:shadow-lg rounded-lg" src="https://images.unsplash.com/photo-1611506168454-656b2c960868?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTI0fHxmb29kJTIwYmFja2dyb3VuZHxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <div class="pt-3 flex items-center justify-between ml-2">
                            <p class="">Cumi-cumi 1kg</p>
                        </div>

                        <s class="pt-1 text-gray-900 font-semibold ml-2">IDR 90.000</s>
                        <p class="pt-1 text-gray-900 font-semibold text-lg ml-2">IDR 80.000</p>
                    </a>
                </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-8 flex flex-col bg-white">
                    <a href="product.php">
                        <img class="w-full h-80 hover:grow hover:shadow-lg rounded-lg" src="https://images.unsplash.com/photo-1619714604882-db1396d4a718?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTQ2fHxmb29kJTIwYmFja2dyb3VuZHxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <div class="pt-3 flex items-center justify-between ml-2">
                            <p class="">Cumi-cumi 500g</p>
                        </div>
                        <s class="pt-1 text-gray-900 font-semibold ml-2">IDR 90.000</s>
                        <p class="pt-1 text-gray-900 font-semibold text-lg ml-2">IDR 80.000</p>
                    </a>
                </div>
                <!--End produk 5-8 -->
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-8 flex flex-col bg-white">
                    <a href="product.php">
                        <img class="w-full h-80 hover:grow hover:shadow-lg rounded-lg" src="https://images.unsplash.com/photo-1598515211932-b130a728a769?ixid=MnwxMjA3fDB8MHxzZWFyY2h8ODF8fGZvb2QlMjBiYWNrZ3JvdW5kfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <div class="pt-3 flex items-center justify-between ml-2">
                        <p class="">Bakso Tenggiri 500g</p>
                        </div>
                        <s class="pt-1 text-gray-900 font-semibold ml-2">IDR 90.000</s>
                        <p class="pt-1 text-gray-900 font-semibold text-lg ml-2">IDR 80.000</p>
                    </a>
                </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-8 flex flex-col bg-white">
                    <a href="product.php">
                        <img class="w-full h-80 hover:grow hover:shadow-lg rounded-lg" src="https://images.unsplash.com/photo-1556471013-0001958d2f12?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTE3fHxmb29kJTIwYmFja2dyb3VuZHxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <div class="pt-3 flex items-center justify-between ml-2">
                            <p class="">Bakso Tenggiri 250g</p>
                        </div>
                        <s class="pt-1 text-gray-900 font-semibold ml-2">IDR 90.000</s>
                        <p class="pt-1 text-gray-900 font-semibold text-lg ml-2">IDR 80.000</p>
                    </a>
                </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-8 flex flex-col bg-white">
                    <a href="product.php">
                        <img class="w-full h-80 hover:grow hover:shadow-lg rounded-lg" src="https://images.unsplash.com/photo-1611506168454-656b2c960868?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTI0fHxmb29kJTIwYmFja2dyb3VuZHxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <div class="pt-3 flex items-center justify-between ml-2">
                            <p class="">Cumi-cumi 1kg</p>
                        </div>
                        <s class="pt-1 text-gray-900 font-semibold ml-2">IDR 90.000</s>
                        <p class="pt-1 text-gray-900 font-semibold text-lg ml-2">IDR 80.000</p>
                    </a>
                </div>
                <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 p-8 flex flex-col bg-white">
                    <a href="product.php">
                        <img class="w-full h-80 hover:grow hover:shadow-lg rounded-lg" src="https://images.unsplash.com/photo-1619714604882-db1396d4a718?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTQ2fHxmb29kJTIwYmFja2dyb3VuZHxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        <div class="pt-3 flex items-center justify-between ml-2">
                            <p class="">Cumi-cumi 500g</p>
                        </div>
                        <s class="pt-1 text-gray-900 font-semibold ml-2">IDR 90.000</s>
                        <p class="pt-1 text-gray-900 font-semibold text-lg ml-2">IDR 80.000</p>
                    </a>
                </div>
            </div>
        </section>

    </body>
</html>
