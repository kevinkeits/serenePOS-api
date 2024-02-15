var app = {
  boot: function() {
    if(getCookie(MSG['cookiePrefix']+'AUTH-TOKEN') != '') {
      doFetch('auth/doAuth','_success=1');
    } else {
      app.auth.clearSession();
    }
  },
  init: function(data) {
    $('#lblDisplayName').html(data.userData.FullName);
    setCookie(MSG['cookiePrefix']+'GLOBAL-USERID',data.userData.ID);
    setCookie(MSG['cookiePrefix']+'GLOBAL-ACCOUNTTYPE',data.userData.AccountType);
    if (data.accessMenu) {
      var primaryUrl = '';
      var primaryName = '';
      var html = '';
      for (i=0;i<data.accessMenu.length;i++) {
        html += '<li class="relative tagMenu tagMenu_'+data.accessMenu[i].URL+' ' + (data.accessMenu[i].ParentID ? 'ml-9' : 'ml-3') +  ' mr-3 px-6 py-3 hover:bg-blue-50 hover:rounded-full">' +
                '<a class="inline-flex items-center w-full text-sm transition-colors duration-150 hover:text-blue-500" href="#" onclick="loadMenu(\''+data.accessMenu[i].URL+'\',\''+data.accessMenu[i].Name+'\')">' +
                  data.accessMenu[i].Icon + 
                  '<span class="ml-4">' + data.accessMenu[i].Name + '</span>' +
                '</a>' +
                '</li>';
        if (primaryUrl=='') { primaryUrl = data.accessMenu[i].URL; }
        if (primaryName=='') { primaryName = data.accessMenu[i].Name; }
      }
      $('#menuWrapperFull').html(html);
      $('#menuWrapperMobile').html(html);
      Swal.close();
      if (primaryName!='' && primaryUrl!='') { loadMenu(primaryUrl,primaryName); }
    }
  },

  /* ===== Auth ===== */
  auth: {
    init: function() {      
      if(getCookie(MSG['cookiePrefix']+'AUTH-REMEMBER') == 'true') {
        window.location.href='index.php';
      }
    },
    doLogout: function() {
      var param = 'Token='+getCookie(MSG['cookiePrefix']+'AUTH-TOKEN');
      doSubmit('auth/doLogout',param);
    },
    loginHandler: function(data) {
      setCookie(MSG['cookiePrefix']+'AUTH-TOKEN',data.Token);
      setCookie(MSG['cookiePrefix']+'AUTH-REMEMBER',data.RememberLogin);
      setCookie(MSG['cookiePrefix']+'AUTH-ISBRANCHADMIN',data.IsBranchAdmin);
      window.location.href='index.php';
    },
    notAuthorizedHandler: function(message) {
      Swal.fire({title:'Error', text:message, icon:'error'})
      .then((result) => {
        if (result.isConfirmed) {
          app.auth.clearSession();
        }
      });
    },
    clearSession: function() {
      setCookie(MSG['cookiePrefix']+'AUTH-TOKEN','');
      setCookie(MSG['cookiePrefix']+'AUTH-REMEMBER','');
      setCookie(MSG['cookiePrefix']+'AUTH-ISBRANCHADMIN','');
      setCookie(MSG['cookiePrefix']+'GLOBAL-USERID','');
      window.location.href='login.php';
    },
  },
};