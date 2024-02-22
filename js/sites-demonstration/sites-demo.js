
$(document).ready(function(){
    var search = window.location.search.substr(1),
	keys = {};

    search.split('&').forEach(function(item) {
    	item = item.split('=');
    	keys[item[0]] = item[1];
    });
    login = keys.login_demo;

	if(typeof login !== "undefined") {
		lightcase.start({
			href: "http://"+login+".krzi.ru/",
			maxWidth: 1920,
			maxHeight: 1080,
			groupClass: "modal-presite modal-presite-3",
            shrinkFactor: 1,
            iframe: {
                width: 'auto',
                height: 'auto',
                frameborder: 0
            },
            onStart: {
				bar: function() {
					if(!$('.pre-line').length) $('#lightcase-sequenceInfo').append("<div class='pre-line'><div class='pre-hsh'><span class='pre-icon'></span><span class='pre-hide'>Скрыть</span><span class='pre-show'>Раскрыть</span></div><div class='mode-icon'><a href='#' class='mode-i mode-i1'></a><a href='#' class='mode-i mode-i2'></a><a href='#' class='mode-i mode-i3'></a></div></div>");
                    
                    $("#lightcase-sequenceInfo").addClass("active");
				}
			},
            onFinish: {
				bar: function() {
                    $("body").on('click', ".mode-i", function(){
                    	a = $(this);
                    	modal = $('#lightcase-case');
                    	modal.removeClass("modal-presite-1 modal-presite-2 modal-presite-3");
                    	if(a.hasClass('mode-i1')) modal.addClass('modal-presite-1');
                    	if(a.hasClass('mode-i2')) modal.addClass('modal-presite-2');
                    	if(a.hasClass('mode-i3')) modal.addClass('modal-presite-3');
                    });
                    $("#lightcase-sequenceInfo").addClass("active");
                    setTimeout(function(){
                        $("#lightcase-sequenceInfo").removeClass("active")
                    }, 1800)
                    $("body").on('click', ".pre-hsh", function(){
                    	el = $(this).parents('#lightcase-sequenceInfo');
                    	el.toggleClass('active');
                    });
				}
			},
		})
	}
	return false;
});


/*
 * Lightcase - jQuery Plugin
 * The smart and flexible Lightbox Plugin.
 */
