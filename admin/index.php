<!DOCTYPE html>
<html x-data="data()" lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ella Froze - Admin Site</title>
    <link href="../assets/favicon.ico" rel="shortcut icon" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="../assets/css/tailwind.css" rel="stylesheet" />
    <link href="../assets/css/pace-flash.css" rel="stylesheet" />
    <link href="../assets/css/chart-2.9.3.min.css" rel="stylesheet" />
    <link href="../assets/css/duallistbox.css" rel="stylesheet" />
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
      .dataTables_empty {
        text-align: center;
        font-size: 0.875rem;
        line-height: 1.25rem;
      }
    </style>
    <script src="../assets/js/alpine-2.8.2.min.js"></script>
    <script src="../assets/js/charts-2.9.3.min.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/dataTables.min.js"></script>
    <script src="../assets/js/dataTables.buttons.min.js"></script>
    <script src="../assets/js/dataTables.buttons.colVis.min.js"></script>
    <script src="../assets/js/dataTables.buttons.html5.min.js"></script>
    <script src="../assets/js/sweetalert2.all.min.js"></script>
    <script src="../assets/js/polyfill.js"></script>
    <script src="../assets/js/pace.min.js"></script>
    <script src="../assets/js/helper.js?ts=<?=time()?>"></script>
    <script src="../assets/js/controller.js?ts=<?=time()?>"></script>
    <script src="../assets/js/duallistbox.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
  </head>
  <body>
    <div
      class="flex h-screen bg-white text-gray-700"
      :class="{ 'overflow-hidden': isSideMenuOpen}"
    >
      <aside id="sidebarWrapper"
        class="z-20 hidden w-64 overflow-y-auto bg-white md:block flex-shrink-0 border-r"
      >
        <div class="py-4">
          <span class="block ml-6 text-lg font-bold">ELLA FROZE</span>
          <span class="block ml-6 text-xs text-gray-500"><small>v0.0.1</small></span>
          <ul id="menuWrapperFull" class="mt-6"></ul>
        </div>
      </aside>  
      <div
        x-show="isSideMenuOpen"
        x-transition:enter="transition ease-in-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in-out duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-10 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
      ></div>
      <aside
        class="fixed inset-y-0 z-20 flex-shrink-0 w-64 mt-16 overflow-y-auto bg-white md:hidden"
        x-show="isSideMenuOpen"
        x-transition:enter="transition ease-in-out duration-150"
        x-transition:enter-start="opacity-0 transform -translate-x-20"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in-out duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 transform -translate-x-20"
        @click.away="closeSideMenu"
        @keydown.escape="closeSideMenu"
      >
        <div class="py-4">
          <ul id="menuWrapperMobile"></ul>
        </div>
      </aside>
      <div class="flex flex-col flex-1">
        <header id="headerWrapper" class="z-10 py-4 bg-white shadow-md">
          <div
            class="container flex items-center justify-between h-full px-6 mx-auto"
          >
            <button
              class="p-1 -ml-1 mr-5 rounded-md md:hidden focus:outline-none focus:shadow-outline-blue"
              @click="toggleSideMenu"
              aria-label="Menu"
            >
              <svg
                class="w-6 h-6"
                aria-hidden="true"
                fill="currentColor"
                viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
              </svg>
            </button>
            <div class="flex justify-center flex-1">
              <!--<svg width="126" height="19" viewBox="0 0 126 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.36375 13.3106C8.5371 13.7388 8.48214 14.2877 8.39757 14.6204C8.24959 15.2108 7.84369 15.8137 6.6429 15.8137C5.51821 15.8137 4.83325 15.1734 4.83325 14.2087V12.4914H0.000488281V13.8553C0.000488281 17.8055 3.15891 18.9989 6.54565 18.9989C9.80132 18.9989 12.482 17.9094 12.909 14.953C13.1289 13.4228 12.9682 12.4207 12.8921 12.0507C12.131 8.34162 5.29835 7.23557 4.78674 5.16067C4.7157 4.85469 4.70994 4.53754 4.76983 4.22925C4.89667 3.65959 5.28989 3.04003 6.41458 3.04003C7.47161 3.04003 8.08469 3.68038 8.08469 4.64506V5.74281H12.5792V4.49537C12.5792 0.636648 9.05716 0.0337219 6.51182 0.0337219C3.30689 0.0337219 0.689675 1.07741 0.211895 3.96729C0.0850511 4.75733 0.0639104 5.46421 0.254177 6.35405C1.03638 9.9716 7.43356 11.0194 8.36375 13.3106Z" fill="#0C4DA2"/>
                <path d="M66.7431 13.3106C66.9165 13.7388 66.8615 14.2877 66.777 14.6204C66.629 15.2108 66.2231 15.8137 65.0223 15.8137C63.8976 15.8137 63.2126 15.1734 63.2126 14.2087V12.4914H58.3799V13.8553C58.3799 17.8055 61.5383 18.9989 64.925 18.9989C68.1807 18.9989 70.8614 17.9094 71.2884 14.953C71.5083 13.4228 71.3476 12.4207 71.2715 12.0507C70.5104 8.34162 63.6777 7.23557 63.1661 5.16067C63.0951 4.85469 63.0893 4.53754 63.1492 4.22925C63.2761 3.65959 63.6693 3.04003 64.794 3.04003C65.851 3.04003 66.4641 3.68038 66.4641 4.64506V5.74281H70.9586V4.49537C70.9586 0.636648 67.4366 0.0337219 64.8912 0.0337219C61.6863 0.0337219 59.0691 1.07741 58.5913 3.96729C58.4644 4.75733 58.4433 5.46421 58.6336 6.35405C59.4158 9.9716 65.813 11.0194 66.7431 13.3106Z" fill="#0C4DA2"/>
                <path d="M19.2807 0.59919L15.9404 18.3086H20.807L23.272 2.23748H23.3735L25.7709 18.3086H30.6121L27.293 0.595032L19.2807 0.59919ZM46.4887 0.59919L44.269 14.138H44.1633L41.9477 0.59919H34.6034L34.206 18.3086H38.7132L38.8231 2.38718H38.9246L41.9308 18.3086H46.5014L49.5119 2.39133H49.6091L49.7233 18.3086H54.2262L53.8288 0.595032L46.4887 0.59919Z" fill="#0C4DA2"/>
                <path d="M82.0262 15.6557C83.2777 15.6557 83.6625 14.8075 83.7513 14.375C83.7893 14.1838 83.7935 13.926 83.7935 13.6973V0.599223H88.3515V13.2939C88.3523 13.6824 88.3382 14.0708 88.3092 14.4582C87.9921 17.7639 85.3368 18.8367 82.0262 18.8367C78.7113 18.8367 76.056 17.7639 75.7389 14.4582C75.7263 14.2836 75.6924 13.6183 75.6967 13.2939V0.595065H80.2546V13.6931C80.2504 13.926 80.2588 14.1838 80.2969 14.375C80.3814 14.8075 80.7704 15.6557 82.0262 15.6557ZM104.046 0.599223L104.292 14.4998H104.194L100.051 0.599223H93.3703V18.1215H97.7972L97.5519 3.7386H97.6492L102.093 18.1215H108.507V0.599223H104.046ZM119.585 15.4728C120.887 15.4728 121.344 14.6619 121.424 14.1838C121.462 13.9883 121.466 13.7388 121.466 13.5185V10.9404H119.619V8.36241H125.999V13.111C125.999 13.4436 125.99 13.6848 125.935 14.2752C125.639 17.5019 122.794 18.6537 119.602 18.6537C116.409 18.6537 113.568 17.5019 113.268 14.2752C113.217 13.6848 113.204 13.4436 113.204 13.111V5.65964C113.204 5.34363 113.247 4.78644 113.281 4.49537C113.682 1.18136 116.409 0.116882 119.602 0.116882C122.794 0.116882 125.593 1.17304 125.923 4.49121C125.982 5.05672 125.965 5.65548 125.965 5.65548V6.25009H121.424V5.25631C121.424 5.25631 121.424 4.84049 121.369 4.58269C121.284 4.19183 120.946 3.29368 119.568 3.29368C118.257 3.29368 117.872 4.14609 117.775 4.58269C117.72 4.81555 117.699 5.13156 117.699 5.41847V13.5143C117.699 13.7388 117.707 13.9883 117.741 14.1879C117.826 14.6619 118.282 15.4728 119.585 15.4728Z" fill="#0C4DA2"/>
              </svg>-->
            </div>
            <ul class="flex items-center flex-shrink-0 space-x-6">
              <li class="relative">
                <button
                  class="align-middle rounded-full bg-gray-300 p-1"
                  @click="toggleProfileMenu"
                  @keydown.escape="closeProfileMenu"
                  aria-label="Account"
                  aria-haspopup="true"
                >
                  <svg
                    class="w-5 h-5"
                    aria-hidden="true"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <circle cx="11.5788" cy="7.27803" r="4.77803"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.00002 18.7014C3.99873 18.3655 4.07385 18.0337 4.2197 17.7311C4.67736 16.8158 5.96798 16.3307 7.03892 16.111C7.81128 15.9462 8.59431 15.836 9.38217 15.7815C10.8408 15.6533 12.3079 15.6533 13.7666 15.7815C14.5544 15.8367 15.3374 15.9468 16.1099 16.111C17.1808 16.3307 18.4714 16.77 18.9291 17.7311C19.2224 18.3479 19.2224 19.064 18.9291 19.6808C18.4714 20.6419 17.1808 21.0812 16.1099 21.2918C15.3384 21.4634 14.5551 21.5766 13.7666 21.6304C12.5794 21.7311 11.3866 21.7494 10.1968 21.6854C9.92221 21.6854 9.65677 21.6854 9.38217 21.6304C8.59663 21.5773 7.81632 21.4641 7.04807 21.2918C5.96798 21.0812 4.68652 20.6419 4.2197 19.6808C4.0746 19.3747 3.99955 19.0401 4.00002 18.7014Z"/>
                  </svg>
                </button>
                <template x-if="isProfileMenuOpen">
                  <ul
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click.away="closeProfileMenu"
                    @keydown.escape="closeProfileMenu"
                    class="absolute right-0 w-56 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md"
                    aria-label="submenu"
                  >
                    <li class="flex" onclick="app.auth.doLogout()">
                      <a
                        class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-blue-50 hover:text-blue-500"
                        href="#"
                      >
                        <svg 
                          class="w-4 h-4 mr-3"
                          aria-hidden="true"
                          fill="none"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          viewBox="0 0 24 24"
                          stroke="currentColor">
                          <path d="M15.0155 7.38951V6.45651C15.0155 4.42151 13.3655 2.77151 11.3305 2.77151H6.45548C4.42148 2.77151 2.77148 4.42151 2.77148 6.45651V17.5865C2.77148 19.6215 4.42148 21.2715 6.45548 21.2715H11.3405C13.3695 21.2715 15.0155 19.6265 15.0155 17.5975V16.6545"/>
                          <path d="M21.8086 12.0214H9.76758"/>
                          <path d="M18.8809 9.10632L21.8089 12.0213L18.8809 14.9373"/>
                        </svg>
                        <span>Log out</span>
                      </a>
                    </li>
                  </ul>
                </template>
              </li>
              <li 
                id="lblDisplayName"
                class="relative text-gray-500 text-sm hidden sm:block cursor-pointer"
                @click="toggleProfileMenu"
                @keydown.escape="closeProfileMenu"
                aria-label="Account"
                aria-haspopup="true">
                &nbsp;
              </li>
            </ul>
          </div>
        </header>
        <main class="h-full pb-16 overflow-y-auto">
          <div id="bgWrapper" class="flex items-center h-28 p-4 mb-6 text-2xl text-white shadow-md" style="background-image: linear-gradient(90deg, #EF4444 0%, rgba(129, 192, 255, 0) 100%), url('../assets/img/bg/pexels-scott-webb-532563.jpg')">
            <span id="lblHero" class="ml-6">&nbsp;</span>
          </div>
          <div class="container px-2 sm:px-6 mx-auto grid">
            <span id="lblBreadcrumb" class="text-gray-400 text-sm hidden sm:block">&nbsp;</span>
            <div id="contentWrapper" class="w-full my-6 mb-8 overflow-hidden rounded-lg shadow-xs"></div>
          </div>
        </main>
      </div>
    </div>

    <div
      id="modalWrapper"
      style="display:none"
      class="fixed inset-0 z-30 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
    >
      <div 
        id="modalBody"
        style="display:none"
        class="w-full px-6 py-4 overflow-scroll max-h-full bg-white rounded-t-lg sm:rounded-lg sm:m-4 sm:max-w-xl">
        <header class="flex justify-end">
          <button
            class="inline-flex items-center justify-center w-6 h-6 text-gray-400 transition-colors duration-150 rounded hover:text-gray-700"
            aria-label="close"
            onclick="modal.close()"
          >
            <svg
              class="w-4 h-4"
              fill="currentColor"
              viewBox="0 0 20 20"
              role="img"
              aria-hidden="true"
            >
              <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd"></path>
            </svg>
          </button>
        </header>
        <div id="modalContentWrapper">&nbsp;</div>
      </div>
    </div>
    <script>
      $(function(){
        app.boot();
      });
    </script>
  </body>
</html>