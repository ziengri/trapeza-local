var isFramed = false;
try{
	isFramed = window != window.top || document != top.document || self.location != top.location;
}catch (e){
	isFramed = true;
}
if (isFramed) $('body').addClass('isIframe');

function confirmlight(param){
	actioncf = $('#сonfirm-actions');
	if(actioncf.length){
		$("#lightcase-case").css('opacity', 0);

		actioncf.find('.сonfirm-actions-title').html(param.title);
		text = typeof param.confirmtext === 'string' ? param.confirmtext : (typeof param.succes === 'string' ? param.succes : "");
		actioncf.find('.сonfirm-actions-text').html(text).show();

		if(!text) actioncf.find('.сonfirm-actions-text').hide();

		if(typeof param.confirmlink === 'string'){
			actioncf.find('.сonfirm-actions-btn').show();
			actioncf.find('.lightcase-ok').attr('href', param.confirmlink);
			if(typeof param.link === 'string'){
				actioncf.find('.lightcase-ok').attr('data-success', param.link);
			}else{
				actioncf.find('.lightcase-ok').removeAttr('data-success');
			}
		}else{
			actioncf.find('.сonfirm-actions-btn').hide();
		}

		if(typeof param.succes === 'string'){
			actioncf.find('.сonfirm-actions-btn-second').show();
		}else{
			actioncf.find('.сonfirm-actions-btn-second').hide();
		}

		if(typeof param.closetext === 'string'){
			actioncf.find('.lightcase-close.lc-close').text(param.closetext);
		}
		if(typeof param.idobj === 'number' || typeof param.idobj === 'string'){
			actioncf.attr('data-id', param.idobj);
		}else{
			actioncf.removeAttr('data-id');
		}

		if(typeof param.start === 'string')	lightcase.start({ href: '#сonfirm-actions', maxWidth:500, showTitle:false});

	}
}

