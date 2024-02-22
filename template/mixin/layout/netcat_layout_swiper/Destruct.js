(function(params) {
    var list_element = params.list_element,
        swiper_container = list_element.parentNode;

    if (!list_element || !swiper_container.swiper) {
        return;
    }

    function remove_swiper_classes(element) {
        element.className = element.className.replace(/\bswiper-[\w-]+\s?\b/g, '').trim();
    }

    var swiper_container_parent = swiper_container.parentNode,
        slides = list_element.children;

    // cleanStyles=false: Swiper полностью очищает атрибут style, чего хотелось бы на всякий случай избежать
    swiper_container.swiper.destroy(true, false);
    // так как destroy() вызван с cleanStyles=false, Swiper может оставить transform: translate3d,
    // из-за чего список будет невидим
    list_element.style.transform = '';

    remove_swiper_classes(list_element);
    swiper_container_parent.insertBefore(list_element, swiper_container);
    swiper_container_parent.removeChild(swiper_container); // IE: нет метода Element.remove()

    for (var i = 0; i < slides.length; i++) {
        remove_swiper_classes(slides[i]);
        var style = slides[i].style;
        style.width = style.maxWidth = style.marginRight = null;
    }

});