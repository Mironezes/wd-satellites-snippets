(()=>{"use strict";function e(e,t,n,o,c,s,a){try{var i=e[s](a),r=i.value}catch(e){return void n(e)}i.done?t(r):Promise.resolve(r).then(o,c)}function t(e,t){for(var n=0;n<t.length;n++){var o=t[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}var n=function(){function n(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,n)}var o,c,s,a,i;return o=n,c=[{key:"confirm",value:(a=regeneratorRuntime.mark((function e(t){var o,c,s,a;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return n.template(t,"confirm"),o=document.querySelector(".notification-content-inputs"),c=o.querySelector("button.confirm"),s=o.querySelector("button.cancel"),a=new Promise((function(e){c.addEventListener("click",(function(){e(!0),n.closeNotification(o)})),s.addEventListener("click",(function(){e(!1),n.closeNotification(o)}))})),e.next=7,a;case 7:return e.abrupt("return",e.sent);case 8:case"end":return e.stop()}}),e)})),i=function(){var t=this,n=arguments;return new Promise((function(o,c){var s=a.apply(t,n);function i(t){e(s,o,c,i,r,"next",t)}function r(t){e(s,o,c,i,r,"throw",t)}i(void 0)}))},function(e){return i.apply(this,arguments)})},{key:"info",value:function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;n.template(e,"info",t)}},{key:"prompt",value:function(e,t){n.template(e,"prompt");var o=document.querySelector(".notification-content-inputs"),c=o.querySelector("input"),s=o.querySelector("button");c.value&&s.addEventListener("click",(function(){t(c.value),n.closeNotification(o)}))}}],s=[{key:"closeNotification",value:function(e,t,n){var o=n?n.includes("wdss-modal"):null,c=document.querySelector("html");e.closest(".notification")&&e.closest(".notification").remove(),("prompt"===t||o)&&c.classList.remove("fixed")}},{key:"template",value:function(e,t,o){var c,s,a=document.querySelector("html");switch(document.onkeydown=function(e){return"Esc"===e.key||"Escape"===e.key?n.closeNotification(document.querySelector(".modal.notification")):null},t){default:s="info";break;case"prompt":s="prompt";break;case"confirm":s="confirm"}if(c='\n\t\t\t\t<div class="modal notification '.concat(s,'">\n\t\t\t\t<div class="notification-header">\n\t\t\t\t\t<i class="fas fa-times"></i>\n\t\t\t\t</div>\n\t\t\t\t<div class="notification-content">\n\t\t\t\t<span>').concat(e,"</span>\n\t\t\t\t</div>"),"prompt"===t?(a.classList.add("fixed"),c+='<div class="notification-content-inputs"><input required type="number" min="100" value="100"><button type="button" class="wdss-button">Enter</button></div>'):"confirm"===t&&(c+='<div class="notification-content-inputs"><button class="wdss-button confirm">Yes</button><button class="wdss-button cancel">No</button></div>'),c+="</div>",document.body.insertAdjacentHTML("beforeend",c),document.querySelector(".notification-header i")){var i=document.querySelector(".notification-header i");i.addEventListener("click",(function(){n.closeNotification(i,t,o)}))}}}],c&&t(o.prototype,c),s&&t(o,s),n}(),o=new n,c=function(e,t){if(e.length!==t.length)return!1;for(var n=0;n<e.length;n++)if(!t.includes(e[n]))return!1;return!0};function s(e){e&&document.querySelector(e.selector)&&document.querySelector(e.selector).addEventListener("click",(function(){var t=document.querySelector(e.input),n="";switch(e.input){case"#wdss-jsonld-schema-orgname input":n=wdss_localize.site_name;break;case"#wdss-jsonld-schema-email input":n=wdss_localize.site_email}t.value=n}))}var a=function(e,t){setTimeout((function(){e.remove()}),t)};function i(e){var t=document.querySelector(e.toggler),n=t.hasAttribute("checked"),o=document.querySelector(e.target);n&&o.classList.toggle("hidden"),t.addEventListener("click",(function(){o.classList.toggle("hidden")}))}function r(){for(var e=arguments.length,t=new Array(e),n=0;n<e;n++)t[n]=arguments[n];t.forEach((function(e){Array.from(document.querySelectorAll(e.button)).forEach((function(t){t.addEventListener("click",(function(){var n=t.closest("div").querySelector(e.target);""!==n.value&&o.confirm("Are you sure?").then((function(e){!0===e&&(n.value="")}))}))}))}))}function l(e){e.setAttribute("checked","checked"),e.checked=!0}function d(e){e.removeAttribute("checked"),e.checked=!1}var u={toggler:"#wdss-auto-featured-image-condition input",target:"#wdss-featured-images-group"},f={toggler:"#wdss-polylang-meta-data-condition input",target:"#wdss-polylang-meta-data-group"},m={button:"#wdss-featured-images-group button.reset",target:"#wdss-featured-images-group input"},v={button:"#wdss-jsonld-schema-logo button.reset",target:"#wdss-jsonld-schema-logo input"},p={select:".image-chooser.featured button.choose",target:".image-chooser.featured input",is_multiple:!0,title:"Select Featured Images"},h={select:".image-chooser.logo button.choose",target:".image-chooser.logo input",is_multiple:!1,title:"Select Organization Logo"},g={selector:"#wdss-generate-orgname",input:"#wdss-jsonld-schema-orgname input"},_={selector:"#wdss-generate-email",input:"#wdss-jsonld-schema-email input"},y={root_el:"#custom-410s-list-settings",action:"e410_dictionary_update",nonce:"e410-dictionary-nonce",name:"e410_dictionary"},w={root_el:"#post-content-settings",action:"excluded_hosts_dictionary_update",nonce:"excluded-hosts-dictionary-nonce",name:"excluded_hosts_dictionary"},b={modal_el:"#exclude-posts-modal",modal_title:"Delete Broken Featured Images",open_modal_btn:"#wdss-remove-broken-featured__choose",fetch_action:"fetch_broken_featured",fetch_nonce_name:"fetch_broken_featured_nonce",fetch_nonce_value:wdss_localize.broken_featured_list_nonce,post_action:"remove_broken_featured",post_nonce_name:"remove_broken_featured_nonce",post_nonce_value:wdss_localize.remove_broken_featured_nonce},S={modal_el:"#fix-validation-posts-modal",modal_title:"Fix Posts Validation Errors",open_modal_btn:"#wdss-fix-validation-errors__choose",fetch_action:"fetch_all_posts",fetch_nonce_name:"all_posts_list_nonce",fetch_nonce_value:wdss_localize.all_posts_list_nonce,post_action:"fix_posts_validation_errors",post_nonce_name:"fix-posts-validation-errors-nonce",post_nonce_value:wdss_localize.fix_posts_validation_errors_nonce};function L(e){Array.from(document.querySelectorAll(e.select)).forEach((function(t){t.addEventListener("click",(function(n){var o;n.preventDefault(),(o=wp.media({title:e.title,multiple:e.is_multiple,library:{type:"image"},button:{text:"Select"}})).on("close",(function(){var n=t.closest(".image-chooser.featured"),c=t.closest(".image-chooser.logo"),s=o.state().get("selection"),a=new Array,i=new Array,r=0;if(n){s.forEach((function(e){a[r]=e.id,r++}));var l=a.join(",");n.querySelector(e.target).value=l}else if(c){s.forEach((function(e){i[r]=e.attributes.url,r++}));var d=i.join(",");c.querySelector(e.target).value=d}})),o.on("open",(function(){var n=t.closest(".wdss-setting-item.image-chooser"),c=o.state().get("selection");n.querySelector(e.target).value.split(",").forEach((function(e){var t=wp.media.attachment(e);t.fetch(),c.add(t?[t]:[])}))})),o.open()}))}))}function k(e,t,n,o,c,s,a){try{var i=e[s](a),r=i.value}catch(e){return void n(e)}i.done?t(r):Promise.resolve(r).then(o,c)}function q(e){return function(){var t=this,n=arguments;return new Promise((function(o,c){var s=e.apply(t,n);function a(e){k(s,o,c,a,i,"next",e)}function i(e){k(s,o,c,a,i,"throw",e)}a(void 0)}))}}var E=new n;function x(e){var t=document.querySelector("html"),n=document.querySelector(e.modal_el),o=document.querySelector(e.open_modal_btn),c=n.querySelector(".wdss-modal-header i.fa-times"),s=wdss_localize.total_post_count,a=Math.ceil(s/100);window["".concat(e.fetch_action,"-setup")]=!0,o&&o.addEventListener("mousedown",(function(){var o,i,r=n.querySelector(".wdss-modal-body"),u=n.querySelector(".wdss-modal-informaion-panel"),f=n.querySelector("tbody"),m=n.querySelector(".wdss-button.submit"),v=n.querySelector(".wdss-button.toggle-all"),p=n.querySelector(".wdss-button.get-posts"),h=n.querySelector(".wdss-modal-posts-count"),g=h.querySelector("strong"),_=n.querySelector(".wdss-modal-welcome-msg"),y='<span class="wdss-modal-not-found-msg">No results...</span>',w=n.querySelector(".wdss-modal-title"),b=e.modal_title,S=[],L=!1;function k(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null;if(e&&e.length>0&&u.classList.remove("active"),!e){var t=n.querySelector(".wdss-modal-not-found-msg");t&&t.remove()}}function x(){n.classList.remove("active"),t.classList.remove("fixed"),Array.from(document.querySelectorAll(".notification")).forEach((function(e){e.remove()}))}function A(){return Array.from(document.querySelectorAll(".wdss-table-row.post"))}function N(){var e=A().length;if(h.classList.add("active"),g)return g.innerHTML=e}function j(){A().forEach((function(e){var t=e.querySelector('.wdss-table-post__select input[type="checkbox"]');t.hasAttribute("checked")?d(t):l(t)}))}function T(){return(T=q(regeneratorRuntime.mark((function t(){var n,o;return regeneratorRuntime.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return n=function(){for(var e=[],t=function(t){try{e.push(new Promise((function(e,n){fetch(document.location.origin+"/wp-json/wp/v2/posts/?per_page=100&page=".concat(t)).then((function(e){return e.json()})).then((function(t){S=S.concat(t),e()})).catch((function(e){console.log(e),n()}))})))}catch(e){return"break"}},n=1;n<=window.next_fetched_page&&"break"!==t(n);n++);return window.next_fetched_page++,window.next_fetched_page<a?console.log("Next page to fetch: ".concat(window.next_fetched_page)):console.log("This is the last page to fetch"),Promise.allSettled(e)},r.classList.add("loading"),t.next=4,n(window.next_fetched_page);case 4:console.log("Fetched posts in first query: ".concat(S.length)),(o={fetched_list:JSON.stringify(S),action:e.fetch_action})[e.fetch_nonce_name]=e.fetch_nonce_value,jQuery.ajax({url:wdss_localize.url,type:"post",data:o}).done((function(e){k(e),I.call(f,e),r.classList.remove("loading"),p.classList.add("inactive")}));case 8:case"end":return t.stop()}}),t)})))).apply(this,arguments)}function I(t){this.insertAdjacentHTML("beforeend",t),N();var c=A();L&&!n.querySelector(".wdss-button.load-more")&&(p.insertAdjacentHTML("beforebegin",'<button type="button" class="wdss-button load-more">Fetch next page</button>'),(o=n.querySelector(".wdss-button.load-more")).classList.add("inactive")),window.next_fetched_page<a&&o?(o.classList.remove("inactive"),v.classList.remove("inactive"),m.classList.remove("inactive")):o.addEventListener("click",(function(){!function(){if(v.addEventListener("click",j),a===window.next_fetched_page)return E.info("You achived the last page",n),void o.classList.add("inactive");k(),o.classList.add("inactive"),r.classList.add("loading"),v.classList.add("inactive"),m.classList.add("inactive");try{fetch(document.location.origin+"/wp-json/wp/v2/posts?per_page=100&page=".concat(window.next_fetched_page)).then((function(e){return e.json()})).then((function(t){var c={fetched_list:JSON.stringify(t),action:e.fetch_action};c[e.fetch_nonce_name]=e.fetch_nonce_value,jQuery.ajax({url:wdss_localize.url,type:"post",data:c}).done((function(e){console.log("Current fetched page: ".concat(window.next_fetched_page)),window.next_fetched_page++,window.next_fetched_page<a?console.log("Next page to fetch: ".concat(window.next_fetched_page)):console.log("This is the last page to fetch");var t=Array.from(n.querySelectorAll(".msg"));t&&t.forEach((function(e){return e.remove()})),r.classList.remove("loading"),window.next_fetched_page<a&&(o.classList.remove("inactive"),v.classList.remove("inactive"),m.classList.remove("inactive")),e?(f.insertAdjacentHTML("beforeend",e),k(e)):(document.querySelector(".wdss-modal-not-found-msg")||u.insertAdjacentHTML("afterbegin",y),v.classList.add("inactive"),m.classList.add("inactive")),N()}))}))}catch(e){console.log(e)}}()})),c.length>0?(c.forEach((function(e){e.addEventListener("click",(function(){var t=e.querySelector('.wdss-table-post__select input[type="checkbox"]');t.hasAttribute("checked")?d(t):l(t)}))})),p.classList.add("inactive"),m.classList.remove("inactive"),v.classList.remove("inactive"),v.addEventListener("click",j)):(u.insertAdjacentHTML("afterbegin",y),v.classList.add("inactive"),m.classList.add("inactive")),m.addEventListener("click",(function(t){t.preventDefault(),E.confirm("Are you ready to start?").then((function(t){if(!0===t){var c,s=Array.from(n.querySelectorAll('.wdss-table-post__select input[type="checkbox"]:checked')),a=[];if(s.length){p.classList.remove("inactive"),s.forEach((function(e){a.push(e.value)})),c=a.join(","),console.log("Selected IDs: ".concat(c)),m.classList.add("inactive"),v.classList.add("inactive"),h.classList.remove("active"),g.innerHTML="",Array.from(n.querySelectorAll(".wdss-table-row.post")).forEach((function(e){e.parentNode.removeChild(e)}));var i={selected_list:JSON.stringify(c),action:e.post_action};i[e.post_nonce_name]=e.post_nonce_value,p.classList.add("inactive"),o&&o.classList.add("inactive"),r.classList.add("processing"),jQuery.ajax({url:wdss_localize.url,type:"post",data:i}).done((function(){u.insertAdjacentHTML("afterbegin",'<span class="msg successful">Completed!<br><small>It can take several minutes while changes are implementing</small></span>'),u.classList.add("active")})).fail((function(e){u.insertAdjacentHTML("afterbegin",'<span class="msg error">An Error occured!<br><smallLook in console for more details</small></span>'),console.log(e)})).always((function(){r.classList.remove("processing")}))}else E.info("Please, select the posts which will be proceded")}}))}))}w.childNodes.length<1&&w.insertAdjacentHTML("afterbegin",b),!0===window["".concat(e.fetch_action,"-setup")]?(E.prompt("Enter max posts per fetch count. The minimum is 100: ",(function(o){var c="<small>Lite-mode: max ".concat(i=o>=100?o:800," posts per fetches</small>");s>i&&(L=!0,w&&w.insertAdjacentHTML("beforeend",c)),L&&(s=i),window.next_fetched_page=Math.ceil(s/100),n.classList.add("active"),t.classList.add("fixed"),window["".concat(e.fetch_action,"-setup")]=!1})),console.log("Total fetchable pages: ".concat(a))):E.info("Please, reload the page first"),c.addEventListener("click",x),document.onkeydown=function(e){return"Esc"===e.key||"Escape"===e.key?x():null},p.addEventListener("click",(function(){var e=n.querySelector(".msg");e&&e.remove(),p.classList.add("inactive"),_.classList.remove("active"),k(),console.log("Current fetched pages: ".concat(window.next_fetched_page)),window.next_fetched_page=Math.ceil(s/100),function(){T.apply(this,arguments)}()}))}))}function A(e,t,n,o,c,s,a){try{var i=e[s](a),r=i.value}catch(e){return void n(e)}i.done?t(r):Promise.resolve(r).then(o,c)}function N(e){return function(){var t=this,n=arguments;return new Promise((function(o,c){var s=e.apply(t,n);function a(e){A(s,o,c,a,i,"next",e)}function i(e){A(s,o,c,a,i,"throw",e)}a(void 0)}))}}var j=new n;function T(e){var t=document.querySelector(e.root_el),n=t.querySelector(".wdss-button.wdss-table-add"),o=t.querySelector(".save-dictionary"),s=document.querySelector("#wdss-settings-page form"),i=t.querySelector(".wdss-table-handler .wdss-table tbody"),r=t.querySelector('input[type="text"]'),l=jQuery("".concat(e.root_el," .wdss-table tbody tr")),d=[];function u(){return(u=N(regeneratorRuntime.mark((function t(n){var s,a,i;return regeneratorRuntime.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:s=jQuery("".concat(e.root_el," .wdss-table tbody tr")),a=[],jQuery.each(s,(function(){a.push(this.id)})),c(d,a)&&(i=!0),i||o.classList.contains("saved")||confirm("All unsaved changes will be lost. Do you want to continue?")||n.preventDefault();case 5:case"end":return t.stop()}}),t)})))).apply(this,arguments)}jQuery.each(l,(function(){d.push(this.id)})),o.addEventListener("click",(function(){var n=jQuery("".concat(e.root_el," .wdss-table-handler .wdss-table tbody tr")),c=[];n.each((function(e,t){var n=t.querySelector("td:nth-of-type(1)").textContent;c.push(n)}));var s={action:e.action,security:e.nonce};s[e.name]=c,jQuery.ajax({url:wdss_localize.url,type:"post",dataType:"json",data:s,success:function(){var e=t.querySelector(".wdss-table-actions span");e&&e.remove(),o.insertAdjacentHTML("afterend",'<span class="msg successful">Table was updated</span>'),a(t.querySelector("span.msg"),1200),o.classList.add("saved")},fail:function(e){var n=t.querySelector(".wdss-table-actions span");n&&n.remove(),o.insertAdjacentHTML("afterend",'<span class="msg error">Error, look at information in console</span>'),a(t.querySelector("span.msg"),1200),console.log(e)}})})),n.addEventListener("click",(function(){if(r.value){o.classList.remove("saved");var e=r.value;r.value="",i.insertAdjacentHTML("beforeend",'\n      <tr id="'.concat(wdss_localize.wp_rand,'">\n        <td>').concat(e,'</td>\n        <td class="wdss-table__remove-item"><i class="fas fa-trash"></i></td>\n      </tr>\n      '))}})),jQuery(document).on("click","".concat(e.root_el," .wdss-table__remove-item i"),(function(){var e=this;o.classList.remove("saved"),j.confirm("Remove this rule from table?").then((function(t){!0===t&&e.closest("tr").remove()}))})),s.addEventListener("submit",(function(e){return u.apply(this,arguments)}))}var I=document.querySelector("#wdss-settings-page");document.addEventListener("DOMContentLoaded",(function(){var e,t,n;I&&(document.querySelectorAll(".wdss-section:not(#wdss-snippets-settings) > .wdss-row").forEach((function(e){e.classList.contains("pinned")||e.classList.add("hidden");var t=e.previousElementSibling,n=t.querySelectorAll(".section-toggler");function o(){t.classList.toggle("active"),e.classList.toggle("hidden")}n&&n.forEach((function(e){e.addEventListener("click",o)}))})),function(){var e=document.querySelectorAll(".wdss-section .section-pin"),t=document.querySelectorAll(".wdss-section:not(#wdss-snippets-settings)");function n(){var e,t,n,o=this.closest(".wdss-section-header"),c=o.closest(".wdss-section").getAttribute("id");(JSON.parse(localStorage.getItem("PINNED_WDS_SECTIONS"))||[]).includes(c)?(this.classList.remove("active"),o.querySelector("h2").classList.remove("pinned"),o.querySelector("i.section-toggler").classList.remove("disabled"),e=c,-1!==(n=(t=JSON.parse(localStorage.getItem("PINNED_WDS_SECTIONS"))||[]).indexOf(e))&&t.splice(n,1),localStorage.setItem("PINNED_WDS_SECTIONS",JSON.stringify(t))):(this.classList.add("active"),o.classList.add("active"),function(e){var t=JSON.parse(localStorage.getItem("PINNED_WDS_SECTIONS"))||[];t.push(e),localStorage.setItem("PINNED_WDS_SECTIONS",JSON.stringify(t))}(c),o.querySelector("h2").classList.add("pinned"),o.querySelector("i.section-toggler").classList.add("disabled"))}!function(){var e;localStorage.getItem("PINNED_WDS_SECTIONS")?e=localStorage.getItem("PINNED_WDS_SECTIONS"):(e=[],localStorage.setItem("PINNED_WDS_SECTIONS",JSON.stringify(e)));var n=e;t.forEach((function(e){var t=e.getAttribute("id"),o=e.querySelector(".wdss-section-header"),c=o.nextElementSibling;n.includes(t)&&(e.classList.add("pinned"),o.querySelector("i.section-pin").classList.add("active"),o.querySelector("i.section-toggler").classList.add("disabled"),o.classList.add("active"),o.querySelector("h2").classList.add("pinned"),c.classList.remove("hidden"))}))}(),e.forEach((function(e){e.addEventListener("click",n)}))}(),wdss_localize.total_post_count>0&&(i(u),s(g),L(p),r(m),x(b),x(S)),wdss_localize.is_polylang_exists&&wdss_localize.is_polylang_setup&&i(f),T(w),T(y),L(h),r(v),e=document.querySelector("#wdss-advanced-jsonld-schema-condition input"),t=document.querySelector(".wdss-jsonld-schema-predifined-settings"),e.hasAttribute("checked")&&t.classList.add("disabled"),e.addEventListener("click",(function(){t.classList.contains("disabled")?t.classList.remove("disabled"):t.classList.add("disabled")})),s(_),Array.from(document.querySelectorAll('input[type="checkbox"')).forEach((function(e){e.addEventListener("click",(function(){e.hasAttribute("checked")?(e.removeAttribute("checked"),e.value=0,e.checked=!1):(e.setAttribute("checked","checked"),e.checked=!0,e.value=1)}))})),n=Array.from(document.querySelectorAll("#wdss-snippets-settings input")),document.querySelector("#wdss-toggle-options").addEventListener("click",(function(){n[0].hasAttribute("checked")?n.forEach((function(e){e.removeAttribute("checked"),e.checked=!1})):n.forEach((function(e){e.setAttribute("checked","checked"),e.checked=!0}))})),Array.from(document.querySelectorAll(".wdss-setting-item-accordion")).forEach((function(e){var t=e.nextElementSibling;e.addEventListener("click",(function(){t.classList.contains("active")?(e.classList.remove("opened"),t.classList.remove("active")):(e.classList.add("opened"),t.classList.add("active"))}))})))}))})();