$(document).ready(function(){
	/**** add lightcase text img ****/
	$(".text-items a img, .txt a img").each(function(){
		a = $(this)
		console.log(a)
		href = a.attr('href') ? a.attr('href') : ""
		if(href.indexOf('.png') > 0 || href.indexOf('.jpg') > 0 || href.indexOf('.jpeg') > 0) a.attr("data-rel", "lightcase:image-txt");
	});

	/**** RUB CHECK ****/
	var rubl = $('.rubl');
	if(rubl.length){
	    $('body').append("<div id='rubl-check' style='font-size:40px;'><span id='rubl-yes'>₽</span><span id='rubl-not'>&#8734;</span></div>");
	    if(document.getElementById('rubl-yes').offsetWidth === document.getElementById('rubl-not').offsetWidth){
	        rubl.text("руб.").removeClass('rubl').addClass('currency');
	    }
	    $('#rubl-check').remove();
	}

	/**** MODAL LIGHTCASE ****/
	$('body').on('click', 'a[data-rel^=lightcase]', function(){
		if(!$(this).data('lc-active')){
			var params = {
				onInit : {
					foo: function() {
						if($(this).attr('href') === "#сonfirm-actions"){ // окно подтверждения
							var param = {};
							link = $(this);
							param.title = (typeof link.attr('title') == 'string' ? link.attr('title') : "");
							param.confirmtext = (typeof link.attr('data-confirm-text') == 'string' ? link.attr('data-confirm-text') : "");
							param.closetext = (typeof link.attr('data-confirm-closetext') == 'string' ? link.attr('data-confirm-closetext') : false);
							param.confirmlink = (typeof link.attr('data-confirm-href') == 'string' ? link.attr('data-confirm-href') : "");
							param.link = (typeof link.attr('data-confirm-success') == 'string' ? link.attr('data-confirm-success') : "");
							confirmlight(param);
						}
					}
				},
				onStart : {
					bar: function() {}
				},
				onFinish : {
					baz: function() {
						lightcaseStyle();
					}
				}
			}
			
			var img = $(this).find(">img");
			if (img.length) {
				var link = $(this).data('lc-href') || $(this).attr('href');
				if (link.indexOf(img.attr('src')) === 0) params.type = 'image';
			}
			
			$(this).data('lc-active', 1).lightcase(params);
			$(this).click();
			return false;
		}
	});

	$('body').on('click', '.lc-close', function(){
		lightcase.close();
		return false;
	});
	/**** END MODAL LIGHTCASE ****/

	// LOAD TABS ON PAGE
	if($('.tabs').length) $('.tabs').menuMaterial();

	//Type otobrazhenya object
	$('body').on('click','a.spisok_type',function(){
		if($(this).hasClass('type1_img')){
			$('.catalog-items').removeClass('block_items_spisok');
			$(this).parent().find('.spisok_type').removeClass('active');
			$(this).addClass('active');
		}
		if($(this).hasClass('type2_img')){
			$('.catalog-items').addClass('block_items_spisok');
			$(this).parent().find('.spisok_type').removeClass('active');
			$(this).addClass('active');
		}
		return false;
	});

	// ajax filte menu
	$(document).on('click', 'ul.subfilter-items a', function(){
		body = $('body');
		if(body.is(".pageobj")) return;

		zonecontent = $('.zone-content');
		a = $(this);
		title = a.find('span').text();
		if(a.parents('li.active').length) return false;
		link = a.attr('href');
		ul = a.parents('ul.subfilter-items');
		ul.find('li.active').removeClass('active');
		a.parents('li').addClass('active');

		document.title = title;
		history.pushState({}, title, link);
		body.addClass('ajax-loading');
		zonecontent.height(zonecontent.innerHeight());
        $.get(link+'?isNaked=1', {}, function(data) {
        		var height = 0;
				body.removeClass('ajax-loading').addClass('ajax-loaded');
				setTimeout(function(){
					zonecontent.html(data);
					countitemsParamAll();
					zonecontent.find("> *").each(function(){
						height += $(this).outerHeight(true);
					});
					zonecontent.height(height);
					setTimeout(function(){
						body.removeClass('ajax-loaded');
						zonecontent.height('auto');
					}, 300);

				}, 300);

        });
		return false;
	});

	// ajax item variable
	$(document).on('click', '.catalog-item .option[data-id], .lightcase-inlineWrap .option[data-url]', function(){
		var li = $(this),
			id = li.attr('data-id'),
			url = li.attr('data-url'),
			ncctpl = li.attr('data-ncctpl'),
			item = li.parents('.obj[data-id], .catalog-item-full'),
			full = !item.is('.obj') ? true : false,
			link = full ? url+"?isNaked=1&fastprew=1" : url+"?isNaked=1&isobj="+id+(ncctpl>0 ? "&nc_ctpl="+ncctpl : "");

		if (!item.hasClass('uncheck')) {
			item.addClass('item-loading');
			$.get(link, {}, function(data) {
				var loaditem = full ? $(data) : $(data).find('.obj');
				item.removeClass('item-loading').addClass('item-loaded');
				setTimeout(function(){
					item.replaceWith(loaditem);

					if(loaditem.find('.blk_first').length){
						parent = loaditem.parents(".catalog-items");
						firstChild = parent.find('.obj:not([data-id="'+id+'"])');
						loaditem.find('.blk_first').height(firstChild.find('.blk_first').height());
						loaditem.find('.blk_second').height(firstChild.find('.blk_second').height());
						loaditem.find('.blk_third').height(firstChild.find('.blk_third').height());
					}

					loaditem.find('.select-style').niceSelect();
					countDown();
					mainPhoto();
					setTimeout(function(){
						item.removeClass('item-loaded');
					}, 300);
				}, 300);
			});
		}
	});



	//menu (akardeon)
	$("body").on('click', '.menu_catalog .menu_arrow, .menu_catalog .menu_plus', function(){
		bodytag = $(this).parents('a').next('ul');
		li = $(this).parent().parent('li');
		if (li.hasClass('active')) {
			li.removeClass('active');
			bodytag.slideUp(200);
		} else {
			li.addClass('active');
			bodytag.slideDown(200);
		}
		return false;
	});
	$("body").on('click', '.menu_catalog li a', function(){
		bodytag = $(this).next('ul');
		li = $(this).parent('li');
		if((li.hasClass('menu_open')&&!li.hasClass('active'))){
			if (li.hasClass('active')) {
				li.removeClass('active');
				bodytag.slideUp(200);
			} else {
				li.addClass('active');
				bodytag.slideDown(200);
			}
			return false;
		}
	});



	// MENU BUTTON
	$("body").on('click', '.menu-button-click .menu-button-head', function(){
		var head = $(this),
			main = head.parent(),
			container = head.parents(".container-zone").length ? head.parents(".container-zone") : head.parents(".container");
		if(!main.hasClass('active')){
			// закрыть другие активные элементы
			load.allItemClose();
			// Активный элемент
			main.attr('data-loadopen', '1').addClass('active');
			container.addClass("menu-btn-open container-menu-btn");
			// Добавляем фон
			overlay = load.itemLoad(container, "start-bg");
			load.triggerClose(overlay, container);

			if(head.parents("#site").length) $('body').addClass('menu-btn-active');
		}else{
			main.removeAttr('data-loadopen', '1').removeClass('active');
			container.removeClass("menu-btn-open");
			load.itemLoad(container, "end");

			$('body').removeClass('menu-btn-active');
		}
	});


	$(document).click(function(e){ // событие клика по веб-документу
		divs = [".basket_mini_open"];
		divs.forEach(function(item) {
			if ($(item).length && !($(e.target).parents(item).length || $(e.target).is(item))) {
				if(item == ".basket_mini_open" && $('.mini_card_open_active').length) $('.mini_card_open_active').removeClass('mini_card_open_active');
			}
		});
	});

	//Card mini (hide - show)
	$('body').on('click','.basket_mini_open',function(){
		if(!$(this).hasClass('basket_mini_gotolink')){
			el = $(this).parents('section');
			if(el.hasClass('mini_card_open_active')){
				//$('.basket_m_spisok').fadeOut(150);
				el.removeClass('mini_card_open_active');
			}else{
				el.addClass('mini_card_open_active');
				//$('.basket_m_spisok').fadeIn(150);
			}
			return false;
		}

	});

	// contacts in block template-2
	$('body').on('click', '.item-contact-select .list-ul li', function(){
		var li = $(this),
			link = li.attr("data-value"),
			bodyWrap = li.parents(".blk_body_wrap");
		bodyWrap.animate({opacity: 0}, 200).height(bodyWrap.innerHeight());
		$.ajax({
			url: link,
			success: function(data) {
				bodyWrap.html(data).find(".select-style").niceSelect();

				setTimeout(function(){
					h = bodyWrap.find('.obj:first-child').innerHeight();
					bodyWrap.height(h).animate({opacity: 1, height: h}, 350, function(){
						$(this).css('height', 'auto');
					});
				}, 200);
			}
		})
	});


	//Инициализация select
	$(".select-style").niceSelect();
	//Стилизованный скролл
	scrollbar = (function(){
		$('.scrollbar-inner, .scrollbar-card').scrollbar();
	});
	scrollbar();
	//Посмотреть пароль в форме
	/*$("body").on('click', '.eye', function(){
		$parent = $(this).parent();
		if($($parent).hasClass('passon')){
			$($parent).find('input').attr('type','password');
			$($parent).removeClass('passon');
		}else{
			$($parent).find('input').attr('type','text');
			$($parent).addClass('passon');
		}
		return false;
	});*/


	//Расскрыть корзину (left block)
	$('body').on('click','.smallcart1 .basket_m_open a',function(){
		parent = $(this).parent('.basket_m_open');
		items = parent.parents('.smallcart1').find('.basket_m_items');
		if(!parent.hasClass('open')){
			parent.addClass('open');
			items.slideDown();
		}else{
			parent.removeClass('open');
			items.slideUp();
		}
		return false;
	});

	//BIG STAR оценка в отзывах
	$("body").on({
		mouseenter: function () {
			var bs_class = $(this).data("item");
			$(this).parents('.big_stars').find('.big_stars_select').css('width', rateWidth(bs_class));
		},
		mouseleave: function () {
			var rating = $(this).parents('.big_stars').find("input").val();
			if(rating == "0"){
				$(this).parents('.big_stars').find(".big_stars_select").css('width', "0px");
			}else{
				$(this).parents('.big_stars').find(".big_stars_select").css('width', rateWidth(rating));
			}
		}
	},".big_stars ul li");

	$('body').on('click','.big_stars ul li',function(){
		var rating = $(this).data("item");
		$(this).parents('.big_stars').find("input").val(rating);
	});


	// обратный отчет
	countDown();
	// слайдер цен
	sliderRange();
	// доставка кол-во дней
	deliveryDays();

	$('body').on('click', '.clear_inpsl', function(){
		var i = $(this).parents('.podbor_block').attr('data-filterid');
		var input = $(this).parent().find('input');
		var inp_par = $(this).parents('.p_p_inp');
		var def = input.data('def');
		input.attr('placeholder', input.attr('placeholder1'));
		input.val('');
		inp_par.removeClass('p_p_inp_cls');
		console.log(input);
		if(def>-1){
			if(input.is('.inp_slider_start')){
				window['sldleft'+i].update({from: def});
			}
			if(input.is('.inp_slider_end')){
				window['sldleft'+i].update({to: def});
			}
		}
	});

	//Раскрывающийся горизонтальный фильтр
	$('body').on('click', '.filter_m_hide_footer', function(){
		fm = $(this).parent('.filter_m_hide');
		if(!fm.hasClass('filter-open')){
			fm.addClass('filter-open');
		}else{
			fm.removeClass('filter-open');
		}
	});



	//Скрывает лишние item в блоке
	colitems('.block_news','.news_item');
	//Resize
	$(window).resize(function() {
		colitems('.block_news','.news_item');
	});



	$('body').on('click', '[data-jsopen]', function(event){
		if (event.target.nodeName == 'LABEL' || (event.target.nodeName == 'SPAN' && $(this).parents('.colheap'))) return;
		el = $(this);
		time = 200;
		name = el.data('jsopen');
		open = $("[data-jsopenthis='"+name+"']");
		main = $("[data-jsopenmain='"+name+"']");
		options = {
			duration: time,
			progress : function(){
				lightcase.resize();
			},
			complete : function(item){
				if($(this).hasClass('addnone')) $(this).addClass('none').removeClass('addnone');
			}
		}
		if(open.hasClass('none')){
			open.slideDown(options).removeClass('none');
			if(main.length) main.addClass('active');
		}else{
			open.addClass('addnone').slideUp(options);
			if(main.length) main.removeClass('active');
		}
		lightcase.resize();
		setTimeout(function(){lightcase.resize();}, time);
	});



	$('body').on('click', '.load-more a', function(event){
		var a = $(this),
			parent = a.parent();

		var sub = parent.attr('data-sub'),
			cc = parent.attr('data-cc'),
 			//param = getAllUrlParams(),
			bitLoadMore = $('body').hasClass('authbit') ? 1 : 0,
			recNum = parent.attr('data-recNum'),
			totRows = parent.attr('data-totRows'),
			curPos = typeof parent.attr('data-curPos') !== "undefined" ? parent.attr('data-curPos') : recNum;
			//param = $.extend(param, {"sub": sub, "cc": cc, "recNum": recNum, "curPos": curPos});
			param = {
				"sub": sub,
				"cc": cc,
				"recNum": recNum,
				"curPos": curPos,
				"bitLoadMore": bitLoadMore
			};

		a.addClass("loading");

        $.get("/bc/modules/default/index.php?user_action=getItemsLoad", param, function(data) {
			items = $("<div class='load-items'></div>").append($(data).find(".obj")).hide();
			parent.parent().find("> [class*='items']").append(items).find(".load-items").slideDown();

			count = parseInt(curPos) + parseInt(recNum)
			parent.attr("data-curPos", count);
			totRows > count ? a.removeClass("loading") : parent.slideUp();
        });
		return false;
	});

	$('body').on('click', '.view-body .pagination a', function(event){
		var a = $(this),
			link = a.attr('href')
			parents = a.parents('.view-body');

		parents.addClass("view-body-loading")

        $.get(link, {}, function(data) {
			parents.html(data).removeClass("view-body-loading").find('select').niceSelect();
        });
		return false;
	});


	$('body').on('keyup',"form.search-life input[name='find']",function(e){
		input = $(this)
		form = input.parents("form")
		searchforminpt = input.parent()
		var result = form.find(".search-result"),
			val = input.val(),
			html = '';

		if(!result.length){
			searchforminpt.append(result = $("<div class='search-result'><ul></ul></div>").hide())
		}

		if(val.length){
			strg = typeof form.data('search') !== 'undefined' ? form.data('search') : "";
			console.log(strg.indexOf(val))
			if(strg.indexOf(val) < 1){
				console.log('searchform: новый текст')
				form.data('search', val)

				clearTimeout(form.data('timer'));

    			form.data('timer', setTimeout(function(){
					$.post("/bc/modules/default/index.php?user_action=searchlife", {val: val}, function(data){

						havedata = false

						for (var i in data) {
							keys = Object.keys(data[i].items);
							if(keys.length){
								html += "<li class='search-res-item search-res-item-main'><span class='search-res-name'>"+data[i].name+"</span></li>"
								for (var id in data[i].items) {
									var name = "<span class='search-res-name ws'>"+data[i].items[id]['name'].replace(val, '<b>'+val+'</b>')+"</span>",
										art = typeof data[i].items[id]['art'] !== 'undefined' ? "<span class='search-res-art ws'>"+data[i].items[id]['art'].replace(val, '<b>'+val+'</b>')+"</span>" : "",
										price = typeof data[i].items[id]['price'] !== 'undefined' && data[i].items[id]['price'] > 0 ? "<span class='search-res-price ws'>"+data[i].items[id]['priceHtml']+"</span>" : ""

									html += "<li class='search-res-item'>"
										if(data[i].items[id]['photo'] !== undefined) html += "<div class='search-res-photo'>"+data[i].items[id]['photo']+"</div>"
										html += "<a href='"+data[i].items[id]['url']+"'>"+name+art+price+"</a>"
									html += "</li>"
									havedata = true
								}
							}
						}
						result.find('ul').html(html)

						havedata ? result.show() : result.hide()

					}, 'json')
		        }, 500))
			}else{
				console.log('searchform: старый текст')
				result.show()
			}
		}else{
			console.log('searchform: нету текста')
			result.hide()
		}
    });




    if (window.Package) {
        Materialize = {};
    } else {
        window.Materialize = {};
    }
    /*******************
     *  Input Plugin  *
     ******************/
    Materialize.updateTextFields = function() {
        var input_selector = 'input[type=text], input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], textarea';
        $(input_selector).each(function(index, element) {
            var $this = $(this);
            if ($(element).val().length > 0 || element.autofocus || $this.attr('placeholder') !== undefined) {
                $this.siblings('label').addClass('active');
            } else if ($(element)[0].validity) {
                $this.siblings('label').toggleClass('active', $(element)[0].validity.badInput === true);
            } else {
                $this.siblings('label').removeClass('active');
            }
        });
    };
    var input_selector = 'input[type=text], input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], textarea';
    $(document).on('change', input_selector, function() {
        if ($(this).val().length !== 0 || $(this).attr('placeholder') !== undefined) {
            $(this).siblings('label').addClass('active');
        }
    });
    $(document).ready(function() {
        Materialize.updateTextFields();
    });
    // HTML DOM FORM RESET handling
    $(document).on('reset', function(e) {
        var formReset = $(e.target);
        if (formReset.is('form')) {
            formReset.find(input_selector).removeClass('valid').removeClass('invalid');
            formReset.find(input_selector).each(function() {
                if ($(this).attr('value') === '') {
                    $(this).siblings('label').removeClass('active');
                }
            });
            // Reset select
            formReset.find('select.initialized').each(function() {
                var reset_text = formReset.find('option[selected]').text();
                formReset.siblings('input.select-dropdown').val(reset_text);
            });
        }
    });
    $(document).on('focus', input_selector, function() {
        $(this).siblings('label, .prefix').addClass('active');
    });
    $(document).on('blur', input_selector, function() {
        var $inputElement = $(this);
        var selector = ".prefix";
        if ($inputElement.val().length === 0 && $inputElement[0].validity.badInput !== true && $inputElement.attr('placeholder') === undefined) {
            selector += ", label";
        }
        $inputElement.siblings(selector).removeClass('active');
    });
    $(document).on('click', ".color-text", function() {
    	el = $(this).parent().find('.sp-replacer');
        if(!el.hasClass('sp-active')){
			el.click();
        }
    });

	if($("body[data-imagehover]").length && !$('.is_mobile').length) imageHoverTable();
});

