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
    <script src="../assets/js/alpine-2.8.2.min.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/sweetalert2.all.min.js"></script>
    <script src="../assets/js/polyfill.js"></script>
    <script src="../assets/js/pace.min.js"></script>
    <script src="../assets/js/helper.js?ts=<?=time()?>"></script>
    <script src="../assets/js/controller.js?ts=<?=time()?>"></script>
  </head>
  <body>
    <div class="flex items-center min-h-screen p-20 bg-gray-50" style="background-image: url('../assets/img/bg/ellabg.jfif')">
    <div
        class="lg:w-1/3 md:w-1/2 h-full max-w-4xl mx-auto overflow-hidden bg-white rounded-lg shadow-xl text-gray-600"
      >
        <div class="flex flex-col overflow-y-auto">
          <div class="flex items-center justify-center p-6 sm:p-12">
            <div class="w-full">
              
              <div class="flex justify-center flex-1 text-semibold color-blue-500">
                <img src="../assets/img/logo.png" style="height:150px">
              </div>

              <div x-show="isLoginPage">
                <h1 class="mt-8 mb-4 font-semibold text-center">
                  Masuk Disini
                </h1>
                <form id="frmLogin" onsubmit="return doSubmitForm(event,'auth/doLogin','frmLogin')">
                  <label class="block mt-4">
                    <span>Username</span>
                    <input
                      name="txtUserName"
                      class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
                      placeholder="username"
                      required
                    />
                  </label>
                  <label class="block mt-4">
                    <span>Password</span>
                    <input
                      name="txtPassword"
                      type="password"
                      class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
                      placeholder="********"
                      required
                    />
                  </label>
                  <div class="flex mt-6 text-sm">
                    <label class="flex items-center">
                      <input
                        name="chkRemember"
                        type="checkbox"
                        class="text-red-600 form-checkbox focus:border-red-400 focus:outline-none focus:shadow-outline-red"
                      />
                      <span class="ml-2">
                        Ingat Saya
                      </span>
                    </label>
                  </div>
                  <button 
                    type="submit"
                    class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg active:bg-red-800 hover:bg-red-700 focus:outline-none focus:shadow-outline-blue">
                    Masuk
                  </button>
                </form>
                <p class="mt-4">
                  <a 
                    @click="openForgot"
                    class="text-sm font-medium text-red-600 hover:underline" 
                    href="#">
                    Lupa Password?
                  </a>
                </p>
              </div>

              <div x-show="!isLoginPage">
                <h1 class="mt-8 mb-4 font-semibold text-center">
                  Lupa Password?
                </h1>
               
                <form id="frmReset" onsubmit="return doSubmitForm(event,'auth/doReset','frmReset')">
                  <label class="block mt-4">
                    <span>Email</span>
                    <input
                      name="txtEmail"
                      type="email"
                      class="border p-2 rounded w-full mt-1 text-sm form-input focus:border-gray-400 focus:outline-none focus:shadow-outline-gray"
                      placeholder="username@domain.com"
                      required
                    />
                  </label>
                  <button 
                    type="submit"
                    class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg active:bg-red-600 hover:bg-red-700 focus:outline-none focus:shadow-outline-blue">
                    Kirim
                  </button>
                </form>
                <p class="mt-4">
                  Ingat kata sandi anda?
                  <a 
                    @click="openLogin"
                    class="text-sm font-medium text-red-600 hover:underline" href="#">
                    Coba masuk kembali
                  </a>
                </p>
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      $(function(){
        app.auth.init();
      });
    </script>
  </body>
</html>