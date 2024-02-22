



$(document).ready(function() {
    window.MicroModal.cookie = {
        set: function(id) {
            const int_id = Number(id.replace('modal-', '').trim());
            console.log(int_id);
            const expires = Number(window.popup_kz[int_id]?.interval_show ?? 0);
            $.cookie(id, '1', { expires , domain: document.domain.split(".").slice(-2).join("."), path: '/'});
        },
        get: function (id) {
            return ($.cookie(id) ? true : false);
        }
    }

    for (const id in window.popup_kz) {
        const popup = window.popup_kz[id];
        if (window.MicroModal.cookie.get(`modal-${popup.id}`)) return;

        $('body').prepend(`
        <div class="modal micromodal-slide" id="modal-${popup.id}" aria-hidden="true" style="z-index:200${popup.id};">
          <div class="modal__overlay" tabindex="-1" data-popup-close="modal-${popup.id}">
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-${popup.id}-title">
              <header class="modal__header">
                <h2 class="modal__title" id="modal-${popup.id}-title">
                ${popup.Title}
                </h2>
                <button class="modal__close" aria-label="Close modal" data-popup-close="modal-${popup.id}"></button>
              </header>
              <main class="modal__content" id="modal-${popup.id}-content">
                ${popup.content}
              </main>
              <!--<footer class="modal__footer">
                <button class="modal__btn modal__btn-primary">Continue</button>
                <button class="modal__btn" data-micromodal-close aria-label="Close this dialog window">Close</button>
              </footer>-->
            </div>
          </div>
        </div>`);
    }

    window.MicroModal.init({
        onShow: modal => console.log(`${modal.id} is shown`),
        onClose: modal => console.log(`${modal.id} is hidden`),
        // openTrigger: 'data-custom-open',
        closeTrigger: 'data-popup-close',
        openClass: 'is-open',
        // disableScroll: true,
        // disableFocus: false,
        // awaitOpenAnimation: false,
        // awaitCloseAnimation: false,
        // debugMode: true
      });

    for (const id in window.popup_kz) {
        const popup = window.popup_kz[id];
        setTimeout(() => {
            window.MicroModal.show(`modal-${popup.id}`);
        }, popup.delay * 1000)
    }

    $('body').on('click', '[data-popup-close]', function(e) {
        if ($(e.target).data('popup-close')) {
            window.MicroModal.cookie.set($(this).data('popup-close'));
            window.MicroModal.close($(this).data('popup-close'));
        }
    })
})