const buyOnClick = {
    url: '/bc/modules/Korzilla/Cart/controller.php',
    type_order: 'buy_one_click',
    init: function () {
        $('body').on('click', 'a.buy_one_click', function () {
            const btn = $(this);
            const obj = btn.parents('[data-id]:first');
            const data = obj.data();
            const title = btn.data('title');

            if (btn.hasClass('colors-required') && !obj.find('.select-color .color-item.active').length) {
                const name = obj.find(".js-variable.color-body").data('name');
                alert('Выберите "' + name + '"');
                return false;
            }

            data.type_order = buyOnClick.type_order;
            data.action = 'add';

            $.post(buyOnClick.url, data)
                .done((responsJSON) => {
                    try {
                        const respons = JSON.parse(responsJSON);
                        if (respons.error) alert(respons.error);
                        else buyOnClick.modal(respons.html, title);
                    } catch (error) {
                        console.error(error);
                    }

                })
        })

        $('body').on('change', '.buyoneclick input[name="itemCount"]', function () {
            const orderForm = $(this).parents('#order');
            const data = orderForm.data();
    
            data.action = 'update_count';
            data.count = $(this).val();
            data.type_order = buyOnClick.type_order;
            $.post(buyOnClick.url, data)
                .done((res) => {
                    try {
                        const data = JSON.parse(res);
                        if (data.error) alert(data.error);
                        for (const itemID in data?.order?.items) {
                            const item = data.order.items[itemID];

                            if (orderForm.data('id') !== item.id) continue;
                            const itemObj = orderForm.find('#item');
                            itemObj.find('.item-price .value').text(item.price ?? '');
                            itemObj.find('[name="itemCount"]').val(item.count ?? 1);
                            itemObj.find('.item-sum .value').text(item.sum ?? '');
        
                        }
                        orderForm.find('.discontSum .price').text(data?.order?.totalSumDiscont ?? 0);
                        orderForm.find('.deliverSum .price').text(data?.order?.delivery?.sum_result ?? 0);
                        orderForm.find('.total_sum_price .price').text(data?.order?.totaldelsum ?? 0);
                    } catch (e) {
                        console.error(e, res);
                    }
                })
            console.log($(this).val());
        })
    },
    delivery(btn, id) {
        const orderForm = btn.parents('#order');
        $.post(buyOnClick.url, {delivery: id, type_order: this.type_order, action: 'delivery'})
            .done((res) => {
                try {
                    const data = JSON.parse(res);
                    if (data.error) alert(data.error);

                    for (const itemID in data?.order?.items) {
                        const item = data.order.items[itemID];
                        console.log(item, orderForm.data('id'));
                        if (orderForm.data('id') !== item.id) continue;
                        const itemObj = orderForm.find('#item');
                        itemObj.find('.item-price .value').text(item.price ?? '');
                        itemObj.find('.item-sum .value').text(item.sum ?? '');
                    }

                    orderForm.find('.discontSum .price').text(data?.order?.totalSumDiscont ?? 0);
                    orderForm.find('.deliverSum .price').text(data?.order?.delivery?.sum_result ?? 0);
                    orderForm.find('.total_sum_price .price').text(data?.order?.totaldelsum ?? 0);

                } catch (e) {
                    console.error(e, res);
                }
            })
    },
    modal: function (html, title) {
        lightcase.start({
            htmlContent: html,
            type: 'html',
            groupClass: 'buyoneclick',
            maxWidth: 390,
            title: title
        });
    }
}
$(document).ready(() => buyOnClick.init())
