window.yml = {
    status(id)
    {
        $.getJSON(`/bc/modules/default/controller_yml.php?action=status&message_id=${id}`, (data) => {
            console.log(data, id);
            const item = $(`div[data-id="${id}"]`);
            if (data?.message) {
                if (data.status == 1 && data?.item != undefined && data?.total_item != undefined) {
                    data.message += ` :${data.item}/${data.total_item}`;
                }
                item.find('.v-line-info .date .value').text(data.message).removeClass('loading');
            }
            if (data?.link) { 
                if (typeof data.link == "object") {
                    if (data.link.length > 0) {
                        item.find('.v-line-info .value').removeClass('loading');
                        item.find('tr.link').remove();
                        
                        data.link.reverse().forEach(el => {
                            item.find('tr.id').after(`<tr class="link">
                                            <td class="name">Ссылка:</td>
                                            <td class="value"><a class="link" href="${el}" target="_blank">${el}</a></td>
                                        </tr>`);
                        })
                    }
                } else {
                    item.find('.v-line-info .value').removeClass('loading');
                    item.find('.v-line-info .value a.link').attr('href', data.link).text(data.link);
                }
                

            }
    

        }).always((data) => {
            if (data?.status == 1 || data?.status == undefined) {
                setTimeout(() => {this.status(id)}, 5000);
            }
        })
    }
}


