<?

require_once 'Mobile_Detect.php'; // Подключаем скрипт
$detect = new Mobile_Detect; // Создаём экземпляр класса





$ios = "#";
$android = "https://play.google.com/store/apps/details?id=korzilla.russkaya_trapeza.app";
//$android = "/mobile/app-release.apk";
//$huawei = "";

// Условие - если это устройство от Apple
if( $detect->isiOS() ){
    header("Location: {$ios}");
	exit;
}

/*
if ($detect->is('Huawei')) {
	header("Location: {$huawei}");
	exit;
}*/

// Условие - если это устройство от Google
if( $detect->isAndroidOS() ){
    header("Location: {$android}");
	exit;
}


echo "<html>
<head>
<title>Скачать приложение</title>
<style>
body {background:#E6E6E6;text-align:center;}
.text {margin:40px 0; font-size:20px;font-family:arial}
</style>
</head>

<body>
<div class=text></div>

<a href='{$ios}' title='Приложение для iOS'><img style='width:300px' src='appstore.png'></a>
<br><br><br><br>
<a href='{$android}' title='Приложение для Android'><img  style='width:300px' src='googleplay.png'></a>

</body>
</html>";
