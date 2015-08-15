<?

namespace Helper;

class Console
{
    const NONE = 0;         // 0000
    const OUTPUT = 1;       // 0001
    const FILE = 2;         // 0010
    const MEMORY = 4;       // 0100
    const BACKTRACE = 8;    // 1000

    private static $started_at, $log = [], $behavior = self::OUTPUT | self::BACKTRACE;

    private function getFilePath()
    {
        return \Application::dirLogs() . '/console.log';
    }

    public static function start()
    {
        if (null === self::$started_at)
        {
            self::$started_at = microtime(true);
        }
    }

    public static function behave($behavior = self::OUTPUT)
    {
        self::$behavior = $behavior;
    }

    public static function log($data)
    {
        if (self::$behavior === self::NONE)
        {
            return;
        }
        self::start();
        $string = $at = $class = '';
        if (self::in(self::BACKTRACE))
        {
            $trace = debug_backtrace();
            if ($trace && isset($trace[0]['file']) && isset($trace[0]['line']))
            {
                $at = ' @' . \Application::basename($trace[0]['file']) . ':' . $trace[0]['line'];
            }
        }
        if ($class)
        {
            $string .= '[' . $class . '] ';
        }
        $string .= $data . $at;
        $time = microtime(true) - self::$started_at;

        if (self::in(self::OUTPUT))
        {
            printf("%1.03f %s\n", $time, $string);
        }
        else if (self::in(self::FILE))
        {
            file_put_contents(self::getFilePath(), date('d.m.y H:i:s ') . sprintf("%1.03f %s\n", $time, $string), FILE_APPEND);
        }
        else
        {
            self::$log[] = ['time' => $time, 'text' => $string];
        }
    }

    public static function flush()
    {
        foreach (self::$log as $log)
        {
            printf("%1.03f %s\n", $log['time'], $log['text']);
        }
    }

    private static function in($value)
    {
        return intval(self::$behavior & $value) > 0;
    }

}
