/*
 * Form Builder 0.0.2
 * KORZILLA 3.0
 */
(function($) {
	var methods = {
		objects: {},
		init: function(options) {
			this.each(function() {
				methods.element = $(this);
				var inited = methods.element.data('formBuilder');
				if (!inited) {
					methods.element.data('formBuilder', {
						target: methods.element,
						inited: 1
					}).hide();
					methods.addMainElements();
					methods.jqueryui();

					var lcresize = setInterval(function() {
						if ($(".lightcase-open").length) {
							setTimeout(function() {
								lightcase.resize();
							lightcaseStyle();
								clearInterval(lcresize);
							}, 150);
						}
					}, 100);
				}
			});
		},
		addMainElements: function() {
			//if(this.length) methods.element = $(this);
			methods.element.after(methods.builder = $('<div id="formBuilder"></div>'));
			methods.builder.append(methods.builder.main = $('<div class="fb-main"></div>'), methods.builder.btns = $('<div class="fb-btns"></div>'));
			methods.builder.main.append(methods.builder.mainFields = $('<div class="fb-main-fields"></div>'));
			methods.builder.btns.append(methods.getInput);
			methods.builder.btns.append(methods.getTextarea);
			methods.builder.btns.append(methods.getSelect);
			methods.builder.btns.append(methods.getRadio);
			methods.builder.btns.append(methods.getCheckbox);
			methods.builder.btns.append(methods.getTitle);
			methods.builder.btns.append(methods.getFile);
			methods.builder.btns.append(methods.getDate);
			methods.element.parents("form").find(".btn-strt, .bc-btn").prepend(methods.add = $('<div class="fb-addform"></div>'));
			methods.btnstrt = methods.element.parents("form").find(".btn-strt input, .bc-btn input");
			/*_self.objects.nav.append(_self.objects.closemobile = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-close"></a>'), _self.objects.prev = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-prev"><span>' + _self.settings.labels['navigator.prev'] + '</span></a>').hide(), _self.objects.next = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-next"><span>' + _self.settings.labels['navigator.next'] + '</span></a>').hide(), _self.objects.play = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-play"><span>' + _self.settings.labels['navigator.play'] + '</span></a>').hide(), _self.objects.pause = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-pause"><span>' + _self.settings.labels['navigator.pause'] + '</span></a>').hide());
			_self.objects.case.append(_self.objects.info = $('<div id="' + _self.settings.idPrefix + 'info"></div>'), _self.objects.content = $('<div id="' + _self.settings.idPrefix + 'content"></div>'));
			_self.objects.case.append(_self.objects.sequenceInfo = $('<div id="' + _self.settings.idPrefix + 'sequenceInfo"></div>'));
			_self.objects.content.append(_self.objects.contentInner = $('<div class="' + _self.settings.classPrefix + 'contentInner"></div>'));*/
		},
		jqueryui: function() {
			methods.builder.find(".fb-main-fields").sortable({
				revert: true
			});

			methods.builder.find(".fb-btn").draggable({
				connectToSortable: "#formBuilder .fb-main-fields",
				helper: "clone",
				revert: "invalid",
				revertDuration: 0,
				create: function( event, ui ) {
					//console.log("create");
				},
				start: function( event, ui ) {
					//console.log("start");
				},
				drag: function( event, ui ) {
					//console.log("drag");
				},
				stop: function(event, ui) {
					lightcaseStyle();
					//console.log(event, ui);
				}
			});
			methods.builder.find(".fb-btn, .fb-main").disableSelection();

			// set json
			json = methods.element.val();
			if(isJson(json)){
				methods.setJson(JSON.parse(json));
			}

			// кнопка добавления
			methods.add.click(function(){
				json = methods.getJson();
				if(json){
					methods.element.val(JSON.stringify(json));
					methods.btnstrt.click();
				}
			});
			// списки
			methods.builder.find('.ns').niceSelect();
		},
		getInput: function(j) {
			param = {
				required: { value: 0, grid: 2 },
				typeInput: { values: ['текст', 'число', 'скрытый'], grid: 2 },
				label: { },
				name: { grid: 2 },
				value: { grid: 2 },
				placeholder: { },
				userType: { values: ['любой', 'физ лицо', 'юр лицо'], grid: 2 },
				coleline: {values: [1, 2, 3, 'height'], grid: 2 }
			}
			if(typeof j === 'object') $.extend(true, param, j);
			result = methods.getElementName('Строка', param);
			result += methods.getElementParam(param);
			result += methods.getElementEdit();

			cls = typeof param.name.value !== "undefined" ? "fb-btn-"+param.name.value : "";

			result = "<div class='fb-btn "+cls+"' data-element='input'>"+result+"</div>";
			if(typeof j === 'object') methods.builder.mainFields.append(result);
			else return result;
		},
		getTextarea: function(j) {
			param = {
				required: { },
				label: { grid: 2 },
				name: { grid: 2 },
				placeholder: { },
				textarea: { },
				userType: { values: ['любой', 'физ лицо', 'юр лицо'], grid: 2 },
				coleline: {values: [1, 2, 3, 'height'], grid: 2 }
			}
			if(typeof j === 'object') $.extend(true, param, j);
			result = methods.getElementName('Текстовое поле', param);
			result += methods.getElementParam(param);
			result += methods.getElementEdit();
			result = "<div class='fb-btn' data-element='textarea'>"+result+"</div>";
			if(typeof j === 'object') methods.builder.mainFields.append(result);
			else return result;
		},
		getSelect: function(j) {
			param = {
				required: { value: 0 },
				label: { grid: 2 },
				name: { grid: 2 },
				selectGroup: {
					values: {
						0: { value: "Опиця 1" },
						1: { value: "Опиця 2" },
						2: { value: "Опиця 3" }
					}
				},
				userType: { values: ['любой', 'физ лицо', 'юр лицо'], grid: 2 },
				coleline: {values: [1, 2, 3, 'height'], grid: 2 }
			}
			if(typeof j === 'object') $.extend(true, param, j);
			result = methods.getElementName('Список', param);
			result += methods.getElementParam(param);
			result += methods.getElementEdit();

			cls = typeof param.name.value !== "undefined" ? "fb-btn-"+param.name.value : "";

			result = "<div class='fb-btn "+cls+"' data-element='select'>"+result+"</div>";
			if(typeof j === 'object') methods.builder.mainFields.append(result);
			else return result;
		},
		getRadio: function(j) {
			param = {
				required: { value: 0 },
				label: { grid: 2 },
				name: { grid: 2 },
				radioGroup: {
					values: {
						0: { value: "Радио 1" },
						1: { value: "Радио 2" },
						2: { value: "Радио 3" }
					}
				},
				userType: { values: ['любой', 'физ лицо', 'юр лицо'], grid: 2 },
				coleline: {values: [1, 2, 3, 'height'], grid: 2 }
			}
			if(typeof j === 'object') $.extend(true, param, j);
			result = methods.getElementName('Radio', param);
			result += methods.getElementParam(param);
			result += methods.getElementEdit();
			result = "<div class='fb-btn' data-element='radio'>"+result+"</div>";
			if(typeof j === 'object') methods.builder.mainFields.append(result);
			else return result;
		},
		getCheckbox: function(j) {
			param = {
				required: { value: 0 },
				label: { grid: 2 },
				name: { grid: 2 },
				checkboxGroup: {
					values: {
						0: { value: "Чекбокс 1" },
						1: { value: "Чекбокс 2" },
						2: { value: "Чекбокс 3" }
					}
				},
				userType: { values: ['любой', 'физ лицо', 'юр лицо'], grid: 2 },
				coleline: {values: [1, 2, 3, 'height'], grid: 2 }
			}
			if(typeof j === 'object') $.extend(true, param, j);
			result = methods.getElementName('Checkbox', param);
			result += methods.getElementParam(param);
			result += methods.getElementEdit();
			result = "<div class='fb-btn' data-element='checkbox'>"+result+"</div>";
			if(typeof j === 'object') methods.builder.mainFields.append(result);
			else return result;
		},
		getTitle: function(j) {
			param = {
				label: { value: 'Строка заголовок', grid: 1 },
				userType: { values: ['любой', 'физ лицо', 'юр лицо'], grid: 2 },
				coleline: {values: [1, 2, 3, 'height'], grid: 2 }
			}
			if(typeof j === 'object') $.extend(true, param, j);
			result = methods.getElementName('Заголовок', param);
			result += methods.getElementParam(param);
			result += methods.getElementEdit();
			result = "<div class='fb-btn' data-element='title'>"+result+"</div>";
			if(typeof j === 'object') methods.builder.mainFields.append(result);
			else return result;
		},
		getFile: function(j) {
			param = {
				label: { value: 'Выберите файл', grid: 2 },
				name: { grid: 2 },
				userType: { values: ['любой', 'физ лицо', 'юр лицо'], grid: 2 },
				coleline: {values: [1, 2, 3, 'height'], grid: 2 }
			}
			if(typeof j === 'object') $.extend(true, param, j);
			result = methods.getElementName('Файл', param);
			result += methods.getElementParam(param);
			result += methods.getElementEdit();
			result = "<div class='fb-btn' data-element='file'>"+result+"</div>";
			if(typeof j === 'object') methods.builder.mainFields.append(result);
			else return result;
		},
		getDate: function(j) {
			param = {
				label: { value: 'Выберите дату', grid: 2 },
				name: { grid: 2 },
				userType: { values: ['любой', 'физ лицо', 'юр лицо'], grid: 2 },
				coleline: {values: [1, 2, 3, 'height'], grid: 2 }
			}
			if(typeof j === 'object') $.extend(true, param, j);
			result = methods.getElementName('Дата', param);
			result += methods.getElementParam(param);
			result += methods.getElementEdit();
			result = "<div class='fb-btn' data-element='date'>"+result+"</div>";
			if(typeof j === 'object') methods.builder.mainFields.append(result);
			else return result;
		},
		getElementName: function(name, param) {
			type = name == 'Строка' ? (typeof param.typeInput.value !== "undefined" ? param.typeInput.value : 'Text') : false;
			return "<span class='fb-btext'>"+name+(type ? "<span class='fb-type'>:<span class='fb-typename'>"+type+"</span></span>" : "")+"</span><span class='fb-btext none'><span class='fb-btext-name'>"+(param.label.value ? param.label.value : "")+"</span><span class='red "+(typeof param.required !== 'undefined' && typeof param.required.value !== 'undefined' && param.required.value ? "" : "none")+"'>*</span></span>";
		},
		getElementEdit: function() {
			return "<span class='fb-panel'><span class='fb-edit' onclick='formbuilder_open(this);'></span><span class='fb-remove' onclick='formbuilder_remove(this, \".fb-btn\");'></span></span>";
		},
		getElementParam: function(param) {
			result = "";
			for (var i = 0 in param) {
				switch (i) {
					case 'required':
						result += methods.templateCheckbox("required", "Обязательный", param[i]["value"], param[i]["grid"]);
					break;
					case 'label':
						result += methods.templateInput("label", "Заголовок", param[i]["value"], param[i]["grid"], 1);
					break;
					case 'name':
						result += methods.templateInput("name", "name", param[i]["value"], param[i]["grid"], 1);
					break;
					case 'placeholder':
						result += methods.templateInput("placeholder", "placeholder", param[i]["value"], param[i]["grid"]);
					break;
					case 'value':
						result += methods.templateInput("value", "Строка", param[i]["value"], param[i]["grid"]);
					break;
					case 'textarea':
						result += methods.templateTextarea("textarea", "Текст", param[i]["value"], param[i]["grid"]);
					break;
					case 'typeInput':
						result += methods.templateSelect("typeInput", "Тип поля", param[i]["value"], param[i]["values"], param[i]["grid"]);
					break;
					case 'selectGroup':
						result += methods.templateSelectGroup("selectGroup", param[i]["value"], param[i]["values"]);
					break;
					case 'radioGroup':
						result += methods.templateRadioGroup("radioGroup", param[i]["value"], param[i]["values"]);
					break;
					case 'checkboxGroup':
						result += methods.templateCheckboxGroup("checkboxGroup", param[i]["value"], param[i]["values"]);
					break;
					case 'userType':
						result += methods.templateSelect("userType", 'Тип пользователя', param[i]["value"], param[i]["values"]);
					break;
					case 'coleline':
						result += methods.templateSelect("coleline", 'Ширина поля', param[i]["value"], param[i]["values"]);
					break;
				}
			}
			return "<div class='fb-param'>"+result+"</div>"
		},
		templateCheckbox: function(name, label, value, grid = 1) {
			return "<div class='fb-line fb-line-"+grid+"' data-name='"+name+"'><div class='switch'><label><input type='checkbox' "+(value ? "checked" : "")+" value='1' name='"+name+"' "+(name=="required" ? "onchange='formbuilder_req(this);'" : "")+"><span class='lever'></span><span class='sw-text'>"+(label ? label : "")+"</span></label></div></div>";
		},
		templateInput: function(name, label, value, grid = 1, req) {
			return "<div class='fb-line fb-line-"+grid+"' data-name='"+name+"'><div class='input-field'><input type='text' name='"+(name ? name : "input")+"' "+(value ? "value='"+value+"'" : "")+" "+(label=="Заголовок" ? "onchange='formbuilder_strtourl(this);' onkeyup='formbuilder_nametohead(this);'" : "")+" "+(req ? "data-req='"+label+"'" : "")+"><label for='input' "+(value ? "class='active'" : "")+">"+(label ? label : "")+(req ? "<span class='red'>*</span>" : "")+"</label><span></span></div></div>";
		},
		templateTextarea: function(name, label, value, grid = 1, req) {
			return "<div class='fb-line fb-line-"+grid+"' data-name='"+name+"'><div class='textarea-field'><textarea name='"+(name ? name : "textarea")+"' placeholder='"+label+"'>"+(value ? value : "")+"</textarea></div></div>";
		},
		templateSelect: function(name, label, value, values, grid = 1) {
			options = "";
			values.forEach(function(v){
				options += "<option value='"+v+"' "+(v==value ? "selected" : "")+">"+v+"</option>";
			});
			return "<div class='fb-line fb-line-"+grid+"' data-name='"+name+"'><div class='input-field input-select'><select name='"+name+"' class='ns' "+(label=="Тип поля" ? "onchange='formbuilder_type(this);'" : "")+">"+options+"</select><label>"+(label ? label : "")+"</label></div></div>";
		},
		removeLine: function(grid = 1) {
			return "<div class='fb-line fb-line-"+grid+"'><div class='fb-remove-line'><span onclick='formbuilder_remove(this, \".multi-line\");'></span></div></div>";
		},
		templateSelectGroup: function(name, value, values) {
			res = "";
			id = "options"+Math.floor(Math.random()*1000000);
			for (var v = 0 in values) {
				res += "<div class='multi-line' data-num='"+v+"'>"+methods.templateInput("option", "Опция", values[v]["value"], "8-7")+methods.removeLine(8)+"</div>";
			}
			return "<div class='fb-options' data-options='"+name+"'><div id='"+id+"'>"+res+"</div><a class='add-btn' href='' onclick='add_line(\""+id+"\"); return false;'>добавить еще</a></div>";
		},
		templateRadioGroup: function(name, value, values) {
			res = "";
			id = "options"+Math.floor(Math.random()*1000000);
			for (var v = 0 in values) {
				res += "<div class='multi-line' data-num='"+v+"'>"+methods.templateInput("option", "Радио", values[v]["value"], "8-7")+methods.removeLine(8)+"</div>";
			}
			return "<div class='fb-options' data-options='"+name+"'><div id='"+id+"'>"+res+"</div><a class='add-btn' href='' onclick='add_line(\""+id+"\"); return false;'>добавить еще</a></div>";
		},
		templateCheckboxGroup: function(name, value, values) {
			res = "";
			id = "options"+Math.floor(Math.random()*1000000);
			for (var v = 0 in values) {
				res += "<div class='multi-line' data-num='"+v+"'>"+methods.templateInput("option", "Чекбокс", values[v]["value"], "8-7")+methods.removeLine(8)+"</div>";
			}
			return "<div class='fb-options' data-options='"+name+"'><div id='"+id+"'>"+res+"</div><a class='add-btn' href='' onclick='add_line(\""+id+"\"); return false;'>добавить еще</a></div>";
		},
		setJson: function(obj) {
			for (var name = 0 in obj) {
				nameElement = name.split('|')[0];
				switch (nameElement) {
					case 'input':
						methods.getInput(obj[name]);
					break;
					case 'textarea':
						methods.getTextarea(obj[name]);
					break;
					case 'select':
						methods.getSelect(obj[name]);
					break;
					case 'radio':
						methods.getRadio(obj[name]);
					break;
					case 'checkbox':
						methods.getCheckbox(obj[name]);
					break;
					case 'title':
						methods.getTitle(obj[name]);
					break;
					case 'file':
						methods.getFile(obj[name]);
					break;
					case 'date':
						methods.getDate(obj[name]);
					break;

				}
			}
		},
		getJson: function() {
			if(this.length) methods.element = $(this);
			var json = {},
				builder = methods.element.parent().find("#formBuilder"),
				noReq = 0;
			builder.find(".fb-main input[data-req]").each(function(i, e){
				var el = $(e);
				if(!el.val()){
					processJson({
						form: el.parents("form"),
						error: "Не заполнено обязательное поле - "+el.attr("data-req")
					})
					el.parents(".fb-btn").find(".fb-edit").click();
					noReq = 1;
					return false;
				}
			});
			if(noReq){
				return false;
			}else{
				builder.find(".fb-main [data-element]").each(function(i, e){
					var el = $(e),
						name = el.attr("data-element")+"|"+Math.floor(Math.random()*1000000);
					json[name] = {};
					el.find(".fb-param > .fb-line[data-name]").each(function(i, e){
						s = $(e).attr("data-name");
						json[name][s] = {};
						input = $(e).find('input, select, textarea');
						value = !input.is("[type='checkbox']") ? input.val() : (input.prop("checked") ? 1 : 0);
						json[name][s]['value'] = value;
					});
					el.find("[data-options]").each(function(i, e){
						s = $(e).attr("data-options");
						json[name][s] = {values: {}};
						$(e).find("[data-name='option']").each(function(i, e){
							json[name][s]['values'][i] = {};
							json[name][s]['values'][i]["value"] = $(this).find('input').val()
						});
					});
				});
				return json;
			}
		}
	};
	$.fn.formBuilder = function(method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Метод с именем ' + method + ' не существует');
		}
	};
})(jQuery);

