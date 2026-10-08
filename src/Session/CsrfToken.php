<?php
namespace DieEwigen\DE2\Session;

/**
 * Schutz gegen untergeschobene Aufrufe (CSRF) für Links und Formulare, die etwas ändern.
 *
 * Je Sitzung gibt es ein zufälliges Token. Eine fremde Seite kennt es nicht und kann den Aufruf deshalb
 * nicht im Namen des Spielers auslösen (z. B. über einen präparierten Link im Chat).
 *
 * Verwendung:
 *   <a href="ally_kick.php?userid=5&amp;'.CsrfToken::query().'">
 *   if (!CsrfToken::check($_GET['token'] ?? '')) { abbrechen }
 */
class CsrfToken
{
    private const SESSION_KEY = 'csrf_token';

    public static function get(): string
    {
        if (empty($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(16));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Parameter für einen Link, z. B. "token=3f9c…".
     */
    public static function query(): string
    {
        return 'token='.self::get();
    }

    /**
     * @param mixed $token Wert aus der Anfrage
     */
    public static function check($token): bool
    {
        return is_string($token)
            && !empty($_SESSION[self::SESSION_KEY])
            && is_string($_SESSION[self::SESSION_KEY])
            && hash_equals($_SESSION[self::SESSION_KEY], $token);
    }
}