// Показывать картинку при нведении на название товара (таблица)
function imageHoverTable(){
	var tip = $('<span/>').addClass("tooltip").attr("id", "tooltip");
	var source ='';
	$('.table_main .td_name, .table_main .td_photo').hover(function(){
		this.title='';
		var img = $(this).parents('tr:first').find(".table-image");

		if(!img.length || img.hasClass('nophoto')) return;

		var itemIMG = img.parent().attr('href');
		if (itemIMG){
			source = $('<img/>').attr('src', itemIMG);
		} else {
			tip = $(tip).remove();
			return false;
		}

		if (typeof source!="undefined") {
			tip = $(tip).remove();
			$('body').append(tip);
			tip.html(source);
			tip.show(); //Show tooltip
		} else {
			tip = $(tip).remove();
		}
	}, function(e) {
	 	get_position();
	}).mousemove(function(e) {
		get_coordinate(e, tip);
	});


	function get_coordinate(e, tip) {
		var mousex = e.pageX + 20;
		var mousey = e.pageY + 20;
		var tipWidth = tip.width();
		var tipHeight = tip.height();

		//Distance of element from the right edge of viewport
		var tipVisX = $(window).width() - (mousex + tipWidth);
		var tipVisY = $(window).height() + (mousey + tipHeight);

		if (tipVisX < 20) {
			mousex = e.pageX - tipWidth - 20;
			$(this).find('.tip').css({  top: mousey, left: mousex });
		}
		if (tipVisY < 20) {
			mousey = e.pageY - tipHeight - 20;
			tip.css({  top: mousey, left: mousex });
		} else {
			tip.css({  top: mousey, left: mousex });
		}
	}
	function get_position(source) {
		if (source) {
			tip.hide().remove(); //Hide and remove tooltip appended to the body
			$(this).append(tip); //Return the tooltip to its original position
			source='';
		} else {
			tip.hide().remove();
		}
	}
}
function countDown(){
	$('[data-countdown]').each(function() {
		finalDate = $(this).data('countdown');
		$(this).countdown(finalDate, function(event) {
			if(event.strftime('%D')>0) $(this).html(event.strftime(''
			 + '<span class="countdown_day cd_time"><span class="cd_i">%D</span>день</span><span class="cd_d">:</span>'
			 + '<span class="countdown_hours cd_time"><span class="cd_i">%H</span>час</span><span class="cd_d">:</span>'
			 + '<span class="countdown_minutes cd_time"><span class="cd_i">%M</span>мин</span>'));
			else $(this).html(event.strftime(''
			 + '<span class="countdown_hours cd_time"><span class="cd_i">%H</span>час</span><span class="cd_d">:</span>'
			 + '<span class="countdown_minutes cd_time"><span class="cd_i">%M</span>мин</span><span class="cd_d">:</span>'
			 + '<span class="countdown_second cd_time"><span class="cd_i">%S</span>сек</span>'));
		});
	});
}
function sliderRange(){
	//Слайдер диапазон цен "В блоке слева"
	$(".inp_slider").each(function(){
		var i = $(this).parents('.podbor_block').attr('data-filterid');
		window['slider_start'+i] = $(this).parents('.podbor_block:first').find(".inp_slider_start:first");
		window['slider_end'+i] = $(this).parents('.podbor_block:first').find(".inp_slider_end:first");

		window['start_'+i] = $(this).data('start');
		window['end_'+i] = $(this).data('end');
		window['cur1'+i] = $(this).data('cur1');
		window['cur2'+i] = $(this).data('cur2');
		if(!window['start_'+i]) window['start_'+i]=0;
		if(!window['end_'+i]) window['end_'+i]=100000;
		window['numstart'+i] = window['start_'+i];
		window['numend'+i] = window['end_'+i];
		window['raznica'+i] = window['end_'+i]-window['start_'+i];
		window['stepr'+i]=100;
		if(window['raznica'+i]<=5000) window['stepr'+i]=10;
		if(window['raznica'+i]<=1000) window['stepr'+i]=1;
		if(window['raznica'+i]<=100) window['stepr'+i]=0.1;

		$(this).ionRangeSlider({
			type: "double",
			min: window['start_'+i],
			max: window['end_'+i],
			from: (window['cur1'+i]>0 ? window['cur1'+i] : window['start_'+i]),
			to: (window['cur2'+i]>0 ? window['cur2'+i] : window['end'+i]),
			step: window['stepr'+i],
			hide_min_max: true,
			hide_from_to: true,
			onChange: function (data) {
                window['slider_start'+i].val(data.from);
                window['slider_end'+i].val(data.to);
                window['numstart'+i]!=data.from ? window['slider_start'+i].parents('.p_p_inp:first').addClass('p_p_inp_cls') : window['slider_start'+i].parents('.p_p_inp:first').removeClass('p_p_inp_cls');
                window['numend'+i]!=data.to ? window['slider_end'+i].parents('.p_p_inp:first').addClass('p_p_inp_cls') : window['slider_end'+i].parents('.p_p_inp:first').removeClass('p_p_inp_cls');
			},
			onStart: function (data) {
				window['slider_start'+i].attr('placeholder',data.from);
				window['slider_end'+i].attr('placeholder',data.to);
				window['slider_start'+i].attr('data-number', data.from);
				window['slider_end'+i].attr('data-number', data.to);
			},
			onFinish: (typeof(сallbackSliderRangeOnFinish) === 'function' ? сallbackSliderRangeOnFinish : '')
		});

		window['sldleft'+i] = $(this).data("ionRangeSlider");
		window['slider_start'+i].on("keyup", function () {
			window['sldleft'+i].update({
				from: $(this).val()
			});
		});
		window['slider_end'+i].on("keyup", function () {
			window['sldleft'+i].update({
				to: $(this).val()
			});
		});
	});
	//Слайдер диапазон цен (Сверху)
	$(".filter_sld").each(function(){
		slider_starttop = $(this).parent().find("#filter_sld_start"),
		slider_endtop = $(this).parent().find("#filter_sld_end")
		console.log(1);
		var stepr,numstart,numend;
		start = $(this).data('start');
		end = $(this).data('end');
		cur1 = $(this).data('cur1');
		cur2 = $(this).data('cur2');
		if(!start){start=0};if(!end){end=100000};
		numstart=start;numend=end;
		raznica = end/start;stepr=100;
		if(raznica<=5000)stepr=10;
		if(raznica<=1000)stepr=1;
		$(this).ionRangeSlider({
			type: "double",
			min: start,
			max: end,
			from: (cur1>0 ? cur1 : start),
			to: (cur2>0 ? cur2 : end),
			step: stepr,
			//drag_interval: true,
			//min_interval: 5000,
			hide_min_max: true,
			hide_from_to: false,
			onChange: function (data) {
				slider_starttop.val(data.from);
				slider_endtop.val(data.to);
			},
			onStart: function (data) {
				if(numstart!=data.from){slider_starttop.val(data.from);}
				if(numend!=data.to){slider_endtop.val(data.to);}
			}
		});
	});
}

