import jQuery from "jquery";

/*
+--------------------------------------------------------------------+
                                                  
                                                  
/-`                                            `-/
dddhso/-`                                `-/oshddd
dddddddddhso/-`                    `-/oshddddddddd
dddddddddddddddhso++++++++++++++oshddddddddddddddd
dddddddddddddddddddddddddddddddddddddddddddddddddd
dddddddddddddddddddddddddddddddddddddddddddddddddd
dddddddddddddddddddddddddddddddddddddddddddddddddd
dddddddddddddddddddddddddddddddddddddddddddddddddd
dddddddddddddddddddddddddddddddddddddddddddddddddd
ddddddddddddds++sdddddddddddddddds++sddddddddddddd
dddddddddddd-    -dddddddddddddd-    -dddddddddddd
dddddddddddd-    -dddddddddddddd-    -dddddddddddd
ddddddddddddds++sdddddddddddddddds++sddddddddddddd
dddddddddddddddddddddddddddddddddddddddddddddddddd
dddddddddddddddddddddddddddddddddddddddddddddddddd
dddddddddddddddddddddddddddddddddddddddddddddddddd
dddddddddddddddddddddddddddddddddddddddddddddddddd
dddddddddddddddddddddddddddddddddddddddddddddddddd
dddddddddddddddddddddddddddddddddddddddddddddddddd


+--------------------------------------------------------------------+

    felixg.io - timeDropper jQuery

    Version: 1.2.0
    Realase: 2022-06-02
    Created by: Felice Gattuso
    Twitter: @felixggi 
    Instagram: @felixggi
    Docs: https://felixg.io/docs/guides/timedropper-jquery
    
+--------------------------------------------------------------------+

*/
!(function(t) {
    (t.fn.timeDropper = function(e, a) {
        return t(this).each(function() {
            var a,
                d = t(this),
                o = !1,
                i = !1,
                n = function(t) {
                    return t < 10 ? "0" + t : t;
                },
                r = t(".td-clock").length,
                s = null,
                l = t.extend({ format: "h:mm a", autoswitch: !1, meridians: !1, mousewheel: !1, setCurrentTime: !0, init_animation: "dropdown", quarters: !0, minutesSteps: !1 }, e);
            d.prop({ readonly: !0 }).addClass("td-input"),
                l.minutesSteps && ([5, 10, 15, 20, 25, 30].includes(l.minutesSteps) || (l.minutesSteps = !1)),
                t("body").append(
                    '      <div class="td-wrap td-n2 ' +
                    l.customClass +
                    '" id="td-clock-' +
                    r +
                    '">        <div class="td-overlay"></div>        <div class="td-clock td-init">          <div class="td-medirian">            <span class="td-icon-am td-n">AM</span>            <span class="td-icon-pm td-n">PM</span>          </div>          <div class="td-lancette">            <div></div><div></div>          </div>          <div class="td-quarters"></div>          <div class="td-time">            <span class="on"></span>:<span></span>          </div>          <div class="td-deg td-n">            <div class="td-select">              <svg xmlns="http://www.w3.org/2000/svg" width="95" height="37" viewBox="0 0 95 37"><g fill="none" fill-rule="evenodd" transform="translate(15.775 15.982)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="30" d="M0 0c9.823 3.873 20.525 6 31.724 6C42.912 6 53.604 3.877 63.42.012"/><circle stroke-width="0" cx="21.225" cy="5.018" r="3" /><circle stroke-width="0" cx="31.225" cy="5.018" r="3" /><circle stroke-width="0" cx="41.225" cy="5.018" r="3" /></g></svg>            </div>          </div>        </div>      </div>      '
                );
            var p = t("#td-clock-" + r),
                c = p.find(".td-overlay"),
                u = p.find(".td-clock"),
                m = -1,
                f = 0,
                h = 0,
                g = function() {
                    var t = u.find(".td-time span.on"),
                        e = parseInt(t.attr("data-id")),
                        deg;
                    0 == t.index() ? (deg = Math.round((360 * e) / 24)) : (deg = Math.round((360 * e) / 60)), (m = -1), (f = deg), (h = deg);
                },
                v = function(t) {
                    var e = u.find(".td-time span.on"),
                        a = e.attr("data-id");
                    a || (a = 0);
                    var o = Math.round((24 * t) / 360),
                        i = Math.round((60 * t) / 360);
                    if (
                        (24 == o && (o = 0),
                            60 == i && (i = 0),
                            0 == e.index()
                                ? (e.attr("data-id", n(o)),
                                    l.meridians &&
                                    (o >= 12 && o < 24 ? (u.find(".td-icon-pm").addClass("td-on"), u.find(".td-icon-am").removeClass("td-on")) : (u.find(".td-icon-am").addClass("td-on"), u.find(".td-icon-pm").removeClass("td-on")),
                                        o > 12 && (o -= 12),
                                        0 == o && (o = 12)),
                                    e.text(n(o)))
                                : ((l.minutesSteps && i % l.minutesSteps == 0) || !l.minutesSteps) && e.attr("data-id", n(i)).text(n(i)),
                            (h = t),
                            u.find(".td-deg").css("transform", "rotate(" + t + "deg)"),
                            0 == e.index())
                    ) {
                        var r = Math.round((360 * o) / 12);
                        u.find(".td-lancette div:last").css("transform", "rotate(" + r + "deg)");
                    } else ((l.minutesSteps && i % l.minutesSteps == 0) || !l.minutesSteps) && u.find(".td-lancette div:first").css("transform", "rotate(" + t + "deg)");
                    var s = u.find(".td-time span:first").attr("data-id"),
                        p = u.find(".td-time span:last").attr("data-id");
                    if (Math.round(s) >= 12 && Math.round(s) < 24) {
                        o = Math.round(s) - 12;
                        var c = "pm",
                            m = "PM";
                    } else (o = Math.round(s)), (c = "am"), (m = "AM");
                    0 == o && (o = 12);
                    var f = l.format
                        .replace(/\b(H)\b/g, Math.round(s))
                        .replace(/\b(h)\b/g, Math.round(o))
                        .replace(/\b(m)\b/g, Math.round(p))
                        .replace(/\b(HH)\b/g, n(Math.round(s)))
                        .replace(/\b(hh)\b/g, n(Math.round(o)))
                        .replace(/\b(mm)\b/g, n(Math.round(p)))
                        .replace(/\b(a)\b/g, c)
                        .replace(/\b(A)\b/g, m);
                    d.val(f).trigger("change");
                };
            /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) && (i = !0),
                u.find(".td-time span").on("click", function(e) {
                    var a = t(this),
                        deg;
                    u.find(".td-time span").removeClass("on"), a.addClass("on");
                    var d = parseInt(a.attr("data-id"));
                    0 == a.index() ? ((deg = Math.round((360 * d) / 24)), l.minutesSteps && u.find(".td-quarters").removeClass("td-show")) : ((deg = Math.round((360 * d) / 60)), l.minutesSteps && u.find(".td-quarters").addClass("td-show")),
                        (m = -1),
                        (f = deg),
                        (h = deg),
                        v(deg);
                }),
                u.find(".td-deg").on("touchstart mousedown", function(e) {
                    g(), e.preventDefault(), clearInterval(a), u.find(".td-deg").removeClass("td-n"), u.find(".td-select").removeClass("td-rubber"), (o = !0);
                    var d,
                        n,
                        r,
                        s,
                        l = u.offset(),
                        p = l.top + u.height() / 2,
                        c = l.left + u.width() / 2,
                        h = 180 / Math.PI;
                    u.removeClass("td-rubber"),
                        t(window).on("touchmove mousemove", function(t) {
                            var move;
                            1 == o &&
                                ((move = i ? t.originalEvent.touches[0] : t),
                                    (d = p - move.pageY),
                                    (n = c - move.pageX),
                                    (r = Math.atan2(d, n) * h) < 0 && (r = 360 + r),
                                    -1 == m && (m = r),
                                    (s = Math.floor(r - m + f)) < 0 ? (s = 360 + s) : s > 360 && (s %= 360),
                                    v(s));
                        });
                }),
                l.mousewheel &&
                u.on("mousewheel", function(t) {
                    t.preventDefault(),
                        u.find(".td-deg").removeClass("td-n"),
                        t.originalEvent.wheelDelta > 0
                            ? h <= 360 && (t.originalEvent.wheelDelta <= 120 ? h++ : t.originalEvent.wheelDelta > 120 && (h += 20), h > 360 && (h = 0))
                            : h >= 0 && (t.originalEvent.wheelDelta >= -120 ? h-- : t.originalEvent.wheelDelta < -120 && (h -= 20), h < 0 && (h = 360)),
                        (m = -1),
                        (f = h),
                        v(h);
                }),
                t(document).on("touchend mouseup", function() {
                    o && ((o = !1), l.autoswitch && (u.find(".td-time span").toggleClass("on"), u.find(".td-time span.on").click()), u.find(".td-deg").addClass("td-n"), u.find(".td-select").addClass("td-rubber"));
                });
            var b = function(e) {
                var a,
                    o,
                    i = new Date(),
                    r = u.find(".td-time span:first"),
                    s = u.find(".td-time span:last");
                if (l.minutesSteps) {
                    u.find(".td-quarters").empty();
                    for (var p = parseInt(60 / l.minutesSteps), c = 0, g = 1; g <= p; g++) {
                        var b = Math.round((360 * c) / 60);
                        u.find(".td-quarters").append('<div class="td-quarter" data-id="' + c + '" style="transform: rotate(' + b + 'deg)"><div class="td-quarter-bullet"></div></div>'), (c += l.minutesSteps);
                    }
                    var x = !1;
                    u.find(".td-quarter-bullet").hover(
                        function() {
                            (x = u.find(".td-time span:last").html()), u.find(".td-time span:last").html(t(this).closest(".td-quarter").attr("data-id")), t(this).addClass("td-on");
                        },
                        function() {
                            u.find(".td-time span:last").html(x), t(this).removeClass("td-on");
                        }
                    ),
                        u.find(".td-quarter").on("click", function(e) {
                            var a = t(this),
                                d = parseInt(a.attr("data-id"));
                            (x = d), (deg = Math.round((360 * d) / 60)), (m = -1), (f = deg), (h = deg), v(deg);
                        });
                }
                if (d.val().length) {
                    var w = /\d+/g,
                        k = d.val().split(":");
                    k
                        ? ((a = k[0].match(w)),
                            (o = k[1].match(w)),
                            -1 != d.val().indexOf("am") || -1 != d.val().indexOf("AM") || -1 != d.val().indexOf("pm") || -1 != d.val().indexOf("PM")
                                ? -1 != d.val().indexOf("am") || -1 != d.val().indexOf("AM")
                                    ? 12 == a && (a = 0)
                                    : a < 13 && 24 == (a = parseInt(a) + 12) && (a = 0)
                                : 24 == a && (a = 0))
                        : ((a = parseInt(r.text()) ? n(r.text()) : n(i.getHours())), (o = parseInt(s.text()) ? n(s.text()) : n(i.getMinutes())));
                } else (a = parseInt(r.text()) ? n(r.text()) : n(i.getHours())), (o = parseInt(s.text()) ? n(s.text()) : n(i.getMinutes()));
                l.minutesSteps && o % l.minutesSteps && (o = 0),
                    r.attr("data-id", a).text(a),
                    s.attr("data-id", o).text(o),
                    r.hasClass("on") && (f = Math.round((360 * a) / 24)),
                    s.hasClass("on") && (f = Math.round((360 * o) / 60)),
                    u.find(".td-lancette div:first").css("transform", "rotate(" + Math.round((360 * o) / 60) + "deg)"),
                    v(f),
                    (h = f),
                    (m = -1);
            };
            l.setCurrentTime && b(),
                d.focus(function(t) {
                    t.preventDefault(), d.blur();
                }),
                d.click(function(t) {
                    clearInterval(s),
                        b(),
                        (s = setTimeout(function() {
                            p.removeClass("td-fadeout"),
                                p.addClass("td-show").addClass("td-" + l.init_animation),
                                u.css({ top: d.offset().top + (d.outerHeight() - 8), left: d.offset().left + d.outerWidth() / 2 - u.outerWidth() / 2 }),
                                u.hasClass("td-init") &&
                                ((a = setInterval(function() {
                                    u.find(".td-select").addClass("td-alert"),
                                        setTimeout(function() {
                                            u.find(".td-select").removeClass("td-alert");
                                        }, 1e3);
                                }, 2e3)),
                                    u.removeClass("td-init"));
                        }, 10));
                }),
                c.click(function() {
                    p.addClass("td-fadeout").removeClass("td-" + l.init_animation),
                        (s = setTimeout(function() {
                            p.removeClass("td-show");
                        }, 300));
                }),
                t(window).on("resize", function() {
                    g(), u.css({ top: d.offset().top + (d.outerHeight() - 8), left: d.offset().left + d.outerWidth() / 2 - u.outerWidth() / 2 });
                });
        });
    }),
        t("head").append(
            '<style>.td-wrap,.td-wrap *{margin:0;padding:0;list-style:none;box-sizing:initial!important;-webkit-tap-highlight-color:rgba(0,0,0,0)}.td-wrap svg{width:100%}.td-input{cursor:pointer}.td-wrap{display:none;font-family:sans-serif;position:absolute;-webkit-user-select:none;-o-user-select:none;user-select:none;outline:none;width:100%;height:100%;top:0;left:0;z-index:9999;color:#4d4d4d}.td-overlay{position:fixed;top:0;left:0;width:100%;height:100%}.td-clock{left:initial!important;top:initial!important;width:192px;height:192px;border-radius:192px;box-shadow:0 0 64px rgba(0,0,0,0.085);position:relative;background:#FFF;margin:0 auto;text-align:center;line-height:192px;position:absolute;background-position:center;background-repeat:no-repeat;background-size:cover}.td-clock:before{position:absolute;top:-8px;margin-left:-10px;left:50%;width:20px;height:20px;transform:rotate(45deg);background:#FFF;border-top-left-radius:4px}.td-quarters{z-index:1;pointer-events:none;transform:scale(0.95);opacity:0;transition:opacity 0.4s ease,transform 0.4s ease}.td-quarters.td-show{opacity:1;transform:scale(1)}.td-quarters:hover .td-quarter-bullet:not(.td-on){opacity:0.35}.td-quarters,.td-quarters .td-quarter{width:100%;height:100%;position:absolute;top:0;left:0}.td-quarters .td-quarter-bullet{width:6px;height:8px;border-radius:12px;position:absolute;cursor:pointer;z-index:2;top:20px;left:50%;transform:translateX(-50%);cursor:pointer;pointer-events:auto;transition:opacity 0.4s ease,height 0.4s ease}.td-quarters .td-quarter-bullet:hover{height:16px}.td-init .td-deg{-webkit-animation:slide 1s cubic-bezier(0.7,0,0.175,1) 1.2s infinite}.td-svg{position:absolute;top:0;bottom:0;left:0;right:0}.td-svg-2{position:absolute;top:18px;left:18px;bottom:18px;right:18px}.td-wrap.td-show{display:flex;justify-content:center;align-items:center;}.td-deg{background-position:center;background-repeat:no-repeat;background-size:80%;position:absolute;z-index:1;pointer-events:none;top:0;left:0;right:0;bottom:0}.td-medirian{position:absolute;top:0;left:0;right:0;bottom:0;pointer-events:none}.td-medirian span{width:40px;height:40px;border-radius:40px;line-height:40px;text-align:center;margin:0;position:absolute;z-index:1;left:50%;margin-left:-20px;font-size:0.8em;opacity:0;font-weight:bold}.td-medirian .td-icon-am{top:60px}.td-medirian .td-icon-pm{bottom:60px}.td-medirian .td-icon-am.td-on{top:40px;opacity:1}.td-medirian .td-icon-pm.td-on{bottom:40px;opacity:1}.td-select{position:absolute;top:12px;left:32px;right:32px;bottom:32px;z-index:11}.td-select svg{position:absolute;top:0;left:0;right:0;cursor:pointer;transform:rotateX(180deg) scale(0.7);pointer-events:auto}.td-clock .td-time{font-weight:bold;position:relative}.td-clock .td-time span{width:42px;height:42px;display:inline-block;vertical-align:middle;line-height:42px;text-align:center;margin:6px;position:relative;z-index:2;cursor:pointer;font-size:2em;border-radius:6px}.td-n{transition:all 0.4s cubic-bezier(0.7,0,0.175,1) 0s}.td-n2{transition:all 0.2s linear 0s}@keyframes td-alert{0%{transform:scale3d(1,1,1)}10%,20%{transform:scale3d(0.9,0.9,0.9) rotate3d(0,0,1,-3deg)}30%,50%,70%,90%{transform:scale3d(1.1,1.1,1.1) rotate3d(0,0,1,3deg)}40%,60%,80%{transform:scale3d(1.1,1.1,1.1) rotate3d(0,0,1,-3deg)}to{transform:scale3d(1,1,1)}}.td-alert{animation-name:td-alert;animation-duration:0.8s;animation-fill-mode:both}@keyframes td-bounce{0%{transform:scale3d(1,1,1)}20%{transform:scale3d(1.25,0.75,1)}30%{transform:scale3d(0.75,1.25,1)}60%{transform:scale3d(1.15,0.85,1)}70%{transform:scale3d(0.95,1.05,1)}80%{transform:scale3d(1.05,0.95,1)}to{transform:scale3d(1,1,1)}}.td-bounce{animation-name:td-bounce;animation-duration:1s}@keyframes td-fadein{0%{opacity:0}to{opacity:1}}.td-fadein{animation-name:td-fadein;animation-duration:0.3s}@keyframes td-fadeout{0%{opacity:1}to{opacity:0}}.td-fadeout{animation:td-fadeout 0.3s forwards}@keyframes td-dropdown{0%{opacity:0;transform:translate3d(0,-32px,0)}to{opacity:1;transform:none}}.td-dropdown{animation-name:td-dropdown;animation-duration:0.5s}.td-bulletpoint,.td-bulletpoint div,.td-lancette,.td-lancette div{position:absolute;top:0;left:0;bottom:0;right:0}.td-bulletpoint div:after{position:absolute;content:"";top:14px;left:50%;margin-left:-2px;width:4px;height:4px;border-radius:10px}.td-lancette{border:4px solid #DFF3FA;border-radius:100%;margin:8px}.td-lancette div:after{position:absolute;top:20px;left:50%;margin-left:-1px;width:2px;bottom:50%;border-radius:10px;background:#DFF3FA;content:""}.td-lancette div:last-child:after{top:36px}.td-clock{color:var(--td-textColor);background:var(--td-backgroundColor)}.td-clock .td-time span.on{color:var(--td-primaryColor)}.td-quarter-bullet{background:var(--td-primaryColor)}.td-clock:before{border-color:var(--td-borderColor)}.td-clock:before,.td-select:after{background:var(--td-backgroundColor)}.td-lancette{border:var(--td-displayBorderWidth) var(--td-displayBorderStyle) var(--td-displayBorderColor);background:var(--td-displayBackgroundColor)}.td-lancette div:after{background:var(--td-handsColor)}.td-bulletpoint div:after{background:var(--td-primaryColor);opacity:0.1}.td-select svg path{stroke:var(--td-handleColor)}.td-select svg circle{fill:var(--td-handlePointColor)}:root{--td-textColor:#999999;--td-backgroundColor:#FFF;--td-primaryColor:#222222;--td-displayBackgroundColor:#FFF;--td-displayBorderColor:#A2E073;--td-displayBorderStyle:solid;--td-displayBorderWidth:4px;--td-handsColor:#A2E07350;--td-handleColor:#A2E073;--td-handlePointColor:white}</style>'
        );
})(jQuery);
