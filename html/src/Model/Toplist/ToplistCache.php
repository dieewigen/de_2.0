<?php
namespace DieEwigen\DE2\Model\Toplist;

/**
 * Ablage der Ranglisten: der Wirtschaftstick (tickler/wt_create_toplist.php) schreibt je Liste
 * die Zeilen als JSON nach cache/toplist/<liste>.json, toplist.php liest sie und stellt sie dar.
 *
 * Verwendung:
 *   $cache = new ToplistCache($directory.'cache/toplist');   // im Tick, $directory aus wt.php
 *   $cache->write('top1a', array('zeilen' => $zeilen));
 *
 *   $liste = (new ToplistCache())->read('top1a');           // in toplist.php, null bis zum ersten WT
 */
class ToplistCache
{
    private string $directory;

    /**
     * @param string|null $directory Verzeichnis der Listen, ohne Angabe cache/toplist im Spielverzeichnis
     */
    public function __construct(?string $directory = null)
    {
        $this->directory = rtrim($directory ?? dirname(__DIR__, 3).'/cache/toplist', '/\\').'/';
    }

    /**
     * Liste schreiben. Erst in eine Zwischendatei, die dann ersetzt: toplist.php liest so nie eine halbe Liste.
     * Fehler (z. B. fehlendes Verzeichnis) nur als Warnung, der Tick läuft weiter.
     */
    public function write(string $list, array $data): void
    {
        $file = $this->path($list);
        $tmp = $file.'.'.getmypid().'.neu';
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($json !== false && file_put_contents($tmp, $json) !== false) {
            rename($tmp, $file);
        }
    }

    /**
     * Liste lesen; null, wenn es sie (noch) nicht gibt.
     */
    public function read(string $list): ?array
    {
        $file = $this->path($list);
        if (!is_file($file)) {
            return null;
        }
        $data = json_decode((string)file_get_contents($file), true);
        return is_array($data) ? $data : null;
    }

    private function path(string $list): string
    {
        //nur feste Listennamen wie top1a, nie Pfadteile
        if (!preg_match('/^[a-z0-9]+$/', $list)) {
            throw new \InvalidArgumentException('Ungültiger Listenname: '.$list);
        }
        return $this->directory.$list.'.json';
    }
}
