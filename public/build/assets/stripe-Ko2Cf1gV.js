var w="https://js.stripe.com/v3",g=/^https:\/\/js\.stripe\.com\/v3\/?(\?.*)?$/,p="loadStripe.setLoadParameters was called but an existing Stripe.js script already exists in the document; existing script parameters will be used",b=function(){for(var e=document.querySelectorAll('script[src^="'.concat(w,'"]')),t=0;t<e.length;t++){var n=e[t];if(g.test(n.src))return n}return null},v=function(e){var t="",n=document.createElement("script");n.src="".concat(w).concat(t);var i=document.head||document.body;if(!i)throw new Error("Expected document.body not to be null. Stripe.js requires a <body> element.");return i.appendChild(n),n},E=function(e,t){!e||!e._registerWrapper||e._registerWrapper({name:"stripe-js",version:"3.2.0",startTime:t})},s=null,u=null,d=null,P=function(e){return function(){e(new Error("Failed to load Stripe.js"))}},L=function(e,t){return function(){window.Stripe?e(window.Stripe):t(new Error("Stripe.js not available"))}},j=function(e){return s!==null?s:(s=new Promise(function(t,n){if(typeof window>"u"||typeof document>"u"){t(null);return}if(window.Stripe&&e&&console.warn(p),window.Stripe){t(window.Stripe);return}try{var i=b();if(i&&e)console.warn(p);else if(!i)i=v(e);else if(i&&d!==null&&u!==null){var a;i.removeEventListener("load",d),i.removeEventListener("error",u),(a=i.parentNode)===null||a===void 0||a.removeChild(i),i=v(e)}d=L(t,n),u=P(n),i.addEventListener("load",d),i.addEventListener("error",u)}catch(c){n(c);return}}),s.catch(function(t){return s=null,Promise.reject(t)}))},_=function(e,t,n){if(e===null)return null;var i=e.apply(void 0,t);return E(i,n),i},l,h=!1,y=function(){return l||(l=j(null).catch(function(e){return l=null,Promise.reject(e)}),l)};Promise.resolve().then(function(){return y()}).catch(function(r){h||console.warn(r)});var C=function(){for(var e=arguments.length,t=new Array(e),n=0;n<e;n++)t[n]=arguments[n];h=!0;var i=Date.now();return y().then(function(a){return _(a,t,i)})};(async()=>{const r=await fetch("/subscription",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({_token:$('[name="_token"]').first().val()})}).then(f=>f.json()),e=await C(r.pk);if(!e)return;const t={theme:"stripe",labels:"floating",variables:{fontFamily:"Advent Pro, sans-serif",primaryColor:"#5b21b6",colorDanger:"#dc2626"},rules:{".Label--resting":{fontSize:"0.875rem",lineHeight:"1rem",fontWeight:"500"},".Label--floating":{fontSize:"0.675rem",lineHeight:"1rem",fontWeight:"500"},".Input":{padding:"0.5rem 0.75rem"}}},n=e.elements({clientSecret:r.cs,appearance:t});n.create("payment",{layout:"tabs"}).mount("#payment-element"),$("#payment-form").on("submit",async function(f){f.preventDefault(),S(!0);const{error:m}=await e.confirmSetup({elements:n,confirmParams:{return_url:r.url}});m.type==="card_error"||m.type==="validation_error"?o(m.message):o(r.str.error),S(!1)});const a=new URLSearchParams(window.location.search).get("payment_intent_client_secret");if(!a)return;const{paymentIntent:c}=await e.retrievePaymentIntent(a);if(c)switch(c.status){case"succeeded":o(r.str.success);break;case"processing":o(r.str.process);break;case"requires_payment_method":o(r.str.failed);break;default:o(r.str.wrong);break}})();function o(r){$("#payment-message").text(r).removeClass("hidden"),setTimeout(()=>{$("#payment-message").addClass("hidden").text("")},4e3)}function S(r){const e=$("#payment-button");e.prop("disabled",r),e.find(".text").css("opacity",r?0:1),e.find(".loader").css("opacity",r?1:0)}