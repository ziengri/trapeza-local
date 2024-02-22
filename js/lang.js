keywords = {
	ru : {
		delitem : {
			title : 'Удалить товар из корзины?'
		},
		delitems : {
			title : 'Удалить все товары из корзины?'
		}
	},
	en : {
		delitem : {
			title : 'Remove goods from the basket?'
		},
		delitems : {
			title : 'Remove all items from the cart?'
		}
	}
}

leng_type = ($('body').attr('data-lang')?$('body').attr('data-lang'):'ru');
lang = keywords[leng_type];