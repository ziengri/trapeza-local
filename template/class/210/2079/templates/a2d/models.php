<?php  require $settings_array[template_path].'top.php';?>
    <script>
        function searchBlock() {
            var input, filter, row, content, i;
            input = $(".catalog_search .search_vim");
            filter = input.val().toUpperCase();
            row = $("#acat-body .subdivision-items .sub");
            for (i = 0; i < row.length; i++) {
                content = row[i].innerHTML.replace(/<[^>]+>/g,' ').replace(/\s+/g,' ').toUpperCase();
                if (content.indexOf(filter) > -1) {
                    row[i].style.display = "";
                } else {
                    row[i].style.display = "none";
                }
            }
        }
    </script>
    <div class="catalog_search">
        <input class="search_vim" id="number" type="text" onkeyup="searchBlock()" placeholder=" ">
        <label class="form__label" for='search_vim'>Фильтр по модели</label>
    </div>
    <?=subdivisions_acat($models, $hrefPrefix."?type={$mark->type}&mark={$mark->short_name}")?>

<?php  require $settings_array[template_path].'bottom.php';?>