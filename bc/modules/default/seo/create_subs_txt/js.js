const respons = new bootstrap.Modal(document.querySelector('#respons'));

$("#form-ajax").submit(function(e) {
    e.preventDefault();
    const url = $(this).attr('action');
    const form = new FormData(this);

    $.ajax({
        url: url,
        type: 'POST',
        data: form,
        success: function (res) {
           console.log(res);
            if (res.status !== 1 || res.data === 'undefined') {
                $('.modal-title').text('Ошибка');
                $('.modal-body').text(res.message ?? '');
                respons.show();
                return true;
            }

            window.catalogData = res.data;
            $('.modal-title').text('Данные валидны');

            let html = `<p>${res.message}</p>`;
            html += `<ul>`;
            for(key in window.catalogData.subs) {
                html += `<li>${window.catalogData.subs[key]}</li>`;
            }
            html += `</ul>`;
            $('.modal-body').html(html);
            $('.modal-footer .submit').css({'display':'block'})
            respons.show();
            return true;
        },
        dataType: 'text json',
        cache: false,
        contentType: false,
        processData: false
    });
});

$('.modal-footer .submit').on('click', function() {
    window.catalogData.action = 'create';
    $.ajax({
        url: $("#form-ajax").attr('action'),
        type: 'POST',
        data: jsonToFormData(window.catalogData),
        success: function (res) {
            if (res.status !== 1 || res.data === 'undefined') {
                $('.modal-title').text('Ошибка');
                $('.modal-body').text(res.message ?? '');
                respons.show();
                return true;
            }

            $('.modal-title').text(res.message);

            html = `<ul>`;
            for(sub of res.data) {
                html += `<li>${sub[0]}: ${sub[1]}</li>`;
            }
            html += `</ul>`;
            $('.modal-body').html(html);
            $('.modal-footer .submit').css({'display':'none'});
            return true;
        },
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false
    });
})

function buildFormData(formData, data, parentKey) {
    if (data && typeof data === 'object' && !(data instanceof Date) && !(data instanceof File)) {
      Object.keys(data).forEach(key => {
        buildFormData(formData, data[key], parentKey ? `${parentKey}[${key}]` : key);
      });
    } else {
      const value = data == null ? '' : data;
  
      formData.append(parentKey, value);
    }
  }
  
  function jsonToFormData(data) {
    const formData = new FormData();
    
    buildFormData(formData, data);
    
    return formData;
  }
