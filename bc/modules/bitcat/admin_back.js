$(document).ready(function(){
// if admin
if (bc) {
	var bc_wrap, loading=0;


	var uri = (function () {
		var b = {
			settings: "/bc/modules/bitcat/index.php",
			body: "/bc/modules/bitcat/index.php"
		};
		return b
	})(uri || {});


	var bc_sort = (function(obj, place){
		block = obj.parents('section.blocks');
		blockid = block.data('blockid');
		prior = block.data('prior');
		 
		if (place=='up') {
			block2 = block.prev('section.blocks[data-blockid]');
		} else if (place=='down') {
			block2 = block.next('section.blocks[data-blockid]');
		}
		blockid2 = block2.data('blockid');

		if (blockid2) {
			if (!obj.hasClass('disabled')) {
				$('.bc_sort a').addClass('disabled');
				prior2 = block2.data('prior');
				$.post(uri.settings,{'bc_action':'sortblock','place':place,'blockid':blockid,'prior':prior,'blockid2':blockid2,'prior2':prior2,'time':Math.round(new Date().getTime() / 1000)}, function(data){
					if (data.status=='ok') {
						block.data('prior',data.block);
						block2.data('prior',data.block2);
						block_c = block.clone(true);
						block.remove();
						if (place=='up') {
							block2.before(block_c);
						} else if (place=='down') {
							block2.after(block_c);
						}
						$('.bc_sort a').removeClass('disabled');
					}
				},'json');
			}
		}
	});


	var bc_blockVis = (function(obj){
		blockid = obj.data('blockid');
		//block = $("#main section[data-blockid='"+blockid+"']");
		check = (obj.is(':checked') ? 1 : 0);
				$.post(uri.settings,{'bc_action':'blockvis','blockid':blockid,'check':check,'time':Math.round(new Date().getTime() / 1000)}, function(data){
					if (data.status=='ok') {
						
					}
				},'json');
	}); 


	// settings visibility
	var bc_isVisSettings = (function(){
		posSet = bc_wrap.position();
		if (posSet.top<0) { //hidden
			return 0;
		} else { //visible
			return 1;
		}
	});

	// open show settings
	var bc_showsettings = (function(close){
		topset = $('#bc_topset');
		tabs = bc_wrap.find('.bc_tabs .bc_tab');
		heightSet = $(window).height();
		bc_wrap.height(heightSet);
		tabs.height(heightSet-45);
		tabs.find('.wrap').height(heightSet-65);
		if (bc_isVisSettings()==0 && !close) { //show
			topset.addClass('topset-open');
			bc_wrap.animate({'top':0},500);
			bc_wrap.find('.bc_tabname a:first').click();
			$('body').addClass('showsettings');
		} else { //hide
			topset.removeClass('topset-open');
			bc_wrap.animate({'top':-heightSet-46},500);
			$('body').removeClass('showsettings');
		}
	});


	var topset = (function () {
		/*$('body').prepend("");*/
		$.post(uri.settings,{'bc_action':'topset','time':Math.round(new Date().getTime() / 1000)}, function(data){
			$('body').prepend("<div id='bc_topset'><div class='bc_toprel'>"+data.html+"</div></div>");
			bc_wrap = $('#bc_topset .bc_wrap');
		},'json');
	});

	topset();

	// add block buttons: sort/edit
	$('section.blocks[data-blockid]').each(function(){
		o = $(this);
		//obj = o.find('span:first');
		objv = o.data();
		o.append("<div class='bc_buttons'></div>");
		id = o.data('blockid');
		bc_but = o.find('.bc_buttons');
		
		
		//bc_but.append("<div class='bc_move bc_set'><a>↔</a></div>");	
		bc_but.append("<div class='bc_setting bc_set'><a data-width='950' rel='pop' href='#nk-admdialog' data-okno='admdialog' data-title='Настройки блока №"+id+"' title='настройки блока' data-loads='/blockofsite/edit_blockofsite_"+objv.blockid+".html?template=1000'>☼</a></div>");
		bc_but.append("<div class='bc_sort bc_set'><a data-place='down' href='' title='переместить вниз'>▼</a> <a data-place='up' href='' title='переместить вверх'>▲</a></div>");
	});
	

	// drag drop block
	$('section.blocks').draggable({
       	 /*helper: function() {
			namehelper = $(this).data('blockid');
			return $('<div class="clone-block"><span>Перемещение блока<b>'+namehelper+'</b></span><div></div></div>');
		},*/
		helper: 'clone',
		cursor: 'move',
		cursorAt: { top: 0, left: 0 },
		distance: 180,
		opacity : 0.8
    });
	

 
    $('.zone').droppable({
        activeClass: "zone-active",
        hoverClass: "zone-hover",
		tolerance: "pointer",
        over: function(event, ui) {
            ui.helper.addClass('clone-block-active');
			//curzone = $(event.target).data('zone');
			//ui.helper.find('div').text('В зону № '+curzone);
			$('.blocks').addClass('active-drop');
        },
        out: function(event, ui) {
            //ui.helper.removeClass('clone-block-active');
			//$('.blocks').removeClass('active-drop');
        },
		drop: function(event, ui) {
			$('.blocks').removeClass('active-drop');
			tozone = $(this).data('zone');
			fromzone =  ui.draggable.parents('.zone:first').data('zone');
			blockid = ui.draggable.data('blockid');
			blockprior = ui.draggable.data('prior');
			block = $('#block'+blockid);
			blockpaste = false;
            if (tozone>0 && fromzone>0 && tozone!=fromzone) {
				$.post(uri.settings,{'bc_action':'changezone','blockid':blockid,'fromzone':fromzone,'tozone':tozone,'time':Math.round(new Date().getTime() / 1000)}, function(data){
					if (data.status=='ok') {
						$('.zone'+tozone).find('.blocks').each(function(){
							curprior = $(this).data('prior');
							if (curprior>blockprior) {
								$(this).before(block);
								blockpaste=true;
								return false;
							}
						});
						if (!blockpaste) {
							if ($('.zone'+tozone+' .blocks').length>0) {
								$('.zone'+tozone+' .blocks:last').after(block);
							} else {
								$('.zone'+tozone).append(block);
							}
						}
						if (data.blocknewwidth>0) {
							$('#block'+blockid).data('width',data.blocknewwidth).removeClass('ui-draggable').removeClassWild("grid_*").addClass('grid_'+data.blocknewwidth);
						}
					}
				},'json');
				$('.blocks').removeClass('active-drop');
			}
			
        }
    });
	
	// delete css classes by mask
    $.fn.removeClassWild = function(mask) {
        return this.removeClass(function(index, cls) {
            var re = mask.replace(/\*/g, '\\S+');
            return (cls.match(new RegExp('\\b' + re + '', 'g')) || []).join(' ');
        });
    };
	
	
	
	// show children of submenu in sitemap
	$(document).on('click','#siteTree i.fld',function(){
		o = $(this);
		if (o.hasClass('open')) o.removeClass('open'); else o.addClass('open');
		el = o.parents('li.mapitem:first');
		reloadSubTree(el);
		return false;
	});

	// button open edit content
	/*$(document).on('click','.bc_setting a',function(){
		parent.nc_form($(this).attr('href'));
		return false;
	});*/

    // button sort blocks
	$(document).on('click','.bc_sort a',function(){
		bc_sort($(this),$(this).data('place'));
		return false;
	});
	
	// button show-hide settings
	$(document).on( "click", "[data-bcact]", function(){
		bcact = $(this).data('bcact');
		if (bcact=='showsettings') bc_showsettings();
		return false;
	});
	
	// esc close settings
	$(this).keydown(function(eventObject){
	  if (eventObject.which == 27 && bc_isVisSettings()==1) {
		bc_showsettings(0);
		return false;
	  }
	});
	
	// tabs
	$(document).on('click','.bc_tabname a',function(){
		if (loading==0) {
			obj = $(this).parents('.bc_tabs');
			tab = $(this).data('opt');
			if (!tab) return;
			subtab = $(this).data('subopt');
			link = $(this).data('link');
			acttab = obj.find(".bc_tab:first > li[data-opt='"+tab+"']");
			actsubtab = obj.find(".bc_tab:first > li[data-opt='"+tab+"'] > div.wrap[data-subopt='"+subtab+"']");

			$(this).parents('ul').find('li').removeClass('bc_act');
			$(this).parents('li').addClass('bc_act');
			$(this).parents('.mm-li-first').parents('.bc_tabname').find('.mm-li-second').slideUp(400);
			obj.find(".bc_tabname a[data-subopt='"+subtab+"']").parents('li').find('.mm-li-second').slideDown(400);

			obj.find('.bc_tab:first > li, .bc_tab:first > li .wrap').hide().removeClass('bc_act'); acttab.show().addClass('bc_act'); actsubtab.show().addClass('bc_act');
			if (link) {
				if (!acttab.find(".wrap[data-subopt='"+subtab+"']").hasClass('loaded')) {
					loading = 1;
					acttab.find(".wrap[data-subopt='"+subtab+"']").html("<img src='/img/loader.gif'>");
					$.get(link,{'template':'4'}, function(data){
						acttab.find(".wrap[data-subopt='"+subtab+"']").addClass('loaded').html(data);
						acttab.find('form').addClass('ajax2');
						acttab.find(".bc_submitblock").prepend("<div class='result'></div>");
						loading = 0;
					});
				}
			}
		}
		return false;
	});
	
	// type of content
	$("body").on("click","[rel='tab_content']",function(){
		$("select.sel_groupSetting").change();
	});
	
	$("body").on("change","select.sel_groupSetting",function(){
		val = $(this).val();
		thistab = $(this).parents('.admtab');
		thistab.find(".setgroup").hide();
		if (val>0) thistab.find(".setgroup"+val).show();
	});
	
	// load class settings 
	$("body").on("change","select.sel_groupSetting, .setgroup [name='f_sub']",function(){
		typecont = $('select.sel_groupSetting').val();
		subid = $("[name='f_sub']").val();  console.log(subid);
		setdiv = $('#classSettings');
		typecontcur = setdiv.data('typecontcur');
		if (typecontcur!=typecont+"_"+subid) setdiv.html("<img src='/img/loader.gif'>");
		$.post(uri.settings,{'bc_action':'loadsetclass','typecont':typecont,'subid':subid,'time':Math.round(new Date().getTime()/1000)}, function(data){
			if (typecontcur!=typecont+"_"+subid) {
				setdiv.html(data);
				setdiv.data('typecontcur',typecont+"_"+subid);
				colorpick();
			}
		});
	});
	

	
	// on/off blocks checkbox
	$(document).on("change", "#bc_blocks li input[type='checkbox']", function(){
		bc_blockVis($(this));
	});
	
	// obj drop link
	$(document).on("click", ".droplink", function(){
		link = $(this).attr('href');
		if (confirm("Удалить объект?")) {
			$.get(link,{'template':'4'}, function(data){
				if (data) { location.reload(true); }
			});
		}
		return false;
	});
	
	// sub drop link
	$(document).on("click", ".dropsub", function(){
		link = $(this).attr('href');
		if (confirm("Удалить раздел?")) {
			$.get(link,"", function(data){
				if (data) { alert(data); ReloadTab(4); }
			});
		}
		return false;
	});
	
	$(document).on("click", "a[data-groupname]", function(){
		$('a[data-groupname]').removeClass('bc_act');
		$(this).addClass('bc_act');
		group = $(this).data('groupname');
		grpos = $("#"+group).position();
		$('#allsetting').animate({'scrollTop':grpos.top}, 300);
		return false;
	});
	
	
}
});


