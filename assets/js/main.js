(()=>{"use strict";function e(e){e&&document.querySelector(e.selector).addEventListener("click",(function(){var t=document.querySelector(e.input),s="";switch(e.input){case"#wdss-jsonld-schema-orgname input":s=wdss_localize.site_name;break;case"#wdss-jsonld-schema-email input":s=wdss_localize.site_email}t.value=s}))}var t=function(e,t){setTimeout((function(){e.remove()}),t)};function s(e){var t=document.querySelector(e.toggler),s=t.hasAttribute("checked"),n=document.querySelector(e.target);s&&n.classList.toggle("hidden"),t.addEventListener("click",(function(){n.classList.toggle("hidden")}))}function n(){for(var e=arguments.length,t=new Array(e),s=0;s<e;s++)t[s]=arguments[s];t.forEach((function(e){Array.from(document.querySelectorAll(e.button)).forEach((function(t){t.addEventListener("click",(function(){var s=t.closest("div").querySelector(e.target);""!==s.value&&confirm("Are you sure?")&&(s.value="")}))}))}))}var o={toggler:"#wdss-auto-featured-image-condition input",target:"#wdss-featured-images-group"},c={toggler:"#wdss-polylang-meta-data-condition input",target:"#wdss-polylang-meta-data-group"},a={button:"#wdss-featured-images-group button.reset",target:"#wdss-featured-images-group input"},r={button:"#wdss-jsonld-schema-logo button.reset",target:"#wdss-jsonld-schema-logo input"},i={select:".image-chooser.featured button.choose",target:".image-chooser.featured input",is_multiple:!0,title:"Select Featured Images"},l={select:".image-chooser.logo button.choose",target:".image-chooser.logo input",is_multiple:!1,title:"Select Organization Logo"},d={selector:"#wdss-generate-orgname",input:"#wdss-jsonld-schema-orgname input"},u={selector:"#wdss-generate-email",input:"#wdss-jsonld-schema-email input"},m={root_el:"#custom-410s-list-settings",action:"e410_dictionary_update",nonce:"e410-dictionary-nonce",name:"e410_dictionary"},f={root_el:"#images-settings",action:"excluded_hosts_dictionary_update",nonce:"excluded-hosts-dictionary-nonce",name:"excluded_hosts_dictionary"};function v(e,t,s,n,o,c,a){try{var r=e[c](a),i=r.value}catch(e){return void s(e)}r.done?t(i):Promise.resolve(i).then(n,o)}function g(e){return function(){var t=this,s=arguments;return new Promise((function(n,o){var c=e.apply(t,s);function a(e){v(c,n,o,a,r,"next",e)}function r(e){v(c,n,o,a,r,"throw",e)}a(void 0)}))}}function p(e){Array.from(document.querySelectorAll(e.select)).forEach((function(t){t.addEventListener("click",(function(s){var n;s.preventDefault(),(n=wp.media({title:e.title,multiple:e.is_multiple,library:{type:"image"},button:{text:"Select"}})).on("close",(function(){var s=t.closest(".image-chooser.featured"),o=t.closest(".image-chooser.logo"),c=n.state().get("selection"),a=new Array,r=new Array,i=0;if(s){c.forEach((function(e){a[i]=e.id,i++}));var l=a.join(",");s.querySelector(e.target).value=l}else if(o){c.forEach((function(e){r[i]=e.attributes.url,i++}));var d=r.join(",");o.querySelector(e.target).value=d}})),n.on("open",(function(){var s=t.closest(".wdss-setting-item.image-chooser"),o=n.state().get("selection");s.querySelector(e.target).value.split(",").forEach((function(e){var t=wp.media.attachment(e);t.fetch(),o.add(t?[t]:[])}))})),n.open()}))}))}function h(e){var s=document.querySelector(e.root_el),n=s.querySelector(".wdss-list-item-handler.add"),o=s.querySelector(".save-dictionary"),c=document.querySelector("#wdss-settings-page form"),a=s.querySelector(".wdss-list-table tbody"),r=s.querySelector('input[type="text"]'),i=jQuery("".concat(e.root_el," .wdss-list-table tbody tr")),l=[];jQuery.each(i,(function(){l.push(this.id)})),o.addEventListener("click",(function(){var n=jQuery("".concat(e.root_el," .wdss-list-table tbody tr")),c=[];n.each((function(e,t){var s=t.querySelector("td:nth-of-type(1)").textContent;c.push(s)}));var a={action:e.action,security:e.nonce};a[e.name]=c,jQuery.ajax({url:wdss_localize.url,type:"post",dataType:"json",data:a,success:function(e){var n=s.querySelector(".wdss-list-table-actions span");n&&n.remove(),o.insertAdjacentHTML("afterend",'<span class="msg successful">Table was updated</span>'),t(s.querySelector("span.msg"),1200),o.classList.add("saved")},fail:function(e){var n=s.querySelector(".wdss-list-table-actions span");n&&n.remove(),o.insertAdjacentHTML("afterend",'<span class="msg error">Error, look at information in console</span>'),t(s.querySelector("span.msg"),1200),console.log(e)}})})),n.addEventListener("click",(function(){if(r.value){o.classList.remove("saved");var e=r.value;r.value="",a.insertAdjacentHTML("beforeend",'\n      <tr id="'.concat(wdss_localize.wp_rand,'">\n        <td>').concat(e,'</td>\n        <td class="wdss-list-table__remove-item"><i class="fas fa-trash"></i></td>\n      </tr>\n      '))}})),jQuery(document).on("click","".concat(e.root_el," .wdss-list-table__remove-item i"),(function(){o.classList.remove("saved"),confirm("Remove this rule from table?")&&this.closest("tr").remove()})),c.addEventListener("submit",(function(t){var s,n=jQuery("".concat(e.root_el," .wdss-list-table tbody tr")),c=[];jQuery.each(n,(function(){c.push(this.id)})),function(e,t){if(e.length!==t.length)return!1;for(var s=0;s<e.length;s++)if(!t.includes(e[s]))return!1;return!0}(l,c)&&(s=!0),s||o.classList.contains("saved")||confirm("All unsaved changes will be lost. Do you want to continue?")||t.preventDefault()}))}var y=document.querySelector("#wdss-settings-page");document.addEventListener("DOMContentLoaded",(function(){var t,v,S;y&&(function(){var e=document.querySelector("html"),t=document.querySelector("#wdss-remove-broken-featured__choose"),s=document.querySelector(".wdss-modal-header i.fa-times"),n=document.querySelector(".wdss-modal"),o=n.querySelector("table"),c=document.querySelector("#wdss-exclude-posts-table tbody"),a=n.querySelector(".wdss-button.submit"),r=n.querySelector(".wdss-button.toggle-all"),i=n.querySelector(".wdss-button.get-posts"),l=n.querySelector(".wdss-modal-posts-count"),d=n.querySelector(".wdss-modal-welcome-msg");function u(){n.classList.remove("active"),e.classList.remove("fixed")}function m(e){this.insertAdjacentHTML("beforeend",e);var t=document.querySelector(".wdss-modal-loading-msg");t&&t.remove();var s=Array.from(document.querySelectorAll(".wdss-table-row.post")),c=l.querySelector("strong");function d(e){e.setAttribute("checked","checked"),e.checked=!0}function u(e){e.removeAttribute("checked"),e.checked=!1}l.classList.add("active"),c&&(c.innerHTML=s.length),s.length>0?(s.forEach((function(e){e.addEventListener("click",(function(){var t=e.querySelector('.wdss-table-post__select input[type="checkbox"]');t.hasAttribute("checked")?u(t):d(t)}))})),i.classList.add("inactive"),a.classList.remove("inactive"),r.classList.remove("inactive"),r.addEventListener("click",(function(e){e.currentTarget.params.forEach((function(e){var t=e.querySelector('.wdss-table-post__select input[type="checkbox"]');t.hasAttribute("checked")?u(t):d(t)}))})),r.params=s):(o.insertAdjacentHTML("afterend",'<span class="wdss-modal-not-found-msg">No results...</span>'),i.classList.remove("inactive")),a.addEventListener("click",(function(){var e,t=n.querySelectorAll('.wdss-table-post__select input[type="checkbox"]:checked'),s=[];0!==t.length?(i.classList.remove("inactive"),t.forEach((function(e){s.push(e.value)})),e=s.join(","),a.classList.add("inactive"),r.classList.add("inactive"),l.classList.remove("active"),c.innerHTML="",Array.from(n.querySelectorAll(".wdss-table-row.post")).forEach((function(e){e.parentNode.removeChild(e)})),jQuery.ajax({url:wdss_localize.url,type:"post",data:{selected_list:JSON.stringify(e),action:"remove_broken_featured",broken_featured_nonce2:wdss_localize.remove_broken_featured_nonce},success:function(e){i.classList.add("inactive"),o.insertAdjacentHTML("afterend",'<span class="msg successful">Completed!<br><small>Please wait several minutes while changes are implementing</small></span>')},error:function(e){o.insertAdjacentHTML("afterend",'<span class="msg error">An Error occured!<br><smallLook in console for more details</small></span>'),console.log(e)}})):i.classList.add("inactive")}))}n.querySelector(".wdss-modal-header").insertAdjacentHTML("afterbegin",'<span class="wdss-modal-title">Delete Broken Featured Images</span>'),t.addEventListener("click",(function(){n.classList.add("active"),e.classList.add("fixed")})),s.addEventListener("click",u),document.onkeydown=function(e){return"Esc"===e.key||"Escape"===e.key?u():null},i.addEventListener("click",(function(){var e=n.querySelector(".msg");e&&e.remove(),i.classList.add("inactive"),d.classList.remove("active"),o.insertAdjacentHTML("afterend",'<span class="wdss-modal-loading-msg">Loading...</span>');var t=[],s=n.querySelector(".wdss-modal-not-found-msg");s&&s.remove();var a=wdss_localize.total_post_count;a>800&&(a=800,console.log("Using lite-mode (up to 800 posts per fetch"));var r=Math.ceil(a/100);function l(){for(var e=[],s=function(s){e.push(new Promise((function(e,n){fetch(document.location.origin+"/wp-json/wp/v2/posts?orderby=id&order=asc&per_page=100&page=".concat(s)).then((function(e){return e.json()})).then((function(s){t=t.concat(s),e()}))})))},n=1;n<=r;n++)s(n);return Promise.all(e)}function u(){return(u=g(regeneratorRuntime.mark((function e(){return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.prev=0,e.next=3,l();case 3:console.log(t),jQuery.ajax({url:wdss_localize.url,type:"post",data:{fetched_list:JSON.stringify(t),action:"fetch_broken_featured",broken_featured_nonce1:wdss_localize.broken_featured_list_nonce},success:function(e){m.call(c,e)}}),e.next=10;break;case 7:e.prev=7,e.t0=e.catch(0),console.log(e.t0);case 10:case"end":return e.stop()}}),e,null,[[0,7]])})))).apply(this,arguments)}console.log("Total pages: ".concat(r)),function(){u.apply(this,arguments)}()}))}(),document.querySelectorAll(".wdss-section:not(#wdss-snippets-settings) > .wdss-row").forEach((function(e){e.classList.contains("pinned")||e.classList.add("hidden");var t=e.previousElementSibling,s=t.querySelectorAll(".section-toggler");function n(){t.classList.toggle("active"),e.classList.toggle("hidden")}s&&s.forEach((function(e){e.addEventListener("click",n)}))})),function(){var e=document.querySelectorAll(".wdss-section .section-pin"),t=document.querySelectorAll(".wdss-section:not(#wdss-snippets-settings)");function s(){var e,t,s,n=this.closest(".wdss-section-header"),o=n.closest(".wdss-section").getAttribute("id");(JSON.parse(localStorage.getItem("PINNED_WDS_SECTIONS"))||[]).includes(o)?(this.classList.remove("active"),n.querySelector("h2").classList.remove("pinned"),n.querySelector("i.section-toggler").classList.remove("disabled"),e=o,-1!==(s=(t=JSON.parse(localStorage.getItem("PINNED_WDS_SECTIONS"))||[]).indexOf(e))&&t.splice(s,1),localStorage.setItem("PINNED_WDS_SECTIONS",JSON.stringify(t))):(this.classList.add("active"),n.classList.add("active"),function(e){var t=JSON.parse(localStorage.getItem("PINNED_WDS_SECTIONS"))||[];t.push(e),localStorage.setItem("PINNED_WDS_SECTIONS",JSON.stringify(t))}(o),n.querySelector("h2").classList.add("pinned"),n.querySelector("i.section-toggler").classList.add("disabled"))}!function(){var e;localStorage.getItem("PINNED_WDS_SECTIONS")?e=localStorage.getItem("PINNED_WDS_SECTIONS"):(e=[],localStorage.setItem("PINNED_WDS_SECTIONS",JSON.stringify(e)));var s=e;t.forEach((function(e){var t=e.getAttribute("id"),n=e.querySelector(".wdss-section-header"),o=n.nextElementSibling;s.includes(t)&&(e.classList.add("pinned"),n.querySelector("i.section-pin").classList.add("active"),n.querySelector("i.section-toggler").classList.add("disabled"),n.classList.add("active"),n.querySelector("h2").classList.add("pinned"),o.classList.remove("hidden"))}))}(),e.forEach((function(e){e.addEventListener("click",s)}))}(),wdss_localize.total_post_count>0&&(s(o),e(d),p(i),n(a)),wdss_localize.is_polylang_exists&&wdss_localize.is_polylang_setup&&s(c),h(m),h(f),p(l),n(r),t=document.querySelector("#wdss-advanced-jsonld-schema-condition input"),v=document.querySelector(".wdss-jsonld-schema-predifined-settings"),t.hasAttribute("checked")&&v.classList.add("disabled"),t.addEventListener("click",(function(){v.classList.contains("disabled")?v.classList.remove("disabled"):v.classList.add("disabled")})),e(u),Array.from(document.querySelectorAll('input[type="checkbox"')).forEach((function(e){e.addEventListener("click",(function(){e.hasAttribute("checked")?(e.removeAttribute("checked"),e.value=0,e.checked=!1):(e.setAttribute("checked","checked"),e.checked=!0,e.value=1)}))})),S=Array.from(document.querySelectorAll("#wdss-snippets-settings input")),document.querySelector("#wdss-toggle-options").addEventListener("click",(function(){S[0].hasAttribute("checked")?S.forEach((function(e){e.removeAttribute("checked"),e.checked=!1})):S.forEach((function(e){e.setAttribute("checked","checked"),e.checked=!0}))})),Array.from(document.querySelectorAll(".wdss-setting-item-accordion")).forEach((function(e){var t=e.nextElementSibling;e.addEventListener("click",(function(){t.classList.contains("active")?(e.classList.remove("opened"),t.classList.remove("active")):(e.classList.add("opened"),t.classList.add("active"))}))})))}))})();