<?

include('includes/application.php');

use \Helper\Console as Console;

Console::behave(Console::MEMORY | Console::BACKTRACE);

$app = Application::run('cache', 'config', 'database', 'controller');

echo $app->getResponse();

<<<<<<< HEAD
// echo '<pre id="app-log">';
// Console::flush();
// echo "Loaded classes: \n\t";
// echo implode("\n\t", \Core\Autoload::getLoaded()) . "\n";
// echo '</pre>';
=======
echo '<pre id="app-log">';
Console::flush();
echo '</pre>';
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