// reload site map
/*
function ReloadSiteMap(){
	$('#alert .close').click();
	ReloadTab(4);
}*/

// reload tab
function ReloadTab(tabid){
	$(".bc_tabs .bc_tab li .wrap[data-subopt='"+tabid+"']").removeClass('loaded bc_act');
	$(".bc_tabs .bc_tabname a[data-subopt='"+tabid+"']").removeClass('bc_act').click();
}


function reloadSubTree(obj,upd) {
		id = parseInt(obj.data('id'));
		if (id>0) { // update porazdel
			pl = obj.find('div');
			if (!pl.hasClass('opened') || upd) {
				pl.html("<img src='/img/loader.gif'>");
				$.get('/bc/modules/bitcat/index.php',{'bc_action':'sitetree','parent':id}, function(data){
					pl.addClass('opened').html(data);
				});
			} else {
				pl.removeClass('opened').text('');
			}
		} else { // update root
			ReloadTab(4);
		}
}

// clear tab
function ClearTab(tabid){
	$(".bc_tabs .bc_tab li .wrap[data-subopt='"+tabid+"']").removeClass('loaded');
}

// clear tab
function ClearAllTab(){
	$(".bc_tabs .bc_tab li .wrap:hidden").removeClass('loaded').text('');
}


function winPopClose() {
	$('.dialog:visible .close').click();
}

