const PothtaRussia = {
    init: function()
    {
        if (document.querySelector('form#order')) {
            if (typeof ecomStartWidget !== 'function') {
                const script = document.createElement('script');
                script.src = 'https://widget.pochta.ru/map/widget/widget.js';
                script.onload = function() {
                    if (!PothtaRussia.isEcomStartWidget) PothtaRussia.init();
                    PothtaRussia.isEcomStartWidget = true;
                }
                document.body.append(script);
            } else {
                PothtaRussia.isInit = true;
                if (PothtaRussia.isStart) {
                    PothtaRussia.start();
                }
            }

            PothtaRussia.orderForm = document.querySelector('form#order');
            PothtaRussia.address = PothtaRussia.orderForm.querySelector('textarea[name="customf[address][value]"]');
            PothtaRussia.city = PothtaRussia.orderForm.querySelector('input[name="customf[city][value]"]');
            PothtaRussia.descr = PothtaRussia.orderForm.querySelector('.PR-post-name');
            PothtaRussia.deliveryPrice = PothtaRussia.orderForm.querySelector('.deliverSum span');
            PothtaRussia.totalSum = PothtaRussia.orderForm.querySelector('.total_sum_price span');
        }
    },
    start: function()
    {
        if (!PothtaRussia.modalWindow) {
            PothtaRussia.view.createMap()
        }
        if (!PothtaRussia.isInit) {
            PothtaRussia.isStart = true;
            PothtaRussia.init();
        } else {
            $.get('/bc/modules/default/index.php', {
                user_action: 'pochta_russia',
                method: 'getParam',
            }, function(res) {
                console.log(res);
                if (res.success) {
                  
                    ecomStartWidget({
                        id: res.data.widgetID,
                        callbackFunction: PothtaRussia.callbackPochta,
                        containerId: 'PR_widget',
                        weight: res.data.weight * 1000,
                        sumoc: res.data.ordersum * 100, 
                    });
                } else {
                    PothtaRussia.descr.innerHTML = res.description
                }
    
            }, 'json')
        }
    },
    view: {
        createMap: function()
        {
            document.body.insertAdjacentHTML('beforeend', 
            `<div id="PR_box">
                <div id="PR__substrate" onclick="PothtaRussia.view.removeMap()"></div>
                <div id="PR_widget"></div>
            </div>`);
            PothtaRussia.modalWindow = document.querySelector('#PR_box');
        },
        removeMap: function()
        {
            if (PothtaRussia.modalWindow) {
                PothtaRussia.modalWindow.remove();
                PothtaRussia.modalWindow = null;
            }
        },
        order: function(res)
        {
            console.log(res)
            console.log(PothtaRussia)
            if (res.success) {
                PothtaRussia.address.value = res.description;
            }
            PothtaRussia.descr.innerHTML = res.description;
            PothtaRussia.deliveryPrice.innerHTML = res.price;
            PothtaRussia.totalSum.innerHTML = res.totaldelsum;
        }
    },
    callbackPochta: function(data)
    {
        const address = `${data.indexTo}, ${data.regionTo}, ${data.cityTo}, ${data.addressTo}`;
        console.log(data);
        $.get('/bc/modules/default/index.php', {
            user_action: 'pochta_russia',
			method: 'select_chooser',
            address: address,
            price: data.cashOfDelivery,
            mailType: data.mailType,
            pvzType: data.pvzType,
            pvzCode: data.indexTo
        }, function(res) {
            res.city = data.cityTo;
            PothtaRussia.view.order(res);
            PothtaRussia.view.removeMap();
        }, 'json')
        
    },
    reCall: function ()
    {
        $.get('/bc/modules/default/index.php', {
            user_action: 'pochta_russia',
			method: 'reCall',
        }, function(res) {
            PothtaRussia.view.order(res);
        }, 'json')
    }
}
PothtaRussia.init()
