// open show settings
var bc_separate = (function(close, num){
	if ((!$('body').is('.showseparate') && !close) || num) { //show
		$('.separate_separatebody').css('display', 'block');
		setTimeout(function() { $('body').addClass('showseparate') }, 0);

		if(!$('.sep-first[data-loadactive]').length){
			 $('#separate_adminmenu li.sep-first:first').click().change();
		}

	} else { //hide
		$('body').removeClass('showseparate');
		setTimeout(function() { $('.separate_separatebody').css('display', 'none'); }, 300);
	}
});

$(document).ready(function(){
	// Вставка админки в html
	var separate = (function () {
		$.post("/bc/modules/bitcat/separate/index.php", {'action':'getBody', 'time': Math.round(new Date().getTime() / 1000)}, function(data){
			$('body').prepend("<div id='separate'>"+data.leftMenu+"<div class='separate_separatebody'><div class='separate_content'>"+data.rightContent+"</div></div><div class='superinfo'><div class='si-line seperate-link'><span>Separate code</span></div></div></div>");
		},'json');
	});
	separate();

	// Клик по кнопке открытия админки
	$(document).on( "click", ".seperate-link span", function(){
		bc_separate();
	});

	// esc close settings
	$(this).keydown(function(eventObject){
		if (eventObject.which == 27 && $('body').is('.showseparate')) {
			bc_separate(0);
			return false;
		}

		if(event.ctrlKey && event.keyCode == 192) {
			bc_separate(0);
			return false;
		}
		// save settings
		if(event.ctrlKey && event.shiftKey && event.keyCode==83){
			/*input = $('.view-content.opened .bc_btnbody .bc-btn input');
			if($('body').is('.showseparate') && input.length) input.click();*/
		}
	});


	$(document).on("change", ".component-body input[type='checkbox']", function(){
		checkbox = $(this);
		colline = checkbox.parents(".colline");
		if(checkbox.prop('checked')){
			colline.removeClass("no-use");
		}else{
			colline.addClass("no-use");
		}
		colline.find(".textarea-body[data-name]").each(function(){
			window[$(this).attr("data-name")].layout();
		});
	});

	$(document).on("click", ".get-component", function(){
		link = $(this);
		textarea = link.parents(".colline").find("textarea");
		id = textarea.parents("[data-idc]").attr("data-idc");

		$.post("/bc/modules/bitcat/separate/index.php", {'action': 'getInComponent', 'get': link.data('get'), 'id': link.data('id'), 'time': Math.round(new Date().getTime() / 1000)}, function(data){
			textarea.val(data);
			window['vs'+id].setValue(data);
		});
		return false;
	});
	$(document).on("click", ".full-c", function(){
		fscreen = $(this);
		if(fscreen.parents(".no-use").length) return;
		parent = fscreen.parents(".textarea-field");
		parent.toggleClass("fullscreen-c");

		id = parent.find("textarea").parents("[data-idc]").attr("data-idc");
		window['vs'+id].layout();
		
		return false;
	});

	var height = $(window).height(); 
	$(window).resize(function(){
		if ($(window).height()==height) return;
		height = $(window).height();		
		
		$(".component-body .textarea-body[data-name]").each(function(){
			name = $(this).attr("data-name");
			window[name].layout();
		})

	})

	$(document).on("click", "#separate_adminmenu li.sep-first", function(){
		id = $(this).data('id');
		name = "monacoInterval"+id;
		var el = $('.sep-load-'+id);
		if(el.length && !el.hasClass("loaded")) el.addClass("loaded");
	});  
	$(document).on("click", ".component-body .tab", function(){
		href = $(this).find('a').attr('href');
		$(href).each(function(){
			$(this).find(".textarea-body[data-name]").each(function(){
				window[$(this).attr("data-name")].layout();
			});
		});
	});  

	$(document).on("click", ".remove-template", function(){
		$(this).parent().remove();
	}); 



	window['separateCallback'] = function(el){
		require.config({ paths: { 'vs': '/js/vs' }});
		require(['vs/editor/editor.main'], function() {
			el.each(function(i){
				sep = $(this);
				if(!sep.hasClass('monaco')){
					sep.addClass('monaco');
					sep.find("textarea").each(function(){
						textarea = $(this).hide();
						textarea.parent().append($("<div class='textarea-body'></div>")).find(".textarea-body").prepend(textarea);
						textarea.parents(".colline").find(".textarea-title").append($("<div class='full-c'></div>").text("full"));
						id = textarea.parents("[data-idc]").attr("data-idc");
						name = 'vs'+id;
						var textareabody = textarea.parents('.textarea-body').attr("data-name", name)[0];
						value = textarea.val();

						window[name] = monaco.editor.create(textareabody, {
							value: value,
							language: 'php',
							fontSize: 12,
							contentHeight: 260,
							theme: 'vs',
							glyphMarginHeight: 0,
							contextmenu: false
						});
						window[name].onKeyUp(function(a, b) {
							textarea = $(a.target).parents('.colline').find('.textarea-field .textarea-body > textarea');
							console.log(textarea);
							name = textarea.parents(".textarea-body[data-name]").attr("data-name");
							console.log(window[name]);
							textarea.val(window[name].getValue());
						});
					});
				}
			})
		})
	}
	

});



function addline_separate(keyw) {
	el = $('#'+keyw+' > [data-num]:last');
	i = el.attr("data-num");
	id = el.attr("data-id");
	if (i && id) {
		i++;
		$.post("/bc/modules/bitcat/separate/index.php", {'action': 'getFileObjectData', 'id': id, 'i': i, 'time': Math.round(new Date().getTime() / 1000)}, function(data){
			body = $('#'+keyw).append(data);
			window['separateCallback'](body.find('.textarea-body:last'));
		});
	}
	return false;
}