var DeveloperTool={
        Init:function(){
                this.headObj =
		document.getElementsByTagName('html')[0].getElementsByTagName('head')[0];
                return this;
        },
        ReloadAllCSS : function(headObj) {
                console.log("DT:ReloadAllCSS");
                var links = headObj.getElementsByTagName('link');
                for (var i=0 ; i < links.length ; i++){
                        var link = links[i];
                        this.ReloadCSSLink(link);
                }
                return this;
        },
        ReloadCSSLink : function(item) {
                var value = item.getAttribute('href');
                var cutI = value.lastIndexOf('?');
                if (cutI != -1)
                        value = value.substring(0, cutI);
                item.setAttribute('href', value + '?t=' + new Date().valueOf());
                return this;
        },
        ReloadAllCSSThisPage : function() {
                this.ReloadAllCSS(this.headObj);
                return this;
        }
};


function keyDown(e){
	if (e.keyCode == 17) ctrl = true;
	else if (e.keyCode == 16) shift = true; 
	else if (e.keyCode == 83 && ctrl && shift)
	$(".bc_tab .bc_act input[type='submit']").click();
}

function keyUp(e){
	if(e.keyCode == 17) ctrl = false;
	if(e.keyCode == 16) shift = false;
}

function add_variant() {
  i = parseInt($('#variants .variant:last').data('num'))+1;
  if (i) {
	$('#variants .variant:last').after("<div class=variant data-num='"+i+"'><input placeholder='Название' type=text name='variable["+i+"][name]'><input placeholder='Цена' type=text name='variable["+i+"][price]'><input placeholder='количество' type=text name='variable["+i+"][stock]'></div>");
  }
  return false;
}

