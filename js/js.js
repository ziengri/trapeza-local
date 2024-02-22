console.log('start');
$.ajaxSetup({ cache: false });
/* jQuery Form Plugin; v20130616 | https://github.com/malsup/form#copyright-and-license */
; (function (e) { "use strict"; function t(t) { var r = t.data; t.isDefaultPrevented() || (t.preventDefault(), e(this).ajaxSubmit(r)) } function r(t) { var r = t.target, a = e(r); if (!a.is("[type=submit],[type=image]")) { var n = a.closest("[type=submit]"); if (0 === n.length) return; r = n[0] } var i = this; if (i.clk = r, "image" == r.type) if (void 0 !== t.offsetX) i.clk_x = t.offsetX, i.clk_y = t.offsetY; else if ("function" == typeof e.fn.offset) { var o = a.offset(); i.clk_x = t.pageX - o.left, i.clk_y = t.pageY - o.top } else i.clk_x = t.pageX - r.offsetLeft, i.clk_y = t.pageY - r.offsetTop; setTimeout(function () { i.clk = i.clk_x = i.clk_y = null }, 100) } function a() { if (e.fn.ajaxSubmit.debug) { var t = "[jquery.form] " + Array.prototype.join.call(arguments, ""); window.console && window.console.log ? window.console.log(t) : window.opera && window.opera.postError && window.opera.postError(t) } } var n = {}; n.fileapi = void 0 !== e("<input type='file'/>").get(0).files, n.formdata = void 0 !== window.FormData; var i = !!e.fn.prop; e.fn.attr2 = function () { if (!i) return this.attr.apply(this, arguments); var e = this.prop.apply(this, arguments); return e && e.jquery || "string" == typeof e ? e : this.attr.apply(this, arguments) }, e.fn.ajaxSubmit = function (t) { function r(r) { var a, n, i = e.param(r, t.traditional).split("&"), o = i.length, s = []; for (a = 0; o > a; a++)i[a] = i[a].replace(/\+/g, " "), n = i[a].split("="), s.push([decodeURIComponent(n[0]), decodeURIComponent(n[1])]); return s } function o(a) { for (var n = new FormData, i = 0; a.length > i; i++)n.append(a[i].name, a[i].value); if (t.extraData) { var o = r(t.extraData); for (i = 0; o.length > i; i++)o[i] && n.append(o[i][0], o[i][1]) } t.data = null; var s = e.extend(!0, {}, e.ajaxSettings, t, { contentType: !1, processData: !1, cache: !1, type: u || "POST" }); t.uploadProgress && (s.xhr = function () { var r = e.ajaxSettings.xhr(); return r.upload && r.upload.addEventListener("progress", function (e) { var r = 0, a = e.loaded || e.position, n = e.total; e.lengthComputable && (r = Math.ceil(100 * (a / n))), t.uploadProgress(e, a, n, r) }, !1), r }), s.data = null; var l = s.beforeSend; return s.beforeSend = function (e, t) { t.data = n, l && l.call(this, e, t) }, e.ajax(s) } function s(r) { function n(e) { var t = null; try { e.contentWindow && (t = e.contentWindow.document) } catch (r) { a("cannot get iframe.contentWindow document: " + r) } if (t) return t; try { t = e.contentDocument ? e.contentDocument : e.document } catch (r) { a("cannot get iframe.contentDocument: " + r), t = e.document } return t } function o() { function t() { try { var e = n(g).readyState; a("state = " + e), e && "uninitialized" == e.toLowerCase() && setTimeout(t, 50) } catch (r) { a("Server abort: ", r, " (", r.name, ")"), s(D), j && clearTimeout(j), j = void 0 } } var r = f.attr2("target"), i = f.attr2("action"); w.setAttribute("target", d), u || w.setAttribute("method", "POST"), i != m.url && w.setAttribute("action", m.url), m.skipEncodingOverride || u && !/post/i.test(u) || f.attr({ encoding: "multipart/form-data", enctype: "multipart/form-data" }), m.timeout && (j = setTimeout(function () { T = !0, s(k) }, m.timeout)); var o = []; try { if (m.extraData) for (var l in m.extraData) m.extraData.hasOwnProperty(l) && (e.isPlainObject(m.extraData[l]) && m.extraData[l].hasOwnProperty("name") && m.extraData[l].hasOwnProperty("value") ? o.push(e('<input type="hidden" name="' + m.extraData[l].name + '">').val(m.extraData[l].value).appendTo(w)[0]) : o.push(e('<input type="hidden" name="' + l + '">').val(m.extraData[l]).appendTo(w)[0])); m.iframeTarget || (v.appendTo("body"), g.attachEvent ? g.attachEvent("onload", s) : g.addEventListener("load", s, !1)), setTimeout(t, 15); try { w.submit() } catch (c) { var p = document.createElement("form").submit; p.apply(w) } } finally { w.setAttribute("action", i), r ? w.setAttribute("target", r) : f.removeAttr("target"), e(o).remove() } } function s(t) { if (!x.aborted && !F) { if (M = n(g), M || (a("cannot access response document"), t = D), t === k && x) return x.abort("timeout"), S.reject(x, "timeout"), void 0; if (t == D && x) return x.abort("server abort"), S.reject(x, "error", "server abort"), void 0; if (M && M.location.href != m.iframeSrc || T) { g.detachEvent ? g.detachEvent("onload", s) : g.removeEventListener("load", s, !1); var r, i = "success"; try { if (T) throw "timeout"; var o = "xml" == m.dataType || M.XMLDocument || e.isXMLDoc(M); if (a("isXml=" + o), !o && window.opera && (null === M.body || !M.body.innerHTML) && --O) return a("requeing onLoad callback, DOM not available"), setTimeout(s, 250), void 0; var u = M.body ? M.body : M.documentElement; x.responseText = u ? u.innerHTML : null, x.responseXML = M.XMLDocument ? M.XMLDocument : M, o && (m.dataType = "xml"), x.getResponseHeader = function (e) { var t = { "content-type": m.dataType }; return t[e] }, u && (x.status = Number(u.getAttribute("status")) || x.status, x.statusText = u.getAttribute("statusText") || x.statusText); var l = (m.dataType || "").toLowerCase(), c = /(json|script|text)/.test(l); if (c || m.textarea) { var f = M.getElementsByTagName("textarea")[0]; if (f) x.responseText = f.value, x.status = Number(f.getAttribute("status")) || x.status, x.statusText = f.getAttribute("statusText") || x.statusText; else if (c) { var d = M.getElementsByTagName("pre")[0], h = M.getElementsByTagName("body")[0]; d ? x.responseText = d.textContent ? d.textContent : d.innerText : h && (x.responseText = h.textContent ? h.textContent : h.innerText) } } else "xml" == l && !x.responseXML && x.responseText && (x.responseXML = X(x.responseText)); try { L = _(x, l, m) } catch (b) { i = "parsererror", x.error = r = b || i } } catch (b) { a("error caught: ", b), i = "error", x.error = r = b || i } x.aborted && (a("upload aborted"), i = null), x.status && (i = x.status >= 200 && 300 > x.status || 304 === x.status ? "success" : "error"), "success" === i ? (m.success && m.success.call(m.context, L, "success", x), S.resolve(x.responseText, "success", x), p && e.event.trigger("ajaxSuccess", [x, m])) : i && (void 0 === r && (r = x.statusText), m.error && m.error.call(m.context, x, i, r), S.reject(x, "error", r), p && e.event.trigger("ajaxError", [x, m, r])), p && e.event.trigger("ajaxComplete", [x, m]), p && !--e.active && e.event.trigger("ajaxStop"), m.complete && m.complete.call(m.context, x, i), F = !0, m.timeout && clearTimeout(j), setTimeout(function () { m.iframeTarget || v.remove(), x.responseXML = null }, 100) } } } var l, c, m, p, d, v, g, x, b, y, T, j, w = f[0], S = e.Deferred(); if (r) for (c = 0; h.length > c; c++)l = e(h[c]), i ? l.prop("disabled", !1) : l.removeAttr("disabled"); if (m = e.extend(!0, {}, e.ajaxSettings, t), m.context = m.context || m, d = "jqFormIO" + (new Date).getTime(), m.iframeTarget ? (v = e(m.iframeTarget), y = v.attr2("name"), y ? d = y : v.attr2("name", d)) : (v = e('<iframe name="' + d + '" src="' + m.iframeSrc + '" />'), v.css({ position: "absolute", top: "-1000px", left: "-1000px" })), g = v[0], x = { aborted: 0, responseText: null, responseXML: null, status: 0, statusText: "n/a", getAllResponseHeaders: function () { }, getResponseHeader: function () { }, setRequestHeader: function () { }, abort: function (t) { var r = "timeout" === t ? "timeout" : "aborted"; a("aborting upload... " + r), this.aborted = 1; try { g.contentWindow.document.execCommand && g.contentWindow.document.execCommand("Stop") } catch (n) { } v.attr("src", m.iframeSrc), x.error = r, m.error && m.error.call(m.context, x, r, t), p && e.event.trigger("ajaxError", [x, m, r]), m.complete && m.complete.call(m.context, x, r) } }, p = m.global, p && 0 === e.active++ && e.event.trigger("ajaxStart"), p && e.event.trigger("ajaxSend", [x, m]), m.beforeSend && m.beforeSend.call(m.context, x, m) === !1) return m.global && e.active--, S.reject(), S; if (x.aborted) return S.reject(), S; b = w.clk, b && (y = b.name, y && !b.disabled && (m.extraData = m.extraData || {}, m.extraData[y] = b.value, "image" == b.type && (m.extraData[y + ".x"] = w.clk_x, m.extraData[y + ".y"] = w.clk_y))); var k = 1, D = 2, A = e("meta[name=csrf-token]").attr("content"), E = e("meta[name=csrf-param]").attr("content"); E && A && (m.extraData = m.extraData || {}, m.extraData[E] = A), m.forceSync ? o() : setTimeout(o, 10); var L, M, F, O = 50, X = e.parseXML || function (e, t) { return window.ActiveXObject ? (t = new ActiveXObject("Microsoft.XMLDOM"), t.async = "false", t.loadXML(e)) : t = (new DOMParser).parseFromString(e, "text/xml"), t && t.documentElement && "parsererror" != t.documentElement.nodeName ? t : null }, C = e.parseJSON || function (e) { return window.eval("(" + e + ")") }, _ = function (t, r, a) { var n = t.getResponseHeader("content-type") || "", i = "xml" === r || !r && n.indexOf("xml") >= 0, o = i ? t.responseXML : t.responseText; return i && "parsererror" === o.documentElement.nodeName && e.error && e.error("parsererror"), a && a.dataFilter && (o = a.dataFilter(o, r)), "string" == typeof o && ("json" === r || !r && n.indexOf("json") >= 0 ? o = C(o) : ("script" === r || !r && n.indexOf("javascript") >= 0) && e.globalEval(o)), o }; return S } if (!this.length) return a("ajaxSubmit: skipping submit process - no element selected"), this; var u, l, c, f = this; "function" == typeof t && (t = { success: t }), u = t.type || this.attr2("method"), l = t.url || this.attr2("action"), c = "string" == typeof l ? e.trim(l) : "", c = c || window.location.href || "", c && (c = (c.match(/^([^#]+)/) || [])[1]), t = e.extend(!0, { url: c, success: e.ajaxSettings.success, type: u || "GET", iframeSrc: /^https/i.test(window.location.href || "") ? "javascript:false" : "about:blank" }, t); var m = {}; if (this.trigger("form-pre-serialize", [this, t, m]), m.veto) return a("ajaxSubmit: submit vetoed via form-pre-serialize trigger"), this; if (t.beforeSerialize && t.beforeSerialize(this, t) === !1) return a("ajaxSubmit: submit aborted via beforeSerialize callback"), this; var p = t.traditional; void 0 === p && (p = e.ajaxSettings.traditional); var d, h = [], v = this.formToArray(t.semantic, h); if (t.data && (t.extraData = t.data, d = e.param(t.data, p)), t.beforeSubmit && t.beforeSubmit(v, this, t) === !1) return a("ajaxSubmit: submit aborted via beforeSubmit callback"), this; if (this.trigger("form-submit-validate", [v, this, t, m]), m.veto) return a("ajaxSubmit: submit vetoed via form-submit-validate trigger"), this; var g = e.param(v, p); d && (g = g ? g + "&" + d : d), "GET" == t.type.toUpperCase() ? (t.url += (t.url.indexOf("?") >= 0 ? "&" : "?") + g, t.data = null) : t.data = g; var x = []; if (t.resetForm && x.push(function () { f.resetForm() }), t.clearForm && x.push(function () { f.clearForm(t.includeHidden) }), !t.dataType && t.target) { var b = t.success || function () { }; x.push(function (r) { var a = t.replaceTarget ? "replaceWith" : "html"; e(t.target)[a](r).each(b, arguments) }) } else t.success && x.push(t.success); if (t.success = function (e, r, a) { for (var n = t.context || this, i = 0, o = x.length; o > i; i++)x[i].apply(n, [e, r, a || f, f]) }, t.error) { var y = t.error; t.error = function (e, r, a) { var n = t.context || this; y.apply(n, [e, r, a, f]) } } if (t.complete) { var T = t.complete; t.complete = function (e, r) { var a = t.context || this; T.apply(a, [e, r, f]) } } var j = e('input[type=file]:enabled[value!=""]', this), w = j.length > 0, S = "multipart/form-data", k = f.attr("enctype") == S || f.attr("encoding") == S, D = n.fileapi && n.formdata; a("fileAPI :" + D); var A, E = (w || k) && !D; t.iframe !== !1 && (t.iframe || E) ? t.closeKeepAlive ? e.get(t.closeKeepAlive, function () { A = s(v) }) : A = s(v) : A = (w || k) && D ? o(v) : e.ajax(t), f.removeData("jqxhr").data("jqxhr", A); for (var L = 0; h.length > L; L++)h[L] = null; return this.trigger("form-submit-notify", [this, t]), this }, e.fn.ajaxForm = function (n) { if (n = n || {}, n.delegation = n.delegation && e.isFunction(e.fn.on), !n.delegation && 0 === this.length) { var i = { s: this.selector, c: this.context }; return !e.isReady && i.s ? (a("DOM not ready, queuing ajaxForm"), e(function () { e(i.s, i.c).ajaxForm(n) }), this) : (a("terminating; zero elements found by selector" + (e.isReady ? "" : " (DOM not ready)")), this) } return n.delegation ? (e(document).off("submit.form-plugin", this.selector, t).off("click.form-plugin", this.selector, r).on("submit.form-plugin", this.selector, n, t).on("click.form-plugin", this.selector, n, r), this) : this.ajaxFormUnbind().bind("submit.form-plugin", n, t).bind("click.form-plugin", n, r) }, e.fn.ajaxFormUnbind = function () { return this.unbind("submit.form-plugin click.form-plugin") }, e.fn.formToArray = function (t, r) { var a = []; if (0 === this.length) return a; var i = this[0], o = t ? i.getElementsByTagName("*") : i.elements; if (!o) return a; var s, u, l, c, f, m, p; for (s = 0, m = o.length; m > s; s++)if (f = o[s], l = f.name, l && !f.disabled) if (t && i.clk && "image" == f.type) i.clk == f && (a.push({ name: l, value: e(f).val(), type: f.type }), a.push({ name: l + ".x", value: i.clk_x }, { name: l + ".y", value: i.clk_y })); else if (c = e.fieldValue(f, !0), c && c.constructor == Array) for (r && r.push(f), u = 0, p = c.length; p > u; u++)a.push({ name: l, value: c[u] }); else if (n.fileapi && "file" == f.type) { r && r.push(f); var d = f.files; if (d.length) for (u = 0; d.length > u; u++)a.push({ name: l, value: d[u], type: f.type }); else a.push({ name: l, value: "", type: f.type }) } else null !== c && c !== void 0 && (r && r.push(f), a.push({ name: l, value: c, type: f.type, required: f.required })); if (!t && i.clk) { var h = e(i.clk), v = h[0]; l = v.name, l && !v.disabled && "image" == v.type && (a.push({ name: l, value: h.val() }), a.push({ name: l + ".x", value: i.clk_x }, { name: l + ".y", value: i.clk_y })) } return a }, e.fn.formSerialize = function (t) { return e.param(this.formToArray(t)) }, e.fn.fieldSerialize = function (t) { var r = []; return this.each(function () { var a = this.name; if (a) { var n = e.fieldValue(this, t); if (n && n.constructor == Array) for (var i = 0, o = n.length; o > i; i++)r.push({ name: a, value: n[i] }); else null !== n && n !== void 0 && r.push({ name: this.name, value: n }) } }), e.param(r) }, e.fn.fieldValue = function (t) { for (var r = [], a = 0, n = this.length; n > a; a++) { var i = this[a], o = e.fieldValue(i, t); null === o || void 0 === o || o.constructor == Array && !o.length || (o.constructor == Array ? e.merge(r, o) : r.push(o)) } return r }, e.fieldValue = function (t, r) { var a = t.name, n = t.type, i = t.tagName.toLowerCase(); if (void 0 === r && (r = !0), r && (!a || t.disabled || "reset" == n || "button" == n || ("checkbox" == n || "radio" == n) && !t.checked || ("submit" == n || "image" == n) && t.form && t.form.clk != t || "select" == i && -1 == t.selectedIndex)) return null; if ("select" == i) { var o = t.selectedIndex; if (0 > o) return null; for (var s = [], u = t.options, l = "select-one" == n, c = l ? o + 1 : u.length, f = l ? o : 0; c > f; f++) { var m = u[f]; if (m.selected) { var p = m.value; if (p || (p = m.attributes && m.attributes.value && !m.attributes.value.specified ? m.text : m.value), l) return p; s.push(p) } } return s } return e(t).val() }, e.fn.clearForm = function (t) { return this.each(function () { e("input,select,textarea", this).clearFields(t) }) }, e.fn.clearFields = e.fn.clearInputs = function (t) { var r = /^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i; return this.each(function () { var a = this.type, n = this.tagName.toLowerCase(); r.test(a) || "textarea" == n ? this.value = "" : "checkbox" == a || "radio" == a ? this.checked = !1 : "select" == n ? this.selectedIndex = -1 : "file" == a ? /MSIE/.test(navigator.userAgent) ? e(this).replaceWith(e(this).clone(!0)) : e(this).val("") : t && (t === !0 && /hidden/.test(a) || "string" == typeof t && e(this).is(t)) && (this.value = "") }) }, e.fn.resetForm = function () { return this.each(function () { ("function" == typeof this.reset || "object" == typeof this.reset && !this.reset.nodeType) && this.reset() }) }, e.fn.enable = function (e) { return void 0 === e && (e = !0), this.each(function () { this.disabled = !e }) }, e.fn.selected = function (t) { return void 0 === t && (t = !0), this.each(function () { var r = this.type; if ("checkbox" == r || "radio" == r) this.checked = t; else if ("option" == this.tagName.toLowerCase()) { var a = e(this).parent("select"); t && a[0] && "select-one" == a[0].type && a.find("option").selected(!1), this.selected = t } }) }, e.fn.ajaxSubmit.debug = !1 })(jQuery);

/* WOW - v1.1.2 - 2015-04-07 */
(function () { var a, b, c, d, e, f = function (a, b) { return function () { return a.apply(b, arguments) } }, g = [].indexOf || function (a) { for (var b = 0, c = this.length; c > b; b++)if (b in this && this[b] === a) return b; return -1 }; b = function () { function a() { } return a.prototype.extend = function (a, b) { var c, d; for (c in b) d = b[c], null == a[c] && (a[c] = d); return a }, a.prototype.isMobile = function (a) { return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(a) }, a.prototype.createEvent = function (a, b, c, d) { var e; return null == b && (b = !1), null == c && (c = !1), null == d && (d = null), null != document.createEvent ? (e = document.createEvent("CustomEvent"), e.initCustomEvent(a, b, c, d)) : null != document.createEventObject ? (e = document.createEventObject(), e.eventType = a) : e.eventName = a, e }, a.prototype.emitEvent = function (a, b) { return null != a.dispatchEvent ? a.dispatchEvent(b) : b in (null != a) ? a[b]() : "on" + b in (null != a) ? a["on" + b]() : void 0 }, a.prototype.addEvent = function (a, b, c) { return null != a.addEventListener ? a.addEventListener(b, c, !1) : null != a.attachEvent ? a.attachEvent("on" + b, c) : a[b] = c }, a.prototype.removeEvent = function (a, b, c) { return null != a.removeEventListener ? a.removeEventListener(b, c, !1) : null != a.detachEvent ? a.detachEvent("on" + b, c) : delete a[b] }, a.prototype.innerHeight = function () { return "innerHeight" in window ? window.innerHeight : document.documentElement.clientHeight }, a }(), c = this.WeakMap || this.MozWeakMap || (c = function () { function a() { this.keys = [], this.values = [] } return a.prototype.get = function (a) { var b, c, d, e, f; for (f = this.keys, b = d = 0, e = f.length; e > d; b = ++d)if (c = f[b], c === a) return this.values[b] }, a.prototype.set = function (a, b) { var c, d, e, f, g; for (g = this.keys, c = e = 0, f = g.length; f > e; c = ++e)if (d = g[c], d === a) return void (this.values[c] = b); return this.keys.push(a), this.values.push(b) }, a }()), a = this.MutationObserver || this.WebkitMutationObserver || this.MozMutationObserver || (a = function () { function a() { "undefined" != typeof console && null !== console && console.warn("MutationObserver is not supported by your browser."), "undefined" != typeof console && null !== console && console.warn("WOW.js cannot detect dom mutations, please call .sync() after loading new content.") } return a.notSupported = !0, a.prototype.observe = function () { }, a }()), d = this.getComputedStyle || function (a) { return this.getPropertyValue = function (b) { var c; return "float" === b && (b = "styleFloat"), e.test(b) && b.replace(e, function (a, b) { return b.toUpperCase() }), (null != (c = a.currentStyle) ? c[b] : void 0) || null }, this }, e = /(\-([a-z]){1})/g, this.WOW = function () { function e(a) { null == a && (a = {}), this.scrollCallback = f(this.scrollCallback, this), this.scrollHandler = f(this.scrollHandler, this), this.resetAnimation = f(this.resetAnimation, this), this.start = f(this.start, this), this.scrolled = !0, this.config = this.util().extend(a, this.defaults), this.animationNameCache = new c, this.wowEvent = this.util().createEvent(this.config.boxClass) } return e.prototype.defaults = { boxClass: "wow", animateClass: "animated", offset: 0, mobile: !0, live: !0, callback: null }, e.prototype.init = function () { var a; return this.element = window.document.documentElement, "interactive" === (a = document.readyState) || "complete" === a ? this.start() : this.util().addEvent(document, "DOMContentLoaded", this.start), this.finished = [] }, e.prototype.start = function () { var b, c, d, e; if (this.stopped = !1, this.boxes = function () { var a, c, d, e; for (d = this.element.querySelectorAll("." + this.config.boxClass), e = [], a = 0, c = d.length; c > a; a++)b = d[a], e.push(b); return e }.call(this), this.all = function () { var a, c, d, e; for (d = this.boxes, e = [], a = 0, c = d.length; c > a; a++)b = d[a], e.push(b); return e }.call(this), this.boxes.length) if (this.disabled()) this.resetStyle(); else for (e = this.boxes, c = 0, d = e.length; d > c; c++)b = e[c], this.applyStyle(b, !0); return this.disabled() || (this.util().addEvent(window, "scroll", this.scrollHandler), this.util().addEvent(window, "resize", this.scrollHandler), this.interval = setInterval(this.scrollCallback, 50)), this.config.live ? new a(function (a) { return function (b) { var c, d, e, f, g; for (g = [], c = 0, d = b.length; d > c; c++)f = b[c], g.push(function () { var a, b, c, d; for (c = f.addedNodes || [], d = [], a = 0, b = c.length; b > a; a++)e = c[a], d.push(this.doSync(e)); return d }.call(a)); return g } }(this)).observe(document.body, { childList: !0, subtree: !0 }) : void 0 }, e.prototype.stop = function () { return this.stopped = !0, this.util().removeEvent(window, "scroll", this.scrollHandler), this.util().removeEvent(window, "resize", this.scrollHandler), null != this.interval ? clearInterval(this.interval) : void 0 }, e.prototype.sync = function () { return a.notSupported ? this.doSync(this.element) : void 0 }, e.prototype.doSync = function (a) { var b, c, d, e, f; if (null == a && (a = this.element), 1 === a.nodeType) { for (a = a.parentNode || a, e = a.querySelectorAll("." + this.config.boxClass), f = [], c = 0, d = e.length; d > c; c++)b = e[c], g.call(this.all, b) < 0 ? (this.boxes.push(b), this.all.push(b), this.stopped || this.disabled() ? this.resetStyle() : this.applyStyle(b, !0), f.push(this.scrolled = !0)) : f.push(void 0); return f } }, e.prototype.show = function (a) { return this.applyStyle(a), a.className = a.className + " " + this.config.animateClass, null != this.config.callback && this.config.callback(a), this.util().emitEvent(a, this.wowEvent), this.util().addEvent(a, "animationend", this.resetAnimation), this.util().addEvent(a, "oanimationend", this.resetAnimation), this.util().addEvent(a, "webkitAnimationEnd", this.resetAnimation), this.util().addEvent(a, "MSAnimationEnd", this.resetAnimation), a }, e.prototype.applyStyle = function (a, b) { var c, d, e; return d = a.getAttribute("data-wow-duration"), c = a.getAttribute("data-wow-delay"), e = a.getAttribute("data-wow-iteration"), this.animate(function (f) { return function () { return f.customStyle(a, b, d, c, e) } }(this)) }, e.prototype.animate = function () { return "requestAnimationFrame" in window ? function (a) { return window.requestAnimationFrame(a) } : function (a) { return a() } }(), e.prototype.resetStyle = function () { var a, b, c, d, e; for (d = this.boxes, e = [], b = 0, c = d.length; c > b; b++)a = d[b], e.push(a.style.visibility = "visible"); return e }, e.prototype.resetAnimation = function (a) { var b; return a.type.toLowerCase().indexOf("animationend") >= 0 ? (b = a.target || a.srcElement, b.className = b.className.replace(this.config.animateClass, "").trim()) : void 0 }, e.prototype.customStyle = function (a, b, c, d, e) { return b && this.cacheAnimationName(a), a.style.visibility = b ? "hidden" : "visible", c && this.vendorSet(a.style, { animationDuration: c }), d && this.vendorSet(a.style, { animationDelay: d }), e && this.vendorSet(a.style, { animationIterationCount: e }), this.vendorSet(a.style, { animationName: b ? "none" : this.cachedAnimationName(a) }), a }, e.prototype.vendors = ["moz", "webkit"], e.prototype.vendorSet = function (a, b) { var c, d, e, f; d = []; for (c in b) e = b[c], a["" + c] = e, d.push(function () { var b, d, g, h; for (g = this.vendors, h = [], b = 0, d = g.length; d > b; b++)f = g[b], h.push(a["" + f + c.charAt(0).toUpperCase() + c.substr(1)] = e); return h }.call(this)); return d }, e.prototype.vendorCSS = function (a, b) { var c, e, f, g, h, i; for (h = d(a), g = h.getPropertyCSSValue(b), f = this.vendors, c = 0, e = f.length; e > c; c++)i = f[c], g = g || h.getPropertyCSSValue("-" + i + "-" + b); return g }, e.prototype.animationName = function (a) { var b; try { b = this.vendorCSS(a, "animation-name").cssText } catch (c) { b = d(a).getPropertyValue("animation-name") } return "none" === b ? "" : b }, e.prototype.cacheAnimationName = function (a) { return this.animationNameCache.set(a, this.animationName(a)) }, e.prototype.cachedAnimationName = function (a) { return this.animationNameCache.get(a) }, e.prototype.scrollHandler = function () { return this.scrolled = !0 }, e.prototype.scrollCallback = function () { var a; return !this.scrolled || (this.scrolled = !1, this.boxes = function () { var b, c, d, e; for (d = this.boxes, e = [], b = 0, c = d.length; c > b; b++)a = d[b], a && (this.isVisible(a) ? this.show(a) : e.push(a)); return e }.call(this), this.boxes.length || this.config.live) ? void 0 : this.stop() }, e.prototype.offsetTop = function (a) { for (var b; void 0 === a.offsetTop;)a = a.parentNode; for (b = a.offsetTop; a = a.offsetParent;)b += a.offsetTop; return b }, e.prototype.isVisible = function (a) { var b, c, d, e, f; return c = a.getAttribute("data-wow-offset") || this.config.offset, f = window.pageYOffset, e = f + Math.min(this.element.clientHeight, this.util().innerHeight()) - c, d = this.offsetTop(a), b = d + a.clientHeight, e >= d && b >= f }, e.prototype.util = function () { return null != this._util ? this._util : this._util = new b }, e.prototype.disabled = function () { return !this.config.mobile && this.util().isMobile(navigator.userAgent) }, e }() }).call(this);
// WOW.js
new WOW().init();


/**
 * Owl Carousel v2.2.1
 * Copyright 2013-2017 David Deutsch
 * Licensed under  ()
 */
!function (a, b, c, d) { function e(b, c) { this.settings = null, this.options = a.extend({}, e.Defaults, c), this.$element = a(b), this._handlers = {}, this._plugins = {}, this._supress = {}, this._current = null, this._speed = null, this._coordinates = [], this._breakpoint = null, this._width = null, this._items = [], this._clones = [], this._mergers = [], this._widths = [], this._invalidated = {}, this._pipe = [], this._drag = { time: null, target: null, pointer: null, stage: { start: null, current: null }, direction: null }, this._states = { current: {}, tags: { initializing: ["busy"], animating: ["busy"], dragging: ["interacting"] } }, a.each(["onResize", "onThrottledResize"], a.proxy(function (b, c) { this._handlers[c] = a.proxy(this[c], this) }, this)), a.each(e.Plugins, a.proxy(function (a, b) { this._plugins[a.charAt(0).toLowerCase() + a.slice(1)] = new b(this) }, this)), a.each(e.Workers, a.proxy(function (b, c) { this._pipe.push({ filter: c.filter, run: a.proxy(c.run, this) }) }, this)), this.setup(), this.initialize() } e.Defaults = { items: 3, loop: !1, center: !1, rewind: !1, mouseDrag: !0, touchDrag: !0, pullDrag: !0, freeDrag: !1, margin: 0, stagePadding: 0, merge: !1, mergeFit: !0, autoWidth: !1, startPosition: 0, rtl: !1, smartSpeed: 250, fluidSpeed: !1, dragEndSpeed: !1, responsive: {}, responsiveRefreshRate: 200, responsiveBaseElement: b, fallbackEasing: "swing", info: !1, nestedItemSelector: !1, itemElement: "div", stageElement: "div", refreshClass: "owl-refresh", loadedClass: "owl-loaded", loadingClass: "owl-loading", rtlClass: "owl-rtl", responsiveClass: "owl-responsive", dragClass: "owl-drag", itemClass: "owl-item", stageClass: "owl-stage", stageOuterClass: "owl-stage-outer", grabClass: "owl-grab" }, e.Width = { Default: "default", Inner: "inner", Outer: "outer" }, e.Type = { Event: "event", State: "state" }, e.Plugins = {}, e.Workers = [{ filter: ["width", "settings"], run: function () { this._width = this.$element.width() } }, { filter: ["width", "items", "settings"], run: function (a) { a.current = this._items && this._items[this.relative(this._current)] } }, { filter: ["items", "settings"], run: function () { this.$stage.children(".cloned").remove() } }, { filter: ["width", "items", "settings"], run: function (a) { var b = this.settings.margin || "", c = !this.settings.autoWidth, d = this.settings.rtl, e = { width: "auto", "margin-left": d ? b : "", "margin-right": d ? "" : b }; !c && this.$stage.children().css(e), a.css = e } }, { filter: ["width", "items", "settings"], run: function (a) { var b = (this.width() / this.settings.items).toFixed(3) - this.settings.margin, c = null, d = this._items.length, e = !this.settings.autoWidth, f = []; for (a.items = { merge: !1, width: b }; d--;)c = this._mergers[d], c = this.settings.mergeFit && Math.min(c, this.settings.items) || c, a.items.merge = c > 1 || a.items.merge, f[d] = e ? b * c : this._items[d].width(); this._widths = f } }, { filter: ["items", "settings"], run: function () { var b = [], c = this._items, d = this.settings, e = Math.max(2 * d.items, 4), f = 2 * Math.ceil(c.length / 2), g = d.loop && c.length ? d.rewind ? e : Math.max(e, f) : 0, h = "", i = ""; for (g /= 2; g--;)b.push(this.normalize(b.length / 2, !0)), h += c[b[b.length - 1]][0].outerHTML, b.push(this.normalize(c.length - 1 - (b.length - 1) / 2, !0)), i = c[b[b.length - 1]][0].outerHTML + i; this._clones = b, a(h).addClass("cloned").appendTo(this.$stage), a(i).addClass("cloned").prependTo(this.$stage) } }, { filter: ["width", "items", "settings"], run: function () { for (var a = this.settings.rtl ? 1 : -1, b = this._clones.length + this._items.length, c = -1, d = 0, e = 0, f = []; ++c < b;)d = f[c - 1] || 0, e = this._widths[this.relative(c)] + this.settings.margin, f.push(d + e * a); this._coordinates = f } }, { filter: ["width", "items", "settings"], run: function () { var a = this.settings.stagePadding, b = this._coordinates, c = { width: Math.ceil(Math.abs(b[b.length - 1])) + 2 * a, "padding-left": a || "", "padding-right": a || "" }; this.$stage.css(c) } }, { filter: ["width", "items", "settings"], run: function (a) { var b = this._coordinates.length, c = !this.settings.autoWidth, d = this.$stage.children(); if (c && a.items.merge) for (; b--;)a.css.width = this._widths[this.relative(b)], d.eq(b).css(a.css); else c && (a.css.width = a.items.width, d.css(a.css)) } }, { filter: ["items"], run: function () { this._coordinates.length < 1 && this.$stage.removeAttr("style") } }, { filter: ["width", "items", "settings"], run: function (a) { a.current = a.current ? this.$stage.children().index(a.current) : 0, a.current = Math.max(this.minimum(), Math.min(this.maximum(), a.current)), this.reset(a.current) } }, { filter: ["position"], run: function () { this.animate(this.coordinates(this._current)) } }, { filter: ["width", "position", "items", "settings"], run: function () { var a, b, c, d, e = this.settings.rtl ? 1 : -1, f = 2 * this.settings.stagePadding, g = this.coordinates(this.current()) + f, h = g + this.width() * e, i = []; for (c = 0, d = this._coordinates.length; c < d; c++)a = this._coordinates[c - 1] || 0, b = Math.abs(this._coordinates[c]) + f * e, (this.op(a, "<=", g) && this.op(a, ">", h) || this.op(b, "<", g) && this.op(b, ">", h)) && i.push(c); this.$stage.children(".active").removeClass("active"), this.$stage.children(":eq(" + i.join("), :eq(") + ")").addClass("active"), this.settings.center && (this.$stage.children(".center").removeClass("center"), this.$stage.children().eq(this.current()).addClass("center")) } }], e.prototype.initialize = function () { if (this.enter("initializing"), this.trigger("initialize"), this.$element.toggleClass(this.settings.rtlClass, this.settings.rtl), this.settings.autoWidth && !this.is("pre-loading")) { var b, c, e; b = this.$element.find("img"), c = this.settings.nestedItemSelector ? "." + this.settings.nestedItemSelector : d, e = this.$element.children(c).width(), b.length && e <= 0 && this.preloadAutoWidthImages(b) } this.$element.addClass(this.options.loadingClass), this.$stage = a("<" + this.settings.stageElement + ' class="' + this.settings.stageClass + '"/>').wrap('<div class="' + this.settings.stageOuterClass + '"/>'), this.$element.append(this.$stage.parent()), this.replace(this.$element.children().not(this.$stage.parent())), this.$element.is(":visible") ? this.refresh() : this.invalidate("width"), this.$element.removeClass(this.options.loadingClass).addClass(this.options.loadedClass), this.registerEventHandlers(), this.leave("initializing"), this.trigger("initialized") }, e.prototype.setup = function () { var b = this.viewport(), c = this.options.responsive, d = -1, e = null; c ? (a.each(c, function (a) { a <= b && a > d && (d = Number(a)) }), e = a.extend({}, this.options, c[d]), "function" == typeof e.stagePadding && (e.stagePadding = e.stagePadding()), delete e.responsive, e.responsiveClass && this.$element.attr("class", this.$element.attr("class").replace(new RegExp("(" + this.options.responsiveClass + "-)\\S+\\s", "g"), "$1" + d))) : e = a.extend({}, this.options), this.trigger("change", { property: { name: "settings", value: e } }), this._breakpoint = d, this.settings = e, this.invalidate("settings"), this.trigger("changed", { property: { name: "settings", value: this.settings } }) }, e.prototype.optionsLogic = function () { this.settings.autoWidth && (this.settings.stagePadding = !1, this.settings.merge = !1) }, e.prototype.prepare = function (b) { var c = this.trigger("prepare", { content: b }); return c.data || (c.data = a("<" + this.settings.itemElement + "/>").addClass(this.options.itemClass).append(b)), this.trigger("prepared", { content: c.data }), c.data }, e.prototype.update = function () { for (var b = 0, c = this._pipe.length, d = a.proxy(function (a) { return this[a] }, this._invalidated), e = {}; b < c;)(this._invalidated.all || a.grep(this._pipe[b].filter, d).length > 0) && this._pipe[b].run(e), b++; this._invalidated = {}, !this.is("valid") && this.enter("valid") }, e.prototype.width = function (a) { switch (a = a || e.Width.Default) { case e.Width.Inner: case e.Width.Outer: return this._width; default: return this._width - 2 * this.settings.stagePadding + this.settings.margin } }, e.prototype.refresh = function () { this.enter("refreshing"), this.trigger("refresh"), this.setup(), this.optionsLogic(), this.$element.addClass(this.options.refreshClass), this.update(), this.$element.removeClass(this.options.refreshClass), this.leave("refreshing"), this.trigger("refreshed") }, e.prototype.onThrottledResize = function () { b.clearTimeout(this.resizeTimer), this.resizeTimer = b.setTimeout(this._handlers.onResize, this.settings.responsiveRefreshRate) }, e.prototype.onResize = function () { return !!this._items.length && (this._width !== this.$element.width() && (!!this.$element.is(":visible") && (this.enter("resizing"), this.trigger("resize").isDefaultPrevented() ? (this.leave("resizing"), !1) : (this.invalidate("width"), this.refresh(), this.leave("resizing"), void this.trigger("resized"))))) }, e.prototype.registerEventHandlers = function () { a.support.transition && this.$stage.on(a.support.transition.end + ".owl.core", a.proxy(this.onTransitionEnd, this)), this.settings.responsive !== !1 && this.on(b, "resize", this._handlers.onThrottledResize), this.settings.mouseDrag && (this.$element.addClass(this.options.dragClass), this.$stage.on("mousedown.owl.core", a.proxy(this.onDragStart, this)), this.$stage.on("dragstart.owl.core selectstart.owl.core", function () { return !1 })), this.settings.touchDrag && (this.$stage.on("touchstart.owl.core", a.proxy(this.onDragStart, this)), this.$stage.on("touchcancel.owl.core", a.proxy(this.onDragEnd, this))) }, e.prototype.onDragStart = function (b) { var d = null; 3 !== b.which && (a.support.transform ? (d = this.$stage.css("transform").replace(/.*\(|\)| /g, "").split(","), d = { x: d[16 === d.length ? 12 : 4], y: d[16 === d.length ? 13 : 5] }) : (d = this.$stage.position(), d = { x: this.settings.rtl ? d.left + this.$stage.width() - this.width() + this.settings.margin : d.left, y: d.top }), this.is("animating") && (a.support.transform ? this.animate(d.x) : this.$stage.stop(), this.invalidate("position")), this.$element.toggleClass(this.options.grabClass, "mousedown" === b.type), this.speed(0), this._drag.time = (new Date).getTime(), this._drag.target = a(b.target), this._drag.stage.start = d, this._drag.stage.current = d, this._drag.pointer = this.pointer(b), a(c).on("mouseup.owl.core touchend.owl.core", a.proxy(this.onDragEnd, this)), a(c).one("mousemove.owl.core touchmove.owl.core", a.proxy(function (b) { var d = this.difference(this._drag.pointer, this.pointer(b)); a(c).on("mousemove.owl.core touchmove.owl.core", a.proxy(this.onDragMove, this)), Math.abs(d.x) < Math.abs(d.y) && this.is("valid") || (b.preventDefault(), this.enter("dragging"), this.trigger("drag")) }, this))) }, e.prototype.onDragMove = function (a) { var b = null, c = null, d = null, e = this.difference(this._drag.pointer, this.pointer(a)), f = this.difference(this._drag.stage.start, e); this.is("dragging") && (a.preventDefault(), this.settings.loop ? (b = this.coordinates(this.minimum()), c = this.coordinates(this.maximum() + 1) - b, f.x = ((f.x - b) % c + c) % c + b) : (b = this.settings.rtl ? this.coordinates(this.maximum()) : this.coordinates(this.minimum()), c = this.settings.rtl ? this.coordinates(this.minimum()) : this.coordinates(this.maximum()), d = this.settings.pullDrag ? -1 * e.x / 5 : 0, f.x = Math.max(Math.min(f.x, b + d), c + d)), this._drag.stage.current = f, this.animate(f.x)) }, e.prototype.onDragEnd = function (b) { var d = this.difference(this._drag.pointer, this.pointer(b)), e = this._drag.stage.current, f = d.x > 0 ^ this.settings.rtl ? "left" : "right"; a(c).off(".owl.core"), this.$element.removeClass(this.options.grabClass), (0 !== d.x && this.is("dragging") || !this.is("valid")) && (this.speed(this.settings.dragEndSpeed || this.settings.smartSpeed), this.current(this.closest(e.x, 0 !== d.x ? f : this._drag.direction)), this.invalidate("position"), this.update(), this._drag.direction = f, (Math.abs(d.x) > 3 || (new Date).getTime() - this._drag.time > 300) && this._drag.target.one("click.owl.core", function () { return !1 })), this.is("dragging") && (this.leave("dragging"), this.trigger("dragged")) }, e.prototype.closest = function (b, c) { var d = -1, e = 30, f = this.width(), g = this.coordinates(); return this.settings.freeDrag || a.each(g, a.proxy(function (a, h) { return "left" === c && b > h - e && b < h + e ? d = a : "right" === c && b > h - f - e && b < h - f + e ? d = a + 1 : this.op(b, "<", h) && this.op(b, ">", g[a + 1] || h - f) && (d = "left" === c ? a + 1 : a), d === -1 }, this)), this.settings.loop || (this.op(b, ">", g[this.minimum()]) ? d = b = this.minimum() : this.op(b, "<", g[this.maximum()]) && (d = b = this.maximum())), d }, e.prototype.animate = function (b) { var c = this.speed() > 0; this.is("animating") && this.onTransitionEnd(), c && (this.enter("animating"), this.trigger("translate")), a.support.transform3d && a.support.transition ? this.$stage.css({ transform: "translate3d(" + b + "px,0px,0px)", transition: this.speed() / 1e3 + "s" }) : c ? this.$stage.animate({ left: b + "px" }, this.speed(), this.settings.fallbackEasing, a.proxy(this.onTransitionEnd, this)) : this.$stage.css({ left: b + "px" }) }, e.prototype.is = function (a) { return this._states.current[a] && this._states.current[a] > 0 }, e.prototype.current = function (a) { if (a === d) return this._current; if (0 === this._items.length) return d; if (a = this.normalize(a), this._current !== a) { var b = this.trigger("change", { property: { name: "position", value: a } }); b.data !== d && (a = this.normalize(b.data)), this._current = a, this.invalidate("position"), this.trigger("changed", { property: { name: "position", value: this._current } }) } return this._current }, e.prototype.invalidate = function (b) { return "string" === a.type(b) && (this._invalidated[b] = !0, this.is("valid") && this.leave("valid")), a.map(this._invalidated, function (a, b) { return b }) }, e.prototype.reset = function (a) { a = this.normalize(a), a !== d && (this._speed = 0, this._current = a, this.suppress(["translate", "translated"]), this.animate(this.coordinates(a)), this.release(["translate", "translated"])) }, e.prototype.normalize = function (a, b) { var c = this._items.length, e = b ? 0 : this._clones.length; return !this.isNumeric(a) || c < 1 ? a = d : (a < 0 || a >= c + e) && (a = ((a - e / 2) % c + c) % c + e / 2), a }, e.prototype.relative = function (a) { return a -= this._clones.length / 2, this.normalize(a, !0) }, e.prototype.maximum = function (a) { var b, c, d, e = this.settings, f = this._coordinates.length; if (e.loop) f = this._clones.length / 2 + this._items.length - 1; else if (e.autoWidth || e.merge) { for (b = this._items.length, c = this._items[--b].width(), d = this.$element.width(); b-- && (c += this._items[b].width() + this.settings.margin, !(c > d));); f = b + 1 } else f = e.center ? this._items.length - 1 : this._items.length - e.items; return a && (f -= this._clones.length / 2), Math.max(f, 0) }, e.prototype.minimum = function (a) { return a ? 0 : this._clones.length / 2 }, e.prototype.items = function (a) { return a === d ? this._items.slice() : (a = this.normalize(a, !0), this._items[a]) }, e.prototype.mergers = function (a) { return a === d ? this._mergers.slice() : (a = this.normalize(a, !0), this._mergers[a]) }, e.prototype.clones = function (b) { var c = this._clones.length / 2, e = c + this._items.length, f = function (a) { return a % 2 === 0 ? e + a / 2 : c - (a + 1) / 2 }; return b === d ? a.map(this._clones, function (a, b) { return f(b) }) : a.map(this._clones, function (a, c) { return a === b ? f(c) : null }) }, e.prototype.speed = function (a) { return a !== d && (this._speed = a), this._speed }, e.prototype.coordinates = function (b) { var c, e = 1, f = b - 1; return b === d ? a.map(this._coordinates, a.proxy(function (a, b) { return this.coordinates(b) }, this)) : (this.settings.center ? (this.settings.rtl && (e = -1, f = b + 1), c = this._coordinates[b], c += (this.width() - c + (this._coordinates[f] || 0)) / 2 * e) : c = this._coordinates[f] || 0, c = Math.ceil(c)) }, e.prototype.duration = function (a, b, c) { return 0 === c ? 0 : Math.min(Math.max(Math.abs(b - a), 1), 6) * Math.abs(c || this.settings.smartSpeed) }, e.prototype.to = function (a, b) { var c = this.current(), d = null, e = a - this.relative(c), f = (e > 0) - (e < 0), g = this._items.length, h = this.minimum(), i = this.maximum(); this.settings.loop ? (!this.settings.rewind && Math.abs(e) > g / 2 && (e += f * -1 * g), a = c + e, d = ((a - h) % g + g) % g + h, d !== a && d - e <= i && d - e > 0 && (c = d - e, a = d, this.reset(c))) : this.settings.rewind ? (i += 1, a = (a % i + i) % i) : a = Math.max(h, Math.min(i, a)), this.speed(this.duration(c, a, b)), this.current(a), this.$element.is(":visible") && this.update() }, e.prototype.next = function (a) { a = a || !1, this.to(this.relative(this.current()) + 1, a) }, e.prototype.prev = function (a) { a = a || !1, this.to(this.relative(this.current()) - 1, a) }, e.prototype.onTransitionEnd = function (a) { if (a !== d && (a.stopPropagation(), (a.target || a.srcElement || a.originalTarget) !== this.$stage.get(0))) return !1; this.leave("animating"), this.trigger("translated") }, e.prototype.viewport = function () { var d; return this.options.responsiveBaseElement !== b ? d = a(this.options.responsiveBaseElement).width() : b.innerWidth ? d = b.innerWidth : c.documentElement && c.documentElement.clientWidth ? d = c.documentElement.clientWidth : console.warn("Can not detect viewport width."), d }, e.prototype.replace = function (b) { this.$stage.empty(), this._items = [], b && (b = b instanceof jQuery ? b : a(b)), this.settings.nestedItemSelector && (b = b.find("." + this.settings.nestedItemSelector)), b.filter(function () { return 1 === this.nodeType }).each(a.proxy(function (a, b) { b = this.prepare(b), this.$stage.append(b), this._items.push(b), this._mergers.push(1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1) }, this)), this.reset(this.isNumeric(this.settings.startPosition) ? this.settings.startPosition : 0), this.invalidate("items") }, e.prototype.add = function (b, c) { var e = this.relative(this._current); c = c === d ? this._items.length : this.normalize(c, !0), b = b instanceof jQuery ? b : a(b), this.trigger("add", { content: b, position: c }), b = this.prepare(b), 0 === this._items.length || c === this._items.length ? (0 === this._items.length && this.$stage.append(b), 0 !== this._items.length && this._items[c - 1].after(b), this._items.push(b), this._mergers.push(1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)) : (this._items[c].before(b), this._items.splice(c, 0, b), this._mergers.splice(c, 0, 1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)), this._items[e] && this.reset(this._items[e].index()), this.invalidate("items"), this.trigger("added", { content: b, position: c }) }, e.prototype.remove = function (a) { a = this.normalize(a, !0), a !== d && (this.trigger("remove", { content: this._items[a], position: a }), this._items[a].remove(), this._items.splice(a, 1), this._mergers.splice(a, 1), this.invalidate("items"), this.trigger("removed", { content: null, position: a })) }, e.prototype.preloadAutoWidthImages = function (b) { b.each(a.proxy(function (b, c) { this.enter("pre-loading"), c = a(c), a(new Image).one("load", a.proxy(function (a) { c.attr("src", a.target.src), c.css("opacity", 1), this.leave("pre-loading"), !this.is("pre-loading") && !this.is("initializing") && this.refresh() }, this)).attr("src", c.attr("src") || c.attr("data-src") || c.attr("data-src-retina")) }, this)) }, e.prototype.destroy = function () { this.$element.off(".owl.core"), this.$stage.off(".owl.core"), a(c).off(".owl.core"), this.settings.responsive !== !1 && (b.clearTimeout(this.resizeTimer), this.off(b, "resize", this._handlers.onThrottledResize)); for (var d in this._plugins) this._plugins[d].destroy(); this.$stage.children(".cloned").remove(), this.$stage.unwrap(), this.$stage.children().contents().unwrap(), this.$stage.children().unwrap(), this.$element.removeClass(this.options.refreshClass).removeClass(this.options.loadingClass).removeClass(this.options.loadedClass).removeClass(this.options.rtlClass).removeClass(this.options.dragClass).removeClass(this.options.grabClass).attr("class", this.$element.attr("class").replace(new RegExp(this.options.responsiveClass + "-\\S+\\s", "g"), "")).removeData("owl.carousel") }, e.prototype.op = function (a, b, c) { var d = this.settings.rtl; switch (b) { case "<": return d ? a > c : a < c; case ">": return d ? a < c : a > c; case ">=": return d ? a <= c : a >= c; case "<=": return d ? a >= c : a <= c } }, e.prototype.on = function (a, b, c, d) { a.addEventListener ? a.addEventListener(b, c, d) : a.attachEvent && a.attachEvent("on" + b, c) }, e.prototype.off = function (a, b, c, d) { a.removeEventListener ? a.removeEventListener(b, c, d) : a.detachEvent && a.detachEvent("on" + b, c) }, e.prototype.trigger = function (b, c, d, f, g) { var h = { item: { count: this._items.length, index: this.current() } }, i = a.camelCase(a.grep(["on", b, d], function (a) { return a }).join("-").toLowerCase()), j = a.Event([b, "owl", d || "carousel"].join(".").toLowerCase(), a.extend({ relatedTarget: this }, h, c)); return this._supress[b] || (a.each(this._plugins, function (a, b) { b.onTrigger && b.onTrigger(j) }), this.register({ type: e.Type.Event, name: b }), this.$element.trigger(j), this.settings && "function" == typeof this.settings[i] && this.settings[i].call(this, j)), j }, e.prototype.enter = function (b) { a.each([b].concat(this._states.tags[b] || []), a.proxy(function (a, b) { this._states.current[b] === d && (this._states.current[b] = 0), this._states.current[b]++ }, this)) }, e.prototype.leave = function (b) { a.each([b].concat(this._states.tags[b] || []), a.proxy(function (a, b) { this._states.current[b]-- }, this)) }, e.prototype.register = function (b) { if (b.type === e.Type.Event) { if (a.event.special[b.name] || (a.event.special[b.name] = {}), !a.event.special[b.name].owl) { var c = a.event.special[b.name]._default; a.event.special[b.name]._default = function (a) { return !c || !c.apply || a.namespace && a.namespace.indexOf("owl") !== -1 ? a.namespace && a.namespace.indexOf("owl") > -1 : c.apply(this, arguments) }, a.event.special[b.name].owl = !0 } } else b.type === e.Type.State && (this._states.tags[b.name] ? this._states.tags[b.name] = this._states.tags[b.name].concat(b.tags) : this._states.tags[b.name] = b.tags, this._states.tags[b.name] = a.grep(this._states.tags[b.name], a.proxy(function (c, d) { return a.inArray(c, this._states.tags[b.name]) === d }, this))) }, e.prototype.suppress = function (b) { a.each(b, a.proxy(function (a, b) { this._supress[b] = !0 }, this)) }, e.prototype.release = function (b) { a.each(b, a.proxy(function (a, b) { delete this._supress[b] }, this)) }, e.prototype.pointer = function (a) { var c = { x: null, y: null }; return a = a.originalEvent || a || b.event, a = a.touches && a.touches.length ? a.touches[0] : a.changedTouches && a.changedTouches.length ? a.changedTouches[0] : a, a.pageX ? (c.x = a.pageX, c.y = a.pageY) : (c.x = a.clientX, c.y = a.clientY), c }, e.prototype.isNumeric = function (a) { return !isNaN(parseFloat(a)) }, e.prototype.difference = function (a, b) { return { x: a.x - b.x, y: a.y - b.y } }, a.fn.owlCarousel = function (b) { var c = Array.prototype.slice.call(arguments, 1); return this.each(function () { var d = a(this), f = d.data("owl.carousel"); f || (f = new e(this, "object" == typeof b && b), d.data("owl.carousel", f), a.each(["next", "prev", "to", "destroy", "refresh", "replace", "add", "remove"], function (b, c) { f.register({ type: e.Type.Event, name: c }), f.$element.on(c + ".owl.carousel.core", a.proxy(function (a) { a.namespace && a.relatedTarget !== this && (this.suppress([c]), f[c].apply(this, [].slice.call(arguments, 1)), this.release([c])) }, f)) })), "string" == typeof b && "_" !== b.charAt(0) && f[b].apply(f, c) }) }, a.fn.owlCarousel.Constructor = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { var e = function (b) { this._core = b, this._interval = null, this._visible = null, this._handlers = { "initialized.owl.carousel": a.proxy(function (a) { a.namespace && this._core.settings.autoRefresh && this.watch() }, this) }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers) }; e.Defaults = { autoRefresh: !0, autoRefreshInterval: 500 }, e.prototype.watch = function () { this._interval || (this._visible = this._core.$element.is(":visible"), this._interval = b.setInterval(a.proxy(this.refresh, this), this._core.settings.autoRefreshInterval)) }, e.prototype.refresh = function () { this._core.$element.is(":visible") !== this._visible && (this._visible = !this._visible, this._core.$element.toggleClass("owl-hidden", !this._visible), this._visible && this._core.invalidate("width") && this._core.refresh()) }, e.prototype.destroy = function () { var a, c; b.clearInterval(this._interval); for (a in this._handlers) this._core.$element.off(a, this._handlers[a]); for (c in Object.getOwnPropertyNames(this)) "function" != typeof this[c] && (this[c] = null) }, a.fn.owlCarousel.Constructor.Plugins.AutoRefresh = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { var e = function (b) { this._core = b, this._loaded = [], this._handlers = { "initialized.owl.carousel change.owl.carousel resized.owl.carousel": a.proxy(function (b) { if (b.namespace && this._core.settings && this._core.settings.lazyLoad && (b.property && "position" == b.property.name || "initialized" == b.type)) for (var c = this._core.settings, e = c.center && Math.ceil(c.items / 2) || c.items, f = c.center && e * -1 || 0, g = (b.property && b.property.value !== d ? b.property.value : this._core.current()) + f, h = this._core.clones().length, i = a.proxy(function (a, b) { this.load(b) }, this); f++ < e;)this.load(h / 2 + this._core.relative(g)), h && a.each(this._core.clones(this._core.relative(g)), i), g++ }, this) }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers) }; e.Defaults = { lazyLoad: !1 }, e.prototype.load = function (c) { var d = this._core.$stage.children().eq(c), e = d && d.find(".owl-lazy"); !e || a.inArray(d.get(0), this._loaded) > -1 || (e.each(a.proxy(function (c, d) { var e, f = a(d), g = b.devicePixelRatio > 1 && f.attr("data-src-retina") || f.attr("data-src"); this._core.trigger("load", { element: f, url: g }, "lazy"), f.is("img") ? f.one("load.owl.lazy", a.proxy(function () { f.css("opacity", 1), this._core.trigger("loaded", { element: f, url: g }, "lazy") }, this)).attr("src", g) : (e = new Image, e.onload = a.proxy(function () { f.css({ "background-image": 'url("' + g + '")', opacity: "1" }), this._core.trigger("loaded", { element: f, url: g }, "lazy") }, this), e.src = g) }, this)), this._loaded.push(d.get(0))) }, e.prototype.destroy = function () { var a, b; for (a in this.handlers) this._core.$element.off(a, this.handlers[a]); for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null) }, a.fn.owlCarousel.Constructor.Plugins.Lazy = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { var e = function (b) { this._core = b, this._handlers = { "initialized.owl.carousel refreshed.owl.carousel": a.proxy(function (a) { a.namespace && this._core.settings.autoHeight && this.update() }, this), "changed.owl.carousel": a.proxy(function (a) { a.namespace && this._core.settings.autoHeight && "position" == a.property.name && this.update() }, this), "loaded.owl.lazy": a.proxy(function (a) { a.namespace && this._core.settings.autoHeight && a.element.closest("." + this._core.settings.itemClass).index() === this._core.current() && this.update() }, this) }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers) }; e.Defaults = { autoHeight: !1, autoHeightClass: "owl-height" }, e.prototype.update = function () { var b = this._core._current, c = b + this._core.settings.items, d = this._core.$stage.children().toArray().slice(b, c), e = [], f = 0; a.each(d, function (b, c) { e.push(a(c).height()) }), f = Math.max.apply(null, e), this._core.$stage.parent().height(f).addClass(this._core.settings.autoHeightClass) }, e.prototype.destroy = function () { var a, b; for (a in this._handlers) this._core.$element.off(a, this._handlers[a]); for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null) }, a.fn.owlCarousel.Constructor.Plugins.AutoHeight = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { var e = function (b) { this._core = b, this._videos = {}, this._playing = null, this._handlers = { "initialized.owl.carousel": a.proxy(function (a) { a.namespace && this._core.register({ type: "state", name: "playing", tags: ["interacting"] }) }, this), "resize.owl.carousel": a.proxy(function (a) { a.namespace && this._core.settings.video && this.isInFullScreen() && a.preventDefault() }, this), "refreshed.owl.carousel": a.proxy(function (a) { a.namespace && this._core.is("resizing") && this._core.$stage.find(".cloned .owl-video-frame").remove() }, this), "changed.owl.carousel": a.proxy(function (a) { a.namespace && "position" === a.property.name && this._playing && this.stop() }, this), "prepared.owl.carousel": a.proxy(function (b) { if (b.namespace) { var c = a(b.content).find(".owl-video"); c.length && (c.css("display", "none"), this.fetch(c, a(b.content))) } }, this) }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers), this._core.$element.on("click.owl.video", ".owl-video-play-icon", a.proxy(function (a) { this.play(a) }, this)) }; e.Defaults = { video: !1, videoHeight: !1, videoWidth: !1 }, e.prototype.fetch = function (a, b) { var c = function () { return a.attr("data-vimeo-id") ? "vimeo" : a.attr("data-vzaar-id") ? "vzaar" : "youtube" }(), d = a.attr("data-vimeo-id") || a.attr("data-youtube-id") || a.attr("data-vzaar-id"), e = a.attr("data-width") || this._core.settings.videoWidth, f = a.attr("data-height") || this._core.settings.videoHeight, g = a.attr("href"); if (!g) throw new Error("Missing video URL."); if (d = g.match(/(http:|https:|)\/\/(player.|www.|app.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com)|vzaar\.com)\/(video\/|videos\/|embed\/|channels\/.+\/|groups\/.+\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/), d[3].indexOf("youtu") > -1) c = "youtube"; else if (d[3].indexOf("vimeo") > -1) c = "vimeo"; else { if (!(d[3].indexOf("vzaar") > -1)) throw new Error("Video URL not supported."); c = "vzaar" } d = d[6], this._videos[g] = { type: c, id: d, width: e, height: f }, b.attr("data-video", g), this.thumbnail(a, this._videos[g]) }, e.prototype.thumbnail = function (b, c) { var d, e, f, g = c.width && c.height ? 'style="width:' + c.width + "px;height:" + c.height + 'px;"' : "", h = b.find("img"), i = "src", j = "", k = this._core.settings, l = function (a) { e = '<div class="owl-video-play-icon"></div>', d = k.lazyLoad ? '<div class="owl-video-tn ' + j + '" ' + i + '="' + a + '"></div>' : '<div class="owl-video-tn" style="opacity:1;background-image:url(' + a + ')"></div>', b.after(d), b.after(e) }; if (b.wrap('<div class="owl-video-wrapper"' + g + "></div>"), this._core.settings.lazyLoad && (i = "data-src", j = "owl-lazy"), h.length) return l(h.attr(i)), h.remove(), !1; "youtube" === c.type ? (f = "//img.youtube.com/vi/" + c.id + "/hqdefault.jpg", l(f)) : "vimeo" === c.type ? a.ajax({ type: "GET", url: "//vimeo.com/api/v2/video/" + c.id + ".json", jsonp: "callback", dataType: "jsonp", success: function (a) { f = a[0].thumbnail_large, l(f) } }) : "vzaar" === c.type && a.ajax({ type: "GET", url: "//vzaar.com/api/videos/" + c.id + ".json", jsonp: "callback", dataType: "jsonp", success: function (a) { f = a.framegrab_url, l(f) } }) }, e.prototype.stop = function () { this._core.trigger("stop", null, "video"), this._playing.find(".owl-video-frame").remove(), this._playing.removeClass("owl-video-playing"), this._playing = null, this._core.leave("playing"), this._core.trigger("stopped", null, "video") }, e.prototype.play = function (b) { var c, d = a(b.target), e = d.closest("." + this._core.settings.itemClass), f = this._videos[e.attr("data-video")], g = f.width || "100%", h = f.height || this._core.$stage.height(); this._playing || (this._core.enter("playing"), this._core.trigger("play", null, "video"), e = this._core.items(this._core.relative(e.index())), this._core.reset(e.index()), "youtube" === f.type ? c = '<iframe width="' + g + '" height="' + h + '" src="//www.youtube.com/embed/' + f.id + "?autoplay=1&rel=0&v=" + f.id + '" frameborder="0" allowfullscreen></iframe>' : "vimeo" === f.type ? c = '<iframe src="//player.vimeo.com/video/' + f.id + '?autoplay=1" width="' + g + '" height="' + h + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>' : "vzaar" === f.type && (c = '<iframe frameborder="0"height="' + h + '"width="' + g + '" allowfullscreen mozallowfullscreen webkitAllowFullScreen src="//view.vzaar.com/' + f.id + '/player?autoplay=true"></iframe>'), a('<div class="owl-video-frame">' + c + "</div>").insertAfter(e.find(".owl-video")), this._playing = e.addClass("owl-video-playing")) }, e.prototype.isInFullScreen = function () { var b = c.fullscreenElement || c.mozFullScreenElement || c.webkitFullscreenElement; return b && a(b).parent().hasClass("owl-video-frame") }, e.prototype.destroy = function () { var a, b; this._core.$element.off("click.owl.video"); for (a in this._handlers) this._core.$element.off(a, this._handlers[a]); for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null) }, a.fn.owlCarousel.Constructor.Plugins.Video = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
	var e = function (b) { this.core = b, this.core.options = a.extend({}, e.Defaults, this.core.options), this.swapping = !0, this.previous = d, this.next = d, this.handlers = { "change.owl.carousel": a.proxy(function (a) { a.namespace && "position" == a.property.name && (this.previous = this.core.current(), this.next = a.property.value) }, this), "drag.owl.carousel dragged.owl.carousel translated.owl.carousel": a.proxy(function (a) { a.namespace && (this.swapping = "translated" == a.type) }, this), "translate.owl.carousel": a.proxy(function (a) { a.namespace && this.swapping && (this.core.options.animateOut || this.core.options.animateIn) && this.swap() }, this) }, this.core.$element.on(this.handlers) }; e.Defaults = { animateOut: !1, animateIn: !1 }, e.prototype.swap = function () { if (1 === this.core.settings.items && a.support.animation && a.support.transition) { this.core.speed(0); var b, c = a.proxy(this.clear, this), d = this.core.$stage.children().eq(this.previous), e = this.core.$stage.children().eq(this.next), f = this.core.settings.animateIn, g = this.core.settings.animateOut; this.core.current() !== this.previous && (g && (b = this.core.coordinates(this.previous) - this.core.coordinates(this.next), d.one(a.support.animation.end, c).css({ left: b + "px" }).addClass("animated owl-animated-out").addClass(g)), f && e.one(a.support.animation.end, c).addClass("animated owl-animated-in").addClass(f)) } }, e.prototype.clear = function (b) { a(b.target).css({ left: "" }).removeClass("animated owl-animated-out owl-animated-in").removeClass(this.core.settings.animateIn).removeClass(this.core.settings.animateOut), this.core.onTransitionEnd() }, e.prototype.destroy = function () { var a, b; for (a in this.handlers) this.core.$element.off(a, this.handlers[a]); for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null) },
		a.fn.owlCarousel.Constructor.Plugins.Animate = e
}(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { var e = function (b) { this._core = b, this._timeout = null, this._paused = !1, this._handlers = { "changed.owl.carousel": a.proxy(function (a) { a.namespace && "settings" === a.property.name ? this._core.settings.autoplay ? this.play() : this.stop() : a.namespace && "position" === a.property.name && this._core.settings.autoplay && this._setAutoPlayInterval() }, this), "initialized.owl.carousel": a.proxy(function (a) { a.namespace && this._core.settings.autoplay && this.play() }, this), "play.owl.autoplay": a.proxy(function (a, b, c) { a.namespace && this.play(b, c) }, this), "stop.owl.autoplay": a.proxy(function (a) { a.namespace && this.stop() }, this), "mouseover.owl.autoplay": a.proxy(function () { this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.pause() }, this), "mouseleave.owl.autoplay": a.proxy(function () { this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.play() }, this), "touchstart.owl.core": a.proxy(function () { this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.pause() }, this), "touchend.owl.core": a.proxy(function () { this._core.settings.autoplayHoverPause && this.play() }, this) }, this._core.$element.on(this._handlers), this._core.options = a.extend({}, e.Defaults, this._core.options) }; e.Defaults = { autoplay: !1, autoplayTimeout: 5e3, autoplayHoverPause: !1, autoplaySpeed: !1 }, e.prototype.play = function (a, b) { this._paused = !1, this._core.is("rotating") || (this._core.enter("rotating"), this._setAutoPlayInterval()) }, e.prototype._getNextTimeout = function (d, e) { return this._timeout && b.clearTimeout(this._timeout), b.setTimeout(a.proxy(function () { this._paused/*||this._core.is("busy")*/ || this._core.is("interacting")/*||c.hidden*/ || this._core.next(e || this._core.settings.autoplaySpeed) }, this), d || this._core.settings.autoplayTimeout) }, e.prototype._setAutoPlayInterval = function () { this._timeout = this._getNextTimeout() }, e.prototype.stop = function () { this._core.is("rotating") && (b.clearTimeout(this._timeout), this._core.leave("rotating")) }, e.prototype.pause = function () { this._core.is("rotating") && (this._paused = !0) }, e.prototype.destroy = function () { var a, b; this.stop(); for (a in this._handlers) this._core.$element.off(a, this._handlers[a]); for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null) }, a.fn.owlCarousel.Constructor.Plugins.autoplay = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { "use strict"; var e = function (b) { this._core = b, this._initialized = !1, this._pages = [], this._controls = {}, this._templates = [], this.$element = this._core.$element, this._overrides = { next: this._core.next, prev: this._core.prev, to: this._core.to }, this._handlers = { "prepared.owl.carousel": a.proxy(function (b) { b.namespace && this._core.settings.dotsData && this._templates.push('<div class="' + this._core.settings.dotClass + '">' + a(b.content).find("[data-dot]").addBack("[data-dot]").attr("data-dot") + "</div>") }, this), "added.owl.carousel": a.proxy(function (a) { a.namespace && this._core.settings.dotsData && this._templates.splice(a.position, 0, this._templates.pop()) }, this), "remove.owl.carousel": a.proxy(function (a) { a.namespace && this._core.settings.dotsData && this._templates.splice(a.position, 1) }, this), "changed.owl.carousel": a.proxy(function (a) { a.namespace && "position" == a.property.name && this.draw() }, this), "initialized.owl.carousel": a.proxy(function (a) { a.namespace && !this._initialized && (this._core.trigger("initialize", null, "navigation"), this.initialize(), this.update(), this.draw(), this._initialized = !0, this._core.trigger("initialized", null, "navigation")) }, this), "refreshed.owl.carousel": a.proxy(function (a) { a.namespace && this._initialized && (this._core.trigger("refresh", null, "navigation"), this.update(), this.draw(), this._core.trigger("refreshed", null, "navigation")) }, this) }, this._core.options = a.extend({}, e.Defaults, this._core.options), this.$element.on(this._handlers) }; e.Defaults = { nav: !1, navText: ["prev", "next"], navSpeed: !1, navElement: "div", navContainer: !1, navContainerClass: "owl-nav", navClass: ["owl-prev", "owl-next"], slideBy: 1, dotClass: "owl-dot", dotsClass: "owl-dots", dots: !0, dotsEach: !1, dotsData: !1, dotsSpeed: !1, dotsContainer: !1 }, e.prototype.initialize = function () { var b, c = this._core.settings; this._controls.$relative = (c.navContainer ? a(c.navContainer) : a("<div>").addClass(c.navContainerClass).appendTo(this.$element)).addClass("disabled"), this._controls.$previous = a("<" + c.navElement + ">").addClass(c.navClass[0]).html(c.navText[0]).prependTo(this._controls.$relative).on("click", a.proxy(function (a) { this.prev(c.navSpeed) }, this)), this._controls.$next = a("<" + c.navElement + ">").addClass(c.navClass[1]).html(c.navText[1]).appendTo(this._controls.$relative).on("click", a.proxy(function (a) { this.next(c.navSpeed) }, this)), c.dotsData || (this._templates = [a("<div>").addClass(c.dotClass).append(a("<span>")).prop("outerHTML")]), this._controls.$absolute = (c.dotsContainer ? a(c.dotsContainer) : a("<div>").addClass(c.dotsClass).appendTo(this.$element)).addClass("disabled"), this._controls.$absolute.on("click", "div", a.proxy(function (b) { var d = a(b.target).parent().is(this._controls.$absolute) ? a(b.target).index() : a(b.target).parent().index(); b.preventDefault(), this.to(d, c.dotsSpeed) }, this)); for (b in this._overrides) this._core[b] = a.proxy(this[b], this) }, e.prototype.destroy = function () { var a, b, c, d; for (a in this._handlers) this.$element.off(a, this._handlers[a]); for (b in this._controls) this._controls[b].remove(); for (d in this.overides) this._core[d] = this._overrides[d]; for (c in Object.getOwnPropertyNames(this)) "function" != typeof this[c] && (this[c] = null) }, e.prototype.update = function () { var a, b, c, d = this._core.clones().length / 2, e = d + this._core.items().length, f = this._core.maximum(!0), g = this._core.settings, h = g.center || g.autoWidth || g.dotsData ? 1 : g.dotsEach || g.items; if ("page" !== g.slideBy && (g.slideBy = Math.min(g.slideBy, g.items)), g.dots || "page" == g.slideBy) for (this._pages = [], a = d, b = 0, c = 0; a < e; a++) { if (b >= h || 0 === b) { if (this._pages.push({ start: Math.min(f, a - d), end: a - d + h - 1 }), Math.min(f, a - d) === f) break; b = 0, ++c } b += this._core.mergers(this._core.relative(a)) } }, e.prototype.draw = function () { var b, c = this._core.settings, d = this._core.items().length <= c.items, e = this._core.relative(this._core.current()), f = c.loop || c.rewind; this._controls.$relative.toggleClass("disabled", !c.nav || d), c.nav && (this._controls.$previous.toggleClass("disabled", !f && e <= this._core.minimum(!0)), this._controls.$next.toggleClass("disabled", !f && e >= this._core.maximum(!0))), this._controls.$absolute.toggleClass("disabled", !c.dots || d), c.dots && (b = this._pages.length - this._controls.$absolute.children().length, c.dotsData && 0 !== b ? this._controls.$absolute.html(this._templates.join("")) : b > 0 ? this._controls.$absolute.append(new Array(b + 1).join(this._templates[0])) : b < 0 && this._controls.$absolute.children().slice(b).remove(), this._controls.$absolute.find(".active").removeClass("active"), this._controls.$absolute.children().eq(a.inArray(this.current(), this._pages)).addClass("active")) }, e.prototype.onTrigger = function (b) { var c = this._core.settings; b.page = { index: a.inArray(this.current(), this._pages), count: this._pages.length, size: c && (c.center || c.autoWidth || c.dotsData ? 1 : c.dotsEach || c.items) } }, e.prototype.current = function () { var b = this._core.relative(this._core.current()); return a.grep(this._pages, a.proxy(function (a, c) { return a.start <= b && a.end >= b }, this)).pop() }, e.prototype.getPosition = function (b) { var c, d, e = this._core.settings; return "page" == e.slideBy ? (c = a.inArray(this.current(), this._pages), d = this._pages.length, b ? ++c : --c, c = this._pages[(c % d + d) % d].start) : (c = this._core.relative(this._core.current()), d = this._core.items().length, b ? c += e.slideBy : c -= e.slideBy), c }, e.prototype.next = function (b) { a.proxy(this._overrides.to, this._core)(this.getPosition(!0), b) }, e.prototype.prev = function (b) { a.proxy(this._overrides.to, this._core)(this.getPosition(!1), b) }, e.prototype.to = function (b, c, d) { var e; !d && this._pages.length ? (e = this._pages.length, a.proxy(this._overrides.to, this._core)(this._pages[(b % e + e) % e].start, c)) : a.proxy(this._overrides.to, this._core)(b, c) }, a.fn.owlCarousel.Constructor.Plugins.Navigation = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { "use strict"; var e = function (c) { this._core = c, this._hashes = {}, this.$element = this._core.$element, this._handlers = { "initialized.owl.carousel": a.proxy(function (c) { c.namespace && "URLHash" === this._core.settings.startPosition && a(b).trigger("hashchange.owl.navigation") }, this), "prepared.owl.carousel": a.proxy(function (b) { if (b.namespace) { var c = a(b.content).find("[data-hash]").addBack("[data-hash]").attr("data-hash"); if (!c) return; this._hashes[c] = b.content } }, this), "changed.owl.carousel": a.proxy(function (c) { if (c.namespace && "position" === c.property.name) { var d = this._core.items(this._core.relative(this._core.current())), e = a.map(this._hashes, function (a, b) { return a === d ? b : null }).join(); if (!e || b.location.hash.slice(1) === e) return; b.location.hash = e } }, this) }, this._core.options = a.extend({}, e.Defaults, this._core.options), this.$element.on(this._handlers), a(b).on("hashchange.owl.navigation", a.proxy(function (a) { var c = b.location.hash.substring(1), e = this._core.$stage.children(), f = this._hashes[c] && e.index(this._hashes[c]); f !== d && f !== this._core.current() && this._core.to(this._core.relative(f), !1, !0) }, this)) }; e.Defaults = { URLhashListener: !1 }, e.prototype.destroy = function () { var c, d; a(b).off("hashchange.owl.navigation"); for (c in this._handlers) this._core.$element.off(c, this._handlers[c]); for (d in Object.getOwnPropertyNames(this)) "function" != typeof this[d] && (this[d] = null) }, a.fn.owlCarousel.Constructor.Plugins.Hash = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { function e(b, c) { var e = !1, f = b.charAt(0).toUpperCase() + b.slice(1); return a.each((b + " " + h.join(f + " ") + f).split(" "), function (a, b) { if (g[b] !== d) return e = !c || b, !1 }), e } function f(a) { return e(a, !0) } var g = a("<support>").get(0).style, h = "Webkit Moz O ms".split(" "), i = { transition: { end: { WebkitTransition: "webkitTransitionEnd", MozTransition: "transitionend", OTransition: "oTransitionEnd", transition: "transitionend" } }, animation: { end: { WebkitAnimation: "webkitAnimationEnd", MozAnimation: "animationend", OAnimation: "oAnimationEnd", animation: "animationend" } } }, j = { csstransforms: function () { return !!e("transform") }, csstransforms3d: function () { return !!e("perspective") }, csstransitions: function () { return !!e("transition") }, cssanimations: function () { return !!e("animation") } }; j.csstransitions() && (a.support.transition = new String(f("transition")), a.support.transition.end = i.transition.end[a.support.transition]), j.cssanimations() && (a.support.animation = new String(f("animation")), a.support.animation.end = i.animation.end[a.support.animation]), j.csstransforms() && (a.support.transform = new String(f("transform")), a.support.transform3d = j.csstransforms3d()) }(window.Zepto || window.jQuery, window, document);

// Ion.RangeSlider | version 2.1.7 | https://github.com/IonDen/ion.rangeSlider
(function (f) { "function" === typeof define && define.amd ? define(["jquery"], function (p) { return f(p, document, window, navigator) }) : "object" === typeof exports ? f(require("jquery"), document, window, navigator) : f(jQuery, document, window, navigator) })(function (f, p, h, t, q) { var u = 0, m = function () { var a = t.userAgent, b = /msie\s\d+/i; return 0 < a.search(b) && (a = b.exec(a).toString(), a = a.split(" ")[1], 9 > a) ? (f("html").addClass("lt-ie9"), !0) : !1 }(); Function.prototype.bind || (Function.prototype.bind = function (a) { var b = this, d = [].slice; if ("function" != typeof b) throw new TypeError; var c = d.call(arguments, 1), e = function () { if (this instanceof e) { var g = function () { }; g.prototype = b.prototype; var g = new g, l = b.apply(g, c.concat(d.call(arguments))); return Object(l) === l ? l : g } return b.apply(a, c.concat(d.call(arguments))) }; return e }); Array.prototype.indexOf || (Array.prototype.indexOf = function (a, b) { var d; if (null == this) throw new TypeError('"this" is null or not defined'); var c = Object(this), e = c.length >>> 0; if (0 === e) return -1; d = +b || 0; Infinity === Math.abs(d) && (d = 0); if (d >= e) return -1; for (d = Math.max(0 <= d ? d : e - Math.abs(d), 0); d < e;) { if (d in c && c[d] === a) return d; d++ } return -1 }); var r = function (a, b, d) { this.VERSION = "2.1.7"; this.input = a; this.plugin_count = d; this.old_to = this.old_from = this.update_tm = this.calc_count = this.current_plugin = 0; this.raf_id = this.old_min_interval = null; this.is_update = this.is_key = this.no_diapason = this.force_redraw = this.dragging = !1; this.is_start = !0; this.is_click = this.is_resize = this.is_active = this.is_finish = !1; b = b || {}; this.$cache = { win: f(h), body: f(p.body), input: f(a), cont: null, rs: null, min: null, max: null, from: null, to: null, single: null, bar: null, line: null, s_single: null, s_from: null, s_to: null, shad_single: null, shad_from: null, shad_to: null, edge: null, grid: null, grid_labels: [] }; this.coords = { x_gap: 0, x_pointer: 0, w_rs: 0, w_rs_old: 0, w_handle: 0, p_gap: 0, p_gap_left: 0, p_gap_right: 0, p_step: 0, p_pointer: 0, p_handle: 0, p_single_fake: 0, p_single_real: 0, p_from_fake: 0, p_from_real: 0, p_to_fake: 0, p_to_real: 0, p_bar_x: 0, p_bar_w: 0, grid_gap: 0, big_num: 0, big: [], big_w: [], big_p: [], big_x: [] }; this.labels = { w_min: 0, w_max: 0, w_from: 0, w_to: 0, w_single: 0, p_min: 0, p_max: 0, p_from_fake: 0, p_from_left: 0, p_to_fake: 0, p_to_left: 0, p_single_fake: 0, p_single_left: 0 }; var c = this.$cache.input; a = c.prop("value"); var e; d = { type: "single", min: 10, max: 100, from: null, to: null, step: 1, min_interval: 0, max_interval: 0, drag_interval: !1, values: [], p_values: [], from_fixed: !1, from_min: null, from_max: null, from_shadow: !1, to_fixed: !1, to_min: null, to_max: null, to_shadow: !1, prettify_enabled: !0, prettify_separator: " ", prettify: null, force_edges: !1, keyboard: !1, keyboard_step: 5, grid: !1, grid_margin: !0, grid_num: 4, grid_snap: !1, hide_min_max: !1, hide_from_to: !1, prefix: "", postfix: "", max_postfix: "", decorate_both: !0, values_separator: " \u2014 ", input_values_separator: ";", disable: !1, onStart: null, onChange: null, onFinish: null, onUpdate: null }; "INPUT" !== c[0].nodeName && console && console.warn && console.warn("Base element should be <input>!", c[0]); c = { type: c.data("type"), min: c.data("min"), max: c.data("max"), from: c.data("from"), to: c.data("to"), step: c.data("step"), min_interval: c.data("minInterval"), max_interval: c.data("maxInterval"), drag_interval: c.data("dragInterval"), values: c.data("values"), from_fixed: c.data("fromFixed"), from_min: c.data("fromMin"), from_max: c.data("fromMax"), from_shadow: c.data("fromShadow"), to_fixed: c.data("toFixed"), to_min: c.data("toMin"), to_max: c.data("toMax"), to_shadow: c.data("toShadow"), prettify_enabled: c.data("prettifyEnabled"), prettify_separator: c.data("prettifySeparator"), force_edges: c.data("forceEdges"), keyboard: c.data("keyboard"), keyboard_step: c.data("keyboardStep"), grid: c.data("grid"), grid_margin: c.data("gridMargin"), grid_num: c.data("gridNum"), grid_snap: c.data("gridSnap"), hide_min_max: c.data("hideMinMax"), hide_from_to: c.data("hideFromTo"), prefix: c.data("prefix"), postfix: c.data("postfix"), max_postfix: c.data("maxPostfix"), decorate_both: c.data("decorateBoth"), values_separator: c.data("valuesSeparator"), input_values_separator: c.data("inputValuesSeparator"), disable: c.data("disable") }; c.values = c.values && c.values.split(","); for (e in c) c.hasOwnProperty(e) && (c[e] !== q && "" !== c[e] || delete c[e]); a !== q && "" !== a && (a = a.split(c.input_values_separator || b.input_values_separator || ";"), a[0] && a[0] == +a[0] && (a[0] = +a[0]), a[1] && a[1] == +a[1] && (a[1] = +a[1]), b && b.values && b.values.length ? (d.from = a[0] && b.values.indexOf(a[0]), d.to = a[1] && b.values.indexOf(a[1])) : (d.from = a[0] && +a[0], d.to = a[1] && +a[1])); f.extend(d, b); f.extend(d, c); this.options = d; this.update_check = {}; this.validate(); this.result = { input: this.$cache.input, slider: null, min: this.options.min, max: this.options.max, from: this.options.from, from_percent: 0, from_value: null, to: this.options.to, to_percent: 0, to_value: null }; this.init() }; r.prototype = { init: function (a) { this.no_diapason = !1; this.coords.p_step = this.convertToPercent(this.options.step, !0); this.target = "base"; this.toggleInput(); this.append(); this.setMinMax(); a ? (this.force_redraw = !0, this.calc(!0), this.callOnUpdate()) : (this.force_redraw = !0, this.calc(!0), this.callOnStart()); this.updateScene() }, append: function () { this.$cache.input.before('<span class="irs js-irs-' + this.plugin_count + '"></span>'); this.$cache.input.prop("readonly", !0); this.$cache.cont = this.$cache.input.prev(); this.result.slider = this.$cache.cont; this.$cache.cont.html('<span class="irs"><span class="irs-line" tabindex="-1"><span class="irs-line-left"></span><span class="irs-line-mid"></span><span class="irs-line-right"></span></span><span class="irs-min">0</span><span class="irs-max">1</span><span class="irs-from">0</span><span class="irs-to">0</span><span class="irs-single">0</span></span><span class="irs-grid"></span><span class="irs-bar"></span>'); this.$cache.rs = this.$cache.cont.find(".irs"); this.$cache.min = this.$cache.cont.find(".irs-min"); this.$cache.max = this.$cache.cont.find(".irs-max"); this.$cache.from = this.$cache.cont.find(".irs-from"); this.$cache.to = this.$cache.cont.find(".irs-to"); this.$cache.single = this.$cache.cont.find(".irs-single"); this.$cache.bar = this.$cache.cont.find(".irs-bar"); this.$cache.line = this.$cache.cont.find(".irs-line"); this.$cache.grid = this.$cache.cont.find(".irs-grid"); "single" === this.options.type ? (this.$cache.cont.append('<span class="irs-bar-edge"></span><span class="irs-shadow shadow-single"></span><span class="irs-slider single"></span>'), this.$cache.edge = this.$cache.cont.find(".irs-bar-edge"), this.$cache.s_single = this.$cache.cont.find(".single"), this.$cache.from[0].style.visibility = "hidden", this.$cache.to[0].style.visibility = "hidden", this.$cache.shad_single = this.$cache.cont.find(".shadow-single")) : (this.$cache.cont.append('<span class="irs-shadow shadow-from"></span><span class="irs-shadow shadow-to"></span><span class="irs-slider from"></span><span class="irs-slider to"></span>'), this.$cache.s_from = this.$cache.cont.find(".from"), this.$cache.s_to = this.$cache.cont.find(".to"), this.$cache.shad_from = this.$cache.cont.find(".shadow-from"), this.$cache.shad_to = this.$cache.cont.find(".shadow-to"), this.setTopHandler()); this.options.hide_from_to && (this.$cache.from[0].style.display = "none", this.$cache.to[0].style.display = "none", this.$cache.single[0].style.display = "none"); this.appendGrid(); this.options.disable ? (this.appendDisableMask(), this.$cache.input[0].disabled = !0) : (this.$cache.cont.removeClass("irs-disabled"), this.$cache.input[0].disabled = !1, this.bindEvents()); this.options.drag_interval && (this.$cache.bar[0].style.cursor = "ew-resize") }, setTopHandler: function () { var a = this.options.max, b = this.options.to; this.options.from > this.options.min && b === a ? this.$cache.s_from.addClass("type_last") : b < a && this.$cache.s_to.addClass("type_last") }, changeLevel: function (a) { switch (a) { case "single": this.coords.p_gap = this.toFixed(this.coords.p_pointer - this.coords.p_single_fake); break; case "from": this.coords.p_gap = this.toFixed(this.coords.p_pointer - this.coords.p_from_fake); this.$cache.s_from.addClass("state_hover"); this.$cache.s_from.addClass("type_last"); this.$cache.s_to.removeClass("type_last"); break; case "to": this.coords.p_gap = this.toFixed(this.coords.p_pointer - this.coords.p_to_fake); this.$cache.s_to.addClass("state_hover"); this.$cache.s_to.addClass("type_last"); this.$cache.s_from.removeClass("type_last"); break; case "both": this.coords.p_gap_left = this.toFixed(this.coords.p_pointer - this.coords.p_from_fake), this.coords.p_gap_right = this.toFixed(this.coords.p_to_fake - this.coords.p_pointer), this.$cache.s_to.removeClass("type_last"), this.$cache.s_from.removeClass("type_last") } }, appendDisableMask: function () { this.$cache.cont.append('<span class="irs-disable-mask"></span>'); this.$cache.cont.addClass("irs-disabled") }, remove: function () { this.$cache.cont.remove(); this.$cache.cont = null; this.$cache.line.off("keydown.irs_" + this.plugin_count); this.$cache.body.off("touchmove.irs_" + this.plugin_count); this.$cache.body.off("mousemove.irs_" + this.plugin_count); this.$cache.win.off("touchend.irs_" + this.plugin_count); this.$cache.win.off("mouseup.irs_" + this.plugin_count); m && (this.$cache.body.off("mouseup.irs_" + this.plugin_count), this.$cache.body.off("mouseleave.irs_" + this.plugin_count)); this.$cache.grid_labels = []; this.coords.big = []; this.coords.big_w = []; this.coords.big_p = []; this.coords.big_x = []; cancelAnimationFrame(this.raf_id) }, bindEvents: function () { if (!this.no_diapason) { this.$cache.body.on("touchmove.irs_" + this.plugin_count, this.pointerMove.bind(this)); this.$cache.body.on("mousemove.irs_" + this.plugin_count, this.pointerMove.bind(this)); this.$cache.win.on("touchend.irs_" + this.plugin_count, this.pointerUp.bind(this)); this.$cache.win.on("mouseup.irs_" + this.plugin_count, this.pointerUp.bind(this)); this.$cache.line.on("touchstart.irs_" + this.plugin_count, this.pointerClick.bind(this, "click")); this.$cache.line.on("mousedown.irs_" + this.plugin_count, this.pointerClick.bind(this, "click")); this.options.drag_interval && "double" === this.options.type ? (this.$cache.bar.on("touchstart.irs_" + this.plugin_count, this.pointerDown.bind(this, "both")), this.$cache.bar.on("mousedown.irs_" + this.plugin_count, this.pointerDown.bind(this, "both"))) : (this.$cache.bar.on("touchstart.irs_" + this.plugin_count, this.pointerClick.bind(this, "click")), this.$cache.bar.on("mousedown.irs_" + this.plugin_count, this.pointerClick.bind(this, "click"))); "single" === this.options.type ? (this.$cache.single.on("touchstart.irs_" + this.plugin_count, this.pointerDown.bind(this, "single")), this.$cache.s_single.on("touchstart.irs_" + this.plugin_count, this.pointerDown.bind(this, "single")), this.$cache.shad_single.on("touchstart.irs_" + this.plugin_count, this.pointerClick.bind(this, "click")), this.$cache.single.on("mousedown.irs_" + this.plugin_count, this.pointerDown.bind(this, "single")), this.$cache.s_single.on("mousedown.irs_" + this.plugin_count, this.pointerDown.bind(this, "single")), this.$cache.edge.on("mousedown.irs_" + this.plugin_count, this.pointerClick.bind(this, "click")), this.$cache.shad_single.on("mousedown.irs_" + this.plugin_count, this.pointerClick.bind(this, "click"))) : (this.$cache.single.on("touchstart.irs_" + this.plugin_count, this.pointerDown.bind(this, null)), this.$cache.single.on("mousedown.irs_" + this.plugin_count, this.pointerDown.bind(this, null)), this.$cache.from.on("touchstart.irs_" + this.plugin_count, this.pointerDown.bind(this, "from")), this.$cache.s_from.on("touchstart.irs_" + this.plugin_count, this.pointerDown.bind(this, "from")), this.$cache.to.on("touchstart.irs_" + this.plugin_count, this.pointerDown.bind(this, "to")), this.$cache.s_to.on("touchstart.irs_" + this.plugin_count, this.pointerDown.bind(this, "to")), this.$cache.shad_from.on("touchstart.irs_" + this.plugin_count, this.pointerClick.bind(this, "click")), this.$cache.shad_to.on("touchstart.irs_" + this.plugin_count, this.pointerClick.bind(this, "click")), this.$cache.from.on("mousedown.irs_" + this.plugin_count, this.pointerDown.bind(this, "from")), this.$cache.s_from.on("mousedown.irs_" + this.plugin_count, this.pointerDown.bind(this, "from")), this.$cache.to.on("mousedown.irs_" + this.plugin_count, this.pointerDown.bind(this, "to")), this.$cache.s_to.on("mousedown.irs_" + this.plugin_count, this.pointerDown.bind(this, "to")), this.$cache.shad_from.on("mousedown.irs_" + this.plugin_count, this.pointerClick.bind(this, "click")), this.$cache.shad_to.on("mousedown.irs_" + this.plugin_count, this.pointerClick.bind(this, "click"))); if (this.options.keyboard) this.$cache.line.on("keydown.irs_" + this.plugin_count, this.key.bind(this, "keyboard")); m && (this.$cache.body.on("mouseup.irs_" + this.plugin_count, this.pointerUp.bind(this)), this.$cache.body.on("mouseleave.irs_" + this.plugin_count, this.pointerUp.bind(this))) } }, pointerMove: function (a) { this.dragging && (this.coords.x_pointer = (a.pageX || a.originalEvent.touches && a.originalEvent.touches[0].pageX) - this.coords.x_gap, this.calc()) }, pointerUp: function (a) { this.current_plugin === this.plugin_count && this.is_active && (this.is_active = !1, this.$cache.cont.find(".state_hover").removeClass("state_hover"), this.force_redraw = !0, m && f("*").prop("unselectable", !1), this.updateScene(), this.restoreOriginalMinInterval(), (f.contains(this.$cache.cont[0], a.target) || this.dragging) && this.callOnFinish(), this.dragging = !1) }, pointerDown: function (a, b) { b.preventDefault(); var d = b.pageX || b.originalEvent.touches && b.originalEvent.touches[0].pageX; 2 !== b.button && ("both" === a && this.setTempMinInterval(), a || (a = this.target || "from"), this.current_plugin = this.plugin_count, this.target = a, this.dragging = this.is_active = !0, this.coords.x_gap = this.$cache.rs.offset().left, this.coords.x_pointer = d - this.coords.x_gap, this.calcPointerPercent(), this.changeLevel(a), m && f("*").prop("unselectable", !0), this.$cache.line.trigger("focus"), this.updateScene()) }, pointerClick: function (a, b) { b.preventDefault(); var d = b.pageX || b.originalEvent.touches && b.originalEvent.touches[0].pageX; 2 !== b.button && (this.current_plugin = this.plugin_count, this.target = a, this.is_click = !0, this.coords.x_gap = this.$cache.rs.offset().left, this.coords.x_pointer = +(d - this.coords.x_gap).toFixed(), this.force_redraw = !0, this.calc(), this.$cache.line.trigger("focus")) }, key: function (a, b) { if (!(this.current_plugin !== this.plugin_count || b.altKey || b.ctrlKey || b.shiftKey || b.metaKey)) { switch (b.which) { case 83: case 65: case 40: case 37: b.preventDefault(); this.moveByKey(!1); break; case 87: case 68: case 38: case 39: b.preventDefault(), this.moveByKey(!0) }return !0 } }, moveByKey: function (a) { var b = this.coords.p_pointer, b = a ? b + this.options.keyboard_step : b - this.options.keyboard_step; this.coords.x_pointer = this.toFixed(this.coords.w_rs / 100 * b); this.is_key = !0; this.calc() }, setMinMax: function () { this.options && (this.options.hide_min_max ? (this.$cache.min[0].style.display = "none", this.$cache.max[0].style.display = "none") : (this.options.values.length ? (this.$cache.min.html(this.decorate(this.options.p_values[this.options.min])), this.$cache.max.html(this.decorate(this.options.p_values[this.options.max]))) : (this.$cache.min.html(this.decorate(this._prettify(this.options.min), this.options.min)), this.$cache.max.html(this.decorate(this._prettify(this.options.max), this.options.max))), this.labels.w_min = this.$cache.min.outerWidth(!1), this.labels.w_max = this.$cache.max.outerWidth(!1))) }, setTempMinInterval: function () { var a = this.result.to - this.result.from; null === this.old_min_interval && (this.old_min_interval = this.options.min_interval); this.options.min_interval = a }, restoreOriginalMinInterval: function () { null !== this.old_min_interval && (this.options.min_interval = this.old_min_interval, this.old_min_interval = null) }, calc: function (a) { if (this.options) { this.calc_count++; if (10 === this.calc_count || a) this.calc_count = 0, this.coords.w_rs = this.$cache.rs.outerWidth(!1), this.calcHandlePercent(); if (this.coords.w_rs) { this.calcPointerPercent(); a = this.getHandleX(); "both" === this.target && (this.coords.p_gap = 0, a = this.getHandleX()); "click" === this.target && (this.coords.p_gap = this.coords.p_handle / 2, a = this.getHandleX(), this.target = this.options.drag_interval ? "both_one" : this.chooseHandle(a)); switch (this.target) { case "base": var b = (this.options.max - this.options.min) / 100; a = (this.result.from - this.options.min) / b; b = (this.result.to - this.options.min) / b; this.coords.p_single_real = this.toFixed(a); this.coords.p_from_real = this.toFixed(a); this.coords.p_to_real = this.toFixed(b); this.coords.p_single_real = this.checkDiapason(this.coords.p_single_real, this.options.from_min, this.options.from_max); this.coords.p_from_real = this.checkDiapason(this.coords.p_from_real, this.options.from_min, this.options.from_max); this.coords.p_to_real = this.checkDiapason(this.coords.p_to_real, this.options.to_min, this.options.to_max); this.coords.p_single_fake = this.convertToFakePercent(this.coords.p_single_real); this.coords.p_from_fake = this.convertToFakePercent(this.coords.p_from_real); this.coords.p_to_fake = this.convertToFakePercent(this.coords.p_to_real); this.target = null; break; case "single": if (this.options.from_fixed) break; this.coords.p_single_real = this.convertToRealPercent(a); this.coords.p_single_real = this.calcWithStep(this.coords.p_single_real); this.coords.p_single_real = this.checkDiapason(this.coords.p_single_real, this.options.from_min, this.options.from_max); this.coords.p_single_fake = this.convertToFakePercent(this.coords.p_single_real); break; case "from": if (this.options.from_fixed) break; this.coords.p_from_real = this.convertToRealPercent(a); this.coords.p_from_real = this.calcWithStep(this.coords.p_from_real); this.coords.p_from_real > this.coords.p_to_real && (this.coords.p_from_real = this.coords.p_to_real); this.coords.p_from_real = this.checkDiapason(this.coords.p_from_real, this.options.from_min, this.options.from_max); this.coords.p_from_real = this.checkMinInterval(this.coords.p_from_real, this.coords.p_to_real, "from"); this.coords.p_from_real = this.checkMaxInterval(this.coords.p_from_real, this.coords.p_to_real, "from"); this.coords.p_from_fake = this.convertToFakePercent(this.coords.p_from_real); break; case "to": if (this.options.to_fixed) break; this.coords.p_to_real = this.convertToRealPercent(a); this.coords.p_to_real = this.calcWithStep(this.coords.p_to_real); this.coords.p_to_real < this.coords.p_from_real && (this.coords.p_to_real = this.coords.p_from_real); this.coords.p_to_real = this.checkDiapason(this.coords.p_to_real, this.options.to_min, this.options.to_max); this.coords.p_to_real = this.checkMinInterval(this.coords.p_to_real, this.coords.p_from_real, "to"); this.coords.p_to_real = this.checkMaxInterval(this.coords.p_to_real, this.coords.p_from_real, "to"); this.coords.p_to_fake = this.convertToFakePercent(this.coords.p_to_real); break; case "both": if (this.options.from_fixed || this.options.to_fixed) break; a = this.toFixed(a + .001 * this.coords.p_handle); this.coords.p_from_real = this.convertToRealPercent(a) - this.coords.p_gap_left; this.coords.p_from_real = this.calcWithStep(this.coords.p_from_real); this.coords.p_from_real = this.checkDiapason(this.coords.p_from_real, this.options.from_min, this.options.from_max); this.coords.p_from_real = this.checkMinInterval(this.coords.p_from_real, this.coords.p_to_real, "from"); this.coords.p_from_fake = this.convertToFakePercent(this.coords.p_from_real); this.coords.p_to_real = this.convertToRealPercent(a) + this.coords.p_gap_right; this.coords.p_to_real = this.calcWithStep(this.coords.p_to_real); this.coords.p_to_real = this.checkDiapason(this.coords.p_to_real, this.options.to_min, this.options.to_max); this.coords.p_to_real = this.checkMinInterval(this.coords.p_to_real, this.coords.p_from_real, "to"); this.coords.p_to_fake = this.convertToFakePercent(this.coords.p_to_real); break; case "both_one": if (!this.options.from_fixed && !this.options.to_fixed) { var d = this.convertToRealPercent(a); a = this.result.to_percent - this.result.from_percent; var c = a / 2, b = d - c, d = d + c; 0 > b && (b = 0, d = b + a); 100 < d && (d = 100, b = d - a); this.coords.p_from_real = this.calcWithStep(b); this.coords.p_from_real = this.checkDiapason(this.coords.p_from_real, this.options.from_min, this.options.from_max); this.coords.p_from_fake = this.convertToFakePercent(this.coords.p_from_real); this.coords.p_to_real = this.calcWithStep(d); this.coords.p_to_real = this.checkDiapason(this.coords.p_to_real, this.options.to_min, this.options.to_max); this.coords.p_to_fake = this.convertToFakePercent(this.coords.p_to_real) } }"single" === this.options.type ? (this.coords.p_bar_x = this.coords.p_handle / 2, this.coords.p_bar_w = this.coords.p_single_fake, this.result.from_percent = this.coords.p_single_real, this.result.from = this.convertToValue(this.coords.p_single_real), this.options.values.length && (this.result.from_value = this.options.values[this.result.from])) : (this.coords.p_bar_x = this.toFixed(this.coords.p_from_fake + this.coords.p_handle / 2), this.coords.p_bar_w = this.toFixed(this.coords.p_to_fake - this.coords.p_from_fake), this.result.from_percent = this.coords.p_from_real, this.result.from = this.convertToValue(this.coords.p_from_real), this.result.to_percent = this.coords.p_to_real, this.result.to = this.convertToValue(this.coords.p_to_real), this.options.values.length && (this.result.from_value = this.options.values[this.result.from], this.result.to_value = this.options.values[this.result.to])); this.calcMinMax(); this.calcLabels() } } }, calcPointerPercent: function () { this.coords.w_rs ? (0 > this.coords.x_pointer || isNaN(this.coords.x_pointer) ? this.coords.x_pointer = 0 : this.coords.x_pointer > this.coords.w_rs && (this.coords.x_pointer = this.coords.w_rs), this.coords.p_pointer = this.toFixed(this.coords.x_pointer / this.coords.w_rs * 100)) : this.coords.p_pointer = 0 }, convertToRealPercent: function (a) { return a / (100 - this.coords.p_handle) * 100 }, convertToFakePercent: function (a) { return a / 100 * (100 - this.coords.p_handle) }, getHandleX: function () { var a = 100 - this.coords.p_handle, b = this.toFixed(this.coords.p_pointer - this.coords.p_gap); 0 > b ? b = 0 : b > a && (b = a); return b }, calcHandlePercent: function () { this.coords.w_handle = "single" === this.options.type ? this.$cache.s_single.outerWidth(!1) : this.$cache.s_from.outerWidth(!1); this.coords.p_handle = this.toFixed(this.coords.w_handle / this.coords.w_rs * 100) }, chooseHandle: function (a) { return "single" === this.options.type ? "single" : a >= this.coords.p_from_real + (this.coords.p_to_real - this.coords.p_from_real) / 2 ? this.options.to_fixed ? "from" : "to" : this.options.from_fixed ? "to" : "from" }, calcMinMax: function () { this.coords.w_rs && (this.labels.p_min = this.labels.w_min / this.coords.w_rs * 100, this.labels.p_max = this.labels.w_max / this.coords.w_rs * 100) }, calcLabels: function () { this.coords.w_rs && !this.options.hide_from_to && ("single" === this.options.type ? (this.labels.w_single = this.$cache.single.outerWidth(!1), this.labels.p_single_fake = this.labels.w_single / this.coords.w_rs * 100, this.labels.p_single_left = this.coords.p_single_fake + this.coords.p_handle / 2 - this.labels.p_single_fake / 2) : (this.labels.w_from = this.$cache.from.outerWidth(!1), this.labels.p_from_fake = this.labels.w_from / this.coords.w_rs * 100, this.labels.p_from_left = this.coords.p_from_fake + this.coords.p_handle / 2 - this.labels.p_from_fake / 2, this.labels.p_from_left = this.toFixed(this.labels.p_from_left), this.labels.p_from_left = this.checkEdges(this.labels.p_from_left, this.labels.p_from_fake), this.labels.w_to = this.$cache.to.outerWidth(!1), this.labels.p_to_fake = this.labels.w_to / this.coords.w_rs * 100, this.labels.p_to_left = this.coords.p_to_fake + this.coords.p_handle / 2 - this.labels.p_to_fake / 2, this.labels.p_to_left = this.toFixed(this.labels.p_to_left), this.labels.p_to_left = this.checkEdges(this.labels.p_to_left, this.labels.p_to_fake), this.labels.w_single = this.$cache.single.outerWidth(!1), this.labels.p_single_fake = this.labels.w_single / this.coords.w_rs * 100, this.labels.p_single_left = (this.labels.p_from_left + this.labels.p_to_left + this.labels.p_to_fake) / 2 - this.labels.p_single_fake / 2, this.labels.p_single_left = this.toFixed(this.labels.p_single_left)), this.labels.p_single_left = this.checkEdges(this.labels.p_single_left, this.labels.p_single_fake)) }, updateScene: function () { this.raf_id && (cancelAnimationFrame(this.raf_id), this.raf_id = null); clearTimeout(this.update_tm); this.update_tm = null; this.options && (this.drawHandles(), this.is_active ? this.raf_id = requestAnimationFrame(this.updateScene.bind(this)) : this.update_tm = setTimeout(this.updateScene.bind(this), 300)) }, drawHandles: function () { this.coords.w_rs = this.$cache.rs.outerWidth(!1); if (this.coords.w_rs) { this.coords.w_rs !== this.coords.w_rs_old && (this.target = "base", this.is_resize = !0); if (this.coords.w_rs !== this.coords.w_rs_old || this.force_redraw) this.setMinMax(), this.calc(!0), this.drawLabels(), this.options.grid && (this.calcGridMargin(), this.calcGridLabels()), this.force_redraw = !0, this.coords.w_rs_old = this.coords.w_rs, this.drawShadow(); if (this.coords.w_rs && (this.dragging || this.force_redraw || this.is_key)) { if (this.old_from !== this.result.from || this.old_to !== this.result.to || this.force_redraw || this.is_key) { this.drawLabels(); this.$cache.bar[0].style.left = this.coords.p_bar_x + "%"; this.$cache.bar[0].style.width = this.coords.p_bar_w + "%"; if ("single" === this.options.type) this.$cache.s_single[0].style.left = this.coords.p_single_fake + "%"; else { this.$cache.s_from[0].style.left = this.coords.p_from_fake + "%"; this.$cache.s_to[0].style.left = this.coords.p_to_fake + "%"; if (this.old_from !== this.result.from || this.force_redraw) this.$cache.from[0].style.left = this.labels.p_from_left + "%"; if (this.old_to !== this.result.to || this.force_redraw) this.$cache.to[0].style.left = this.labels.p_to_left + "%" } this.$cache.single[0].style.left = this.labels.p_single_left + "%"; this.writeToInput(); this.old_from === this.result.from && this.old_to === this.result.to || this.is_start || (this.$cache.input.trigger("change"), this.$cache.input.trigger("input")); this.old_from = this.result.from; this.old_to = this.result.to; this.is_resize || this.is_update || this.is_start || this.is_finish || this.callOnChange(); if (this.is_key || this.is_click) this.is_click = this.is_key = !1, this.callOnFinish(); this.is_finish = this.is_resize = this.is_update = !1 } this.force_redraw = this.is_click = this.is_key = this.is_start = !1 } } }, drawLabels: function () { if (this.options) { var a = this.options.values.length, b = this.options.p_values, d; if (!this.options.hide_from_to) if ("single" === this.options.type) a = a ? this.decorate(b[this.result.from]) : this.decorate(this._prettify(this.result.from), this.result.from), this.$cache.single.html(a), this.calcLabels(), this.$cache.min[0].style.visibility = this.labels.p_single_left < this.labels.p_min + 1 ? "hidden" : "visible", this.$cache.max[0].style.visibility = this.labels.p_single_left + this.labels.p_single_fake > 100 - this.labels.p_max - 1 ? "hidden" : "visible"; else { a ? (this.options.decorate_both ? (a = this.decorate(b[this.result.from]), a += this.options.values_separator, a += this.decorate(b[this.result.to])) : a = this.decorate(b[this.result.from] + this.options.values_separator + b[this.result.to]), d = this.decorate(b[this.result.from]), b = this.decorate(b[this.result.to])) : (this.options.decorate_both ? (a = this.decorate(this._prettify(this.result.from), this.result.from), a += this.options.values_separator, a += this.decorate(this._prettify(this.result.to), this.result.to)) : a = this.decorate(this._prettify(this.result.from) + this.options.values_separator + this._prettify(this.result.to), this.result.to), d = this.decorate(this._prettify(this.result.from), this.result.from), b = this.decorate(this._prettify(this.result.to), this.result.to)); this.$cache.single.html(a); this.$cache.from.html(d); this.$cache.to.html(b); this.calcLabels(); b = Math.min(this.labels.p_single_left, this.labels.p_from_left); a = this.labels.p_single_left + this.labels.p_single_fake; d = this.labels.p_to_left + this.labels.p_to_fake; var c = Math.max(a, d); this.labels.p_from_left + this.labels.p_from_fake >= this.labels.p_to_left ? (this.$cache.from[0].style.visibility = "hidden", this.$cache.to[0].style.visibility = "hidden", this.$cache.single[0].style.visibility = "visible", this.result.from === this.result.to ? ("from" === this.target ? this.$cache.from[0].style.visibility = "visible" : "to" === this.target ? this.$cache.to[0].style.visibility = "visible" : this.target || (this.$cache.from[0].style.visibility = "visible"), this.$cache.single[0].style.visibility = "hidden", c = d) : (this.$cache.from[0].style.visibility = "hidden", this.$cache.to[0].style.visibility = "hidden", this.$cache.single[0].style.visibility = "visible", c = Math.max(a, d))) : (this.$cache.from[0].style.visibility = "visible", this.$cache.to[0].style.visibility = "visible", this.$cache.single[0].style.visibility = "hidden"); this.$cache.min[0].style.visibility = b < this.labels.p_min + 1 ? "hidden" : "visible"; this.$cache.max[0].style.visibility = c > 100 - this.labels.p_max - 1 ? "hidden" : "visible" } } }, drawShadow: function () { var a = this.options, b = this.$cache, d = "number" === typeof a.from_min && !isNaN(a.from_min), c = "number" === typeof a.from_max && !isNaN(a.from_max), e = "number" === typeof a.to_min && !isNaN(a.to_min), g = "number" === typeof a.to_max && !isNaN(a.to_max); "single" === a.type ? a.from_shadow && (d || c) ? (d = this.convertToPercent(d ? a.from_min : a.min), c = this.convertToPercent(c ? a.from_max : a.max) - d, d = this.toFixed(d - this.coords.p_handle / 100 * d), c = this.toFixed(c - this.coords.p_handle / 100 * c), d += this.coords.p_handle / 2, b.shad_single[0].style.display = "block", b.shad_single[0].style.left = d + "%", b.shad_single[0].style.width = c + "%") : b.shad_single[0].style.display = "none" : (a.from_shadow && (d || c) ? (d = this.convertToPercent(d ? a.from_min : a.min), c = this.convertToPercent(c ? a.from_max : a.max) - d, d = this.toFixed(d - this.coords.p_handle / 100 * d), c = this.toFixed(c - this.coords.p_handle / 100 * c), d += this.coords.p_handle / 2, b.shad_from[0].style.display = "block", b.shad_from[0].style.left = d + "%", b.shad_from[0].style.width = c + "%") : b.shad_from[0].style.display = "none", a.to_shadow && (e || g) ? (e = this.convertToPercent(e ? a.to_min : a.min), a = this.convertToPercent(g ? a.to_max : a.max) - e, e = this.toFixed(e - this.coords.p_handle / 100 * e), a = this.toFixed(a - this.coords.p_handle / 100 * a), e += this.coords.p_handle / 2, b.shad_to[0].style.display = "block", b.shad_to[0].style.left = e + "%", b.shad_to[0].style.width = a + "%") : b.shad_to[0].style.display = "none") }, writeToInput: function () { "single" === this.options.type ? (this.options.values.length ? this.$cache.input.prop("value", this.result.from_value) : this.$cache.input.prop("value", this.result.from), this.$cache.input.data("from", this.result.from)) : (this.options.values.length ? this.$cache.input.prop("value", this.result.from_value + this.options.input_values_separator + this.result.to_value) : this.$cache.input.prop("value", this.result.from + this.options.input_values_separator + this.result.to), this.$cache.input.data("from", this.result.from), this.$cache.input.data("to", this.result.to)) }, callOnStart: function () { this.writeToInput(); if (this.options.onStart && "function" === typeof this.options.onStart) this.options.onStart(this.result) }, callOnChange: function () { this.writeToInput(); if (this.options.onChange && "function" === typeof this.options.onChange) this.options.onChange(this.result) }, callOnFinish: function () { this.writeToInput(); if (this.options.onFinish && "function" === typeof this.options.onFinish) this.options.onFinish(this.result) }, callOnUpdate: function () { this.writeToInput(); if (this.options.onUpdate && "function" === typeof this.options.onUpdate) this.options.onUpdate(this.result) }, toggleInput: function () { this.$cache.input.toggleClass("irs-hidden-input") }, convertToPercent: function (a, b) { var d = this.options.max - this.options.min; return d ? this.toFixed((b ? a : a - this.options.min) / (d / 100)) : (this.no_diapason = !0, 0) }, convertToValue: function (a) { var b = this.options.min, d = this.options.max, c = b.toString().split(".")[1], e = d.toString().split(".")[1], g, l, f = 0, k = 0; if (0 === a) return this.options.min; if (100 === a) return this.options.max; c && (f = g = c.length); e && (f = l = e.length); g && l && (f = g >= l ? g : l); 0 > b && (k = Math.abs(b), b = +(b + k).toFixed(f), d = +(d + k).toFixed(f)); a = (d - b) / 100 * a + b; (b = this.options.step.toString().split(".")[1]) ? a = +a.toFixed(b.length) : (a /= this.options.step, a *= this.options.step, a = +a.toFixed(0)); k && (a -= k); k = b ? +a.toFixed(b.length) : this.toFixed(a); k < this.options.min ? k = this.options.min : k > this.options.max && (k = this.options.max); return k }, calcWithStep: function (a) { var b = Math.round(a / this.coords.p_step) * this.coords.p_step; 100 < b && (b = 100); 100 === a && (b = 100); return this.toFixed(b) }, checkMinInterval: function (a, b, d) { var c = this.options; if (!c.min_interval) return a; a = this.convertToValue(a); b = this.convertToValue(b); "from" === d ? b - a < c.min_interval && (a = b - c.min_interval) : a - b < c.min_interval && (a = b + c.min_interval); return this.convertToPercent(a) }, checkMaxInterval: function (a, b, d) { var c = this.options; if (!c.max_interval) return a; a = this.convertToValue(a); b = this.convertToValue(b); "from" === d ? b - a > c.max_interval && (a = b - c.max_interval) : a - b > c.max_interval && (a = b + c.max_interval); return this.convertToPercent(a) }, checkDiapason: function (a, b, d) { a = this.convertToValue(a); var c = this.options; "number" !== typeof b && (b = c.min); "number" !== typeof d && (d = c.max); a < b && (a = b); a > d && (a = d); return this.convertToPercent(a) }, toFixed: function (a) { a = a.toFixed(20); return +a }, _prettify: function (a) { return this.options.prettify_enabled ? this.options.prettify && "function" === typeof this.options.prettify ? this.options.prettify(a) : this.prettify(a) : a }, prettify: function (a) { return a.toString().replace(/(\d{1,3}(?=(?:\d\d\d)+(?!\d)))/g, "$1" + this.options.prettify_separator) }, checkEdges: function (a, b) { if (!this.options.force_edges) return this.toFixed(a); 0 > a ? a = 0 : a > 100 - b && (a = 100 - b); return this.toFixed(a) }, validate: function () { var a = this.options, b = this.result, d = a.values, c = d.length, e, g; "string" === typeof a.min && (a.min = +a.min); "string" === typeof a.max && (a.max = +a.max); "string" === typeof a.from && (a.from = +a.from); "string" === typeof a.to && (a.to = +a.to); "string" === typeof a.step && (a.step = +a.step); "string" === typeof a.from_min && (a.from_min = +a.from_min); "string" === typeof a.from_max && (a.from_max = +a.from_max); "string" === typeof a.to_min && (a.to_min = +a.to_min); "string" === typeof a.to_max && (a.to_max = +a.to_max); "string" === typeof a.keyboard_step && (a.keyboard_step = +a.keyboard_step); "string" === typeof a.grid_num && (a.grid_num = +a.grid_num); a.max < a.min && (a.max = a.min); if (c) for (a.p_values = [], a.min = 0, a.max = c - 1, a.step = 1, a.grid_num = a.max, a.grid_snap = !0, g = 0; g < c; g++)e = +d[g], isNaN(e) ? e = d[g] : (d[g] = e, e = this._prettify(e)), a.p_values.push(e); if ("number" !== typeof a.from || isNaN(a.from)) a.from = a.min; if ("number" !== typeof a.to || isNaN(a.to)) a.to = a.max; "single" === a.type ? (a.from < a.min && (a.from = a.min), a.from > a.max && (a.from = a.max)) : (a.from < a.min && (a.from = a.min), a.from > a.max && (a.from = a.max), a.to < a.min && (a.to = a.min), a.to > a.max && (a.to = a.max), this.update_check.from && (this.update_check.from !== a.from && a.from > a.to && (a.from = a.to), this.update_check.to !== a.to && a.to < a.from && (a.to = a.from)), a.from > a.to && (a.from = a.to), a.to < a.from && (a.to = a.from)); if ("number" !== typeof a.step || isNaN(a.step) || !a.step || 0 > a.step) a.step = 1; if ("number" !== typeof a.keyboard_step || isNaN(a.keyboard_step) || !a.keyboard_step || 0 > a.keyboard_step) a.keyboard_step = 5; "number" === typeof a.from_min && a.from < a.from_min && (a.from = a.from_min); "number" === typeof a.from_max && a.from > a.from_max && (a.from = a.from_max); "number" === typeof a.to_min && a.to < a.to_min && (a.to = a.to_min); "number" === typeof a.to_max && a.from > a.to_max && (a.to = a.to_max); if (b) { b.min !== a.min && (b.min = a.min); b.max !== a.max && (b.max = a.max); if (b.from < b.min || b.from > b.max) b.from = a.from; if (b.to < b.min || b.to > b.max) b.to = a.to } if ("number" !== typeof a.min_interval || isNaN(a.min_interval) || !a.min_interval || 0 > a.min_interval) a.min_interval = 0; if ("number" !== typeof a.max_interval || isNaN(a.max_interval) || !a.max_interval || 0 > a.max_interval) a.max_interval = 0; a.min_interval && a.min_interval > a.max - a.min && (a.min_interval = a.max - a.min); a.max_interval && a.max_interval > a.max - a.min && (a.max_interval = a.max - a.min) }, decorate: function (a, b) { var d = "", c = this.options; c.prefix && (d += c.prefix); d += a; c.max_postfix && (c.values.length && a === c.p_values[c.max] ? (d += c.max_postfix, c.postfix && (d += " ")) : b === c.max && (d += c.max_postfix, c.postfix && (d += " "))); c.postfix && (d += c.postfix); return d }, updateFrom: function () { this.result.from = this.options.from; this.result.from_percent = this.convertToPercent(this.result.from); this.options.values && (this.result.from_value = this.options.values[this.result.from]) }, updateTo: function () { this.result.to = this.options.to; this.result.to_percent = this.convertToPercent(this.result.to); this.options.values && (this.result.to_value = this.options.values[this.result.to]) }, updateResult: function () { this.result.min = this.options.min; this.result.max = this.options.max; this.updateFrom(); this.updateTo() }, appendGrid: function () { if (this.options.grid) { var a = this.options, b, d; b = a.max - a.min; var c = a.grid_num, e, g, f = 4, h, k, m, n = ""; this.calcGridMargin(); a.grid_snap ? 50 < b ? (c = 50 / a.step, e = this.toFixed(a.step / .5)) : (c = b / a.step, e = this.toFixed(a.step / (b / 100))) : e = this.toFixed(100 / c); 4 < c && (f = 3); 7 < c && (f = 2); 14 < c && (f = 1); 28 < c && (f = 0); for (b = 0; b < c + 1; b++) { h = f; g = this.toFixed(e * b); 100 < g && (g = 100, h -= 2, 0 > h && (h = 0)); this.coords.big[b] = g; k = (g - e * (b - 1)) / (h + 1); for (d = 1; d <= h && 0 !== g; d++)m = this.toFixed(g - k * d), n += '<span class="irs-grid-pol small" style="left: ' + m + '%"></span>'; n += '<span class="irs-grid-pol" style="left: ' + g + '%"></span>'; d = this.convertToValue(g); d = a.values.length ? a.p_values[d] : this._prettify(d); n += '<span class="irs-grid-text js-grid-text-' + b + '" style="left: ' + g + '%">' + d + "</span>" } this.coords.big_num = Math.ceil(c + 1); this.$cache.cont.addClass("irs-with-grid"); this.$cache.grid.html(n); this.cacheGridLabels() } }, cacheGridLabels: function () { var a, b, d = this.coords.big_num; for (b = 0; b < d; b++)a = this.$cache.grid.find(".js-grid-text-" + b), this.$cache.grid_labels.push(a); this.calcGridLabels() }, calcGridLabels: function () { var a, b; b = []; var d = [], c = this.coords.big_num; for (a = 0; a < c; a++)this.coords.big_w[a] = this.$cache.grid_labels[a].outerWidth(!1), this.coords.big_p[a] = this.toFixed(this.coords.big_w[a] / this.coords.w_rs * 100), this.coords.big_x[a] = this.toFixed(this.coords.big_p[a] / 2), b[a] = this.toFixed(this.coords.big[a] - this.coords.big_x[a]), d[a] = this.toFixed(b[a] + this.coords.big_p[a]); this.options.force_edges && (b[0] < -this.coords.grid_gap && (b[0] = -this.coords.grid_gap, d[0] = this.toFixed(b[0] + this.coords.big_p[0]), this.coords.big_x[0] = this.coords.grid_gap), d[c - 1] > 100 + this.coords.grid_gap && (d[c - 1] = 100 + this.coords.grid_gap, b[c - 1] = this.toFixed(d[c - 1] - this.coords.big_p[c - 1]), this.coords.big_x[c - 1] = this.toFixed(this.coords.big_p[c - 1] - this.coords.grid_gap))); this.calcGridCollision(2, b, d); this.calcGridCollision(4, b, d); for (a = 0; a < c; a++)b = this.$cache.grid_labels[a][0], this.coords.big_x[a] !== Number.POSITIVE_INFINITY && (b.style.marginLeft = -this.coords.big_x[a] + "%") }, calcGridCollision: function (a, b, d) { var c, e, g, f = this.coords.big_num; for (c = 0; c < f; c += a) { e = c + a / 2; if (e >= f) break; g = this.$cache.grid_labels[e][0]; g.style.visibility = d[c] <= b[e] ? "visible" : "hidden" } }, calcGridMargin: function () { this.options.grid_margin && (this.coords.w_rs = this.$cache.rs.outerWidth(!1), this.coords.w_rs && (this.coords.w_handle = "single" === this.options.type ? this.$cache.s_single.outerWidth(!1) : this.$cache.s_from.outerWidth(!1), this.coords.p_handle = this.toFixed(this.coords.w_handle / this.coords.w_rs * 100), this.coords.grid_gap = this.toFixed(this.coords.p_handle / 2 - .1), this.$cache.grid[0].style.width = this.toFixed(100 - this.coords.p_handle) + "%", this.$cache.grid[0].style.left = this.coords.grid_gap + "%")) }, update: function (a) { this.input && (this.is_update = !0, this.options.from = this.result.from, this.options.to = this.result.to, this.update_check.from = this.result.from, this.update_check.to = this.result.to, this.options = f.extend(this.options, a), this.validate(), this.updateResult(a), this.toggleInput(), this.remove(), this.init(!0)) }, reset: function () { this.input && (this.updateResult(), this.update()) }, destroy: function () { this.input && (this.toggleInput(), this.$cache.input.prop("readonly", !1), f.data(this.input, "ionRangeSlider", null), this.remove(), this.options = this.input = null) } }; f.fn.ionRangeSlider = function (a) { return this.each(function () { f.data(this, "ionRangeSlider") || f.data(this, "ionRangeSlider", new r(this, a, u++)) }) }; (function () { for (var a = 0, b = ["ms", "moz", "webkit", "o"], d = 0; d < b.length && !h.requestAnimationFrame; ++d)h.requestAnimationFrame = h[b[d] + "RequestAnimationFrame"], h.cancelAnimationFrame = h[b[d] + "CancelAnimationFrame"] || h[b[d] + "CancelRequestAnimationFrame"]; h.requestAnimationFrame || (h.requestAnimationFrame = function (b, d) { var c = (new Date).getTime(), e = Math.max(0, 16 - (c - a)), f = h.setTimeout(function () { b(c + e) }, e); a = c + e; return f }); h.cancelAnimationFrame || (h.cancelAnimationFrame = function (a) { clearTimeout(a) }) })() });


/*!
 * The Final Countdown for jQuery v2.1.0 (http://hilios.github.io/jQuery.countdown/)
 * Copyright (c) 2015 Edson Hilios
 */
!function (a) { "use strict"; "function" == typeof define && define.amd ? define(["jquery"], a) : a(jQuery) }(function (a) { "use strict"; function b(a) { if (a instanceof Date) return a; if (String(a).match(g)) return String(a).match(/^[0-9]*$/) && (a = Number(a)), String(a).match(/\-/) && (a = String(a).replace(/\-/g, "/")), new Date(a); throw new Error("Couldn't cast `" + a + "` to a date object.") } function c(a) { var b = a.toString().replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1"); return new RegExp(b) } function d(a) { return function (b) { var d = b.match(/%(-|!)?[A-Z]{1}(:[^;]+;)?/gi); if (d) for (var f = 0, g = d.length; g > f; ++f) { var h = d[f].match(/%(-|!)?([a-zA-Z]{1})(:[^;]+;)?/), j = c(h[0]), k = h[1] || "", l = h[3] || "", m = null; h = h[2], i.hasOwnProperty(h) && (m = i[h], m = Number(a[m])), null !== m && ("!" === k && (m = e(l, m)), "" === k && 10 > m && (m = "0" + m.toString()), b = b.replace(j, m.toString())) } return b = b.replace(/%%/, "%") } } function e(a, b) { var c = "s", d = ""; return a && (a = a.replace(/(:|;|\s)/gi, "").split(/\,/), 1 === a.length ? c = a[0] : (d = a[0], c = a[1])), 1 === Math.abs(b) ? d : c } var f = [], g = [], h = { precision: 100, elapse: !1 }; g.push(/^[0-9]*$/.source), g.push(/([0-9]{1,2}\/){2}[0-9]{4}( [0-9]{1,2}(:[0-9]{2}){2})?/.source), g.push(/[0-9]{4}([\/\-][0-9]{1,2}){2}( [0-9]{1,2}(:[0-9]{2}){2})?/.source), g = new RegExp(g.join("|")); var i = { Y: "years", m: "months", n: "daysToMonth", w: "weeks", d: "daysToWeek", D: "totalDays", H: "hours", M: "minutes", S: "seconds" }, j = function (b, c, d) { this.el = b, this.$el = a(b), this.interval = null, this.offset = {}, this.options = a.extend({}, h), this.instanceNumber = f.length, f.push(this), this.$el.data("countdown-instance", this.instanceNumber), d && ("function" == typeof d ? (this.$el.on("update.countdown", d), this.$el.on("stoped.countdown", d), this.$el.on("finish.countdown", d)) : this.options = a.extend({}, h, d)), this.setFinalDate(c), this.start() }; a.extend(j.prototype, { start: function () { null !== this.interval && clearInterval(this.interval); var a = this; this.update(), this.interval = setInterval(function () { a.update.call(a) }, this.options.precision) }, stop: function () { clearInterval(this.interval), this.interval = null, this.dispatchEvent("stoped") }, toggle: function () { this.interval ? this.stop() : this.start() }, pause: function () { this.stop() }, resume: function () { this.start() }, remove: function () { this.stop.call(this), f[this.instanceNumber] = null, delete this.$el.data().countdownInstance }, setFinalDate: function (a) { this.finalDate = b(a) }, update: function () { if (0 === this.$el.closest("html").length) return void this.remove(); var b, c = void 0 !== a._data(this.el, "events"), d = new Date; b = this.finalDate.getTime() - d.getTime(), b = Math.ceil(b / 1e3), b = !this.options.elapse && 0 > b ? 0 : Math.abs(b), this.totalSecsLeft !== b && c && (this.totalSecsLeft = b, this.elapsed = d >= this.finalDate, this.offset = { seconds: this.totalSecsLeft % 60, minutes: Math.floor(this.totalSecsLeft / 60) % 60, hours: Math.floor(this.totalSecsLeft / 60 / 60) % 24, days: Math.floor(this.totalSecsLeft / 60 / 60 / 24) % 7, daysToWeek: Math.floor(this.totalSecsLeft / 60 / 60 / 24) % 7, daysToMonth: Math.floor(this.totalSecsLeft / 60 / 60 / 24 % 30.4368), totalDays: Math.floor(this.totalSecsLeft / 60 / 60 / 24), weeks: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 7), months: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 30.4368), years: Math.abs(this.finalDate.getFullYear() - d.getFullYear()) }, this.options.elapse || 0 !== this.totalSecsLeft ? this.dispatchEvent("update") : (this.stop(), this.dispatchEvent("finish"))) }, dispatchEvent: function (b) { var c = a.Event(b + ".countdown"); c.finalDate = this.finalDate, c.elapsed = this.elapsed, c.offset = a.extend({}, this.offset), c.strftime = d(this.offset), this.$el.trigger(c) } }), a.fn.countdown = function () { var b = Array.prototype.slice.call(arguments, 0); return this.each(function () { var c = a(this).data("countdown-instance"); if (void 0 !== c) { var d = f[c], e = b[0]; j.prototype.hasOwnProperty(e) ? d[e].apply(d, b.slice(1)) : null === String(e).match(/^[$A-Z_][0-9A-Z_$]*$/i) ? (d.setFinalDate.call(d, e), d.start()) : a.error("Method %s does not exist on jQuery.countdown".replace(/\%s/gi, e)) } else new j(this, b[0], b[1]) }) } });


/**
 * jQuery CSS Customizable Scrollbar
 * @version 0.2.8
 * @url https://github.com/gromo/jquery.scrollbar/
 */
!function (l, e) { "function" == typeof define && define.amd ? define(["jquery"], e) : e(l.jQuery) }(this, function (l) { "use strict"; function e(e) { if (t.webkit && !e) return { height: 0, width: 0 }; if (!t.data.outer) { var o = { border: "none", "box-sizing": "content-box", height: "200px", margin: "0", padding: "0", width: "200px" }; t.data.inner = l("<div>").css(l.extend({}, o)), t.data.outer = l("<div>").css(l.extend({ left: "-1000px", overflow: "scroll", position: "absolute", top: "-1000px" }, o)).append(t.data.inner).appendTo("body") } return t.data.outer.scrollLeft(1e3).scrollTop(1e3), { height: Math.ceil(t.data.outer.offset().top - t.data.inner.offset().top || 0), width: Math.ceil(t.data.outer.offset().left - t.data.inner.offset().left || 0) } } function o() { var l = e(!0); return !(l.height || l.width) } function s(l) { var e = l.originalEvent; return e.axis && e.axis === e.HORIZONTAL_AXIS ? !1 : e.wheelDeltaX ? !1 : !0 } var r = !1, t = { data: { index: 0, name: "scrollbar" }, macosx: /mac/i.test(navigator.platform), mobile: /android|webos|iphone|ipad|ipod|blackberry/i.test(navigator.userAgent), overlay: null, scroll: null, scrolls: [], webkit: /webkit/i.test(navigator.userAgent) && !/edge\/\d+/i.test(navigator.userAgent) }; t.scrolls.add = function (l) { this.remove(l).push(l) }, t.scrolls.remove = function (e) { for (; l.inArray(e, this) >= 0;)this.splice(l.inArray(e, this), 1); return this }; var i = { autoScrollSize: !0, autoUpdate: !0, debug: !1, disableBodyScroll: !1, duration: 200, ignoreMobile: !1, ignoreOverlay: !1, scrollStep: 30, showArrows: !1, stepScrolling: !0, scrollx: null, scrolly: null, onDestroy: null, onInit: null, onScroll: null, onUpdate: null }, n = function (s) { t.scroll || (t.overlay = o(), t.scroll = e(), a(), l(window).resize(function () { var l = !1; if (t.scroll && (t.scroll.height || t.scroll.width)) { var o = e(); (o.height !== t.scroll.height || o.width !== t.scroll.width) && (t.scroll = o, l = !0) } a(l) })), this.container = s, this.namespace = ".scrollbar_" + t.data.index++, this.options = l.extend({}, i, window.jQueryScrollbarOptions || {}), this.scrollTo = null, this.scrollx = {}, this.scrolly = {}, s.data(t.data.name, this), t.scrolls.add(this) }; n.prototype = { destroy: function () { if (this.wrapper) { this.container.removeData(t.data.name), t.scrolls.remove(this); var e = this.container.scrollLeft(), o = this.container.scrollTop(); this.container.insertBefore(this.wrapper).css({ height: "", margin: "", "max-height": "" }).removeClass("scroll-content scroll-scrollx_visible scroll-scrolly_visible").off(this.namespace).scrollLeft(e).scrollTop(o), this.scrollx.scroll.removeClass("scroll-scrollx_visible").find("div").andSelf().off(this.namespace), this.scrolly.scroll.removeClass("scroll-scrolly_visible").find("div").andSelf().off(this.namespace), this.wrapper.remove(), l(document).add("body").off(this.namespace), l.isFunction(this.options.onDestroy) && this.options.onDestroy.apply(this, [this.container]) } }, init: function (e) { var o = this, r = this.container, i = this.containerWrapper || r, n = this.namespace, c = l.extend(this.options, e || {}), a = { x: this.scrollx, y: this.scrolly }, d = this.wrapper, h = { scrollLeft: r.scrollLeft(), scrollTop: r.scrollTop() }; if (t.mobile && c.ignoreMobile || t.overlay && c.ignoreOverlay || t.macosx && !t.webkit) return !1; if (d) i.css({ height: "auto", "margin-bottom": -1 * t.scroll.height + "px", "margin-right": -1 * t.scroll.width + "px", "max-height": "" }); else { if (this.wrapper = d = l("<div>").addClass("scroll-wrapper").addClass(r.attr("class")).css("position", "absolute" == r.css("position") ? "absolute" : "relative").insertBefore(r).append(r), r.is("textarea") && (this.containerWrapper = i = l("<div>").insertBefore(r).append(r), d.addClass("scroll-textarea")), i.addClass("scroll-content").css({ height: "auto", "margin-bottom": -1 * t.scroll.height + "px", "margin-right": -1 * t.scroll.width + "px", "max-height": "" }), r.on("scroll" + n, function () { l.isFunction(c.onScroll) && c.onScroll.call(o, { maxScroll: a.y.maxScrollOffset, scroll: r.scrollTop(), size: a.y.size, visible: a.y.visible }, { maxScroll: a.x.maxScrollOffset, scroll: r.scrollLeft(), size: a.x.size, visible: a.x.visible }), a.x.isVisible && a.x.scroll.bar.css("left", r.scrollLeft() * a.x.kx + "px"), a.y.isVisible && a.y.scroll.bar.css("top", r.scrollTop() * a.y.kx + "px") }), d.on("scroll" + n, function () { d.scrollTop(0).scrollLeft(0) }), c.disableBodyScroll) { var p = function (l) { s(l) ? a.y.isVisible && a.y.mousewheel(l) : a.x.isVisible && a.x.mousewheel(l) }; d.on("MozMousePixelScroll" + n, p), d.on("mousewheel" + n, p), t.mobile && d.on("touchstart" + n, function (e) { var o = e.originalEvent.touches && e.originalEvent.touches[0] || e, s = { pageX: o.pageX, pageY: o.pageY }, t = { left: r.scrollLeft(), top: r.scrollTop() }; l(document).on("touchmove" + n, function (l) { var e = l.originalEvent.targetTouches && l.originalEvent.targetTouches[0] || l; r.scrollLeft(t.left + s.pageX - e.pageX), r.scrollTop(t.top + s.pageY - e.pageY), l.preventDefault() }), l(document).on("touchend" + n, function () { l(document).off(n) }) }) } l.isFunction(c.onInit) && c.onInit.apply(this, [r]) } l.each(a, function (e, t) { var i = null, d = 1, h = "x" === e ? "scrollLeft" : "scrollTop", p = c.scrollStep, u = function () { var l = r[h](); r[h](l + p), 1 == d && l + p >= f && (l = r[h]()), -1 == d && f >= l + p && (l = r[h]()), r[h]() == l && i && i() }, f = 0; t.scroll || (t.scroll = o._getScroll(c["scroll" + e]).addClass("scroll-" + e), c.showArrows && t.scroll.addClass("scroll-element_arrows_visible"), t.mousewheel = function (l) { if (!t.isVisible || "x" === e && s(l)) return !0; if ("y" === e && !s(l)) return a.x.mousewheel(l), !0; var i = -1 * l.originalEvent.wheelDelta || l.originalEvent.detail, n = t.size - t.visible - t.offset; return (i > 0 && n > f || 0 > i && f > 0) && (f += i, 0 > f && (f = 0), f > n && (f = n), o.scrollTo = o.scrollTo || {}, o.scrollTo[h] = f, setTimeout(function () { o.scrollTo && (r.stop().animate(o.scrollTo, 240, "linear", function () { f = r[h]() }), o.scrollTo = null) }, 1)), l.preventDefault(), !1 }, t.scroll.on("MozMousePixelScroll" + n, t.mousewheel).on("mousewheel" + n, t.mousewheel).on("mouseenter" + n, function () { f = r[h]() }), t.scroll.find(".scroll-arrow, .scroll-element_track").on("mousedown" + n, function (s) { if (1 != s.which) return !0; d = 1; var n = { eventOffset: s["x" === e ? "pageX" : "pageY"], maxScrollValue: t.size - t.visible - t.offset, scrollbarOffset: t.scroll.bar.offset()["x" === e ? "left" : "top"], scrollbarSize: t.scroll.bar["x" === e ? "outerWidth" : "outerHeight"]() }, a = 0, v = 0; return l(this).hasClass("scroll-arrow") ? (d = l(this).hasClass("scroll-arrow_more") ? 1 : -1, p = c.scrollStep * d, f = d > 0 ? n.maxScrollValue : 0) : (d = n.eventOffset > n.scrollbarOffset + n.scrollbarSize ? 1 : n.eventOffset < n.scrollbarOffset ? -1 : 0, p = Math.round(.75 * t.visible) * d, f = n.eventOffset - n.scrollbarOffset - (c.stepScrolling ? 1 == d ? n.scrollbarSize : 0 : Math.round(n.scrollbarSize / 2)), f = r[h]() + f / t.kx), o.scrollTo = o.scrollTo || {}, o.scrollTo[h] = c.stepScrolling ? r[h]() + p : f, c.stepScrolling && (i = function () { f = r[h](), clearInterval(v), clearTimeout(a), a = 0, v = 0 }, a = setTimeout(function () { v = setInterval(u, 40) }, c.duration + 100)), setTimeout(function () { o.scrollTo && (r.animate(o.scrollTo, c.duration), o.scrollTo = null) }, 1), o._handleMouseDown(i, s) }), t.scroll.bar.on("mousedown" + n, function (s) { if (1 != s.which) return !0; var i = s["x" === e ? "pageX" : "pageY"], c = r[h](); return t.scroll.addClass("scroll-draggable"), l(document).on("mousemove" + n, function (l) { var o = parseInt((l["x" === e ? "pageX" : "pageY"] - i) / t.kx, 10); r[h](c + o) }), o._handleMouseDown(function () { t.scroll.removeClass("scroll-draggable"), f = r[h]() }, s) })) }), l.each(a, function (l, e) { var o = "scroll-scroll" + l + "_visible", s = "x" == l ? a.y : a.x; e.scroll.removeClass(o), s.scroll.removeClass(o), i.removeClass(o) }), l.each(a, function (e, o) { l.extend(o, "x" == e ? { offset: parseInt(r.css("left"), 10) || 0, size: r.prop("scrollWidth"), visible: d.width() } : { offset: parseInt(r.css("top"), 10) || 0, size: r.prop("scrollHeight"), visible: d.height() }) }), this._updateScroll("x", this.scrollx), this._updateScroll("y", this.scrolly), l.isFunction(c.onUpdate) && c.onUpdate.apply(this, [r]), l.each(a, function (l, e) { var o = "x" === l ? "left" : "top", s = "x" === l ? "outerWidth" : "outerHeight", t = "x" === l ? "width" : "height", i = parseInt(r.css(o), 10) || 0, n = e.size, a = e.visible + i, d = e.scroll.size[s]() + (parseInt(e.scroll.size.css(o), 10) || 0); c.autoScrollSize && (e.scrollbarSize = parseInt(d * a / n, 10), e.scroll.bar.css(t, e.scrollbarSize + "px")), e.scrollbarSize = e.scroll.bar[s](), e.kx = (d - e.scrollbarSize) / (n - a) || 1, e.maxScrollOffset = n - a }), r.scrollLeft(h.scrollLeft).scrollTop(h.scrollTop).trigger("scroll") }, _getScroll: function (e) { var o = { advanced: ['<div class="scroll-element">', '<div class="scroll-element_corner"></div>', '<div class="scroll-arrow scroll-arrow_less"></div>', '<div class="scroll-arrow scroll-arrow_more"></div>', '<div class="scroll-element_outer">', '<div class="scroll-element_size"></div>', '<div class="scroll-element_inner-wrapper">', '<div class="scroll-element_inner scroll-element_track">', '<div class="scroll-element_inner-bottom"></div>', "</div>", "</div>", '<div class="scroll-bar">', '<div class="scroll-bar_body">', '<div class="scroll-bar_body-inner"></div>', "</div>", '<div class="scroll-bar_bottom"></div>', '<div class="scroll-bar_center"></div>', "</div>", "</div>", "</div>"].join(""), simple: ['<div class="scroll-element">', '<div class="scroll-element_outer">', '<div class="scroll-element_size"></div>', '<div class="scroll-element_track"></div>', '<div class="scroll-bar"></div>', "</div>", "</div>"].join("") }; return o[e] && (e = o[e]), e || (e = o.simple), e = "string" == typeof e ? l(e).appendTo(this.wrapper) : l(e), l.extend(e, { bar: e.find(".scroll-bar"), size: e.find(".scroll-element_size"), track: e.find(".scroll-element_track") }), e }, _handleMouseDown: function (e, o) { var s = this.namespace; return l(document).on("blur" + s, function () { l(document).add("body").off(s), e && e() }), l(document).on("dragstart" + s, function (l) { return l.preventDefault(), !1 }), l(document).on("mouseup" + s, function () { l(document).add("body").off(s), e && e() }), l("body").on("selectstart" + s, function (l) { return l.preventDefault(), !1 }), o && o.preventDefault(), !1 }, _updateScroll: function (e, o) { var s = this.container, r = this.containerWrapper || s, i = "scroll-scroll" + e + "_visible", n = "x" === e ? this.scrolly : this.scrollx, c = parseInt(this.container.css("x" === e ? "left" : "top"), 10) || 0, a = this.wrapper, d = o.size, h = o.visible + c; o.isVisible = d - h > 1, o.isVisible ? (o.scroll.addClass(i), n.scroll.addClass(i), r.addClass(i)) : (o.scroll.removeClass(i), n.scroll.removeClass(i), r.removeClass(i)), "y" === e && r.css(s.is("textarea") || h > d ? { height: h + t.scroll.height + "px", "max-height": "none" } : { "max-height": h + t.scroll.height + "px" }), (o.size != s.prop("scrollWidth") || n.size != s.prop("scrollHeight") || o.visible != a.width() || n.visible != a.height() || o.offset != (parseInt(s.css("left"), 10) || 0) || n.offset != (parseInt(s.css("top"), 10) || 0)) && (l.extend(this.scrollx, { offset: parseInt(s.css("left"), 10) || 0, size: s.prop("scrollWidth"), visible: a.width() }), l.extend(this.scrolly, { offset: parseInt(s.css("top"), 10) || 0, size: this.container.prop("scrollHeight"), visible: a.height() }), this._updateScroll("x" === e ? "y" : "x", n)) } }; var c = n; l.fn.scrollbar = function (e, o) { return "string" != typeof e && (o = e, e = "init"), "undefined" == typeof o && (o = []), l.isArray(o) || (o = [o]), this.not("body, .scroll-wrapper").each(function () { var s = l(this), r = s.data(t.data.name); (r || "init" === e) && (r || (r = new c(s)), r[e] && r[e].apply(r, o)) }), this }, l.fn.scrollbar.options = i; var a = function () { var l = 0, e = 0; return function (o) { var s, i, n, c, d, h, p; for (s = 0; s < t.scrolls.length; s++)c = t.scrolls[s], i = c.container, n = c.options, d = c.wrapper, h = c.scrollx, p = c.scrolly, (o || n.autoUpdate && d && d.is(":visible") && (i.prop("scrollWidth") != h.size || i.prop("scrollHeight") != p.size || d.width() != h.visible || d.height() != p.visible)) && (c.init(), n.debug && (window.console && console.log({ scrollHeight: i.prop("scrollHeight") + ":" + c.scrolly.size, scrollWidth: i.prop("scrollWidth") + ":" + c.scrollx.size, visibleHeight: d.height() + ":" + c.scrolly.visible, visibleWidth: d.width() + ":" + c.scrollx.visible }, !0), e++)); r && e > 10 ? (window.console && console.log("Scroll updates exceed 10"), a = function () { }) : (clearTimeout(l), l = setTimeout(a, 300)) } }(); window.angular && !function (l) { l.module("jQueryScrollbar", []).provider("jQueryScrollbar", function () { var e = i; return { setOptions: function (o) { l.extend(e, o) }, $get: function () { return { options: l.copy(e) } } } }).directive("jqueryScrollbar", function (l, e) { return { restrict: "AC", link: function (o, s, r) { var t = e(r.jqueryScrollbar), i = t(o); s.scrollbar(i || l.options).on("$destroy", function () { s.scrollbar("destroy") }) } } }) }(window.angular) });

/*!
 * parallax.js v1.4.2 (http://pixelcog.github.io/parallax.js/)
 * @copyright 2016 PixelCog, Inc.
 * @license MIT (https://github.com/pixelcog/parallax.js/blob/master/LICENSE)
 */
!function (t, i, e, s) { function o(i, e) { var h = this; "object" == typeof e && (delete e.refresh, delete e.render, t.extend(this, e)), this.$element = t(i), !this.imageSrc && this.$element.is("img") && (this.imageSrc = this.$element.attr("src")); var r = (this.position + "").toLowerCase().match(/\S+/g) || []; if (r.length < 1 && r.push("center"), 1 == r.length && r.push(r[0]), ("top" == r[0] || "bottom" == r[0] || "left" == r[1] || "right" == r[1]) && (r = [r[1], r[0]]), this.positionX != s && (r[0] = this.positionX.toLowerCase()), this.positionY != s && (r[1] = this.positionY.toLowerCase()), h.positionX = r[0], h.positionY = r[1], "left" != this.positionX && "right" != this.positionX && (this.positionX = isNaN(parseInt(this.positionX)) ? "center" : parseInt(this.positionX)), "top" != this.positionY && "bottom" != this.positionY && (this.positionY = isNaN(parseInt(this.positionY)) ? "center" : parseInt(this.positionY)), this.position = this.positionX + (isNaN(this.positionX) ? "" : "px") + " " + this.positionY + (isNaN(this.positionY) ? "" : "px"), navigator.userAgent.match(/(iPod|iPhone|iPad)/)) return this.imageSrc && this.iosFix && !this.$element.is("img") && this.$element.css({ backgroundImage: "url(" + this.imageSrc + ")", backgroundSize: "cover", backgroundPosition: this.position }), this; if (navigator.userAgent.match(/(Android)/)) return this.imageSrc && this.androidFix && !this.$element.is("img") && this.$element.css({ backgroundImage: "url(" + this.imageSrc + ")", backgroundSize: "cover", backgroundPosition: this.position }), this; this.$mirror = t("<div />").prependTo("body"); var a = this.$element.find(">.parallax-slider"), n = !1; 0 == a.length ? this.$slider = t("<img />").prependTo(this.$mirror) : (this.$slider = a.prependTo(this.$mirror), n = !0), this.$mirror.addClass("parallax-mirror").css({ visibility: "hidden", zIndex: this.zIndex, position: "fixed", top: 0, left: 0, overflow: "hidden" }), this.$slider.addClass("parallax-slider").one("load", function () { h.naturalHeight && h.naturalWidth || (h.naturalHeight = this.naturalHeight || this.height || 1, h.naturalWidth = this.naturalWidth || this.width || 1), h.aspectRatio = h.naturalWidth / h.naturalHeight, o.isSetup || o.setup(), o.sliders.push(h), o.isFresh = !1, o.requestRender() }), n || (this.$slider[0].src = this.imageSrc), (this.naturalHeight && this.naturalWidth || this.$slider[0].complete || a.length > 0) && this.$slider.trigger("load") } function h(s) { return this.each(function () { var h = t(this), r = "object" == typeof s && s; this == i || this == e || h.is("body") ? o.configure(r) : h.data("px.parallax") ? "object" == typeof s && t.extend(h.data("px.parallax"), r) : (r = t.extend({}, h.data(), r), h.data("px.parallax", new o(this, r))), "string" == typeof s && ("destroy" == s ? o.destroy(this) : o[s]()) }) } !function () { for (var t = 0, e = ["ms", "moz", "webkit", "o"], s = 0; s < e.length && !i.requestAnimationFrame; ++s)i.requestAnimationFrame = i[e[s] + "RequestAnimationFrame"], i.cancelAnimationFrame = i[e[s] + "CancelAnimationFrame"] || i[e[s] + "CancelRequestAnimationFrame"]; i.requestAnimationFrame || (i.requestAnimationFrame = function (e) { var s = (new Date).getTime(), o = Math.max(0, 16 - (s - t)), h = i.setTimeout(function () { e(s + o) }, o); return t = s + o, h }), i.cancelAnimationFrame || (i.cancelAnimationFrame = function (t) { clearTimeout(t) }) }(), t.extend(o.prototype, { speed: .2, bleed: 0, zIndex: -100, iosFix: !0, androidFix: !0, position: "center", overScrollFix: !1, refresh: function () { this.boxWidth = this.$element.outerWidth(), this.boxHeight = this.$element.outerHeight() + 2 * this.bleed, this.boxOffsetTop = this.$element.offset().top - this.bleed, this.boxOffsetLeft = this.$element.offset().left, this.boxOffsetBottom = this.boxOffsetTop + this.boxHeight; var t = o.winHeight, i = o.docHeight, e = Math.min(this.boxOffsetTop, i - t), s = Math.max(this.boxOffsetTop + this.boxHeight - t, 0), h = this.boxHeight + (e - s) * (1 - this.speed) | 0, r = (this.boxOffsetTop - e) * (1 - this.speed) | 0; if (h * this.aspectRatio >= this.boxWidth) { this.imageWidth = h * this.aspectRatio | 0, this.imageHeight = h, this.offsetBaseTop = r; var a = this.imageWidth - this.boxWidth; this.offsetLeft = "left" == this.positionX ? 0 : "right" == this.positionX ? -a : isNaN(this.positionX) ? -a / 2 | 0 : Math.max(this.positionX, -a) } else { this.imageWidth = this.boxWidth, this.imageHeight = this.boxWidth / this.aspectRatio | 0, this.offsetLeft = 0; var a = this.imageHeight - h; this.offsetBaseTop = "top" == this.positionY ? r : "bottom" == this.positionY ? r - a : isNaN(this.positionY) ? r - a / 2 | 0 : r + Math.max(this.positionY, -a) } }, render: function () { var t = o.scrollTop, i = o.scrollLeft, e = this.overScrollFix ? o.overScroll : 0, s = t + o.winHeight; this.boxOffsetBottom > t && this.boxOffsetTop <= s ? (this.visibility = "visible", this.mirrorTop = this.boxOffsetTop - t, this.mirrorLeft = this.boxOffsetLeft - i, this.offsetTop = this.offsetBaseTop - this.mirrorTop * (1 - this.speed)) : this.visibility = "hidden", this.$mirror.css({ transform: "translate3d(0px, 0px, 0px)", visibility: this.visibility, top: this.mirrorTop - e, left: this.mirrorLeft, height: this.boxHeight, width: this.boxWidth }), this.$slider.css({ transform: "translate3d(0px, 0px, 0px)", position: "absolute", top: this.offsetTop, left: this.offsetLeft, height: this.imageHeight, width: this.imageWidth, maxWidth: "none" }) } }), t.extend(o, { scrollTop: 0, scrollLeft: 0, winHeight: 0, winWidth: 0, docHeight: 1 << 30, docWidth: 1 << 30, sliders: [], isReady: !1, isFresh: !1, isBusy: !1, setup: function () { if (!this.isReady) { var s = t(e), h = t(i), r = function () { o.winHeight = h.height(), o.winWidth = h.width(), o.docHeight = s.height(), o.docWidth = s.width() }, a = function () { var t = h.scrollTop(), i = o.docHeight - o.winHeight, e = o.docWidth - o.winWidth; o.scrollTop = Math.max(0, Math.min(i, t)), o.scrollLeft = Math.max(0, Math.min(e, h.scrollLeft())), o.overScroll = Math.max(t - i, Math.min(t, 0)) }; h.on("resize.px.parallax load.px.parallax", function () { r(), o.isFresh = !1, o.requestRender() }).on("scroll.px.parallax load.px.parallax", function () { a(), o.requestRender() }), r(), a(), this.isReady = !0 } }, configure: function (i) { "object" == typeof i && (delete i.refresh, delete i.render, t.extend(this.prototype, i)) }, refresh: function () { t.each(this.sliders, function () { this.refresh() }), this.isFresh = !0 }, render: function () { this.isFresh || this.refresh(), t.each(this.sliders, function () { this.render() }) }, requestRender: function () { var t = this; this.isBusy || (this.isBusy = !0, i.requestAnimationFrame(function () { t.render(), t.isBusy = !1 })) }, destroy: function (e) { var s, h = t(e).data("px.parallax"); for (h.$mirror.remove(), s = 0; s < this.sliders.length; s += 1)this.sliders[s] == h && this.sliders.splice(s, 1); t(e).data("px.parallax", !1), 0 === this.sliders.length && (t(i).off("scroll.px.parallax resize.px.parallax load.px.parallax"), this.isReady = !1, o.isSetup = !1) } }); var r = t.fn.parallax; t.fn.parallax = h, t.fn.parallax.Constructor = o, t.fn.parallax.noConflict = function () { return t.fn.parallax = r, this }, t(e).on("ready.px.parallax.data-api", function () { t('[data-parallax="scroll"]').parallax() }) }(jQuery, window, document);


if ($('body').hasClass('mobileapp')) {
	$('body').on("click", "a", function () {
		let ahref = $(this).attr('href');
		if (
			$(this).hasClass('i_cart')
			|| $(this).is("[data-link]")
			|| $(this).is("[data-rel]")
			|| !ahref
			|| ahref === ""
			|| ahref == "undefined") {
			// нихуя
		} else {
			if (ahref.indexOf('#') == -1) {
				$('body').addClass('mobilepreload');
				setTimeout(function () { $('body').removeClass('mobilepreload'); }, 1500);
			}
			console.log('href:' + ahref);
		}

	});

	if ($('body').width() > 900) {
		$("meta[name='viewport']").attr("content", "width=100%,initial-scale=1.4,maximum-scale=1.4,user-scalable=0");

	}
}


/*
 *   MASONRY
 */
(function ($) {

	var global = {
		defaults: {
			column: 6,
			gutter: '10px',
			itemHeight: '100',
			itemSelector: '>*',
		},
		options: {
			gridWidth: null,
			gridHeight: null,
			gridGutter: null,
			gridItemWidth: null,
			gridItemHeight: null,
			gridMap: [],
			breakpoints: [],
			rangeValues: [],
			currentbreakpoint: {},
			resizeDelay: 180,
			resizeTimeout: null,
		},
		functions: {
			isPx: function (value) {
				value = value.toString().toLowerCase();
				if (~value.indexOf('px')) {
					return true;
				}
				return false;
			},
			isPercent: function (value) {
				value = value.toString().toLowerCase();
				if (~value.indexOf('%')) {
					return true;
				}
				return false;
			},
			getPxValue: function (value, size) {
				size_ = parseInt(value);
				if (isNaN(size_)) {
					size_ = 0;
				}
				if (global.functions.isPercent(value)) {
					return Math.floor(size * size_ / 100);
				}
				if (global.functions.isPx(value)) {
					return Math.floor(size_);
				}
				return 0;
			},
		}
	};

	Grid = function (block, settings) {
		this.options = {};
		this.options.grid = $(block);
		this.init(settings);
	};

	Grid.prototype = {
		init: function (settings) {
			var self = this;
			$.extend(true, this.options, global.options);
			$.extend(true, this.options, global.defaults);
			$.extend(true, this.options, settings);
			$.each(this.options.breakpoints, function (key, breakpoint) {
				breakpoint.condition = self.parseBreakpoint(key, breakpoint.range);
			});
			this.resize();
		},
		resize: function () {
			var self = this;
			if (screenSize == "mobile" && this.options.grid.hasClass('subdivision-items')) {
				if (this.options.grid.attr("style")) {
					this.options.grid.attr('style', '').find("> *").attr('style', '');
				}
			} else if (this.options.column == 1) {
				this.options.grid.attr('style', "").find("> *").attr('style', "width:100%").removeClass("rowspan colspan");
			} else {
				this.options.resizeTimeout = null;
				$.each(this.options.breakpoints, function (key, breakpoint) {
					if ((breakpoint.condition)(window.innerWidth)) {
						if (breakpoint.range != self.options.currentbreakpoint.range) {
							self.options.currentbreakpoint = breakpoint;
							$.extend(true, self.options, breakpoint.options);
						}
					}
				});
				this.options.grid.css({
					'position': 'relative'
				});
				this.options.gridWidth = this.options.grid.width();
				this.options.gridGutter = global.functions.getPxValue(this.options.gutter, this.options.gridWidth);
				this.options.gridItemWidth = Math.floor((this.options.gridWidth - (this.options.column - 1) * this.options.gridGutter) / this.options.column);
				this.options.gridItemHeight = this.options.grid.children(this.options.itemSelector).height();

				w1 = this.options.grid.find(':first-child').width()
				h1 = this.options.grid.find(':first-child').height()
				this.options.gridProcent = (100 / w1) * h1;

				this.initMap();
				this.calculateMap();
				this.renderGrid();
			}
		},
		calculateMap: function () {
			var self = this;
			this.options.grid.children(this.options.itemSelector).css({
				'position': 'absolute',
				'overflow': 'hidden'
			}).each(function (k) {
				if (self.masonryGrids(k, 'colspan')) $(this).addClass('colspan');
				if (self.masonryGrids(k, 'rowspan')) $(this).addClass('rowspan');

				var colspan = $(this).hasClass('colspan') ? 2 : 1;
				var rowspan = $(this).hasClass('rowspan') ? 2 : 1;
				colspan = Math.min(colspan, self.options.column);
				var added = false;
				var i, j;
				for (i = 0; i < self.options.gridMap.length; ++i) {
					for (j = 0; j < self.options.gridMap[i].length; ++j) {
						if (self.isFreeMap(i, j, colspan, rowspan)) {
							self.addBlockToMap(i, j, colspan, rowspan, {
								'block': this,
								'colspan': colspan,
								'rowspan': rowspan
							});
							added = true;
							break;
						}
					}
					if (added) {
						break;
					}
				}
			});
		},
		masonryGrids: function (i, type) {
			if (this.options.grid.hasClass('masonry-1')) {
				i = i % 8;
				data = {
					1: { rowspan: 1 },
					2: { colspan: 1 },
					5: { rowspan: 1 },
					6: { colspan: 1 }
				};
				if (typeof data[i] !== "undefined" && typeof data[i][type] !== "undefined") return true;
			}
			if (this.options.grid.hasClass('masonry-2')) {
				i = i % 23;
				data = {
					0: { rowspan: 1, colspan: 1 },
					5: { rowspan: 1 },
					7: { rowspan: 1 },
					8: { rowspan: 1, colspan: 1 },
					10: { rowspan: 1 },
					12: { colspan: 1 },
					13: { rowspan: 1, colspan: 1 },
					16: { rowspan: 1, colspan: 1 },
					19: { rowspan: 1 },
				};
				if (typeof data[i] !== "undefined" && typeof data[i][type] !== "undefined") return true;
			}
			if (this.options.grid.hasClass('masonry-3')) {
				i = i % 8;
				data = {
					1: { rowspan: 1 },
					2: { colspan: 1 },
					6: { colspan: 1 }
				};
				if (typeof data[i] !== "undefined" && typeof data[i][type] !== "undefined") return true;
			}
			return false;
		},
		renderGrid: function () {
			this.removeEmptyRows();
			this.options.grid.css({
				'height': this.calculateItemHeight(this.options.gridMap.length),
			});
			if (document.body.clientWidth < this.options.gridWidth) {
				this.resize();
			}
			var i, j;
			for (i = 0; i < this.options.gridMap.length; ++i) {
				for (j = 0; j < this.options.gridMap[i].length; ++j) {
					if (typeof (this.options.gridMap[i][j]) == 'object') {
						$(this.options.gridMap[i][j].block).css({
							'top': this.calculateItemTop(i),
							'left': this.calculateItemLeft(j),
							'width': this.calculateItemWidth(this.options.gridMap[i][j].colspan),
							'height': this.calculateItemHeight(this.options.gridMap[i][j].rowspan),
						});
					}
				}
			}
		},
		initMap: function () {
			var length = 0;
			this.options.grid.children(this.options.itemSelector).each(function () {
				length += $(this).data('rowspan') || 1;
			});
			this.options.gridMap = new Array(length);
			var i;
			for (i = 0; i < this.options.gridMap.length; ++i) {
				this.options.gridMap[i] = new Array(this.options.column);
			}
		},
		removeEmptyRows: function () {
			var i, j;
			var isFree = null;
			for (i = 0; i < this.options.gridMap.length; ++i) {
				isFree = true;
				for (j = 0; j < this.options.gridMap[i].length; ++j) {
					if (this.options.gridMap[i][j] !== undefined) {
						isFree = false;
						break;
					}
				}
				if (isFree) {
					break;
				}
			}
			if (isFree) {
				var length = this.options.gridMap.length - 1;
				for (var k = length; k >= i; --k) {
					this.options.gridMap.pop();
				}
			}
		},
		isFreeMap: function (i_, j_, colspan, rowspan) {
			var isFree = true;
			var i, j;
			if (colspan > this.options.column - j_) {
				isFree = false;
			} else {
				for (i = i_; i < i_ + rowspan; ++i) {
					for (j = j_; j < j_ + colspan; ++j) {
						if (this.options.gridMap[i][j] !== undefined) {
							isFree = false;
							break;
						}
					}
					if (!isFree) {
						break;
					}
				}
			}
			return isFree;
		},
		addBlockToMap: function (i_, j_, colspan, rowspan, object) {
			this.options.gridMap[i_][j_] = object;
			var i, j;
			for (i = i_; i < i_ + rowspan; ++i) {
				for (j = j_; j < j_ + colspan; ++j) {
					if (i != i_ || j != j_) {
						this.options.gridMap[i][j] = 0;
					}
				}
			}
		},
		calculateItemWidth: function (colspan) {
			return this.options.gridItemWidth * colspan + this.options.gridGutter * (colspan - 1);
		},
		calculateItemHeight: function (rowspan) {
			r = (this.options.gridItemWidth / 100) * this.options.itemHeight;
			return r * rowspan + this.options.gridGutter * (rowspan - 1);
		},
		calculateItemTop: function (row) {
			return (row === 0) ? 0 : this.calculateItemHeight(row) + this.options.gridGutter;
		},
		calculateItemLeft: function (col) {
			return (col === 0) ? 0 : this.calculateItemWidth(col) + this.options.gridGutter;
		},
		parseBreakpoint: function (key, range) {
			var self = this;
			if (typeof range != 'string')
				condition = function (v) { return false; };
			if (range == '*')
				condition = function (v) { return true; };
			else if (range.charAt(0) == '-') {
				this.options.rangeValues[key] = parseInt(range.substring(1));
				condition = function (v) { return (v <= self.options.rangeValues[key]); };
			}
			else if (range.charAt(range.length - 1) == '-') {
				this.options.rangeValues[key] = parseInt(range.substring(0, range.length - 1));
				condition = function (v) { return (v >= self.options.rangeValues[key]); };
			}
			else if (~range.indexOf(range, '-')) {
				range = range.split('-');
				this.options.rangeValues[key] = [parseInt(range[0]), parseInt(range[1])];
				condition = function (v) { return (v >= self.options.rangeValues[key][0] && v <= self.options.rangeValues[key][1]); };
			}
			else {
				this.options.rangeValues[key] = parseInt(range);
				condition = function (v) { return (v == self.options.rangeValues[key]); };
			}
			return condition;
		},
	};

	$.fn.responsivegrid = function (settings) {
		if (typeof settings === 'object') {
			this.each(function () {
				$(this).data('column', settings.column);
				new Grid(this, settings);
			});
		}
		return this;
	};

})(jQuery);


/*******************
 *  Mask  *
 ******************/
(function (factory, jQuery, Zepto) {

	if (typeof define === 'function' && define.amd) {
		define(['jquery'], factory);
	} else if (typeof exports === 'object') {
		module.exports = factory(require('jquery'));
	} else {
		factory(jQuery || Zepto);
	}

}(function ($) {
	'use strict';

	var Mask = function (el, mask, options) {

		var p = {
			invalid: [],
			getCaret: function () {
				try {
					var sel,
						pos = 0,
						ctrl = el.get(0),
						dSel = document.selection,
						cSelStart = ctrl.selectionStart;

					// IE Support
					if (dSel && navigator.appVersion.indexOf('MSIE 10') === -1) {
						sel = dSel.createRange();
						sel.moveStart('character', -p.val().length);
						pos = sel.text.length;
					}
					// Firefox support
					else if (cSelStart || cSelStart === '0') {
						pos = cSelStart;
					}

					return pos;
				} catch (e) { }
			},
			setCaret: function (pos) {
				try {
					if (el.is(':focus')) {
						var range, ctrl = el.get(0);

						// Firefox, WebKit, etc..
						if (ctrl.setSelectionRange) {
							ctrl.setSelectionRange(pos, pos);
						} else { // IE
							range = ctrl.createTextRange();
							range.collapse(true);
							range.moveEnd('character', pos);
							range.moveStart('character', pos);
							range.select();
						}
					}
				} catch (e) { }
			},
			events: function () {
				el
					.on('keydown.mask', function (e) {
						el.data('mask-keycode', e.keyCode || e.which);
						el.data('mask-previus-value', el.val());
						el.data('mask-previus-caret-pos', p.getCaret());
						p.maskDigitPosMapOld = p.maskDigitPosMap;
					})
					.on($.jMaskGlobals.useInput ? 'input.mask' : 'keyup.mask', p.behaviour)
					.on('paste.mask drop.mask', function () {
						setTimeout(function () {
							el.keydown().keyup();
						}, 100);
					})
					.on('change.mask', function () {
						el.data('changed', true);
					})
					.on('blur.mask', function () {
						if (oldValue !== p.val() && !el.data('changed')) {
							el.trigger('change');
						}
						el.data('changed', false);
					})
					// it's very important that this callback remains in this position
					// otherwhise oldValue it's going to work buggy
					.on('blur.mask', function () {
						oldValue = p.val();
					})
					// select all text on focus
					.on('focus.mask', function (e) {
						if (options.selectOnFocus === true) {
							$(e.target).select();
						}
					})
					// clear the value if it not complete the mask
					.on('focusout.mask', function () {
						if (options.clearIfNotMatch && !regexMask.test(p.val())) {
							p.val('');
						}
					});
			},
			getRegexMask: function () {
				var maskChunks = [], translation, pattern, optional, recursive, oRecursive, r;

				for (var i = 0; i < mask.length; i++) {
					translation = jMask.translation[mask.charAt(i)];

					if (translation) {

						pattern = translation.pattern.toString().replace(/.{1}$|^.{1}/g, '');
						optional = translation.optional;
						recursive = translation.recursive;

						if (recursive) {
							maskChunks.push(mask.charAt(i));
							oRecursive = { digit: mask.charAt(i), pattern: pattern };
						} else {
							maskChunks.push(!optional && !recursive ? pattern : (pattern + '?'));
						}

					} else {
						maskChunks.push(mask.charAt(i).replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'));
					}
				}

				r = maskChunks.join('');

				if (oRecursive) {
					r = r.replace(new RegExp('(' + oRecursive.digit + '(.*' + oRecursive.digit + ')?)'), '($1)?')
						.replace(new RegExp(oRecursive.digit, 'g'), oRecursive.pattern);
				}

				return new RegExp(r);
			},
			destroyEvents: function () {
				el.off(['input', 'keydown', 'keyup', 'paste', 'drop', 'blur', 'focusout', ''].join('.mask '));
			},
			val: function (v) {
				var isInput = el.is('input'),
					method = isInput ? 'val' : 'text',
					r;

				if (arguments.length > 0) {
					if (el[method]() !== v) {
						el[method](v);
					}
					r = el;
				} else {
					r = el[method]();
				}

				return r;
			},
			calculateCaretPosition: function () {
				var oldVal = el.data('mask-previus-value') || '',
					newVal = p.getMasked(),
					caretPosNew = p.getCaret();
				if (oldVal !== newVal) {
					var caretPosOld = el.data('mask-previus-caret-pos') || 0,
						newValL = newVal.length,
						oldValL = oldVal.length,
						maskDigitsBeforeCaret = 0,
						maskDigitsAfterCaret = 0,
						maskDigitsBeforeCaretAll = 0,
						maskDigitsBeforeCaretAllOld = 0,
						i = 0;

					for (i = caretPosNew; i < newValL; i++) {
						if (!p.maskDigitPosMap[i]) {
							break;
						}
						maskDigitsAfterCaret++;
					}

					for (i = caretPosNew - 1; i >= 0; i--) {
						if (!p.maskDigitPosMap[i]) {
							break;
						}
						maskDigitsBeforeCaret++;
					}

					for (i = caretPosNew - 1; i >= 0; i--) {
						if (p.maskDigitPosMap[i]) {
							maskDigitsBeforeCaretAll++;
						}
					}

					for (i = caretPosOld - 1; i >= 0; i--) {
						if (p.maskDigitPosMapOld[i]) {
							maskDigitsBeforeCaretAllOld++;
						}
					}

					// if the cursor is at the end keep it there
					if (caretPosNew > oldValL) {
						caretPosNew = newValL * 10;
					} else if (caretPosOld >= caretPosNew && caretPosOld !== oldValL) {
						if (!p.maskDigitPosMapOld[caretPosNew]) {
							var caretPos = caretPosNew;
							caretPosNew -= maskDigitsBeforeCaretAllOld - maskDigitsBeforeCaretAll;
							caretPosNew -= maskDigitsBeforeCaret;
							if (p.maskDigitPosMap[caretPosNew]) {
								caretPosNew = caretPos;
							}
						}
					}
					else if (caretPosNew > caretPosOld) {
						caretPosNew += maskDigitsBeforeCaretAll - maskDigitsBeforeCaretAllOld;
						caretPosNew += maskDigitsAfterCaret;
					}
				}
				return caretPosNew;
			},
			behaviour: function (e) {
				e = e || window.event;
				p.invalid = [];

				var keyCode = el.data('mask-keycode');

				if ($.inArray(keyCode, jMask.byPassKeys) === -1) {
					var newVal = p.getMasked(),
						caretPos = p.getCaret();

					// this is a compensation to devices/browsers that don't compensate
					// caret positioning the right way
					setTimeout(function () {
						p.setCaret(p.calculateCaretPosition());
					}, $.jMaskGlobals.keyStrokeCompensation);

					p.val(newVal);
					p.setCaret(caretPos);
					return p.callbacks(e);
				}
			},
			getMasked: function (skipMaskChars, val) {
				var buf = [],
					value = val === undefined ? p.val() : val + '',
					m = 0, maskLen = mask.length,
					v = 0, valLen = value.length,
					offset = 1, addMethod = 'push',
					resetPos = -1,
					maskDigitCount = 0,
					maskDigitPosArr = [],
					lastMaskChar,
					check;

				if (options.reverse) {
					addMethod = 'unshift';
					offset = -1;
					lastMaskChar = 0;
					m = maskLen - 1;
					v = valLen - 1;
					check = function () {
						return m > -1 && v > -1;
					};
				} else {
					lastMaskChar = maskLen - 1;
					check = function () {
						return m < maskLen && v < valLen;
					};
				}

				var lastUntranslatedMaskChar;
				while (check()) {
					var maskDigit = mask.charAt(m),
						valDigit = value.charAt(v),
						translation = jMask.translation[maskDigit];

					if (translation) {
						if (valDigit.match(translation.pattern)) {
							buf[addMethod](valDigit);
							if (translation.recursive) {
								if (resetPos === -1) {
									resetPos = m;
								} else if (m === lastMaskChar && m !== resetPos) {
									m = resetPos - offset;
								}

								if (lastMaskChar === resetPos) {
									m -= offset;
								}
							}
							m += offset;
						} else if (valDigit === lastUntranslatedMaskChar) {
							// matched the last untranslated (raw) mask character that we encountered
							// likely an insert offset the mask character from the last entry; fall
							// through and only increment v
							maskDigitCount--;
							lastUntranslatedMaskChar = undefined;
						} else if (translation.optional) {
							m += offset;
							v -= offset;
						} else if (translation.fallback) {
							buf[addMethod](translation.fallback);
							m += offset;
							v -= offset;
						} else {
							p.invalid.push({ p: v, v: valDigit, e: translation.pattern });
						}
						v += offset;
					} else {
						if (!skipMaskChars) {
							buf[addMethod](maskDigit);
						}

						if (valDigit === maskDigit) {
							maskDigitPosArr.push(v);
							v += offset;
						} else {
							lastUntranslatedMaskChar = maskDigit;
							maskDigitPosArr.push(v + maskDigitCount);
							maskDigitCount++;
						}

						m += offset;
					}
				}

				var lastMaskCharDigit = mask.charAt(lastMaskChar);
				if (maskLen === valLen + 1 && !jMask.translation[lastMaskCharDigit]) {
					buf.push(lastMaskCharDigit);
				}

				var newVal = buf.join('');
				p.mapMaskdigitPositions(newVal, maskDigitPosArr, valLen);
				return newVal;
			},
			mapMaskdigitPositions: function (newVal, maskDigitPosArr, valLen) {
				var maskDiff = options.reverse ? newVal.length - valLen : 0;
				p.maskDigitPosMap = {};
				for (var i = 0; i < maskDigitPosArr.length; i++) {
					p.maskDigitPosMap[maskDigitPosArr[i] + maskDiff] = 1;
				}
			},
			callbacks: function (e) {
				var val = p.val(),
					changed = val !== oldValue,
					defaultArgs = [val, e, el, options],
					callback = function (name, criteria, args) {
						if (typeof options[name] === 'function' && criteria) {
							options[name].apply(this, args);
						}
					};

				callback('onChange', changed === true, defaultArgs);
				callback('onKeyPress', changed === true, defaultArgs);
				callback('onComplete', val.length === mask.length, defaultArgs);
				callback('onInvalid', p.invalid.length > 0, [val, e, el, p.invalid, options]);
			}
		};

		el = $(el);
		var jMask = this, oldValue = p.val(), regexMask;

		mask = typeof mask === 'function' ? mask(p.val(), undefined, el, options) : mask;

		// public methods
		jMask.mask = mask;
		jMask.options = options;
		jMask.remove = function () {
			var caret = p.getCaret();
			if (jMask.options.placeholder) {
				el.removeAttr('placeholder');
			}
			if (el.data('mask-maxlength')) {
				el.removeAttr('maxlength');
			}
			p.destroyEvents();
			p.val(jMask.getCleanVal());
			p.setCaret(caret);
			return el;
		};

		// get value without mask
		jMask.getCleanVal = function () {
			return p.getMasked(true);
		};

		// get masked value without the value being in the input or element
		jMask.getMaskedVal = function (val) {
			return p.getMasked(false, val);
		};

		jMask.init = function (onlyMask) {
			onlyMask = onlyMask || false;
			options = options || {};

			jMask.clearIfNotMatch = $.jMaskGlobals.clearIfNotMatch;
			jMask.byPassKeys = $.jMaskGlobals.byPassKeys;
			jMask.translation = $.extend({}, $.jMaskGlobals.translation, options.translation);

			jMask = $.extend(true, {}, jMask, options);

			regexMask = p.getRegexMask();

			if (onlyMask) {
				p.events();
				p.val(p.getMasked());
			} else {
				if (options.placeholder) {
					el.attr('placeholder', options.placeholder);
				}

				// this is necessary, otherwise if the user submit the form
				// and then press the "back" button, the autocomplete will erase
				// the data. Works fine on IE9+, FF, Opera, Safari.
				if (el.data('mask')) {
					el.attr('autocomplete', 'off');
				}

				// detect if is necessary let the user type freely.
				// for is a lot faster than forEach.
				for (var i = 0, maxlength = true; i < mask.length; i++) {
					var translation = jMask.translation[mask.charAt(i)];
					if (translation && translation.recursive) {
						maxlength = false;
						break;
					}
				}

				if (maxlength) {
					el.attr('maxlength', mask.length).data('mask-maxlength', true);
				}

				p.destroyEvents();
				p.events();

				var caret = p.getCaret();
				p.val(p.getMasked());
				p.setCaret(caret);
			}
		};

		jMask.init(!el.is('input'));
	};

	$.maskWatchers = {};
	var HTMLAttributes = function () {
		var input = $(this),
			options = {},
			prefix = 'data-mask-',
			mask = input.attr('data-mask');

		if (input.attr(prefix + 'reverse')) {
			options.reverse = true;
		}

		if (input.attr(prefix + 'clearifnotmatch')) {
			options.clearIfNotMatch = true;
		}

		if (input.attr(prefix + 'selectonfocus') === 'true') {
			options.selectOnFocus = true;
		}

		if (notSameMaskObject(input, mask, options)) {
			return input.data('mask', new Mask(this, mask, options));
		}
	},
		notSameMaskObject = function (field, mask, options) {
			options = options || {};
			var maskObject = $(field).data('mask'),
				stringify = JSON.stringify,
				value = $(field).val() || $(field).text();
			try {
				if (typeof mask === 'function') {
					mask = mask(value);
				}
				return typeof maskObject !== 'object' || stringify(maskObject.options) !== stringify(options) || maskObject.mask !== mask;
			} catch (e) { }
		},
		eventSupported = function (eventName) {
			var el = document.createElement('div'), isSupported;

			eventName = 'on' + eventName;
			isSupported = (eventName in el);

			if (!isSupported) {
				el.setAttribute(eventName, 'return;');
				isSupported = typeof el[eventName] === 'function';
			}
			el = null;

			return isSupported;
		};

	$.fn.mask = function (mask, options) {
		options = options || {};
		var selector = this.selector,
			globals = $.jMaskGlobals,
			interval = globals.watchInterval,
			watchInputs = options.watchInputs || globals.watchInputs,
			maskFunction = function () {
				if (notSameMaskObject(this, mask, options)) {
					return $(this).data('mask', new Mask(this, mask, options));
				}
			};

		$(this).each(maskFunction);

		if (selector && selector !== '' && watchInputs) {
			clearInterval($.maskWatchers[selector]);
			$.maskWatchers[selector] = setInterval(function () {
				$(document).find(selector).each(maskFunction);
			}, interval);
		}
		return this;
	};

	$.fn.masked = function (val) {
		return this.data('mask').getMaskedVal(val);
	};

	$.fn.unmask = function () {
		clearInterval($.maskWatchers[this.selector]);
		delete $.maskWatchers[this.selector];
		return this.each(function () {
			var dataMask = $(this).data('mask');
			if (dataMask) {
				dataMask.remove().removeData('mask');
			}
		});
	};

	$.fn.cleanVal = function () {
		return this.data('mask').getCleanVal();
	};

	$.applyDataMask = function (selector) {
		selector = selector || $.jMaskGlobals.maskElements;
		var $selector = (selector instanceof $) ? selector : $(selector);
		$selector.filter($.jMaskGlobals.dataMaskAttr).each(HTMLAttributes);
	};

	var globals = {
		maskElements: 'input,td,span,div',
		dataMaskAttr: '*[data-mask]',
		dataMask: true,
		watchInterval: 300,
		watchInputs: true,
		keyStrokeCompensation: 10,
		// old versions of chrome dont work great with input event
		useInput: !/Chrome\/[2-4][0-9]|SamsungBrowser/.test(window.navigator.userAgent) && eventSupported('input'),
		watchDataMask: false,
		byPassKeys: [9, 16, 17, 18, 36, 37, 38, 39, 40, 91],
		translation: {
			'0': { pattern: /\d/ },
			'9': { pattern: /\d/, optional: true },
			'#': { pattern: /\d/, recursive: true },
			'A': { pattern: /[a-zA-Z0-9]/ },
			'S': { pattern: /[a-zA-Z]/ }
		}
	};

	$.jMaskGlobals = $.jMaskGlobals || {};
	globals = $.jMaskGlobals = $.extend(true, {}, globals, $.jMaskGlobals);

	// looking for inputs with data-mask attribute
	if (globals.dataMask) {
		$.applyDataMask();
	}

	setInterval(function () {
		if ($.jMaskGlobals.watchDataMask) {
			$.applyDataMask();
		}
	}, globals.watchInterval);
}, window.jQuery, window.Zepto));
window.mask_option = {
	'translation': {
		Z: { pattern: /[0-9]/ }
	},
	mask: function (cep) {
		var cep = cep.replace(/\+8/g, '+7').replace(/[^\+\d]+/g, "")
		return cep.indexOf("+7") == 0 || cep.indexOf("7") == 0 ? "+Z ZZZ ZZZ-ZZ-ZZ" : "+ZZZ ZZ ZZZ ZZ ZZ"
	},
	onKeyPress: function (cep, e, field, options) {
		var cep = cep.replace(/\+8/g, '+7')
		field.val(cep).mask(window.mask_option.mask.apply({}, arguments), window.mask_option);
	}
}

	/*
	 * Lightcase - jQuery Plugin
	 * The smart and flexible Lightbox Plugin.
	 */
	;
(function ($) {
	'use strict';
	var _self = {
		cache: {},
		support: {},
		objects: {},
		init: function (options) {
			return this.each(function () {
				$(this).unbind('click.lightcase').bind('click.lightcase', function (event) {
					event.preventDefault();
					$(this).lightcase('start', options);
				});
			});
		},
		start: function (options) {
			_self.origin = lightcase.origin = this;

			var kzParam = {};
			if (_self.origin.data) {
				for (var name in _self.origin.data()) {
					if (name == 'maxwidth') kzParam['maxWidth'] = _self.origin.data(name);
					if (name == 'maxheight') kzParam['maxHeight'] = _self.origin.data(name);
					if (name == 'groupclass') kzParam['groupClass'] = _self.origin.data(name);
					if (name == 'metr') kzParam['metr'] = _self.origin.data(name);
					if (name == 'type') kzParam['type'] = _self.origin.data(name);
				}
			}
			_self.settings = lightcase.settings = $.extend(true, {
				groupClass: 'standartModal',
				metr: '',
				idPrefix: 'lightcase-',
				classPrefix: 'lightcase-',
				attrPrefix: 'lc-',
				transition: 'elastic',
				transitionIn: null,
				transitionOut: null,
				cssTransitions: true,
				speedIn: 250,
				speedOut: 250,
				maxWidth: 1000,
				maxHeight: 850,
				forceWidth: false,
				forceHeight: false,
				liveResize: true,
				fullScreenModeForMobile: false,
				mobileMatchExpression: /(iphone|ipod|ipad|android|blackberry|symbian)/,
				disableShrink: false,
				shrinkFactor: .95,
				overlayOpacity: .7,
				slideshow: false,
				slideshowAutoStart: true,
				timeout: 5000,
				swipe: true,
				useKeys: true,
				useCategories: true,
				navigateEndless: true,
				closeOnOverlayClick: true,
				title: null,
				caption: null,
				showTitle: true,
				showCaption: true,
				showSequenceInfo: true,
				heightHeader: 0,
				fixHeight: 0,
				inline: {
					width: 'auto',
					height: 'auto'
				},
				ajax: {
					width: 'auto',
					height: 'auto',
					type: 'get',
					dataType: 'html',
					data: {}
				},
				iframe: {
					width: 1000,
					height: 500,
					frameborder: 0
				},
				flash: {
					width: 400,
					height: 205,
					wmode: 'transparent'
				},
				video: {
					width: 'auto',
					height: 'auto',
					poster: '',
					preload: 'auto',
					controls: true,
					autobuffer: true,
					autoplay: true,
					loop: false
				},
				html: {
					width: 'auto',
					height: 'auto'
				},
				attr: 'data-rel',
				href: null,
				type: null,
				reinit: 0,
				typeMapping: {
					'image': 'jpg,jpeg,gif,png,bmp,webp',
					'flash': 'swf',
					'video': 'mp4,mov,ogv,ogg,webm',
					'inline': '#'
				},
				errorMessage: function () {
					return '<p class="' + _self.settings.classPrefix + 'error">' + _self.settings.labels['errorMessage'] + '</p>';
				},
				labels: {
					'errorMessage': 'Ошибка загрузки данных',
					'sequenceInfo.of': ' из ',
					'close': 'Закрыть',
					'navigator.prev': 'Предыдущий',
					'navigator.next': 'Следующий',
					'navigator.play': 'Запуск',
					'navigator.pause': 'Пауза'
				},
				markup: function () {
					$('body').append(_self.objects.overlay = $('<div id="' + _self.settings.idPrefix + 'overlay"></div>'), _self.objects.loading = $('<div id="' + _self.settings.idPrefix + 'loading" class="' + _self.settings.classPrefix + 'icon-spin"></div>'), _self.objects.case = $('<div id="' + _self.settings.idPrefix + 'case" aria-hidden="true" role="dialog"></div>'));
					_self.objects.case.after(_self.objects.nav = $('<div id="' + _self.settings.idPrefix + 'nav"></div>'));
					_self.objects.nav.append(_self.objects.closemobile = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-close"></a>'), _self.objects.prev = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-prev"><span>' + _self.settings.labels['navigator.prev'] + '</span></a>').hide(), _self.objects.next = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-next"><span>' + _self.settings.labels['navigator.next'] + '</span></a>').hide(), _self.objects.play = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-play"><span>' + _self.settings.labels['navigator.play'] + '</span></a>').hide(), _self.objects.pause = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-pause"><span>' + _self.settings.labels['navigator.pause'] + '</span></a>').hide());
					_self.objects.case.append(_self.objects.info = $('<div id="' + _self.settings.idPrefix + 'info"></div>'), _self.objects.content = $('<div id="' + _self.settings.idPrefix + 'content"></div>'));
					_self.objects.case.append(_self.objects.sequenceInfo = $('<div id="' + _self.settings.idPrefix + 'sequenceInfo"></div>'));
					_self.objects.content.append(_self.objects.contentInner = $('<div class="' + _self.settings.classPrefix + 'contentInner"></div>'));
					_self.objects.info.append(_self.objects.title = $('<h4 id="' + _self.settings.idPrefix + 'title"></h4>'), _self.objects.caption = $('<p id="' + _self.settings.idPrefix + 'caption"></p>'), _self.objects.close = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-close"></a>'));
				},
				onInit: {},
				onStart: {},
				onFinish: {
					callAfter: (function () {
						if (typeof window.lightcase_after === "function") lightcase_after();
					}),
					scrollInit: (function () {
						var scrolls = _self.objects.case.find('.scrollbar-inner, scrollbar-outer');
						if (scrolls.length) scrolls.scrollbar();
					}),
					phoneMask: (function () {
						_self.objects.case.find(".input-field-standart input[name*='f_phone'], .callForm input[name*='phone'], .callForm input[name*='telefon'], .callForm input[name*='telephon'], form[data-metr*='genform'] input[name*='phone'], form[data-metr*='genform'] input[name*='telefon'], form[data-metr*='genform'] input[name*='telephon'], #order input[name*='phone'], #order input[name*='telefon'], #order input[name*='telephon'], input[name='AUTH_PHONE']").each(function () {
							var input = $(this);
							if (input.is('[type="hidden"]')) return;
							if (!input.is("[name*='][name]']")) input.mask(window.mask_option.mask(input.val()), window.mask_option);
						})
					}),
					dataLayer: (function () {
						yaDataLayer('detail', { from: 'lightcase' });
					}),
					h1: (function () {
						if ($('h1').length && _self.objects.case.find('input[name="f_subname"]').length) _self.objects.case.find('input[name="f_subname"]').val($('h1').text().trim());
					}),
					selectChange: (function () {
						_self.objects.case.find('select.select-style').on('change', function () {
							_self.resize();
						});
					}),
					finishCall: (function () {
						if (typeof window[_self.settings.onFinishCall] === 'function') window[_self.settings.onFinishCall](_self);
					}),
					resizeWithVideoContent: (function () {
						if (_self.objectData.type === 'video') {
							_self.objects.case.find('video').on('canplay', function () {
								setTimeout(function () {
									_self.resize();
								}, _self.settings.speedIn || 16);
							});
						}
					})
				},
				onClose: {},
				onCleanup: {
					callClose: (function () {
						if (typeof window.lightcase_afterClose === 'function') lightcase_afterClose(_self);
					})
				}
			}, options, _self.origin.data ? _self.origin.data('lc-options') : {}, kzParam);

			if (_self.settings.groupClass.indexOf("card-fast-prew") >= 0) {
				_self.settings.ajax.data["isNaked"] = 1;
				_self.settings.ajax.data["fastprew"] = 1;
			}

			_self._callHooks(_self.settings.onInit);
			_self.objectData = _self._setObjectData(this);
			_self._cacheScrollPosition();
			_self._watchScrollInteraction();
			_self._addElements();
			_self._open();
			_self.dimensions = _self.getViewportDimensions();
		},
		set: function (name, param) {
			_self.settings[name] = param;
		},
		get: function (name) {
			return _self.objects[name];
		},
		getObjectData: function () {
			return _self.objectData;
		},
		_setObjectData: function (object) {
			var $object = $(object),
				objectData = {
					title: _self.settings.title || $object.attr(_self._prefixAttributeName('title')) || $object.attr('title'),
					caption: _self.settings.caption || $object.attr(_self._prefixAttributeName('caption')) || $object.children('img').attr('alt'),
					url: _self._determineUrl(),
					requestType: _self.settings.ajax.type,
					requestData: _self.settings.ajax.data,
					requestDataType: _self.settings.ajax.dataType,
					rel: $object.attr(_self._determineAttributeSelector()),
					type: _self.settings.type || _self._verifyDataType(_self._determineUrl()),
					isPartOfSequence: _self._isPartOfSequence($object.attr(_self.settings.attr), ':'),
					isPartOfSequenceWithSlideshow: _self._isPartOfSequence($object.attr(_self.settings.attr), ':slideshow'),
					currentIndex: $(_self._determineAttributeSelector()).index($object),
					sequenceLength: $(_self._determineAttributeSelector()).length
				};
			if (objectData.type == 'image') {
				_self.settings.transition = "scrollHorizontal";
				_self.settings.shrinkFactor = .85;
			}
			objectData.sequenceInfo = (objectData.currentIndex + 1) + _self.settings.labels['sequenceInfo.of'] + objectData.sequenceLength;
			objectData.prevIndex = objectData.currentIndex - 1;
			objectData.nextIndex = objectData.currentIndex + 1;
			return objectData;
		},
		_prefixAttributeName: function (name) {
			return 'data-' + _self.settings.attrPrefix + name;
		},
		_determineLinkTarget: function () {
			return _self.settings.href || $(_self.origin).attr(_self._prefixAttributeName('href')) || $(_self.origin).attr('href');
		},
		_determineAttributeSelector: function () {
			var $origin = $(_self.origin),
				selector = '';
			if (typeof _self.cache.selector !== 'undefined') {
				selector = _self.cache.selector;
			} else if (_self.settings.useCategories === true && $origin.attr(_self._prefixAttributeName('categories'))) {
				var categories = $origin.attr(_self._prefixAttributeName('categories')).split(' ');
				$.each(categories, function (index, category) {
					if (index > 0) {
						selector += ',';
					}
					selector += '[' + _self._prefixAttributeName('categories') + '~="' + category + '"]';
				});
			} else {
				selector = '[' + _self.settings.attr + '="' + $origin.attr(_self.settings.attr) + '"]';
			}
			_self.cache.selector = selector;
			return selector;
		},
		_determineUrl: function () {
			var dataUrl = _self._verifyDataUrl(_self._determineLinkTarget()),
				width = 0,
				density = 0,
				url;
			if (['array', 'object'].indexOf(typeof dataUrl) > -1) {
				$.each(dataUrl, function (index, src) {
					if (_self._devicePixelRatio() >= src.density && src.density >= density && _self._matchMedia()('screen and (min-width:' + src.width + 'px)').matches && src.width >= width) {
						width = src.width;
						density = src.density;
						url = src.url;
					}
				});
			}
			return url;
		},
		_normalizeUrl: function (url) {
			var srcExp = /^\d+$/;
			return url.split(',').map(function (str) {
				var src = {
					width: 0,
					density: 0
				};
				str.trim().split(/\s+/).forEach(function (url, i) {
					if (i === 0) {
						return src.url = url;
					}
					var value = url.substring(0, url.length - 1),
						lastChar = url[url.length - 1],
						intVal = parseInt(value, 10),
						floatVal = parseFloat(value);
					if (lastChar === 'w' && srcExp.test(value)) {
						src.width = intVal;
					} else if (lastChar === 'h' && srcExp.test(value)) {
						src.height = intVal;
					} else if (lastChar === 'x' && !isNaN(floatVal)) {
						src.density = floatVal;
					}
				});
				return src;
			});
		},
		_isPartOfSequence: function (rel, expression) {
			var getSimilarLinks = $('[' + _self.settings.attr + '="' + rel + '"]'),
				regexp = new RegExp(expression);
			return (regexp.test(rel) && getSimilarLinks.length > 1);
		},
		isSlideshowEnabled: function () {
			return (_self.objectData.isPartOfSequence && (_self.settings.slideshow === true || _self.objectData.isPartOfSequenceWithSlideshow === true));
		},
		_loadContent: function () {
			if (_self.cache.originalObject) {
				_self._restoreObject();
			}
			_self._createObject();
		},
		_createObject: function () {
			var $object;
			switch (_self.objectData.type) {
				case 'image':
					$object = $(new Image());
					$object.attr({
						'src': _self.objectData.url,
						'alt': _self.objectData.title
					});
					break;
				case 'inline':
					$object = $('<div class="' + _self.settings.classPrefix + 'inlineWrap"></div>');
					$object.html(_self._cloneObject($(_self.objectData.url)));
					$.each(_self.settings.inline, function (name, value) {
						$object.attr(_self._prefixAttributeName(name), value);
					});
					break;
				case 'html':
					$object = $('<div class="' + _self.settings.classPrefix + 'inlineWrap"></div>').append(_self.settings.htmlContent);
					$.each(_self.settings.html, function (name, value) {
						$object.attr(_self._prefixAttributeName(name), value);
					});
					break;
				case 'ajax':
					$object = $('<div class="' + _self.settings.classPrefix + 'inlineWrap"></div>');
					$.each(_self.settings.ajax, function (name, value) {
						if (name !== 'data') {
							$object.attr(_self._prefixAttributeName(name), value);
						}
					});
					break;
				case 'flash':
					$object = $('<embed src="' + _self.objectData.url + '" type="application/x-shockwave-flash"></embed>');
					$.each(_self.settings.flash, function (name, value) {
						$object.attr(name, value);
					});
					break;
				case 'video':
					$object = $('<video></video>');
					$object.attr('src', _self.objectData.url);
					$.each(_self.settings.video, function (name, value) {
						$object.attr(name, value);
					});
					break;
				default:
					$object = $('<iframe></iframe>');
					$object.attr({
						'src': _self.objectData.url
					});
					$.each(_self.settings.iframe, function (name, value) {
						$object.attr(name, value);
					});
					break;
			}
			_self._addObject($object);
			_self._loadObject($object);
		},
		_addObject: function ($object) {
			_self.objects.contentInner.html($object);
			_self._loading('start');
			_self._callHooks(_self.settings.onStart);
			if (_self.settings.showSequenceInfo === true && _self.objectData.isPartOfSequence) {
				_self.objects.sequenceInfo.html(_self.objectData.sequenceInfo);
				_self.objects.sequenceInfo.show();
			} else {
				_self.objects.sequenceInfo.empty();
				_self.objects.sequenceInfo.hide();
			}
			if (_self.settings.showTitle === true && _self.objectData.title !== undefined && _self.objectData.title !== '') {
				_self.objects.title.html(_self.objectData.title);
				_self.objects.title.show();
			} else {
				_self.objects.title.empty();
				_self.objects.title.hide();
			}
			if (_self.settings.showCaption === true && _self.objectData.caption !== undefined && _self.objectData.caption !== '') {
				_self.objects.caption.html(_self.objectData.caption);
				_self.objects.caption.show();
			} else {
				_self.objects.caption.empty();
				_self.objects.caption.hide();
			}
			if (_self.objects.info.outerHeight() > 0) _self.settings.heightHeader = _self.objects.info.outerHeight();
		},
		_loadObject: function ($object) {
			switch (_self.objectData.type) {
				case 'inline':
					if ($(_self.objectData.url)) {
						_self._showContent($object);
					} else {
						_self.error();
					}
					break;
				case 'html':
					_self._showContent($object);
					break;
				case 'ajax':
					$.ajax($.extend({}, _self.settings.ajax, {
						url: _self.objectData.url,
						type: _self.objectData.requestType,
						dataType: _self.objectData.requestDataType,
						data: _self.objectData.requestData,
						success: function (data, textStatus, jqXHR) {
							if (_self.objectData.requestDataType === 'json') {
								_self.objectData.data = data;
							} else {
								$object.html(data);
							}
							_self._showContent($object);
						},
						error: function (jqXHR, textStatus, errorThrown) {
							_self.error();
						}
					}));
					break;
				case 'flash':
					_self._showContent($object);
					break;
				case 'video':
					if (typeof ($object.get(0).canPlayType) === 'function' || _self.objects.case.find('video').length === 0) {
						_self._showContent($object);
					} else {
						_self.error();
					}
					break;
				default:
					if (_self.objectData.url) {
						$object.on('load', function () {
							_self._showContent($object);
						});
						$object.on('error', function () {
							_self.error();
						});
					} else {
						_self.error();
					}
					break;
			}
		},
		error: function () {
			_self.objectData.type = 'error';
			var $object = $('<div class="' + _self.settings.classPrefix + 'inlineWrap"></div>');
			$object.html(_self.settings.errorMessage);
			_self.objects.contentInner.html($object);
			_self._showContent(_self.objects.contentInner);
		},
		_calculateDimensions: function ($object) {
			if (typeof $object !== 'object') return
			_self._cleanupDimensions();
			var dimensions = {
				objectWidth: $object.attr('width') ? $object.attr('width') : $object.attr(_self._prefixAttributeName('width')),
				objectHeight: $object.attr('height') ? parseInt($object.attr('height')) + 100 : $object.attr(_self._prefixAttributeName('height'))
			};
			if (!_self.settings.disableShrink) {
				dimensions.maxWidth = parseInt(_self.dimensions.windowWidth * _self.settings.shrinkFactor);
				dimensions.maxHeight = parseInt(_self.dimensions.windowHeight * _self.settings.shrinkFactor);
				if (dimensions.maxWidth > _self.settings.maxWidth) {
					dimensions.maxWidth = _self.settings.maxWidth;
				}
				if (dimensions.maxHeight > _self.settings.maxHeight) {
					dimensions.maxHeight = _self.settings.maxHeight;
				}
				dimensions.differenceWidthAsPercent = parseInt(100 / dimensions.maxWidth * dimensions.objectWidth);
				dimensions.differenceHeightAsPercent = parseInt(100 / dimensions.maxHeight * dimensions.objectHeight);
				switch (_self.objectData.type) {
					case 'image':
					case 'flash':
					case 'video':
						if (dimensions.differenceWidthAsPercent > 100 && dimensions.differenceWidthAsPercent > dimensions.differenceHeightAsPercent) {
							dimensions.objectWidth = dimensions.maxWidth;
							dimensions.objectHeight = parseInt(dimensions.objectHeight / dimensions.differenceWidthAsPercent * 100);
						}
						if (dimensions.differenceHeightAsPercent > 100 && dimensions.differenceHeightAsPercent > dimensions.differenceWidthAsPercent) {
							dimensions.objectWidth = parseInt(dimensions.objectWidth / dimensions.differenceHeightAsPercent * 100);
							dimensions.objectHeight = dimensions.maxHeight;
						}
						if (dimensions.differenceHeightAsPercent > 100 && dimensions.differenceWidthAsPercent < dimensions.differenceHeightAsPercent) {
							dimensions.objectWidth = parseInt(dimensions.maxWidth / dimensions.differenceHeightAsPercent * dimensions.differenceWidthAsPercent);
							dimensions.objectHeight = dimensions.maxHeight;
						}
						break;
					case 'error':
						if (!isNaN(dimensions.objectWidth) && dimensions.objectWidth > dimensions.maxWidth) {
							dimensions.objectWidth = dimensions.maxWidth;
						}
						break;
					default:
						if ((isNaN(dimensions.objectWidth) || dimensions.objectWidth > dimensions.maxWidth) && !_self.settings.forceWidth) {
							dimensions.objectWidth = dimensions.maxWidth;
						}
						if (((isNaN(dimensions.objectHeight) && dimensions.objectHeight !== 'auto') || dimensions.objectHeight > dimensions.maxHeight) && !_self.settings.forceHeight) {
							dimensions.objectHeight = dimensions.maxHeight;
						}
						break;
				}
			}
			if (_self.settings.forceWidth) {
				dimensions.maxWidth = dimensions.objectWidth;
			} else if ($object.attr(_self._prefixAttributeName('max-width'))) {
				dimensions.maxWidth = $object.attr(_self._prefixAttributeName('max-width'));
			}
			if (_self.settings.forceHeight) {
				dimensions.maxHeight = dimensions.objectHeight;
			} else if ($object.attr(_self._prefixAttributeName('max-height'))) {
				dimensions.maxHeight = $object.attr(_self._prefixAttributeName('max-height'));
			}
			if (_self.settings.fixHeight > 0) {
				dimensions.maxHeight = _self.settings.fixHeight;
				dimensions.objectHeight = _self.settings.fixHeight;
			}
			_self._adjustDimensions($object, dimensions);
		},
		_adjustDimensions: function ($object, dimensions) {
			$object.css({
				'width': dimensions.objectWidth,
				'height': dimensions.objectHeight,
				'max-width': dimensions.maxWidth,
				'max-height': dimensions.maxHeight - _self.settings.heightHeader
			});
			_self.objects.contentInner.css({
				'width': $object.outerWidth(),
				'height': $object.outerHeight(),
				'max-width': '100%'
			});
			_self.objects.case.css({
				'width': _self.objects.contentInner.outerWidth(),
				'height': dimensions.objectHeight,
				'max-height': dimensions.maxHeight
			});
			_self.objects.case.css({
				'margin-top': parseInt(-(_self.objects.case.outerHeight() / 2)),
				'margin-left': parseInt(-(_self.objects.case.outerWidth() / 2))
			});
		},
		_loading: function (process) {
			if (process === 'start') {
				_self.objects.case.addClass(_self.settings.classPrefix + 'loading');
				_self.objects.loading.show();
			} else if (process === 'end') {
				_self.objects.case.removeClass(_self.settings.classPrefix + 'loading');
				_self.objects.loading.hide();
			}
		},
		getViewportDimensions: function () {
			return {
				windowWidth: $(window).innerWidth(),
				windowHeight: $(window).innerHeight()
			};
		},
		_verifyDataUrl: function (dataUrl) {
			if (!dataUrl || dataUrl === undefined || dataUrl === '') {
				return false;
			}
			if (dataUrl.indexOf('#') > -1) {
				dataUrl = dataUrl.split('#');
				dataUrl = '#' + dataUrl[dataUrl.length - 1];
			}
			return _self._normalizeUrl(dataUrl.toString());
		},
		_verifyDataType: function (url) {
			var typeMapping = _self.settings.typeMapping;
			if (!url) {
				return false;
			}
			for (var key in typeMapping) {
				if (typeMapping.hasOwnProperty(key)) {
					var suffixArr = typeMapping[key].split(',');
					for (var i = 0; i < suffixArr.length; i++) {
						var suffix = suffixArr[i].toLowerCase(),
							regexp = new RegExp('\.(' + suffix + ')$', 'i'),
							str = url.toLowerCase().split('?')[0].substr(-5);
						if (regexp.test(str) === true || (key === 'inline' && (url.indexOf(suffix) > -1))) {
							return key;
						}
					}
				}
			}
			if (url.indexOf('//') !== -1) {
				return 'iframe';
			}
			return 'ajax';
		},
		_addElements: function () {
			if (typeof _self.objects.case !== 'undefined' && $('#' + _self.objects.case.attr('id')).length) {
				if (_self.objects.case.attr(_self._prefixAttributeName('type')) != 'image' && ['prev', 'next'].indexOf(_self.cache.action) === -1 && _self.isOpen) {
					_self.settings.reinit = 1;
					_self.close();
				}
				return;
			}
			_self.settings.markup();
		},
		_showContent: function ($object) {
			_self.objects.case.attr(_self._prefixAttributeName('type'), _self.objectData.type);
			_self.cache.object = $object;
			_self._calculateDimensions($object);
			_self._callHooks(_self.settings.onFinish);
			switch (_self.settings.transitionIn) {
				case 'scrollTop':
				case 'scrollRight':
				case 'scrollBottom':
				case 'scrollLeft':
				case 'scrollHorizontal':
				case 'scrollVertical':
					_self.transition.scroll(_self.objects.case, 'in', _self.settings.speedIn);
					_self.transition.fade(_self.objects.contentInner, 'in', _self.settings.speedIn);
					break;
				case 'elastic':
					if (_self.objects.case.css('opacity') < 1) {
						_self.transition.zoom(_self.objects.case, 'in', _self.settings.speedIn);
						_self.transition.fade(_self.objects.contentInner, 'in', _self.settings.speedIn);
					}
				case 'fade':
				case 'fadeInline':
					_self.transition.fade(_self.objects.case, 'in', _self.settings.speedIn);
					_self.transition.fade(_self.objects.contentInner, 'in', _self.settings.speedIn);
					break;
				default:
					_self.transition.fade(_self.objects.case, 'in', 0);
					break;
			}
			_self._loading('end');
			if (_self.objects.case.find('.tabs').length) _self.objects.case.find('.tabs').menuMaterial();
			_self.isBusy = false;
		},
		_processContent: function () {
			_self.isBusy = true;
			switch (_self.settings.transitionOut) {
				case 'scrollTop':
				case 'scrollRight':
				case 'scrollBottom':
				case 'scrollLeft':
				case 'scrollVertical':
				case 'scrollHorizontal':
					if (_self.objects.case.is(':hidden')) {
						_self.transition.fade(_self.objects.case, 'out', 0, 0, function () {
							_self._loadContent();
						});
						_self.transition.fade(_self.objects.contentInner, 'out', 0);
					} else {
						_self.transition.scroll(_self.objects.case, 'out', _self.settings.speedOut, function () {
							_self._loadContent();
						});
					}
					break;
				case 'fade':
					if (_self.objects.case.is(':hidden')) {
						_self.transition.fade(_self.objects.case, 'out', 0, 0, function () {
							_self._loadContent();
						});
					} else {
						_self.transition.fade(_self.objects.case, 'out', _self.settings.speedOut, 0, function () {
							_self._loadContent();
						});
					}
					break;
				case 'fadeInline':
				case 'elastic':
					if (_self.objects.case.is(':hidden')) {
						_self.transition.fade(_self.objects.case, 'out', 0, 0, function () {
							_self._loadContent();
						});
					} else {
						_self.transition.fade(_self.objects.contentInner, 'out', _self.settings.speedOut, 0, function () {
							_self._loadContent();
						});
					}
					break;
				default:
					_self.transition.fade(_self.objects.case, 'out', 0, 0, function () {
						_self._loadContent();
					});
					break;
			}
		},
		_handleEvents: function () {
			_self._unbindEvents();
			_self.objects.nav.children().not(_self.objects.close).hide();
			if (_self.isSlideshowEnabled()) {
				if ((_self.settings.slideshowAutoStart === true || _self.isSlideshowStarted) && !_self.objects.nav.hasClass(_self.settings.classPrefix + 'paused')) {
					_self._startTimeout();
				} else {
					_self._stopTimeout();
				}
			}
			if (_self.settings.liveResize) {
				_self._watchResizeInteraction();
			}
			_self.objects.close.click(function (event) {
				event.preventDefault();
				_self.close();
			});
			if (_self.settings.closeOnOverlayClick === true) {
				_self.objects.overlay.css('cursor', 'pointer').click(function (event) {
					event.preventDefault();
					_self.close();
				});
			}
			if (_self.settings.useKeys === true) {
				_self._addKeyEvents();
			}
			if (_self.objectData.isPartOfSequence) {
				_self.objects.nav.attr(_self._prefixAttributeName('ispartofsequence'), true);
				_self.objects.nav.data('items', _self._setNavigation());
				_self.objects.prev.click(function (event) {
					event.preventDefault();
					if (_self.settings.navigateEndless === true || !_self.item.isFirst()) {
						_self.objects.prev.unbind('click');
						_self.cache.action = 'prev';
						_self.objects.nav.data('items').prev.click();
						if (_self.isSlideshowEnabled()) {
							_self._stopTimeout();
						}
					}
				});
				_self.objects.next.click(function (event) {
					event.preventDefault();
					if (_self.settings.navigateEndless === true || !_self.item.isLast()) {
						_self.objects.next.unbind('click');
						_self.cache.action = 'next';
						_self.objects.nav.data('items').next.click();
						if (_self.isSlideshowEnabled()) {
							_self._stopTimeout();
						}
					}
				});
				if (_self.isSlideshowEnabled()) {
					_self.objects.play.click(function (event) {
						event.preventDefault();
						_self._startTimeout();
					});
					_self.objects.pause.click(function (event) {
						event.preventDefault();
						_self._stopTimeout();
					});
				}
				if (_self.settings.swipe === true) {
					if ($.isPlainObject($.event.special.swipeleft)) {
						_self.objects.case.on('swipeleft', function (event) {
							event.preventDefault();
							_self.objects.next.click();
							if (_self.isSlideshowEnabled()) {
								_self._stopTimeout();
							}
						});
					}
					if ($.isPlainObject($.event.special.swiperight)) {
						_self.objects.case.on('swiperight', function (event) {
							event.preventDefault();
							_self.objects.prev.click();
							if (_self.isSlideshowEnabled()) {
								_self._stopTimeout();
							}
						});
					}
				}
			}
		},
		_addKeyEvents: function () {
			$(document).bind('keyup.lightcase', function (event) {
				if (_self.isBusy) {
					return;
				}
				switch (event.keyCode) {
					case 27:
						_self.objects.close.click();
						break;
					case 37:
						if (_self.objectData.isPartOfSequence) {
							_self.objects.prev.click();
						}
						break;
					case 39:
						if (_self.objectData.isPartOfSequence) {
							_self.objects.next.click();
						}
						break;
				}
			});
		},
		_startTimeout: function () {
			_self.isSlideshowStarted = true;
			_self.objects.play.hide();
			_self.objects.pause.show();
			_self.cache.action = 'next';
			_self.objects.nav.removeClass(_self.settings.classPrefix + 'paused');
			_self.timeout = setTimeout(function () {
				_self.objects.nav.data('items').next.click();
			}, _self.settings.timeout);
		},
		_stopTimeout: function () {
			_self.objects.play.show();
			_self.objects.pause.hide();
			_self.objects.nav.addClass(_self.settings.classPrefix + 'paused');
			clearTimeout(_self.timeout);
		},
		_setNavigation: function () {
			var $links = $((_self.cache.selector || _self.settings.attr)),
				sequenceLength = _self.objectData.sequenceLength - 1,
				items = {
					prev: $links.eq(_self.objectData.prevIndex),
					next: $links.eq(_self.objectData.nextIndex)
				};
			if (_self.objectData.currentIndex > 0) {
				_self.objects.prev.show();
			} else {
				items.prevItem = $links.eq(sequenceLength);
			}
			if (_self.objectData.nextIndex <= sequenceLength) {
				_self.objects.next.show();
			} else {
				items.next = $links.eq(0);
			}
			if (_self.settings.navigateEndless === true) {
				_self.objects.prev.show();
				_self.objects.next.show();
			}
			return items;
		},
		item: {
			isFirst: function () {
				return (_self.objectData.currentIndex === 0);
			},
			isLast: function () {
				return (_self.objectData.currentIndex === (_self.objectData.sequenceLength - 1));
			}
		},
		_cloneObject: function ($object) {
			var $clone = $object.clone(),
				objectId = $object.attr('id');
			if ($object.is(':hidden')) {
				_self._cacheObjectData($object);
				$object.attr('id', _self.settings.idPrefix + 'temp-' + objectId).empty();
			} else {
				$clone.removeAttr('id');
			}
			return $clone.show();
		},
		isMobileDevice: function () {
			var deviceAgent = navigator.userAgent.toLowerCase(),
				agentId = deviceAgent.match(_self.settings.mobileMatchExpression);
			return agentId ? true : false;
		},
		isTransitionSupported: function () {
			var body = $('body').get(0),
				isTransitionSupported = false,
				transitionMapping = {
					'transition': '',
					'WebkitTransition': '-webkit-',
					'MozTransition': '-moz-',
					'OTransition': '-o-',
					'MsTransition': '-ms-'
				};
			for (var key in transitionMapping) {
				if (transitionMapping.hasOwnProperty(key) && key in body.style) {
					_self.support.transition = transitionMapping[key];
					isTransitionSupported = true;
				}
			}
			return isTransitionSupported;
		},
		transition: {
			fade: function ($object, type, speed, opacity, callback) {
				var isInTransition = type === 'in',
					startTransition = {},
					startOpacity = $object.css('opacity'),
					endTransition = {},
					endOpacity = opacity ? opacity : isInTransition ? 1 : 0;
				if (!_self.isOpen && isInTransition) return;
				startTransition['opacity'] = startOpacity;
				endTransition['opacity'] = endOpacity;
				$object.css(startTransition).show();
				if (_self.support.transitions) {
					endTransition[_self.support.transition + 'transition'] = speed + 'ms ease';
					setTimeout(function () {
						$object.css(endTransition);
						setTimeout(function () {
							$object.css(_self.support.transition + 'transition', '');
							if (callback && (_self.isOpen || !isInTransition)) {
								callback();
							}
						}, speed);
					}, 15);
				} else {
					$object.stop();
					$object.animate(endTransition, speed, callback);
				}
			},
			scroll: function ($object, type, speed, callback) {
				var isInTransition = type === 'in',
					transition = isInTransition ? _self.settings.transitionIn : _self.settings.transitionOut,
					direction = 'left',
					startTransition = {},
					startOpacity = isInTransition ? 0 : 1,
					startOffset = isInTransition ? '-50%' : '50%',
					endTransition = {},
					endOpacity = isInTransition ? 1 : 0,
					endOffset = isInTransition ? '50%' : '-50%';
				if (!_self.isOpen && isInTransition) return;
				switch (transition) {
					case 'scrollTop':
						direction = 'top';
						break;
					case 'scrollRight':
						startOffset = isInTransition ? '150%' : '50%';
						endOffset = isInTransition ? '50%' : '150%';
						break;
					case 'scrollBottom':
						direction = 'top';
						startOffset = isInTransition ? '150%' : '50%';
						endOffset = isInTransition ? '50%' : '150%';
						break;
					case 'scrollHorizontal':
						startOffset = isInTransition ? '150%' : '50%';
						endOffset = isInTransition ? '50%' : '-50%';
						break;
					case 'scrollVertical':
						direction = 'top';
						startOffset = isInTransition ? '-50%' : '50%';
						endOffset = isInTransition ? '50%' : '150%';
						break;
				}
				if (_self.cache.action === 'prev') {
					switch (transition) {
						case 'scrollHorizontal':
							startOffset = isInTransition ? '-50%' : '50%';
							endOffset = isInTransition ? '50%' : '150%';
							break;
						case 'scrollVertical':
							startOffset = isInTransition ? '150%' : '50%';
							endOffset = isInTransition ? '50%' : '-50%';
							break;
					}
				}
				startTransition['opacity'] = startOpacity;
				startTransition[direction] = startOffset;
				endTransition['opacity'] = endOpacity;
				endTransition[direction] = endOffset;
				$object.css(startTransition).show();
				if (_self.support.transitions) {
					endTransition[_self.support.transition + 'transition'] = speed + 'ms ease';
					setTimeout(function () {
						$object.css(endTransition);
						setTimeout(function () {
							$object.css(_self.support.transition + 'transition', '');
							if (callback && (_self.isOpen || !isInTransition)) {
								callback();
							}
						}, speed);
					}, 15);
				} else {
					$object.stop();
					$object.animate(endTransition, speed, callback);
				}
			},
			zoom: function ($object, type, speed, callback) {
				var isInTransition = type === 'in',
					startTransition = {},
					startOpacity = $object.css('opacity'),
					startScale = isInTransition ? 'scale(0.75)' : 'scale(1)',
					endTransition = {},
					endOpacity = isInTransition ? 1 : 0,
					endScale = isInTransition ? 'scale(1)' : 'scale(0.75)';
				if (!_self.isOpen && isInTransition) return;
				startTransition['opacity'] = startOpacity;
				startTransition[_self.support.transition + 'transform'] = startScale;
				endTransition['opacity'] = endOpacity;
				$object.css(startTransition).show();
				if (_self.support.transitions) {
					endTransition[_self.support.transition + 'transform'] = endScale;
					endTransition[_self.support.transition + 'transition'] = speed + 'ms ease';
					setTimeout(function () {
						$object.css(endTransition);
						setTimeout(function () {
							$object.css(_self.support.transition + 'transform', '');
							$object.css(_self.support.transition + 'transition', '');
							if (callback && (_self.isOpen || !isInTransition)) {
								callback();
							}
						}, speed);
					}, 15);
				} else {
					$object.stop();
					$object.animate(endTransition, speed, callback);
				}
			}
		},
		_callHooks: function (hooks) {
			if (typeof (hooks) === 'object') {
				$.each(hooks, function (index, hook) {
					if (typeof (hook) === 'function') {
						hook.call(_self.origin);
					}
				});
			}
		},
		_cacheObjectData: function ($object) {
			$.data($object, 'cache', {
				id: $object.attr('id'),
				content: $object.html()
			});
			_self.cache.originalObject = $object;
		},
		_restoreObject: function () {
			var $object = $('[id^="' + _self.settings.idPrefix + 'temp-"]');
			$object.attr('id', $.data(_self.cache.originalObject, 'cache').id);
			$object.html($.data(_self.cache.originalObject, 'cache').content);
		},
		resize: function () {
			if (!_self.isOpen) return;
			if (_self.isSlideshowEnabled()) {
				_self._stopTimeout();
			}
			_self.dimensions = _self.getViewportDimensions();
			_self._calculateDimensions(_self.cache.object);
		},
		_cacheScrollPosition: function () {
			var $window = $(window),
				$document = $(document),
				offset = {
					'top': $window.scrollTop(),
					'left': $window.scrollLeft()
				};
			_self.cache.scrollPosition = _self.cache.scrollPosition || {};
			if (!_self._assertContentInvisible()) {
				_self.cache.cacheScrollPositionSkipped = true;
			} else if (_self.cache.cacheScrollPositionSkipped) {
				delete _self.cache.cacheScrollPositionSkipped;
				_self._restoreScrollPosition();
			} else {
				if ($document.width() > $window.width()) {
					_self.cache.scrollPosition.left = offset.left;
				}
				if ($document.height() > $window.height()) {
					_self.cache.scrollPosition.top = offset.top;
				}
			}
		},
		_watchResizeInteraction: function () {
			$(window).resize(_self.resize);
		},
		_unwatchResizeInteraction: function () {
			$(window).off('resize', _self.resize);
		},
		_watchScrollInteraction: function () {
			$(window).scroll(_self._cacheScrollPosition);
			$(window).resize(_self._cacheScrollPosition);
		},
		_unwatchScrollInteraction: function () {
			$(window).off('scroll', _self._cacheScrollPosition);
			$(window).off('resize', _self._cacheScrollPosition);
		},
		_assertContentInvisible: function () {
			return $($('body').children().not('[id*=' + _self.settings.idPrefix + ']').get(0)).height() > 0;
		},
		_restoreScrollPosition: function () {
			$(window).scrollTop(parseInt(_self.cache.scrollPosition.top)).scrollLeft(parseInt(_self.cache.scrollPosition.left)).resize();
		},
		_switchToFullScreenMode: function () {
			_self.settings.shrinkFactor = 1;
			_self.settings.overlayOpacity = 1;
			$('html').addClass(_self.settings.classPrefix + 'fullScreenMode');
		},
		_open: function () {
			//if(_self.isOpen && !_self.objects.case.hasClass('.lc-type-image') && _self.objectData.type != 'image') _self.objects.case.css('opacity', 0);
			_self.isOpen = true;
			_self.support.transitions = _self.settings.cssTransitions ? _self.isTransitionSupported() : false;
			_self.support.mobileDevice = _self.isMobileDevice();
			if (_self.support.mobileDevice) {
				$('html').addClass(_self.settings.classPrefix + 'isMobileDevice');
				if (_self.settings.fullScreenModeForMobile) {
					_self._switchToFullScreenMode();
				}
			}
			if (!_self.settings.transitionIn) {
				_self.settings.transitionIn = _self.settings.transition;
			}
			if (!_self.settings.transitionOut) {
				_self.settings.transitionOut = _self.settings.transition;
			}

			// add class
			_self.objects.case.removeClass().addClass(_self.settings.groupClass + " lc-type-" + _self.objectData.type);
			// yaCounter
			yaCounterFunction(_self.settings.metr);

			switch (_self.settings.transitionIn) {
				case 'fade':
				case 'fadeInline':
				case 'elastic':
				case 'scrollTop':
				case 'scrollRight':
				case 'scrollBottom':
				case 'scrollLeft':
				case 'scrollVertical':
				case 'scrollHorizontal':
					if (_self.objects.case.is(':hidden')) {
						_self.objects.close.css('opacity', 0);
						if (_self.settings.reinit) _self.settings.reinit = 0;
						else _self.objects.overlay.css('opacity', 0);
						_self.objects.case.css('opacity', 0);
						_self.objects.contentInner.css('opacity', 0);
					}
					_self.transition.fade(_self.objects.overlay, 'in', _self.settings.speedIn, _self.settings.overlayOpacity, function () {
						_self.transition.fade(_self.objects.close, 'in', _self.settings.speedIn);
						_self._handleEvents();
						_self._processContent();
					});
					break;
				default:
					_self.transition.fade(_self.objects.overlay, 'in', 0, _self.settings.overlayOpacity, function () {
						_self.transition.fade(_self.objects.close, 'in', 0);
						_self._handleEvents();
						_self._processContent();
					});
					break;
			}
			$('html').addClass(_self.settings.classPrefix + 'open');
			_self.objects.case.attr('aria-hidden', 'false');
		},
		close: function () {
			_self.isOpen = false;
			if (_self.isSlideshowEnabled()) {
				_self._stopTimeout();
				_self.isSlideshowStarted = false;
				_self.objects.nav.removeClass(_self.settings.classPrefix + 'paused');
			}
			_self.objects.loading.hide();
			_self._unbindEvents();
			_self._unwatchResizeInteraction();
			_self._unwatchScrollInteraction();
			$('html').removeClass(_self.settings.classPrefix + 'open');
			_self.objects.case.attr('aria-hidden', 'true');
			_self.objects.nav.children().hide();
			_self._callHooks(_self.settings.onClose);
			switch (_self.settings.transitionOut) {
				case 'fade':
				case 'fadeInline':
				case 'scrollTop':
				case 'scrollRight':
				case 'scrollBottom':
				case 'scrollLeft':
				case 'scrollHorizontal':
				case 'scrollVertical':
					_self.transition.fade(_self.objects.case, 'out', _self.settings.speedOut, 0, function () {
						_self.transition.fade(_self.objects.overlay, 'out', _self.settings.speedOut, 0, function () {
							_self.cleanup();
						});
					});
					break;
				case 'elastic':
					_self.transition.zoom(_self.objects.case, 'out', _self.settings.speedOut, function () {
						_self.transition.fade(_self.objects.overlay, 'out', _self.settings.speedOut, 0, function () {
							_self.cleanup();
						});
					});
					break;
				default:
					_self.cleanup();
					break;
			}
		},
		_unbindEvents: function () {
			_self.objects.overlay.unbind('click');
			$(document).unbind('keyup.lightcase');
			_self.objects.case.unbind('swipeleft').unbind('swiperight');
			_self.objects.prev.unbind('click');
			_self.objects.next.unbind('click');
			_self.objects.play.unbind('click');
			_self.objects.pause.unbind('click');
			_self.objects.close.unbind('click');
		},
		_cleanupDimensions: function () {
			var opacity = _self.objects.contentInner.css('opacity');
			_self.objects.case.css({
				'width': '',
				'height': '',
				'top': '',
				'left': '',
				'margin-top': '',
				'margin-left': ''
			});
			_self.objects.contentInner.removeAttr('style').css('opacity', opacity);
			_self.objects.contentInner.children().removeAttr('style');
		},
		cleanup: function () {
			_self._cleanupDimensions();
			_self.objects.loading.hide();
			_self.objects.overlay.hide();
			_self.objects.case.hide();
			_self.objects.prev.hide();
			_self.objects.next.hide();
			_self.objects.play.hide();
			_self.objects.pause.hide();
			_self.objects.case.removeAttr(_self._prefixAttributeName('type'));
			_self.objects.nav.removeAttr(_self._prefixAttributeName('ispartofsequence'));
			_self.objects.contentInner.empty().hide();
			_self.objects.info.children().empty();
			if (_self.cache.originalObject) {
				_self._restoreObject();
			}
			_self._callHooks(_self.settings.onCleanup);
			_self.cache = {};
		},
		_matchMedia: function () {
			return window.matchMedia || window.msMatchMedia;
		},
		_devicePixelRatio: function () {
			return window.devicePixelRatio || 1;
		},
		_isPublicMethod: function (method) {
			return (typeof _self[method] === 'function' && method.charAt(0) !== '_');
		},
		_export: function () {
			window.lightcase = {};
			$.each(_self, function (property) {
				if (_self._isPublicMethod(property)) {
					lightcase[property] = _self[property];
				}
			});
		}
	};
	_self._export();
	$.fn.lightcase = function (method) {
		if (_self._isPublicMethod(method)) {
			return _self[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return _self.init.apply(this, arguments);
		} else {
			$.error('Method ' + method + ' does not exist on jQuery.lightcase');
		}
	};
})(jQuery);



/*! npm.im/object-fit-images 3.2.3 */
var objectFitImages = function () { "use strict"; function t(t, e) { return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='" + t + "' height='" + e + "'%3E%3C/svg%3E" } function e(t) { if (t.srcset && !m && window.picturefill) { var e = window.picturefill._; t[e.ns] && t[e.ns].evaled || e.fillImg(t, { reselect: !0 }), t[e.ns].curSrc || (t[e.ns].supported = !1, e.fillImg(t, { reselect: !0 })), t.currentSrc = t[e.ns].curSrc || t.src } } function i(t) { for (var e, i = getComputedStyle(t).fontFamily, r = {}; null !== (e = l.exec(i));)r[e[1]] = e[2]; return r } function r(e, i, r) { var n = t(i || 1, r || 0); p.call(e, "src") !== n && b.call(e, "src", n) } function n(t, e) { t.naturalWidth ? e(t) : setTimeout(n, 100, t, e) } function c(t) { var c = i(t), o = t[a]; if (c["object-fit"] = c["object-fit"] || "fill", !o.img) { if ("fill" === c["object-fit"]) return; if (!o.skipTest && g && !c["object-position"]) return } if (!o.img) { o.img = new Image(t.width, t.height), o.img.srcset = p.call(t, "data-ofi-srcset") || t.srcset, o.img.src = p.call(t, "data-ofi-src") || t.src, b.call(t, "data-ofi-src", t.src), t.srcset && b.call(t, "data-ofi-srcset", t.srcset), r(t, t.naturalWidth || t.width, t.naturalHeight || t.height), t.srcset && (t.srcset = ""); try { s(t) } catch (t) { window.console && console.warn("https://bit.ly/ofi-old-browser") } } e(o.img), t.style.backgroundImage = 'url("' + (o.img.currentSrc || o.img.src).replace(/"/g, '\\"') + '")', t.style.backgroundPosition = c["object-position"] || "center", t.style.backgroundRepeat = "no-repeat", t.style.backgroundOrigin = "content-box", /scale-down/.test(c["object-fit"]) ? n(o.img, function () { o.img.naturalWidth > t.width || o.img.naturalHeight > t.height ? t.style.backgroundSize = "contain" : t.style.backgroundSize = "auto" }) : t.style.backgroundSize = c["object-fit"].replace("none", "auto").replace("fill", "100% 100%"), n(o.img, function (e) { r(t, e.naturalWidth, e.naturalHeight) }) } function s(t) { var e = { get: function (e) { return t[a].img[e || "src"] }, set: function (e, i) { return t[a].img[i || "src"] = e, b.call(t, "data-ofi-" + i, e), c(t), e } }; Object.defineProperty(t, "src", e), Object.defineProperty(t, "currentSrc", { get: function () { return e.get("currentSrc") } }), Object.defineProperty(t, "srcset", { get: function () { return e.get("srcset") }, set: function (t) { return e.set(t, "srcset") } }) } function o(t, e) { var i = !h && !t; if (e = e || {}, t = t || "img", f && !e.skipTest || !d) return !1; "img" === t ? t = document.getElementsByTagName("img") : "string" == typeof t ? t = document.querySelectorAll(t) : "length" in t || (t = [t]); for (var r = 0; r < t.length; r++)t[r][a] = t[r][a] || { skipTest: e.skipTest }, c(t[r]); i && (document.body.addEventListener("load", function (t) { "IMG" === t.target.tagName && o(t.target, { skipTest: e.skipTest }) }, !0), h = !0, t = "img"), e.watchMQ && window.addEventListener("resize", o.bind(null, t, { skipTest: e.skipTest })) } var a = "bfred-it:object-fit-images", l = /(object-fit|object-position)\s*:\s*([-\w\s%]+)/g, u = "undefined" == typeof Image ? { style: { "object-position": 1 } } : new Image, g = "object-fit" in u.style, f = "object-position" in u.style, d = "background-size" in u.style, m = "string" == typeof u.currentSrc, p = u.getAttribute, b = u.setAttribute, h = !1; return o.supportsObjectFit = g, o.supportsObjectPosition = f, function () { function t(t, e) { return t[a] && t[a].img && ("src" === e || "srcset" === e) ? t[a].img : t } f || (HTMLImageElement.prototype.getAttribute = function (e) { return p.call(t(this, e), e) }, HTMLImageElement.prototype.setAttribute = function (e, i) { return b.call(t(this, e), e, String(i)) }) }(), o }();
$(function () {
	objectFitImages($('.image-cover img, .image-fill img, .image-contain img'));
});


/* my orders */
function setVisibilityProducts(el) {
	let div_products = $('.div_products');
	let div_products_element = $(el).parent().children(".div_products")[0];
	var texts = $('.table_person .pr_table');
	for (let i = 0; i < div_products.length; i++) {
		let but = $(div_products[i]).parent().find(".button_set_visibility span");
		let count_products = $(but).text().split("(");
		if (div_products[i] === div_products_element) {
			if ($(div_products[i]).css('display') === 'none') {
				$(div_products[i]).slideDown();

				$(but).text(texts.data('text1') + " (" + count_products[1]);
			} else {
				$(div_products[i]).slideUp();
				$(but).text(texts.data('text2') + " (" + count_products[1]);
			}

		} else {
			$(div_products[i]).slideUp();
			$(but).text(texts.data('text2') + " (" + count_products[1]);
		}
	}
}

$('.button_set_visibility').click(function () {
	setVisibilityProducts($(this));
});



/* jQuery JSON plugin 2.4.0 | code.google.com/p/jquery-json */
(function ($) {
	'use strict'; var escape = /["\\\x00-\x1f\x7f-\x9f]/g, meta = { '\b': '\\b', '\t': '\\t', '\n': '\\n', '\f': '\\f', '\r': '\\r', '"': '\\"', '\\': '\\\\' }, hasOwn = Object.prototype.hasOwnProperty; $.toJSON = typeof JSON === 'object' && JSON.stringify ? JSON.stringify : function (o) {
		if (o === null) { return 'null'; }
		var pairs, k, name, val, type = $.type(o); if (type === 'undefined') { return undefined; }
		if (type === 'number' || type === 'boolean') { return String(o); }
		if (type === 'string') { return $.quoteString(o); }
		if (typeof o.toJSON === 'function') { return $.toJSON(o.toJSON()); }
		if (type === 'date') {
			var month = o.getUTCMonth() + 1, day = o.getUTCDate(), year = o.getUTCFullYear(), hours = o.getUTCHours(), minutes = o.getUTCMinutes(), seconds = o.getUTCSeconds(), milli = o.getUTCMilliseconds(); if (month < 10) { month = '0' + month; }
			if (day < 10) { day = '0' + day; }
			if (hours < 10) { hours = '0' + hours; }
			if (minutes < 10) { minutes = '0' + minutes; }
			if (seconds < 10) { seconds = '0' + seconds; }
			if (milli < 100) { milli = '0' + milli; }
			if (milli < 10) { milli = '0' + milli; }
			return '"' + year + '-' + month + '-' + day + 'T' +
				hours + ':' + minutes + ':' + seconds + '.' + milli + 'Z"';
		}
		pairs = []; if ($.isArray(o)) {
			for (k = 0; k < o.length; k++) { pairs.push($.toJSON(o[k]) || 'null'); }
			return '[' + pairs.join(',') + ']';
		}
		if (typeof o === 'object') {
			for (k in o) {
				if (hasOwn.call(o, k)) {
					type = typeof k; if (type === 'number') { name = '"' + k + '"'; } else if (type === 'string') { name = $.quoteString(k); } else { continue; }
					type = typeof o[k]; if (type !== 'function' && type !== 'undefined') { val = $.toJSON(o[k]); pairs.push(name + ':' + val); }
				}
			}
			return '{' + pairs.join(',') + '}';
		}
	}; $.evalJSON = typeof JSON === 'object' && JSON.parse ? JSON.parse : function (str) { return eval('(' + str + ')'); }; $.secureEvalJSON = typeof JSON === 'object' && JSON.parse ? JSON.parse : function (str) {
		var filtered = str.replace(/\\["\\\/bfnrtu]/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, ''); if (/^[\],:{}\s]*$/.test(filtered)) { return eval('(' + str + ')'); }
		throw new SyntaxError('Error parsing JSON, source is not valid.');
	}; $.quoteString = function (str) {
		if (str.match(escape)) {
			return '"' + str.replace(escape, function (a) {
				var c = meta[a]; if (typeof c === 'string') { return c; }
				c = a.charCodeAt(); return '\\u00' + Math.floor(c / 16).toString(16) + (c % 16).toString(16);
			}) + '"';
		}
		return '"' + str + '"';
	};
}(jQuery));


var variantHash = '', colorHash = '', spolerId = '', winHeight, winWidth, screenSize, bc;
var hash1 = window.location.hash;
var hash = hash1.replace('#', '');
hash = hash.replace('nk-', '');
if (hash.indexOf('v_') + 1) variantHash = hash.replace('v_', '');
if (hash.indexOf('c_') + 1) colorHash = hash.replace('c_', '');
if (hash.indexOf('spoler') + 1) spolerId = hash;

var dt = new Date().getDate();
var nospm = parseInt(dt * 63312);

function getInternetExplorerVersion(browsers) {
	var result = false
	browsers.forEach(function (a) {
		// if (navigator.userAgent.search(/Safari/) > 0) {a = 'Safari'};
		// if (navigator.userAgent.search(/Firefox/) > 0) {a = 'MozillaFirefox'};
		if (a == 'ie' && window.navigator.userAgent.search(/MSIE/) > 0 || window.navigator.userAgent.search(/NET CLR /) > 0) result = true;
		// if (navigator.userAgent.search(/Chrome/) > 0) {a = 'Google Chrome'};
		// if (navigator.userAgent.search(/YaBrowser/) > 0) {a = 'Яндекс браузер'};
		// if (navigator.userAgent.search(/OPR/) > 0) {a = 'Opera'};
		// if (navigator.userAgent.search(/Konqueror/) > 0) {a = 'Konqueror'};
		// if (navigator.userAgent.search(/Iceweasel/) > 0) {a = 'Debian Iceweasel'};
		// if (navigator.userAgent.search(/SeaMonkey/) > 0) {a = 'SeaMonkey'};
		if (a == 'edge' && window.navigator.userAgent.search(/Edge/) > 0) result = true;
	})
	return result;
}
if (getInternetExplorerVersion(['ie', 'edge'])) $('body').addClass('ie');


function addsubm() {
	$('#nc_moderate_form').prepend("<input name='posting' type='hidden' value='1'><input name='hdiaca' type='hidden' value='" + nospm + "'>");
	//$('input[type=submit],input[type=reset],input[type=button],button').not('.nosubm').addClass('ssubm');
	$('input[type=text],input[type=password],textarea').not('.noinp').addClass('inp');
}

function yaCounterFunction(id) {
	if (id) {
		// yandex metrika
		metrikaid = $("[data-metrikaid]").data('metrikaid');
		console.log(metrikaid)
		if (metrikaid && id && window['yaCounter' + metrikaid]) {
			eval('yaCounter' + metrikaid + '.reachGoal("' + id + '")');
			console.log('yaCounter' + metrikaid + '.reachGoal("' + id + '")');
		}
		// google targ id
		if (typeof _gaq === 'object') {
			_gaq.push(['_trackEvent', 'button', id]);
			console.log('_gaq.push("button", "' + id + '")');
		}
	}
}


function number_format(number, decimals, dec_point, thousands_sep) {
	var i, j, kw, kd, km, minus = '';

	if (number < 0) {
		minus = "-";
		number = number * -1;
	}

	if (isNaN(decimals = Math.abs(decimals))) {
		decimals = 2;
	}
	if (dec_point == undefined) {
		dec_point = ",";
	}
	if (thousands_sep == undefined) {
		thousands_sep = ".";
	}

	i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

	if ((j = i.length) > 3) {
		j = j % 3;
	} else {
		j = 0;
	}

	km = (j ? i.substr(0, j) + thousands_sep : "");
	kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
	kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");
	return minus + km + kw + kd;
}


function _open(url, width, height) {
	window.open(url, '', 'width=' + width + ',height=' + height + ',left=' + ((window.innerWidth - width) / 2) + ',top=' + ((window.innerHeight - height) / 2));
	return false;
}


$.cookie = function (name, value, options) {
	if (typeof value != 'undefined') {
		options = options || {};
		if (value === null) {
			value = '';
			options = $.extend({}, options);
			options.expires = -1;
		}
		var expires = '';
		if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
			var date;
			if (typeof options.expires == 'number') {
				date = new Date();
				date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
			} else {
				date = options.expires;
			}
			expires = '; expires=' + date.toUTCString();
		}

		var path = options.path ? '; path=' + (options.path) : '';
		var domain = options.domain ? '; domain=' + (options.domain) : '';
		var secure = options.secure ? '; secure' : '';
		document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
	} else {
		var cookieValue = null;
		if (document.cookie && document.cookie != '') {
			var cookies = document.cookie.split(';');
			for (var i = 0; i < cookies.length; i++) {
				var cookie = jQuery.trim(cookies[i]);

				if (cookie.substring(0, name.length + 1) == (name + '=')) {
					cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
					break;
				}
			}
		}
		return cookieValue;
	}
};

// при прокрутке страницы
function scrollPos() {
	var scroll = $(window).scrollTop();
	// btn scroll top
	if (scroll >= 450) $('#bottombut').fadeIn();
	else $('#bottombut').fadeOut();
	// fix zone
	zonesTop = $('.zone-fixtop');
	if (zonesTop.length) zonesTop.each(function () {
		admin = $('.authbit').length ? 46 : 0;
		scroll += admin;
		zone = $(this);
		id = zone.attr('data-id');

		defaultTop = $(".substrate-" + id).length ? $(".substrate-" + id).offset().top : zone.offset().top;

		if (scroll <= defaultTop || screenSize == "mobile") {
			zone.removeClass('fixed-active');
			$(".substrate-" + id).remove();
		} else {
			if (!$(".substrate-" + id).length) {
				substrate = $("<div class='substrate substrate-" + id + "'></div>").height(zone.outerHeight(true));
				zone.before(substrate);
			}
			zone.addClass('fixed-active');
		}
	});
	// parallax
	zonesParallax = $('.zone-parallax');
	if (zonesParallax.length) zonesParallax.each(function () {
		zone = $(this);
		zone.parallax({
			speed: 0.5
		});
	});
}

// наверх
$('#bottombut a.top').click(function () {
	$('html, body').animate({ 'scrollTop': 0 }, 500);
	return false;
});


// ссылки для запросов
var uricart = (function () {
	var b = {
		cartadd: "/cart/?template=-1&nc_ctpl=2006&isNaked=1"
	};
	return b
})(uricart || {});



// preloader
var pagePreloader = $('#page-preloader');
if (pagePreloader.length > 0) {
	var blks = $('.subdivision-items, .catalog-items, .gallery-items, .vendor-items, .advantage-items, .portfolio-items, .news-items, .gencomponent-items');
	preloader = setInterval(function () {
		var owl = count = 0;
		blks.each(function () {
			items = $(this)
			if (items.hasClass('owl-carousel') && !items.hasClass('owl-loaded')) owl++;
			else if (!items.hasClass('owl-carousel') && !items.attr('data-calculated')) count++;
		});
		if (!count && !owl) {
			clearTimeout(preloader);
			pagePreloader.fadeOut(50);
		}
	}, 5);
	setTimeout(function () {
		clearTimeout(preloader);
		pagePreloader.fadeOut(50);
	}, 3000);
}


pageLoadEvent = (function () {
	$('.slider-items').each(function (i) {
		slider = $(this);
		var outIndex,
			isDragged = false;
		var nav = slider.data('owl-nav') || false;
		var dots = slider.data('owl-dots') || false;
		var autoplay = slider.data('owl-autoplay') || 5000;
		var animateIn = slider.data('owl-effect-in') || false;
		var animateOut = slider.data('owl-effect-out') || false;
		var speed = slider.data('owl-speed') || 700;
		slider.owlCarousel({
			items: 1,
			margin: 0,
			autoplay: true,
			nav: nav,
			dots: dots,
			animateIn: animateIn,
			animateOut: animateOut,
			navSpeed: speed,
			dotsSpeed: speed,
			autoplaySpeed: speed,
			autoplayTimeout: autoplay,
			loop: true,
			singleItem: true,
			onChange: function (event) {
				var currentItem = $('.owl-item', this.$element).eq(event.item.index);
				if (!this.$element.data('init')) {
					this.$element.data('init', 1);
					textCenter(this.$element, currentItem);
				} else {
					var elemsToanim = currentItem.find("[data-animation-out]");
					setAnimation(elemsToanim, 'out');
				}
			},
			onChanged: function (event) {
				var currentItem = $('.owl-item', this.$element).eq(event.item.index);
				var elemsToanim = currentItem.find("[data-animation-in]");

				textCenter(this.$element, currentItem);
				setAnimation(elemsToanim, 'in');
			},
			onResized: function (event) {
				var currentItem = $('.owl-item', this.$element).eq(event.item.index);
				textCenter(this.$element, currentItem);
			},
			onInitialized: function (event) {
				var currentItem = $('.owl-item', this.$element).eq(event.item.index);
				textCenter(this.$element, currentItem);
				if (dots) {
					sld = $(this.$element);
					sld.find('.owl-item:not(.cloned) .slider-item').each(function (i) {
						try {
							item = $(this);
							text = item.find(".slider-name span").text();
							sld.find(".owl-dot:nth-child(" + (++i) + ") span").text(text.length ? text : i);
						} catch (e) { }
					});
				}
			}
		});
		function setAnimation(_elem, _InOut) {
			if (!_elem.length) return;
			var animationEndEvent = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
			_elem.each(function () {
				var elem = $(this);
				var animationType = 'animated ' + elem.data('animation-' + _InOut);
				if (_InOut == 'out') elem.removeClass(elem.data('animation-in'));
				elem.addClass(animationType).one(animationEndEvent, function () {
					elem.removeClass(animationType);
				});
			});
		}
		function textCenter(slider, currentItem) {
			if (screenSize != 'mobile' && slider.hasClass('bannerText-center')) {
				data = currentItem.find('.slider-data');
				hmain = currentItem.height();
				h = data.height();
				data.css("top", (hmain - h) / 2);
			}
		}
	});

	$(".blocks .owl-carousel:not(.slider-items), .comparison-param-value").each(function () {
		el = $(this);
		var blockID = el.parents('[data-blockid]').data('blockid');

		wmargin = el.data('margin') >= 0 ? el.data('margin') : 15;
		autoplay = el.data('owl-autoplay') || 5000;
		autoplayBoolean = el.data('owl-autoplay') > 0 ? true : false;
		scrollspeed = el.data('owl-scrollspeed') || 400;
		nav = el.data('owl-nav') == 1 || false;
		dots = el.data('owl-dots') || false;
		autowidth = el.data('owl-autowidth') || false;
		loop = el.data('owl-loop') !== undefined ? el.data('owl-loop') : true;
		count_item = coutItem(el)

		if (el.is(".owl-carousel") && el.parents(".block_analogi, .block_buywith").length) {
			el.addClass("count-catalog-" + count_item)
			el.removeClass("owl-carousel")

			var w = 0,
				items = el.find(".catalog-item")
			for (var i = 0; i < items.length; i++) {
				w += $(items[i]).outerWidth()
			}
			if (el.outerWidth() < w) {
				el.removeClass("count-catalog-" + count_item)
				el.addClass("owl-carousel")
			} else {
				return true;
			}
		}

		var option = {
			items: count_item,
			margin: wmargin,
			nav: nav,
			dots: dots,
			navSpeed: scrollspeed,
			dotsSpeed: scrollspeed,
			loop: loop,
			autoplay: autoplayBoolean,
			autoplayTimeout: autoplay,
			autoplayHoverPause: autoplayBoolean,
			smartSpeed: scrollspeed,
			autoWidth: autowidth,
			/*autoplay: true,
			autoplayTimeout: 2000,*/
		};

		// NAV TYPES
		blk = el.parents("[data-blockid]");
		space = blk.find('.blk_head');
		if (el.data('owl-nav') == 2) {
			nav = "<div class='owl-nav'><div class='owl-prev'></div><div class='owl-next'></div></div>";
			if (space.length) {
				blk.addClass("nav-type-" + el.data('owl-nav'));
				space.append(nav);
			}
		}
		if (el.data('owl-nav') == 3) {
			next = "<a href='#' class='owl-head owl-head-next'></a>";
			prev = "<a href='#' class='owl-head owl-head-prev'></a>";
			if (space.length) {
				blk.addClass("nav-type-" + el.data('owl-nav'));
				space.prepend(prev);
				space.append(next);
			}
		}

		window['slider' + blockID] = el;
		window['slider' + blockID].on('resize.owl.carousel', function (event) {
			window['slider' + blockID].data('owl.carousel').options.items = coutItem(window['slider' + blockID]);
		}).on('initialized.owl.carousel', function (event) {
			miniCardHeightFunc(window['slider' + blockID]);
		}).owlCarousel(option);

	});
	$('body').on('click', '.blk_head .owl-nav div, .owl-head', function () {
		nav = $(this);
		next = nav.hasClass('owl-next') || nav.hasClass('owl-head-next') || false;
		carusel = nav.parents("[data-blockid]").find('.owl-carousel.owl-loaded');
		if (carusel.length) {
			if (next) carusel.data('owl.carousel').next();
			else carusel.data('owl.carousel').prev();
		}
		return false;
	});
})


// ######## start ready
jQuery(function ($) {

	var kopeek = $('body[data-kopeek]').length ? 2 : 0;


	addsubm();
	setTimeout(function () {
		$(".person_line.person_usertype input:checked").click();
	}, 200);

	if ($('.heightFix aside#submenu li.active').length > 0) {
		$('.heightFix aside#submenu').each(function () {
			pos = $(this).find('li.active:first').position();
			if (pos.top) $(this).parents('.typeblock').scrollTop(pos.top);
		});
	}

	$("#seltema").change(function () {
		$("#addvopros").load($('#seltema :selected').val(), function () { addsubm(); });
	});

	$(window).on('scroll', function () {
		scrollPos();
	});

	// телефон в блоке
	$(".blocks .modal-body form input[name*='phone'], .blocks .modal-body form input[name*='telefon'], .blocks .modal-body form input[name*='telephon']").each(function () {
		var input = $(this)
		if (input.is('[type="hidden"]')) return;
		if (!input.is("[name*='][name]']")) input.mask(window.mask_option.mask(input.val()), window.mask_option)
	})

	$(".basket_blks input[name*='phone'], .basket_blks input[name*='telefon'], .basket_blks input[name*='telephon']").each(function () {
		var input = $(this)
		if (input.is('[type="hidden"]')) return;
		if (!input.is("[name*='][name]']")) input.mask(window.mask_option.mask(input.val()), window.mask_option)
	})
	$("body").on("change", "#order input, #order select, #order textarea", function () {
		if (save_cart) {
			var data = $(this).parents("#order").serializeArray(),
				save_data = []
			for (var i in data) {
				if (data.hasOwnProperty(i)) {
					var item = data[i],
						key = item['name']
					if ($.inArray(key, ["posting", "hdiaca", "nc_token", "catalogue", "cc", "sub", "f_Checked"]) == -1 && key.indexOf('is_reqlist') == -1 && key.indexOf('req_customf') == -1 && key.indexOf('][name]') == -1) {
						save_data.push(item)
					}
				}
			}
			localStorage.setItem('order_data', JSON.stringify(save_data))
		}
	})
	// insert data in cart
	var save_cart = true
	if (localStorage.getItem('order_data')) {
		save_cart = false
		try {
			var data_cart = JSON.parse(localStorage.getItem('order_data')),
				form = $("#order")
			for (var i in data_cart) {
				if (data_cart.hasOwnProperty(i)) {
					var item = data_cart[i],
						key = item['name'],
						value = item['value'],
						inp = form.find("[name='" + key + "']")

					if (inp.is('input')) {
						switch (inp.attr('type')) {
							case "text":
								inp.val(value)
								break;
							case "radio":
							case "checkbox":
								inp.filter("[value='" + value + "']").click()
								break;
						}
					} else if (inp.is('select')) {
						inp.parents(".input-field-standart, .method_items").find("li.option[data-value='" + value + "']").click()
					} else if (inp.is('textarea')) {
						inp.val(value)
					}
				}
			}
			form.click()
		} catch (e) { }
		save_cart = true
	}


	// data link
	$("body").on("click", "[data-link]", function (e) {
		e.preventDefault();
		if (!$(this).is("[data-subopt]")) location.href = $(this).data('link');
	});

	// scrollto
	$("body").on("click", "*[data-scrollto]", function () {
		obj = $(this);
		to = $(obj.data('scrollto'));
		pos = to.offset();
		$('html, body').animate({ 'scrollTop': parseInt(pos.top - 60) }, 500);
		if (to.data('opt') > 0) to.click();
		return false;
	});

	$("body").on("click", "a[href^='/#']", function () {

		a = $(this);
		var margintop = typeof a.data('margintop') !== typeof undefined && $(this).data('margintop') !== false ? parseInt($(this).data('margintop')) : 60;
		console.log(margintop)
		name = a.attr('href').replace("/", "");
		to = $(name);
		pos = to.offset();
		$('html, body').animate({ 'scrollTop': parseInt(pos.top - margintop) }, 500);
		if (to.data('opt') > 0) to.click();
		$('.header_menu li').removeClass('active');
		return false;
	});


	$('ul#cssmenu > li:last').addClass('last');


	$('body').on('click', '#tabs li', function () {
		tab = $(this).attr('rel');
		$('#tabs li').removeClass('act');
		$(this).addClass('act');
		$('.tab').hide();
		$('#' + tab).show();
		return false;
	});

	// metrica click
	$('body').on('click', '[data-metr="headphone"], [data-metr="contactemail"], a[data-metr]:not([data-rel="lightcase"]), span[data-metr]:not([data-rel="lightcase"])', function () {
		metr = $(this).data('metr');
		if (metr.length) yaCounterFunction(metr);
	});


	// ajax sumbit

	function ajaxSumbit() {
		if (typeof (myAjaxSumbit) === "function") {
			return myAjaxSumbit($(this));
		}
		submit = $(this);
		form = submit.parents('form:first');
		winalert = $("#alert");
		metr = form.data("metr");

		//return false;
		if (form.hasClass('ajax')) {
			submit.addClass('disabled').attr('disabled', true);
			form.prepend("<input name='isNaked' type='hidden' value='1'><input name='posting' type='hidden' value='1'><input name='hdiaca' type='hidden' value='" + nospm + "'>");
			$.post(form.attr("action"), form.serialize(), function (data) {
				if (data.title) {
					winalert.find('.modal_h1').html(data.title);
					if (data.error) {
						targ = (data.target ? $(data.target) : form.find('.result'));
						targ.html('<div class=warnText><b>' + data.title + ':</b> ' + data.error + '</div>');
						setTimeout(function () { targ.text(''); }, 8000);
					}
					if (data.succes) {
						// metrika
						yaCounterFunction(metr)
						// google targ id
						if (metr && typeof _gaq === 'object') {
							_gaq.push(['_trackEvent', 'button', metr]);
							console.log('_gaq.push("button", "' + metr + '")');
						}
						targ = (data.target ? $(data.target) : (winalert.find('.wrap').length ? winalert.find('.wrap') : form.find('.result')));
						if (data.hash) window.location.hash = data.hash;
						form[0].reset();
						targ.html(data.succes);
						if (data.redirect) setTimeout(function () { location.href = data.redirect; }, 5000);
					}
					if (data.ok) {
						winalert.find('.wrap').append("<div class='submit close'><input type='button' value='" + data.ok + "'></div>");
					}
					if (data.items && data.transaction) {
						var itemsStr = '';
						var transaction = parseInt(data.transaction);
						var items = jQuery.parseJSON(data.items);

						for (var key in items) {
							if (items.hasOwnProperty(key)) {
								itemsStr += (itemsStr ? "," : "") + "{id: " + items[key]['id'] + ", qnt: " + items[key]['count'] + ",  price: " + items[key]['sum'] + "}";
							}
						}
						eval('rrApiOnReady.push(function() { try { rrApi.order({transaction: ' + transaction + ', items: [ ' + itemsStr + ' ]	});	} catch(e) {} });');
					}

				} else {
					form.find('.result').html(data);
					setTimeout(function () { $('.result').text(''); }, 8000);
				}
				if (data.todo == 'clearcart') clearcartfunc();
				submit.removeClass('disabled').prop('disabled', false);
			}, 'json');
			return false;
		}

		if (submit.hasClass('ajax-btn')) {
			if (submit.attr('href') == "clearcartfunc" || submit.attr('href') == "delitem") {
				lightcase.close();
				return false;
			}
			submit.addClass('disabled');
			parent = submit.parent();
			parent.addClass('before');

			$.ajax({
				url: submit.attr('href'),
				success: function (data) {
					parent.removeClass('before');

					if (submit.data('success')) {
						jsonparam = submit.data('success');
					} else if ($.trim(data).charAt(0) == "{") {
						jsonparam = data;
					} else {
						jsonparam = {};
					}
					jsonparam = typeof jsonparam != "object" ? JSON.parse(jsonparam) : jsonparam;
					processJson(jsonparam);
				}
			})

			return false;
		}

		if (form.hasClass('ajax2')) {
			if (form.find('.cke').length > 0) CKupdate();
			form.prepend("<input name='isNaked' type='hidden' value='1'><input name='posting' type='hidden' value='1'><input name='hdiaca' type='hidden' value='" + nospm + "'>");
			submit.addClass('disabled').attr('disabled', true);
			if (form.parents("#tabview707-0").length) {  /* для мультиязычности чтобы не упираться в лимит инпутов */
				langPost = { lang: {} };
				$.each(form.serializeArray(), function (key, item) {
					if (item.name.indexOf('bc_lists') != -1) langPost.lang[item.name] = item.value;
					else langPost[item.name] = item.value;
				});
				langPost.lang = JSON.stringify(langPost.lang);
				$.post(form.attr('action'), langPost, function (data) {
					if ($.trim(data).charAt(0) == "{") {
						jsonparam = data;
					} else {
						jsonparam = {};
					}
					jsonparam = typeof jsonparam != "object" ? JSON.parse(jsonparam) : jsonparam;
					processJson(jsonparam);
				});
			} else {
				form.ajaxSubmit({
					// dataType: 'json',
					success: processJson,
					error: (res, err) => {
						console.error('Ошибка ответа сервера', res, err);
					}
				});
			}
			return false;
		}

	}

	$('body').on("click", "input[type=image], input[type=submit], .falsesubmit, .ajax-btn", ajaxSumbit);

	function CKupdate() {
		for (instance in CKEDITOR.instances)
			CKEDITOR.instances[instance].updateElement();
	}



	/**** MOBILE SCRIPT ****/

	/* SEARCH */
	$('body').on('keyup', '.msearch-input input', function (event) {
		clearbtn = $('.msearch-clear');
		if (event.target.value == "") clearbtn.click();
		else clearbtn.addClass('active');
	});
	$('body').on('click', '.msearch-clear', function () {
		$('#mobile-search .msearch-input input').val('');
		$(this).removeClass('active');
		$(".search-result").remove()
	});
	/* CART */
	$('body').on('click', '.mpanel-cart', function () {
		location.href = '/cart/';
	});
	/* INFO */
	$('body').on('click', '.mpanel-info', function () {
		load.close();
		load.allItemClose();
		lightcase.start({
			href: $('#mobile-info').length ? '#mobile-info' : '/bc/modules/default/index.php?user_action=mobileInfo',
			groupClass: 'mob-info modal-nopadding',
			transition: 'scrollTop',
			maxWidth: 390,
			speedIn: 450
		});

	});
	/* MOBILE MENU STEP */
	$('body').on('click', '.mobile-menu-drop .menu-open > a, .mobile-menu-drop .mm-back > a', function () {
		a = $(this);
		blk = a.parents('.mobile-block');
		if (blk.find('.mblock-head').length) blk.addClass('menu-step');
		menu = a.parents('.mobile-menu-drop');
		ul = a.parent().parent();
		uldrop = a.parent('li').find("> ul");
		allul = a.parents('.mobile-menu-drop').find('.menu-second');
		i = menu.attr('data-i') > 0 ? menu.attr('data-i') : 0;
		head = $('.menu-step > .mblock-head');

		// remove active
		allul.removeClass('active-step');
		// add active
		uldrop.addClass('active-step')
		a.parents('.menu-second').addClass('active-step');

		if (a.parent().hasClass('mm-back')) {
			menu.attr('data-i', (i > 0 ? --i : 0));
			if (i == 0) head.css('left', 0);
			height = ul.parent().parent().height();
			minus = ul.parent().parent().find('> .mblock-head').height();
		} else {
			menu.attr('data-i', ++i);
			if (i == 1) head.css('left', '-100%');
			height = uldrop.height();
			minus = $(".menu-step .mblock-head").outerHeight();

			// minus = uldrop.find('> .mblock-head').height();
		}

		a.parents('.mobile_menu_drop').css('height', height - (minus > 0 ? minus : 0));
		// left %
		menu.css('left', "-" + (100 * i) + "%");
		return false;
	});





	/* CART SCRIPT */
	// alert incart
	closealert = (function () {
		alertcl = setTimeout(function () { $('#notification .alert').fadeOut(700); }, 5000);
	});
	$('body').on('click', '#notification .close', function () {
		clearTimeout(alertcl);
		$('#notification .alert').hide();
		return false;
	});
	$('body').on('mouseover', '#notification .alert', function () { clearTimeout(alertcl); });
	$('body').on('mouseleave', '#notification .alert', function () { closealert(); });

	// item count +1/-1
	$('body').on('click', '[class $= "_up"]:not(.colline), [class $= "_down"]:not(.colline)', function () {
		var newcount = 1;
		btn = $(this);
		input = btn.parent().find('input');
		obj = btn.parents('[data-id]:first');

		if (btn.is('[class $= "up"]')) {
			if (input.val() > 0) newcount = parseInt(input.val()) + 1;
		} else {
			if (input.val() > 1) {
				newcount = parseInt(input.val()) - 1;
			} else {
				if (btn.parents('.cart-btn').hasClass('active') || !btn.parents('.cart-btn').length) delitemModal(obj);
				return false;
			}
		}
		if (newcount > 0) {
			input.val(newcount).prop('value', newcount).change();
		}
		return false;
	});



	// ADD in cart
	$('body').on('click', '.incart-js', function () {
		a = $(this);
		btn = a.parent('.cart-btn');
		obj = btn.parents('[data-id]:first');
		id = obj.data('id');
		input = btn.find('input[name="count"]');

		// уже добавлен в корзину
		if (btn.hasClass('active')) return false;
		// обязательный выбор цвета
		if (a.hasClass('colors-required') && !obj.find('.select-color .color-item.active').length) {
			name = obj.find(".js-variable.color-body").data('name');
			alert('Выберите "' + name + '"');
			return false;
		}
		a.addClass('disabled');
		obj.data('count', input.val());
		changeItem(obj, a, "add");

		if (a.attr('data-metr')) yaCounterFunction(a.attr('data-metr'))

		return false;
	});
	// пересчет количества
	$('body').on('change', '.cart-btn.active input, .basket_m_num input, .bt_incard_num input', function () {
		input = $(this);
		input.attr('disabled', 'disabled');
		obj = input.parents('[data-id]:first');
		if (input.val() < 1) input.val(1);
		obj.data('count', input.val());
		changeItem(obj, input, "update");
		return false;
	});
	// удаление товара из корзины
	$('body').on('click', '.delitem', function () {
		obj = $(this).closest('[data-id]');
		delitemModal(obj);
		return false;
	});
	// удалить все товары
	$('body').on('click', '.cartclear', function () {
		confirmlight({ title: lang.delitems.title, confirmlink: "clearcartfunc", start: 'open' });
		return false;
	});
	$('body').on('click', 'a.lightcase-ok[href="clearcartfunc"]', function () {
		clearcartfunc();
	});

	// type minicart
	minicart = $('[data-minicart]').first().data('minicart');
	if (!minicart) minicart = 1;

	// add and change item
	changeItem = (function (obj, el, type) {
		obj.data('minicart', minicart);

		// open or not
		smallcart1 = $('.basket_m_open.open').length ? 1 : 0;
		obj.data('smallcart1', smallcart1);
		cdekClearCache();
		$.post(uricart.cartadd, obj.data(), function (data) {
			// убираем disabled у кнопки
			if (el.hasClass('disabled')) el.removeClass('disabled');
			else el.prop('disabled', false);

			// обновление дубликатов объекта
			items = $(".item-obj[data-id='" + data.itemid + "']");
			itembtn = items.find(".cart-btn");
			itembtn.addClass('active');
			itembtn.find('input').val(data.itemcount);
			itembtn.find('.incart-js').attr('title', data.incart_info2)
				.find('span').text(data.incart_info2);

			// внутряк корзины
			carttabl = $("#bigcart");
			if (carttabl.length) {
				carttabl.find('[data-id=' + data.itemid + '] .totalsumItem').text(number_format(data.itemsum, kopeek, ',', ' '));
				$('.discontSumTr .discontSum span:first').text(number_format(data.totsumdiscont, 2, ',', ' '));
				$('.deliverSumTr .deliverSum span:first').text(number_format(data.deliversum, 2, ',', ' '));
				if (typeof data.delivery_sum_pay_after !== 'undefined') $('.deliverySumPayAfterTr .deliverySumPayAfter span:first').text(number_format(data.delivery_sum_pay_after, 2, ',', ' '));
				$('.total_sum_price span:first').text(number_format(data.totdelsum, 2, ',', ' '));
				$('.total_sum_price').data("totaldelsum", data.totdelsum);

				// бесплатная доставка
				if (data.delivery_free) $(".delivery_free_text").addClass('active')
				else $(".delivery_free_text").removeClass('active')
				if (data.delivery_nothave > 0) $(".df_price").text(number_format(data.delivery_nothave, 2, ',', ' '))
				if (data.delivery_freevisible) $('.delivery_free_info').show()
				else $(".delivery_free_info").hide()
				recalcAssistDelivery();
			}

			// если добавление товара
			if (type == "add") {
				el.parent('.cart-btn').addClass('active');
				$('#notification .alert > div').html(data.incart_info4);
				$('#notification .alert').fadeIn(700, function () {
					closealert();
				});
				if (location.href == '/cart/') location.reload(true);
			}
			loadsmallcart(data);
			yaDataLayer('changeItem', data.change_item);
			/*Службы доставки*/
			if (typeof data.all.delivery.assist === 'object'
				&& typeof data.all.delivery.assist.description === 'string'
				&& data.all.delivery.assist.type == 'cdek'
			) {
				$('.delivery-assist-blk.cdek .cdek-post-name').html(data.all.delivery.assist.description);
			}

			if (typeof data.all?.delivery?.assist === 'object'
				&& typeof data.all?.delivery?.assist?.description === 'string'
				&& data.all?.delivery?.assist?.type == 'pochta_russia'
			) {
				$('.delivery-assist-blk.PR .PR-post-name').html(data.all?.delivery?.assist?.description);
			}
		}, "json");
	});
	// delitem
	delitemModal = (function (obj) {
		confirmlight({ title: lang.delitem.title, confirmlink: "delitem", idobj: obj.data('id'), start: 'open' });
	});
	$('body').on('click', 'a.lightcase-ok[href="delitem"]', function () {
		delitem($(this).parents("#сonfirm-actions").attr("data-id"));
	});
	delitem = (function (id) {
		// open or not
		smallcart1 = $('.basket_m_open.open').length ? 1 : 0;
		cdekClearCache();
		$.get(uricart.cartadd, { 'delitem': id, 'minicart': minicart, 'smallcart1': smallcart1 }, function (data) {
			// удаление с мини-корзины
			$(".smallcart [data-id='" + id + "']").remove();

			// Обновление всех дубликатов
			itms = $(".catalog-item[data-id='" + id + "'], .catalog-item-full[data-id='" + id + "']");
			itms.find(".cart-btn").removeClass('active');
			itms.data('count', 1);
			itms.find('.incart-js').attr('title', data.incart_info1)
				.find('span').text(data.incart_info1);
			itms.find(".cart-btn input").val(1);

			// внутряк корзины
			carttabl = $("#bigcart");
			if (carttabl.length) {
				carttabl.find("[data-id='" + id + "']").remove();
				$('.discontSumTr .discontSum span:first').text(number_format(data.totsumdiscont, 2, ',', ' '));
				$('.deliverSumTr .deliverSum span:first').text(number_format(data.deliversum, 2, ',', ' '));
				if (data.delivery_sum_pay_after) $('.deliverySumPayAfterTr .deliverySumPayAfter span:first').text(number_format(data.delivery_sum_pay_after, 2, ',', ' '));
				$('.total_sum_price span:first').text(number_format(data.totdelsum, 2, ',', ' '));
				$('.total_sum_price').data("totaldelsum", data.totdelsum);
				// бесплатная доставка
				if (data.delivery_free) $(".delivery_free_text").addClass('active')
				else $(".delivery_free_text").removeClass('active')
				if (data.delivery_nothave > 0) $(".df_price").text(number_format(data.delivery_nothave, 2, ',', ' '))
				if (data.delivery_freevisible) $('.delivery_free_info').show()
				else $(".delivery_free_info").hide()
				recalcAssistDelivery();
			}
			loadsmallcart(data);
			yaDataLayer('changeItem', data.change_item);
			/*Службы доставки*/
			if (typeof data.all.delivery.assist === 'object'
				&& typeof data.all.delivery.assist.description === 'string'
				&& data.all.delivery.assist.type == 'cdek'
			) {
				$('.delivery-assist-blk.cdek .cdek-post-name').html(data.all.delivery.assist.description);
			}
		}, "json");
	});
	// clear cart
	clearcartfunc = (function () {
		console.log("clearcartfunc");
		// open or not
		smallcart1 = $('.basket_m_open.open').length ? 1 : 0;
		cdekClearCache();
		$.get(uricart.cartadd, { 'clear': 1, 'minicart': minicart, 'smallcart1': smallcart1, 'time': Math.round(new Date().getTime() / 1000) }, function (data) {
			loadsmallcart(data);
			$('.tocart').remove();
			if ($('#bigcart').length > 0) {
				if (data.text) $("#bigcart").html(data.text);
				if ($("#orderform").length > 0) $("#orderform").remove();
			}

			// очищаем товар
			btn = $('.cart-btn');
			btn.removeClass('active');
			btn.find('input').val(1);
			btn.find('.incart-js').attr('title', data.incart_info1)
				.find('span').text(data.incart_info1);
			$('.catalog-item, .catalog-item-full').data('count', 1);

			yaDataLayer('clearCart', data.change_item);
		}, "json");
	});
	// обновление корзины
	loadsmallcart = (function (data) {
		if (data.totcount > 0) {
			$('.smallcart .blk_body_wrap .basket_mini, .smallcart .blk_body_wrap .card_left').replaceWith(data.smallcart)
			$('.smallcart .card_left').parents('.blocks').slideDown();
			$('.scrollbar-inner, .scrollbar-card').scrollbar();
			$('.panelr').stop().animate({ 'right': '10px' }, 400);
			$('#mobcart span').stop().fadeTo(250, 0.1).fadeTo(250, 1).fadeTo(250, 0.1).fadeTo(250, 1).fadeTo(250, 0.1).fadeTo(250, 1);
			$('.incart_info').html(data.incart_info);
			if (data.kvkOrderBase64 && data.kvkSig) {
				kvkBl = $('#kvkBlock');
				kvkBl.data('order', data.kvkOrderBase64);
				kvkBl.data('sig', data.kvkSig);
			}
		} else {
			$('#bigcart').parents('article:first').html(data.incart_info);
			$('.incart_info').text('');
			$('.panelr').stop().animate({ 'right': '-41px' }, 400);
			$('.smallcart .blk_body_wrap .basket_mini, .smallcart .blk_body_wrap .card_left').replaceWith(data.smallcart).parents('.blocks');
			$('.smallcart .card_left').parents('.blocks').slideUp();
		}
		if (data.totcount >= 0) {
			$('.minicartCount2 span, .mpanel-cart-count').text(data.totcount);
			if (data.totcount > 0) $(".mpanel-cart").addClass('mpanel-cart-active');
			else $(".mpanel-cart").removeClass('mpanel-cart-active');
		}
		if (data.error) alert(data.error);
		minsumcart(data);
	});



	// обновление корзины
	minsumcart = (function (data) {
		minOrderSum = parseFloat($('.minOrderFail .sum').text());
		totsum = parseFloat($('.total_sum_price').data('totaldelsum'));
		console.log(totsum + ' ' + minOrderSum);
		if (minOrderSum > 0) {
			if (totsum < minOrderSum && minOrderSum) { $('.minOrderFail').show(); $('#order').hide(); } else { $('.minOrderFail').hide(); $('#order').show(); }
		}
	});

	minsumcart();




	/* доставка */
	calcDelivery = (function (deliverID, assistParam) {
		var delivery_json = { 'delivery': deliverID };
		if (assistParam) delivery_json.assistDelivery = { type: assistParam.type, value: assistParam.value };

		$.get(uricart.cartadd, delivery_json, function (data) {
			if (data.error) {
				$('#order .result').html('<div class="warnText">' + data.error + '</div>');
				setTimeout(function () { $('#order .result').text(''); }, 5000);
			}
			$('.deliverSumTr .deliverSum span:first').text(number_format(data.deliversum, 2, ',', ' '));
			$('.total_sum_price span:first').text(number_format(data.totdelsum, 2, ',', ' '));
			$('.total_sum_price').data("totaldelsum", data.totdelsum);
			/* бесплатная доставка */
			if (data.delivery_free) $(".delivery_free_text").addClass('active')
			else $(".delivery_free_text").removeClass('active')
			if (data.delivery_nothave > 0) $(".df_price").text(number_format(data.delivery_nothave, 2, ',', ' '))
			if (data.delivery_freevisible) $('.delivery_free_info').show()
			else $(".delivery_free_info").hide();
		}, "json");
	})
	$('body').on('change', "[name='f_delivery']", function () {
		orderFormInputChanger('delivery', { obj: $(this) });
		checkDelivery($(this));
		if ($('[name="f_payment"]').hasClass('select-style')) {
			$('[name="f_payment"]').removeClass('disabled').niceSelect('update');
		}
	});
	checkDelivery = (function (item) {
		var item = (item ? item : $("[name='f_delivery']"));
		if ($('#bigcart').length == 0 || item.length == 0) return;
		var deliverID = parseFloat(item.val());
		var option = item.find('option:selected');
		var deliveryAssist = $('.delivery-assist-blk');
		deliveryAssist.addClass('none-important');
		$('.deliverySumPayAfterTr').addClass('none-important');
		switch (option.data('type')) {
			case 'cdek':
				deliveryAssist.filter('.cdek').removeClass('none-important');
				cdekStart();
				break;
			case 'edost':
				deliveryAssist.filter('.edost').removeClass('none-important')
					.find('.edost-result').html('');
				break;
			case 'PR':
				deliveryAssist.filter('.PR').removeClass('none-important');
				if (PothtaRussia?.reCall) {
					PothtaRussia.start()
					PothtaRussia.reCall()
				}
				break;
			default:
				calcDelivery(deliverID);
				break;
		}
		if (deliverID == 0 || ['cdek', 'edost'].indexOf(option.data('type')) != -1) $(".delivery_free_info").hide();
	});
	/*edost*/
	$('body').on('change', 'select[name="edostCities"]', function () {
		getCalcEdost($(this));
	});
	function getCalcEdost(item) {
		if ($('[name="f_payment"]').hasClass('select-style')) {
			$('[name="f_payment"]').removeClass('disabled').niceSelect('update');
		}
		if (item.val() != '') {
			$('.edost-result').html('').addClass('disabled');
			$.post('/bc/modules/default/index.php?user_action=edost_delivery', "&edost_to_city=" + item.val(), function (data) {
				if (data.success) {
					var deliveryList = "";
					$.each(data.result, function (key, item) {
						var dataParams = [
							'name="' + item.description + '"',
							'price="' + item.price + " <span class='rubl'>Р</span>\"",
							'text="' + (item.days ? 'Срок доставки: ' + item.days : '') + '"',
							'transfer="' + (item.transfer ? 'true' : 'false') + '"'
						];
						deliveryList += '<option value="' + key + '" data-' + dataParams.join(' data-') + '></option>';
					});
					var deliverySelect = $("<select name='edost' class='select-style select-lists edost-delivery assist-delivery-select'><option value='' data-name='Выберите тип доставки'>Выберите тип доставки</option>" + deliveryList + "</select>");
					$('.edost-result').html(deliverySelect);
					deliverySelect.niceSelect();
				} else {
					$('#order .result').html('<div class="warnText"><b>Ошибка:<b/>' + data.error + '</div>');
					setTimeout(function () { $('#order .result').text(''); }, 5000);
				}
				$('.edost-result').removeClass('disabled');
			}, 'json');
		}
	}
	$('body').on('change', '.assist-delivery-select', function (e) {
		var item = $(this);
		var assistParam = {
			type: item.attr('name'),
			value: item.val()
		};
		var payment = $('[name="f_payment"]');
		payment.removeClass('disabled').niceSelect('update');
		if (item.hasClass('edost-delivery') && item.find('option:selected').data('transfer')) {
			payment.find('option').each(function () {
				var option = $(this);
				if (option.data('name').match(/налич/i)) {
					option.prop('selected', true);
					payment.addClass('disabled').niceSelect('update');
					return false;
				}
			});
		}
		calcDelivery($("[name='f_delivery']").val(), assistParam);
	});
	/* перерасчет доставки */
	function recalcAssistDelivery() {
		var delivery = $('[name="f_delivery"]');
		if (delivery.length) {
			var delType = (delivery.hasClass('select-style') ? delivery.find('option:selected').data('type') : delivery.find(':checked').data('type'));
			switch (['edost'].indexOf(delType)) {
				case 0: getCalcEdost($('[name="edostCities"]')); break;
				default: break;
			}
		}
	}
	if (window.noDeliveryCheckStart !== true && $('#order').length) $('#order [name="f_delivery"]:first').change();
	/* end доставка */

	// есть ли варианты товара
	isItemVariant = (function (obj1) {
		obj = (obj1 ? obj1.parents('[data-id]') : $('.itemcard'));
		if (!itemcard && obj.hasClass('itemcard')) itemcard = 1;
		incart = obj.find('.incart').parent();
		instock = obj.find('.stock:first');
		notstock = obj.find('.notstock');
		dicont = parseFloat(obj.data('oldprice'));
		selvariant = obj.find('.selvariant');
		selvariantS = selvariant.find("option:selected");
		if (selvariant.length < 1) {
			selvariant = obj.find('.selcolor');
			selvariantS = selvariant.find("li.act");
		}

		if (selvariant.length > 0 && obj) {
			if (selvariantS.data('name')) {
				console.log('varitem');
				variantPrice = parseFloat(selvariantS.data('price') ? selvariantS.data('price') : obj.data('origprice'));
				variantStock = parseFloat(selvariantS.data('stock') ? selvariantS.data('stock') : 0);
				variantHex = selvariantS.data('hex');
				if (dicont > 0) {
					variantPriceOld = parseFloat(selvariantS.data('oldprice'));
				}
				variantName = selvariantS.data('name');
				if (itemcard && obj.hasClass('itemcard')) {
					if (obj.find('.selcolor').length > 0) window.location.hash = "c_" + selvariantS.data('num'); else window.location.hash = "v_" + selvariantS.data('num');
				}
				incart.show();
			} else {
				console.log('not varitem');
				variantPrice = parseFloat(obj.data('oldprice') ? obj.data('oldprice') : obj.data('origprice'));
				variantStock = parseFloat(obj.data('origstock'));
				variantName = obj.data('origname');
				variantHex = obj.data('orighex');
				//if (incart.hasClass('variable')) incart.hide();
				if (itemcard) window.location.hash = '';
			}
			obj.data('price', variantPrice).data('name', variantName).data('stock', variantStock).data('hex', variantHex);
			if (itemcard) { // title
				obj.find('h1:first').text(variantName);
				$('#fastprew .modal_h1').text(variantName);
			}
			obj.data('price', variantPrice);
			if (obj.hasClass('stockbuy')) { // show/hide incart but
				if (variantStock > 0) {
					if (!obj.find('.incart').hasClass('variable')) incart.show();
					instock.show(); notstock.hide();
				} else {
					incart.hide(); instock.hide(); notstock.show();
				}
			}
			if (dicont > 0 && selvariantS.data('name')) {
				console.log('varitem set');
				obj.find('.normal_price .cen').text(number_format(variantPrice, '', ',', ' '));
				obj.find('.last_price .cen').text(number_format(variantPriceOld, '', ',', ' '));
			} else {
				obj.find('.normal_price .cen').text(number_format(variantPrice, '', ',', ' '));
			}
		} else {
			//incart.show();
		}
	});



	/*$('body').on('change','.selvariant',function(){
		isItemVariant($(this));
		return false;
	});

	if (variantHash) {
		$('.selvariant').find("option[data-num='"+variantHash+"']").prop('selected',true);
		$('.selvariant').change();
		isItemVariant();
	}*/

	// item colors
	$(document).on('click', '.js-variable.select-color [data-num]', function () {
		item = $(this).parents('.catalog-item, .catalog-item-full');

		color = $(this);
		// делаем активным элемент
		color.siblings().removeClass('active');
		color.addClass('active');
		// устанавливаем data цвета у товара
		item.data('colornum', color.data('num'))
			.data('colorname', color.data('colorname'))
			.data('colorcode', color.data('colorcode'))
			.data('colorphoto', color.data('colorphoto'));
		// перелистывание фото
		if (color.data('colorphoto')) {
			if (item.is('.catalog-item-full')) {
				id = parseInt(color.data('colorphoto') - 1);
				item.find(".gallery-mini .g_m_img:eq(" + id + ")").click();
			} else {
				id = parseInt(color.data('colorphoto'));
				photo = item.find("img[data-photo='" + id + "']");
				if (photo.length) {
					photo.siblings('img').hide();
					photo.show();
				}
			}
		}


		// кнопка в корзину
		incart = item.find('.cart-btn').removeClass('active');
		incart.find('a[data-title]').attr('title', incart.find('a[data-title]').attr('data-title'));
		// 1клик
		item.find('.inorder-js').removeClass('none');

		// цена до скидки
		discont = parseFloat(item.data('oldprice'));

		// сбор параметров
		colorPrice = parseFloat(color.data('price') ? color.data('price') : item.data('origprice'));
		colorStock = parseFloat(color.data('stock') ? color.data('stock') : 0);
		colorStockHave = parseFloat(color.data('stockhave') ? color.data('stockhave') : 0);
		colorStockName = color.data('stockname') ? color.data('stockname') : '';
		colorHex = parseFloat(color.data('price')) ? color.data('hex') : item.data('orighex');
		if (discont > 0) colorPriceLast = parseFloat(color.data('price') ? color.data('oldprice') : item.data('oldprice'));
		colorName = color.data('name');

		// установка параметров
		item.data('price', colorPrice)
			.data('name', colorName)
			.data('stock', colorStock)
			.data('hex', colorHex);

		// лейбл наличие / нет в наличии
		spanStock = item.find('.blk_stock span, .have_item .instock, .have_item .nostock');
		spanStock.text(colorStockName).removeClass().addClass(colorStockHave ? "instock icons i_check" : "nostock icons i_del3");

		// замена заголовка
		if (item.hasClass('catalog-item-full')) {
			item.find('h1:first').text(colorName);
			$('#lightcase-case.card-fast-prew .lightcase-title').text(colorName);
		} else {
			item.find(".blk_name a").text(colorName);
		}
		// замена цен
		priceBlocks = $('.card_price_info, .blk_priceblock');
		if (colorPrice > 0) {
			priceBlocks.removeClass('none');
			item.find('.normal_price .cen').text(number_format(colorPrice, kopeek, ',', ' '));
		} else {
			priceBlocks.addClass('none');
		}
		if (discont > 0) item.find('.last_price .cen').text(number_format(colorPriceLast, kopeek, ',', ' '));

		miniCardHeightFunc(item.parent(), "update")

		return false;
	});

	// select if color hash
	/*if (colorHash) {
		$(".selcolor li[data-num='"+colorHash+"']").click();
		isItemVariant();
	}*/

	// select default color if required
	//if (itemcard && $('.itemcard .incart').hasClass('buycolors')) $(".selcolor li:first").click();



	// Owl Carusel Objects
	$(window).load(pageLoadEvent);




	// alltime resize
	resizeAction = (function () {
		// mobile or not
		winWidth = $(this).width();
		screenSize = (winWidth <= 780 ? 'mobile' : 'full');

		$('body').removeClass(screenSize == 'full' ? 'mobile' : 'full').addClass(screenSize == 'full' ? 'full' : 'mobile');

		// count item in line block
		countitemsParamAll();
		dataload()

		// add btn filter in mobile
		filterBlock = $('.blocks.class2041');
		if (filterBlock.length
			&& !$('.mobile-filter').length
			&& $('body').hasClass('class2001')
			&& screenSize == 'mobile'
			&& location.pathname != '/search/') {
			if ($('.zone-title').length) $('.zone-title').addClass('mobile-filter-have').append("<div class='mobile-filter mobyes'><a class='open-filter mainmenubg' href='" + filterBlock.find("[data-load]").data('load') + "' data-rel='lightcase' data-lc-options='{\"maxWidth\":450,\"groupClass\":\"modal-filter modal-nopadding\"}' title='Фильтр'><span>Открыть фильтр</span></a></div>")
		}

		// totrows count
		if ($('[data-totrows]').length && $('.filter-item-count span').length && !$('.filter-item-count span').hasClass('added')) {
			if ($('#content [data-totrows]').data('totrows') >= 0) {
				$('.filter-item-count span').text($('#content [data-totrows]').data('totrows')).addClass('added');
				$('.filter-item-count').css('display', 'inline-block');
			}
		}
		if ($('.zone-sort').length && !$('[data-totrows]').length) $('.zone-sort').hide();


		gallery = $('.catalog-item-full.template-type2 .gallery');
		content = $('.catalog-item-full.template-type2 .content_info');
		if (gallery.length && content.length) {
			galleryh = gallery.outerHeight()
			contenth = content.outerHeight();
			content.height(galleryh >= contenth ? galleryh : 'auto');
		}


	});

	// window resize
	resizeAction();
	var width = $(window).width();
	$(window).resize(function () {
		if ($(window).width() == width) return;
		width = $(window).width();
		resizeAction();
	})
	$(document).ready(function () {
		mainPhoto();
		yaDataLayer('detail');
	});


	// spoler
	$(document).on('click', '.spoler a', function () {
		obj = $(this).parent();
		spoler = obj.next('.spolerText');
		if (obj.hasClass('act')) {
			obj.removeClass('act '); spoler.slideUp();
			//window.location.hash = "";
		} else {
			if (!$('body').hasClass('slider-noclose')) {
				$('.spolerText').slideUp(); $('.spoler').removeClass('act');
			}
			obj.addClass('act'); spoler.slideDown();
			//window.location.hash = obj.attr('id');
		}
		return false;
	});

	if (spolerId) {
		$("#" + spolerId).find('a').click();
	}

	$(document).on('click', '.vkapp a', function () {
		newlink = '';
		link = $(this).attr('href'); //console.log(link.indexOf('template'));
		if (link == '/') link = '/catalog/';
		if (link && link != '#' && !link.indexOf('template') + 1 && !link.indexOf('.jpg') + 1) {
			newlink = link + (link.indexOf('?') + 1 ? "&" : "?") + "template=4";
			$(this).attr('href', newlink);
		}
	});


	if ($('.vkapp nav#menu li:eq(0)').text() == 'Главная') $('.vkapp nav#menu li:eq(0)').hide();

	// targeting
	$('body').on('click', '.modal-targeting li.targcookie a', function () {
		cityid = $(this).data('cityid');

		if ($(this).data('dom')) {
			domain = $(this).data('dom')
			$.cookie('city', cityid, { expires: 365, path: "/", domain: domain });
			$.cookie('cityname', '', { expires: 365, path: "/", domain: domain });
			window.location = "//" + domain;
		} else {
			const splitDomen = location.hostname.split('.').reverse();
			const mainDomen = splitDomen[1] + '.' + splitDomen[0];
			$.cookie('city', cityid, { expires: 365, path: "/", domain: mainDomen });
			$.cookie('city', cityid, { expires: 365, path: "/", domain: location.hostname });
			$.cookie('cityname', '', { expires: 365, path: "/", domain: mainDomen });
			$.cookie('cityname', '', { expires: 365, path: "/", domain: location.hostname });
			lightcase.close();
			$('body').addClass('lc-loading');
			yaCounterFunction("selcity");
			location.reload(true);
		}
		return false;
	});

	// обязательный выбор города
	if (!$.cookie('city') && $('.targeting-a').length > 0 && $('.targeting-a').hasClass('targReq')) {
		var link = $('.targeting-a').first();
		datalcoptions = link.data('lc-options');
		if (typeof datalcoptions !== 'undefined') {
			datalcoptions['closeOnOverlayClick'] = false;
			datalcoptions['groupClass'] += " noclose";
			link.data('data-lc-options', datalcoptions);
		}
		link.click();
	}

	// get parsel
	$('body').on('click', '.deliv', function () {
		if ($('#getInCity').length > 0) {
			$('#getInCity').slideToggle();
			return false;
		}
	});


	$('.mailaj a').each(function () {
		obj = $(this); d = obj.data(); metr = obj.data('metr');
		m = d.a1 + '&#64;' + d.a3 + '&#46;' + d.a2;
		obj.replaceWith("<a href='mailto:" + m + "' data-metr=" + metr + ">" + m + "</a>");
	});


	$('body').on('click', '.podbor_input, .button_green', function () {
		$(this).parent().find('input').click();
		return false;
	});

	$('body').on('click', ' .clear_filter', function (event) {
		event.stopPropagation();
		const ajaxFilterObj = $(this).parents('.ajax-filter');

		if (ajaxFilterObj.length > 0) ajaxFilterObj.addClass('cleaning');

		obj = $(this).parents('.podrob_title').length ? $(this).parents('.podbor_block') : $(this).parents('form');
		obj.find('input').prop('checked', '').removeAttr('checked').change();
		obj.find('.nice-select li:first-of-type').click().parents('.nice-select').removeClass('open');
		obj.find('.clear_inpsl').click();

		if (ajaxFilterObj.length > 0) {
			ajaxFilterObj.removeClass('cleaning');
			ajaxFilter();
		}
		return false;
	});

	//Аккардеон
	$('body').on('click', '.js-acord-head', function () {
		var options = {
			duration: 200,
			progress: function () {
				lightcase.resize();
			}
		};
		if (!$(this).is('.js-acord-none')) {
			$(this).addClass('js-acord-none ');
			$(this).addClass('ad-accord-azat');
			$(this).parent().find('.js-acord-body').slideUp(options);
		} else {
			$(this).removeClass('js-acord-none ');
			
			$(this).parent().find('.js-acord-body').slideDown(options);
		}
	});

	// показать/скрыть блоки
	$('body').on('click', '[data-opt]', function () {
		id = $(this).data('opt');

		$('[data-optbody]')
			.addClass('none')
			.hide();

		$('[data-optbody="' + id + '"]')
			.removeClass('none')
			.show();

		// cart select option
		$('select.select-style').each(function () {
			var select = $(this);
			var option = select.find('option:selected');
			if (!option.is('[data-optbody]') || option.data('optbody') == id) return;
			select.find('option:not([data-optbody="' + id + '"])').first().prop('selected', true);
			select.trigger('change');
			select.niceSelect('update');
		});
		$('input[type="radio"]').parents('[data-optbody]').each(function () {
			var optBody = $(this);
			var input = optBody.find('input[type="radio"]');
			if (optBody.data('id') == id || !input.prop('checked')) return;
			$('input[type="radio"][name="' + input.attr('name') + '"]').each(function () {
				var optBody = $(this).parents('[data-optbody]:first');
				if (optBody.length && optBody.data('optbody') != id) return;
				$(this).prop('checked', true).trigger('change');
				return false;
			});
		});
	});

	$(document).on('click', '.toggle', function () {
		var data = $(this).data();
		if (data.target) $(data.target).toggleClass(data.toggle);
		else if (data.siblings) $(this).siblings(data.siblings).toggleClass(data.toggle);
		else if (data.parents) $(this).parents(data.parents).toggleClass(data.toggle);
	});
	$(document).on('click.outside-close', function (e) {
		var list = $('.outside-close').not($(e.target).closest('.outside-close'));
		if (list.length) {
			list.each(function () {
				$(this).removeClass($(this).data('closeclass'));
			});
		}
	});

	setTimeout(function () {
		$('.catalog-items:not(.owl-carousel)').each(function () {
			miniCardHeightFunc($(this));
		})
	}, 10);
	setTimeout(function () {
		$('.catalog-items:not(.owl-carousel)').each(function () {
			miniCardHeightFunc($(this), "update");
		})
	}, 400);
	/**** END HEIGHT ELEMENT IN CARD ****/

	$('.sub-tags-block').each(function () { $(this).subTags(); });

	$('body').append("<a id='adm' href='/profile/?isNaked=1' title='Авторизация' data-rel='lightcase' data-lc-options='{\"maxWidth\":320,\"groupClass\":\"login\"}'></a>");

	// подбор по авто
	getlist = (function (name, value) {
		console.log(name);
		form = $('#podborautoform');
		if (name == 'models') form.find('#models, #year, #modification').prop('disabled', true).attr('disabled', 'disabled').html("<option value=''>не выбрано</option>");
		form.find('#' + name).addClass('disabled').find('option:first').text('Загрузка...');
		$.get("/index/podbor/", name + "=" + value + "&ajax=1&isNaked=1", function (data) {
			form.find('#' + name).removeClass('disabled').prop('disabled', false).html(data.list);
		}, "json");
	});

}); // end ready

/**** HEIGHT ELEMENT IN CARD ****/
function miniCardHeightFunc(el, type) {
	second = third = first = 0;
	if (type == 'update') {
		$(".blk_first, .blk_second, .blk_third").height('auto')
	}
	if (!el.hasClass('catalog-items-list') && !(el.hasClass('owl-carousel') && !el.hasClass('owl-loaded'))) {
		el.find('.catalog-item').each(function () {
			first_h = $(this).find('.blk_first').height();
			if (first < first_h) first = first_h;
			second_h = $(this).find('.blk_second').height();
			if (second < second_h) second = second_h;
			third_h = $(this).find('.blk_third').height();
			if (third < third_h) third = third_h;
		});
		el.find('.catalog-item .blk_first').height(first);
		el.find('.catalog-item .blk_second').height(second);
		el.find('.catalog-item .blk_third').height(third);
	}
}

// load blocks ajax
function dataload() {
	$("[data-load]:not(.data-loaded):not(:hidden)").each(function (i) {
		// no filter in mobile
		if ($(this).parents('.class2041').length && (screenSize == 'mobile' || $('body').hasClass('mobile') || $('body').hasClass('class2001obj'))) return;

		$(this).addClass("loading");

		window["dataload" + i] = $(this);
		url = window["dataload" + i].data('load');

		// find у раздела для фильтра
		if ($("[data-find]").length) url += "&subfind=" + $("[data-find]").attr("data-find");

		$.post(url, { isNaked: 1 }, function (data) {
			window["dataload" + i].removeClass('loading').html(data);
			if (!window["dataload" + i].parents('.class2041')) window["dataload" + i].removeAttr('data-load')
			window["dataload" + i].addClass('data-loaded');

			if (window["dataload" + i].hasClass('filtrLoad') && window["dataload" + i].is(':visible')) {
				sliderRange();
				window["dataload" + i].find('.scrollbar-inner, .scrollbar-card').scrollbar();
				window["dataload" + i].find('.select-style').niceSelect();
			}
		});
	});
}

// count item in line block
function countitemsParamAll() {
	countitemsParam({ type: 'subdivision' });
	countitemsParam({ type: 'catalog' });
	countitemsParam({ type: 'gallery' });
	countitemsParam({ type: 'vendor' });
	countitemsParam({ type: 'advantage' });
	countitemsParam({ type: 'portfolio' });
	countitemsParam({ type: 'news' });
	countitemsParam({ type: 'gencomponent' });
}

function countitemsParam(json) {
	$('.' + json.type + '-items').each(function () {
		el = $(this);
		// IE
		if (getInternetExplorerVersion(['ie', 'edge'])) el.addClass('count-ie-fix')

		if (!el.hasClass('owl-carousel') && (el.data('sizeitem') >= 0 || el.data('sizeitem')) && el.data('margin') >= 0) {
			count = coutItem(el);
			if (el.is('[data-masonry]') && el.find(">*").length) {
				el.responsivegrid({
					column: count,
					gutter: el.data('margin') + "px",
					itemHeight: el.data('sizeimage') > 0 ? el.data('sizeimage') : '100',
					itemSelector: '.' + el.find('*:first-child').attr("class").split(' ')[0]
				}).attr('data-calculated', '1');
			} else {
				if (!el.hasClass('count-' + json.type + '-' + count)) {
					for (var i = 12; i > 0; i--) el.removeClass('count-' + json.type + '-' + i);
					el.addClass('count-' + json.type + '-' + count).attr('data-calculated', '1');
				}
			}
		}
	});
}
function coutItem(el) {
	wcard = el.data('sizeitem');
	wmargin = el.data('margin') >= 0 ? el.data('margin') : 15;

	if (el.find('.owl-stage-outer').length) {
		wblock = el.find('.owl-stage-outer').width();
	} else if (el.hasClass('subdivision-items') || el.hasClass('owl-carousel')) {
		wblock = el.width();
	} else {
		wblock = el.find('.obj').parent().width();
	}

	var count = 0;
	wcardString = "" + wcard;
	if (~wcardString.indexOf(":")) {
		var w = $(window).width();
		wcardString.split('.').reverse().forEach(function (val) {
			value = val.split(':');
			if (w > value[0] || !count) count = value[1];
		});
	} else {
		for (w = wcard; w <= wblock; ++count) w += wcard + wmargin;
		el.parents('.blocks').find('.block_slide_nav span').text(count);
	}
	return parseInt(count) > 0 ? parseInt(count) : 1;
}
// PHOTO SLIDER INCARD
function mainPhoto() {
	mainphoto = $(".owl-incard.owl-carousel:not(.owl-loaded), .portfolio-photo.owl-carousel:not(.owl-loaded), .gencomponent-photo.owl-carousel:not(.owl-loaded), .news-photo.owl-carousel:not(.owl-loaded)");
	if (mainphoto.length) {
		mainphoto.each(function (i) {
			var photo = $(this);
			nav = photo.hasClass('news-photo') || false;
			portfolio = photo.hasClass('portfolio-photo') || false;

			let isAutoplay = true;
			if (photo.attr('data-autoplay') === 'disable') {
				isAutoplay = false;
			}

			photo.owlCarousel({
				items: 1,
				margin: 5,
				nav: nav,
				dots: portfolio,
				autoplay: isAutoplay,
				loop: true,
				autoplayTimeout: 6000,
				smartSpeed: 600,
				autoHeight: portfolio,
				onInitialize: function () {
					// add 'active' class first img
					var miniImg = $('.g_m_img');
					if (miniImg.length && !miniImg.hasClass('active')) miniImg.first().addClass('active');
				},
				onTranslate: function (event) {
					// add 'active' class on dragged
					mini = photo.parent().parent().find('.g_m_img');
					if (mini.length) {
						mini.removeClass("active");
						mini.filter('[data-val="' + event.item.index + '"]').addClass("active");
					}
				}
			});
			$("body").on("click", ".g_m_img", function () {
				mini = $(this);
				var v = mini.attr('data-val');
				mini.parent().parent().find(".owl-carousel").data('owl.carousel').to(v);
				return;
			});
			setTimeout(function () {
				$(".owl-incard .owl-item.cloned a").each(function () {
					a = $(this)
					a.attr("data-rel", a.attr("data-rel") + "-clone")
				})
				mainphoto = $(".portfolio-photo.owl-carousel.owl-loaded");
				if (mainphoto.length) {
					mainphoto.each(function (i) {
						$(this).data("owl.carousel").refresh();
					});
				}
				for (let element of document.querySelectorAll('.owl-item.cloned')) {
					element.querySelector('a').removeAttribute('data-rel');
				}
			}, 800);
		});
	}
	lightcase.resize();
}

// isNumber
function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}
// склонения численый
function declOfNum(number, titles) {
	cases = [2, 0, 1, 1, 1, 2];
	return titles[(number % 100 > 4 && number % 100 < 20) ? 2 : cases[(number % 10 < 5) ? number % 10 : 5]];
}
function processJson(data) {
	let result;
	let form;
	if (typeof data !== 'object') {
		var resultData = parseJson(data);
		if (resultData !== false) data = resultData;
		else if (typeof data === 'string' && data.indexOf('nc_error') != -1) {
			var resultData = $(data).find('#nc_error');
			data = {
				error: resultData.text()
			}
		}
	}
	if (typeof submit != "undefined") {
		submit.removeClass('disabled').prop('disabled', false);
		if (typeof form == "undefined") form = submit.parents("form")
	}
	result = (data.target ? $(data.target) : (typeof submit != "undefined" ? form.find('.result').hide() : null));
	yaCounterData = typeof form !== 'undefined' && form.length ? form.data("metr") || "" : "";

	if (data.form) {
		form = data.form;
		result = form.find('.result').hide();
	}
	// ответ окна подтверждения
	result = $('#lightcase-case #сonfirm-actions .result').length ? $('#lightcase-case #сonfirm-actions .result') : result;
	if ($('#lightcase-case #сonfirm-actions').length && typeof data.error === "undefined" && typeof data.succes === "undefined") {
		lightcase.close();
		if ($('body').is('.showsettings')) {
			if (typeof reloadTab === "function") reloadTab();
		} else {
			location.reload(true);
		}
	}

	if (data.error) { // ошибка
		if (typeof form != "undefined") {
			result.empty().append('<div class=warnText>' + (data.title ? "<b>" + data.title + ":</b> " : "") + data.error + '</div>').fadeIn();
			if (data.reload) {
				setTimeout(function () {
					location.reload(true);
				}, 4000);
			} else {
				setTimeout(function () {
					result.html('');
					lightcase.resize();
				}, 8000);
			}
		}
	} else if (data.succes) { // все гуд
		yaCounterFunction(yaCounterData);
		console.log(result);
		if (data.purchase_items) yaDataLayer('purchase', { items: data.purchase_items, id: data.transaction })
		if (result != null && result.length && !data.submodal) result.html('<div class=okText>' + data.succes + '</div>').fadeIn();

		if (data.reloadtab && typeof reloadTab === "function") reloadTab(); // good
		if (data.modal) lightcase.close(); // good

		if (data.openModalEdit) {
			lightcase.set('speedOut', 0);
			lightcase.close();
			setTimeout(function () {
				lightcase.start(
					{
						href: data.openModalEdit,
						maxWidth: 950,
						groupClass: "modal-edit",
						title: "Изменение раздела №" + data.idsub
					}
				);
			}, 100);
		} // good

		if (data.reload) location.reload(true); // good
		if (data.submodal) { // открыть окно с сообщением
			confirmlight(data);
			lightcase.start({ href: '#сonfirm-actions', maxWidth: 500, showTitle: false, groupClass: "modal-succes" });
		}
		if (data.field == "clear" && typeof form != "undefined") { // очистка формы
			form.find('textarea, input[type=text], input[type=number], input[type=password]').val('');
			form.find('input[type=checkbox]').prop('checked', false);
		}
		if (data.todo == 'clearcart') clearcartfunc();


		if (data.redirect) location.href = data.redirect;
		if (data.cleartab) ClearTab(data.cleartab);
		if (data.todo == 'ReloadCSSLink') DeveloperTool.ReloadCSSLink($("[href*='/bc_custom.css']")[0]);
		if (data.todo == 'ReloadSiteMap') ReloadSiteMap();
		if (data.reloadbtn) $('.bc_links-update').addClass('active');

	}

	lightcase.resize();
}





// LOADING AND BG
var load = {
	statusBefore: 'end',
	type: '',
	open: 0,
	itemActive: '',
	objects: {},
	self: {},

	// loading
	paramStart: {
		'transition': '250ms ease',
		'opacity': 0.7
	},
	paramEnd: {
		'transition': '250ms ease',
		'opacity': 0
	},
	before: function () {
		if ($("#load-loading").length < 1) {
			$('body').append(load.objects.overlay = $('<div id="load-overlay"></div>'), load.objects.loading = $('<div id="load-loading"></div>'));
			load.objects.overlay.click(function () {
				if (load.type == 'bg' && load.open) {
					load.close();
					load.allItemClose();
				}
			});
		}
	},
	loading: function (param) {
		load.type = 'loading';
		load.activateBg(param);
	},
	bg: function (param) {
		load.type = 'bg';
		load.activateBg(param);
	},
	activateBg: function (param) {
		load.before();
		if (param == "start") {
			if (!load.open) {
				load.objects.overlay.show();
				setTimeout(function () {
					load.objects.overlay.css(load.paramStart);
				}, 15);
			}
			if (load.type == "loading") {
				load.objects.loading.show();
			}
			if (load.type == "bg") {
				load.objects.loading.hide();
			}
			load.open = 1;
		}
		if (param == "end") load.close();
	},
	close: function () {
		if (load.open) {
			load.before();

			load.objects.overlay.css(load.paramEnd);
			setTimeout(function () {
				load.objects.loading.hide();
				load.objects.overlay.hide();
			}, 250);

			load.open = 0;
		}
	},
	closeAll: function () {
		load.before();
		var overlay = $(".js-overlay");
		var loading = $(".js-loading");
		overlay.css(load.paramEnd);
		setTimeout(function () {
			loading.hide();
			overlay.hide();
		}, 250);

		load.open = 0;
	},
	triggerClose: function (overlay, container) {
		if (overlay.length) {
			overlay.click(function () {
				load.closeAll();
				load.itemLoad(container, "end");
				load.allItemClose();

				// фича
				$('body').removeClass('menu-btn-active');
				$('.menu-btn-open').removeClass('menu-btn-open');

			});
		}
	},


	// item open/close
	itemLoad: function (el, param) {
		if (param.indexOf("start") != -1) {
			overlay = el.find(".js-overlay");
			loading = el.find(".js-loading");

			if (overlay.length < 1) {
				el.append(overlay = $('<div class="js-overlay"></div>'));
				if (param != "start-bg") el.append(loading = $('<div class="js-loading"></div>'));
			}

			overlay.show();
			if (param != "start-bg") loading.show();
			setTimeout(function () {
				overlay.css(load.paramStart);
			}, 15);
			return overlay;
		}
		if (param == "end") {
			if (el.find(".js-overlay").length) {
				var overlay = el.find(".js-overlay");
				var loading = el.find(".js-loading");
				overlay.css(load.paramEnd);
				setTimeout(function () {
					loading.remove();
					overlay.remove();
				}, 250);

			}
		}
	},
	clickItem: function (el, link) {
		$("[data-loadactive]").removeAttr("data-loadactive");
		self.link = $(link);
		if (!$(el).attr('data-loadopen')) {
			load.itemOpen(el);
			if (typeof link !== 'undefined') self.link.attr("data-loadactive", 1);
		} else {
			load.itemClose(el);
		}
	},
	itemOpen: function (el) {
		load.allItemClose();
		$(el)
			.attr('data-loadopen', '1')
			.addClass('active');
		if (!$(el).attr('data-nobg')) load.bg('start');
		// input focus
		if ($(el).find('input[type="text"]').length == 1) $(el).find('input[type="text"]').focus();
		// scroll
		// load.scroll("off");
		// load data
		if ($(el).attr('data-loaditem')) load.loadData($(el));
		if ($(el).find('[data-loaditem]')) load.loadData($(el).find('[data-loaditem]'));
	},
	itemClose: function (el) {
		$(el)
			.removeAttr('data-loadopen')
			.removeClass('active');
		load.bg('end');
		// scroll
		//load.scroll("on");
	},
	allItemClose: function (el) {
		$('[data-loadopen]').each(function () {
			$(this)
				.removeAttr('data-loadopen')
				.removeClass('active');
		});
		// load.scroll("on");
	},
	loadData: function (el) {
		load.itemLoad(el.parent(), "start");
		el.css('opacity', 0);
		$.get(el.attr('data-loaditem'), function (data) {
			el.removeAttr('data-loaditem')
				.html(data)
				.css({ opacity: 1, transition: '200ms ease' });
			// callback
			if (typeof self.link !== 'undefined'
				&& self.link.attr('data-callback')
				&& typeof window[self.link.attr('data-callback')] === 'function') { window[self.link.attr('data-callback')](el); }

			load.itemLoad(el.parent(), "end");
			el.find(".tabs").each(function () {
				$(this).menuMaterial();
			});
		}, "html");
	},

	// scroll on/off
	scroll: function (param) {
		if (param == "off") {
			var winScrollTop = $(window).scrollTop();
			$(window).bind('scroll', function () {
				$(window).scrollTop(winScrollTop);
			});
		}
		if (param == "on") {
			$(window).unbind('scroll');
		}
	},
	scrollBefore: function (element) {
		element.attr('data-scrollTop', element.scrollTop());
	},
	scrollTo: function () {
		scrollTop = $('[data-scrollTop]');
		if (scrollTop.length) {
			scrollTop.each(function () {
				scrollTop = $(this).attr('data-scrollTop');
				if (scrollTop > 0) $(this).animate({ 'scrollTop': scrollTop }, 300);
				$(this).removeAttr('data-scrollTop');
			});
		}
	}
};


/* MENU */
/*$('body').on('click', '.mpanel-menu', function(){
	menu = $('#mobile-menu');
	if(!menu.hasClass('active')){ // открыть
		load.bg('start');
		$('#mobile-menu').addClass('active');
		menubody = $('.mobile-menu-body');
		scroll("off");
		closeElements('menu');
	}else{ // закрыть
		$('#mobile-menu').removeClass('active');
		scroll("on");
		load.close();
	}
});
$('body').on('click', '.menu-close', function(){
	$('#mobile-menu').removeClass('active');
	scroll("on");
});*/

function getAllUrlParams(url) {

	var queryString = url ? url.split('?')[1] : window.location.search.slice(1);

	var obj = {};

	if (queryString) {

		queryString = queryString.split('#')[0];

		var arr = queryString.split('&');

		for (var i = 0; i < arr.length; i++) {
			var a = arr[i].split('=');

			var paramNum = undefined;
			var paramName = a[0].replace(/\[\d*\]/, function (v) {
				paramNum = v.slice(1, -1);
				return '';
			});

			var paramValue = typeof (a[1]) === 'undefined' ? true : a[1];

			//	paramName = paramName.toLowerCase();
			//	paramValue = paramValue.toLowerCase();

			if (obj[paramName]) {
				if (typeof obj[paramName] === 'string') {
					obj[paramName] = [obj[paramName]];
				}
				if (typeof paramNum === 'undefined') {
					obj[paramName].push(paramValue);
				} else {
					obj[paramName][paramNum] = paramValue;
				}
			} else {
				obj[paramName] = paramValue;
			}
		}
	}

	return obj;
}










/*******************
 *  Menu Material  *
 ******************/
$.fn.menuMaterial = function () {
	this.each(function () {
		tabs = $(this);

		if (tabs.hasClass('tabs-hash') && window.location.hash && tabs.find("a[href='" + window.location.hash + "']").length) {
			tabs.find('li>a').removeClass('active');
			aActive = tabs.find("a[href='" + window.location.hash + "']").addClass('active');
		} else if (tabs.find('a.active').length) aActive = tabs.find('a.active');
		else aActive = tabs.find('li:first-of-type>a').addClass('active');

		tabs.find('li>a:not(.active):not([data-rel])').each(function () {
			if ($(this).attr("href").length && $($(this).attr("href")).length) $($(this).attr("href")).hide();
		});

		$(aActive.attr('href')).show().parent().find().hide();

		border(tabs, aActive);
	});

}
function border(tabs, aActive) {
	if (tabs.hasClass('tabs-border')) {
		t = 0;
		if (!tabs.find('.t-border').length) {
			tabs.append("<div class='t-border'></div>"); t = 200;
		}
		setTimeout(function () {
			tabs.find('.t-border').stop().css({
				left: aActive[0].offsetLeft,
				width: aActive.width()
			});
		}, t);
	}
}
$(document).on('click', '.tabs a:not([data-rel])', function (event) {
	a = $(this);
	tabs = a.closest('.tabs');
	href = $(a.attr('href'));
	link = a.data('link') ? a.data('link') : "";
	time = tabs.attr("data-time") ? tabs.attr("data-time") : 300;

	if (link && !a.hasClass('loaded')) {
		loadingView("start", null, 150);

		$.get(link, { 'template': '4' }, function (data) {
			if ($(data).find('.view-body-inline').length || $(data).hasClass('view-body-inline')) href.parents('.view-body').addClass('inner-body');
			href.html(data);
			href.find('input.color').color();
			if (typeof formBuilder === "function" && href.find(".colline.type-15").length) formBuilder(href);
			if (typeof collineSelectNumber === "function" && href.find(".colline:not(.select-number) [name*='_select']").length) collineSelectNumber();
			href.find('select').niceSelect();
			loadingView("end", null, 150);
		});

		a.addClass('loaded');
	}

	tabs.find('li>a.active').removeClass('active');
	$(this).addClass('active');

	tbs = href.closest('.tabs-body').find('> *');
	tbs.stop().fadeOut(time, function () {
		$(this).removeClass('active');
	}).hide();

	href.stop().fadeIn(time, function () {
		$(this).addClass('active');
		dataload();
	});

	href.parent().attr('data-active', a.attr('href'));

	border(tabs, $(this));

	if ($('#lightcase-case').data("lcType")) lightcase.resize();

	event.preventDefault();

});

/*******************
 *  Select Plugin  *
 ******************/
$.fn.niceSelect = function (method) {
	// Methods
	if (typeof method == 'string') {
		if (method == 'update') {
			this.each(function () {
				var $select = $(this);
				var $dropdown = $(this).next('.nice-select');
				var open = $dropdown.hasClass('open');
				if ($dropdown.length) {
					$dropdown.remove();
					create_nice_select($select);
					if (open) {
						$select.next().trigger('click');
					}
				}
			});
		} else if (method == 'destroy') {
			this.each(function () {
				var $select = $(this);
				var $dropdown = $(this).next('.nice-select');
				if ($dropdown.length) {
					$dropdown.remove();
					$select.css('display', '');
				}
			});
			if ($('.nice-select').length == 0) {
				$(document).off('.nice_select');
			}
		} else {
			console.log('Method "' + method + '" does not exist.')
		}
		return this;
	}
	// Hide native select
	this.hide();
	// Create custom markup
	this.each(function () {
		var $select = $(this);
		if (!$select.next().hasClass('nice-select')) {
			create_nice_select($select);
			$select.addClass('none-important')
		}
	});

	function create_nice_select($select) {
		var $input_search = $select.hasClass('select-search') ? "<div class='select-search-wrapper'><input type='text' placeholder='Поиск' class='search-inp'></div>" : "";
		$select.after($('<div></div>').addClass('nice-select').addClass($select.attr('class') || '').addClass($select.attr('disabled') ? 'disabled' : '').attr('tabindex', $select.attr('disabled') ? null : '0').html('<span class="current"><span></span></span><div class="list">' + $input_search + '<ul class="list-ul"></ul></div>'));
		var $dropdown = $select.next();
		var $options = $select.find('> *');
		var $selected = $select.find('option:selected');
		$dropdown.find('.current span').html($selected.data('display') || $selected.data('name') || $selected.html()).addClass($selected.attr('class'));
		$options.each(function (i) {
			var $option = $(this);
			switch ($option.context.localName) {
				case 'optgroup':
					$dropdown.find('ul').append('<li class="option group-name"><span class="title">' + $option.attr('label') + '</span></li>');
					$option.find('option').each(function () {
						$dropdown.find('ul').append(nice_select_option_struct($(this), $select.hasClass('select-lists'), true));
					});
					break;
				case 'option': {
					if (i == 0 && $input_search && $option.text().indexOf('Выберите') != -1) { }
					else $dropdown.find('ul').append(nice_select_option_struct($option, $select.hasClass('select-lists'), false));
				}
					break;
				default: break;
			}
		});
		$dropdown.removeClass('none-important');
		$('.select-search-wrapper').find("input").on("keyup click", function () {
			var input = $(this),
				text = ((input[0].value) ? (input[0].value).toLowerCase() : '');
			var li = input.parents(".nice-select").find("li");
			for (var i = 0; i < li.length; i++) {
				var item = $(li[i]);
				item.text().toLowerCase().indexOf(text) != -1 ? item.removeClass('none') : item.addClass('none');
			}
		});
	}
	function nice_select_option_struct($option, $selectLists, $inGroup) {
		var display = $option.data('display');
		var $li = $("<li></li>")
			.attr('data-value', $option.val())
			.attr('data-link', $option.data('link'))
			.attr('data-url', $option.data('url'))
			.attr('data-id', $option.data('id'))
			.attr('data-optbody', $option.data('optbody'))
			.attr('data-ncctpl', $option.data('ncctpl'))
			.attr('data-display', (display || null))
			.addClass('option' + ($option.is(':selected') ? ' selected' : '') + ($option.is(':disabled') ? ' disabled' : '') + ($inGroup ? " group-in" : ""))
			.addClass($option.attr("class"))
			.html($option.text());
		if ($selectLists) {
			$li.html('').append($('<div class="select-name"><span class="select-name-line">' + $option.data('name') + ' </span></div>'));
			if ($option.data('price') !== undefined && ('' + $option.data('price')).length) $li.find('.select-name').append($('<span class="select-price"></span>').html($option.data('price')));
			if ($option.data('text') !== undefined && $option.data('text').length) $li.append($('<div class="select-text"></div>').text(' ' + $option.data('text')));
		}
		return $li;
	}
	$(document).off('.nice_select');
	// Open/close
	$(document).on('click.nice_select', '.nice-select:not(.disabled)', function (event) {
		var $dropdown = $(this);
		$('.nice-select').not($dropdown).removeClass('open');

		/*if(!$dropdown.hasClass('open')){
			$dropdown.removeClass('niceVertical');
			list = $dropdown.find('.list');
			h = list.outerHeight();
			hTop = list.offset().top;
			hWindow = $(window).height();
			if(hWindow < (hTop + h)){
				$dropdown.addClass('niceVertical');
			}else{
				$dropdown.removeClass('niceVertical');
			}
		}*/
		if ($(event.target).hasClass('search-inp')) return false;
		$dropdown.toggleClass('open');

		if ($dropdown.hasClass('open')) {
			$dropdown.find('.option');
			$dropdown.find('.focus').removeClass('focus');
			$dropdown.find('.selected').addClass('focus');
		} else {
			$dropdown.focus();
		}
	});
	// Close when clicking outside
	$(document).on('click.nice_select:not(.search-inp)', function (event) {
		if ($(event.target).hasClass('search-inp')) return false;
		if ($(event.target).closest('.nice-select').length === 0) {
			$('.nice-select').removeClass('open').find('.option');
		}
	});
	// Option click
	$(document).on('click.nice_select', '.nice-select .option:not(.disabled)', function (event) {
		var $option = $(this);
		var $dropdown = $option.closest('.nice-select');
		$dropdown.find('.selected').removeClass('selected');
		$option.addClass('selected');
		var text = $option.data('display') || $option.data('name') || $option.html();
		var classSelect = $option.attr('class');
		$dropdown.find('.current span').html(text).removeClass().addClass(classSelect.replace('option', '').replace('selected', ''));
		if (!$option.parents(".item-full-fastprew").length) $dropdown.prev('select').val($option.data('value')).trigger('change');
		if ($option.parents('.catalog-item').length == 0 && $option.data('url')) window.location = $option.data('url');
	});
	// Keyboard events
	$(document).on('keydown.nice_select', '.nice-select', function (event) {
		var $dropdown = $(this);
		var $focused_option = $($dropdown.find('.focus') || $dropdown.find('.list .option.selected'));
		// Space or Enter
		if (event.keyCode == 32 || event.keyCode == 13) {
			if ($dropdown.hasClass('open')) {
				$focused_option.trigger('click');
			} else {
				$dropdown.trigger('click');
			}
			return false;
			// Down
		} else if (event.keyCode == 40) {
			if (!$dropdown.hasClass('open')) {
				$dropdown.trigger('click');
			} else {
				var $next = $focused_option.nextAll('.option:not(.disabled)').first();
				if ($next.length > 0) {
					$dropdown.find('.focus').removeClass('focus');
					$next.addClass('focus');
				}
			}
			return false;
			// Up
		} else if (event.keyCode == 38) {
			if (!$dropdown.hasClass('open')) {
				$dropdown.trigger('click');
			} else {
				var $prev = $focused_option.prevAll('.option:not(.disabled)').first();
				if ($prev.length > 0) {
					$dropdown.find('.focus').removeClass('focus');
					$prev.addClass('focus');
				}
			}
			return false;
			// Esc
		} else if (event.keyCode == 27) {
			if ($dropdown.hasClass('open')) {
				$dropdown.trigger('click');
			}
			// Tab
		} else if (event.keyCode == 9) {
			if ($dropdown.hasClass('open')) {
				return false;
			}
		}
	});
	// Detect CSS pointer-events support, for IE <= 10. From Modernizr.
	var style = document.createElement('a').style;
	style.cssText = 'pointer-events:auto';
	if (style.pointerEvents !== 'auto') {
		$('html').addClass('no-csspointerevents');
	}
	return this;
};

function yaDataLayer(type, param) {
	if (!('' + $('body').data('metrikaid')).length) return
	window.dataLayer = window.dataLayer || []
	var actionType = false;
	var bread = getBread()
	switch (type) {
		case 'detail':
			var selector = (typeof param === 'object' && param.from == 'lightcase' ? '#lightcase-case ' : '.zone-content ') + '.catalog-item-full'
			var item = $(selector)
			if (!item.length) return
			var product = {
				id: item.data('id'),
				name: item.data('origname'),
				price: item.data('price')
			}
			if (bread.length) product.category = bread.join('/').replace(/ {2,}/g, '  ').replace(/\s{2,}/g, '')
			actionType = { products: [product] }
			if (item.find('.cart-param-vendor .cart-param-body').length) actionType.products.brand = item.find('.cart-param-vendor .cart-param-body').text()
			break
		case 'changeItem':
			if (typeof param !== 'object' || param.count == 0) return
			var product = {
				id: param.id,
				name: param.name,
				price: +param.price,
				quantity: Math.abs(+param.count)
			}
			if (bread.length) product.category = bread.join('/').replace(/ {2,}/g, '  ').replace(/\s{2,}/g, '')
			actionType = { products: [product] }
			type = +param.count > 0 ? 'add' : 'remove'
			break
		case 'clearCart':
			if (typeof param !== 'object' || !param.length) return
			type = 'remove'
			actionType = { products: [] }
			for (var i = 0; i < param.length; i++) {
				var product = {
					id: param[i].id,
					name: param[i].name,
					price: +param[i].price,
					quantity: +param[i].count
				}
				if (bread.length) product.category = bread.join('/').replace(/ {2,}/g, '  ').replace(/\s{2,}/g, '')
				actionType.products.push(product)
			}
			break
		case 'purchase':
			if (typeof param !== 'object' || !param.items.length) return
			actionType = { actionField: { id: param.id }, products: [] }
			for (var i = 0; i < param.items.length; i++) {
				var product = {
					id: param.items[i].id,
					name: param.items[i].name,
					price: +param.items[i].price,
					quantity: +param.items[i].count
				}
				if (bread.length) product.category = bread.join('/').replace(/ {2,}/g, '  ').replace(/\s{2,}/g, '')
				actionType.products.push(product)
			}
			break
		default: console.log('f(yaDataLayer) неверный тип: ' + type)
			break
	}

	if (actionType !== false) {
		var simbolsLimit = 1948,
			i = 0,
			next = false;

		do {
			var actionClone = Object.assign({}, actionType, { products: [] });
			i++;
			next = false;

			do {
				var isToMachSiblos = JSON.stringify(actionClone).length + JSON.stringify(actionType.products[0]).length > simbolsLimit;
				if (isToMachSiblos) {
					next = true;
					break;
				}
				actionClone.products.push(actionType.products[0]);
				actionType.products.shift();
			} while (actionType.products.length);

			if (next && typeof actionType.actionField !== 'undefined') {
				actionClone.actionField = Object.assign({}, actionType.actionField);
				actionClone.actionField.id += '-' + i;
			}

			var obj = { ecommerce: {} };
			obj.ecommerce[type] = actionClone;

			window.dataLayer.push(obj);
		} while (next);
	}
}
function getBread() {
	var bread = []
	$('.xleb-default-item, .xleb-item').each(function (key, item) {
		bread.push($(this).text())
	})
	return bread;
}

function insertImg(img, file) {
	if (img.constructor.name !== "HTMLImageElement" || file.type.indexOf('image') == -1) return false
	img.setAttribute('src', URL.createObjectURL(file))
	return true
}
function checkImgLoaded(img, callback) {
	if (isImageLoaded(img)) {
		if (typeof callback === 'function') callback()
	} else setTimeout(function () { checkImgLoaded(img, callback) }, 10)
}
function isImageLoaded(img) {
	if (!img.complete || (typeof img.naturalWidth !== "undefined" && img.naturalWidth === 0)) return false
	return true
}

/* живой подсчет в фильтре */
$(document).on('change', '.filter-form select, .filter-form input', function () {
	var flt = $(this).parents('form.filter-form:first');
	if (!flt.hasClass('live-count')) return;

	if (typeof window.fltgetcount !== 'undefined') clearTimeout(fltgetcount);

	window.fltRequestCheck = Date.now();
	var fltCheckTime = Date.now();

	fltgetcount = setTimeout(function () {
		if (!checkFilterValues(flt)) {
			flt.addClass('no-selected');
		} else {
			var loadUrl = flt.parents('[data-load]:first').data('load').split('?');
			var get = '';
			$.each(loadUrl[1].split('&'), function () {
				if (this.indexOf('flt') === -1) get += (get ? '&' : '') + this;
			});
			if (window.fltRequestCheck > fltCheckTime) return false;
			$.get(loadUrl[0], get + '&' + flt.serialize() + '&method=getcount', function (data) {
				if (window.fltRequestCheck > fltCheckTime) return false;
				var btn = flt.find('a.podbor_add');
				btn.find('.live-count-val').text(data);

				if (+data) btn.removeClass('disabled');
				else btn.addClass('disabled');

				flt.removeClass('no-selected');
			});
		}
	}, 100);
});
checkFilterValues = (function (flt) {
	var result = false;
	$.each(flt.serializeArray(), function () {
		if (this.value && this.name.indexOf('flt') === 0 && this.name.indexOf('flt[params_range]') !== 0) return !(result = true);
	});
	return result;
});

$.fn.subTags = function () {
	if (!this.length) return;
	var block = $(this);
	var obj = {
		block: block,
		wrapper: block.find('.sub-tags-wrapper'),
		track: block.find('.sub-tags-track'),
		listBlock: block.find('.sub-tag-list'),
		elements: block.find('.sub-tag-wrapper'),
		links: block.find('a'),
		btnPrev: block.find('.btn-prev'),
		btnNext: block.find('.btn-next'),
		changer: block.find('.sub-tags-show-more'),
		wrapW: block.find('.sub-tags-wrapper'),
		drag: false,
		dragDefault: false,
		trackPosition: 0,
		trackGoTo: false,
		open: false,
		mouse: {},
	};
	function caclcElWidth() {
		obj.listW = 0;
		obj.elements.each(function () {
			obj.listW += $(this).outerWidth(true);
		});
	}
	function setEvents() {
		obj.block.on('dragstart', function () {
			if (obj.drag) return !(obj.dragDefault = true);
		});
		obj.block.on('click', function (e) {
			if (obj.dragDefault) {
				e.preventDefault();
				return obj.dragDefault = false;
			}
		});
		obj.changer.on('click', function () {
			obj.open = !obj.open;
			block.toggleClass('open');
			trackAnimateTransformOF();
			trackTransform(obj.open ? 0 : obj.trackPosition);
			obj.changer.text(obj.open ? obj.changer.data('textopen') : obj.changer.data('textclose'));
		});
		obj.btnPrev.on('click', function () {
			trackGoSlide('prev');
		});
		obj.btnNext.on('click', function () {
			trackGoSlide('next');
		});
		obj.track.on('mousedown touchstart', function (e) {
			if (!obj.block.hasClass('active') || obj.open) return;
			obj.drag = true;
			if (event.type == 'touchstart') {
				obj.mouse.positionX = event.targetTouches[0].clientX;
			} else if (event.type == 'mousedown') {
				obj.mouse.positionX = e.clientX;
			}
			obj.block.addClass('draged');
			trackAnimateTransformOF();
		});
		$(document).on('mousemove touchmove', function (e) {
			if (!obj.drag) return;
			var pos = false;
			if (event.type == 'mousemove') {
				pos = e.clientX;
			} else if (event.type == 'touchmove') {
				pos = event.targetTouches[0].clientX;
			}
			if (isNumber(pos)) {
				moveTrack(pos - obj.mouse.positionX);
				trackTransform(obj.trackPosition);
				obj.mouse.positionX = pos;
			}
		});
		$(document).on('mouseup touchend', function (e) {
			obj.drag = false;
			obj.block.removeClass('draged');
		});
	}
	function trackDragStart(type, e) {
		if (!obj.block.hasClass('active') || obj.open) return;
		var pos = false;
		if (type == 'mouse') {
			pos = e.clientX;
		} else if (type == 'touch') {
			pos = e.targetTouches[0].clientX;
		}
		if (!isNumber(pos)) return;
	}
	function moveTrack(move) {
		setTrackPos(obj.trackPosition + move);
	}
	function setTrackPos(pos) {
		obj.trackPosition = checkTrackLimit(pos);
	}
	function checkTrackLimit(pos) {
		if (pos > 0) pos = 0;
		if (pos < -1 * (obj.listW - obj.wrapper.width())) pos = -1 * (obj.listW - obj.wrapper.width());
		return pos;
	}
	function trackTransform(pos) {
		obj.track.css({ transform: 'translateX(' + pos + 'px)' });
		setBtnStatus(pos);
	}
	function setBtnStatus(pos) {
		obj.btnPrev.removeClass('disabled');
		obj.btnNext.removeClass('disabled');
		if (pos == 0) obj.btnPrev.addClass('disabled');
		if (pos == -1 * (obj.listW - obj.wrapper.width())) obj.btnNext.addClass('disabled');
	}
	function trackGoSlide(type) {
		if (obj.trackGoTo === false) obj.trackGoTo = obj.trackPosition;
		var pos = null;
		var el = [];
		if (type == 'prev') el = obj.elements.get().reverse();
		else if (type == 'next') el = obj.elements;
		$.each(el, function () {
			var item = $(this);
			var itemW = item.width();
			var itemPosLift = -Math.floor(item.position().left);
			if (type == 'prev' && obj.trackGoTo < itemPosLift) {
				return !(pos = itemPosLift);
			} else if (type == 'next') {
				if (itemW > obj.wrapper.width()) {
					if (obj.trackGoTo > itemPosLift) return !(pos = itemPosLift);
				} else if (obj.trackGoTo - obj.wrapper.width() > itemPosLift - itemW * 0.9) {
					return !(pos = itemPosLift - itemW + obj.wrapper.width());
				}
			}
		});
		trackAnimateTransform(pos);
	}
	function trackAnimateTransform(pos) {
		if (isNumber(pos)) obj.trackGoTo = checkTrackLimit(pos);
		if (typeof window.subTagsAnimateTranform !== 'undefined') return;
		window.subTagsAnimateTranform = setInterval(function () {
			if (obj.trackPosition > obj.trackGoTo && obj.trackPosition - 15 >= obj.trackGoTo) {
				obj.trackPosition -= 15;
			} else if (obj.trackPosition < obj.trackGoTo && obj.trackPosition + 15 <= obj.trackGoTo) {
				obj.trackPosition += 15;
			} else {
				obj.trackPosition = obj.trackGoTo;
			}
			trackTransform(checkTrackLimit(obj.trackPosition));
			if (obj.trackPosition == obj.trackGoTo) trackAnimateTransformOF();
		}, 16);
	}
	function trackAnimateTransformOF() {
		if (typeof window.subTagsAnimateTranform !== 'undefined') {
			clearInterval(window.subTagsAnimateTranform);
			obj.trackGoTo = false;
			window.subTagsAnimateTranform = undefined;
		}
	}
	function checkSize() {
		obj.block.removeClass('active');
		trackTransform(0);
		if (obj.wrapper.width() < obj.listW) {
			obj.block.addClass('active');
			if (obj.open) {
				obj.block.addClass('open');
				trackTransform(0);
			} else {
				obj.block.removeClass('open');
				trackTransform(obj.trackPosition);
			}
		}
		return obj.wrapper.width() < obj.listW;
	}

	setEvents();
	caclcElWidth();
	checkSize();
	$(window).resize(function () {
		if (typeof window.resizeOnTabTags !== 'undefined') clearTimeout(window.resizeOnTabTags);
		window.resizeOnTabTags = setTimeout(function () {
			caclcElWidth();
			var check = checkSize();
			if (check && !obj.open) trackTransform(checkTrackLimit(obj.trackPosition));
		}, 16);
	});
}

function parseJson(json) {
	try {
		return JSON.parse(json);
	} catch (error) {
		return false;
	}
}

function orderFormInputChanger(type, params = {}) {
	if (type == 'delivery') {
		/*слелекторы полей адресов относящихся к доставке в оформлениях заказа (cart, в 1 клик)*/
		var fields = $("#order .person_city, #order .userline-oneclick-city"
			+ ",#order .person_address, #order .userline-oneclick-address"
			+ ",#order .person_street, #order .userline-oneclick-street"
			+ ",#order .person_home, #order .userline-oneclick-home"
			+ ",#order .person_housing, #order .userline-oneclick-housing"
			+ ",#order .person_porch, #order .userline-oneclick-porch"
			+ ",#order .person_apart, #order .userline-oneclick-apart"
			+ ",#order .person_floor, #order .userline-oneclick-floor"
			+ ",#order .person_apartment, #order .userline-oneclick-apartment");

		if (fields.length) {

			if (params.force == 'show') fields.removeClass('none-important');
			else if (params.force == 'hide') fields.addClass('none-important');
			else {
				var opt = params.obj && params.obj.is('[name="f_delivery"]') ? params.obj.children('option:selected') : $('#order [name="f_delivery"] option:selected');
				console.log(opt.data('deliverytype'))
				if (opt.length && (opt.data('deliverytype') == 2 || opt.data('deliverytype') == '')) { /*если способ доставки "доставка" включить поля доставки*/

					fields.removeClass('none-important');
				} else { /*если способ доставки не "доставка" скрыть поля доставки*/
					fields.addClass('none-important');
				}
			}
			if (fields.parents('#lightcase-case').length) lightcase.resize();
		}
	}
}

/*******************
 *   СДЕК / CDEK   *
 ******************/
function cdekStart(e) {
	if (typeof window.cdek === 'undefined') cdekInit({ call: 'cdekStart' });
	else if (!cdek.processInit) {
		if ($('.delivery-assist-blk.cdek').hasClass('choosed') && !$(event.target).hasClass('cdek-selected-change')) {
			cdekDeliveryRecalc();
		} else {
			cdekModalChooseOpen();
		}
	}
}
function cdekInit(params = {}) {
	if (typeof window.cdek === 'undefined') {
		window.cdek = {};
		cdek.processInit = true;
	}
	else if (cdek.processInit) return false;

	$.get('/bc/modules/default/index.php', { user_action: 'cdek', method: 'choose_post' }, function (data) {
		window.cdek = data;
		cdek.processInit = true;
		if (data.error) {
			cdek.modalHtml = '<p class="error">' + data.error + '</p>';
		} else {
			/* поиск городов */
			cdek.searchCity = {
				input: $('<input class="search-inp" name="cdek-city-search" value="" placeholder="' + data.text.searchCity + '" autocomplete="off">'),
				select: $('<select name="cdek-city-search" class="select-style cdek-city-list"></select>')
			};
			$.each(data.citySort, function () {
				this.selected = $.cookie('cityname') == this.name;
				cdek.searchCity.select.append('<option value="' + this.code + '" data-cx="' + this.coord.x + '" data-cy="' + this.coord.y + '"' + (this.selected ? ' selected' : '') + '>' + this.name + '</option>');
			});

			var searchCityBlock = $('<div class="cdek-search-city-wrapper"></div>')
				.append(cdek.searchCity.input)
				.append(cdek.searchCity.select);

			cdek.searchCity.select.niceSelect();
			cdek.searchCity.niceSelect = {
				block: cdek.searchCity.select.next(),
				options: cdek.searchCity.select.next().find('li')
			};

			/* map */
			var map = $('<div class="cdek-map-wrapper"></div>')
				.append('<div id="cdek-map"></div>')
				.append(searchCityBlock);

			/*Информационные панели пункт*/
			cdek.infoPanels = {
				pvz: {},
				courier: {},
				item: $('<div class="cdek-delivery-item"></div>')
					.append('<div class="delivery-time"><span class="title">' + data.text.deliveryTimeTitle + '</span><span class="value"></span></div>')
					.append('<div class="delivery-price"><span class="title">' + data.text.deliveryPriceTitle + '</span><span class="value"></span></div>')
					.append('<div class="cdek-panel-btn-wrapper"><button class="cdek-btn-choose" onclick="cdekDeliveryChoose(this)">' + data.text.chooseBtn + '</button></div>')
			}

			/* пункт выдачи */
			cdek.infoPanels.pvz = $('<div class="cdek-panel pvz-panel"></div>')
				.append('<div class="cdek-panel-name">' + data.text.pickupTitle + '</div>')
				.append('<div class="pvz-no-selected">' + data.text.pickupNoSelect + '</div>')
				.append('<div class="pvz-address"><span class="title">' + data.text.pickupAddressTitle + '</span><span class="value"></span></div>')
				.append('<div class="cdek-delivery-list-wrapper"></div>')
				.append('<div class="cantdelivery">' + data.text.cantDEliveryPickup + '</div>')
				.append('<div class="loader"></div>');

			/* доставка курьером */
			cdek.infoPanels.courier = $('<div class="cdek-panel courier-panel"></div>')
				.append('<div class="cdek-panel-name">' + data.text.courierTitle + '</div>')
				.append('<div class="city-name"><span class="title">' + data.text.cityTitle + '</span><span class="value"></span></div>')
				.append('<div class="cdek-delivery-list-wrapper"></div>')
				.append('<div class="cantdelivery">' + data.text.cantDeliveryCourier + '</div>')
				.append('<div class="loader"></div>');

			cdek.infoPanels.block = $('<div class="cdek-info-panel-wrapper"></div>')
				.append(cdek.infoPanels.courier)
				.append(cdek.infoPanels.pvz);

			/*html модального окна*/
			cdek.modalHtml = $('<div class="cdek-modal-content"></div>')
				.append(cdek.infoPanels.block)
				.append(map);

			cdek.citySort = undefined;
		}
		cdek.processInit = false;
		if (typeof window[params.call] === 'function') window[params.call]();
	}, 'json');
}
function cdekModalChooseOpen() {
	var params = {
		htmlContent: cdek.modalHtml,
		type: 'html',
		groupClass: 'modal-cdek-choose',
		maxWidth: 320,
		title: 'Выбор пункта СДЭК'
	};
	if (!cdek.error) {
		params.onFinishCall = 'cdekMapInit';
		params.maxWidth = 800;
	}
	lightcase.start(params);
}
function cdekMapInit() {
	var city = cdek.city[+cdek.searchCity.select.val()];

	if (typeof ymaps !== 'object') {
		var script = document.createElement('script');
		script.src = "https://api-maps.yandex.ru/2.1/?lang=ru_RU";
		script.onload = (function () {
			ymaps.ready(function () {
				cdekMapInit();
			});
		});
		document.body.append(script);
	} else if (typeof cdek.map === 'undefined') {
		cdek.map = new ymaps.Map('cdek-map', {
			center: [city.coord.y, city.coord.x],
			zoom: 11,
			controls: ['zoomControl']
		});
		/* Установка меток */
		cdek.mapPvz = {};
		cdek.mapPvzArr = [];
		$.each(cdek.pvz, function (key, item) {
			cdek.mapPvz[key] = new ymaps.Placemark([+item.coord.y.replace(',', '.'), +item.coord.x.replace(',', '.')], {
				hintContent: '<b>' + cdek.text[item.type] + '</b><br><span><b>' + cdek.text.workTimeTitle + '</b> ' + item.workTime + '</span>'
			}, {
				preset: 'islands#darkGreenDotIcon',
				iconColor: '#0a8c37'
			});
			cdek.mapPvz[key].events.add(['balloonopen', 'click'], function (mark) {
				if (cdek.getData) return;
				cdekMarkSelected(key);
				var prev = cdek.MapPvzSelected;
				cdek.MapPvzSelected = mark.get('target');
				try {
					prev.events.fire('mouseleave');
				} catch (e) { }
			});
			cdek.mapPvz[key].events.add(['mouseenter'], function (mark) {
				mark.get('target').options.set({ iconColor: '#333' });
			});
			cdek.mapPvz[key].events.add(['mouseleave'], function (mark) {
				if (cdek.MapPvzSelected != mark.get('target')) {
					mark.get('target').options.set({ iconColor: '#0a8c37' });
				}
			});
			cdek.mapPvzArr.push(cdek.mapPvz[key]);
		});
		cdek.clusterer = new ymaps.Clusterer({
			gridSize: 50,
			preset: 'islands#ClusterIcons',
			clusterIconColor: '#0a8c37',
			hasBalloon: false,
			groupByCoordinates: false,
			clusterDisableClickZoom: false,
			maxZoom: 11,
			zoomMargin: [45],
			clusterHideIconOnBalloonOpen: false,
			geoObjectHideIconOnBalloonOpen: false
		});
		cdek.clusterer.add(cdek.mapPvzArr);
		cdek.map.geoObjects.add(cdek.clusterer);
		cdek.mapPvz = cdek.mapPvzArr = cdek.clusterer = undefined;
	}
	cdekSetInfo(city.code);
	cdekSetEvents();
}
function cdekSetEvents() {
	if (!cdek.error) {
		cdek.searchCity.input
			.on('click', function () {
				cdek.searchCity.niceSelect.block.addClass('open');
			})
			.on('input chnage', function (e) {
				var val = $(this).val().toUpperCase();
				cdek.searchCity.niceSelect.options.each(function () {
					if (!val || $(this).text().toUpperCase().indexOf(val) > -1) $(this).show();
					else $(this).hide();
				});
			})
			.on('keypress', function (e) {
				if ($(this).val() && e.keyCode == 13) cdek.searchCity.niceSelect.options.filter(':visible:first').click();
			});
		cdek.searchCity.select.on('change', function () {
			var item = cdek.searchCity.select.find('option:selected');
			cdek.searchCity.input.val(item.text());
			cdek.map.setCenter([+item.data('cy'), +item.data('cx')], 11);
			cdekSetInfo($(this).val());
		});
	}
}
function cdekSetInfo(cityCode, params = {}) {
	var city = cdek.city[cityCode];
	var pvz = params.pvz || cdek.pvzLastSelected || false;
	cdek.pvzLastSelected = pvz;

	if (!params.second) {
		cdekCourierPanel(city);
		cdekPvzPanel(params.pvz);
	}
	if (typeof city.price === 'undefined') {
		cdekSetCityPrice(cityCode, { call: 'cityCode', callParams: params });
	} else {
		cdekSetDeliveryItems(city, pvz);
	}
	$(window).resize();
}
function cdekCourierPanel(city) {
	cdek.infoPanels.courier.data('citycode', city.code);
	cdek.infoPanels.courier.find('.city-name .value').text(city.name);
	cdek.infoPanels.courier.find('.cantdelivery, .cdek-delivery-list-wrapper').hide();
	cdek.infoPanels.courier.find('.loader').show();
}
function cdekPvzPanel(pvz) {
	if (!pvz) {
		cdek.infoPanels.pvz.find('.pvz-no-selected').show();
		cdek.infoPanels.pvz.find('.pvz-name, .pvz-address, .cdek-delivery-list-wrapper, .cantdelivery, .loader').hide();
	} else {
		cdek.infoPanels.pvz.find('.pvz-name').show().find('.value').html(pvz.name);
		cdek.infoPanels.pvz.find('.pvz-address').show().find('.value').html(pvz.address);
		cdek.infoPanels.pvz.find('.loader').show();
		cdek.infoPanels.pvz.find('.pvz-no-selected, .cdek-delivery-list-wrapper, .cantdelivery').hide();
	}
}
function cdekSetDeliveryItems(city, pvz) {
	if (typeof city.price.courier === 'undefined') cdek.infoPanels.courier.find('.cantdelivery').show();
	else {
		var delItems = $('<div class="cdek-delivery-list"></div>');
		$.each(city.price.courier, function () {
			var item = cdek.infoPanels.item.clone();
			item.find('.delivery-time .value').html(this.period);
			item.find('.delivery-price .value').html(this.pricehtml);
			item.data({ tariffid: this.id, citycode: city.code });
			delItems.append(item);
		});
		cdek.infoPanels.courier.find('.cdek-delivery-list-wrapper').css('display', 'flex').html(delItems);
	}
	cdek.infoPanels.courier.find('.loader').hide();
	if (pvz && pvz.cityCode == city.code) {
		var add = false;
		if (typeof city.price.pickup !== 'undefined') {
			var delItems = $('<div class="cdek-delivery-list"></div>');
			$.each(city.price.pickup, function () {
				if (pvz.type != this.pointType) return;
				var item = cdek.infoPanels.item.clone();
				item.find('.delivery-time .value').html(this.period);
				item.find('.delivery-price .value').html(this.pricehtml);
				item.data({ tariffid: this.id, citycode: city.code, pvzcode: pvz.code });
				delItems.append(item);
				add = true;
			});
			if (add) {
				cdek.infoPanels.pvz.find('.cdek-delivery-list-wrapper').css('display', 'flex').html(delItems);
			}
		}
		if (!add) cdek.infoPanels.pvz.find('.cantdelivery').show();
		cdek.infoPanels.pvz.find('.loader').hide();
	}
}
function cdekSetCityPrice(cityCode, params) {
	cdek.getData = true;
	$.get('/bc/modules/default/index.php', { user_action: 'cdek', method: 'get_deivery_price', city_code: cityCode }, function (data) {
		cdek.city[cityCode].price = data;
		cdek.getData = false;
		if (params.call == 'cityCode') {
			params.callParams.second = true;
			cdekSetInfo(cityCode, params.callParams);
		}
	}, 'json');
}
function cdekMarkSelected(key) {
	if (typeof cdek.pvz[key] !== 'undefined') {
		cdekSetInfo(cdek.pvz[key].cityCode, { pvz: cdek.pvz[key] });
	}
}
function cdekDeliveryChoose(item) {
	cdek.modalHtml.find('.cdek-btn-choose').addClass('disabled');
	item = $(item);
	item.addClass('loading');
	var data = item.parents('.cdek-delivery-item').data() || {};
	var get = {
		tariffid: data.tariffid,
		cityCode: data.citycode,
		pvzcode: data.pvzcode || ''
	}
	cdekDeliveryRequest(get);
}
function cdekDeliveryRecalc() {
	var block = $('.delivery-assist-blk.cdek:visible');
	if (block.length) {
		var data = block.data();
		if (data.tariffid && data.citycode) {
			cdekDeliveryRequest({
				tariffid: data.tariffid,
				cityCode: data.citycode,
				pvzcode: data.pvzcode
			});
		}
	}
}
function cdekDeliveryRequest(get) {
	if (cdek.getData) return;
	$('.delivery-assist-blk.cdek')
		.addClass('choosed')
		.data({
			tariffid: get.tariffid,
			citycode: get.cityCode,
			pvzcode: get.pvzcode || ''
		});

	get.user_action = 'cdek';
	get.method = 'choose';

	cdek.getData = true;
	$.get('/bc/modules/default/index.php', get, function (data) {
		cdek.getData = false;
		cdek.modalHtml.find('.cdek-btn-choose').removeClass('disabled loading');
		cdek.searchCity.select.find('option[value="' + get.cityCode + '"]').prop('selected', true);

		if (data.address) $('.delivery-assist-blk.cdek').children('.cdek-post-name').html(data.address);
		if (typeof data.deliversum !== 'undefined') $('.deliverSumTr .deliverSum span:first').text(number_format(data.deliversum, 2, ',', ' '));
		if (data.delivery_sum_pay_after > 0) {
			$('.deliverySumPayAfterTr')
				.removeClass('none-important')
				.find('.deliverySumPayAfter span:first')
				.text(number_format(data.delivery_sum_pay_after, 2, ',', ' '));
		} else {
			$('.deliverySumPayAfterTr').addClass('none-important');
		}
		if (typeof data.totdelsum !== 'undefined') {
			$('.total_sum_price span:first').text(number_format(data.totdelsum, 2, ',', ' '));
			$('.total_sum_price').data("totaldelsum", data.totdelsum);
		}
		if (data.success) {
			if ($('#lightcase-case:visible').length) lightcase.close();
			orderFormInputChanger('delivery', {
				force: (data.tariffType == 'courier' ? 'show' : 'hide')
			});
			$('#order [name="f_delivery"] option[data-type="cdek"]').data('deliverytype', data.deliveryType)
		} else {
			$('#order [name="f_delivery"] option[data-type="cdek"]').data('deliverytype', '')
			$('#order .delivery-assist-blk.cdek')
				.data({ tariffid: '', citycode: '', pvzcode: '' })
				.children('.cdek-post-name').html(cdek.text.deliveryNoSelect);
		}
	}, 'json');
}
function cdekClearCache() {
	if (typeof cdek !== 'undefined' && typeof cdek.city === 'object') {
		$.each(cdek.city, function (key) {
			cdek.city[key].price = undefined;
		});
	}
}
// ajax filter

function сallbackSliderRangeOnFinish(data) {
	ajaxFilter();
}

$('body').on('change', '.ajax-filter input[type="checkbox"], .ajax-filter select', function () {
	ajaxFilter();
})
if (window.matchMedia("(max-width: 780px)").matches) {
	$('body').off('.podbor_input').on('click', '.ajax-filter .podbor_click input', function (e) {
		e.preventDefault();
		lightcase.close();
	})
}

function ajaxFilter() {
	console.log($('.ajax-filter').hasClass('cleaning'));
	if ($('.ajax-filter').hasClass('cleaning')) return false;

	$('.ajax-filter').append(`<div class='preloader'>
								<div class="dot"></div>
								<div class="dot"></div>
								<div class="dot"></div>
								<div class="dot"></div>
								<div class="dot"></div>
							</div>`);

	$('.filter-form.ajax-filter').ajaxSubmit({
		success: function (res) {
			let parser = new DOMParser();
			let doc = $(parser.parseFromString(res, "text/html"));
			let filterUrl = doc.find("[data-load]").data('load');
			if (window.matchMedia("(min-width: 780px)").matches) {
				$('#content').replaceWith(doc.find('#content'));
			} else {
				$('#center').replaceWith(doc.find('#center'));
			}
			history.pushState('', '', this.url);
			pageLoadEvent();
			countitemsParam({ type: 'catalog' });
			$.ajax({
				url: filterUrl,
				success: function (data) {
					$('.filtrLoad').html('').append(data);
					$('.podbor_tovarov').replaceWith(data);
					sliderRange();
					$('.select-style').niceSelect();
					//mobile
					if (window.matchMedia("(max-width: 780px)").matches) {
						lightcase.resize();
						if (location.pathname != '/search/' && $('.zone-title').length) {
							$('.zone-title').addClass('mobile-filter-have').append(`<div class='mobile-filter mobyes'><a class='open-filter mainmenubg' href='${filterUrl}' data-rel='lightcase' data-lc-options='{"maxWidth":450,"groupClass":"modal-filter modal-nopadding"}' title='Фильтр'><span>Открыть фильтр</span></a></div>`)
						}
					}
					$('.ajax-filter .preloader').remove();
				}
			});
		}
	})
	return false;
}
// end ajax filter
$(document).on('click', '.hidephone a, .hidephone span', function () {
	var item = $(this);
	if (item.is('span')) item.parent().addClass('opened');
	else if (!item.parent().hasClass('opened')) return false;
});

// добавление в избранное
$('body').on('change', '.favorit-flag input', function () {
	var item = $(this);
	$.get('/bc/modules/default/index.php', 'user_action=fav_change&item_id=' + item.attr('data-id'), function (data) {
		if (data.status == 'ok') {
			var str = 'Товар <a href="' + item.attr('data-href') + '">' + item.attr('data-name') + '</a>' + (item.prop('checked') ? ' добавлен в' : ' удален из') + ' <a href="/favorites/">Избранное</a>';
			$('#notification .alert > div').html(str);
			$('#notification .alert').fadeIn(700, function () { closealert(); });
			$("[name='" + item.attr('name') + "']").parent().toggleClass('active');
			if ($('.favorit_title').length) $('.favorit_title').text((item.prop('checked') ? 'В избранном' : 'В избранное'));
			if ($('.fav .fav-count').length) $('.fav .fav-count').text(data.count);
			if ($('#link-favorite span').length) $('#link-favorite span').text(data.count);

		} else {
			$("[name='" + item.attr('name') + "']").prop('checked', (item.prop('checked') ? false : true));
		}

	}, "json");
});

function getServiceSupplers(arr) {
	console.log(arr)
}


function deleteCurrentUser(confirmed = false)
{
	if (!confirmed) {
		confirmlight({
			title: 'Вы уверены?', 
			confirmlink: "delete_current_user_confirmed", 
			start: 'open'
		});

		$('body')
		.off('click', '[href="delete_current_user_confirmed"]')
		.on('click', '[href="delete_current_user_confirmed"]', function(e){
			lightcase.settings.onCleanup.deleteCurrentUser = function() {
				delete lightcase.settings.onCleanup.deleteCurrentUser;
				deleteCurrentUser(true);
			};
			lightcase.close();

			$('body').off('click', '[href="delete_current_user_confirmed"]');
			
			return false;
		});

		return;
	}

	$.get('/bc/modules/default/index.php', { user_action: 'delete_current_user' }, function(response){
		processJson(response);
	}, 'json');
}