function rateWidth(rating){
	rating = parseFloat(rating);
	switch (rating){
		case 0.5: width = "11px"; break;
		case 1.0: width = "23px"; break;
		case 1.5: width = "37px"; break;
		case 2.0: width = "48px"; break;
		case 2.5: width = "62px"; break;
		case 3.0: width = "75px"; break;
		case 3.5: width = "88px"; break;
		case 4.0: width = "100px"; break;
		case 4.5: width = "114px"; break;
		case 5.0: width = "126px"; break;
		default:  width =  "0px";
	}
	return width;
}

function heighset(elementblok, height){
	h=0;
	if(!height){
		$(elementblok).each(function() {
			if(h < $(this).height()) h = $(this).height();
		});
		heighset(elementblok, h);
	}else{
		$(elementblok).each(function() {$(this).height(height);});
	}
}

function colitems(nameblock, nameitem){
	if ($(nameblock).length>0) {
		$(nameblock).each(function(i,elem) {
			col_now = 0;
			wblock = $(this).width();
			colitem = $(this).find(nameitem).length;
			witem = $(this).find(nameitem).width();
			$(this).find(nameitem).show();
			if(colitem>Math.round(wblock/witem)){
				col_now =  colitem % Math.round(wblock/witem)*-1;
				i=-1;
				while(i>=col_now){
					$(this).find(nameitem).eq(i).hide(); i--;
				}
			}
		});
	}
}