function add_color() {
  i = parseInt($('#colors .colors:last').data('num'))+1;
  if (i) {
	$('#colors .colors:last').after("<div class=colors data-num='"+i+"'><input placeholder='Название цвета' type=text name='colors["+i+"][name]'><input class=color2 type=text name='colors["+i+"][code]'></div>");
  }
  return false;
}

function add_line(keyw) {
  i = parseInt($('#datas .data:last').data('num'))+1;
  if (i) { console.log(keyw);
	price = (keyw=='delivery' ? "<input placeholder='Цена' type=text name='datan["+i+"][price]'> " : "");
	labels = (keyw=='itemlabel' ? "фон: <input type=text class=color2 name='datan["+i+"][color1]'> текст: <input type=text class=color2 name='datan["+i+"][color2]'> " : "");
	keyword = (keyw=='targetcity' ? "<input size=40 placeholder='название на латинице без пробелов' type=text name='datan["+i+"][keyword]'> " : "");
	keyword = (keyw=='texts' ? "<input type=text name='datan["+i+"][keyword]'> " : "");
	$('#datas .data:last').after("<div class=data data-num='"+i+"'><input placeholder='Название' size=40 type=text name='datan["+i+"][name]'> "+price+labels+keyword+"<label><input type=checkbox value='1' checked name='datan["+i+"][checked]'> включить</label></div>");
	colorpick();
}
return false;
}