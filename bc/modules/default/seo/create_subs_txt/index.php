<?php

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];

require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

if (!getIP('office')) die;

$current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <title>Создания разделов из файла *.txt</title>
</head>
<body class="container">
    <div class="row title mb-3 align-items-center">
        <h1>Создать разделы на сайте "<?=$current_catalogue['Catalogue_Name']?>" (<?=$current_catalogue['Domain']?>)</h1>
    </div>
    <div class="row form justify-content-center">
        <form action="./controler.php" method="post" id="form-ajax" class="col-4">
            <input type="hidden" name="catalogue" enctype="multipart/form-data" value="<?=$current_catalogue['Catalogue_ID']?>">
            <input type="hidden" name="action" value="validate">
            <div class="container">
                <div class="mb-3 row">
                    <label for="id-sub" class="form-label">ID раздела</label>
                    <input type="number" class="form-control" id="id-sub" name="sub_id">
                </div>
                <div class="mb-3 row">
                    <label class="form-check-label" for="sub_id_find">
                        <input class="form-check-input" type="checkbox" name="sub_id_find" value="1" id="sub_id_find">
                        Выводить товар только из этого раздела
                    </label>
                </div>
                <div class="mb-3 row">
                    <label for="id-sub" class="form-label">ID разделы через запятую</label>
                    <input type="text" class="form-control" id="id-sub" name="sub_id_serch">
                </div>
                <div class="mb-5 row">
                    <label for="formFile" class="form-label">Файл с разделами (.txt) </label>
                    <input class="form-control" type="file" accept=".txt" id="formFile" name="sub_file">
                    <div id="file-help" class="form-text">Каждый раздел с новой строки</div>
                </div>

                <div class="row justify-content-center">
                    <button type="submit" class="btn btn-primary col-4">Проверить</button>
                </div>
            </div>
        </form>
    </div>


    <div class="modal fade" id="respons" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            ...
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            <button type="button" class="btn btn-primary submit" style="display: none;">Создать</button>
        </div>
        </div>
    </div>
    </div>
    <script src="./js.js"></script>
</body>
</html>