(()=>{"use strict";function e(e){var t=document.querySelector(e),s="";switch(e){case"#wdss-title-ending input":s=wdss_localize.site_yoast_ending;break;case"#wdss-jsonld-schema-orgname input":s=wdss_localize.site_name;break;case"#wdss-jsonld-schema-email input":s=wdss_localize.site_email}t.value=s}var t=function(e,t){setTimeout((function(){e.remove()}),t)};function s(e){var t=document.querySelector(e.toggler),s=t.hasAttribute("checked"),c=document.querySelector(e.target);s&&c.classList.toggle("hidden"),t.addEventListener("click",(function(){c.classList.toggle("hidden")}))}function c(e){Array.from(document.querySelectorAll(e.button)).forEach((function(t){t.addEventListener("click",(function(){var s=t.closest("div").querySelector(e.target);""!==s.value&&confirm("Are you sure?")&&(s.value="")}))}))}function n(e){Array.from(document.querySelectorAll(e.select)).forEach((function(t){t.addEventListener("click",(function(s){var c;s.preventDefault(),(c=wp.media({title:e.title,multiple:e.is_multiple,library:{type:"image"},button:{text:"Select"}})).on("close",(function(){var s=t.closest(".image-chooser.featured"),n=t.closest(".image-chooser.logo"),i=c.state().get("selection"),o=new Array,a=new Array,r=0;if(s){i.forEach((function(e){o[r]=e.id,r++}));var l=o.join(",");s.querySelector(e.target).value=l}else if(n){i.forEach((function(e){a[r]=e.attributes.url,r++}));var d=a.join(",");n.querySelector(e.target).value=d}})),c.on("open",(function(){var s=t.closest(".wdss-setting-item.image-chooser"),n=c.state().get("selection");s.querySelector(e.target).value.split(",").forEach((function(e){var t=wp.media.attachment(e);t.fetch(),n.add(t?[t]:[])}))})),c.open()}))}))}function i(){var e=document.querySelector("#wdss-title-clipping-excluded__choose"),t=document.querySelector("#wdss-title-clipping-excluded");function s(e){var t=document.querySelector("html");t.classList.add("fixed"),this.insertAdjacentHTML("beforeend",e),Array.from(document.querySelectorAll(".wdss-table-row.post")).forEach((function(e){e.addEventListener("click",(function(){var t=e.querySelector('.wdss-table-post__select input[type="checkbox"]');t.hasAttribute("checked")?function(e){e.removeAttribute("checked"),e.checked=!1}(t):function(e){e.setAttribute("checked","checked"),e.checked=!0}(t)}))}));var s=this.querySelector(".wdss-modal"),c=s.querySelector(".wdss-modal-header i"),n=s.querySelector(".wdss-button.submit"),i=this.querySelector('#wdss-title-clipping-excluded input[type="text"]');if(""!==i.value){var o=i.value.split(",");Array.from(s.querySelectorAll('.wdss-table-post__select input[type="checkbox"]')).forEach((function(e){o.includes(e.value)&&(e.setAttribute("checked","checked"),e.checked=!0)}))}function a(){s.remove(),t.classList.remove("fixed")}c.addEventListener("click",a),document.onkeydown=function(e){"Esc"!==e.key&&"Escape"!==e.key||a()},n.addEventListener("click",(function(){var e,c=s.querySelectorAll('.wdss-table-post__select input[type="checkbox"]:checked'),n=[];c.forEach((function(e){n.push(e.value)})),e=n.join(","),i.value=e,s.remove(),t.classList.remove("fixed")}))}e.addEventListener("click",(function(){jQuery.ajax({url:wdss_localize.url,type:"post",data:{action:"fetch_modal_content",security:wdss_localize.nonce},success:function(e){s.call(t,e)}})}))}var o=document.querySelector("#wdss-settings-page"),a={toggler:"#wdss-title-clipping-condition input",target:"#wdss-title-clipping-group"},r={toggler:"#wdss-auto-featured-image-condition input",target:"#wdss-featured-images-group"},l={toggler:"#wdss-advanced-jsonld-schema-condition input",target:"#wdss-advanced-jsonld-schema-group"},d={toggler:"#wdss-polylang-meta-data-condition input",target:"#wdss-polylang-meta-data-group"},u={button:"#wdss-title-clipping-excluded button.reset",target:"#wdss-title-clipping-excluded input"},m={button:"#wdss-title-clipping-by-date button.reset",target:"#wdss-title-clipping-by-date input"},g={button:"#wdss-featured-images-group button.reset",target:"#wdss-featured-images-group input"},f={button:"#wdss-jsonld-schema-logo button.reset",target:"#wdss-jsonld-schema-logo input"},p={select:".image-chooser.featured button.choose",target:".image-chooser.featured input",is_multiple:!0,title:"Select Featured Images"},v={select:".image-chooser.logo button.choose",target:".image-chooser.logo input",is_multiple:!1,title:"Select Organization Logo"};document.addEventListener("DOMContentLoaded",(function(){var h,y,S,w,b,L,_,E,k,q;o&&(document.querySelectorAll(".wdss-section:not(#wdss-snippets-settings) > .wdss-row").forEach((function(e){e.classList.contains("pinned")||e.classList.add("hidden");var t=e.previousElementSibling,s=t.querySelectorAll(".section-toggler");function c(){t.classList.toggle("active"),e.classList.toggle("hidden")}s&&s.forEach((function(e){e.addEventListener("click",c)}))})),function(){var e=document.querySelectorAll(".wdss-section .section-pin"),t=document.querySelectorAll(".wdss-section:not(#wdss-snippets-settings)");function s(){var e,t,s,c=this.closest(".wdss-section-header"),n=c.closest(".wdss-section").getAttribute("id");(JSON.parse(localStorage.getItem("PINNED_WDS_SECTIONS"))||[]).includes(n)?(this.classList.remove("active"),c.querySelector("h2").classList.remove("pinned"),c.querySelector("i.section-toggler").classList.remove("disabled"),e=n,-1!==(s=(t=JSON.parse(localStorage.getItem("PINNED_WDS_SECTIONS"))||[]).indexOf(e))&&t.splice(s,1),localStorage.setItem("PINNED_WDS_SECTIONS",JSON.stringify(t))):(this.classList.add("active"),c.classList.add("active"),function(e){var t=JSON.parse(localStorage.getItem("PINNED_WDS_SECTIONS"))||[];t.push(e),localStorage.setItem("PINNED_WDS_SECTIONS",JSON.stringify(t))}(n),c.querySelector("h2").classList.add("pinned"),c.querySelector("i.section-toggler").classList.add("disabled"))}!function(){var e;localStorage.getItem("PINNED_WDS_SECTIONS")?e=localStorage.getItem("PINNED_WDS_SECTIONS"):(e=[],localStorage.setItem("PINNED_WDS_SECTIONS",JSON.stringify(e)));var s=e;t.forEach((function(e){var t=e.getAttribute("id"),c=e.querySelector(".wdss-section-header"),n=c.nextElementSibling;s.includes(t)&&(e.classList.add("pinned"),c.querySelector("i.section-pin").classList.add("active"),c.querySelector("i.section-toggler").classList.add("disabled"),c.classList.add("active"),c.querySelector("h2").classList.add("pinned"),n.classList.remove("hidden"))}))}(),e.forEach((function(e){e.addEventListener("click",s)}))}(),wdss_localize.total_post_count>0&&(s(a),s(r),document.querySelector("#wdss-generate-orgname").addEventListener("click",(function(){e("#wdss-jsonld-schema-orgname input")})),n(p),c(u),c(m),c(g),i()),wdss_localize.is_polylang_exists&&wdss_localize.is_polylang_setup&&s(d),S=document.querySelector(".wdss-list-item-handler.add"),w=document.querySelector(".save-dictionary"),b=document.querySelector("#wdss-settings-page form"),L=document.querySelector(".wdss-list-table tbody"),_=document.querySelector("#wdss-410s-dictionary-url"),E=jQuery(".wdss-list-table tbody tr"),k=[],jQuery.each(E,(function(){k.push(this.id)})),w.addEventListener("click",(function(){var e=jQuery(".wdss-list-table tbody tr"),s=[];e.each((function(e,t){var c=t.querySelector("td:nth-of-type(1)").textContent;s.push(c)})),jQuery.ajax({url:wdss_localize.url,type:"post",dataType:"json",data:{action:"e410_dictionary_update",e410_dictionary:s,security:wdss_localize.e410_dictionary_nonce},success:function(e){var s=document.querySelector(".wdss-list-table-actions span");s&&s.remove(),w.insertAdjacentHTML("afterend",'<span class="msg successful">Table was updated</span>'),t(document.querySelector("span.msg"),1200),w.classList.add("saved")},fail:function(e){var s=document.querySelector(".wdss-list-table-actions span");s&&s.remove(),w.insertAdjacentHTML("afterend",'<span class="msg error">Error, look at information in console</span>'),t(document.querySelector("span.msg"),1200),console.log(e)}})})),S.addEventListener("click",(function(){if(_.value){w.classList.remove("saved");var e=_.value;_.value="",L.insertAdjacentHTML("beforeend",'\n      <tr id="'.concat(wdss_localize.wp_rand,'">\n        <td>').concat(e,'</td>\n        <td class="wdss-list-table__remove-item"><i class="fas fa-trash"></i></td>\n      </tr>\n      '))}})),jQuery(document).on("click",".wdss-list-table__remove-item i",(function(){w.classList.remove("saved"),confirm("Remove this rule from table?")&&this.closest("tr").remove()})),b.addEventListener("submit",(function(e){var t,s=jQuery(".wdss-list-table tbody tr"),c=[];jQuery.each(s,(function(){c.push(this.id)})),function(e,t){if(e.length!==t.length)return!1;for(var s=0;s<e.length;s++)if(!t.includes(e[s]))return!1;return!0}(k,c)&&(t=!0),t||w.classList.contains("saved")||confirm("All unsaved changes will be lost. Do you want to continue?")||e.preventDefault()})),n(v),c(f),h=document.querySelector("#wdss-advanced-jsonld-schema-condition input"),y=document.querySelector(".wdss-jsonld-schema-predifined-settings"),h.hasAttribute("checked")&&y.classList.add("disabled"),h.addEventListener("click",(function(){y.classList.contains("disabled")?y.classList.remove("disabled"):y.classList.add("disabled")})),document.querySelector("#wdss-get-title").addEventListener("click",(function(){e("#wdss-title-ending input")})),document.querySelector("#wdss-generate-email").addEventListener("click",(function(){e("#wdss-jsonld-schema-email input")})),s(l),Array.from(document.querySelectorAll('input[type="checkbox"')).forEach((function(e){e.addEventListener("click",(function(){e.hasAttribute("checked")?(e.removeAttribute("checked"),e.value=0,e.checked=!1):(e.setAttribute("checked","checked"),e.checked=!0,e.value=1)}))})),q=Array.from(document.querySelectorAll("#wdss-snippets-settings input")),document.querySelector("#wdss-toggle-options").addEventListener("click",(function(){q[0].hasAttribute("checked")?q.forEach((function(e){e.removeAttribute("checked"),e.checked=!1})):q.forEach((function(e){e.setAttribute("checked","checked"),e.checked=!0}))})),Array.from(document.querySelectorAll(".wdss-setting-item-accordion")).forEach((function(e){var t=e.nextElementSibling;e.addEventListener("click",(function(){t.classList.contains("active")?(e.classList.remove("opened"),t.classList.remove("active")):(e.classList.add("opened"),t.classList.add("active"))}))})))}))})();