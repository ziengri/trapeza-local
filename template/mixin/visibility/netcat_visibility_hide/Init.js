// Дополнительные действия для скрытия блока во флексе в строку
(function(params) {
    if (params.breakpoint_type !== 'block') {
        return;
    }

    var block_element = params.block_element,
        parent_element = block_element.parentNode,
        parent_computed_style = getComputedStyle(parent_element);

    if (parent_computed_style.display !== 'flex' || parent_computed_style.flexDirection !== 'row') {
        return;
    }

    function on_parent_resize() {
        block_element.style.display = '';
        setTimeout(function() {
            if (getComputedStyle(block_element).height === '0px') {
                block_element.style.display = 'none';
            }
        }, 1); // даём примениться стилям CSS Element Queries
    }

    setTimeout(on_parent_resize, 50); // даём примениться всем CSS Element Queries (и надеемся, что 50мс хватит)
    var sensor = new ResizeSensor(parent_element, on_parent_resize);

    return {
        detach: function() { sensor.detach(on_parent_resize); }
    };
});