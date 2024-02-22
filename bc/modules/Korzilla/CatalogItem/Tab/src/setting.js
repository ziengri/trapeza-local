$('#row-tabs').nestedSortable({
    forcePlaceholderSize: true,
    handle: '.dropthree',
    helper: 'clone',
    items: 'li',
    opacity: .6,
    placeholder: 'placeholder',
    revert: 100,
    tolerance: 'pointer',
    toleranceElement: '> div',
    maxLevels: 0,
    isTree: true,
    expandOnHover: 1000,
    startCollapsed: false,
    ProtectRoot: true,
    start: function(event, ui) {
        $('#gr_cart_full_tabs').addClass('siteTree-active');
    },
    stop: function(event, ui) {
        $('#gr_cart_full_tabs').removeClass('siteTree-active');
    },
    update: function(event, ui) {
        const serialize = $('#row-tabs').nestedSortable('toArray');
        const result = [];
        for (const element of serialize) {
            if (!element.id) continue;
            result.push($(`#row-tabs .row-tab#id-${element.id}`).data('id'));
        }
        $.post('/bc/modules/Korzilla/CatalogItem/Tab/controller.php?action=dragged_tab', {
            'serialize': result
        }, (data) => {
            console.log(data);
        });
    }
})
// $('#gr_cart_full_tabs a.add-btn').on('click', function(e) {
//     e.preventDefault();
//     console.log('click');
// })