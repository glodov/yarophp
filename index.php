<?

include('includes/application.php');

use \Helper\Console as Console;

Console::behave(Console::MEMORY | Console::BACKTRACE);

$app = Application::run('cache', 'config', 'database', 'controller');

echo $app->getResponse();

echo '<pre>';
Console::flush();
echo '</pre>';