function lightcaseStyle(){
	lightcasecase = $('#lightcase-case');
	lightcasecase.find('.input-select select, select.ns, .select-style').niceSelect();
	if(typeof $.fn.color == "function") lightcasecase.find('input.color').color();
	if(lightcasecase.find('[data-ckeditor]').length){
		tinymceEditor(lightcasecase.find('[data-ckeditor]'));
	}
	if(lightcasecase.find('.datapicker input').length){
		lightcasecase.find('.datapicker input').each(function(){
			var salf = $(this);
			$(this).datepicker({
				startDate: new Date($(this).val().length ? $(this).val() : ""),
				todayButton: new Date(),
				onSelect: function onSelect(fd, date) {
					var zeros = ['00', '0', ''];
					year = date.getFullYear();
					month = (date.getMonth()+1);
					month = zeros[new String(month).length] + month;
					day = zeros[new String(date.getDate()).length] + date.getDate();
					hours = zeros[new String(date.getHours()).length] + date.getHours();
					minutes = zeros[new String(date.getMinutes()).length] + date.getMinutes();
					sec = zeros[new String(date.getSeconds()).length] + date.getSeconds();

					bd = salf.parents('.date-calendar');
					if(bd.length){
						bd.find('[name$="_day"]').val(day);
						bd.find('[name$="_month"]').val(month);
						bd.find('[name$="_year"]').val(year);
						bd.find('[name$="_hours"]').val(hours);
						bd.find('[name$="_minutes"]').val(minutes);
						bd.find('[name$="_seconds"]').val(sec);
						bd.find('.input-field label').addClass('active');
					}

				}
			});
		});
	}
	countDown();
	mainPhoto();
	deliveryDays();
	if(typeof collineSelectNumber == "function") collineSelectNumber();
	sliderRange();
	if(lightcasecase.find("textarea[data-formbuilder]").length) formBuilder(lightcasecase);
	lightcase.resize();
}


