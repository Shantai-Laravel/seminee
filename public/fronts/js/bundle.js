!function(e){var t={};function n(o){if(t[o])return t[o].exports;var s=t[o]={i:o,l:!1,exports:{}};return e[o].call(s.exports,s,s.exports,n),s.l=!0,s.exports}n.m=e,n.c=t,n.d=function(e,t,o){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var s in e)n.d(o,s,function(t){return e[t]}.bind(null,s));return o},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=0)}([function(e,t,n){n(1),e.exports=n(2)},function(e,t){$(function(){var e=$(".footerTitle"),t=$(".footerBloc").children("ul"),n=$(".footerBloc").children(".line");screen.width<768&&($(e).addClass("bcgPlusFooter"),$(n).remove(),e.on("click",function(n){!function(n){$(e).removeClass("bcgMinusFooter"),$(e).addClass("bcgPlusFooter"),$(t).css("height","0"),$(t).css("padding-bottom","0"),$(n.target).hasClass("bcgPlusFooter")?($(n.target).removeClass("bcgPlusFooter"),$(n.target).addClass("bcgMinusFooter"),$(n.target).next(t).css("height","auto"),$(n.target).next(t).css("padding-bottom","20px")):($(n.target).addClass("bcgPlusFooter"),$(n.target).removeClass("bcgMinusFooter"),$(n.target).next(t).css("height","0"),$(n.target).next(t).css("padding-bottom","0"))}(n)}))}),$(function(){$(document).ready(function(){$(".slideHome").slick({slidesToShow:1,slidesToScroll:1,infinite:!1,autoplay:!1,autoplaySpeed:2e3,centerMode:!0,speed:3e3,arrows:!0,dots:!0,speed:500,rows:0}),$(".slideOneProduct").slick({slidesToShow:1,slidesToScroll:1,infinite:!0,autoplay:!0,autoplaySpeed:2e3,centerMode:!0,speed:3e3,arrows:!1,dots:!1,speed:500}),$(".slideSimilar").slick({rows:0,slidesToShow:1,slidesToScroll:1,infinite:!0,autoplay:!0,autoplaySpeed:2e3,centerMode:!0,speed:3e3,arrows:!0,dots:!1,speed:500})})}),$(function(){var e=$(".buttSearch").children("span"),t=$(".closeMenu"),n=$(".menuOpen"),o=$(".menuCabinet"),s=($(".menuCenter"),$(".submenuItem").children("span").children("a")),r=$(".submenu"),i=$("li.titleSubmenu"),c=$("#burger"),l=$(".menuItem").children("span"),u=$(".titleItem");$(u).click(function(e){$(this).parent().toggleClass("active")}),$(e).click(function(e){!function(e){$(n).css("display","none"),$(e.target).next(".menuOpen").css("display","block")}(e)}),$(t).click(function(e){$(n).css("display","none")}),screen.width<768&&($(s).removeAttr("href"),$(s).click(function(e){var t,n;n=(t=(t=e).target).innerText,$(t).parent().next().css("left","0px"),$(t).parent().parent().children().children("li.titleSubmenu").text(n)}),$(l).click(function(e){var t,n;n=(t=(t=e).target).innerText,$(t).next().css("left","0px"),$(t).parent().children(r).children("li.titleSubmenu").text(n)}),$(i).click(function(e){$(e.target).parent().css("left","-360px")}),$(c).click(function(){$(this).toggleClass("burgerOpen"),parseInt($(".menuCenter").css("left"))<0?$(".menuCenter").css("left","0"):($(".menuCenter").css("left","-360px"),$(r).css("left","-360px"),$(".submeItemBloc").css("left","-360px"))})),$(document).mouseup(function(e){n.is(e.target)||o.is(e.target)||0!==o.has(e.target).length||$(n).css("display","none")})}),$(document).ready(function(){var e=$(".btnFilter"),t=$(".filterOpen"),n=$(".closeFilter"),o=$(".opt"),s=$("optionFiltrOpen"),r=$(".closeThis");$(e).click(function(){$(t).show(),$("body").addClass("bodyHidden")}),$(n).click(function(){$(t).hide(),$("body").removeClass("bodyHidden")}),$(r).click(function(){$(t).hide(),$("body").removeClass("bodyHidden")}),$(o).click(function(){$(this).next(s).toggle(),$(this).toggleClass("submenuBcgMinus")})})},function(e,t,n){}]);
//# sourceMappingURL=bundle.js.map
