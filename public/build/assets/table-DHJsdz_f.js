import{a as y}from"./jquery-D4UJyEuM.js";import{K as r,t as P}from"./K-DYX4k3mD.js";import"./_commonjsHelpers-Cpj98o6Y.js";window.jQuery=y;window.$=y;window.K=r;$(()=>{let p=0;const R=$("#assetURL").val(),x=$('meta[name="csrf-token"]').attr("content"),g=$("#pushRows"),h=$("#spinner"),C=$("[is=row]"),L=$("#counter"),l=$("#pagination"),m=$("#length"),f=$("#future"),u=$("#search"),U=$(".sort-button"),c=()=>parseInt(m.val()||10),i=()=>parseInt(l.data("page")||1),n=()=>Math.ceil(p/c()),b=()=>u.val().trim(),v=()=>f.is(":checked")?1:0,k=()=>$(".sort-active").data("by"),w=()=>$(".sort-active").hasClass("sort-asc")?"asc":"desc",o=(s=!1)=>{s&&l.data("page",1)&&r.removeURLParam("page"),$("tr:not(.keep)",g).remove(),h.show(),$.ajax({url:R,method:"POST",data:{_token:x,length:c(),page:i(),future:v(),by:k(),dir:w(),search:b()},success:function(e){console.log(e),e.items&&e.items.length>0&&_(e.items);const a=(i()-1)*c()+1,t=`${a} to ${a+e.items.length-1} of ${e.filtered}`;L.text(`${t}${e.total!=e.filtered?` ( filtered from ${e.total} total )`:""}`),h.hide(),refreshAll(),p=e.filtered,I()}})},_=s=>{r.each(s,(e,a)=>{let t;t=C.clone(),t.data("modal",a.modal.edit),$(".hide-receipt",t).data("modal",a.modal.receipt),$(".hide-changes",t).data("modal",a.modal.changes),t.attr("id",`edit${a.id}`),a.has_changes||$(".hide-changes",t).addClass("hidden"),a.has_image||$(".hide-receipt",t).addClass("hidden"),a.is_future?t.addClass("is-future"):$(".hide-future",t).addClass("hidden"),a.is_repeat||$(".hide-repeat",t).addClass("hidden"),r.each(a,(d,K)=>{$(`.${d}`,t).html(K)}),g.append(t.removeClass("hidden skip-tooltip keep"))})},I=()=>{const s="cursor-pointer border border-gray-300 rounded-md px-1 min-w-6 bg-gray-100 text-center leading-7 shadow-sm",e=l;e.html(""),i()>1&&(e.append($("<i />",{class:`fal fa-angles-left ${s}`,page:1,title:"First"})),e.append($("<i />",{class:`fal fa-angle-left ${s}`,page:i()-1,title:"Previous"})));let a=i()-3,t=i()+3;a=a<1?1:a,t=t>n()?n():t,n()<=7?(a=1,t=n()):i()<=3?(a=1,t=7):t>=n()&&(a=n()-6,t=n());for(let d=a;d<=t;d++)e.append($("<span />",{class:`${i()===d?"border-indigo-300 text-indigo-500 active":"border-gray-300"}  cursor-pointer border rounded-md px-1 min-w-6 bg-gray-100 text-center leading-7 shadow-sm`,page:d,title:`Page ${d}`,text:d}));i()!=n()&&(e.append($("<i />",{class:`fal fa-angle-right ${s}`,page:i()+1,title:"Next"})),e.append($("<i />",{class:`fal fa-angles-right ${s}`,page:n(),title:"Last"})))},j=P();u.on("input",()=>{j.run(()=>{const s=b();s.length?r.addURLParam("search",s):r.removeURLParam("search"),o(!0)},800)}),l.on("click","[page]:not(.active)",s=>{const e=parseInt($(s.target).attr("page"));r.addURLParam("page",e),l.data("page",e),o()}),m.on("change",s=>{const e=parseInt($(s.target).val());r.addURLParam("length",e),o(!0)}),f.on("change",s=>{v()?r.addURLParam("future",1):r.removeURLParam("future"),o(!0)}),U.on("click",function(){const s=$(this).data("by"),e=$(this).hasClass("sort-asc")?"desc":"asc";$(".sort-button").removeClass("sort-asc sort-desc sort-active"),$(this).addClass(`sort-${e} sort-active`),r.addURLParam("by",s),r.addURLParam("dir",e),o(!0)}),P().run(()=>{o()},100)});