;
(function($) {
    'use strict';
    var _self = {
        cache: {},
        support: {},
        objects: {},
        init: function(options) {
            return this.each(function() {
                $(this).unbind('click.lightcase').bind('click.lightcase', function(event) {
                    event.preventDefault();
                    $(this).lightcase('start', options);
                });
            });
        },
        start: function(options) {
            _self.origin = lightcase.origin = this;
            _self.settings = lightcase.settings = $.extend(true, {
                groupClass: 'standartModal',
                idPrefix: 'lightcase-',
                classPrefix: 'lightcase-',
                attrPrefix: 'lc-',
                transition: 'elastic',
                transitionIn: null,
                transitionOut: null,
                cssTransitions: true,
                speedIn: 250,
                speedOut: 250,
                maxWidth: 800,
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
                    width: 800,
                    height: 500,
                    frameborder: 0
                },
                flash: {
                    width: 400,
                    height: 205,
                    wmode: 'transparent'
                },
                video: {
                    width: 400,
                    height: 225,
                    poster: '',
                    preload: 'auto',
                    controls: true,
                    autobuffer: true,
                    autoplay: true,
                    loop: false
                },
                attr: 'data-rel',
                href: null,
                type: null,
                reinit: 0,
                typeMapping: {
                    'image': 'jpg,jpeg,gif,png,bmp',
                    'flash': 'swf',
                    'video': 'mp4,mov,ogv,ogg,webm',
                    'inline': '#'
                },
                errorMessage: function() {
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
                markup: function() {
                    $('body').append(_self.objects.overlay = $('<div id="' + _self.settings.idPrefix + 'overlay"></div>'), _self.objects.loading = $('<div id="' + _self.settings.idPrefix + 'loading" class="' + _self.settings.classPrefix + 'icon-spin"></div>'), _self.objects.case = $('<div id="' + _self.settings.idPrefix + 'case" aria-hidden="true" role="dialog"></div>'));
                    _self.objects.case.after(_self.objects.nav = $('<div id="' + _self.settings.idPrefix + 'nav"></div>'));
                    _self.objects.nav.append(_self.objects.closemobile = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-close"></a>'), _self.objects.prev = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-prev"><span>' + _self.settings.labels['navigator.prev'] + '</span></a>').hide(), _self.objects.next = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-next"><span>' + _self.settings.labels['navigator.next'] + '</span></a>').hide(), _self.objects.play = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-play"><span>' + _self.settings.labels['navigator.play'] + '</span></a>').hide(), _self.objects.pause = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-pause"><span>' + _self.settings.labels['navigator.pause'] + '</span></a>').hide());
                    _self.objects.case.append(_self.objects.info = $('<div id="' + _self.settings.idPrefix + 'info"></div>'), _self.objects.content = $('<div id="' + _self.settings.idPrefix + 'content"></div>'));
                    _self.objects.case.append(_self.objects.sequenceInfo = $('<div id="' + _self.settings.idPrefix + 'sequenceInfo"></div>'));
                	_self.objects.content.append(_self.objects.contentInner = $('<div class="' + _self.settings.classPrefix + 'contentInner"></div>'));
                    _self.objects.info.append( _self.objects.title = $('<h4 id="' + _self.settings.idPrefix + 'title"></h4>'), _self.objects.caption = $('<p id="' + _self.settings.idPrefix + 'caption"></p>'), _self.objects.close = $('<a href="#" class="' + _self.settings.classPrefix + 'icon-close"></a>'));
                },
                onInit: {},
                onStart: {},
                onFinish: {},
                onClose: {},
                onCleanup: {}
            }, options, _self.origin.data ? _self.origin.data('lc-options') : {});
            _self._callHooks(_self.settings.onInit);
            _self.objectData = _self._setObjectData(this);
            _self._cacheScrollPosition();
            _self._watchScrollInteraction();
            _self._addElements();
            _self._open();
            _self.dimensions = _self.getViewportDimensions();
        },
        set: function(name, param) {
            _self.settings[name] = param;
        },
        get: function(name) {
            return _self.objects[name];
        },
        getObjectData: function() {
            return _self.objectData;
        },
        _setObjectData: function(object) {
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
            if(objectData.type == 'image'){
            	_self.settings.transition = "scrollHorizontal";
            	_self.settings.shrinkFactor = .85;
            }
            objectData.prevIndex = objectData.currentIndex - 1;
            objectData.nextIndex = objectData.currentIndex + 1;
            return objectData;
        },
        _prefixAttributeName: function(name) {
            return 'data-' + _self.settings.attrPrefix + name;
        },
        _determineLinkTarget: function() {
            return _self.settings.href || $(_self.origin).attr(_self._prefixAttributeName('href')) || $(_self.origin).attr('href');
        },
        _determineAttributeSelector: function() {
            var $origin = $(_self.origin),
                selector = '';
            if (typeof _self.cache.selector !== 'undefined') {
                selector = _self.cache.selector;
            } else if (_self.settings.useCategories === true && $origin.attr(_self._prefixAttributeName('categories'))) {
                var categories = $origin.attr(_self._prefixAttributeName('categories')).split(' ');
                $.each(categories, function(index, category) {
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
        _determineUrl: function() {
            var dataUrl = _self._verifyDataUrl(_self._determineLinkTarget()),
                width = 0,
                density = 0,
                url;
            $.each(dataUrl, function(index, src) {
                if (_self._devicePixelRatio() >= src.density && src.density >= density && _self._matchMedia()('screen and (min-width:' + src.width + 'px)').matches && src.width >= width) {
                    width = src.width;
                    density = src.density;
                    url = src.url;
                }
            });
            return url;
        },
        _normalizeUrl: function(url) {
            var srcExp = /^\d+$/;
            return url.split(',').map(function(str) {
                var src = {
                    width: 0,
                    density: 0
                };
                str.trim().split(/\s+/).forEach(function(url, i) {
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
        _isPartOfSequence: function(rel, expression) {
            var getSimilarLinks = $('[' + _self.settings.attr + '="' + rel + '"]'),
                regexp = new RegExp(expression);
            return (regexp.test(rel) && getSimilarLinks.length > 1);
        },
        isSlideshowEnabled: function() {
            return (_self.objectData.isPartOfSequence && (_self.settings.slideshow === true || _self.objectData.isPartOfSequenceWithSlideshow === true));
        },
        _loadContent: function() {
            if (_self.cache.originalObject) {
                _self._restoreObject();
            }
            _self._createObject();
        },
        _createObject: function() {
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
                    $.each(_self.settings.inline, function(name, value) {
                        $object.attr(_self._prefixAttributeName(name), value);
                    });
                    break;
                case 'ajax':
                    $object = $('<div class="' + _self.settings.classPrefix + 'inlineWrap"></div>');
                    $.each(_self.settings.ajax, function(name, value) {
                        if (name !== 'data') {
                            $object.attr(_self._prefixAttributeName(name), value);
                        }
                    });
                    break;
                case 'flash':
                    $object = $('<embed src="' + _self.objectData.url + '" type="application/x-shockwave-flash"></embed>');
                    $.each(_self.settings.flash, function(name, value) {
                        $object.attr(name, value);
                    });
                    break;
                case 'video':
                    $object = $('<video></video>');
                    $object.attr('src', _self.objectData.url);
                    $.each(_self.settings.video, function(name, value) {
                        $object.attr(name, value);
                    });
                    break;
                default:
                    $object = $('<iframe></iframe>');
                    $object.attr({
                        'src': _self.objectData.url
                    });
                    $.each(_self.settings.iframe, function(name, value) {
                        $object.attr(name, value);
                    });
                    break;
            }
            _self._addObject($object);
            _self._loadObject($object);
        },
        _addObject: function($object) {
            _self.objects.contentInner.html($object);
            _self._loading('start');
            _self._callHooks(_self.settings.onStart);
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
            if(_self.objects.info.outerHeight() > 0) _self.settings.heightHeader = _self.objects.info.outerHeight();
        },
        _loadObject: function($object) {
            switch (_self.objectData.type) {
                case 'inline':
                    if ($(_self.objectData.url)) {
                        _self._showContent($object);
                    } else {
                        _self.error();
                    }
                    break;
                case 'ajax':
                    $.ajax($.extend({}, _self.settings.ajax, {
                        url: _self.objectData.url,
                        type: _self.objectData.requestType,
                        dataType: _self.objectData.requestDataType,
                        data: _self.objectData.requestData,
                        success: function(data, textStatus, jqXHR) {
                            if (_self.objectData.requestDataType === 'json') {
                                _self.objectData.data = data;
                            } else {
                                $object.html(data);
                            }
                            _self._showContent($object);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            _self.error();
                        }
                    }));
                    break;
                case 'flash':
                    _self._showContent($object);
                    break;
                case 'video':
                    if (typeof($object.get(0).canPlayType) === 'function' || _self.objects.case.find('video').length === 0) {
                        _self._showContent($object);
                    } else {
                        _self.error();
                    }
                    break;
                default:
                    if (_self.objectData.url) {
                        $object.on('load', function() {
                            _self._showContent($object);
                        });
                        $object.on('error', function() {
                            _self.error();
                        });
                    } else {
                        _self.error();
                    }
                    break;
            }
        },
        error: function() {
            _self.objectData.type = 'error';
            var $object = $('<div class="' + _self.settings.classPrefix + 'inlineWrap"></div>');
            $object.html(_self.settings.errorMessage);
            _self.objects.contentInner.html($object);
            _self._showContent(_self.objects.contentInner);
        },
        _calculateDimensions: function($object) {
            _self._cleanupDimensions();
            var dimensions = {
                objectWidth: $object.attr('width') ? $object.attr('width') : $object.attr(_self._prefixAttributeName('width')),
                objectHeight: $object.attr('height') ? parseInt($object.attr('height') + 100) : $object.attr(_self._prefixAttributeName('height'))
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
            _self._adjustDimensions($object, dimensions);
        },
		_adjustDimensions: function($object, dimensions) {
            $object.css({
                'width': dimensions.objectWidth,
                'height': dimensions.objectHeight,
                'max-width': dimensions.maxWidth,
                'max-height':  dimensions.maxHeight - _self.settings.heightHeader
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
        _loading: function(process) {
            if (process === 'start') {
                _self.objects.case.addClass(_self.settings.classPrefix + 'loading');
                _self.objects.loading.show();
            } else if (process === 'end') {
                _self.objects.case.removeClass(_self.settings.classPrefix + 'loading');
                _self.objects.loading.hide();
            }
        },
        getViewportDimensions: function() {
            return {
                windowWidth: $(window).innerWidth(),
                windowHeight: $(window).innerHeight()
            };
        },
        _verifyDataUrl: function(dataUrl) {
            if (!dataUrl || dataUrl === undefined || dataUrl === '') {
                return false;
            }
            if (dataUrl.indexOf('#') > -1) {
                dataUrl = dataUrl.split('#');
                dataUrl = '#' + dataUrl[dataUrl.length - 1];
            }
            return _self._normalizeUrl(dataUrl.toString());
        },
        _verifyDataType: function(url) {
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
        _addElements: function() {
            if (typeof _self.objects.case !== 'undefined' && $('#' + _self.objects.case.attr('id')).length) {
            	if(_self.objects.case.attr(_self._prefixAttributeName('type')) != 'image' && _self.isOpen){
	            	_self.settings.reinit = 1;
	            	_self.close();
            	}
                return;
            }
            _self.settings.markup();
        },
        _showContent: function($object) {
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
        _processContent: function() {
            _self.isBusy = true;
            switch (_self.settings.transitionOut) {
                case 'scrollTop':
                case 'scrollRight':
                case 'scrollBottom':
                case 'scrollLeft':
                case 'scrollVertical':
                case 'scrollHorizontal':
                    if (_self.objects.case.is(':hidden')) {
                        _self.transition.fade(_self.objects.case, 'out', 0, 0, function() {
                            _self._loadContent();
                        });
                        _self.transition.fade(_self.objects.contentInner, 'out', 0);
                    } else {
                        _self.transition.scroll(_self.objects.case, 'out', _self.settings.speedOut, function() {
                            _self._loadContent();
                        });
                    }
                    break;
                case 'fade':
                    if (_self.objects.case.is(':hidden')) {
                        _self.transition.fade(_self.objects.case, 'out', 0, 0, function() {
                            _self._loadContent();
                        });
                    } else {
                        _self.transition.fade(_self.objects.case, 'out', _self.settings.speedOut, 0, function() {
                            _self._loadContent();
                        });
                    }
                    break;
                case 'fadeInline':
                case 'elastic':
                    if (_self.objects.case.is(':hidden')) {
                        _self.transition.fade(_self.objects.case, 'out', 0, 0, function() {
                            _self._loadContent();
                        });
                    } else {
                        _self.transition.fade(_self.objects.contentInner, 'out', _self.settings.speedOut, 0, function() {
                            _self._loadContent();
                        });
                    }
                    break;
                default:
                    _self.transition.fade(_self.objects.case, 'out', 0, 0, function() {
                        _self._loadContent();
                    });
                    break;
            }
        },
        _handleEvents: function() {
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
            _self.objects.close.click(function(event) {
                event.preventDefault();
                _self.close();
            });
            if (_self.settings.closeOnOverlayClick === true) {
                _self.objects.overlay.css('cursor', 'pointer').click(function(event) {
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
                _self.objects.prev.click(function(event) {
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
                _self.objects.next.click(function(event) {
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
                    _self.objects.play.click(function(event) {
                        event.preventDefault();
                        _self._startTimeout();
                    });
                    _self.objects.pause.click(function(event) {
                        event.preventDefault();
                        _self._stopTimeout();
                    });
                }
                if (_self.settings.swipe === true) {
                    if ($.isPlainObject($.event.special.swipeleft)) {
                        _self.objects.case.on('swipeleft', function(event) {
                            event.preventDefault();
                            _self.objects.next.click();
                            if (_self.isSlideshowEnabled()) {
                                _self._stopTimeout();
                            }
                        });
                    }
                    if ($.isPlainObject($.event.special.swiperight)) {
                        _self.objects.case.on('swiperight', function(event) {
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
        _addKeyEvents: function() {
            $(document).bind('keyup.lightcase', function(event) {
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
        _startTimeout: function() {
            _self.isSlideshowStarted = true;
            _self.objects.play.hide();
            _self.objects.pause.show();
            _self.cache.action = 'next';
            _self.objects.nav.removeClass(_self.settings.classPrefix + 'paused');
            _self.timeout = setTimeout(function() {
                _self.objects.nav.data('items').next.click();
            }, _self.settings.timeout);
        },
        _stopTimeout: function() {
            _self.objects.play.show();
            _self.objects.pause.hide();
            _self.objects.nav.addClass(_self.settings.classPrefix + 'paused');
            clearTimeout(_self.timeout);
        },
        _setNavigation: function() {
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
            isFirst: function() {
                return (_self.objectData.currentIndex === 0);
            },
            isLast: function() {
                return (_self.objectData.currentIndex === (_self.objectData.sequenceLength - 1));
            }
        },
        _cloneObject: function($object) {
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
        isMobileDevice: function() {
            var deviceAgent = navigator.userAgent.toLowerCase(),
                agentId = deviceAgent.match(_self.settings.mobileMatchExpression);
            return agentId ? true : false;
        },
        isTransitionSupported: function() {
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
            fade: function($object, type, speed, opacity, callback) {
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
                    setTimeout(function() {
                        $object.css(endTransition);
                        setTimeout(function() {
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
            scroll: function($object, type, speed, callback) {
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
                    setTimeout(function() {
                        $object.css(endTransition);
                        setTimeout(function() {
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
            zoom: function($object, type, speed, callback) {
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
                    setTimeout(function() {
                        $object.css(endTransition);
                        setTimeout(function() {
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
        _callHooks: function(hooks) {
            if (typeof(hooks) === 'object') {
                $.each(hooks, function(index, hook) {
                    if (typeof(hook) === 'function') {
                        hook.call(_self.origin);
                    }
                });
            }
        },
        _cacheObjectData: function($object) {
            $.data($object, 'cache', {
                id: $object.attr('id'),
                content: $object.html()
            });
            _self.cache.originalObject = $object;
        },
        _restoreObject: function() {
            var $object = $('[id^="' + _self.settings.idPrefix + 'temp-"]');
            $object.attr('id', $.data(_self.cache.originalObject, 'cache').id);
            $object.html($.data(_self.cache.originalObject, 'cache').content);
        },
        resize: function() {
            if (!_self.isOpen) return;
            if (_self.isSlideshowEnabled()) {
                _self._stopTimeout();
            }
            _self.dimensions = _self.getViewportDimensions();
            _self._calculateDimensions(_self.cache.object);
        },
        _cacheScrollPosition: function() {
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
        _watchResizeInteraction: function() {
            $(window).resize(_self.resize);
        },
        _unwatchResizeInteraction: function() {
            $(window).off('resize', _self.resize);
        },
        _watchScrollInteraction: function() {
            $(window).scroll(_self._cacheScrollPosition);
            $(window).resize(_self._cacheScrollPosition);
        },
        _unwatchScrollInteraction: function() {
            $(window).off('scroll', _self._cacheScrollPosition);
            $(window).off('resize', _self._cacheScrollPosition);
        },
        _assertContentInvisible: function() {
            return $($('body').children().not('[id*=' + _self.settings.idPrefix + ']').get(0)).height() > 0;
        },
        _restoreScrollPosition: function() {
            $(window).scrollTop(parseInt(_self.cache.scrollPosition.top)).scrollLeft(parseInt(_self.cache.scrollPosition.left)).resize();
        },
        _switchToFullScreenMode: function() {
            _self.settings.shrinkFactor = 1;
            _self.settings.overlayOpacity = 1;
            $('html').addClass(_self.settings.classPrefix + 'fullScreenMode');
        },
        _open: function() {
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
            _self.objects.case.removeClass().addClass(_self.settings.groupClass+" lc-type-"+_self.objectData.type);
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
                       	if(_self.settings.reinit) _self.settings.reinit = 0;
                       	else _self.objects.overlay.css('opacity', 0);
                        _self.objects.case.css('opacity', 0);
                        _self.objects.contentInner.css('opacity', 0);
                    }
                    _self.transition.fade(_self.objects.overlay, 'in', _self.settings.speedIn, _self.settings.overlayOpacity, function() {
                        _self.transition.fade(_self.objects.close, 'in', _self.settings.speedIn);
                        _self._handleEvents();
                        _self._processContent();
                    });
                    break;
                default:
                    _self.transition.fade(_self.objects.overlay, 'in', 0, _self.settings.overlayOpacity, function() {
                        _self.transition.fade(_self.objects.close, 'in', 0);
                        _self._handleEvents();
                        _self._processContent();
                    });
                    break;
            }
            $('html').addClass(_self.settings.classPrefix + 'open');
            _self.objects.case.attr('aria-hidden', 'false');
        },
        close: function() {
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
                    _self.transition.fade(_self.objects.case, 'out', _self.settings.speedOut, 0, function() {
                        _self.transition.fade(_self.objects.overlay, 'out', _self.settings.speedOut, 0, function() {
                            _self.cleanup();
                        });
                    });
                    break;
                case 'elastic':
                    _self.transition.zoom(_self.objects.case, 'out', _self.settings.speedOut, function() {
                        _self.transition.fade(_self.objects.overlay, 'out', _self.settings.speedOut, 0, function() {
                            _self.cleanup();
                        });
                    });
                    break;
                default:
                    _self.cleanup();
                    break;
            }
        },
        _unbindEvents: function() {
            _self.objects.overlay.unbind('click');
            $(document).unbind('keyup.lightcase');
            _self.objects.case.unbind('swipeleft').unbind('swiperight');
            _self.objects.prev.unbind('click');
            _self.objects.next.unbind('click');
            _self.objects.play.unbind('click');
            _self.objects.pause.unbind('click');
            _self.objects.close.unbind('click');
        },
        _cleanupDimensions: function() {
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
        cleanup: function() {
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
        _matchMedia: function() {
            return window.matchMedia || window.msMatchMedia;
        },
        _devicePixelRatio: function() {
            return window.devicePixelRatio || 1;
        },
        _isPublicMethod: function(method) {
            return (typeof _self[method] === 'function' && method.charAt(0) !== '_');
        },
        _export: function() {
            window.lightcase = {};
            $.each(_self, function(property) {
                if (_self._isPublicMethod(property)) {
                    lightcase[property] = _self[property];
                }
            });
        }
    };
    _self._export();
    $.fn.lightcase = function(method) {
        if (_self._isPublicMethod(method)) {
            return _self[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return _self.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.lightcase');
        }
    };
})(jQuery);
