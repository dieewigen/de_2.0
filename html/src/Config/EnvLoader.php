<?php
namespace DieEwigen\DE2\Config;

//Liest Umgebungsvariablen und überschreibt damit die Defaultwerte in
//html/inc/env.inc.php und html/inc/sv.inc.php. Auf den Docker-Servern kommen die
//Variablen aus docker/sde/.env bzw. docker/xde/.env (env_file); der Name der
//Umgebungsvariable ist der PHP-Schlüssel in Großbuchstaben (sv_winscore => SV_WINSCORE).
//Fehlt die Variable oder ist sie leer, gilt der Defaultwert aus der PHP-Datei.
class EnvLoader
{
    public static function str($name, $default = '')
    {
        return self::value($name, $default, 'str');
    }

    public static function int($name, $default)
    {
        return self::value($name, $default, 'int');
    }

    public static function float($name, $default)
    {
        return self::value($name, $default, 'float');
    }

    public static function bool($name, $default)
    {
        return self::value($name, $default, 'bool');
    }

    //Komma-Liste, z. B. "3,14,45" (leere Einträge werden ignoriert)
    public static function listInt($name, $default)
    {
        return self::value($name, $default, 'listInt');
    }

    public static function listFloat($name, $default)
    {
        return self::value($name, $default, 'listFloat');
    }

    //JSON (z. B. Hekate-Auftragsvorlagen, S666-Beute)
    public static function json($name, $default)
    {
        return self::value($name, $default, 'json');
    }

    /**
     * Optionale Werte (botcheck, Siegel, Hekate/Thanatos, Feldzug, S666):
     * setzt $GLOBALS[$phpKey] nur, wenn die Umgebungsvariable gesetzt ist;
     * sonst bleibt der Key unset und die Standardwerte im Spielcode gelten (??-Fallback).
     *
     * @param string $type str|int|float|bool|listInt|listFloat|json
     */
    public static function optional($phpKey, $name, $type)
    {
        if (self::roh($name) === null) {
            return;
        }
        $GLOBALS[$phpKey] = self::value($name, null, $type);
    }

    /**
     * Roher Wert der Umgebungsvariable oder null, wenn sie fehlt oder leer ist.
     */
    private static function roh($name)
    {
        $v = getenv($name);
        if ($v === false || trim($v) === '') {
            return null;
        }
        return $v;
    }

    private static function value($name, $default, $type)
    {
        $v = self::roh($name);
        if ($v === null) {
            return $default;
        }

        switch ($type) {
            case 'str':
                return $v;
            case 'int':
                return (int)$v;
            case 'float':
                return (float)str_replace(',', '.', $v);
            case 'bool':
                return in_array(strtolower(trim($v)), ['1', 'true', 'yes', 'on'], true);
            case 'listInt':
                return array_map('intval', self::split($v));
            case 'listFloat':
                return array_map(function ($t) {
                    return (float)str_replace(',', '.', $t);
                }, self::split($v));
            case 'json':
                $d = json_decode($v, true);
                if (!is_array($d)) {
                    throw new \RuntimeException('Ungültiges JSON in Umgebungsvariable ' . $name);
                }
                return $d;
        }

        throw new \RuntimeException('Unbekannter Typ "' . $type . '" für Umgebungsvariable ' . $name);
    }

    private static function split($v)
    {
        return array_values(array_filter(array_map('trim', explode(',', $v)), function ($t) {
            return $t !== '';
        }));
    }
}
