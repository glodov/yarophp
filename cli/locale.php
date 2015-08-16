<?

include_once( dirname(__DIR__).'/includes/application.php' );

Application::run('config,locale');

use Helper\Locale as Locale;
use Helper\Console as Console;
use Core\Runtime as Runtime;

function compile($dir, $locale, $file, $name, $module)
{
	Locale::clear();
	Locale::set($locale);

	//Locale::load('Common', 'DateTime', 'Backend');
	Locale::load($name, true);

	$data = json_encode(Locale::get(true), JSON_PRETTY_PRINT);
	$file = $dir . '/' . $module . '.json';

	$md5 = '';
	if (file_exists($file))
	{
		$md5 = md5(file_get_contents($file));
	}
	if ($md5 == md5($data))
	{
		return false;
	}
	file_put_contents($file, $data);
	Console::log($file);
	return true;
}

$arr = Locale::getLocales();

foreach ($arr as $locale)
{
	$dir = Application::dirRoot() . '/frontend/i18n/' . $locale;
	if (!is_dir($dir))
	{
		mkdir($dir, 0755, true);
	}

	$locale_dir = Runtime::get('LOCALE_DIR') . '/' . $locale;
	$module_dir = $locale_dir . '/Backend';
	foreach (glob($module_dir . '/*.ini') as $file)
	{
		$name = substr($file, strlen($locale_dir) + 1);
		$module = substr($name, strlen('modules/'), strlen($name) - strlen('modules/') - 4);
		compile($dir, $locale, $file, $name, 'Backend_' . $module);
	}
	compile($dir, $locale, $locale_dir . '/Backend.ini', 'Backend.ini', 'Backend');
}