$(window).on('click', '#formBuilder', function(){
	console.log('click');
});
$(window).on('change', '#formBuilder input', function(){
	console.log('click');
});

function formbuilder_remove(el, str){
	$(el).parents(str).remove();
	lightcase.resize();
}
function formbuilder_strtourl(el){
	var name = $(el).parents(".fb-param").find('[data-name="name"] input');
	if(name.val()=="") name.val(transliterate($(el).val())).change();
}
function formbuilder_nametohead(el){
	$(el).parents(".fb-btn").find('.fb-btext-name').text($(el).val());
}
function formbuilder_type(el){
	$(el).parents(".fb-btn").find(".fb-typename").text($(el).val());
}
function formbuilder_req(el){
	el = $(el);
	red = el.parents(".fb-btn").find(".fb-btext .red");
	if(el.prop('checked')) red.removeClass('none');
	else red.addClass('none');
}
function formbuilder_open(el){
	option = {
		duration: el>=0 ? el : 250,
		progress: function() {
			lightcase.resize()
		}
	}
	body = $(el).parents(".fb-btn").find(".fb-param");
	panel = $(el).parents(".fb-panel");
	if(body.hasClass("active")){
		body.removeClass("active").slideUp(option);
		panel.removeClass('active');
	}else{
		$(".fb-param").not(body[0]).removeClass("active").slideUp(option);
		$(".fb-panel").not(panel[0]).removeClass("active");
		body.addClass("active").slideDown(option);
		panel.addClass('active');
	}
}

