<?
namespace App\modules\Korzilla\Google\GoogleFeed\UI\WEB\View;

class View
{
    public function render(string $path, array $fields = [])
    {
        ob_start();
        include $path;
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}