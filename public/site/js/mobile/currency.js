!function(e){var n={};function r(t){if(n[t])return n[t].exports;var u=n[t]={i:t,l:!1,exports:{}};return e[t].call(u.exports,u,u.exports,r),u.l=!0,u.exports}r.m=e,r.c=n,r.d=function(e,n,t){r.o(e,n)||Object.defineProperty(e,n,{enumerable:!0,get:t})},r.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},r.t=function(e,n){if(1&n&&(e=r(e)),8&n)return e;if(4&n&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(r.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&n&&"string"!=typeof e)for(var u in e)r.d(t,u,function(n){return e[n]}.bind(null,u));return t},r.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(n,"a",n),n},r.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},r.p="",r(r.s=7)}({1:function(e,n){e.exports=jQuery},7:function(e,n,r){(function(e){e(document).ready((function(){var n=e(".currencies-table"),r=e(".expand-currencies");r.click((function(){r.hide(),n.addClass("expanded")}));var t=e("#currency-from")[0],u=e("#currency-to")[0];e(".swap-currencies").click((function(){var e,n,r,c;n=u,r=(e=t).parentNode,c=e.nextSibling===n?e:e.nextSibling,n.parentNode.insertBefore(e,n),r.insertBefore(n,c)}));var c=e("input[name='in-value']"),o=e("input[name='out-value']"),i=e("select[name='in-currency']"),a=e("select[name='out-currency']");function l(n){var r=Number(n.val())*Number(e("select[name='in-currency']").val())/Number(e("select[name='out-currency']").val());e("input[name='out-value']").val(r.toFixed(2))}function f(n){var r=Number(n.val()),t=Number(e("select[name='in-currency']").val()),u=r*Number(e("select[name='out-currency']").val())/t;e("input[name='in-value']").val(u.toFixed(2))}c.on("keyup",(function(){l(e(this))})),o.on("keyup",(function(){f(e(this))})),i.on("change",(function(){""!==c.val()&&l(c)})),a.on("change",(function(){""!==o.val()&&f(o)}))}))}).call(this,r(1))}});