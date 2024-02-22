$(document).ready(function(){
    setTimeout(function(){
    	comparisonHeightName($(this));
    }, 10);
	const comparisonList = {};
	$('.comparison-list li[data-key]').each(function() {
		const el = $(this)
		const data = el.data('key');
		const val = el.text();

		if (!comparisonList[data]) comparisonList[data] = []
		comparisonList[data].push(val)
	})

	$('body').on('change', '[name="show_table[]"]', function() {
		const btn = $(this);
		const val = btn.val();
		const url = (new URL(location)).searchParams
		url.set('comparison', val);
		history.pushState(null, null, location.pathname + '?' + url.toString());
		$('.comparison-full li[data-key]').removeClass('none');

		if (val == 'differ') {
			for (const key in comparisonList) {
				if (Object.hasOwnProperty.call(comparisonList, key)) {
					const comparisonRow = comparisonList[key];
					if (comparisonRow.length > 1)
					if ([...new Set(comparisonRow)].length == 1 ) $(`.comparison-full li[data-key="${key}"]`).addClass('none');
				}
			}
		}
	})

	const comparisonRadio = (new URL(location)).searchParams.get('comparison');
	if (comparisonRadio) $(`[name="show_table[]"][value="${comparisonRadio}"]`).click().change();

    $("body").on('click', '.comparison-close', function(){
        btn = $(this)
        id = btn.parents('[data-id]').attr('data-id')
        i = btn.parents('.owl-item').index();
        btn.parents('.owl-carousel').trigger('remove.owl.carousel', [i]).trigger('refresh.owl.carousel');
        $.post('/bc/modules/default/index.php',{'user_action': 'comparison', 'action': 'remove','id': id}, function(data){}, 'json');
    })
	$("body").on('click', '.comparison-add', function(){
		btn = $(this)
		comparison = btn.parents('.comparison')
		id = btn.parents('[data-id]').attr('data-id')
		if(comparison.hasClass('active')){
			comparison.removeClass('active')
			action = 'remove';
		}else{
			comparison.addClass('active')
			action = 'add';
		}
		$.post('/bc/modules/default/index.php',{'user_action': 'comparison', 'action': action,'id': id}, function(data){}, 'json');
	})

	$("body").on('click', '.remove_comparison_list', function(){
        $.post('/bc/modules/default/index.php',{'user_action': 'comparison', 'action': 'removeAdd', 'id': 1}, function(data) {
			if (data.succes) location.reload();
		}, 'json');
    })

});

function comparisonHeightName(el, type){
	h = 0;
	$('.comparison-namefull, .comparison-info-first').each(function(){
		el_h = $(this).height();
		if (h<el_h) h=el_h;
	});
	$('.comparison-namefull, .comparison-info-first').height(h);
}
