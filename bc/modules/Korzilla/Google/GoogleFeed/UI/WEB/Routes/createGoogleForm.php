<?
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";
use App\modules\Korzilla\Google\GoogleFeed\UI\WEB\Controllers\Controller;

echo (new Controller())->createGoogleForm();