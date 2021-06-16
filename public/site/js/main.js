!function(e){var t={};function i(n){if(t[n])return t[n].exports;var r=t[n]={i:n,l:!1,exports:{}};return e[n].call(r.exports,r,r.exports,i),r.l=!0,r.exports}i.m=e,i.c=t,i.d=function(e,t,n){i.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},i.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(e,t){if(1&t&&(e=i(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(i.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)i.d(n,r,function(t){return e[t]}.bind(null,r));return n},i.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return i.d(t,"a",t),t},i.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},i.p="",i(i.s=11)}([function(e,t,i){"use strict";function n(e,t){var i,n,r=!1;return function o(){if(r)return i=arguments,void(n=this);e.apply(this,arguments),r=!0,setTimeout((function(){r=!1,i&&(o.apply(n,i),i=n=null)}),t)}}function r(e){var t=e.getBoundingClientRect();return t.top>=0&&t.left>=0&&t.bottom<=(window.innerHeight||document.documentElement.clientHeight)&&t.right<=(window.innerWidth||document.documentElement.clientWidth)}function o(e,t,i){var n={value:t,expiry:(new Date).getTime()+i};localStorage.setItem(e,JSON.stringify(n))}function a(e){var t=localStorage.getItem(e);if(!t)return null;var i=JSON.parse(t);return(new Date).getTime()>i.expiry?(localStorage.removeItem(e),null):i.value}function l(){var e=document.createElement("script");e.async=!0,e.src="https://www.youtube.com/iframe_api";var t=document.getElementsByTagName("script")[0];t.parentNode.insertBefore(e,t)}function s(){return!!navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry|BB10|Tizen|webOS|IEMobile|Opera Mini|Symbian)/i)}function c(e){return e.length>0}i.d(t,"g",(function(){return n})),i.d(t,"c",(function(){return r})),i.d(t,"f",(function(){return o})),i.d(t,"b",(function(){return a})),i.d(t,"e",(function(){return l})),i.d(t,"d",(function(){return s})),i.d(t,"a",(function(){return c}))},function(e,t){e.exports=jQuery},function(e,t,i){"use strict";function n(e,t){for(var i=0;i<t.length;i++){var n=t[i];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}var r=function(){function e(t){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.$el=t,this.$player=t.querySelector("audio"),this.duration=0,this.currentVolume=1,this.isPlaying=!1,this.$togglePlayBtn=t.querySelector(".toggle-play"),this.$togglePlayIcon=this.$togglePlayBtn.querySelector("i"),this.$stopBtn=t.querySelector(".stop"),this.$progressBar=t.querySelector(".progress"),this.$tracker=t.querySelector(".progress .tracker"),this.$currentTime=t.querySelector(".current-time"),this.$totalTime=t.querySelector(".total-time"),this.$muteBtn=t.querySelector(".toggle-mute"),this.$muteBtnIcon=t.querySelector(".toggle-mute i"),this.$valueBar=t.querySelector(".volume"),this.$volumeAmount=t.querySelector(".amount")}var t,i,r;return t=e,(i=[{key:"init",value:function(){var e=this,t=this.$player,i=this.$tracker,n=this.$currentTime,r=this.$totalTime,s=this.$stopBtn,c=this.$progressBar,u=this.$muteBtn,d=this.$valueBar;t.load(),t.volume=1,t.onloadedmetadata=function(){e.duration=t.duration;var i=parseInt(e.duration/60,10),n=parseInt(e.duration%60);n=n<10?"0".concat(n):n,r.textContent="".concat(i," : ").concat(n)},t.addEventListener("timeupdate",(function(){var e,r,o=t.currentTime,a=t.duration;i.style.width=100*(o/a).toFixed(2)+"%",n.textContent=(r=~~((e=o)%60),"".concat(~~(e/60)," : ").concat(r<10?"0"+r:r))}),!1),t.addEventListener("ended",(function(){e.handleStop()})),s&&s.addEventListener("click",(function(){e.handleStop()})),c.addEventListener("click",(function(t){o.call(e,t)})),u&&u.addEventListener("click",(function(){l.call(e)})),d&&d.addEventListener("click",(function(t){a.call(e,t)}))}},{key:"handlePlay",value:function(){var e=this.$player,t=this.$togglePlayIcon;e.play(),t.classList.toggle("icon-play",e.paused),t.classList.toggle("icon-pause",!e.paused),this.isPlaying=!0}},{key:"handlePause",value:function(){var e=this.$player,t=this.$togglePlayIcon;e.pause(),t.classList.toggle("icon-play",e.paused),t.classList.toggle("icon-pause",!e.paused),this.isPlaying=!1}},{key:"handleStop",value:function(){var e=this.$player,t=this.$togglePlayIcon;e.pause(),e.currentTime=0,this.isPlaying=!1,t.classList.remove("icon-pause"),t.classList.add("icon-play")}}])&&n(t.prototype,i),r&&n(t,r),e}();function o(e){var t=this.$player,i=this.$tracker,n=this.duration,r=e.offsetX/e.target.offsetWidth;t.currentTime=r*n,i.style.width=100*r+"%"}function a(e){var t=this.$player,i=this.$volumeAmount,n=e.offsetX/e.target.offsetWidth;t.volume=n.toFixed(2),i.style.width=100*n+"%"}function l(){var e=this.$player,t=this.$volumeAmount,i=this.currentVolume,n=this.$muteBtnIcon;0==e.volume?e.volume=i:(this.currentVolume=e.volume,e.volume=0),t.style.width=100*e.volume+"%",n.classList.toggle("icon-volume-on",0!=e.volume),n.classList.toggle("icon-volume-off",0==e.volume)}t.a=r},,,,,,,,,function(e,t,i){"use strict";i.r(t),function(e){var t=i(0),n=i(2);e(document).ready((function(){var i=e(".intro-slider .swiper-container");if(Object(t.a)(i)){var r=new Swiper(i,{lazy:{loadPrevNext:!0,loadPrevNextAmount:2,preloadImages:!1},autoplay:{delay:8e3},loop:!0});e(".intro-slider .prev-slide").click((function(){return r.slidePrev()})),e(".intro-slider .next-slide").click((function(){return r.slideNext()}))}var o=e(".breaking-news-slider .swiper-container");if(Object(t.a)(o)){var a=new Swiper(o,{autoplay:{delay:5e3},loop:!0});e(".breaking-news-slider .prev-slide").click((function(){a.slidePrev()})),e(".breaking-news-slider .next-slide").click((function(){a.slideNext()}))}var l=e(".live-stream-container")[0];if(l){var s=e(".live-stream"),c=e(".live-stream iframe"),u=e(".remove-stream"),d=Object(t.b)("streamClosed"),f=Object(t.g)((function(){Object(t.c)(l)?s.removeClass("fixed"):s.addClass("fixed")}),250);null===d&&(e(window).on("scroll",f),s.addClass("fixed")),u.click((function(){e(window).off("scroll",f),s.removeClass("fixed"),c.attr("src",c.attr("src")),Object(t.f)("streamClosed",!0,36e5)}))}var p={lazy:{loadPrevNext:!0,loadPrevNextAmount:2,preloadImages:!1},spaceBetween:30,breakpoints:{1024:{slidesPerView:3},575:{slidesPerView:2},300:{slidesPerView:1}}},v=e(".main-for-week .swiper-container");if(Object(t.a)(v)){var m=new Swiper(v,p);e(".main-for-week .prev-slide").click((function(){m.slidePrev()})),e(".main-for-week .next-slide").click((function(){m.slideNext()}))}var w=e(".audio-playlist .swiper-container");if(Object(t.a)(w)){var y=new Swiper(w,p);e(".audio-playlist .prev-slide").click((function(){y.slidePrev()})),e(".audio-playlist .next-slide").click((function(){y.slideNext()}))}var h=[];e(".news-player").each((function(){var t=new n.a(e(this)[0]);t.init(),h.push(t)})),e(document).on("click",".news-player .toggle-play",(function(){for(var t=e(this).closest(".news-player"),i=e(".news-player").index(t),n=0;n<h.length;n++)n!==i&&h[n].handlePause();h[i].isPlaying?h[i].handlePause():h[i].handlePlay()}));var g=e(".most-read-news .swiper-container");if(Object(t.a)(g)){var P=new Swiper(g,p);e(".most-read-news .prev-slide").click((function(){P.slidePrev()})),e(".most-read-news .next-slide").click((function(){P.slideNext()}))}var b=e(".most-important-news .swiper-container");if(Object(t.a)(b)){var x=new Swiper(b,p);e(".most-important-news .prev-slide").click((function(){x.slidePrev()})),e(".most-important-news .next-slide").click((function(){x.slideNext()}))}var k=e(".exclusive-news .swiper-container");if(Object(t.a)(k)){var S=new Swiper(k,p);e(".exclusive-news .prev-slide").click((function(){S.slidePrev()})),e(".exclusive-news .next-slide").click((function(){S.slideNext()}))}var $=e(".video-news .swiper-container");if(Object(t.a)($)){var O=new Swiper($,{lazy:{loadPrevNext:!0,loadPrevNextAmount:2,preloadImages:!1},spaceBetween:30,loop:!0,breakpoints:{767:{slidesPerView:2}}});e(".video-news .prev-slide").click((function(){O.slidePrev()})),e(".video-news .next-slide").click((function(){O.slideNext()}))}var j=e(".photo-news .swiper-container");if(Object(t.a)(j)){var N=new Swiper(j,p);e(".photo-news .prev-slide").click((function(){N.slidePrev()})),e(".photo-news .next-slide").click((function(){N.slideNext()}))}var B=e(".interviews .swiper-container");if(Object(t.a)(B)){var I=new Swiper(B,{lazy:{loadPrevNext:!0,loadPrevNextAmount:2,preloadImages:!1},spaceBetween:30,breakpoints:{1024:{slidesPerView:4},767:{slidesPerView:3},575:{slidesPerView:2},300:{slidesPerView:1}}});e(".interviews .prev-slide").click((function(){I.slidePrev()})),e(".interviews .next-slide").click((function(){I.slideNext()}))}})),e(window).on("load",(function(){var i=e(".youtube-videos-container");if(Object(t.a)(i)){var n=function(e){e.data==YT.PlayerState.PLAYING?l.autoplay.stop():e.data==YT.PlayerState.PAUSED&&l.autoplay.start()},r=e(".youtube-videos-slider .swiper-container"),o=e(".youtube-videos-slider .swiper-slide"),a=[],l=new Swiper(r,{autoplay:{delay:7e3},effect:"fade",noSwipingClass:"no-swipe",pagination:{el:".swiper-pagination",type:"bullets",clickable:!0},on:{init:function(){Object(t.e)()}}});window.onYouTubeIframeAPIReady=function(){var t=[];for(var i in o.each((function(){t.push(e(this).find("iframe").attr("id"))})),t)a[i]=new YT.Player(t[i],{events:{onStateChange:n}})},e(".youtube-videos-slider").on("click",".swiper-pagination-bullet",(function(){!function(e){for(var t in a)t!==e&&a[t].stopVideo()}(e(this).index())}))}}))}.call(this,i(1))}]);