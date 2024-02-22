<?

namespace App\modules\Korzilla\Google\GoogleFeed\UI\WEB\Controllers;

use App\modules\Korzilla\Google\GoogleFeed\UI\WEB\View\View;

class Controller
{
    public function createGoogleForm()
    {
        return (new View())->render('../View/index.php');
    }
}