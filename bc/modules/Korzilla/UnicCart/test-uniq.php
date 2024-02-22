<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Уникальные товары</title>
</head>
<body>
<pre>
<?
require_once $_SERVER['DOCUMENT_ROOT'] . "/autoload.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/vars.inc.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/bc/connect_io.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/bc/modules/default/function.inc.php";

use App\modules\Korzilla\UnicCart\UnicCart;


$Subdivision = [
    "Catalogue_ID" => '1112', // $self->current_sub["Catalogue_ID"]
    "Parent_Sub_ID" => '323622', // $self->current_sub["Parent_Sub_ID"]
    "Subdivision_ID" => '323886', // $self->Subdivision_ID
    "Subdivision_IDS" => '', // $self->Subdivision_IDS
];
$Message = [
    "cc" => '393310', // $self->cc
    "Message_ID" => '2465985', // $self->id
    "vendor" => '', // $self->vendor
    "nameFull" => 'Ланч-бокс ', // $self->nameFull
];
$kzn = '27';
$chn = '46';
$UnicCart_kzn = new UnicCart($Message, $Subdivision, $kzn);
pre_dump($UnicCart_kzn->Message);

$UnicCart_chn = new UnicCart($Message, $Subdivision, $chn);
pre_dump($UnicCart_chn->Message);


echo "<hr>";


$Subdivision = [
    "Catalogue_ID" => '1112', // $self->current_sub["Catalogue_ID"]
    "Parent_Sub_ID" => '323622', // $self->current_sub["Parent_Sub_ID"]
    "Subdivision_ID" => '323893', // $self->Subdivision_ID
    "Subdivision_IDS" => '', // $self->Subdivision_IDS
];
$Message = [
    "cc" => '393317', // $self->cc
    "Message_ID" => '2466035', // $self->id
    "vendor" => '', // $self->vendor
    "nameFull" => 'Влажные салфетки  ', // $self->nameFull
];
$UnicCart_kzn2 = new UnicCart($Message, $Subdivision, $kzn);
pre_dump($UnicCart_kzn2->Message);

$UnicCart_chn2 = new UnicCart($Message, $Subdivision, $chn);
pre_dump($UnicCart_chn2->Message);

?>
</pre>
</body>
</html>