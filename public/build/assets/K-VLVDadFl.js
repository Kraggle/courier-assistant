const O=[],C=O.indexOf,k=function(t){return t!=null&&t===t.window},g={},y=g.toString,m=g.hasOwnProperty,S=m.toString,U=S.call(Object),i={version:"2.0",extend(e,...t){let r,n,s,o,a,l,c={},u=0,f=!1;const p=t.length;for(i.type(e)=="boolean"?(f=e,c=t[u]||{},u++):c=e,i.type(c)!=="object"&&!i.isFunction(c)&&(c={}),u===p&&(c=this,u--);u<p;u++)if(r=t[u],r!=null)for(n in r)s=c[n],o=r[n],c!==o&&(f&&o&&(i.isPlainObject(o)||(a=i.isArray(o)))?(a?(a=!1,l=s&&i.isArray(s)?s:[]):l=s&&i.isPlainObject(s)?s:{},c[n]=i.extend(f,l,o)):o!==void 0&&(c[n]=o));return c},each(e,t){let r,n=0;if(i.isArray(e))for(r=e.length;n<r&&t.call(e[n],n,e[n])!==!1;n++);else for(const s in e)if(t.call(e[s],s,e[s])===!1)break;return e},type(e){return e==null?e+"":typeof e=="object"||typeof e=="function"?g[y.call(e)]||"object":typeof e},isFunction(e){return i.type(e)==="function"},isDate(e){return Object.prototype.toString.call(e)==="[object Date]"},isPlainObject(e){if(!e||y.call(e)!=="[object Object]")return!1;const t=Object.getPrototypeOf(e);if(!t)return!0;const r=m.call(t,"constructor")&&t.constructor;return typeof r=="function"&&S.call(r)===U},isObject(e){return Object.prototype.toString.call(e)==="[object Object]"},isValue(e){return!this.isObject(e)&&!this.isArray(e)},isArray:Array.isArray||function(e){return Object.prototype.toString.call(e)==="[object Array]"},indexOf(e,t){for(let r=0;r<e.length;r++)if(e[r]===t)return r;return-1},urlParam(e){const t={};let r=window.location.href.match(/\?([^#]*)/);return r==null?e?"":{}:(r=r[1].split("&"),i.each(r,function(n,s){s=s.split("="),s[0]&&(t[s[0]]=decodeURI(s[1]))}),e?t[e]||"":t)},addURLParam(e,t){const r=this.urlParam();r[e]=t;const n=new URLSearchParams(r);history.replaceState(null,"","?"+n.toString())},removeURLParam(e){const t=this.urlParam();delete t[e];const r=new URLSearchParams(t);history.replaceState(null,"","?"+r.toString())},curtail(){let e=arguments[0],t=arguments[1];return!Object.keys(e).length&&Object.keys(t).length&&(e=i.extend({},arguments[1]),t=arguments[2]),t&&typeof t!="object"&&(t=[t]),i.each(t,function(r,n){n in e&&delete e[n]}),e},reduce(){let e=arguments[0],t=arguments[1];return!Object.keys(e).length&&Object.keys(t).length&&(e=i.extend({},arguments[1]),t=arguments[2]),t&&typeof t!="object"&&(t=[t]),i.each(e,function(r){!i.has(r,t)&&delete e[r]}),e},inArray:(e,t,r)=>{if(t==null)return-1;if(i.type(e)=="object"){for(let n=0;n<t.length;n++)if(i.equals(t[n],e))return n;return-1}return C.call(t,e,r)},isInArray:(e,t)=>{switch(i.type(t)){case"array":return i.inArray(e,t)!=-1;case"object":return e in t;default:return!1}},local:(e,t)=>{if(t===void 0)return e===void 0?localStorage:JSON.parse(localStorage.getItem(e));localStorage.setItem(e,JSON.stringify(t))},localRemove:e=>localStorage.removeItem(e),isIterable(e){return i.has(i.type(e),["array","object"])},equals:(e,t)=>{const r=i.type(e),n=i.type(t);if(r==n)switch(r){case"array":if(i.empty(e)&&i.empty(t))return!0;if(e.length!=t.length)return!1;for(let s=0,o=e.length;s<o;s++)if(!i.in(e[s],t))return!1;break;case"object":if(i.empty(e)&&i.empty(t))return!0;for(const s in e)if(!i.has(s,t)||!i.equals(e[s],t[s]))return!1;for(const s in t)if(typeof e[s]>"u")return!1;break;case"number":case"string":case"boolean":if(e!=t)return!1;break;case"function":if(e.toString()!=t.toString())return!1;break;default:if(!e!=!t)return!1}else{if(i.empty(e)&&i.empty(t))return!0;switch(r){case"array":case"object":return!1;case"number":return!!(!e&&i.Util.isNull(t));case"string":case"boolean":if(e!=t)return!1;break;default:if(!e!=!t)return!1;break}}return!0},diff:function(){return{CREATED:"created",UPDATED:"updated",REMOVED:"deleted",UNCHANGED:"unchanged",map(e,t){if(i.isFunction(e)||i.isFunction(t))throw"Cannot compare functions";if(i.isValue(e)||i.isValue(t))return{type:this.compare(e,t),data:{new:e,old:t}};const r={};for(const n in e){if(i.isFunction(e[n]))continue;let s;t[n]!==void 0&&(s=t[n]);const o=this.map(e[n],s);o&&(r[n]=o)}for(const n in t)i.isFunction(t[n])||r[n]!==void 0||(r[n]=this.map(void 0,t[n]));for(const n in r)r[n].type==this.UNCHANGED&&delete r[n];return r},compare(e,t){return e===t||i.isDate(e)&&i.isDate(t)&&e.getTime()===t.getTime()?this.UNCHANGED:e===void 0?this.CREATED:t===void 0?this.REMOVED:this.UPDATED}}}(),isNull:e=>i.has(i.type(e),["null","undefined"]),length:e=>{const t=i.type(e);return t=="object"?Object.keys(e).length:t=="array"?e.length:0},empty:e=>{switch(i.type(e)){case"object":return!Object.keys(e).length;case"array":return!e.length;case"number":case"boolean":return!1;default:return!e}},getPropByString:(e,t)=>{if(!t)return e;const r=t.split(".");let n=0;for(let s=r.length-1;n<s;n++){const o=r[n],a=e[o];if(a!==void 0)e=a;else break}return e[r[n]]},isNumeric:e=>i.in(i.type(e),["number","string"])&&!isNaN(parseFloat(e))&&isFinite(e),isBoolean:e=>i.in(e,[0,1,"0","1","true","false",!0,!1]),toBoolean:e=>i.isBoolean(e)?i.isInArray(e,["1",1,!0,"true"]):!!e,parse:e=>(e=i.JSON.parse(e),i.isNumeric(e)?e=parseInt(e):i.type(e)!="boolean"&&i.isBoolean(e)&&(e=e=="true"),e),includeHTML:()=>{let e,t,r,n;const s=document.querySelectorAll("[include]");for(e=0;e<s.length;e++)if(t=s[e],r=t.getAttribute("include"),r){n=new XMLHttpRequest,n.onreadystatechange=function(){this.readyState==4&&(this.status==200&&(t.innerHTML=this.responseText),this.status==404&&(t.innerHTML="Page not found."),t.removeAttribute("include"),i.includeHTML())},n.open("GET",r,!0),n.send();return}},imgToSvg:(e,t)=>{document.querySelectorAll(`img.${e}`).forEach(n=>{const s=n.id,o=n.className,a=n.src,l=n.getAttribute("style"),c=new XMLHttpRequest;c.onreadystatechange=function(){if(this.readyState==4&&this.status==200){const u=this.responseXML.getElementsByTagName("svg")[0];s&&(u.id=s),o&&u.setAttribute("class",o.removeClass("svg-me")),u.removeAttribute("xmlns:a");const f=u.getAttribute("viewBox"),p=u.getAttribute("width"),h=u.getAttribute("height");!f&&p&&h&&u.setAttribute("viewBox",`0 0 ${h} ${p}`),l&&u.setAttribute("style",l),n.replaceWith(u),t&&t.call(u)}},c.open("GET",a,!0),c.send()})},loadStyles:e=>{e.forEach(t=>{const r=document.createElement("link");r.setAttribute("rel","stylesheet"),r.setAttribute("href",t),document.head.appendChild(r)})},loadStylesWithCondition:(e,t)=>e&&i.loadStyles(t),choice:e=>e[i.random(0,e.length)],dateToTimestamp:(e=new Date)=>e.toISOString().replace(/(T|\.\d+Z)/g," ").trim(),generateID(e=10){const t=[],r="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz",n=r.length;for(let s=0;s<e;s++)t.push(r.charAt(Math.floor(Math.random()*n)));return t.join("")},delay:e=>new Promise(t=>setTimeout(t,e)),getValue(e,t){if(i.type(e)!="object")return e;let r;const n=t.split(".");for(t of n)if(r=e[t],i.type(r)!="object")break;return r},now:(e=!0)=>e?new Date().getTime():new Date,random:(e=0,t=100)=>Math.floor(Math.random()*(t-e+1)+e),noCommas:e=>e.toString().replace(/\B(?=(\d{3})+(?!\d))/g,","),noFormatter(e,t){const r=[{value:1,symbol:""},{value:1e3,symbol:"k"},{value:1e6,symbol:"M"},{value:1e9,symbol:"G"},{value:1e12,symbol:"T"},{value:1e15,symbol:"P"},{value:1e18,symbol:"E"}],n=/\.0+$|(\.[0-9]*[1-9])0+$/;let s=r.slice().reverse().find(function(o){return e>=o.value});return s?(e/s.value).toFixed(t).replace(n,"$1")+s.symbol:"0"}};i.JSON={stringify(e){return i.in(i.type(e),["array","object"])?JSON.stringify(e):e},parse(e){try{return JSON.parse(e)}catch{return e}}};i.Util={create:Object.create||function(){function e(){}return function(t){return e.prototype=t,new e}}(),bind(e,t){const r=Array.prototype.slice;if(e.bind)return e.bind.apply(e,r.call(arguments,1));const n=r.call(arguments,2);return function(){return e.apply(t,n.length?n.concat(r.call(arguments)):arguments)}},stamp(e){return e._k_id=e._k_id||++i.Util.lastId,e._k_id},lastId:0,throttle(e,t,r){let n,s;const o=()=>{n=!1,s&&(a.apply(r,s),s=!1)},a=()=>{n?s=arguments:(e.apply(r,arguments),setTimeout(o,t),n=!0)};return a},wrapNum(e,t,r){const n=t[1],s=t[0],o=n-s;return e===n&&r?e:((e-s)%o+o)%o+s},falseFn(){return!1},formatNum(e,t){const r=Math.pow(10,t||5);return Math.round(e*r)/r},trim(e){return e.trim?e.trim():e.replace(/^\s+|\s+$/g,"")},splitWords(e){return i.Util.trim(e).split(/\s+/)},setOptions(e,t){i.has("options",e)||(e.options=e.options?i.Util.create(e.options):{});for(const r in t)e.options[r]=t[r];return e.options},getParamString(e,t,r){const n=[];for(const s in e)n.push(encodeURIComponent(r?s.toUpperCase():s)+"="+encodeURIComponent(e[s]));return(!t||t.indexOf("?")===-1?"?":"&")+n.join("&")},template(e,t){return e.replace(i.Util.templateRe,function(r,n){let s=t[n];if(s===void 0)throw new Error("No value provided for variable "+r);return typeof s=="function"&&(s=s(t)),s})},templateRe:/\{ *([\w_-]+) *\}/g,cookie:{assign(e){for(let t=1;t<arguments.length;t++){const r=arguments[t];for(let n in r)e[n]=r[n]}return e},defaultConverter:{read(e){return e.replace(/(%[\dA-F]{2})+/gi,decodeURIComponent)},write(e){return encodeURIComponent(e).replace(/%(2[346BF]|3[AC-F]|40|5[BDE]|60|7[BCD])/g,decodeURIComponent)}},init(e,t){function r(s,o,a){if(typeof document>"u")return;a=i.Util.cookie.assign({},t,a),typeof a.expires=="number"&&(a.expires=new Date(Date.now()+a.expires*864e5)),a.expires&&(a.expires=a.expires.toUTCString()),s=encodeURIComponent(s).replace(/%(2[346B]|5E|60|7C)/g,decodeURIComponent).replace(/[()]/g,escape),o=e.write(o,s);let l="";for(const c in a)a[c]&&(l+="; "+c,a[c]!==!0&&(l+="="+a[c].split(";")[0]));return document.cookie=s+"="+o+l}function n(s){if(typeof document>"u"||arguments.length&&!s)return;const o=document.cookie?document.cookie.split("; "):[],a={};for(const l of o){const c=l.split("=");let u=c.slice(1).join("=");u[0]==="="&&(u=u.slice(1,-1));try{const f=i.Util.cookie.defaultConverter.read(c[0]);if(a[f]=e.read(u,f),s===f)break}catch{}}return s?a[s]:a}return Object.create({set:r,get:n,remove:function(s,o){r(s,"",i.Util.cookie.assign({},o,{expires:-1}))},withAttributes:function(s){return i.Util.cookie.init(this.converter,i.Util.cookie.assign({},this.attributes,s))},withConverter:function(s){return i.Util.cookie.init(i.Util.cookie.assign({},this.converter,s),this.attributes)}},{attributes:{value:Object.freeze(t)},converter:{value:Object.freeze(e)}})}}};i.cookie=i.Util.cookie.init(i.Util.cookie.defaultConverter,{path:"/"});i.has=i.inArray;i.in=i.inArray;i.isIn=i.isInArray;i.isWindow=k;i.evaluate={"+":(e,t)=>e+t,"-":(e,t)=>e-t,"*":(e,t)=>e*t,"/":(e,t)=>e/t,"<":(e,t)=>e<t,">":(e,t)=>e>t,">=":(e,t)=>e>=t,"<=":(e,t)=>e<=t};i.supportsPassive=!1;try{const e=Object.defineProperty({},"passive",{get:()=>i.supportsPassive=!0});window.addEventListener("testPassive",null,e),window.removeEventListener("testPassive",null,e)}catch{}i.each("Boolean Number String Function Array Date RegExp Object Error Symbol".split(" "),function(e,t){g["[object "+t+"]"]=t.toLowerCase()});const A=/[^\x20\t\r\n\f]+/g;function w(e){return Array.isArray(e)?e:typeof e=="string"?e.match(A)||[]:[]}function d(e){return(e.match(A)||[]).join(" ")}i.isMobile=()=>navigator.userAgent.bMatch(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i);Array.prototype.removeDupes||(Array.prototype.removeDupes=function(){const e=[],t=[];return i.each(this,function(r,n){const s=JSON.stringify(n);i.in(s,e)||(e.push(s),t.push(n))}),t});String.prototype.filter||(String.prototype.filter=function(){const e=this.toString().split(" "),t=[];return e.clean(""),e.length<2?e[0]:(i.each(e,function(r,n){i.in(n,t)||t.push(n)}),t.join(" "))});Array.prototype.clean||(Array.prototype.clean=function(e){for(let t=0;t<this.length;t++)this[t]==e&&(this.splice(t,1),t--);return this});Array.prototype.average||(Array.prototype.average=function(){return this.length?this.reduce((t,r)=>t+r)/this.length:0});String.prototype.bMatch||(String.prototype.bMatch=function(e){return this.toString().match(e)!==null});String.prototype.contains||(String.prototype.contains=function(e="",t=!1){let r=this.toString();return t||(r=r.toLowerCase(),e=e.toLowerCase()),r.indexOf(e)!=-1});String.prototype.space||(String.prototype.space=function(){return this.toString().replace(/([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g,"$1$4 $2$3$5").trim()});String.prototype.titleCase||(String.prototype.titleCase=function(){return this.toString().replace(/(?:^|\s)\w/g,function(t){return t.toUpperCase()})});String.prototype.firstToUpper||(String.prototype.firstToUpper=function(){const e=this.toString();return e.substr(0,1).toUpperCase()+e.substr(1)});String.prototype.equals||(String.prototype.equals=function(e,t=!1){if(i.type(e)!="string")return!1;let r=this.toString();return t||(r=r.toLowerCase(),e=e.toLowerCase()),r==e});String.prototype.addClass||(String.prototype.addClass=function(e){const t=w(e),r=this.toString();let n,s,o,a;if(t.length&&(s=r||"",n=` ${d(s)} `,n)){for(a=0;o=t[a++];)n.indexOf(` ${o} `)<0&&(n+=o+" ");return d(n)}return r});String.prototype.removeClass||(String.prototype.removeClass=function(e){const t=w(e),r=this.toString();let n,s,o,a;if(t.length&&(s=r||"",n=` ${d(s)} `,n)){for(a=0;o=t[a++];)for(;n.indexOf(` ${o} `)>-1;)n=n.replace(` ${o} `," ");return d(n)}return r});String.prototype.get||(String.prototype.get=function(e){const t=this.toString(),r=t.match(e);return i.empty(r)?"":r[0]});Date.prototype.addDays||(Date.prototype.addDays=function(e){return this.setTime(this.getTime()+e*24*60*60*1e3),this});Date.prototype.addHours||(Date.prototype.addHours=function(e){return this.setTime(this.getTime()+e*60*60*1e3),this});Date.prototype.addMinutes||(Date.prototype.addMinutes=function(e){return this.setTime(this.getTime()+e*60*1e3),this});class T{run(t,r){this.time=r,this.callback=t,this._action()}_action(){this.timeout&&clearTimeout(this.timeout),this.timeout=setTimeout(this.callback,this.time)}}function D(){return new T}export{i as K,D as t};
