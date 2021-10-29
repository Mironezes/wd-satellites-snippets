(()=>{"use strict";var e=function(e,t){setTimeout((function(){e.remove()}),t)};function t(e){var t=document.querySelector(e.toggler),s=t.hasAttribute("checked"),c=document.querySelector(e.target);s&&c.classList.toggle("hidden"),t.addEventListener("click",(function(){c.classList.toggle("hidden")}))}function s(e){var t=document.querySelector(e.button),s=document.querySelector(e.target);t.addEventListener("click",(function(){""!==s.value&&confirm("Are you sure?")&&(s.value="")}))}function c(e){var t=document.querySelector(e.select);t.addEventListener("click",(function(s){var c;s.preventDefault(),c&&c.open(),(c=wp.media({title:"Select Featured Images",multiple:e.is_multiple,library:{type:"image"},button:{text:"Select"}})).on("close",(function(){var s=t.closest("#wdss-featured-images-list"),n=c.state().get("selection"),i=new Array,o=new Array,r=0;if(s){n.forEach((function(e){i[r]=e.id,r++}));var a=i.join(",");document.querySelector(e.target).value=a}else{n.forEach((function(e){o[r]=e.attributes.url,r++}));var l=o.join(",");document.querySelector(e.target).value=l}})),c.on("open",(function(){var t=c.state().get("selection");document.querySelector(e.target).value.split(",").forEach((function(e){var s=wp.media.attachment(e);s.fetch(),t.add(s?[s]:[])}))})),c.open()}))}function n(){var e=document.querySelector("#wdss-title-clipping-excluded__choose"),t=document.querySelector("#wdss-title-clipping-excluded");function s(e){var t=document.querySelector("html");t.classList.add("fixed"),this.insertAdjacentHTML("beforeend",e),Array.from(document.querySelectorAll(".wdss-table-row.post")).forEach((function(e){e.addEventListener("click",(function(){var t=e.querySelector('.wdss-table-post__select input[type="checkbox"]');t.hasAttribute("checked")?function(e){e.removeAttribute("checked"),e.checked=!1}(t):function(e){e.setAttribute("checked","checked"),e.checked=!0}(t)}))}));var s=this.querySelector(".wdss-modal"),c=s.querySelector(".wdss-modal-header i"),n=s.querySelector(".wdss-button.submit"),i=this.querySelector('#wdss-title-clipping-excluded input[type="text"]');if(""!==i.value){var o=i.value.split(",");Array.from(s.querySelectorAll('.wdss-table-post__select input[type="checkbox"]')).forEach((function(e){o.includes(e.value)&&(e.setAttribute("checked","checked"),e.checked=!0)}))}function r(){s.remove(),t.classList.remove("fixed")}c.addEventListener("click",r),document.onkeydown=function(e){"Esc"!==e.key&&"Escape"!==e.key||r()},n.addEventListener("click",(function(){var e=s.querySelectorAll('.wdss-table-post__select input[type="checkbox"]:checked');console.log(e);var c,n=[];e.forEach((function(e){n.push(e.value)})),c=n.join(","),i.value=c,s.remove(),t.classList.remove("fixed")}))}e.addEventListener("click",(function(){jQuery.ajax({url:wdss_localize.url,type:"post",data:{action:"fetch_modal_content",security:wdss_localize.nonce},success:function(e){s.call(t,e)}})}))}var i=document.querySelector("#wdss-settings-page"),o={toggler:"#wdss-title-clipping-condition input",target:"#wdss-title-clipping-group"},r={toggler:"#wdss-auto-featured-image-condition input",target:"#wdss-featured-images-group"},a={toggler:"#wdss-advanced-jsonld-schema-condition input",target:"#wdss-advanced-jsonld-schema-group"},l={toggler:"#wdss-polylang-meta-data-condition input",target:"#wdss-polylang-meta-data-group"},d={button:"#wdss-title-clipping-excluded button.reset",target:"#wdss-title-clipping-excluded input"},u={button:"#wdss-title-clipping-by-date button.reset",target:"#wdss-title-clipping-by-date input"},m={button:"#wdss-featured-images-group button.reset",target:"#wdss-featured-images-group input"},g={button:"#wdss-jsonld-schema-logo button.reset",target:"#wdss-jsonld-schema-logo input"},f={select:"#wdss-featured-images__choose",target:"#wdss-featured-images-list input",is_multiple:!0},p={select:"#wdss_jsonld_schema_logo__choose",target:"#wdss-jsonld-schema-logo input",is_multiple:!1};document.addEventListener("DOMContentLoaded",(function(){var v,y,h,S,w,b,_,L,E,q;i&&(document.querySelectorAll(".wdss-section:not(#wdss-snippets-settings) > .wdss-row").forEach((function(e){e.classList.contains("pinned")||e.classList.add("hidden");var t=e.previousElementSibling,s=t.querySelectorAll(".section-toggler");function c(){t.classList.toggle("active"),e.classList.toggle("hidden")}s&&s.forEach((function(e){e.addEventListener("click",c)}))})),function(){var e=document.querySelectorAll(".wdss-section .section-pin"),t=document.querySelectorAll(".wdss-section:not(#wdss-snippets-settings)");function s(){var e,t,s,c=this.closest(".wdss-section-header"),n=c.closest(".wdss-section").getAttribute("id");(JSON.parse(localStorage.getItem("PINNED_WDS_SECTIONS"))||[]).includes(n)?(this.classList.remove("active"),c.querySelector("h2").classList.remove("pinned"),c.querySelector("i.section-toggler").classList.remove("disabled"),e=n,-1!==(s=(t=JSON.parse(localStorage.getItem("PINNED_WDS_SECTIONS"))||[]).indexOf(e))&&t.splice(s,1),localStorage.setItem("PINNED_WDS_SECTIONS",JSON.stringify(t))):(this.classList.add("active"),c.classList.add("active"),function(e){var t=JSON.parse(localStorage.getItem("PINNED_WDS_SECTIONS"))||[];t.push(e),localStorage.setItem("PINNED_WDS_SECTIONS",JSON.stringify(t))}(n),c.querySelector("h2").classList.add("pinned"),c.querySelector("i.section-toggler").classList.add("disabled"))}!function(){var e;localStorage.getItem("PINNED_WDS_SECTIONS")?e=localStorage.getItem("PINNED_WDS_SECTIONS"):(e=[],localStorage.setItem("PINNED_WDS_SECTIONS",JSON.stringify(e)));var s=e;t.forEach((function(e){var t=e.getAttribute("id"),c=e.querySelector(".wdss-section-header"),n=c.nextElementSibling;s.includes(t)&&(e.classList.add("pinned"),c.querySelector("i.section-pin").classList.add("active"),c.querySelector("i.section-toggler").classList.add("disabled"),c.classList.add("active"),c.querySelector("h2").classList.add("pinned"),n.classList.remove("hidden"))}))}(),e.forEach((function(e){e.addEventListener("click",s)}))}(),wdss_localize.total_post_count>0&&(t(o),t(r),function(){if(document.querySelector("#wdss-get-title")){var e=document.querySelector("#wdss-get-title"),t=document.querySelector("#wdss-title-ending input"),s=wdss_localize.site_title;e.addEventListener("click",(function(){return t.value=s}))}}(),c(f),s(d),s(u),s(m),n()),wdss_localize.is_polylang_exists&&t(l),h=document.querySelector(".wdss-list-item-handler.add"),S=document.querySelector(".save-dictionary"),w=document.querySelector("#wdss-settings-page form"),b=document.querySelector(".wdss-list-table tbody"),_=document.querySelector("#wdss-410s-dictionary-url"),L=jQuery(".wdss-list-table tbody tr"),E=[],jQuery.each(L,(function(){E.push(this.id)})),S.addEventListener("click",(function(){var t=jQuery(".wdss-list-table tbody tr"),s=[];t.each((function(e,t){var c=t.querySelector("td:nth-of-type(1)").textContent;s.push(c),console.log(s)})),jQuery.ajax({url:wdss_localize.url,type:"post",dataType:"json",data:{action:"e410_dictionary_update",e410_dictionary:s,security:wdss_localize.e410_dictionary_nonce},success:function(t){var s=document.querySelector(".wdss-list-table-actions span");s&&s.remove(),S.insertAdjacentHTML("afterend",'<span class="msg successful">Table was updated</span>'),e(document.querySelector("span.msg"),1200),S.classList.add("saved")},fail:function(t){var s=document.querySelector(".wdss-list-table-actions span");s&&s.remove(),S.insertAdjacentHTML("afterend",'<span class="msg error">Error, look at information in console</span>'),e(document.querySelector("span.msg"),1200),console.log(t)}})})),h.addEventListener("click",(function(){if(_.value){S.classList.remove("saved");var e=_.value;_.value="",b.insertAdjacentHTML("beforeend",'\n      <tr id="'.concat(wdss_localize.wp_rand,'">\n        <td>').concat(e,'</td>\n        <td class="wdss-list-table__remove-item"><i class="fas fa-trash"></i></td>\n      </tr>\n      '))}})),jQuery(document).on("click",".wdss-list-table__remove-item i",(function(){S.classList.remove("saved"),confirm("Remove this rule from table?")&&this.closest("tr").remove()})),w.addEventListener("submit",(function(e){var t,s=jQuery(".wdss-list-table tbody tr"),c=[];jQuery.each(s,(function(){c.push(this.id)})),function(e,t){if(e.length!==t.length)return!1;for(var s=0;s<e.length;s++)if(!t.includes(e[s]))return!1;return!0}(E,c)&&(t=!0),t||S.classList.contains("saved")||confirm("All unsaved changes will be lost. Do you want to continue?")||e.preventDefault()})),c(p),s(g),v=document.querySelector("#wdss-advanced-jsonld-schema-condition input"),y=document.querySelector(".wdss-jsonld-schema-predifined-settings"),v.hasAttribute("checked")&&y.classList.add("disabled"),v.addEventListener("click",(function(){y.classList.contains("disabled")?y.classList.remove("disabled"):y.classList.add("disabled")})),t(a),Array.from(document.querySelectorAll('input[type="checkbox"')).forEach((function(e){e.addEventListener("click",(function(){e.hasAttribute("checked")?(e.removeAttribute("checked"),e.value=0,e.checked=!1):(e.setAttribute("checked","checked"),e.checked=!0,e.value=1)}))})),q=Array.from(document.querySelectorAll("#wdss-snippets-settings input")),document.querySelector("#wdss-toggle-options").addEventListener("click",(function(){q[0].hasAttribute("checked")?q.forEach((function(e){e.removeAttribute("checked"),e.checked=!1})):q.forEach((function(e){e.setAttribute("checked","checked"),e.checked=!0}))})),Array.from(document.querySelectorAll(".wdss-setting-item-accordion")).forEach((function(e){var t=e.nextElementSibling;e.addEventListener("click",(function(){t.classList.contains("active")?(e.classList.remove("opened"),t.classList.remove("active")):(e.classList.add("opened"),t.classList.add("active"))}))})))}))})();