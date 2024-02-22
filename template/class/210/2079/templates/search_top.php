<form class="catalog_search" method='GET' action='<?=$hrefPrefix?>'>
    <input class="hidden" id='search-info' type="checkbox">
    <label class="search-info-icon" for='search-info'>i</label>
    <div class="search-info modal-number-info">
        <span class="modal-vin-info-close"></span>
        <div class="number-info">
            <p>Поиск по марке: цифры, латиница/кириллица. Поиск иномарок на кириллице (бмв, хендай и др.)</p>
            <p>Поиск по модели: цифры, латиница/кириллица</p>
            <p>Поиск по VIN/кузову: марки Abarth, Alfa-Romeo, Fiat, Lancia, Audi, Skoda, Seat, Volkswagen, Bmw, Mini, Rolls-Royce, Kia, Hyundai, Nissan, Infinity, Toyota, Lexus</p>
        </div>
    </div>
    <input required class="search_vim" id="search_vim" type='text' name='text' placeholder=' ' value="<?=$searchValue?>">
    <label class="form__label" for='search_vim'>Поиск по VIN, кузову, марке или модели</label>
    <input class="button button--green" type='submit' value="Найти">
    <input type='hidden' name='search' value='1'>
</form>