function tinymceEditor(el) {
	//if($(".SUPERVIKTOR").length){
	if($("body").hasClass("editor-2")){
		if(typeof tinymce === 'undefined'){
			script = document.createElement('script');
			script.src = "/js/tinymce/tinymce.min.js";
			document.body.appendChild(script);
			script.onload = function() {
				tinymceInit()
			}
		}else{
			tinymceInit();
		}
	}else{
		setTimeout(function() {
			el.each(function(){
				name = $(this).attr('name');
					try {
						CKEDITOR.replace(name, {
							toolbarGroups: ["mode",{"name":"clipboard"},{"name":"undo"},{"name":"find"},{"name":"selection"},{"name":"forms"},{"name":"basicstyles"},{"name":"cleanup"},{"name":"list"},{"name":"indent"},{"name":"blocks"},{"name":"align"}, {"name":"links"}, {"name":"insert"}, {"name":"styles"},{"name":"colors"}],
							skin: 'moono',
							language: 'ru',
							filebrowserBrowseUrl: '/bc/editors/ckeditor4/ckfinder/ckfinder.html',
							filebrowserImageBrowseUrl: '/bc/editors/ckeditor4/ckfinder/ckfinder.html?type=Images',
							filebrowserUploadUrl: '/bc/editors/ckeditor4/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
							filebrowserImageUploadUrl: '/bc/editors/ckeditor4/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
							allowedContent: true,
							entities: true,
							autoParagraph: true,
							enterMode: 1
						});
					} catch (exception) {}
			});
			CKEDITOR.on('instanceReady',function(ev) {
				lightcase.resize();
			    ev.editor.on('resize',function(reEvent){lightcase.resize();});
			});
		}, 250);
	}
}
function tinymceInit() {
    tinymce.baseURL = "/js/tinymce/";
    tinymce.suffix = ".min";
    tinymce.triggerSave()
    tinymce.init({
        selector: '[data-ckeditor="1"]',
        height: 420,
        language: 'ru',
        theme: 'silver',
        statusbar: false,
        plugins: [
            'quickbars',
            'code',
            'template',
            'lists',
            'media',
            'image',
            'ImageEditor',
            'imagetools',
            'responsivefilemanager',
            'advlist',
            'autolink',
            'link',
            'charmap',
            'preview',
            'hr',
            'anchor',
            'pagebreak',
            'searchreplace',
            'wordcount',
            'visualblocks',
            'visualchars',
            'insertdatetime',
            'nonbreaking',
            'save',
            'table',
			'tablecellvalign',
            'contextmenu',
            'directionality',
            'emoticons',
            'paste',
            'textcolor',
            'colorpicker',
            'textpattern',
            'toc',
            'fullscreen',
        ],
        content_css: [
            '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
            '//www.tinymce.com/css/codepen.min.css',
            '/bc/editors/tinymce/css/style.css',
            '/bc/editors/tinymce/css/style_in_editor.css'
        ],
        toolbar: 'code undo redo | template emoticons| styles removeformat | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | media image responsivefilemanager link | forecolor backcolor | fullscreen fontsizeselect',
        code_dialog_width: 750,
        code_dialog_height: 500,
        relative_urls: false,
        entity_encoding: "numeric",
        template_selected_content_classes: 'selcontent selectedcontent',
        templates: '/bc/editors/tinymce/tinymce_templates.php',
        image_advtab: true,
        external_plugins: {
            filemanager: "/js/tinymce/plugins/responsivefilemanager/plugin.min.js",
        },
        external_filemanager_path: "/bc/modules/filemanagerTiny/",
        filemanager_title: "Responsive Filemanager",
        extended_valid_elements : '*[*]',
        valid_children: "+body[style|script],*[*]",
        valid_elements : '*[*],pre[!href]',
        entities: "quot",
        cleanup: false,
        setup: function (editor) {
            editor.on('init', function () {
                setTimeout(function () {
                    lightcase.resize();
                }, 380)
                $(editor.getWin()).bind('resize', function (e) {
                    lightcase.resize();
                })
                editor.save();
            });
            editor.on('change', function () {
                editor.save();
            });
        },
    });
}
// загрузка кол-ва дней Деловые линии
function deliveryDays(){
	var deliveryDays = $('.delivery-days.loading');
	if(deliveryDays.length){
		$.post("/bc/modules/default/index.php?user_action=delivery_days", {cityname: deliveryDays.attr('data-cityname'), citymain: deliveryDays.attr('data-citymain')}, function(data){
			deliveryDays.html(data).removeClass("loading");
		});
	}
}
