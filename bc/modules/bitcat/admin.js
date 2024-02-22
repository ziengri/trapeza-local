// Spectrum Colorpicker v1.8.0
// https://github.com/bgrins/spectrum
// Author: Brian Grinstead
// License: MIT
!function(a){"use strict";"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports&&"object"==typeof module?module.exports=a(require("jquery")):a(jQuery)}(function(a,b){"use strict";function i(b,c,d,e){for(var g=[],h=0;h<b.length;h++){var i=b[h];if(i){var j=tinycolor(i),k=j.toHsl().l<.5?"sp-thumb-el sp-thumb-dark":"sp-thumb-el sp-thumb-light";k+=tinycolor.equals(c,i)?" sp-thumb-active":"";var l=j.toString(e.preferredFormat||"rgb"),m=f?"background-color:"+j.toRgbString():"filter:"+j.toFilter();g.push('<span title="'+l+'" data-color="'+j.toRgbString()+'" class="'+k+'"><span class="sp-thumb-inner" style="'+m+';" /></span>')}else{var n="sp-clear-display";g.push(a("<div />").append(a('<span data-color="" style="background-color:transparent;" class="'+n+'"></span>').attr("title",e.noColorSelectedText)).html())}}return"<div class='sp-cf "+d+"'>"+g.join("")+"</div>"}function j(){for(var a=0;a<d.length;a++)d[a]&&d[a].hide()}function k(b,d){var e=a.extend({},c,b);return e.callbacks={move:p(e.move,d),change:p(e.change,d),show:p(e.show,d),hide:p(e.hide,d),beforeShow:p(e.beforeShow,d)},e}function l(c,l){function xa(){if(n.showPaletteOnly&&(n.showPalette=!0),ka.text(n.showPaletteOnly?n.togglePaletteMoreText:n.togglePaletteLessText),n.palette){M=n.palette.slice(0),N=a.isArray(M[0])?M:[M],O={};for(var b=0;b<N.length;b++)for(var c=0;c<N[b].length;c++){var d=tinycolor(N[b][c]).toRgbString();O[d]=!0}}X.toggleClass("sp-flat",p),X.toggleClass("sp-input-disabled",!n.showInput),X.toggleClass("sp-alpha-enabled",n.showAlpha),X.toggleClass("sp-clear-enabled",wa),X.toggleClass("sp-buttons-disabled",!n.showButtons),X.toggleClass("sp-palette-buttons-disabled",!n.togglePaletteOnly),X.toggleClass("sp-palette-disabled",!n.showPalette),X.toggleClass("sp-palette-only",n.showPaletteOnly),X.toggleClass("sp-initial-disabled",!n.showInitial),X.addClass(n.className).addClass(n.containerClassName),Ua()}function ya(){function c(b){return b.data&&b.data.ignore?(Na(a(b.target).closest(".sp-thumb-el").data("color")),Qa()):(Na(a(b.target).closest(".sp-thumb-el").data("color")),Qa(),Ta(!0),n.hideAfterPaletteSelect&&La()),!1}if(e&&X.find("*:not(input)").attr("unselectable","on"),xa(),na&&V.after(oa).hide(),wa||ia.hide(),p)V.after(X).hide();else{var b="parent"===n.appendTo?V.parent():a(n.appendTo);1!==b.length&&(b=a("body")),b.append(X)}za(),pa.bind("click.spectrum touchstart.spectrum",function(b){W||Ha(),b.stopPropagation(),a(b.target).is("input")||b.preventDefault()}),(V.is(":disabled")||n.disabled===!0)&&Ya(),X.click(o),ea.change(Ga),ea.bind("paste",function(){setTimeout(Ga,1)}),ea.keydown(function(a){13==a.keyCode&&Ga()}),ha.text(n.cancelText),ha.bind("click.spectrum",function(a){a.stopPropagation(),a.preventDefault(),Ma(),La()}),ia.attr("title",n.clearText),ia.bind("click.spectrum",function(a){a.stopPropagation(),a.preventDefault(),va=!0,Qa(),p&&Ta(!0)}),ja.text(n.chooseText),ja.bind("click.spectrum",function(a){a.stopPropagation(),a.preventDefault(),e&&ea.is(":focus")&&ea.trigger("change"),Pa()&&(Ta(!0),La())}),ka.text(n.showPaletteOnly?n.togglePaletteMoreText:n.togglePaletteLessText),ka.bind("click.spectrum",function(a){a.stopPropagation(),a.preventDefault(),n.showPaletteOnly=!n.showPaletteOnly,n.showPaletteOnly||p||X.css("left","-="+(Y.outerWidth(!0)+5)),xa()}),q(ca,function(a,b,c){L=a/F,va=!1,c.shiftKey&&(L=Math.round(10*L)/10),Qa()},Ea,Fa),q(_,function(a,b){I=parseFloat(b/D),va=!1,n.showAlpha||(L=1),Qa()},Ea,Fa),q(Z,function(a,b,c){if(c.shiftKey){if(!S){var d=J*A,e=B-K*B,f=Math.abs(a-d)>Math.abs(b-e);S=f?"x":"y"}}else S=null;var g=!S||"x"===S,h=!S||"y"===S;g&&(J=parseFloat(a/A)),h&&(K=parseFloat((B-b)/B)),va=!1,n.showAlpha||(L=1),Qa()},Ea,Fa),ra?(Na(ra),Ra(),ta=n.preferredFormat||tinycolor(ra).format,Aa(ra)):Ra(),p&&Ia();var d=e?"mousedown.spectrum":"click.spectrum touchstart.spectrum";fa.delegate(".sp-thumb-el",d,c),ga.delegate(".sp-thumb-el:nth-child(1)",d,{ignore:!0},c)}function za(){if(u&&window.localStorage){try{var b=window.localStorage[u].split(",#");b.length>1&&(delete window.localStorage[u],a.each(b,function(a,b){Aa(b)}))}catch(a){}try{P=window.localStorage[u].split(";")}catch(a){}}}function Aa(b){if(t){var c=tinycolor(b).toRgbString();if(!O[c]&&a.inArray(c,P)===-1)for(P.push(c);P.length>Q;)P.shift();if(u&&window.localStorage)try{window.localStorage[u]=P.join(";")}catch(a){}}}function Ba(){var a=[];if(n.showPalette)for(var b=0;b<P.length;b++){var c=tinycolor(P[b]).toRgbString();O[c]||a.push(P[b])}return a.reverse().slice(0,n.maxSelectionSize)}function Ca(){var b=Oa(),c=a.map(N,function(a,c){return i(a,b,"sp-palette-row sp-palette-row-"+c,n)});za(),P&&c.push(i(Ba(),b,"sp-palette-row sp-palette-row-selection",n)),fa.html(c.join(""))}function Da(){if(n.showInitial){var a=sa,b=Oa();ga.html(i([a,b],b,"sp-palette-row-initial",n))}}function Ea(){(B<=0||A<=0||D<=0)&&Ua(),z=!0,X.addClass(R),S=null,V.trigger("dragstart.spectrum",[Oa()])}function Fa(){z=!1,X.removeClass(R),V.trigger("dragstop.spectrum",[Oa()])}function Ga(){var a=ea.val();if(null!==a&&""!==a||!wa){var b=tinycolor(a);b.isValid()?(Na(b),Ta(!0)):ea.addClass("sp-validation-error")}else Na(null),Ta(!0)}function Ha(){y?La():Ia()}function Ia(){var b=a.Event("beforeShow.spectrum");return y?void Ua():(V.trigger(b,[Oa()]),void(w.beforeShow(Oa())===!1||b.isDefaultPrevented()||(j(),y=!0,a(T).bind("keydown.spectrum",Ja),a(T).bind("click.spectrum",Ka),a(window).bind("resize.spectrum",x),oa.addClass("sp-active"),X.removeClass("sp-hidden"),Ua(),Ra(),sa=Oa(),Da(),w.show(sa),V.trigger("show.spectrum",[sa]))))}function Ja(a){27===a.keyCode&&La()}function Ka(a){2!=a.button&&(z||(ua?Ta(!0):Ma(),La()))}function La(){y&&!p&&(y=!1,a(T).unbind("keydown.spectrum",Ja),a(T).unbind("click.spectrum",Ka),a(window).unbind("resize.spectrum",x),oa.removeClass("sp-active"),X.addClass("sp-hidden"),w.hide(Oa()),V.trigger("hide.spectrum",[Oa()]))}function Ma(){Na(sa,!0)}function Na(a,b){if(tinycolor.equals(a,Oa()))return void Ra();var c,d;!a&&wa?va=!0:(va=!1,c=tinycolor(a),d=c.toHsv(),I=d.h%360/360,J=d.s,K=d.v,L=d.a),Ra(),c&&c.isValid()&&!b&&(ta=n.preferredFormat||c.getFormat())}function Oa(a){return a=a||{},wa&&va?null:tinycolor.fromRatio({h:I,s:J,v:K,a:Math.round(100*L)/100},{format:a.format||ta})}function Pa(){return!ea.hasClass("sp-validation-error")}function Qa(){Ra(),w.move(Oa()),V.trigger("move.spectrum",[Oa()])}function Ra(){ea.removeClass("sp-validation-error"),Sa();var a=tinycolor.fromRatio({h:I,s:1,v:1});Z.css("background-color",a.toHexString());var b=ta;L<1&&(0!==L||"name"!==b)&&("hex"!==b&&"hex3"!==b&&"hex6"!==b&&"name"!==b||(b="rgb"));var c=Oa({format:b}),d="";if(qa.removeClass("sp-clear-display"),qa.css("background-color","transparent"),!c&&wa)qa.addClass("sp-clear-display");else{var g=c.toHexString(),h=c.toRgbString();if(f||1===c.alpha?qa.css("background-color",h):(qa.css("background-color","transparent"),qa.css("filter",c.toFilter())),n.showAlpha){var i=c.toRgb();i.a=0;var j=tinycolor(i).toRgbString(),k="linear-gradient(left, "+j+", "+g+")";e?ba.css("filter",tinycolor(j).toFilter({gradientType:1},g)):(ba.css("background","-webkit-"+k),ba.css("background","-moz-"+k),ba.css("background","-ms-"+k),ba.css("background","linear-gradient(to right, "+j+", "+g+")"))}d=c.toString(b)}n.showInput&&ea.val(d),n.showPalette&&Ca(),Da()}function Sa(){var a=J,b=K;if(wa&&va)da.hide(),aa.hide(),$.hide();else{da.show(),aa.show(),$.show();var c=a*A,d=B-b*B;c=Math.max(-C,Math.min(A-C,c-C)),d=Math.max(-C,Math.min(B-C,d-C)),$.css({top:d+"px",left:c+"px"});var e=L*F;da.css({left:e-G/2+"px"});var f=I*D;aa.css({top:f-H+"px"})}}function Ta(a){var b=Oa(),c="",d=!tinycolor.equals(b,sa);b&&(c=b.toString(ta),Aa(b)),la&&V.val(c),a&&d&&(w.change(b),V.trigger("change",[b]))}function Ua(){y&&(A=Z.width(),B=Z.height(),C=$.height(),E=_.width(),D=_.height(),H=aa.height(),F=ca.width(),G=da.width(),p||(X.css("position","absolute"),n.offset?X.offset(n.offset):X.offset(m(X,pa))),Sa(),n.showPalette&&Ca(),V.trigger("reflow.spectrum"))}function Va(){V.show(),pa.unbind("click.spectrum touchstart.spectrum"),X.remove(),oa.remove(),d[$a.id]=null}function Wa(c,d){return c===b?a.extend({},n):d===b?n[c]:(n[c]=d,"preferredFormat"===c&&(ta=n.preferredFormat),void xa())}function Xa(){W=!1,V.attr("disabled",!1),pa.removeClass("sp-disabled")}function Ya(){La(),W=!0,V.attr("disabled",!0),pa.addClass("sp-disabled")}function Za(a){n.offset=a,Ua()}var n=k(l,c),p=n.flat,t=n.showSelectionPalette,u=n.localStorageKey,v=n.theme,w=n.callbacks,x=r(Ua,10),y=!1,z=!1,A=0,B=0,C=0,D=0,E=0,F=0,G=0,H=0,I=0,J=0,K=0,L=1,M=[],N=[],O={},P=n.selectionPalette.slice(0),Q=n.maxSelectionSize,R="sp-dragging",S=null,T=c.ownerDocument,V=(T.body,a(c)),W=!1,X=a(h,T).addClass(v),Y=X.find(".sp-picker-container"),Z=X.find(".sp-color"),$=X.find(".sp-dragger"),_=X.find(".sp-hue"),aa=X.find(".sp-slider"),ba=X.find(".sp-alpha-inner"),ca=X.find(".sp-alpha"),da=X.find(".sp-alpha-handle"),ea=X.find(".sp-input"),fa=X.find(".sp-palette"),ga=X.find(".sp-initial"),ha=X.find(".sp-cancel"),ia=X.find(".sp-clear"),ja=X.find(".sp-choose"),ka=X.find(".sp-palette-toggle"),la=V.is("input"),ma=la&&"color"===V.attr("type")&&s(),na=la&&!p,oa=na?a(g).addClass(v).addClass(n.className).addClass(n.replacerClassName):a([]),pa=na?oa:V,qa=oa.find(".sp-preview-inner"),ra=n.color||la&&V.val(),sa=!1,ta=n.preferredFormat,ua=!n.showButtons||n.clickoutFiresChange,va=!ra,wa=n.allowEmpty&&!ma;ya();var $a={show:Ia,hide:La,toggle:Ha,reflow:Ua,option:Wa,enable:Xa,disable:Ya,offset:Za,set:function(a){Na(a),Ta()},get:Oa,destroy:Va,container:X};return $a.id=d.push($a)-1,$a}function m(b,c){var d=0,e=b.outerWidth(),f=b.outerHeight(),g=c.outerHeight(),h=b[0].ownerDocument,i=h.documentElement,j=i.clientWidth+a(h).scrollLeft(),k=i.clientHeight+a(h).scrollTop(),l=c.offset();return l.top+=g,l.left-=Math.min(l.left,l.left+e>j&&j>e?Math.abs(l.left+e-j):0),l.top-=Math.min(l.top,l.top+f>k&&k>f?Math.abs(f+g-d):d),l}function n(){}function o(a){a.stopPropagation()}function p(a,b){var c=Array.prototype.slice,d=c.call(arguments,2);return function(){return a.apply(b,d.concat(c.call(arguments)))}}function q(b,c,d,f){function n(a){a.stopPropagation&&a.stopPropagation(),a.preventDefault&&a.preventDefault(),a.returnValue=!1}function o(a){if(h){if(e&&g.documentMode<9&&!a.button)return q();var d=a.originalEvent&&a.originalEvent.touches&&a.originalEvent.touches[0],f=d&&d.pageX||a.pageX,m=d&&d.pageY||a.pageY,o=Math.max(0,Math.min(f-i.left,k)),p=Math.max(0,Math.min(m-i.top,j));l&&n(a),c.apply(b,[o,p,a])}}function p(c){var e=c.which?3==c.which:2==c.button;e||h||d.apply(b,arguments)!==!1&&(h=!0,j=a(b).height(),k=a(b).width(),i=a(b).offset(),a(g).bind(m),a(g.body).addClass("sp-dragging"),o(c),n(c))}function q(){h&&(a(g).unbind(m),a(g.body).removeClass("sp-dragging"),setTimeout(function(){f.apply(b,arguments)},0)),h=!1}c=c||function(){},d=d||function(){},f=f||function(){};var g=document,h=!1,i={},j=0,k=0,l="ontouchstart"in window,m={};m.selectstart=n,m.dragstart=n,m["touchmove mousemove"]=o,m["touchend mouseup"]=q,a(b).bind("touchstart mousedown",p)}function r(a,b,c){var d;return function(){var e=this,f=arguments,g=function(){d=null,a.apply(e,f)};c&&clearTimeout(d),!c&&d||(d=setTimeout(g,b))}}function s(){return a.fn.spectrum.inputTypeColorSupport()}var c={beforeShow:n,move:n,change:n,show:n,hide:n,color:!1,flat:!1,showInput:!1,allowEmpty:!1,showButtons:!0,clickoutFiresChange:!0,showInitial:!1,showPalette:!1,showPaletteOnly:!1,hideAfterPaletteSelect:!1,togglePaletteOnly:!1,showSelectionPalette:!0,localStorageKey:!1,appendTo:"body",maxSelectionSize:7,cancelText:"cancel",chooseText:"choose",togglePaletteMoreText:"more",togglePaletteLessText:"less",clearText:"Clear Color Selection",noColorSelectedText:"No Color Selected",preferredFormat:!1,className:"",containerClassName:"",replacerClassName:"",showAlpha:!1,theme:"sp-light",palette:[["#ffffff","#000000","#ff0000","#ff8000","#ffff00","#008000","#0000ff","#4b0082","#9400d3"]],selectionPalette:[],disabled:!1,offset:null},d=[],e=!!/msie/i.exec(window.navigator.userAgent),f=function(){function a(a,b){return!!~(""+a).indexOf(b)}var b=document.createElement("div"),c=b.style;return c.cssText="background-color:rgba(0,0,0,.5)",a(c.backgroundColor,"rgba")||a(c.backgroundColor,"hsla")}(),g=["<div class='sp-replacer'>","<div class='sp-preview'><div class='sp-preview-sec'><div class='sp-preview-inner'></div></div></div>","<div class='sp-dd'>&#9660;</div>","</div>"].join(""),h=function(){var a="";if(e)for(var b=1;b<=6;b++)a+="<div class='sp-"+b+"'></div>";return["<div class='sp-container sp-hidden'>","<div class='sp-palette-container'>","<div class='sp-palette sp-thumb sp-cf'></div>","<div class='sp-palette-button-container sp-cf'>","<button type='button' class='sp-palette-toggle'></button>","</div>","</div>","<div class='sp-picker-container'>","<div class='sp-input-container sp-cf'>","<input class='sp-input' type='text' spellcheck='false' placeholder='#color'  />","</div>","<div class='sp-top sp-cf'>","<div class='sp-fill'></div>","<div class='sp-top-inner'>","<div class='sp-color'>","<div class='sp-sat'>","<div class='sp-val'>","<div class='sp-dragger'></div>","</div>","</div>","</div>","<div class='sp-clear sp-clear-display'>","</div>","<div class='sp-hue'>","<div class='sp-slider'></div>",a,"</div>","</div>","<div class='sp-alpha'><div class='sp-alpha-inner'><div class='sp-alpha-handle'></div></div></div>","</div>","<div class='sp-initial sp-thumb sp-cf'></div>","<div class='sp-button-container sp-cf'>","<a class='sp-cancel' href='#'></a>","<button type='button' class='sp-choose'></button>","</div>","</div>","</div>"].join("")}(),t="spectrum.id";a.fn.spectrum=function(b,c){if("string"==typeof b){var e=this,f=Array.prototype.slice.call(arguments,1);return this.each(function(){var c=d[a(this).data(t)];if(c){var g=c[b];if(!g)throw new Error("Spectrum: no such method: '"+b+"'");"get"==b?e=c.get():"container"==b?e=c.container:"option"==b?e=c.option.apply(c,f):"destroy"==b?(c.destroy(),a(this).removeData(t)):g.apply(c,f)}}),e}return this.spectrum("destroy").each(function(){var c=a.extend({},b,a(this).data()),d=l(this,c);a(this).data(t,d.id)})},a.fn.spectrum.load=!0,a.fn.spectrum.loadOpts={},a.fn.spectrum.draggable=q,a.fn.spectrum.defaults=c,a.fn.spectrum.inputTypeColorSupport=function b(){if("undefined"==typeof b._cachedResult){var c=a("<input type='color'/>")[0];b._cachedResult="color"===c.type&&""!==c.value}return b._cachedResult},a.spectrum={},a.spectrum.localization={},a.spectrum.palettes={},a.fn.spectrum.processNativeColorInputs=function(){var b=a("input[type=color]");b.length&&!s()&&b.spectrum({preferredFormat:"hex6"})},function(){function j(a){var b={r:0,g:0,b:0},c=1,d=!1,e=!1;return"string"==typeof a&&(a=S(a)),"object"==typeof a&&(a.hasOwnProperty("r")&&a.hasOwnProperty("g")&&a.hasOwnProperty("b")?(b=k(a.r,a.g,a.b),d=!0,e="%"===String(a.r).substr(-1)?"prgb":"rgb"):a.hasOwnProperty("h")&&a.hasOwnProperty("s")&&a.hasOwnProperty("v")?(a.s=O(a.s),a.v=O(a.v),b=o(a.h,a.s,a.v),d=!0,e="hsv"):a.hasOwnProperty("h")&&a.hasOwnProperty("s")&&a.hasOwnProperty("l")&&(a.s=O(a.s),a.l=O(a.l),b=m(a.h,a.s,a.l),d=!0,e="hsl"),a.hasOwnProperty("a")&&(c=a.a)),c=H(c),{ok:d,format:a.format||e,r:f(255,g(b.r,0)),g:f(255,g(b.g,0)),b:f(255,g(b.b,0)),a:c}}function k(a,b,c){return{r:255*I(a,255),g:255*I(b,255),b:255*I(c,255)}}function l(a,b,c){a=I(a,255),b=I(b,255),c=I(c,255);var h,i,d=g(a,b,c),e=f(a,b,c),j=(d+e)/2;if(d==e)h=i=0;else{var k=d-e;switch(i=j>.5?k/(2-d-e):k/(d+e),d){case a:h=(b-c)/k+(b<c?6:0);break;case b:h=(c-a)/k+2;break;case c:h=(a-b)/k+4}h/=6}return{h:h,s:i,l:j}}function m(a,b,c){function g(a,b,c){return c<0&&(c+=1),c>1&&(c-=1),c<1/6?a+6*(b-a)*c:c<.5?b:c<2/3?a+(b-a)*(2/3-c)*6:a}var d,e,f;if(a=I(a,360),b=I(b,100),c=I(c,100),0===b)d=e=f=c;else{var h=c<.5?c*(1+b):c+b-c*b,i=2*c-h;d=g(i,h,a+1/3),e=g(i,h,a),f=g(i,h,a-1/3)}return{r:255*d,g:255*e,b:255*f}}function n(a,b,c){a=I(a,255),b=I(b,255),c=I(c,255);var h,i,d=g(a,b,c),e=f(a,b,c),j=d,k=d-e;if(i=0===d?0:k/d,d==e)h=0;else{switch(d){case a:h=(b-c)/k+(b<c?6:0);break;case b:h=(c-a)/k+2;break;case c:h=(a-b)/k+4}h/=6}return{h:h,s:i,v:j}}function o(a,b,c){a=6*I(a,360),b=I(b,100),c=I(c,100);var e=d.floor(a),f=a-e,g=c*(1-b),h=c*(1-f*b),i=c*(1-(1-f)*b),j=e%6,k=[c,h,g,g,i,c][j],l=[i,c,c,h,g,g][j],m=[g,g,i,c,c,h][j];return{r:255*k,g:255*l,b:255*m}}function p(a,b,c,d){var f=[N(e(a).toString(16)),N(e(b).toString(16)),N(e(c).toString(16))];return d&&f[0].charAt(0)==f[0].charAt(1)&&f[1].charAt(0)==f[1].charAt(1)&&f[2].charAt(0)==f[2].charAt(1)?f[0].charAt(0)+f[1].charAt(0)+f[2].charAt(0):f.join("")}function q(a,b,c,d){var f=[N(P(d)),N(e(a).toString(16)),N(e(b).toString(16)),N(e(c).toString(16))];return f.join("")}function r(a,b){b=0===b?0:b||10;var c=i(a).toHsl();return c.s-=b/100,c.s=J(c.s),i(c)}function s(a,b){b=0===b?0:b||10;var c=i(a).toHsl();return c.s+=b/100,c.s=J(c.s),i(c)}function t(a){return i(a).desaturate(100)}function u(a,b){b=0===b?0:b||10;var c=i(a).toHsl();return c.l+=b/100,c.l=J(c.l),i(c)}function v(a,b){b=0===b?0:b||10;var c=i(a).toRgb();return c.r=g(0,f(255,c.r-e(255*-(b/100)))),c.g=g(0,f(255,c.g-e(255*-(b/100)))),c.b=g(0,f(255,c.b-e(255*-(b/100)))),i(c)}function w(a,b){b=0===b?0:b||10;var c=i(a).toHsl();return c.l-=b/100,c.l=J(c.l),i(c)}function x(a,b){var c=i(a).toHsl(),d=(e(c.h)+b)%360;return c.h=d<0?360+d:d,i(c)}function y(a){var b=i(a).toHsl();return b.h=(b.h+180)%360,i(b)}function z(a){var b=i(a).toHsl(),c=b.h;return[i(a),i({h:(c+120)%360,s:b.s,l:b.l}),i({h:(c+240)%360,s:b.s,l:b.l})]}function A(a){var b=i(a).toHsl(),c=b.h;return[i(a),i({h:(c+90)%360,s:b.s,l:b.l}),i({h:(c+180)%360,s:b.s,l:b.l}),i({h:(c+270)%360,s:b.s,l:b.l})]}function B(a){var b=i(a).toHsl(),c=b.h;return[i(a),i({h:(c+72)%360,s:b.s,l:b.l}),i({h:(c+216)%360,s:b.s,l:b.l})]}function C(a,b,c){b=b||6,c=c||30;var d=i(a).toHsl(),e=360/c,f=[i(a)];for(d.h=(d.h-(e*b>>1)+720)%360;--b;)d.h=(d.h+e)%360,f.push(i(d));return f}function D(a,b){b=b||6;for(var c=i(a).toHsv(),d=c.h,e=c.s,f=c.v,g=[],h=1/b;b--;)g.push(i({h:d,s:e,v:f})),f=(f+h)%1;return g}function G(a){var b={};for(var c in a)a.hasOwnProperty(c)&&(b[a[c]]=c);return b}function H(a){return a=parseFloat(a),(isNaN(a)||a<0||a>1)&&(a=1),a}function I(a,b){L(a)&&(a="100%");var c=M(a);return a=f(b,g(0,parseFloat(a))),c&&(a=parseInt(a*b,10)/100),d.abs(a-b)<1e-6?1:a%b/parseFloat(b)}function J(a){return f(1,g(0,a))}function K(a){return parseInt(a,16)}function L(a){return"string"==typeof a&&a.indexOf(".")!=-1&&1===parseFloat(a)}function M(a){return"string"==typeof a&&a.indexOf("%")!=-1}function N(a){return 1==a.length?"0"+a:""+a}function O(a){return a<=1&&(a=100*a+"%"),a}function P(a){return Math.round(255*parseFloat(a)).toString(16)}function Q(a){return K(a)/255}function S(c){c=c.replace(a,"").replace(b,"").toLowerCase();var d=!1;if(E[c])c=E[c],d=!0;else if("transparent"==c)return{r:0,g:0,b:0,a:0,format:"name"};var e;return(e=R.rgb.exec(c))?{r:e[1],g:e[2],b:e[3]}:(e=R.rgba.exec(c))?{r:e[1],g:e[2],b:e[3],a:e[4]}:(e=R.hsl.exec(c))?{h:e[1],s:e[2],l:e[3]}:(e=R.hsla.exec(c))?{h:e[1],s:e[2],l:e[3],a:e[4]}:(e=R.hsv.exec(c))?{h:e[1],s:e[2],v:e[3]}:(e=R.hsva.exec(c))?{h:e[1],s:e[2],v:e[3],a:e[4]}:(e=R.hex8.exec(c))?{a:Q(e[1]),r:K(e[2]),g:K(e[3]),b:K(e[4]),format:d?"name":"hex8"}:(e=R.hex6.exec(c))?{r:K(e[1]),g:K(e[2]),b:K(e[3]),format:d?"name":"hex"}:!!(e=R.hex3.exec(c))&&{r:K(e[1]+""+e[1]),g:K(e[2]+""+e[2]),b:K(e[3]+""+e[3]),format:d?"name":"hex"}}var a=/^[\s,#]+/,b=/\s+$/,c=0,d=Math,e=d.round,f=d.min,g=d.max,h=d.random,i=function(a,b){if(a=a?a:"",b=b||{},a instanceof i)return a;if(!(this instanceof i))return new i(a,b);var d=j(a);this._originalInput=a,this._r=d.r,this._g=d.g,this._b=d.b,this._a=d.a,this._roundA=e(100*this._a)/100,this._format=b.format||d.format,this._gradientType=b.gradientType,this._r<1&&(this._r=e(this._r)),this._g<1&&(this._g=e(this._g)),this._b<1&&(this._b=e(this._b)),this._ok=d.ok,this._tc_id=c++};i.prototype={isDark:function(){return this.getBrightness()<128},isLight:function(){return!this.isDark()},isValid:function(){return this._ok},getOriginalInput:function(){return this._originalInput},getFormat:function(){return this._format},getAlpha:function(){return this._a},getBrightness:function(){var a=this.toRgb();return(299*a.r+587*a.g+114*a.b)/1e3},setAlpha:function(a){return this._a=H(a),this._roundA=e(100*this._a)/100,this},toHsv:function(){var a=n(this._r,this._g,this._b);return{h:360*a.h,s:a.s,v:a.v,a:this._a}},toHsvString:function(){var a=n(this._r,this._g,this._b),b=e(360*a.h),c=e(100*a.s),d=e(100*a.v);return 1==this._a?"hsv("+b+", "+c+"%, "+d+"%)":"hsva("+b+", "+c+"%, "+d+"%, "+this._roundA+")"},toHsl:function(){var a=l(this._r,this._g,this._b);return{h:360*a.h,s:a.s,l:a.l,a:this._a}},toHslString:function(){var a=l(this._r,this._g,this._b),b=e(360*a.h),c=e(100*a.s),d=e(100*a.l);return 1==this._a?"hsl("+b+", "+c+"%, "+d+"%)":"hsla("+b+", "+c+"%, "+d+"%, "+this._roundA+")"},toHex:function(a){return p(this._r,this._g,this._b,a)},toHexString:function(a){return"#"+this.toHex(a)},toHex8:function(){return q(this._r,this._g,this._b,this._a)},toHex8String:function(){return"#"+this.toHex8()},toRgb:function(){return{r:e(this._r),g:e(this._g),b:e(this._b),a:this._a}},toRgbString:function(){return 1==this._a?"rgb("+e(this._r)+", "+e(this._g)+", "+e(this._b)+")":"rgba("+e(this._r)+", "+e(this._g)+", "+e(this._b)+", "+this._roundA+")"},toPercentageRgb:function(){return{r:e(100*I(this._r,255))+"%",g:e(100*I(this._g,255))+"%",b:e(100*I(this._b,255))+"%",a:this._a}},toPercentageRgbString:function(){return 1==this._a?"rgb("+e(100*I(this._r,255))+"%, "+e(100*I(this._g,255))+"%, "+e(100*I(this._b,255))+"%)":"rgba("+e(100*I(this._r,255))+"%, "+e(100*I(this._g,255))+"%, "+e(100*I(this._b,255))+"%, "+this._roundA+")"},toName:function(){return 0===this._a?"transparent":!(this._a<1)&&(F[p(this._r,this._g,this._b,!0)]||!1)},toFilter:function(a){var b="#"+q(this._r,this._g,this._b,this._a),c=b,d=this._gradientType?"GradientType = 1, ":"";if(a){var e=i(a);c=e.toHex8String()}return"progid:DXImageTransform.Microsoft.gradient("+d+"startColorstr="+b+",endColorstr="+c+")"},toString:function(a){var b=!!a;a=a||this._format;var c=!1,d=this._a<1&&this._a>=0,e=!b&&d&&("hex"===a||"hex6"===a||"hex3"===a||"name"===a);return e?"name"===a&&0===this._a?this.toName():this.toRgbString():("rgb"===a&&(c=this.toRgbString()),"prgb"===a&&(c=this.toPercentageRgbString()),"hex"!==a&&"hex6"!==a||(c=this.toHexString()),"hex3"===a&&(c=this.toHexString(!0)),"hex8"===a&&(c=this.toHex8String()),"name"===a&&(c=this.toName()),"hsl"===a&&(c=this.toHslString()),"hsv"===a&&(c=this.toHsvString()),c||this.toHexString())},_applyModification:function(a,b){var c=a.apply(null,[this].concat([].slice.call(b)));return this._r=c._r,this._g=c._g,this._b=c._b,this.setAlpha(c._a),this},lighten:function(){return this._applyModification(u,arguments)},brighten:function(){return this._applyModification(v,arguments)},darken:function(){return this._applyModification(w,arguments)},desaturate:function(){return this._applyModification(r,arguments)},saturate:function(){return this._applyModification(s,arguments)},greyscale:function(){return this._applyModification(t,arguments)},spin:function(){return this._applyModification(x,arguments)},_applyCombination:function(a,b){return a.apply(null,[this].concat([].slice.call(b)))},analogous:function(){return this._applyCombination(C,arguments)},complement:function(){return this._applyCombination(y,arguments)},monochromatic:function(){return this._applyCombination(D,arguments)},splitcomplement:function(){return this._applyCombination(B,arguments)},triad:function(){return this._applyCombination(z,arguments)},tetrad:function(){return this._applyCombination(A,arguments)}},i.fromRatio=function(a,b){if("object"==typeof a){var c={};for(var d in a)a.hasOwnProperty(d)&&("a"===d?c[d]=a[d]:c[d]=O(a[d]));a=c}return i(a,b)},i.equals=function(a,b){return!(!a||!b)&&i(a).toRgbString()==i(b).toRgbString()},i.random=function(){return i.fromRatio({r:h(),g:h(),b:h()})},i.mix=function(a,b,c){c=0===c?0:c||50;var j,d=i(a).toRgb(),e=i(b).toRgb(),f=c/100,g=2*f-1,h=e.a-d.a;j=g*h==-1?g:(g+h)/(1+g*h),j=(j+1)/2;var k=1-j,l={r:e.r*j+d.r*k,g:e.g*j+d.g*k,b:e.b*j+d.b*k,a:e.a*f+d.a*(1-f)};return i(l)},i.readability=function(a,b){var c=i(a),d=i(b),e=c.toRgb(),f=d.toRgb(),g=c.getBrightness(),h=d.getBrightness(),j=Math.max(e.r,f.r)-Math.min(e.r,f.r)+Math.max(e.g,f.g)-Math.min(e.g,f.g)+Math.max(e.b,f.b)-Math.min(e.b,f.b);return{brightness:Math.abs(g-h),color:j}},i.isReadable=function(a,b){var c=i.readability(a,b);return c.brightness>125&&c.color>500},i.mostReadable=function(a,b){for(var c=null,d=0,e=!1,f=0;f<b.length;f++){var g=i.readability(a,b[f]),h=g.brightness>125&&g.color>500,j=3*(g.brightness/125)+g.color/500;(h&&!e||h&&e&&j>d||!h&&!e&&j>d)&&(e=h,d=j,c=i(b[f]))}return c};var E=i.names={aliceblue:"f0f8ff",antiquewhite:"faebd7",aqua:"0ff",aquamarine:"7fffd4",azure:"f0ffff",beige:"f5f5dc",bisque:"ffe4c4",black:"000",blanchedalmond:"ffebcd",blue:"00f",blueviolet:"8a2be2",brown:"a52a2a",burlywood:"deb887",burntsienna:"ea7e5d",cadetblue:"5f9ea0",chartreuse:"7fff00",chocolate:"d2691e",coral:"ff7f50",cornflowerblue:"6495ed",cornsilk:"fff8dc",crimson:"dc143c",cyan:"0ff",darkblue:"00008b",darkcyan:"008b8b",darkgoldenrod:"b8860b",darkgray:"a9a9a9",darkgreen:"006400",darkgrey:"a9a9a9",darkkhaki:"bdb76b",darkmagenta:"8b008b",darkolivegreen:"556b2f",darkorange:"ff8c00",darkorchid:"9932cc",darkred:"8b0000",darksalmon:"e9967a",darkseagreen:"8fbc8f",darkslateblue:"483d8b",darkslategray:"2f4f4f",darkslategrey:"2f4f4f",darkturquoise:"00ced1",darkviolet:"9400d3",deeppink:"ff1493",deepskyblue:"00bfff",dimgray:"696969",dimgrey:"696969",dodgerblue:"1e90ff",firebrick:"b22222",floralwhite:"fffaf0",forestgreen:"228b22",fuchsia:"f0f",gainsboro:"dcdcdc",ghostwhite:"f8f8ff",gold:"ffd700",goldenrod:"daa520",gray:"808080",green:"008000",greenyellow:"adff2f",grey:"808080",honeydew:"f0fff0",hotpink:"ff69b4",indianred:"cd5c5c",indigo:"4b0082",ivory:"fffff0",khaki:"f0e68c",lavender:"e6e6fa",lavenderblush:"fff0f5",lawngreen:"7cfc00",lemonchiffon:"fffacd",lightblue:"add8e6",lightcoral:"f08080",lightcyan:"e0ffff",lightgoldenrodyellow:"fafad2",lightgray:"d3d3d3",lightgreen:"90ee90",lightgrey:"d3d3d3",lightpink:"ffb6c1",lightsalmon:"ffa07a",lightseagreen:"20b2aa",lightskyblue:"87cefa",lightslategray:"789",lightslategrey:"789",lightsteelblue:"b0c4de",lightyellow:"ffffe0",lime:"0f0",limegreen:"32cd32",linen:"faf0e6",magenta:"f0f",maroon:"800000",mediumaquamarine:"66cdaa",mediumblue:"0000cd",mediumorchid:"ba55d3",mediumpurple:"9370db",mediumseagreen:"3cb371",mediumslateblue:"7b68ee",mediumspringgreen:"00fa9a",mediumturquoise:"48d1cc",mediumvioletred:"c71585",midnightblue:"191970",mintcream:"f5fffa",mistyrose:"ffe4e1",moccasin:"ffe4b5",navajowhite:"ffdead",navy:"000080",oldlace:"fdf5e6",olive:"808000",olivedrab:"6b8e23",orange:"ffa500",orangered:"ff4500",orchid:"da70d6",palegoldenrod:"eee8aa",palegreen:"98fb98",paleturquoise:"afeeee",palevioletred:"db7093",papayawhip:"ffefd5",peachpuff:"ffdab9",peru:"cd853f",pink:"ffc0cb",plum:"dda0dd",powderblue:"b0e0e6",purple:"800080",rebeccapurple:"663399",red:"f00",rosybrown:"bc8f8f",royalblue:"4169e1",saddlebrown:"8b4513",salmon:"fa8072",sandybrown:"f4a460",seagreen:"2e8b57",seashell:"fff5ee",sienna:"a0522d",silver:"c0c0c0",skyblue:"87ceeb",slateblue:"6a5acd",slategray:"708090",slategrey:"708090",snow:"fffafa",springgreen:"00ff7f",steelblue:"4682b4",tan:"d2b48c",teal:"008080",thistle:"d8bfd8",tomato:"ff6347",turquoise:"40e0d0",violet:"ee82ee",wheat:"f5deb3",white:"fff",whitesmoke:"f5f5f5",yellow:"ff0",yellowgreen:"9acd32"},F=i.hexNames=G(E),R=function(){var a="[-\\+]?\\d+%?",b="[-\\+]?\\d*\\.\\d+%?",c="(?:"+b+")|(?:"+a+")",d="[\\s|\\(]+("+c+")[,|\\s]+("+c+")[,|\\s]+("+c+")\\s*\\)?",e="[\\s|\\(]+("+c+")[,|\\s]+("+c+")[,|\\s]+("+c+")[,|\\s]+("+c+")\\s*\\)?";return{rgb:new RegExp("rgb"+d),rgba:new RegExp("rgba"+e),hsl:new RegExp("hsl"+d),hsla:new RegExp("hsla"+e),hsv:new RegExp("hsv"+d),hsva:new RegExp("hsva"+e),hex3:/^([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,hex6:/^([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/,hex8:/^([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/}}();window.tinycolor=i}(),a(function(){a.fn.spectrum.load&&a.fn.spectrum.processNativeColorInputs()})});

// PLUGIN AIR DATEPICKER
!function(t,e,i){!function(){var s,a,n,h="2.2.3",o="datepicker",r=".datepicker-here",c=!1,d='<div class="datepicker"><i class="datepicker--pointer"></i><nav class="datepicker--nav"></nav><div class="datepicker--content"></div></div>',l={classes:"",inline:!1,language:"ru",startDate:new Date,firstDay:"",weekends:[6,0],dateFormat:"",altField:"",altFieldDateFormat:"@",toggleSelected:!0,keyboardNav:!0,position:"bottom left",offset:12,view:"days",minView:"days",showOtherMonths:!0,selectOtherMonths:!0,moveToOtherMonthsOnSelect:!0,showOtherYears:!0,selectOtherYears:!0,moveToOtherYearsOnSelect:!0,minDate:"",maxDate:"",disableNavWhenOutOfRange:!0,multipleDates:!1,multipleDatesSeparator:",",range:!1,todayButton:!1,clearButton:!1,showEvent:"focus",autoClose:!1,monthsField:"monthsShort",prevHtml:'<svg><path d="M 17,12 l -5,5 l 5,5"></path></svg>',nextHtml:'<svg><path d="M 14,12 l 5,5 l -5,5"></path></svg>',navTitles:{days:"MM, <i>yyyy</i>",months:"yyyy",years:"yyyy1 - yyyy2"},timepicker:!1,onlyTimepicker:!1,dateTimeSeparator:" ",timeFormat:"",minHours:0,maxHours:24,minMinutes:0,maxMinutes:59,hoursStep:1,minutesStep:1,onSelect:"",onShow:"",onHide:"",onChangeMonth:"",onChangeYear:"",onChangeDecade:"",onChangeView:"",onRenderCell:""},u={ctrlRight:[17,39],ctrlUp:[17,38],ctrlLeft:[17,37],ctrlDown:[17,40],shiftRight:[16,39],shiftUp:[16,38],shiftLeft:[16,37],shiftDown:[16,40],altUp:[18,38],altRight:[18,39],altLeft:[18,37],altDown:[18,40],ctrlShiftUp:[16,17,38]},m=function(t,a){this.el=t,this.$el=e(t),this.opts=e.extend(!0,{},l,a,this.$el.data()),s==i&&(s=e("body")),this.opts.startDate||(this.opts.startDate=new Date),"INPUT"==this.el.nodeName&&(this.elIsInput=!0),this.opts.altField&&(this.$altField="string"==typeof this.opts.altField?e(this.opts.altField):this.opts.altField),this.inited=!1,this.visible=!1,this.silent=!1,this.currentDate=this.opts.startDate,this.currentView=this.opts.view,this._createShortCuts(),this.selectedDates=[],this.views={},this.keys=[],this.minRange="",this.maxRange="",this._prevOnSelectValue="",this.init()};n=m,n.prototype={VERSION:h,viewIndexes:["days","months","years"],init:function(){c||this.opts.inline||!this.elIsInput||this._buildDatepickersContainer(),this._buildBaseHtml(),this._defineLocale(this.opts.language),this._syncWithMinMaxDates(),this.elIsInput&&(this.opts.inline||(this._setPositionClasses(this.opts.position),this._bindEvents()),this.opts.keyboardNav&&!this.opts.onlyTimepicker&&this._bindKeyboardEvents(),this.$datepicker.on("mousedown",this._onMouseDownDatepicker.bind(this)),this.$datepicker.on("mouseup",this._onMouseUpDatepicker.bind(this))),this.opts.classes&&this.$datepicker.addClass(this.opts.classes),this.opts.timepicker&&(this.timepicker=new e.fn.datepicker.Timepicker(this,this.opts),this._bindTimepickerEvents()),this.opts.onlyTimepicker&&this.$datepicker.addClass("-only-timepicker-"),this.views[this.currentView]=new e.fn.datepicker.Body(this,this.currentView,this.opts),this.views[this.currentView].show(),this.nav=new e.fn.datepicker.Navigation(this,this.opts),this.view=this.currentView,this.$el.on("clickCell.adp",this._onClickCell.bind(this)),this.$datepicker.on("mouseenter",".datepicker--cell",this._onMouseEnterCell.bind(this)),this.$datepicker.on("mouseleave",".datepicker--cell",this._onMouseLeaveCell.bind(this)),this.inited=!0},_createShortCuts:function(){this.minDate=this.opts.minDate?this.opts.minDate:new Date(-86399999136e5),this.maxDate=this.opts.maxDate?this.opts.maxDate:new Date(86399999136e5)},_bindEvents:function(){this.$el.on(this.opts.showEvent+".adp",this._onShowEvent.bind(this)),this.$el.on("mouseup.adp",this._onMouseUpEl.bind(this)),this.$el.on("blur.adp",this._onBlur.bind(this)),this.$el.on("keyup.adp",this._onKeyUpGeneral.bind(this)),e(t).on("resize.adp",this._onResize.bind(this)),e("body").on("mouseup.adp",this._onMouseUpBody.bind(this))},_bindKeyboardEvents:function(){this.$el.on("keydown.adp",this._onKeyDown.bind(this)),this.$el.on("keyup.adp",this._onKeyUp.bind(this)),this.$el.on("hotKey.adp",this._onHotKey.bind(this))},_bindTimepickerEvents:function(){this.$el.on("timeChange.adp",this._onTimeChange.bind(this))},isWeekend:function(t){return-1!==this.opts.weekends.indexOf(t)},_defineLocale:function(t){"string"==typeof t?(this.loc=e.fn.datepicker.language[t],this.loc||(console.warn("Can't find language \""+t+'" in Datepicker.language, will use "ru" instead'),this.loc=e.extend(!0,{},e.fn.datepicker.language.ru)),this.loc=e.extend(!0,{},e.fn.datepicker.language.ru,e.fn.datepicker.language[t])):this.loc=e.extend(!0,{},e.fn.datepicker.language.ru,t),this.opts.dateFormat&&(this.loc.dateFormat=this.opts.dateFormat),this.opts.timeFormat&&(this.loc.timeFormat=this.opts.timeFormat),""!==this.opts.firstDay&&(this.loc.firstDay=this.opts.firstDay),this.opts.timepicker&&(this.loc.dateFormat=[this.loc.dateFormat,this.loc.timeFormat].join(this.opts.dateTimeSeparator)),this.opts.onlyTimepicker&&(this.loc.dateFormat=this.loc.timeFormat);var i=this._getWordBoundaryRegExp;(this.loc.timeFormat.match(i("aa"))||this.loc.timeFormat.match(i("AA")))&&(this.ampm=!0)},_buildDatepickersContainer:function(){c=!0,s.append('<div class="datepickers-container" id="datepickers-container"></div>'),a=e("#datepickers-container")},_buildBaseHtml:function(){var t,i=e('<div class="datepicker-inline">');t="INPUT"==this.el.nodeName?this.opts.inline?i.insertAfter(this.$el):a:i.appendTo(this.$el),this.$datepicker=e(d).appendTo(t),this.$content=e(".datepicker--content",this.$datepicker),this.$nav=e(".datepicker--nav",this.$datepicker)},_triggerOnChange:function(){if(!this.selectedDates.length){if(""===this._prevOnSelectValue)return;return this._prevOnSelectValue="",this.opts.onSelect("","",this)}var t,e=this.selectedDates,i=n.getParsedDate(e[0]),s=this,a=new Date(i.year,i.month,i.date,i.hours,i.minutes);t=e.map(function(t){return s.formatDate(s.loc.dateFormat,t)}).join(this.opts.multipleDatesSeparator),(this.opts.multipleDates||this.opts.range)&&(a=e.map(function(t){var e=n.getParsedDate(t);return new Date(e.year,e.month,e.date,e.hours,e.minutes)})),this._prevOnSelectValue=t,this.opts.onSelect(t,a,this)},next:function(){var t=this.parsedDate,e=this.opts;switch(this.view){case"days":this.date=new Date(t.year,t.month+1,1),e.onChangeMonth&&e.onChangeMonth(this.parsedDate.month,this.parsedDate.year);break;case"months":this.date=new Date(t.year+1,t.month,1),e.onChangeYear&&e.onChangeYear(this.parsedDate.year);break;case"years":this.date=new Date(t.year+10,0,1),e.onChangeDecade&&e.onChangeDecade(this.curDecade)}},prev:function(){var t=this.parsedDate,e=this.opts;switch(this.view){case"days":this.date=new Date(t.year,t.month-1,1),e.onChangeMonth&&e.onChangeMonth(this.parsedDate.month,this.parsedDate.year);break;case"months":this.date=new Date(t.year-1,t.month,1),e.onChangeYear&&e.onChangeYear(this.parsedDate.year);break;case"years":this.date=new Date(t.year-10,0,1),e.onChangeDecade&&e.onChangeDecade(this.curDecade)}},formatDate:function(t,e){e=e||this.date;var i,s=t,a=this._getWordBoundaryRegExp,h=this.loc,o=n.getLeadingZeroNum,r=n.getDecade(e),c=n.getParsedDate(e),d=c.fullHours,l=c.hours,u=t.match(a("aa"))||t.match(a("AA")),m="am",p=this._replacer;switch(this.opts.timepicker&&this.timepicker&&u&&(i=this.timepicker._getValidHoursFromDate(e,u),d=o(i.hours),l=i.hours,m=i.dayPeriod),!0){case/@/.test(s):s=s.replace(/@/,e.getTime());case/aa/.test(s):s=p(s,a("aa"),m);case/AA/.test(s):s=p(s,a("AA"),m.toUpperCase());case/dd/.test(s):s=p(s,a("dd"),c.fullDate);case/d/.test(s):s=p(s,a("d"),c.date);case/DD/.test(s):s=p(s,a("DD"),h.days[c.day]);case/D/.test(s):s=p(s,a("D"),h.daysShort[c.day]);case/mm/.test(s):s=p(s,a("mm"),c.fullMonth);case/m/.test(s):s=p(s,a("m"),c.month+1);case/MM/.test(s):s=p(s,a("MM"),this.loc.months[c.month]);case/M/.test(s):s=p(s,a("M"),h.monthsShort[c.month]);case/ii/.test(s):s=p(s,a("ii"),c.fullMinutes);case/i/.test(s):s=p(s,a("i"),c.minutes);case/hh/.test(s):s=p(s,a("hh"),d);case/h/.test(s):s=p(s,a("h"),l);case/yyyy/.test(s):s=p(s,a("yyyy"),c.year);case/yyyy1/.test(s):s=p(s,a("yyyy1"),r[0]);case/yyyy2/.test(s):s=p(s,a("yyyy2"),r[1]);case/yy/.test(s):s=p(s,a("yy"),c.year.toString().slice(-2))}return s},_replacer:function(t,e,i){return t.replace(e,function(t,e,s,a){return e+i+a})},_getWordBoundaryRegExp:function(t){var e="\\s|\\.|-|/|\\\\|,|\\$|\\!|\\?|:|;";return new RegExp("(^|>|"+e+")("+t+")($|<|"+e+")","g")},selectDate:function(t){var e=this,i=e.opts,s=e.parsedDate,a=e.selectedDates,h=a.length,o="";if(Array.isArray(t))return void t.forEach(function(t){e.selectDate(t)});if(t instanceof Date){if(this.lastSelectedDate=t,this.timepicker&&this.timepicker._setTime(t),e._trigger("selectDate",t),this.timepicker&&(t.setHours(this.timepicker.hours),t.setMinutes(this.timepicker.minutes)),"days"==e.view&&t.getMonth()!=s.month&&i.moveToOtherMonthsOnSelect&&(o=new Date(t.getFullYear(),t.getMonth(),1)),"years"==e.view&&t.getFullYear()!=s.year&&i.moveToOtherYearsOnSelect&&(o=new Date(t.getFullYear(),0,1)),o&&(e.silent=!0,e.date=o,e.silent=!1,e.nav._render()),i.multipleDates&&!i.range){if(h===i.multipleDates)return;e._isSelected(t)||e.selectedDates.push(t)}else i.range?2==h?(e.selectedDates=[t],e.minRange=t,e.maxRange=""):1==h?(e.selectedDates.push(t),e.maxRange?e.minRange=t:e.maxRange=t,n.bigger(e.maxRange,e.minRange)&&(e.maxRange=e.minRange,e.minRange=t),e.selectedDates=[e.minRange,e.maxRange]):(e.selectedDates=[t],e.minRange=t):e.selectedDates=[t];e._setInputValue(),i.onSelect&&e._triggerOnChange(),i.autoClose&&!this.timepickerIsActive&&(i.multipleDates||i.range?i.range&&2==e.selectedDates.length&&e.hide():e.hide()),e.views[this.currentView]._render()}},removeDate:function(t){var e=this.selectedDates,i=this;if(t instanceof Date)return e.some(function(s,a){return n.isSame(s,t)?(e.splice(a,1),i.selectedDates.length?i.lastSelectedDate=i.selectedDates[i.selectedDates.length-1]:(i.minRange="",i.maxRange="",i.lastSelectedDate=""),i.views[i.currentView]._render(),i._setInputValue(),i.opts.onSelect&&i._triggerOnChange(),!0):void 0})},today:function(){this.silent=!0,this.view=this.opts.minView,this.silent=!1,this.date=new Date,this.opts.todayButton instanceof Date&&this.selectDate(this.opts.todayButton)},clear:function(){this.selectedDates=[],this.minRange="",this.maxRange="",this.views[this.currentView]._render(),this._setInputValue(),this.opts.onSelect&&this._triggerOnChange()},update:function(t,i){var s=arguments.length,a=this.lastSelectedDate;return 2==s?this.opts[t]=i:1==s&&"object"==typeof t&&(this.opts=e.extend(!0,this.opts,t)),this._createShortCuts(),this._syncWithMinMaxDates(),this._defineLocale(this.opts.language),this.nav._addButtonsIfNeed(),this.opts.onlyTimepicker||this.nav._render(),this.views[this.currentView]._render(),this.elIsInput&&!this.opts.inline&&(this._setPositionClasses(this.opts.position),this.visible&&this.setPosition(this.opts.position)),this.opts.classes&&this.$datepicker.addClass(this.opts.classes),this.opts.onlyTimepicker&&this.$datepicker.addClass("-only-timepicker-"),this.opts.timepicker&&(a&&this.timepicker._handleDate(a),this.timepicker._updateRanges(),this.timepicker._updateCurrentTime(),a&&(a.setHours(this.timepicker.hours),a.setMinutes(this.timepicker.minutes))),this._setInputValue(),this},_syncWithMinMaxDates:function(){var t=this.date.getTime();this.silent=!0,this.minTime>t&&(this.date=this.minDate),this.maxTime<t&&(this.date=this.maxDate),this.silent=!1},_isSelected:function(t,e){var i=!1;return this.selectedDates.some(function(s){return n.isSame(s,t,e)?(i=s,!0):void 0}),i},_setInputValue:function(){var t,e=this,i=e.opts,s=e.loc.dateFormat,a=i.altFieldDateFormat,n=e.selectedDates.map(function(t){return e.formatDate(s,t)});i.altField&&e.$altField.length&&(t=this.selectedDates.map(function(t){return e.formatDate(a,t)}),t=t.join(this.opts.multipleDatesSeparator),this.$altField.val(t)),n=n.join(this.opts.multipleDatesSeparator),this.$el.val(n)},_isInRange:function(t,e){var i=t.getTime(),s=n.getParsedDate(t),a=n.getParsedDate(this.minDate),h=n.getParsedDate(this.maxDate),o=new Date(s.year,s.month,a.date).getTime(),r=new Date(s.year,s.month,h.date).getTime(),c={day:i>=this.minTime&&i<=this.maxTime,month:o>=this.minTime&&r<=this.maxTime,year:s.year>=a.year&&s.year<=h.year};return e?c[e]:c.day},_getDimensions:function(t){var e=t.offset();return{width:t.outerWidth(),height:t.outerHeight(),left:e.left,top:e.top}},_getDateFromCell:function(t){var e=this.parsedDate,s=t.data("year")||e.year,a=t.data("month")==i?e.month:t.data("month"),n=t.data("date")||1;return new Date(s,a,n)},_setPositionClasses:function(t){t=t.split(" ");var e=t[0],i=t[1],s="datepicker -"+e+"-"+i+"- -from-"+e+"-";this.visible&&(s+=" active"),this.$datepicker.removeAttr("class").addClass(s)},setPosition:function(t){t=t||this.opts.position;var e,i,s=this._getDimensions(this.$el),a=this._getDimensions(this.$datepicker),n=t.split(" "),h=this.opts.offset,o=n[0],r=n[1];switch(o){case"top":e=s.top-a.height-h;break;case"right":i=s.left+s.width+h;break;case"bottom":e=s.top+s.height+h;break;case"left":i=s.left-a.width-h}switch(r){case"top":e=s.top;break;case"right":i=s.left+s.width-a.width;break;case"bottom":e=s.top+s.height-a.height;break;case"left":i=s.left;break;case"center":/left|right/.test(o)?e=s.top+s.height/2-a.height/2:i=s.left+s.width/2-a.width/2}this.$datepicker.css({left:i,top:e})},show:function(){var t=this.opts.onShow;this.setPosition(this.opts.position),this.$datepicker.addClass("active"),this.visible=!0,t&&this._bindVisionEvents(t)},hide:function(){var t=this.opts.onHide;this.$datepicker.removeClass("active").css({left:"-100000px"}),this.focused="",this.keys=[],this.inFocus=!1,this.visible=!1,this.$el.blur(),t&&this._bindVisionEvents(t)},down:function(t){this._changeView(t,"down")},up:function(t){this._changeView(t,"up")},_bindVisionEvents:function(t){this.$datepicker.off("transitionend.dp"),t(this,!1),this.$datepicker.one("transitionend.dp",t.bind(this,this,!0))},_changeView:function(t,e){t=t||this.focused||this.date;var i="up"==e?this.viewIndex+1:this.viewIndex-1;i>2&&(i=2),0>i&&(i=0),this.silent=!0,this.date=new Date(t.getFullYear(),t.getMonth(),1),this.silent=!1,this.view=this.viewIndexes[i]},_handleHotKey:function(t){var e,i,s,a=n.getParsedDate(this._getFocusedDate()),h=this.opts,o=!1,r=!1,c=!1,d=a.year,l=a.month,u=a.date;switch(t){case"ctrlRight":case"ctrlUp":l+=1,o=!0;break;case"ctrlLeft":case"ctrlDown":l-=1,o=!0;break;case"shiftRight":case"shiftUp":r=!0,d+=1;break;case"shiftLeft":case"shiftDown":r=!0,d-=1;break;case"altRight":case"altUp":c=!0,d+=10;break;case"altLeft":case"altDown":c=!0,d-=10;break;case"ctrlShiftUp":this.up()}s=n.getDaysCount(new Date(d,l)),i=new Date(d,l,u),u>s&&(u=s),i.getTime()<this.minTime?i=this.minDate:i.getTime()>this.maxTime&&(i=this.maxDate),this.focused=i,e=n.getParsedDate(i),o&&h.onChangeMonth&&h.onChangeMonth(e.month,e.year),r&&h.onChangeYear&&h.onChangeYear(e.year),c&&h.onChangeDecade&&h.onChangeDecade(this.curDecade)},_registerKey:function(t){var e=this.keys.some(function(e){return e==t});e||this.keys.push(t)},_unRegisterKey:function(t){var e=this.keys.indexOf(t);this.keys.splice(e,1)},_isHotKeyPressed:function(){var t,e=!1,i=this,s=this.keys.sort();for(var a in u)t=u[a],s.length==t.length&&t.every(function(t,e){return t==s[e]})&&(i._trigger("hotKey",a),e=!0);return e},_trigger:function(t,e){this.$el.trigger(t,e)},_focusNextCell:function(t,e){e=e||this.cellType;var i=n.getParsedDate(this._getFocusedDate()),s=i.year,a=i.month,h=i.date;if(!this._isHotKeyPressed()){switch(t){case 37:"day"==e?h-=1:"","month"==e?a-=1:"","year"==e?s-=1:"";break;case 38:"day"==e?h-=7:"","month"==e?a-=3:"","year"==e?s-=4:"";break;case 39:"day"==e?h+=1:"","month"==e?a+=1:"","year"==e?s+=1:"";break;case 40:"day"==e?h+=7:"","month"==e?a+=3:"","year"==e?s+=4:""}var o=new Date(s,a,h);o.getTime()<this.minTime?o=this.minDate:o.getTime()>this.maxTime&&(o=this.maxDate),this.focused=o}},_getFocusedDate:function(){var t=this.focused||this.selectedDates[this.selectedDates.length-1],e=this.parsedDate;if(!t)switch(this.view){case"days":t=new Date(e.year,e.month,(new Date).getDate());break;case"months":t=new Date(e.year,e.month,1);break;case"years":t=new Date(e.year,0,1)}return t},_getCell:function(t,i){i=i||this.cellType;var s,a=n.getParsedDate(t),h='.datepicker--cell[data-year="'+a.year+'"]';switch(i){case"month":h='[data-month="'+a.month+'"]';break;case"day":h+='[data-month="'+a.month+'"][data-date="'+a.date+'"]'}return s=this.views[this.currentView].$el.find(h),s.length?s:e("")},destroy:function(){var t=this;t.$el.off(".adp").data("datepicker",""),t.selectedDates=[],t.focused="",t.views={},t.keys=[],t.minRange="",t.maxRange="",t.opts.inline||!t.elIsInput?t.$datepicker.closest(".datepicker-inline").remove():t.$datepicker.remove()},_handleAlreadySelectedDates:function(t,e){this.opts.range?this.opts.toggleSelected?this.removeDate(e):2!=this.selectedDates.length&&this._trigger("clickCell",e):this.opts.toggleSelected&&this.removeDate(e),this.opts.toggleSelected||(this.lastSelectedDate=t,this.opts.timepicker&&(this.timepicker._setTime(t),this.timepicker.update()))},_onShowEvent:function(t){this.visible||this.show()},_onBlur:function(){!this.inFocus&&this.visible&&this.hide()},_onMouseDownDatepicker:function(t){this.inFocus=!0},_onMouseUpDatepicker:function(t){this.inFocus=!1,t.originalEvent.inFocus=!0,t.originalEvent.timepickerFocus||this.$el.focus()},_onKeyUpGeneral:function(t){var e=this.$el.val();e||this.clear()},_onResize:function(){this.visible&&this.setPosition()},_onMouseUpBody:function(t){t.originalEvent.inFocus||this.visible&&!this.inFocus&&this.hide()},_onMouseUpEl:function(t){t.originalEvent.inFocus=!0,setTimeout(this._onKeyUpGeneral.bind(this),4)},_onKeyDown:function(t){var e=t.which;if(this._registerKey(e),e>=37&&40>=e&&(t.preventDefault(),this._focusNextCell(e)),13==e&&this.focused){if(this._getCell(this.focused).hasClass("-disabled-"))return;if(this.view!=this.opts.minView)this.down();else{var i=this._isSelected(this.focused,this.cellType);if(!i)return this.timepicker&&(this.focused.setHours(this.timepicker.hours),this.focused.setMinutes(this.timepicker.minutes)),void this.selectDate(this.focused);this._handleAlreadySelectedDates(i,this.focused)}}27==e&&this.hide()},_onKeyUp:function(t){var e=t.which;this._unRegisterKey(e)},_onHotKey:function(t,e){this._handleHotKey(e)},_onMouseEnterCell:function(t){var i=e(t.target).closest(".datepicker--cell"),s=this._getDateFromCell(i);this.silent=!0,this.focused&&(this.focused=""),i.addClass("-focus-"),this.focused=s,this.silent=!1,this.opts.range&&1==this.selectedDates.length&&(this.minRange=this.selectedDates[0],this.maxRange="",n.less(this.minRange,this.focused)&&(this.maxRange=this.minRange,this.minRange=""),this.views[this.currentView]._update())},_onMouseLeaveCell:function(t){var i=e(t.target).closest(".datepicker--cell");i.removeClass("-focus-"),this.silent=!0,this.focused="",this.silent=!1},_onTimeChange:function(t,e,i){var s=new Date,a=this.selectedDates,n=!1;a.length&&(n=!0,s=this.lastSelectedDate),s.setHours(e),s.setMinutes(i),n||this._getCell(s).hasClass("-disabled-")?(this._setInputValue(),this.opts.onSelect&&this._triggerOnChange()):this.selectDate(s)},_onClickCell:function(t,e){this.timepicker&&(e.setHours(this.timepicker.hours),e.setMinutes(this.timepicker.minutes)),this.selectDate(e)},set focused(t){if(!t&&this.focused){var e=this._getCell(this.focused);e.length&&e.removeClass("-focus-")}this._focused=t,this.opts.range&&1==this.selectedDates.length&&(this.minRange=this.selectedDates[0],this.maxRange="",n.less(this.minRange,this._focused)&&(this.maxRange=this.minRange,this.minRange="")),this.silent||(this.date=t)},get focused(){return this._focused},get parsedDate(){return n.getParsedDate(this.date)},set date(t){return t instanceof Date?(this.currentDate=t,this.inited&&!this.silent&&(this.views[this.view]._render(),this.nav._render(),this.visible&&this.elIsInput&&this.setPosition()),t):void 0},get date(){return this.currentDate},set view(t){return this.viewIndex=this.viewIndexes.indexOf(t),this.viewIndex<0?void 0:(this.prevView=this.currentView,this.currentView=t,this.inited&&(this.views[t]?this.views[t]._render():this.views[t]=new e.fn.datepicker.Body(this,t,this.opts),this.views[this.prevView].hide(),this.views[t].show(),this.nav._render(),this.opts.onChangeView&&this.opts.onChangeView(t),this.elIsInput&&this.visible&&this.setPosition()),t)},get view(){return this.currentView},get cellType(){return this.view.substring(0,this.view.length-1)},get minTime(){var t=n.getParsedDate(this.minDate);return new Date(t.year,t.month,t.date).getTime()},get maxTime(){var t=n.getParsedDate(this.maxDate);return new Date(t.year,t.month,t.date).getTime()},get curDecade(){return n.getDecade(this.date)}},n.getDaysCount=function(t){return new Date(t.getFullYear(),t.getMonth()+1,0).getDate()},n.getParsedDate=function(t){return{year:t.getFullYear(),month:t.getMonth(),fullMonth:t.getMonth()+1<10?"0"+(t.getMonth()+1):t.getMonth()+1,date:t.getDate(),fullDate:t.getDate()<10?"0"+t.getDate():t.getDate(),day:t.getDay(),hours:t.getHours(),fullHours:t.getHours()<10?"0"+t.getHours():t.getHours(),minutes:t.getMinutes(),fullMinutes:t.getMinutes()<10?"0"+t.getMinutes():t.getMinutes()}},n.getDecade=function(t){var e=10*Math.floor(t.getFullYear()/10);return[e,e+9]},n.template=function(t,e){return t.replace(/#\{([\w]+)\}/g,function(t,i){return e[i]||0===e[i]?e[i]:void 0})},n.isSame=function(t,e,i){if(!t||!e)return!1;var s=n.getParsedDate(t),a=n.getParsedDate(e),h=i?i:"day",o={day:s.date==a.date&&s.month==a.month&&s.year==a.year,month:s.month==a.month&&s.year==a.year,year:s.year==a.year};return o[h]},n.less=function(t,e,i){return t&&e?e.getTime()<t.getTime():!1},n.bigger=function(t,e,i){return t&&e?e.getTime()>t.getTime():!1},n.getLeadingZeroNum=function(t){return parseInt(t)<10?"0"+t:t},n.resetTime=function(t){return"object"==typeof t?(t=n.getParsedDate(t),new Date(t.year,t.month,t.date)):void 0},e.fn.datepicker=function(t){return this.each(function(){if(e.data(this,o)){var i=e.data(this,o);i.opts=e.extend(!0,i.opts,t),i.update()}else e.data(this,o,new m(this,t))})},e.fn.datepicker.Constructor=m,e.fn.datepicker.language={ru:{days:["Воскресенье","Понедельник","Вторник","Среда","Четверг","Пятница","Суббота"],daysShort:["Вос","Пон","Вто","Сре","Чет","Пят","Суб"],daysMin:["Вс","Пн","Вт","Ср","Чт","Пт","Сб"],months:["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],monthsShort:["Янв","Фев","Мар","Апр","Май","Июн","Июл","Авг","Сен","Окт","Ноя","Дек"],today:"Сегодня",clear:"Очистить",dateFormat:"dd.mm.yyyy",timeFormat:"hh:ii",firstDay:1}},e(function(){e(r).datepicker()})}(),function(){var t={days:'<div class="datepicker--days datepicker--body"><div class="datepicker--days-names"></div><div class="datepicker--cells datepicker--cells-days"></div></div>',months:'<div class="datepicker--months datepicker--body"><div class="datepicker--cells datepicker--cells-months"></div></div>',years:'<div class="datepicker--years datepicker--body"><div class="datepicker--cells datepicker--cells-years"></div></div>'},s=e.fn.datepicker,a=s.Constructor;s.Body=function(t,i,s){this.d=t,this.type=i,this.opts=s,this.$el=e(""),this.opts.onlyTimepicker||this.init()},s.Body.prototype={init:function(){this._buildBaseHtml(),this._render(),this._bindEvents()},_bindEvents:function(){this.$el.on("click",".datepicker--cell",e.proxy(this._onClickCell,this))},_buildBaseHtml:function(){this.$el=e(t[this.type]).appendTo(this.d.$content),this.$names=e(".datepicker--days-names",this.$el),this.$cells=e(".datepicker--cells",this.$el)},_getDayNamesHtml:function(t,e,s,a){return e=e!=i?e:t,s=s?s:"",a=a!=i?a:0,a>7?s:7==e?this._getDayNamesHtml(t,0,s,++a):(s+='<div class="datepicker--day-name'+(this.d.isWeekend(e)?" -weekend-":"")+'">'+this.d.loc.daysMin[e]+"</div>",this._getDayNamesHtml(t,++e,s,++a))},_getCellContents:function(t,e){var i="datepicker--cell datepicker--cell-"+e,s=new Date,n=this.d,h=a.resetTime(n.minRange),o=a.resetTime(n.maxRange),r=n.opts,c=a.getParsedDate(t),d={},l=c.date;switch(e){case"day":n.isWeekend(c.day)&&(i+=" -weekend-"),c.month!=this.d.parsedDate.month&&(i+=" -other-month-",r.selectOtherMonths||(i+=" -disabled-"),r.showOtherMonths||(l=""));break;case"month":l=n.loc[n.opts.monthsField][c.month];break;case"year":var u=n.curDecade;l=c.year,(c.year<u[0]||c.year>u[1])&&(i+=" -other-decade-",r.selectOtherYears||(i+=" -disabled-"),r.showOtherYears||(l=""))}return r.onRenderCell&&(d=r.onRenderCell(t,e)||{},l=d.html?d.html:l,i+=d.classes?" "+d.classes:""),r.range&&(a.isSame(h,t,e)&&(i+=" -range-from-"),a.isSame(o,t,e)&&(i+=" -range-to-"),1==n.selectedDates.length&&n.focused?((a.bigger(h,t)&&a.less(n.focused,t)||a.less(o,t)&&a.bigger(n.focused,t))&&(i+=" -in-range-"),a.less(o,t)&&a.isSame(n.focused,t)&&(i+=" -range-from-"),a.bigger(h,t)&&a.isSame(n.focused,t)&&(i+=" -range-to-")):2==n.selectedDates.length&&a.bigger(h,t)&&a.less(o,t)&&(i+=" -in-range-")),a.isSame(s,t,e)&&(i+=" -current-"),n.focused&&a.isSame(t,n.focused,e)&&(i+=" -focus-"),n._isSelected(t,e)&&(i+=" -selected-"),(!n._isInRange(t,e)||d.disabled)&&(i+=" -disabled-"),{html:l,classes:i}},_getDaysHtml:function(t){var e=a.getDaysCount(t),i=new Date(t.getFullYear(),t.getMonth(),1).getDay(),s=new Date(t.getFullYear(),t.getMonth(),e).getDay(),n=i-this.d.loc.firstDay,h=6-s+this.d.loc.firstDay;n=0>n?n+7:n,h=h>6?h-7:h;for(var o,r,c=-n+1,d="",l=c,u=e+h;u>=l;l++)r=t.getFullYear(),o=t.getMonth(),d+=this._getDayHtml(new Date(r,o,l));return d},_getDayHtml:function(t){var e=this._getCellContents(t,"day");return'<div class="'+e.classes+'" data-date="'+t.getDate()+'" data-month="'+t.getMonth()+'" data-year="'+t.getFullYear()+'">'+e.html+"</div>"},_getMonthsHtml:function(t){for(var e="",i=a.getParsedDate(t),s=0;12>s;)e+=this._getMonthHtml(new Date(i.year,s)),s++;return e},_getMonthHtml:function(t){var e=this._getCellContents(t,"month");return'<div class="'+e.classes+'" data-month="'+t.getMonth()+'">'+e.html+"</div>"},_getYearsHtml:function(t){var e=(a.getParsedDate(t),a.getDecade(t)),i=e[0]-1,s="",n=i;for(n;n<=e[1]+1;n++)s+=this._getYearHtml(new Date(n,0));return s},_getYearHtml:function(t){var e=this._getCellContents(t,"year");return'<div class="'+e.classes+'" data-year="'+t.getFullYear()+'">'+e.html+"</div>"},_renderTypes:{days:function(){var t=this._getDayNamesHtml(this.d.loc.firstDay),e=this._getDaysHtml(this.d.currentDate);this.$cells.html(e),this.$names.html(t)},months:function(){var t=this._getMonthsHtml(this.d.currentDate);this.$cells.html(t)},years:function(){var t=this._getYearsHtml(this.d.currentDate);this.$cells.html(t)}},_render:function(){this.opts.onlyTimepicker||this._renderTypes[this.type].bind(this)()},_update:function(){var t,i,s,a=e(".datepicker--cell",this.$cells),n=this;a.each(function(a,h){i=e(this),s=n.d._getDateFromCell(e(this)),t=n._getCellContents(s,n.d.cellType),i.attr("class",t.classes)})},show:function(){this.opts.onlyTimepicker||(this.$el.addClass("active"),this.acitve=!0)},hide:function(){this.$el.removeClass("active"),this.active=!1},_handleClick:function(t){var e=t.data("date")||1,i=t.data("month")||0,s=t.data("year")||this.d.parsedDate.year,a=this.d;if(a.view!=this.opts.minView)return void a.down(new Date(s,i,e));var n=new Date(s,i,e),h=this.d._isSelected(n,this.d.cellType);return h?void a._handleAlreadySelectedDates.bind(a,h,n)():void a._trigger("clickCell",n)},_onClickCell:function(t){var i=e(t.target).closest(".datepicker--cell");i.hasClass("-disabled-")||this._handleClick.bind(this)(i)}}}(),function(){var t='<div class="datepicker--nav-action" data-action="prev">#{prevHtml}</div><div class="datepicker--nav-title">#{title}</div><div class="datepicker--nav-action" data-action="next">#{nextHtml}</div>',i='<div class="datepicker--buttons"></div>',i2='<div class="datepicker--close"><div class="datepicker--closeitem">OK</div></div>',s='<span class="datepicker--button" data-action="#{action}">#{label}</span>',a=e.fn.datepicker,n=a.Constructor;a.Navigation=function(t,e){this.d=t,this.opts=e,this.$buttonsContainer="",this.init()},a.Navigation.prototype={init:function(){this._buildBaseHtml(),this._bindEvents()},_bindEvents:function(){this.d.$nav.on("click",".datepicker--nav-action",e.proxy(this._onClickNavButton,this)),this.d.$nav.on("click",".datepicker--nav-title",e.proxy(this._onClickNavTitle,this)),this.d.$datepicker.on("click",".datepicker--button",e.proxy(this._onClickNavButton,this))},_buildBaseHtml:function(){this.opts.onlyTimepicker||this._render(),this._addButtonsIfNeed()},_addButtonsIfNeed:function(){this.opts.todayButton&&this._addButton("today"),this.opts.clearButton&&this._addButton("clear")},_render:function(){var i=this._getTitle(this.d.currentDate),s=n.template(t,e.extend({title:i},this.opts));this.d.$nav.html(s),"years"==this.d.view&&e(".datepicker--nav-title",this.d.$nav).addClass("-disabled-"),this.setNavStatus()},_getTitle:function(t){return this.d.formatDate(this.opts.navTitles[this.d.view],t)},_addButton:function(t){this.$buttonsContainer.length||this._addButtonsContainer();var i={action:t,label:this.d.loc[t]},a=n.template(s,i);e("[data-action="+t+"]",this.$buttonsContainer).length||this.$buttonsContainer.append(a)},_addButtonsContainer:function(){this.d.$datepicker.append(i),this.d.$datepicker.append(i2),this.$buttonsContainer=e(".datepicker--buttons",this.d.$datepicker)},setNavStatus:function(){if((this.opts.minDate||this.opts.maxDate)&&this.opts.disableNavWhenOutOfRange){var t=this.d.parsedDate,e=t.month,i=t.year,s=t.date;switch(this.d.view){case"days":this.d._isInRange(new Date(i,e-1,1),"month")||this._disableNav("prev"),this.d._isInRange(new Date(i,e+1,1),"month")||this._disableNav("next");break;case"months":this.d._isInRange(new Date(i-1,e,s),"year")||this._disableNav("prev"),this.d._isInRange(new Date(i+1,e,s),"year")||this._disableNav("next");break;case"years":var a=n.getDecade(this.d.date);this.d._isInRange(new Date(a[0]-1,0,1),"year")||this._disableNav("prev"),this.d._isInRange(new Date(a[1]+1,0,1),"year")||this._disableNav("next")}}},_disableNav:function(t){e('[data-action="'+t+'"]',this.d.$nav).addClass("-disabled-")},_activateNav:function(t){e('[data-action="'+t+'"]',this.d.$nav).removeClass("-disabled-")},_onClickNavButton:function(t){var i=e(t.target).closest("[data-action]"),s=i.data("action");this.d[s]()},_onClickNavTitle:function(t){return e(t.target).hasClass("-disabled-")?void 0:"days"==this.d.view?this.d.view="months":void(this.d.view="years")}}}(),function(){var t='<div class="datepicker--time"><div class="datepicker--time-current">   <span class="datepicker--time-current-hours">#{hourVisible}</span>   <span class="datepicker--time-current-colon">:</span>   <span class="datepicker--time-current-minutes">#{minValue}</span></div><div class="datepicker--time-sliders">   <div class="datepicker--time-row">      <input type="range" name="hours" value="#{hourValue}" min="#{hourMin}" max="#{hourMax}" step="#{hourStep}"/>   </div>   <div class="datepicker--time-row">      <input type="range" name="minutes" value="#{minValue}" min="#{minMin}" max="#{minMax}" step="#{minStep}"/>   </div></div></div>',i=e.fn.datepicker,s=i.Constructor;i.Timepicker=function(t,e){this.d=t,this.opts=e,this.init()},i.Timepicker.prototype={init:function(){var t="input";this._setTime(this.d.date),this._buildHTML(),navigator.userAgent.match(/trident/gi)&&(t="change"),this.d.$el.on("selectDate",this._onSelectDate.bind(this)),this.$ranges.on(t,this._onChangeRange.bind(this)),this.$ranges.on("mouseup",this._onMouseUpRange.bind(this)),this.$ranges.on("mousemove focus ",this._onMouseEnterRange.bind(this)),this.$ranges.on("mouseout blur",this._onMouseOutRange.bind(this))},_setTime:function(t){var e=s.getParsedDate(t);this._handleDate(t),this.hours=e.hours<this.minHours?this.minHours:e.hours,this.minutes=e.minutes<this.minMinutes?this.minMinutes:e.minutes},_setMinTimeFromDate:function(t){this.minHours=t.getHours(),this.minMinutes=t.getMinutes(),this.d.lastSelectedDate&&this.d.lastSelectedDate.getHours()>t.getHours()&&(this.minMinutes=this.opts.minMinutes)},_setMaxTimeFromDate:function(t){
this.maxHours=t.getHours(),this.maxMinutes=t.getMinutes(),this.d.lastSelectedDate&&this.d.lastSelectedDate.getHours()<t.getHours()&&(this.maxMinutes=this.opts.maxMinutes)},_setDefaultMinMaxTime:function(){var t=23,e=59,i=this.opts;this.minHours=i.minHours<0||i.minHours>t?0:i.minHours,this.minMinutes=i.minMinutes<0||i.minMinutes>e?0:i.minMinutes,this.maxHours=i.maxHours<0||i.maxHours>t?t:i.maxHours,this.maxMinutes=i.maxMinutes<0||i.maxMinutes>e?e:i.maxMinutes},_validateHoursMinutes:function(t){this.hours<this.minHours?this.hours=this.minHours:this.hours>this.maxHours&&(this.hours=this.maxHours),this.minutes<this.minMinutes?this.minutes=this.minMinutes:this.minutes>this.maxMinutes&&(this.minutes=this.maxMinutes)},_buildHTML:function(){var i=s.getLeadingZeroNum,a={hourMin:this.minHours,hourMax:i(this.maxHours),hourStep:this.opts.hoursStep,hourValue:this.hours,hourVisible:i(this.displayHours),minMin:this.minMinutes,minMax:i(this.maxMinutes),minStep:this.opts.minutesStep,minValue:i(this.minutes)},n=s.template(t,a);this.$timepicker=e(n).appendTo(this.d.$datepicker),this.$ranges=e('[type="range"]',this.$timepicker),this.$hours=e('[name="hours"]',this.$timepicker),this.$minutes=e('[name="minutes"]',this.$timepicker),this.$hoursText=e(".datepicker--time-current-hours",this.$timepicker),this.$minutesText=e(".datepicker--time-current-minutes",this.$timepicker),this.d.ampm&&(this.$ampm=e('<span class="datepicker--time-current-ampm">').appendTo(e(".datepicker--time-current",this.$timepicker)).html(this.dayPeriod),this.$timepicker.addClass("-am-pm-"))},_updateCurrentTime:function(){var t=s.getLeadingZeroNum(this.displayHours),e=s.getLeadingZeroNum(this.minutes);this.$hoursText.html(t),this.$minutesText.html(e),this.d.ampm&&this.$ampm.html(this.dayPeriod)},_updateRanges:function(){this.$hours.attr({min:this.minHours,max:this.maxHours}).val(this.hours),this.$minutes.attr({min:this.minMinutes,max:this.maxMinutes}).val(this.minutes)},_handleDate:function(t){this._setDefaultMinMaxTime(),t&&(s.isSame(t,this.d.opts.minDate)?this._setMinTimeFromDate(this.d.opts.minDate):s.isSame(t,this.d.opts.maxDate)&&this._setMaxTimeFromDate(this.d.opts.maxDate)),this._validateHoursMinutes(t)},update:function(){this._updateRanges(),this._updateCurrentTime()},_getValidHoursFromDate:function(t,e){var i=t,a=t;t instanceof Date&&(i=s.getParsedDate(t),a=i.hours);var n=e||this.d.ampm,h="am";if(n)switch(!0){case 0==a:a=12;break;case 12==a:h="pm";break;case a>11:a-=12,h="pm"}return{hours:a,dayPeriod:h}},set hours(t){this._hours=t;var e=this._getValidHoursFromDate(t);this.displayHours=e.hours,this.dayPeriod=e.dayPeriod},get hours(){return this._hours},_onChangeRange:function(t){var i=e(t.target),s=i.attr("name");this.d.timepickerIsActive=!0,this[s]=i.val(),this._updateCurrentTime(),this.d._trigger("timeChange",[this.hours,this.minutes]),this._handleDate(this.d.lastSelectedDate),this.update()},_onSelectDate:function(t,e){this._handleDate(e),this.update()},_onMouseEnterRange:function(t){var i=e(t.target).attr("name");e(".datepicker--time-current-"+i,this.$timepicker).addClass("-focus-")},_onMouseOutRange:function(t){var i=e(t.target).attr("name");this.d.inFocus||e(".datepicker--time-current-"+i,this.$timepicker).removeClass("-focus-")},_onMouseUpRange:function(t){this.d.timepickerIsActive=!1}}}()}(window,jQuery);
$('body').on('click', '.datepicker--closeitem', function(){ $("[data-timepicker]").data('datepicker').hide(); });

// open show settings
var bc_showsettings = (function(close, num){
	if ((!$('body').is('.showsettings') && !close) || num) { //show
		$('.bc_contentbody').css('display', 'block');
		setTimeout(function() { $('body').addClass('showsettings') }, 0);

		if(num){
			$('.adm-first').removeClass('active');
			$('.adm-second').hide();
			$('[data-subopt="'+num+'"').click();
		}
		else if(!$('.adm-first.active').length) $('#bc_adminmenu a:first').click();

		//load.scroll("off");
	} else { //hide
		$('body').removeClass('showsettings');
		setTimeout(function() { $('.bc_contentbody').css('display', 'none'); }, 300);
		//load.scroll("on");
	}
});
var uri = (function () {
	var b = {
		settings: "/bc/modules/bitcat/index.php",
		body: "/bc/modules/bitcat/index.php"
	};
	return b
})(uri || {});


// Вставка админки в html



$(document).ready(function(){
// if admin
if (bc) {

	var topset = (async function () {
		$.post(uri.settings, {'bc_action':'topset', 'time': Math.round(new Date().getTime() / 1000)}, function(data){
			$('body').prepend("<div id='bc_topset'>"+data.topLine+data.leftMenu+"<div class='bc_contentbody'><div class='bc_content'></div></div></div>");
			caruselFontColor();
		},'json');
	});

	topset();

	var bc_sort = (function(obj, place){
		block = obj.parents('section.blocks');
		admid = block.data('admid');
		prior = block.data('prior');

		if (place=='up') {
			block2 = block.prev('section.blocks[data-blockid]');
		} else if (place=='down') {
			block2 = block.next('section.blocks[data-blockid]');
		}
		admid2 = block2.data('admid');
		if (admid2) {
			if (!obj.hasClass('disabled')) {
				$('.bc_sort a').addClass('disabled');
				prior2 = block2.data('prior');


				
				$.post(uri.settings,{'bc_action':'sortblock','place':place,'blockid':admid,'prior':prior,'blockid2':admid2,'prior2':prior2,'time':Math.round(new Date().getTime() / 1000)}, function(data){
					if (data.status=='ok') {
						block.data('prior',data.block);
						block2.data('prior',data.block2);
						block_c = block.clone(true);
						block.remove();
						if (place=='up') {
							block2.before(block_c);
						} else if (place=='down') {
							block2.after(block_c);
						}
						$('.bc_sort a').removeClass('disabled');
					}
				},'json');
			}
		}
	});




	// Клик по кнопке открытия админки
	$(document).on( "click", ".bc_settings", function(){
		bc_showsettings();
	});












	$("body").on("keyup",".sub-item-main [name='srch']",function(){
        var input = $(this),
            value = input.val().toLowerCase();
        input.parents(".sub-item-main").find(".sub-item").each(function(){
        	var item = $(this)
        	if(item.attr("data-name").indexOf(value) > -1){
				item.removeClass('none')
				item.parents(".sub-item.none").removeClass('none')
        	}else{
				item.addClass('none')
        	}
        })
    });
	$("body").on("change",".sub-item-main input[type='checkbox']",function(){
        var checkbox = $(this),
        	checked = checkbox.prop("checked"),
        	id = checkbox.val(),
        	name = checkbox.parents(".sub-item").attr("data-fullname")
        if(checked){
        	$(".selected-list").append("<div class='selected-item' data-id='"+id+"'>"+name+"</div>")
        }else{
        	$(".selected-item[data-id='"+id+"']").remove()
        }
	})









	var caruselFontColor = (function () {
	    // карусель шрифтов и цветов
		var tt = 110, ttr = 0; rrs = {};
	    for (var i = 0; i < 20; i++) {rrs[ttr] = {items: i}; ttr += tt;}
	    $('.bc_getcolor .bc_select_item').owlCarousel({
	        margin: 15,
	        nav:true,
	        responsiveClass: true,
	        responsiveBaseElement: ".bc_getcolor .bc_select_item",
	        responsive: rrs
	    });
	    // карусель шрифтов и цветов
		var tt = 160, ttr = 0, rrs = {};
	    for (var i = 0; i < 20; i++) {rrs[ttr] = {items: i}; ttr += tt;}
	    $('.bc_getfont .bc_select_item').owlCarousel({
	        margin: 10,
	        nav:true,
	        responsiveClass: true,
	        responsiveBaseElement: ".bc_getfont .bc_select_item",
	        responsive: rrs
	    });
	});

	// add block buttons: sort/edit

	$('section.blocks[data-blockid]').each(function(){
		o = $(this);
		objv = o.data();
		o.append("<div class='bc_blk_btn'><div class='bc_buttons'></div></div>");
		id = o.data('blockid');
		bc_but = o.find('.bc_buttons');

		bc_but.append("<a class='bc_buttons_sett' title='Настройки блока №"+id+"' data-rel='lightcase' data-lc-options='{\"maxWidth\":950,\"groupClass\":\"modal-edit block-edit-"+id+"\"}' href='/blockofsite/edit_blockofsite_"+objv.admid+".html?isNaked=1&settblk=1'>Настройки блока</a>");
		bc_but.append("<a data-place='down' class='icons i_gooddown bc_buttons_downl' href='' title='переместить вниз'></a><a data-place='up' class='icons i_goodup bc_buttons_upl' href='' title='переместить вверх'></a>");
	});
	$('[data-zone]').each(function(){
		o = $(this);
		if(o.find('.blocks[data-blockid]').length){
			o.append("<div class='bc_zone_btn'><div class='bc_zb_pos'><div class='bc_zone_buttons'></div></div></div>");
			id = o.data('id');
			zone = o.data('zone');
			name = o.data('name');
			editLink = (o.data('editlink') ? o.data('editlink') : "/zone/zone_"+ zone + '.html');
			bc_but = o.find('.bc_zone_buttons');

			bc_but.append("<div class='bc_zone_line'><a title='Добавить блок' class='zone-edit zone-edit-add' data-rel='lightcase' data-lc-options='{\"maxWidth\":950,\"groupClass\":\"modal-edit\"}' href='/blockofsite/add_blockofsite.html&f_col="+id+"&isNaked=1&settblk=1'><span class='zone-edit-name'>Добавить блок</span></a></div>");
			bc_but.append("<div class='bc_zone_line'><a title='Настройки зоны' class='zone-edit icons admin_icon_7' data-rel='lightcase' data-lc-options='{\"maxWidth\":950,\"groupClass\":\"modal-edit\"}' href='"+ editLink +"?isNaked=1&settblk=1'><span class='zone-edit-name'>Настройка зоны</span></a></div>");
		}
	});

	var colors = ["rgba(159, 0, 255, 0.2)", "rgba(0, 255, 162, 0.2)", "rgba(0, 153, 255, 0.2)", "rgba(249, 255, 0, 0.2)", "rgba(0, 14, 255, 0.2)", "rgba(255, 0, 104, 0.2)"];
	var i = 0;
	$('.bc_zone_btn').each(function(){
		if(colors.length <= i) i = 0;
		$(this).css('background', colors[i]);
		i++;
	});

    // button sort blocks
	$(document).on('click','.bc_blk_btn a[data-place]',function(){
		bc_sort($(this), $(this).data('place'));
		return false;
	});


	// esc close settings
	$(this).keydown(function(eventObject){
		if (eventObject.which == 27 && $('body').is('.showsettings')) {
			bc_showsettings(0);
			return false;
		}

		if(event.ctrlKey && event.keyCode == 192) {
			bc_showsettings(0);
			$('.admin-menu-css').click();
			return false;
		}
		// save settings
		if(event.ctrlKey && event.shiftKey && event.keyCode==83){
			input = $('.view-content.opened .bc_btnbody .bc-btn input');
			if($('body').is('.showsettings') && input.length) input.click();
		}
	});
	setTimeout(function(){
		// drag drop block
		$('#bc_topset section.blocks').draggable({
			helper: 'clone',
			handle: ".bc_setting",
			cursor: 'move',
			cursorAt: { top: 20, right: 50 },
			distance: 30,
			opacity : 0.8
	    });
		$(".bc_setting").disableSelection();

	    $('#bc_topset .zone').droppable({
	        activeClass: "zone-active",
	        hoverClass: "zone-hover",
			tolerance: "pointer",
	        over: function(event, ui) {
	            ui.helper.addClass('clone-block-active');
				//curzone = $(event.target).data('zone');
				//ui.helper.find('div').text('В зону № '+curzone);
				$('.blocks').addClass('active-drop');
	        },
	        out: function(event, ui) {
	            //ui.helper.removeClass('clone-block-active');
				//$('.blocks').removeClass('active-drop');
	        },
			drop: function(event, ui) {
				$('.blocks').removeClass('active-drop');
				tozone = $(this).data('zone');
				tozonewidth = ($(this).data('width') ? $(this).data('width') : 12);
				fromzone =  ui.draggable.parents('.zone:first').data('zone');
				blockid = ui.draggable.data('blockid');
				width = ui.draggable.data('width');
				blockprior = ui.draggable.data('prior');
				block = $('#block'+blockid);
				blockpaste = false;
	            if (tozone>0 && fromzone>0 && tozone!=fromzone) {
					console.log('blockid: '+blockid+' width: '+width+' fromzone: '+fromzone+' tozone: '+tozone+' tozonewidth: '+tozonewidth);
					$.post(uri.settings,{'bc_action':'changezone','blockid':blockid,'width':width,'fromzone':fromzone,'tozone':tozone,'tozonewidth':tozonewidth,'time':Math.round(new Date().getTime() / 1000)}, function(data){
						if (data.status=='ok') {
							$('.zone'+tozone).find('.blocks').each(function(){
								curprior = $(this).data('prior');
								if (curprior>blockprior) {
									$(this).before(block);
									blockpaste=true;
									return false;
								}
							});
							if(width>tozonewidth){
								$('[data-blockid='+blockid+']').removeClass('grid_'+width).addClass('grid_'+tozonewidth);
							}
							if (!blockpaste) {
								if ($('.zone'+tozone+' .blocks').length>0) {
									$('.zone'+tozone+' .blocks:last').after(block);
								} else {
									$('.zone'+tozone).append(block);
								}
							}
							if (data.blocknewwidth>0) {
								$('#block'+blockid).data('width',data.blocknewwidth).removeClass('ui-draggable').removeClassWild("grid_*").addClass('grid_'+data.blocknewwidth);
							}
						}
					},'json');
					$('.blocks').removeClass('active-drop');
				}

	        }
	    });

	},1000);



	var cache;
	// сохранить/отменить изменения в зонах
	$('body').on('click', '.admin-activate-zone .adm-drop-save, .admin-activate-zone .adm-drop-nosave', function(e){
		btn = $(this);
		if(btn.hasClass('adm-drop-save')){
			btn.parents('.admin-zone-btn').addClass('disabled-btn');
			var arrzone = {}, myArray = {};
			$('[data-zoneposition]').each(function(el, el1){
				$(this).find('[data-zoneid]').each(function(i){
					myArray[i] = {"id": $(this).data('zoneid')};
				});
				arrzone[$(el1).data('zoneposition')] = myArray;
				myArray = {};
			});
			// отправка данных
			if (arrzone) {
				$.post(uri.settings,{'bc_action':'dropZone','arrzone':arrzone,'time':Math.round(new Date().getTime() / 1000)}, function(data){
					if (data.status=='ok') {
						$('.bc_links-update').addClass('active');
					}else{
						console.log(6);
					}
				},'json');
			}
		}
		if(btn.hasClass('adm-drop-nosave')){
			$(".admin-block-zone").html(cache);
		}
		$('.admin-block-zone').height('auto')
 		$("#sortable").sortable('disable');
		$('.admin-design.admin-green').slideDown(300);
		$('.zone-content-zgl').slideUp(300);
		$('.admin-block-zone').removeClass('admin-zone-active-zn');
		forEndBtn(btn);
		e.stopPropagation();
	});
	// активация переноса зон
	$('body').on('click', '.admin-activate-zone', function(){
		btn = $(this);
		forStartBtn(btn);
		$('.admin-design.admin-green').slideUp(300);
		$('.zone-content-zgl').slideDown(200);
		$('.admin-block-zone').addClass('admin-zone-active-zn');
      	$(".admin-zonedrop.admin-design").sortable({
	    	connectWith: '.admin-zonedrop.admin-design',
			handle: ".admin-zone-dr-text",
			containment: ".admin-block-zone",
			cursor: "move",
  			axis: 'y'
        });
	    $('.admin-zonedrop.admin-design').droppable();
		$('.admin-zonedrop.admin-design, .admin-zone.admin-zonedrop').disableSelection();
		return false;
	});
	// сохранить/отменить изменения в блоков
	$('body').on('click', '.admin-activate-sub .adm-drop-save, .admin-activate-sub .adm-drop-nosave', function(e){
		btn = $(this);
		if(btn.hasClass('adm-drop-save')){
			btn.parents('.admin-zone-btn').addClass('disabled-btn');
			var i = 0, arrblocks = {};
			$('[data-zoneid]').each(function(i1, el){
				zonegrid = $(el).data('grid');
				zoneid = $(el).data('zoneid');
				$(this).find('[data-blockid]').each(function(){
					blkgrid = $(this).data('gridblk');
					blkid = $(this).data('blockid');
					arrblocks[i] = {"zone-id" : zoneid, "block-id" : blkid, "gridblk" : (blkgrid > zonegrid ? zonegrid : blkgrid)};
					i++;
				});
			});
			// отправка данных
			if (arrblocks) {
				$.post(uri.settings,{'bc_action':'dropBlocks','arrblocks':arrblocks,'time':Math.round(new Date().getTime() / 1000)}, function(data){},'json');
			}
		}
		if(btn.hasClass('adm-drop-nosave')){
			$(".admin-block-zone").html(cache);
		}
		$('.admin-block-zone').height('auto')
 		$("#sortable").sortable('disable');
		$('.admin-block-zone').removeClass('admin-zone-active-sub');
		forEndBtn(btn);
		e.stopPropagation();
	});
	// активация переноса блоков
	$('body').on('click', '.admin-activate-sub', function(){
		btn = $(this);
		forStartBtn(btn);
		var height;
	    var WidthMain = $('.admin-zone-body:first').width();
		$('.admin-block-zone').addClass('admin-zone-active-sub');
      	$(".admin-zone-body").sortable({
	    	connectWith: '.admin-zone-body',
			handle: ".admin-checkbox-drs",
  			items: "> .admin-block",
			cursor: "move"
      	});
      	$(".admin-zone-body .admin-block").resizable({
			helper: "ui-resizable-helper",
			handles: "e",
			maxWidth: WidthMain,
	        create:function(){
	            var list=this;
	            resize = function(){
	                $(list).css("height","auto");
	                $(list).height($(list).height());
	            };
	            $(list).height($(list).height());
	            $(list).find('img').load(resize).error(resize);
	        },
	        start: function(event, ui){
	            height = ui.originalElement.height();
	            WidthMain = ui.originalElement.parent().width();
	            $( ".admin-zone-body .admin-block" ).resizable( "option", "maxWidth", WidthMain);
	        },
	        stop: function(event, ui){
	            ui.originalElement.css('height', height+'px').css('width', '');
	        },
  			resize: function( event, ui ) {
  				var grnam = $('.ui-resizable-helper').width()/(WidthMain/ui.originalElement.parents('.admin-zone').data('grid'));
  				grnam = grnam>=1 ? grnam : 1;
  				ui.originalElement.removeClassWild("admin-block-*").addClass('admin-block-'+Math.round(grnam)).css('width', '').data('gridblk', Math.round(grnam));
  			}
		});
	    $('.admin-zone-body').droppable();
		$('.admin-zone-body, admin-zone-body .admin-block').disableSelection();
		return false;
	});
	function forStartBtn(el){
		cache = $(".admin-block-zone").html();
		$('.adm-btn-top').addClass('admin-zone-map-active');
		$('.zone-block').addClass('active');
		el.addClass('admin-zone-btn-active');
	}
	function forEndBtn(el){
		el.parents('.admin-zone-btn').removeClass('disabled-btn');
		$('.adm-btn-top').removeClass('admin-zone-map-active');
		$('.zone-block').removeClass('active');
		el.removeClass('admin-zone-btn-active');
		btn.parents('.admin-zone-btn').removeClass('admin-zone-btn-active');
	}


	$('body').on('click', '.add-slide-1 > label', function(){
		el = $(this);
		parent = el.parents('.add-slide-1');
		if(!parent.hasClass('none')){
			$('.add-slide-1').addClass('none');
			$('.add-slide-2').show();
			title = el.parents('#lightcase-case').find('#lightcase-title');
			text = title.text() + ': <b>' + el.find('.add-slide-name').text() + '</b>';
			title.html(text);
			lightcase.resize();
		}
	});

	// load class settings
	$("body").on("change","[name='phpset\[contenttype\]'], #tab_content [name='f_sub']",function(){
		typecont = $("[name='phpset\[contenttype\]']").val();
		subid = $("[name='f_sub']").val();
		subidthisblk = $(this).parents('form').find("[name='message']").val();
		setdiv = $('#classSettings');
		contentType = setdiv.data('type');
		contentSub = setdiv.data('sub');
		console.log('load class settings ');
		if (contentType!=typecont || contentSub!=subid){
			loadingView("start", setdiv);
			$.post(uri.settings,{'bc_action':'loadsetclass','typecont':typecont,'subid':subid,'subidthisblk':subidthisblk,'time':Math.round(new Date().getTime()/1000)}, function(data){

				for (var i = 0; i <= 5; i++) {
					if(i==typecont) $('.nosetgroup'+i).hide();
					else $('.nosetgroup'+i).css('display', 'inline-block');
					if(i!=typecont) $('.setgroup'+i).hide();
					else $('.setgroup'+i).css('display', 'inline-block');
				}

				$(".setgroup"+typecont).show();
				setdiv.html(data);
				setdiv.data('type', typecont).data('sub', subid);
				lightcaseStyle();
				lightcase.resize();
				loadingView("end", setdiv);
			});
		}
	});

	$("body").on("change", "input[name='phpset[contsetclass][itemsinmenu]']",function(){
		var input = $(this),
			enable = input.prop("checked")
		$("input[name='phpset[contsetclass][itemsinmenu]']").not(input).prop("checked", enable)
	})


}
});




// on/off blocks checkbox
$(document).on("change", ".admin-checkbox .switch input", function(){
	bc_blockVis($(this).parents('[data-blockid]'));
});
var bc_blockVis = (function(obj){
	admid = obj.data('blockid');
	check = (obj.find('input').is(':checked') ? 1 : 0);
	if(check) obj.addClass('admin-blk-on').removeClass('admin-blk-off');
	else obj.removeClass('admin-blk-on').addClass('admin-blk-off');
	$.post(uri.settings,{'bc_action':'blockvis','blockid':admid,'check':check,'time':Math.round(new Date().getTime() / 1000)}, function(data){},'json');
});

$('body').on('click','.sc-color-head',function(){
	parent = $(this).parent('.sc-color-sel');
	parent.toggleClass('active');
});
$('body').on('click','.bc_element_head, .bc_close',function(){
	if($(this).hasClass('bc_element_head')){
		$(this).parent('.bc_element').toggleClass('active');
	}else{
		$(this).parents('.bc_element').removeClass('active');
	}
});
$(document).click(function(e){ // событие клика по веб-документу
	divs = [".bc_getcolor", ".bc_getfont", ".bc_profile"];
	divs.forEach(function(item, i, arr) {
    	if ($(item).length && !($(e.target).parents(item).length || $(e.target).is(item))) {
			$(item).removeClass('active');
		}
	});
});

// Выбор палитры или шрифта
$('body').on('click','.sc-col-item .sc-col-item-body, .sc-random',function(){
	bc_showsettings(1); // закрыть админку
	parent = $(this).parent('.sc-col-item');
	numcolor = parent.data('numcolor');
	namefont = parent.data('namefont');
	random = $(this).data('random') ? 1 : 0;
	if(numcolor>0 || namefont || random){

		load.loading('start');

		post = (numcolor>0 ? "numcolor="+numcolor : "");
		post += (namefont ? (post ? "&" : "")+"namefont="+namefont : "");
		post += (random ? (post ? "&" : "")+"random=1" : "");
		thissub = $('body').data('sub');
		$.post('/bc/modules/bitcat/index.php?bc_action=savecolorfont&thissub='+thissub, post, function(data){

	  		//DeveloperTool.ReloadCSSLink($("[href*='/bc_custom.css']")[0]);

	  		//if(data.jsonparam) setjsonparam(JSON.parse(data.jsonparam), 1);

	  		//load.loading('end');
			if (data.reload) location.reload(true); // good
	  		/*$('[data-view]').remove();
	  		$('.bc_menubody a, .adm-first').removeClass('loaded').removeClass('active');
	  		$('.adm-second').hide();*/

		}, 'json');
	}
});
$('body').on('click', '.bc_clear', function(){
	$(this).parents('.bc_element_body').find('.owl-item:first .sc-col-item').click();
});
// Переключение настроек зоны
$('body').on('click', '.sc-settings-line', function(){
	$('body').toggleClass('sc-zone-active');
});


// колво символов в строке
$('body').on('keyup',".input-field input",function(){
	var spanlab = $(this).parents('.input-field').find('label b');
	if (spanlab.length>0) {
		var lentxt = $(this).val().replace(/\s+/g,'').length;
		spanlab.text("Длина: - "+lentxt);
	}
});

// колво символов в строке
$('body').on('keyup',".textarea-field textarea",function(){
	var spanlab = $(this).parents('.textarea-field').find('.textarea-title b');
	if (spanlab.length>0) {
		var lentxt = $(this).val().replace(/\s+/g,'').length;
		spanlab.text("Длина: - "+lentxt);
	}
});


// search photo
$(document).on('click','.colblock-search .add-btn, .photo-search-item .add-btn',function(){
	var querystr = '',
		result = '',
		btn = $(this),
		btnSpan = btn.find('span');

	var form = btn.parents('form:first, .photo-search-item'),
		field_data = btn.parents('.colblock-search').find(".serach-photo"),
		line,
		name_field = btn.data('name');

	if(!field_data.length){
		sr = btn.parents('.photo-search-item')
		if(!sr.next(".photo-item-tr").length) sr.after('<tr class="photo-item-tr"><td colspan="4"></td></tr>');
		field_data = sr.next(".photo-item-tr").find('td')
	}

	var input = form.find("input[name='f_"+name_field+"']"),
		name = input.val(),
		title = input.parent().find('label').text();

	if(!field_data.find(".searach-line-"+name_field).length){
		field_data.prepend(line = $('<div class="searach-line-'+name_field+'"></div>'))
	}else{
		line = field_data.find(".searach-line-"+name_field)
	}

	line.slideUp();

	if (!name || name == '') {
		line.html('<p>Заполните поле "'+title+'"</p>').slideDown();
		btnSpan.removeClass("loading")
	}else{
		btnSpan.addClass("loading")
		start = (parseInt($(this).data('start'))>0 ? parseInt($(this).data('start')) : 0) + 1;
		btn.data('start', start);

		panel = form.hasClass('photo-search-item') ? 1 : 0;
		id = panel ? form.attr('data-orderid') : "";

		$.post(uri.settings, {'bc_action':'getphoto', 'query': name, 'start': start, 'id': id, 'panel': panel, 'time':Math.round(new Date().getTime() / 1000)}, function(data){
			if (data.status=='ok') {
				result = '<p>"'+name+'" - поиск по полю ' + title.replace("*", "") + '</p>' + data.html;
			} else {
				result = '<p>Ничего не найдено</p>';
				btn.data('start', '0');
			}
			line.html(result).slideDown({progress: function(){ lightcase.resize() }});
			btnSpan.removeClass("loading")
		},'json');
	}

	return false;
});



/*function setjsonparam(param, all){
	// массив статичных зон
	var nolinezone = [2, 3, 4, 5];

	if($('[data-zone]').length>0){
		$('[data-zone]').each(function(){
			id = $(this).data('id');
			if($(this).find('.zone-bg').length>0){
				$(this).find('.zone-bg').removeClass().addClass(param.zone[id].classbg);
			}
		});
	}
	if(all){
		if($('[data-blockid]').length>0){
			$('[data-blockid]').each(function(){
				id = $(this).data('blockid');
				if(typeof param.blocks[id].class !== "undefined") $(this).removeClass().addClass(param.blocks[id].class);
			});
		}
	}
}*/


var DeveloperTool={
        Init:function(){
                this.headObj =
		document.getElementsByTagName('html')[0].getElementsByTagName('head')[0];
                return this;
        },
        ReloadAllCSS : function(headObj) {
                console.log("DT:ReloadAllCSS");
                var links = headObj.getElementsByTagName('link');
                for (var i=0 ; i < links.length ; i++){
                        var link = links[i];
                        this.ReloadCSSLink(link);
                }
                return this;
        },
        ReloadCSSLink : function(item) {
                var value = item.getAttribute('href');
                var cutI = value.lastIndexOf('?');
                if (cutI != -1)
                        value = value.substring(0, cutI);
                item.setAttribute('href', value + '?t=' + new Date().valueOf());
                return this;
        },
        ReloadAllCSSThisPage : function() {
                this.ReloadAllCSS(this.headObj);
                return this;
        }
};

function add_variant() {
  i = parseInt($('#variants .variant:last').data('num'))+1;
  if (i) {
	$('#variants .variant:last').after("<div class=variant data-num='"+i+"'><input placeholder='Название' type=text name='variable["+i+"][name]'><input placeholder='Цена' type=text name='variable["+i+"][price]'><input placeholder='количество' type=text name='variable["+i+"][stock]'></div>");
  }
  return false;
}

function add_color() {
  i = parseInt($('#colors .colors:last').data('num'))+1;
  if (i) {
	$('#colors .colors:last').after("<div class=colors data-num='"+i+"'><input placeholder='Название цвета' type=text name='colors["+i+"][name]'><input class=color2 type=text name='colors["+i+"][code]'></div>");
  }
  return false;
}

function add_line(keyw) {
	el = $('#'+keyw+' .multi-line:last');
	if (el.length == 0) {
		document.querySelector('#' + keyw).append(document.querySelector('#template_' + keyw)?.content.cloneNode(true));
	} else {
		var newLine = el.clone();
		el.after(newLine);

		i = parseInt(el.data('num'));
		newLine.attr('data-num', i+1).addClass("multi-line-new");
		newLine.prepend("<input type='hidden' name='newvariable["+(i+1)+"]'>");
		newLine.find('[name*="['+i+']"], [for*="['+i+']"], [name^="'+keyw+'['+i+']"], [for^="'+keyw+'['+i+']"], [name="f_'+keyw+'_name['+i+']"], [for="f_'+keyw+'_name['+i+']"], [name="f_'+keyw+'_file['+i+'][]"]').each(function(){
			var attrName = $(this).attr('name') ? 'name' : 'for';
			if($(this).is('[name*="['+i+']"]') || $(this).is('[for*="['+i+']"]')){
				// свободная замена
				text = $(this).attr(attrName)
					.replace('['+i+']', '['+(i+1)+']')
					.replace('['+i+']', '['+(i+1)+']');
			}else{
				// точная замена
				text = $(this).attr(attrName).replace(keyw + '['+ i +']', keyw + '['+ (i+1) +']');
				text = text.replace(keyw + '_name['+ i +']', keyw + '_name['+ (i+1) +']');
				text = text.replace(keyw + '_file['+ i +']', keyw + '_file['+ (i+1) +']');
			}
			$(this).attr(attrName, text);
		});


		newLine.find('input:not(.color):not([name="'+keyw+'['+(i+1)+'][checked]"]), textarea').val('').removeAttr('readonly');
		newLine.find('.input-field label').removeClass('active');
		newLine.find('.input-file').removeClass('active').find('.file-name').text('Выберите файл');
		newLine.find('input[type="checkbox"]').removeAttr('checkbox'); // checkbox

		newLine.find('.sp-replacer').remove();
		newLine.find('.input-color').removeClass('active')
		newLine.find('.color-bottom').text('');
		newLine.find('input.color').val('').color();

		newLine.find('.nice-select').remove();
		newLine.find('select').niceSelect();

		lightcase.resize();
	}
	$('#' + keyw ).parent().find('.delete-btn').show()
	return false;
}
function remove_line(el){
	$(el).parents(".multi-line").remove();
	lightcase.resize();
}

function remove_all_line(keyw)
{
	$('#' + keyw + ' .multi-line').remove();
	$('#' + keyw ).parent().find('.delete-btn').hide()
	lightcase.resize();
}
$("body").on("change", ".multi-line input[type='checkbox'][name*='checked']", function(){
	checkbox = $(this);
	parent = checkbox.parents(".multi-line");
	if(checkbox.prop("checked")){
		parent.removeClass("multi-disable");
	}else{
		parent.addClass("multi-disable");
	}
})

$.fn.removeClassWild = function(mask) {
    return this.removeClass(function(index, cls) {
        var re = mask.replace(/\*/g, '\\S+');
        return (cls.match(new RegExp('\\b' + re + '', 'g')) || []).join(' ');
    });
};

$('body').on('click','label.bc_edit_notitle',function(){
	if($(this).find('input').prop("checked")){
		$(this).parents('.bc_setvalue').addClass('bc_setvalue_dis');
	}else{
		$(this).parents('.bc_setvalue').removeClass('bc_setvalue_dis');
	}
});

// Смена режима админки
$('body').on('change', 'input[name=scsett]', function(){
	v = $(this).val();
	$.cookie('adminkorzilla', v, { path: '/' });

	$('body')
		.removeClass('admin-sett-1')
		.removeClass('admin-sett-2')
		.removeClass('admin-sett-3')
		.addClass('admin-sett-'+v);
});


$('body').on('change', '[name="f_discont"], [name="f_pricediscont"]', function(){
	v = $(this).val();
	if(isNumber(v) > 0){
		datapicker = $(this).parents('.date-calendar').find('[data-timepicker]');
		datapicker.click();
	}
});


/*** ПЕРЕХОДЫ ПО МЕНЮ СЛЕВА ***/
// subdivision map drag
function letsfunction(){
	var sortableMap = $('ol.sortable');
	sortableMap.nestedSortable({
			forcePlaceholderSize: true,
			handle: '.dropthree',
			helper:	'clone',
			items: 'li',
			opacity: .6,
			placeholder: 'placeholder',
			revert: 100,
			tabSize: 25,
			tolerance: 'pointer',
			toleranceElement: '> div',
			maxLevels: 12,

			isTree: true,
			expandOnHover: 500,
			startCollapsed: true,
			start: function( event, ui ) {
				$('#siteTree').addClass('siteTree-active');
			},
			stop: function( event, ui ) {
				$('#siteTree').removeClass('siteTree-active');
			},
			update: function( event, ui ) {
	            var serialize = sortableMap.nestedSortable('toArray');
	            if(serialize){
					$.post(uri.settings,{'bc_action':'dropSub','serialize':serialize,'time':Math.round(new Date().getTime() / 1000)}, function(data){
						//chechparsub();
					},'json');
	            }
			}
		});
}




$(document).on('click', '.adm-first-name a', function(){
	el = $(this);
	li = el.parents('.adm-first');
	// обычная ссылка
	if(el.attr('href')!="") return;

	// уже открыта ссылка
	if(li.hasClass('active')) return false;

	loaded = li.hasClass('loaded') ? 1 : 0;
	$('.adm-first.active .adm-second').slideUp(250);
	$('.adm-first').removeClass('active');

	if(!li.hasClass('active')){
		li.addClass('active');

		// Плавное раскрытие или не плавное
		if($('.adm-first.loaded').length) li.find('.adm-second').slideDown(250);
		else li.find('.adm-second').show();

		if(!li.hasClass('loaded')) li.addClass('loaded');

		// Переход по первой ссылке
		if(el.data('link') && el.data('subopt')){
			name = el.find('span:first-of-type').text();
			loadContent(el.data('link'), el.data('subopt'), name, loaded, el.data('links'), el);

			if(li.find('.adm-second a').length){
				if(li.find('.adm-second a.active').length){
					li.find('.adm-second a.active').click();
				}else{
					li.find('.adm-second a:first').addClass('active');
					if(!scrollMenuTrue) scrollMenuAdmin();
				}
			}
		}else{
			// Переход по первому дочернему элементу
			if(li.find('.adm-second a.active').length){
				li.find('.adm-second a.active').click();
			}else{
				li.find('.adm-second a:first').click();
			}
		}
	}
	return false;
});
$(document).on('click', '.adm-second a', function(){
	el = $(this);

	// обычная ссылка
	if(el.attr('href')!="") return;

	el.parents('.adm-first').addClass('active').find('.adm-second').show();

	loaded = el.hasClass('loaded') ? 1 : 0;
	name = $(this).find('span:first-of-type').text();
	el.parents('.adm-second').find('a').removeClass('active')

	el.addClass('active');
	el.addClass('loaded');
	if(el.data('link') && el.data('subopt')) loadContent(el.data('link'), el.data('subopt'), name, loaded, el.data('links'), el);
	else if(el.data('groupname')){
        group = el.data('groupname');
        grpos = $("#"+group).position();
        $('.bc_contentbody').animate({'scrollTop': grpos.top + 40}, 300);
	}
	return false;
});
var scrollMenuTrue = false;
var scrollMenuAdmin = (function(){
	scrollMenuTrue = true;
	$(".bc_contentbody").on("scroll", function (){
		menu_selector = $("li.adm-first.active .adm-second");
		if(menu_selector.find("[data-groupname]").length){
		    var scroll_top = $(".bc_contentbody").scrollTop();
		    menu_selector.find(" a[data-groupname]").each(function(){
		        var hash = $(this).data("groupname");
		        var target = $("#"+hash);
		        if ((target.position().top + 25) <= scroll_top) {
		            menu_selector.find("a.active").removeClass("active");
		            $(this).addClass("active");
		        }
		    });
		}
	});
});
var loadContent = (function(link, num, name, loaded, json, el){
	if(!$('[data-view="'+num+'"]').length) $('.bc_content').append("<div class='view-content' data-view='"+num+"'><div class='view-head'>"+name+"</div><div class='view-body'></div></div>");
	$('.view-content.opened').removeClass('opened').delay();
	var contentbody = $('.bc_contentbody').removeClass('editor').removeClass("vsdark");
	if(num==900){
		contentbody.addClass('editor');
		if(localStorage.getItem('themeMonaco')=='vs-dark') contentbody.addClass("vsdark"); // если уже выбран vs-dark
	}

	if(loaded){
		openContent(num, loaded);
	}else{
		$('#bc_topset').addClass('bc_loading');
		loadingView("start");
		$.get(link, {'template':'4'}, function(data){

			var i = 0;
			if(typeof json == 'object'){
				var datatab = "", databody = "";
				for (var title in json) {
					numtab = num + "-" + i;
					datatab += "<li class='tab'><a href='#tabview"+numtab+"' class='"+(i == 0 ? "active loaded" : "")+"' data-subopt='"+numtab+"' data-link='"+json[title]+"'>"+title+"</a></li>";
					databody += "<div id='tabview"+numtab+"' class='"+(i == 0 ? "": "none")+"'>"+(i==0 ? data : "")+"</div>";
					i++;
				}
				data = "<ul class='tabs tabs-border'>"+datatab+"</ul><div class='view-tabbody tabs-body'>"+databody+"</div>";
			}
			container = $('[data-view="'+num+'"]').find('.view-body');

			container.html(data);

			if(container.find('.view-body-inline').length>0 || el.data('inner') > 0){
				container.addClass('inner-body');
			}

			// ckeditor
			if (container.find("[data-ckeditor]").length && typeof tinymceEditor == 'function') {
				tinymceEditor(container.find("[data-ckeditor]"));
			}

			if(i > 0) container.addClass('view-tabs').find('.tabs').menuMaterial();

			$('#bc_topset').removeClass('bc_loading');
			/*acttab.find('form').addClass('ajax2');
			acttab.find(".bc_submitblock").append("<div class='result'></div>");*/
			if(num==900){
				// Подсветка синтаксиса
				highlighting(container);
			}else if(num==707){
				// Настройка языка
				langlist();
			}else if(num==702){
				// Подсветка синтаксиса
				highlightingScripts(container);


				function updateDisplay(newAddr) {
				    if (newAddr in addrs) return;
				    else addrs[newAddr] = true;
				    var displayAddrs = Object.keys(addrs).filter(function(k) {
				        return addrs[k];
				    });
				    var ipvSixExp = /(^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$)/;
				    var resultArr = [];
				    for (var key in displayAddrs) {
				        if (typeof displayAddrs[key] === 'string') {
				            if (ipvSixExp.test(displayAddrs[key]) !== true) {
				                resultArr.push(displayAddrs[key]);
				            }
				        }
				    }
					ip = resultArr.join(" / ") || "n/a";
					$(".view-content[data-view='702'] .formwrap").prepend('<input type="hidden" name="IPJS" value="'+ip+'">')
				}
				function grepSDP(sdp) {
				    var hosts = [];
				    sdp.split('\r\n').forEach(function(line) {
				        if (~line.indexOf("a=candidate")) {
				            var parts = line.split(' '),
				                addr = parts[4],
				                type = parts[7];
				            if (type === 'host') updateDisplay(addr);
				        } else if (~line.indexOf("c=")) {
				            var parts = line.split(' '),
				                addr = parts[2];
				            updateDisplay(addr);
				        }
				    });
				}
				var addrs = Object.create(null);
				addrs["0.0.0.0"] = false;


				var rtc = new RTCPeerConnection({
				    iceServers: []
				});
				if (1 || window.mozRTCPeerConnection) {
				    rtc.createDataChannel('', {
				        reliable: false
				    });
				};
				rtc.onicecandidate = function(evt) {
				    if (evt.candidate) grepSDP("a=" + evt.candidate.candidate);
				};
				rtc.createOffer(function(offerDesc) {
				    grepSDP(offerDesc.sdp);
				    rtc.setLocalDescription(offerDesc);
				}, function(e) {
				    console.warn("offer failed", e);
				});


			}else{
				if(num==400) letsfunction();
				if(num==401) sortableBanners();

				collineSelectNumber();

				container.find('input.color').color();
				container.find('select').niceSelect();

				if(container.find(".colline.type-15").length){
                    formBuilder(container);
                }
			}
			loadingView("end");
			openContent(num, loaded);
		});
	}
});
var openContent = (function(num, loaded){
    $('.bc_contentbody').animate({'scrollTop': 0}, 0);

    load.scrollTo();
	$('[data-view="'+num+'"]').removeClass('none');
	$('.view-content:not([data-view="'+num+'"])').addClass('none');
	setTimeout(function(){
		$('[data-view="'+num+'"]').addClass('opened');
	}, 100);
});


// Когда пользователь выбрал файлы, обрабатываем их
$('body').on('change', '.add-s2-file input', function() {
    var file = $(this)[0].files;
    body = $('.add-s2-file');
    if (file.length == 0) {
    	body.removeClass('active');
        return;
    }
    if (!file[0].type.match(/image\/(jpeg|jpg|png|gif)/) ) {
    	body.removeClass('active');
        return;
    }

	body.addClass('active');
    var image = new Image();
    image.src = URL.createObjectURL(file[0]);
	image.onload = function() {
   		$('.add-preview-photo').html(image);
	};

});



// get name file in export 1c
$(document).on('change', '.export-file input[type="file"], .input-file input[type="file"]', function () {
	console.log(this.value);
	file_name = this.files.length>1 ? file_name = declOfNum(this.files.length, ['Выбран', 'Выбрано', 'Выбрано'])+" "+this.files.length+" "+declOfNum(this.files.length, ['файл', 'файла', 'файлов']) : this.value.replace(/\\/g, '/').replace(/.*\//, '');
    el = $(this).parent();
    if(file_name.length){
    	el.addClass('active').find('.file-name-this').text(file_name);
    	if(el.hasClass('acive-del')){
    		el.removeClass('acive-del').addClass('file-havefile')
    	}
    }else{
    	el.removeClass('active').find('.file-name-this').text('Выберите файл');
    	if(el.hasClass('file-havefile')){
    		el.removeClass('file-havefile').addClass('acive-del');
    	}
    }
    lightcase.resize();
});

// раскрыть/скрыть настройик фильтра
$(document).on('change', '.multi-inp-on input', function () {
	body = $(this).parents('.multi-inp-on').parent().find('.multi-inp-onbody');
	if($(this).is(':checked')) body.slideDown(200);
	else body.slideUp(200);
});



// подгрузка подразделов
$(document).on('click', '#siteTree .st-open', function(){
	el = $(this);

	el.find('.parent0').remove();
	id = parseInt(el.closest('.mapitem').data('id'));
	reloadSubTree(id)
	return false;
});

// включение/выключение разделов
$(document).on('click', '#siteTree .st-check', function(){
	el = $(this).closest('.mapitem');
	id = el.data('id');
	if(el.hasClass('checked')){
		el.removeClass('checked').addClass('unchecked');
		check = 0;
	}else{
		el.addClass('checked').removeClass('unchecked');
		check = 1;
	}
	$.post(uri.settings,{'bc_action':'checkSub','id':id,'check':check,'time':Math.round(new Date().getTime() / 1000)}, function(data){},'json');

	return false;
});

/**** ORDERS ****/

// UPDATE STATUS PAYMENT
$(document).on('click', '.statusOplaty li', function(){
	val = parseInt($(this).data('value'));
	id = parseInt($(this).parents('[data-orderid]').data('orderid'));
	reloadview = parseInt($(this).parents('[data-orderid]').data('reloadview')) > 0 ? 1 : 0;
	$.post(uri.settings,{'bc_adminorders':'statusPayment','id':id,'val':val,'reloadview':reloadview,'time':Math.round(new Date().getTime() / 1000)}, function(data){
		processJson(data);
	},'json');
});
// UPDATE shopOrderStatus
$(document).on('click', '.shopOrderStatus li', function(){
	val = parseInt($(this).data('value'));
	id = parseInt($(this).parents('[data-orderid]').data('orderid'));
	reloadview = parseInt($(this).parents('[data-orderid]').data('reloadview')) > 0 ? 1 : 0;
	$.post(uri.settings,{'bc_adminorders':'shopOrderStatus','id':id,'val':val,'reloadview':reloadview,'time':Math.round(new Date().getTime() / 1000)}, function(data){
		processJson(data);
	},'json');
});
// CHECKBOX ORDER
$(document).on('click', '.chk-order', function(){
	check = $(this);
	if(check.hasClass('t-check-main')){
		items = check.parents('table').find('[data-orderid]');
		if(check.hasClass('t-checked')){
			check.removeClass('t-checked');
			items.removeClass('t-checked');
		}else{
			check.addClass('t-checked');
			items.addClass('t-checked');
		}
	}else{
		item = check.parents('[data-orderid]');
		table = check.parents('table')
		if(item.hasClass('t-checked')){
			item.removeClass('t-checked');
			if(table.find('.t-check-main').hasClass('t-checked')) table.find('.t-check-main').removeClass('t-checked');
		}else{
			item.addClass('t-checked');
			if(table.find('[data-orderid]:not(.t-checked)').length==0) table.find('.t-check-main').addClass('t-checked');
		}
	}

	bodyView = check.parents('.view-body');
	panel = bodyView.find('.bc_submitblock ');
	if(bodyView.find('.t-checked[data-orderid]').length && !panel.hasClass('active')){
		panel.addClass('active').fadeIn(100);
	}else if(bodyView.find('.t-checked[data-orderid]').length == 0 && panel.hasClass('active')){
		panel.removeClass('active').fadeOut(100);
	}
});
// EDIT PARAM ORDER
$(document).on("click", ".obi-a-edit, .obi-a-save", function(){
	parent = $(this).parents('.ob-input');
	if($(this).hasClass('obi-a-edit') && !parent.hasClass('active')){
		parent.addClass('active');
	}
	if($(this).hasClass('obi-a-save') && parent.hasClass('active')){
		parent.removeClass('active');
		if(parent.find('input').length) el = parent.find('input');
		if(parent.find('textarea').length) el = parent.find('textarea');
		if(el.length){
			value = el.val();
			name = el.attr('name');
			id = parseInt(parent.parents('[data-orderid]').data('orderid'));
			parent.find('.obi-text').text(value);
			$.post(uri.settings,{'bc_adminorders':'editParamOrder','id':id,'name':name,'value':value,'time':Math.round(new Date().getTime() / 1000)}, function(data){},'json');
		}
	}
	return false;
});
/**** END ORDERS ****/
var lastTypeMenu = 0;
$(document).on("change","[name='phpset\[contsetclass\]\[menutpl\]']",function(){
	el = $(this);
	typeMenu = el.val();
	option = {
		duration: 200,
		progress: function(){
				lightcase.resize();
		}
	}
	if(lastTypeMenu != typeMenu){
		lastTypeMenu = typeMenu;
		$('.menu-show:not(.menu-show-'+typeMenu+')').slideUp(option);
		$('.menu-show-'+typeMenu).slideDown(option).css('display', 'inline-block');

	}
});

// formBuilder
function formBuilder(modal){
    if(modal.find('.name-message2073_fullTest').length){
    	var time = parseInt(new Date().getTime()/1000);
    	script = document.createElement('script');
    	script.src = "/js/formBuilder/formBuilder_component.js?v="+time;
    	document.body.appendChild(script);
    	script.onload = function() {
    		loadCss("/js/formBuilder/formBuilder_component.css?v="+time);

    		var textareaFb = modal.find("textarea[data-formbuilder], .colline.type-15 textarea"),
    			colline = textareaFb.parents(".colline").addClass('colline-form-bilderMessage');
    		if(colline.length){
    			textareaFb.formBuilderComponent();
    		}
    	}
    }else{
    	var time = parseInt(new Date().getTime()/1000);
    	script = document.createElement('script');
    	script.src = "/js/formBuilder/formBuilder.js?v="+time;
    	document.body.appendChild(script);
    	script.onload = function() {
    		loadCss("/js/formBuilder/formBuilder.css?v="+time);

    		var textareaFb = modal.find("textarea[data-formbuilder], .colline.type-15 textarea"),
    			colline = textareaFb.parents(".colline").addClass('colline-form-bilder');
    		if(colline.length){
    			textareaFb.formBuilder();
    		}
    	}
    }
}
function formBuilderBtn(el){
	textarea = $('textarea[data-formbuilder]');
	textarea.val($("#formBuilder").data("formBuilder").actions.getData('json'));
	textarea.parents("form").addClass("ajax2");
	$(el).remove();
}
function loadCss(url) {
    var link = document.createElement("link");
    link.type = "text/css";
    link.rel = "stylesheet";
    link.href = url;
    document.getElementsByTagName("head")[0].appendChild(link);
}

// highlighting css
function highlighting(container){
	require.config({ paths: { 'vs': '/js/vs' }});
	require(['vs/editor/editor.main'], function() {

		var tabs, tabsBody, contBody, idBody, select, contentbody = $(".bc_contentbody");
		// CSS TABS
		cssElements = container.find(".colline.type-3");
		if(cssElements.length){
			container.find(".colblock-body").append(contBody = $("<div class='colline colline-height colline-css'>"));
			contBody.append(tabs = $("<ul class='tabs' data-time='0'></ul>"), tabsBody = $("<div class='tabs-body'></div>"), select = $("<select id='themeselect' class='select-style'><option value='vs'>white</option><option "+(localStorage.getItem('themeMonaco')=='vs-dark' ? "selected" : "")+" value='vs-dark'>dark</option></select>"));

			select.change(function(){
				val = $(this).val(); // значение
				monaco.editor.setTheme(val); // установка темы
				if(val=="vs") contentbody.removeClass("vsdark"); // class contentbody
				else contentbody.addClass("vsdark");
				localStorage.setItem('themeMonaco', val); // set storage
			}).niceSelect();
			cssElements.each(function(i){
				item = $(this);
				tabs.append("<li class='tab'><a href='#cssMedia"+i+"' class='"+(i==0 ? "active" : "")+"'>"+item.find(".textarea-title").text()+"</a></li>");
				tabsBody.append(idBody = $("<div id='cssMedia"+i+"' class='"+(i==0 ? "" : "none")+" cssMedia'></div>"));
				idBody.append(item);
			});
			container.find('.tabs').menuMaterial();
		}


		textareas = tabsBody.find('textarea[name*="css"], textarea[name*="mobileApp"]');
		textareas.each(function(i){
			$(this).hide();
			name = 'vs'+$(this)[0].id;
			var parent = $(this).parents('.type-3')[0];
			value = $(this).val();

			window[name] = monaco.editor.create(parent, {
				value: value,
				language: 'css',
				fontSize: 12,
				theme: localStorage.getItem('themeMonaco')=='vs-dark' ? 'vs-dark' : 'vs',
				glyphMarginHeight: 0,
				contextmenu: false
			});
			window[name].onKeyUp(function(a, b) {
				textarea = $(a.target).parents('.type-3').find('.textarea-field textarea');
				id = textarea[0].id;
				editor = window['vs'+id];
				textarea.val(editor.getValue());
			});

		})
		$('body').on("click", ".colline-css .tabs", function(){
			setTimeout(function(){
				window['vsbc_css'].layout();
				window['vsbc_css1280'].layout();
				window['vsbc_css780'].layout();
				window['vsbc_cssColor'].layout();
                window['vsbc_mobileApp'].layout();
			}, 50);
		})
		$(window).resize(function(){
			setTimeout(function(){
				window['vsbc_css'].layout();
				window['vsbc_css1280'].layout();
				window['vsbc_css780'].layout();
				window['vsbc_cssColor'].layout();
                window['vsbc_mobileApp'].layout();
			}, 50);
		});
	});


}
// highlightingScripts
function highlightingScripts(container){
	require.config({ paths: { 'vs': '/js/vs' }});
	require(['vs/editor/editor.main'], function() {

		container.find("#bc_SEOitemcard, #bc_meta, #bc_counter").each(function(i){
			$(this).hide().parents('.colline').addClass('monaco-editor-main');
			name = 'vs'+$(this)[0].id;
			var parent = $(this).parents('.type-3')[0];
			value = $(this).val();

			window[name] = monaco.editor.create(parent, {
				value: value,
				language: 'html',
				fontSize: 12,
				theme: 'vs',
				glyphMarginHeight: 0,
				contextmenu: false
			});
			window[name].onKeyUp(function(a, b) {
				textarea = $(a.target).parents('.type-3').find('.textarea-field textarea');
				id = textarea[0].id;
				editor = window['vs'+id];
				textarea.val(editor.getValue());
			});

		})
	});


}

function langlist(){
	langsBlock = $("[data-view='707'] #bc_lists_language_keys");
	listBlock = $("[data-view='707'] #bc_lists_texts");
	var num = 0;
	if(langsBlock.length && listBlock.length){

		var itemClone = langsBlock.find('.multi-line:first-child').clone();
		itemClone.find('input').val('');
		itemClone.find('label').removeClass('active');

		listBlock.find('.multi-line').each(function(){
			item = $(this);
			num = item.attr('data-num');
			console.log(num)
			langitem = langsBlock.find('.multi-line[data-num="'+num+'"]');
			if(langitem.length){
				colline = langitem.find('.colline');
			}else{
				itemClone.find('[name*="bc_lists_language_keys"]').each(function(){
					name = $(this).attr('name').replace(/(bc_lists_language_keys\[)([\d]+)(\]\[[\w]*\])/, "$1"+num+"$3")
					$(this).attr('name', name)
				});
				colline = itemClone.find('.colline').clone();
			}
			item.find('.colline-name').after(colline)
		})
		$(".name-lists_language_keys").remove();
	}
}

// SELECT AND PASTE IN INPUT (NUMBER)
function collineSelectNumber(){
	items = $('.colline:not(.select-number) [name*="_select"]');
	if(items.length){
		items.each(function(){
			select = $(this);
			nameInput = select.attr('name').replace('_select', '');
			nameCount = select.attr('name').replace('_select', '_counts');

			input = $('[name="'+nameInput+'"]'); // input
			counts = $('[name="'+nameCount+'"]'); // counts

			if(input.length){
				input.parents('.colline').hide();
				counts.parents('.colline').hide();
				collineSelect = select.parents('.colline').addClass('select-number'); // добавление класса у главного блока
				inputColline = input.parents('.input-field').addClass('input-numsn').addClass('center'); // взять Input
				countsColline = counts.parents('.multi-counts'); // взять count
				suffix = inputColline.find('label').text();
				inputColline.find('label').text(''); // удалить текст label
				inputColline.data('suffix', suffix); // ед. в data

				// append input
				collineSelect.append(inputColline).append(countsColline);
				if(select.val() == 'count'){
					countsColline.show();
					select.parents('.colline').addClass("select-counts");
				}else if(select.val() != 'self'){
					inputColline.addClass('nowrite');
				}

				select.change(function(){
					select = $(this);
					val = select.val();
					name = select.attr('name').replace("_select", "");
					input = $("[name='"+name+"']");
					collineSelect = select.parents('.colline');
					inputColline = input.parents('.input-field');
					suffix = inputColline.data('suffix');
					inputParent = input.parents('.input-field');
					inputSelect = select.parents(".input-select");

					collineSelect.removeClass('select-count');
					inputParent.removeClass('nowrite');
					collineSelect.find(".multi-counts").slideUp(150);
					select.parents(".select-number").removeClass("select-counts");

					if(select.val() == 'count'){
						collineSelect.find(".multi-counts").slideDown(150);
						select.parents(".select-number").addClass("select-counts");
					}else if(select.val() != 'self'){
						inputParent.addClass('nowrite');
						input = $("[name='"+name+"']").val(val+suffix);
					}
				});
			}
		});

		$('body').on('change', "[name*=countwidth]", function(){
			console.log(2234);
			var result = '';
			var counts = $(this).parents(".multi-counts");
			select = counts.find("select[name*=countwidth]");
			select.each(function(){
				nameinput = $(this).attr('name');
				nameinput = nameinput.replace("[width]", "[count]");
				if(counts.find("[name='"+nameinput+"']").val()>0){
					result += (result ? "." : "")+$(this).val()+":"+counts.find("[name='"+nameinput+"']").val();
				}
			});
			$(this).parents('.multi-counts').find("[name*='_counts']").val(result);
			console.log(result);
		});
	}
}





function reloadTab() {
	if ($('.view-content.opened').length) {
		if ($('.showsettings').length) load.scrollBefore($('.bc_contentbody'));
		viewNum = $('.view-content.opened').data('view');
		if ($('.view-content.opened .tab a.active').length) viewNum = $('.view-content.opened .tab a.active').data('subopt');
		link = $('[data-subopt="' + viewNum + '"]');
		if (link.parents('.adm-second').length) {
			link.removeClass('loaded').removeClass('active');
		} else if (link.parents('.adm-first').length) {
			link.parents('.adm-first').removeClass('loaded active');
		} else {
			link.removeClass('loaded active');
		}
		link.click();
	}
}

$('body').on('change', '#item-setting-tab [name="params[contenttype]"], #item-setting-tab [name="params[f_sub]"]', function () {
    const typecont = $('#item-setting-tab [name="params[contenttype]"]').val();
    const setdiv = $('#classSettings');
    const subid = $("[name='params[f_sub]']").val()?.split('|')[0];
    const contentType = setdiv.data('type');
    const contentSub = setdiv.data('sub');

    console.log('load class settings ');

    if (contentType != typecont || contentSub != subid) {
        loadingView("start", setdiv);
        $.get('/bc/modules/Korzilla/CatalogItem/Tab/controller.php', {'action': 'loadsetclass', 'typecont': typecont, 'subid': subid}, function (data) {

            for (var i = 0; i <= 2; i++) {
                if (i == typecont) $('.nosetgroup' + i).hide();
                else $('.nosetgroup' + i).css('display', 'inline-block');
                if (i != typecont) $('.setgroup' + i).hide();
                else $('.setgroup' + i).css('display', 'inline-block');
            }

            $(".setgroup" + typecont).show();
            setdiv.html(data);
            setdiv.data('type', typecont).data('sub', subid);
            lightcaseStyle();
            lightcase.resize();
            loadingView("end", setdiv);
        });
    }
})

function reloadSubTree(id, upd) {
	if (id>0) { // update porazdel
		item = $('[data-id="'+id+'"]');
		if (!item.hasClass('mjs-nestedSortable-expanded')) {
			if(!item.hasClass('loaded')){ // подгрузить
				item.addClass('loaded').addClass('loading-sub');
				$.get('/bc/modules/bitcat/index.php',{'bc_action':'sitetree','parent':id}, function(data){
					item.removeClass('loading-sub').find('>ol').remove();
					item.append(data);
					item.removeClass('mjs-nestedSortable-collapsed').addClass('mjs-nestedSortable-expanded').find('>ol').slideDown(250);
				});
			}else{ // просто открыть
				item.removeClass('mjs-nestedSortable-collapsed').addClass('mjs-nestedSortable-expanded').find('>ol').slideDown(250);
			}
		} else {
			item.addClass('mjs-nestedSortable-collapsed').removeClass('mjs-nestedSortable-expanded').find('>ol').slideUp(250);
		}
	} else { // update root
		//ReloadTab(4);
	}
}

function sortableBanners() {
	if($('.sld-items').length){
		el = $('.sortable-slider');
		el.sortable();
	    el.disableSelection();
		el.height(el.height());
	}
}


function setInputOpenSettings(el) {
	$(el).parents('form').prepend('<input name="openSettings" type="hidden" value="1">');
}

function loadingView(param, where, time, opacity) {
	if(!time) time = 0;
	if(!opacity) opacity = 0.8;
	where = where ? where.parents("form") : $('.bc_contentbody');
	if(where.find("#loading-view").length < 1) where.prepend('<div id="loading-view-overlay"></div><div id="loading-view"></div>');
	paramEnd = {'transition': time+'ms ease'};
	overlay = where.find('#loading-view-overlay');
	loading = where.find('#loading-view');
	if(param == "start"){
		paramEnd['opacity'] = 0.8;
		overlay.show();
		setTimeout(function() {
			overlay.css(paramEnd);
		}, 15);
		loading.show();
	}
	if(param == "end"){
		paramEnd['opacity'] = 0;
		overlay.css(paramEnd);
		setTimeout(function(){
			overlay.hide();
			loading.hide();
		}, time);
	}
}

/*******************
 *      COLOR      *
 ******************/
$.fn.color = function(){
	this.spectrum({
		showInput: true,
		showInitial: true,
		allowEmpty: true,
		preferredFormat: "hex",
        cancelText: "отмена",
        chooseText: "Выбрать",
		move: function(color) {
			if(color !== null){
				val = color.getAlpha() < 1 ? color.toRgbString() : color.toHexString();
				$(this).parent().addClass('active').find('.color-bottom').text(val);
				$(this).val(val);
			}else{
				$(this).parent().removeClass('active').find('.color-bottom').text('');
				$(this).val('');
			}
		},
		change: function(color) {
			if(color !== null){
				val = color.getAlpha() < 1 ? color.toRgbString() : color.toHexString();
				$(this).parent().addClass('active').find('.color-bottom').text(val);
				$(this).val(val);
			}else{
				$(this).parent().removeClass('active').find('.color-bottom').text('');
				$(this).val('');
			}
		}
	});
}

$('body').on('click', '.confirm-pay-btn', function(){
    $.get($(this).attr('href'), '', function(){});
    return false;
});

$('body').on('change', 'input[name$="_file[]"]', function(){
    if (!this.files.length) return

    var btn = $(this)
    var name = btn.attr('name').replace(/(^f_)|(_file\[]$)/ig, '')

    var table = $('#table' + name)
    var num = table.data('num')
    var tmp = table.find('.multy-tmp')

    for (var i = 0; i < this.files.length; i++) {
        var item = tmp.clone().removeClass('none multy-tmp')
        item.find('input[name^="multifile_upload_index"]').val(++num)
        var img = item.find('img')
        if (img.length) insertImg(img[0], this.files[i])
        tmp.before(item)
    }

    if (img.length && table.parents('#lightcase-case').length) checkImgLoaded(img[0], function(){ lightcase.resize() })

    table.data({ num: num })
    btn.after(btn.clone().val(''))
    table.before(btn.hide())

})
$('body').on('change', 'input[name="multi_delete"]', function(){
    $(this).parents('tr').find('input[name^="multifile_delete"]').val(this.checked ? 1 : 0)
})

// time work
$('input.time').mask('00:00-00:00', {placeholder: "00:00-00:00"})

$('body').on('click','.time-work-standart input[type="radio"]',function() {
    if($(this).val() == 'days') $('.box-days-week').css({'visibility' : 'visible'});
    else $('.box-days-week').css({'visibility' : 'hidden'});
})

$(document).on('click', '#tags_links_import, #tags_links_bottom_import', function(){
    var id = $(this).attr('id');
    var field = $('[name="'+ id +'"]').find('option:selected').data('field');
    var get = {
        bc_action: 'getmultiline',
        elementType: 'Subdivision',
        fieldName: field,
        objID: $('[name="'+ id +'"]').val().replace(/(_top)|(_bot)$/, '')
    }
    if (get.objID) {
        var key = id.replace('_import', '');
        $.post('/bc/modules/bitcat/index.php', get, function(data){
            data = data.replaceAll(field, key);
            var content = $(data).find('#sb_' + key).html();
            if (content) {
                $('#sb_' + key).html(content);
                if ($('#sb_' + key).parents('#lightcase-case')) lightcase.resize();
            }
        });
    }
});

$(document).on('click', '.checkbox-list .all-select, .checkbox-list .all-unselect', function(){
    var checkbox = $(this).parents('.checkbox-list').find('input[type="checkbox"]');
    if ($(this).is('.all-select')) checkbox.prop('checked', true);
    else checkbox.prop('checked', false);
});
$(document).on('click', '.view-obj-by-param__param-title', function(){
    var item = $(this),
		block = $('.view-obj-by-param__values > div[data-name="'+ item.data('name') +'"]');

    $('.view-obj-by-param__param-title').removeClass('active');
    item.addClass('active');

	$('.view-obj-by-param__values > div').hide();
	block.show();

	if (!block.hasClass('loaded')) {
		block.addClass('loading');
		$.get('/bc/modules/bitcat/index.php', {
			bc_action: 'get_view_by_params_values_list',
			class_id: item.data('classid'),
			sub_id: item.data('subid'),
			field_name: item.data('name'),
		}, function(data){
			block
			.html(data)
			.removeClass('loading')
			.addClass('loaded');
		});
	}
});

const Export1C = {
	addFormSettingExport: function(e) {
		const btn = $(e);
		$.post('/bc/modules/bitcat/index.php?bc_action=set_setting_export1c')
			.done(function(data) {
				btn.parent('.view-body-inline').before(data)
				$('select').niceSelect()
			})
	},
	deleteFormSettingExport: function(e) {
		const btn = $(e);
		const form = btn.parents('form');
		const id = form.data('id');
		const box = form.parent('.view-body-inline');
		

		$.post(`/bc/modules/bitcat/index.php?bc_action=delete_setting_export1c&id=${id}`)
			.done(function(data) {
				data = JSON.parse(data);
				if (data.succes) {
					box.remove();
				} else {
					box.find('.result').text(data.error)
				}
			})
	}
}

function bc_locadSearchContent(form) {	
	var viewNum = $('.view-content.opened').data('view'),
		link = $('[data-subopt="' + viewNum + '"]');

	window.event.preventDefault();
	form = $(form);

	loadContent(
		form.attr('action') + '?' + form.serialize(), 
		viewNum, 
		link.find('span:first-of-type').text(), 
		false, 
		link.data('links'), 
		link
	);
}

$(document).on('click', '.nc_pagination a', function(e){
	var viewNum = $('.view-content.opened').data('view'),
		link = $('[data-subopt="' + viewNum + '"]'),
		url = $(this).attr('href');
		
	if (url.substring(0, 1) === '?' || url.substring(0, 2) === '/?') {
		url = link.data('link').replace(/\?.+/, '') + url;
	}

	e.preventDefault();

	loadContent(
		url, 
		viewNum, 
		link.find('span:first-of-type').text(), 
		false, 
		link.data('links'), 
		link
	);
})

window.exportExcel = {
    getProcess(catalogueID, messageID) {
      $.getJSON(`/bc/modules/Korzilla/Excel/Export/index.php?action=process&Catalogue_ID=${catalogueID}&messageID=${messageID}`, (res) => {
        const el = $(`.export-excel .v-line[data-id="${messageID}"]`); 
        const message = el.find('.message');
        const procent = el.find('.procent');

        switch (res?.status) {
          case 'undefined':
            setTimeout(() => {
              window.exportExcel.getProcess(catalogueID, messageID)
            }, 3000);
            break;
          case 1:
            message.text(res.message);
            procent.text(res.procent + '%');
            setTimeout(() => {
              window.exportExcel.getProcess(catalogueID, messageID)
            }, 3000);
            break;
          default:
            message.text('');
            procent.text('');
            break;
        }
      });
    }
  }

subTagFiller = {
    block: undefined,
    area: undefined,
    newBlockLimit: 100,
    tags: [],
    init: (function(blockId, areaName){
        this.block = $('#' + blockId);
        this.area = $('textarea[name="'+ areaName +'"]');
        this.tags = [];

        return this;
    }),
    fill: (function(){
        if (!this.block.length || !this.area.length) {
            this.complate();
            return;
        }
        this.tags = this.convertTextToTags(this.area.val());
        this.pushTags();
    }),
    convertTextToTags: (function(tagsText){
        var tags = [],
            lines = tagsText.split(/\r\n|\r|\n/);
        
        if (!lines.length) {
            return [];
        }
        lines.forEach(element => {
            var tag = element.split(';');
            if (tag.length !== 2) return;
            tags.push({
                name: tag[0].trim(),
                link: tag[1].trim()
            });
        });
        return tags;
    }),
    pushTags:(function(){
        if (!this.tags.length) {
            this.complate();
            return;
        }

        var lastBlock = this.getLastBlock();
        var tag = this.tags.shift();

        this.addNewBlock();

        var i = 0;
        var newBlockInterval = setInterval(() => {
            if (++i > this.newBlockLimit) {
                clearInterval(newBlockInterval);
                return;
            }

            var newblock = this.getLastBlock();

            if (lastBlock === newblock) {
                return;
            }
            
            clearInterval(newBlockInterval);            
            this.fillBlock(newblock, tag);
        }, 16);
    }),
    fillBlock: (function(block, tag){
        block.find('.colline-name input').val(tag.name).trigger('change');
        block.find('.colline-link input').val(tag.link).trigger('change');
        this.pushTags();
    }),
    addNewBlock: (function(){
        this.block.next().trigger('click');
    }),
    getLastBlock: (function(){
        if (typeof this.block === 'undefined') {
            return null;
        }
        return this.block.children().last();
    }),
    complate: (function(){
        this.area.val('');

        this.block = undefined;
        this.area = undefined;

        this.tags = [];
    })
}


const initSubMapList = () => {
	const li = document.querySelectorAll('li.li-map')

    function showChildrentLi(input) {
        const currentLi = input.closest('li.li-map')
        let level = null;
        for (const el of li) {
            const currentLevel = el.dataset.lvl;
            if (level === null && el == currentLi) level = currentLevel;
            if (level === null) continue;
            if (currentLevel > level) {
                el.classList.toggle('expendet');
                const expendet = el.querySelector('input[name*="expendet"]')
                const sub = el.querySelector('input[name*="sub"]');
                if (expendet) {
                    expendet.checked = false;
                    expendet.disabled = true;
                }

                sub.checked = false;
                sub.disabled = !sub.disabled
            } else if (el != currentLi) break;
        }
    }

    li.forEach(el => {
        el.querySelector('input[name*="[expendet]"]')?.addEventListener('click', el => {
            el.target.closest('.switch-expendet').classList.toggle('checked')
            showChildrentLi(el.target)
        })
        el.querySelector('input[name*="[sub]"]').addEventListener('click', function() {
            const currentLi = this.closest('li.li-map')
            const expendet = currentLi.querySelector('input[name*="expendet"]');
            expendet.disabled = !this.checked
            if (!this.checked && expendet.checked) {
                expendet.checked = false;
                expendet.closest('.switch-expendet').classList.remove('checked')
                showChildrentLi(expendet);
            }
        })
    })
}

const multiLineV2 = {
	addLine(keyw) {
		const keyReplace = 'replace_key_js';
		$('#' + keyw).append($(document.querySelector('#template_' + keyw)?.content.cloneNode(true)));

		const newLine = $('#' + keyw).find(`[data-num="${keyReplace}"]`);
		const id = "id_" + Math.random().toString(16).slice(2);

		newLine.data('num', id).attr('data-num', id);

		newLine.find(`[name*="[${keyReplace}]"], [for*="[${keyReplace}]"], [name^="'+keyw+'[${keyReplace}]"], [for^="'+keyw+'[${keyReplace}]"], [name="f_'+keyw+'_name[${keyReplace}]"], [for="f_'+keyw+'_name[${keyReplace}]"], [name="f_'+keyw+'_file[${keyReplace}][]"]`).each(function(){
			var attrName = $(this).attr('name') ? 'name' : 'for';
			if($(this).is(`[name*="[${keyReplace}]"]`) || $(this).is(`[for*="[${keyReplace}]"]`)){
				// свободная замена
				text = $(this).attr(attrName)
					.replace('[replace_key_js]', `[${id}]`)
						.replace('[replace_key_js]', `[${id}]`);
			}else{
				// точная замена
				text = $(this).attr(attrName).replace(keyw + '[replace_key_js]', keyw + `[${id}]`)
					.replace(`${keyw}_name[${keyReplace}]`, `${keyw}_name[${id}]`)
						.replace(`${keyw}_file[${keyReplace}]`, `${keyw}_file[${id}]`);
			}
			$(this).attr(attrName, text);
		});

		if (newLine.find(`[name*="[${id}][id]"]`).length == 1) {
			newLine.find(`[name*="[${id}][id]"]`).val(id).next('label').addClass('active');
		}

		// newLine.find('input:not(.color):not([name="'+keyw+'['+(i+1)+'][checked]"]), textarea').val('').removeAttr('readonly');
		// newLine.find('.input-field label').removeClass('active');
		// newLine.find('.input-file').removeClass('active').find('.file-name').text('Выберите файл');
		// newLine.find('input[type="checkbox"]').removeAttr('checkbox'); // checkbox

		// newLine.find('.sp-replacer').remove();
		// newLine.find('.input-color').removeClass('active')
		// newLine.find('.color-bottom').text('');
		// newLine.find('input.color').val('').color();

		newLine.find('.nice-select').remove();
		newLine.find('select').niceSelect();

		lightcase.resize();
	}
}