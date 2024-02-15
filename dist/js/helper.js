// Global Setting
var maxTimeout = 90000; //9 minutes
//var apiUrl = 'http://localhost/ella-froze/api/public'; //for localhost
var apiUrl = 'https://ellafroze.com/api'; //for server env
var uploadedUrl = apiUrl + '/uploaded';
//var uploadedUrl = 'https://ellafroze.com/api/uploaded';
var MSG = {
  cookiePrefix: 'MEFTDI-BETA-',
  onLoading: 'Tunggu sebentar...',
  onProcess: 'Masih dalam proses, mohon ditunggu...',
  disconnected: 'Mohon cek koneksi Anda',
  UrlNotFound: 'Halaman tidak ditemukan'
};

// Global Function
function setCookie(cname,cvalue) {
  var d = new Date();
  d.setTime(d.getTime() + ((30*12)*24*60*60*1000));
  var expires = 'expires=' + d.toGMTString();
  document.cookie = cname + '=' + cvalue + ';' + expires + ';path=/';
}
function getCookie(cname) {
  var name = cname + '=';
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return '';
}
function doStripTags(str) { 
  if ((str===null) || (str==='')) {
    return false; 
  } else {
    str = str.toString(); 
  }
  return str.replace( /(<([^>]+)>)/ig, ''); 
} 
function doFormatNumber(str,limit=0) {
  if (str != null) {
    if (limit==0) { 
      if (parseFloat(str)!=0) {
        if (str.toString().includes('.')) { 
          var result = parseFloat(str).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,').toString(); 
          return result.substr(0,result.length-3);
        }
        else { return str.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }
      } else {
        return str;
      }
    }
    else { return parseFloat(str).toFixed(limit).replace(/\d(?=(\d{3})+\.)/g, '$&,'); }
  } else {
    return "0";
  }
}
function doFormatID(str) {
  return str.replace(/\s+/g, '').toUpperCase();
}
function doBeforeSend(loading=true) {
  Pace.restart(); 
  if (loading) { 
    ons.notification.toast(MSG['onLoading'], {
      timeout: 1000
    });
  }
}
function doHandleSuccess(e) {
  setCookie(MSG['cookiePrefix']+'IsLoading',"false");
  if (!e.status) {
    if (e.message != '') {
      ons.notification.toast(e.message, {
        timeout: 2000
      });
    }
    if (e.callback) { eval(e.callback); }
  } else {
    if (e.message) { 
      if (e.message != "") {
        ons.notification.toast(e.message, {
          timeout: 2000
        });
      }
    }
    if (e.callback) { eval(e.callback); }
  }
}
function doHandleError(e, t, i) {
  setCookie(MSG['cookiePrefix']+'IsLoading',"false");
  var message = '';
  if (i == 'timeout' || i == '') { message = MSG['disconnected']; }
  if (i == 'Not Found') { message = MSG['UrlNotFound']; }
  message = message == '' ? i : message;
  if (message != "") {
    ons.notification.toast(message, {
      timeout: 2000
    });
  }
  if (e.callback) { eval(e.callback); }
  console.log(e, t, i);
}
function doSubmit(postUrl,arrData) {
  setCookie(MSG['cookiePrefix']+'IsLoading',"true");
  $.ajax({
    url: apiUrl+'/'+postUrl,
    type: 'post',
    data: arrData+'&_s='+getCookie(MSG['cookiePrefix']+'AUTH-TOKEN'),
    beforeSend: function(e, t, i) { doBeforeSend(); },
    success: function(e) { doHandleSuccess(e); },
    error: function(e, t, i) { doHandleError(e, t, i) },
    timeout: maxTimeout
  });
}
function doFetch(postUrl,params,loading=true) {
  $.ajax({
    url: apiUrl+'/'+postUrl+'?'+params+'&_s='+getCookie(MSG['cookiePrefix']+'AUTH-TOKEN'),
    type: 'get',
    beforeSend: function(e, t, i) { doBeforeSend(loading); },
    success: function(e) { doHandleSuccess(e); },
    error: function(e, t, i) { doHandleError(e, t, i) },
    timeout: maxTimeout
  });
}
function doOpenPage(pageSource,arrData="",callback="",animation="slide-md") {
  var mainNav = document.getElementById('mainNav');
  mainNav.pushPage(pageSource, { data: arrData == "" ? {} : arrData, animation: "none", callback: callback });
}
function doOpenURL(url) {
  window.open(url,'_blank');
}