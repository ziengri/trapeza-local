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
            $.post(buyOnClick.url, data).done(responsJSON => {
                try {
                    const respons = JSON.parse(responsJSON);
                    if (respons.error) alert(respons.error); else buyOnClick.modal(respons.html, title);
                } catch (error) {
                    console.error(error);
                }
            });
        });
        $('body').on('change', '.buyoneclick input[name="itemCount"]', function () {
            const orderForm = $(this).parents('#order');
            const data = orderForm.data();
            data.action = 'update_count';
            data.count = $(this).val();
            data.type_order = buyOnClick.type_order;
            $.post(buyOnClick.url, data).done(res => {
                try {
                    var _data$order$totalSumD, _data$order2, _data$order$delivery$, _data$order3, _data$order$totaldels, _data$order4;
                    const data = JSON.parse(res);
                    if (data.error) alert(data.error);
                    for (const itemID in data === null || data === void 0 || (_data$order = data.order) === null || _data$order === void 0 ? void 0 : _data$order.items) {
                        var _data$order, _item$price, _item$count, _item$sum;
                        const item = data.order.items[itemID];
                        if (orderForm.data('id') !== item.id) continue;
                        const itemObj = orderForm.find('#item');
                        itemObj.find('.item-price .value').text((_item$price = item.price) !== null && _item$price !== void 0 ? _item$price : '');
                        itemObj.find('[name="itemCount"]').val((_item$count = item.count) !== null && _item$count !== void 0 ? _item$count : 1);
                        itemObj.find('.item-sum .value').text((_item$sum = item.sum) !== null && _item$sum !== void 0 ? _item$sum : '');
                    }
                    orderForm.find('.discontSum .price').text((_data$order$totalSumD = data === null || data === void 0 || (_data$order2 = data.order) === null || _data$order2 === void 0 ? void 0 : _data$order2.totalSumDiscont) !== null && _data$order$totalSumD !== void 0 ? _data$order$totalSumD : 0);
                    orderForm.find('.deliverSum .price').text((_data$order$delivery$ = data === null || data === void 0 || (_data$order3 = data.order) === null || _data$order3 === void 0 || (_data$order3 = _data$order3.delivery) === null || _data$order3 === void 0 ? void 0 : _data$order3.sum_result) !== null && _data$order$delivery$ !== void 0 ? _data$order$delivery$ : 0);
                    orderForm.find('.total_sum_price .price').text((_data$order$totaldels = data === null || data === void 0 || (_data$order4 = data.order) === null || _data$order4 === void 0 ? void 0 : _data$order4.totaldelsum) !== null && _data$order$totaldels !== void 0 ? _data$order$totaldels : 0);
                } catch (e) {
                    console.error(e, res);
                }
            });
            console.log($(this).val());
        });
    },
    delivery(btn, id) {
        const orderForm = btn.parents('#order');
        $.post(buyOnClick.url, {
            delivery: id,
            type_order: this.type_order,
            action: 'delivery'
        }).done(res => {
            try {
                var _data$order$totalSumD2, _data$order6, _data$order$delivery$2, _data$order7, _data$order$totaldels2, _data$order8;
                const data = JSON.parse(res);
                if (data.error) alert(data.error);
                for (const itemID in data === null || data === void 0 || (_data$order5 = data.order) === null || _data$order5 === void 0 ? void 0 : _data$order5.items) {
                    var _data$order5, _item$price2, _item$sum2;
                    const item = data.order.items[itemID];
                    console.log(item, orderForm.data('id'));
                    if (orderForm.data('id') !== item.id) continue;
                    const itemObj = orderForm.find('#item');
                    itemObj.find('.item-price .value').text((_item$price2 = item.price) !== null && _item$price2 !== void 0 ? _item$price2 : '');
                    itemObj.find('.item-sum .value').text((_item$sum2 = item.sum) !== null && _item$sum2 !== void 0 ? _item$sum2 : '');
                }
                orderForm.find('.discontSum .price').text((_data$order$totalSumD2 = data === null || data === void 0 || (_data$order6 = data.order) === null || _data$order6 === void 0 ? void 0 : _data$order6.totalSumDiscont) !== null && _data$order$totalSumD2 !== void 0 ? _data$order$totalSumD2 : 0);
                orderForm.find('.deliverSum .price').text((_data$order$delivery$2 = data === null || data === void 0 || (_data$order7 = data.order) === null || _data$order7 === void 0 || (_data$order7 = _data$order7.delivery) === null || _data$order7 === void 0 ? void 0 : _data$order7.sum_result) !== null && _data$order$delivery$2 !== void 0 ? _data$order$delivery$2 : 0);
                orderForm.find('.total_sum_price .price').text((_data$order$totaldels2 = data === null || data === void 0 || (_data$order8 = data.order) === null || _data$order8 === void 0 ? void 0 : _data$order8.totaldelsum) !== null && _data$order$totaldels2 !== void 0 ? _data$order$totaldels2 : 0);
            } catch (e) {
                console.error(e, res);
            }
        });
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
};
$(document).ready(() => buyOnClick.init());