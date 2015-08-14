<?

namespace Helper;

class Console
{
    const OUTPUT = 1;
    const FILE = 3;
    const MEMORY = 7;

    private static $started_at, $log = [], $behavior = self::OUTPUT;

    private function getFilePath()
    {
        return \Application::dirLogs() . '/console.log';
    }

    public static function start()
    {
        self::$started_at = microtime(true);
    }

    public static function behave($behavior = self::OUTPUT)
    {
        self::$behavior = $behavior;
    }

    public static function log($source, $data = null)
    {
        if (null === self::$started_at)
        {
            self::start();
        }
        $string = '';
        if (null === $data)
        {
            $data = $source;
        }
        if (is_array($data))
        {
            foreach ($data as $value)
            {
                self::log($source, $value);
            }
            return;
        }
        if (is_object($source))
        {
            $string = get_class($source) . '> ';
        }
        $string .= $data;
        $time = microtime(true) - self::$started_at;

        if (self::OUTPUT === self::$behavior)
        {
            printf("%1.03f %s\n", $time, $string);
        }
        else if (self::FILE === self::$behavior)
        {
            file_put_contents(self::getFilePath(), date('d.m.y H:i:s ') . sprintf("%1.03f %s\n", $time, $string), FILE_APPEND);
        }
        else
        {
            self::$log[] = ['time' => $time, 'text' => $string];
        }
    }

}
