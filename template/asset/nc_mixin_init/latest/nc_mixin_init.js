/**
 * Инициализация JS миксина
 * @param {String} block_selector селектор блока, к которому применён миксин
 * @param {String} list_selector селектор элемента со списком в блоке
 * @param {String} mixin_selector дополнительный селектор для применения миксина
 * @param {Number} from брейкпоинт начала диапазона действия миксина (включительно)
 * @param {Number} to брейкпоинт конца диапазона действия миксина (менее указанного значения)
 * @param {Function} init функция, выполняющаяся при инициализации миксина
 *   Будет передан объект с ключами:
 *      block_element: {Element} — элемент, содержащий блок
 *      list_element: {Element} — элемент, содержащий список записей
 *      settings: {Object} — настройки миксина
 *      breakpoint_type: {String} — к чему применяются брейкпоинты (block, viewport)
 * @param {Function} destruct функция, выполняющаяся при прекращении действия миксина
 *   Будет передан объект как в init, дополнительно может присутствовать ключ init_result с
 *   возвращёнными init-функцией данными (если они были)
 * @param {Object} settings объект с настройками
 * @param {String} breakpoint_type block|viewport
 */
function nc_mixin_init(block_selector, list_selector, mixin_selector, from, to, init, destruct, settings, breakpoint_type) {
    var block_element = document.querySelector(block_selector),
        list_element = block_element.querySelector(list_selector),
        was_inside_range,
        handler_params = {
            block_element: block_element,
            list_element: list_element,
            settings: settings,
            breakpoint_type: breakpoint_type
        },
        check_width;

    if (!block_element) {
        return;
    }

    function element_matches_mixin_selector() {
        return !mixin_selector ||
               (block_element.matches && block_element.matches(mixin_selector)) ||
               (block_element.msMatchesSelector && block_element.msMatchesSelector(mixin_selector)); // IE9—Edge 14
    }

    function resize_handler() {
        var is_inside_range = check_width() && element_matches_mixin_selector();

        if (!was_inside_range && is_inside_range && init) {
            handler_params.init_result = init(handler_params);
        }

        if (was_inside_range && !is_inside_range && destruct) {
            destruct(handler_params);
        }

        was_inside_range = is_inside_range;
    }

    // ждём изменения размеров
    if (breakpoint_type === 'block') {
        new ResizeSensor(block_element, resize_handler);
        check_width = function() {
            return block_element.offsetWidth >= from && block_element.offsetWidth < to;
        };
    } else { // сейчас есть только два вида брейкпоинтов (block, viewport)
        window.addEventListener('resize', resize_handler);
        check_width = function() {
            return window.matchMedia
                    ? window.matchMedia('(min-width:' + from + 'px) and (max-width:' + (to - 0.01) + 'px)').matches
                    : window.innerWidth >= from && window.innerWidth < to; // IE9—10
        };
    }

    resize_handler();

    // если есть дополнительный селектор для применения миксина, следим также за изменением атрибута class
    if (mixin_selector && window.MutationObserver) { // IE11+ (IE9—10 в пролёте; можно повесить на интервал)
        new MutationObserver(resize_handler).observe(block_element, { attributeFilter: ['class'] });
    }

}
