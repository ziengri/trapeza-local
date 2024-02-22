$(document).ready(function () {
	$('.number-info-cell label').click(function (event) {
		var top = $(this).offset().top - $(document).scrollTop();
		var bottom = $(window).height() - top - $(this).height();
		$('.modal-number-info').hide();

		var info = $(this).parent().find('.modal-number-info');
		if (top < bottom) {
			info.removeClass('top').addClass('bottom').find('.number-info-cell').css('max-height', bottom - 10);
		} else {
			info.removeClass('bottom').addClass('top').find('.number-info-cell').css('max-height', top - 10);
		}
		info.show();
	});

	$(document).mouseup(function(e) {
		var container = $(".number-info-cell .modal-number-info");
		if (!container.is(e.target) && container.has(e.target).length === 0) container.hide();
	});

	$('.modal-number-info-close').click(function () {
		$(this).closest('.modal-number-info').hide();
	});

	$("#imageLayout").draggable({
		drag: function (event, ui) {
			__dx = ui.position.left - ui.originalPosition.left;
			__dy = ui.position.top - ui.originalPosition.top;
			ui.position.left = ui.originalPosition.left + ( __dx);
			ui.position.top = ui.originalPosition.top + ( __dy );
			ui.position.left += __recoupLeft;
			ui.position.top += __recoupTop;
		},
		start: function (event, ui) {
			$(this).css('cursor', 'pointer');
			var left = parseInt($(this).css('left'), 10);
			left = isNaN(left) ? 0 : left;
			var top = parseInt($(this).css('top'), 10);
			top = isNaN(top) ? 0 : top;
			__recoupLeft = left - ui.position.left;
			__recoupTop = top - ui.position.top;
		},
		create: function (event, ui) {
			$(this).attr('oriLeft', $(this).css('left'));
			$(this).attr('oriTop', $(this).css('top'));
		}
	});

	function getIEVersion() {
		var agent = navigator.userAgent;
		var reg = /MSIE\s?(\d+)(?:\.(\d+))?/i;
		var matches = agent.match(reg);
		if (matches != null) {
			return {major: matches[1], minor: matches[2]};
		}
		return {major: "-1", minor: "-1"};
	}

	var ie_version = getIEVersion();
	var is_ie10 = ie_version.major == 10;
	var is_ie11 = /Trident.*rv[ :]*11\./.test(navigator.userAgent);

	var isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
	var tmpImg = new Image();

	var $imgArea = $('.image-tab.active .main-image-area');
	var imgAreaWidth = parseInt($imgArea.width());
	var imgAreaHeight = parseInt($imgArea.height());

	tmpImg.onload = function () {
		var tmpImgWidth = parseInt(tmpImg.width);
		var tmpImgHeight = parseInt(tmpImg.height);

		var scaleX = imgAreaWidth / tmpImgWidth;
		var scaleY = imgAreaHeight / tmpImgHeight;

		var zoom = Math.min.apply(null, [scaleX, scaleY]) * 1;
		var origin = Math.min.apply(null, [scaleX, scaleY]) * 1;
		var left = (imgAreaWidth - tmpImgWidth) / 2;
		var top = (imgAreaHeight - tmpImgHeight) / 2;

		$('.imageArea-info-plus').click(function () {
			if (zoom) {
				zoom = zoom + 0.1;
				if (zoom < 0.1) {
					zoom = 0.1;
				}
				$('.image-tab.active .imageLayout').css({
					'transform': 'scale(' + zoom + ', ' + zoom + ')'
				});
			}
		});

		$('.imageArea-info-minus').click(function () {
			if (zoom) {
				zoom = zoom - 0.1;
				if (zoom < 0.1) {
					zoom = 0.1;
				}
				$('.image-tab.active .imageLayout').css({
					'transform': 'scale(' + zoom + ', ' + zoom + ')'
				});
			}
		});

		function stretch() {
			zoom = Math.min.apply(null, [scaleX, scaleY]) * 1;
			left = (imgAreaWidth - tmpImgWidth) / 2;
			top = (imgAreaHeight - tmpImgHeight) / 2;
			$('.image-tab.active .imageLayout').css({
				'transform': 'scale(' + zoom + ', ' + zoom + ')',
				'top': top,
				'left': left
			});
		}

		$('.imageArea-info-stretch').click(function () {
			stretch();
		});

		if ($('.image-tab.active').length > 0) {
			stretch();
		}
		var binds = isFirefox ? 'MozMousePixelScroll' : (is_ie10 || is_ie11) ? 'wheel' : 'mousewheel DOMMouseScroll wheel';
		$('.image-tab.active .main-image-area .imageArea').bind(binds, function (e) {
			if (!origin)
				origin = 1;
			if (e.type === 'wheel') {
				if (e.originalEvent.deltaY > 0) {
					zoom = zoom - (origin * 0.01);
				} else {
					zoom = zoom * 1 + (origin * 0.01);
				}
			} else if (e.type === 'mousewheel') {
				if (e.originalEvent.wheelDelta < 0) {
					zoom = zoom - (origin * 0.01);
				} else {
					zoom = zoom * 1 + (origin * 0.01);
				}
			} else if (e.type === 'DOMMouseScroll' || e.type === 'MozMousePixelScroll') {
				if (e.originalEvent.detail > 0) {
					zoom = zoom - (origin * 0.01);
				} else {
					zoom = zoom * 1 + (origin * 0.01);
				}
			}
			if (zoom) {
				if (zoom < 0.01) {
					zoom = 0.01;
				}
				e.preventDefault();
				$('.image-tab.active .imageLayout').css({
					'transform': 'scale(' + zoom + ', ' + zoom + ')'
				});
			}
		});
		$(".to-image")
			.dblclick(function () {
				var a = left - parseInt($('.image-tab.active .ladel.active').css('left').replace('px', '')) * zoom + ($('.image-tab.active .main-image-area .imageLayout').width() * zoom / 2)
					,
					e = top - parseInt($('.image-tab.active .ladel.active').css('top').replace('px', '')) * zoom + ($('.image-tab.active .main-image-area .imageLayout').height() * zoom / 2)
					, t = $(this)
						.attr("data-index");
				$(".image-tab.active .imageLayout")
					.css({
						left: a
					})
					.css({
						top: e
					}), $("html, body")
					.animate({
						scrollTop: $(".image-tab.active .main-image-area")
							.offset()
							.top - 70
					}, 1e3), $(".image-tab.active")
					.find("[data-index='" + t + "']")
					.addClass("active")
			});
	};
	tmpImg.src = $('.image-tab.active .imageLayout img').attr('src');

	$(".imageLayout .ladel")
		.click(function () {
			var a = $(this)
				.attr("data-index");
			$(".imageArea-related .table-row")
				.removeClass("active")
				, $(".imageLayout .ladel")
				.removeClass("active")
				, a ? ($(".image-tab.active")
					.find("[data-index='" + a + "']")
					.addClass("active"), $(".table.imageArea-related")
					.find("[data-index='" + a + "']")
					.addClass("active")) :
				$(this)
					.addClass("active")
		}),

		$(".imageLayout .ladel")
			.dblclick(function () {
				var a = $(this)
					.attr("data-index");
				$("html, body")
					.animate({
						scrollTop: $(".imageArea-related")
							.find("[data-index='" + a + "']")
							.first()
							.offset()
							.top - 70
					}, 1e3)
			}), $(".to-image")
		.click(function () {
			var a = $(this)
				.attr("data-index");
			$(".imageLayout .ladel")
				.removeClass("active"), a && $(".image-tab.active")
				.find("[data-index='" + a + "']")
				.addClass("active")
		}), $(".imageArea-info-label")
		.click(function () {
			$(this)
				.hasClass("active") ? ($(this)
				.removeClass("active"), $(".image-tab.active .imageArea-info-label span")
				.hide(), $(".image-tab.active .imageArea .ladel")
				.css("opacity", "")) : ($(this)
				.addClass("active"), $(".image-tab.active .imageArea-info-label span")
				.show(), $(".image-tab.active .imageArea .ladel")
				.css("opacity", 0))
		}), $(".image-tab-nav:not(.href-tab)")
		.click(function () {
			var a = $(this)
				.attr("data-subgroup");
			$(".image-tab-nav.active")
				.removeClass("active"), $(this)
				.addClass("active"), $(".image-tab.active")
				.removeClass("active")
				.addClass("hidden"), $("#image-tab-" + a)
				.removeClass("hidden")
				.addClass("active"), $(".table-tab.active")
				.removeClass("active")
				.addClass("hidden"), $("#table-tab-" + a)
				.removeClass("hidden")
				.addClass("active")
		}), $(".imageArea-info-icon")
		.click(function () {
			var a = $(".image-tab.active .imageArea-info");
			a.hasClass("active") ? a.removeClass("active") : a.addClass("active")
		}), $(document)
		.mouseup(function (a) {
			var e = $(".image-tab.active .imageArea-info-icon");
			e.is(a.target) || 0 !== e.has(a.target)
				.length || e.find(".imageArea-info")
				.removeClass("active")
		});
	$('.model_modal').on('click',function(){
		id = $(this).attr('href');
		lightcase.start({
			href: id,
			title:'Выбор',
		});
	});
});