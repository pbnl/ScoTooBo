/*! password-generator - v1.0.1 (2015-09-24)
 * -----------------
 * Copyright(c) 2011-2015 Bermi Ferrer <bermi@bermilabs.com>
 * MIT Licensed
 */
(function(e){function o(e,t){var n,r,i=new Uint8Array(t);u(i);for(n in i)if(i.hasOwnProperty(n)){r=i[n];if(r>e&&r<t)return r}return o(e,t)}function u(t){if(e.crypto&&e.crypto.getRandomValues)e.crypto.getRandomValues(t);else if(typeof e.msCrypto=="object"&&typeof e.msCrypto.getRandomValues=="function")e.msCrypto.getRandomValues(t);else{if(module.exports!==i||typeof require=="undefined")throw new Error("No secure random number generator available.");var n=require("crypto").randomBytes(t.length);t.set(n)}}var t,n,r,i,s;r=/[a-zA-Z]$/,s=/[aeiouAEIOU]$/,n=/[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]$/,t=e.localPasswordGeneratorLibraryName||"generatePassword",i=function(e,t,r,i){var u="",a,f,l=[];e==null&&(e=10),t==null&&(t=!0),r==null&&(r=/\w/),i==null&&(i="");if(!t){for(f=33;126>f;f+=1)u=String.fromCharCode(f),u.match(r)&&l.push(u);if(!l.length)throw new Error("Could not find characters that match the password pattern "+r+". Patterns must match individual "+"characters, not the password as a whole.")}while(i.length<e)t?(i.match(n)?r=s:r=n,a=o(33,126),u=String.fromCharCode(a)):u=l[o(0,l.length)],t&&(u=u.toLowerCase()),u.match(r)&&(i=""+i+u);return i},(typeof exports!="undefined"?exports:e)[t]=i,typeof exports!="undefined"&&typeof module!="undefined"&&module.exports&&(module.exports=i)})(this);


/*
My stuff
 */
function goBack() {
    window.history.back();
}

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

/* Navigation */
$(document).ready(function () {
    $('[data-toggle="offcanvas"]').click(function () {
        $('.row-offcanvas').toggleClass('active')
    });
});