var a = {"!":"-","@":"-","#":"-","$":"-","%":"-","^":"-","&":"-","*":"-","(":"-",")":"-","~":"-","/":"-","|":"-",".":"-",",":"-"," ":"-","Ё":"YO","Й":"I","Ц":"TS","У":"U","К":"K","Е":"E","Н":"N","Г":"G","Ш":"SH","Щ":"SCH","З":"Z","Х":"H","Ъ":"'","ё":"yo","й":"i","ц":"ts","у":"u","к":"k","е":"e","н":"n","г":"g","ш":"sh","щ":"sch","з":"z","х":"h","ъ":"'","Ф":"F","Ы":"I","В":"V","А":"a","П":"P","Р":"R","О":"O","Л":"L","Д":"D","Ж":"ZH","Э":"E","ф":"f","ы":"i","в":"v","а":"a","п":"p","р":"r","о":"o","л":"l","д":"d","ж":"zh","э":"e","Я":"Ya","Ч":"CH","С":"S","М":"M","И":"I","Т":"T","Ь":"'","Б":"B","Ю":"YU","я":"ya","ч":"ch","с":"s","м":"m","и":"i","т":"t","ь":"'","б":"b","ю":"yu"};
function transliterate(word){
  return word.split('').map(function (char) {
    return a[char] || char;
  }).join("");
}
function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
