<?php
namespace DieEwigen\DE2\Model\Exile;

/**
 * Mails von Fluxurion an Spieler im Exil (Sektor 1).
 *
 * Layout wie die HTML-Mails der Accountverwaltung (cron/lscron.php): schwarzer Hintergrund,
 * Tahoma, Links in #f8ae56, Hintergrundbilder von login.die-ewigen.com.
 *
 * Typen: vorab (Dienstmitteilung vor dem Parken), bericht1, bericht2, bericht3, neue_runde.
 */
class ExileMail
{
    private const LINK = '#f8ae56';
    private const FONT = 'Tahoma, Verdana, Arial, Helvetica, sans-serif';

    private array $lang;
    private string $serverName;
    private string $serverTag;
    private string $serverUrl;
    private string $loginUrl;

    public function __construct(array $lang, string $serverName, string $serverTag, string $serverUrl, string $loginUrl)
    {
        $this->lang = $lang;
        $this->serverName = $serverName;
        $this->serverTag = $serverTag;
        $this->serverUrl = rtrim($serverUrl, '/');
        $this->loginUrl = $loginUrl;
    }

    /**
     * Mail erzeugen.
     *
     * @param array $data Werte aus ExileService::buildMailData(), zusätzlich 'optout_url' für Lageberichte.
     *                    Für 'vorab' genügen 'name', 'days', 'col' und 'sector' (aktueller Sektor).
     * @return array{subject:string, html:string, headers:array<string,string>}
     */
    public function render(string $type, array $data): array
    {
        $l = $this->lang;
        $vars = [
            '{NAME}' => $this->esc($data['name'] ?? ''),
            '{DAYS}' => (int)($data['days'] ?? 0),
            '{COL}' => ExileService::formatNumber((int)($data['col'] ?? 0)),
            '{TAG}' => $this->esc($this->serverTag),
            '{SERVER}' => $this->esc($this->serverName),
        ];

        $paragraphs = [];
        $headers = [];

        //wer schon in Sektor 1 war, wird nicht dorthin verlegt, sein System ruht dort nur
        $sektor1 = $type === 'vorab' ? (int)($data['sector'] ?? 0) === 1 : (int)($data['from_sector'] ?? 0) === 1;

        if ($type === 'vorab') {
            $subject = $sektor1 ? $l['betreff_vorab_sektor1'] : $l['betreff_vorab'];
            $title = $sektor1 ? $l['titel_vorab_sektor1'] : $l['titel_vorab'];
            $paragraphs[] = $sektor1 ? $l['intro_vorab_sektor1'] : $l['intro_vorab'];
            if ((int)($data['col'] ?? 0) > 25) {
                $paragraphs[] = $l['intro_vorab_kollektoren'];
            }
            $paragraphs[] = $l['outro_vorab'];
            //Dienstmitteilung ohne Abmeldelink, das Token entsteht erst beim Parken
            $footer = $this->fill($l['fuss_vorab'], $vars).'<br>'.$l['fuss_vorab_optionen'];
        } else {
            $subject = $type === 'neue_runde' ? $l['betreff_neue_runde'] : $l['betreff_bericht'];
            $title = $l['titel_'.$type];
            $paragraphs[] = ($type === 'bericht1' && $sektor1) ? $l['intro_bericht1_sektor1'] : $l['intro_'.$type];
            foreach ($this->blocks($type, $data) as $block) {
                $paragraphs[] = $block;
            }
            $paragraphs[] = $l['outro_'.$type];

            $footer = $this->fill($l['fuss_bericht'], $vars);
            if (!empty($data['optout_url'])) {
                $footer .= '<br><a href="'.$this->esc($data['optout_url']).'" style="color:'.self::LINK.';">'.$l['fuss_abmelden'].'</a>';
                $headers['List-Unsubscribe'] = '<'.$data['optout_url'].'>';
                $headers['List-Unsubscribe-Post'] = 'List-Unsubscribe=One-Click';
            }
        }

        $vars['{DATE}'] = $this->esc($data['universe']['round_date'] ?? '');
        $body = '';
        foreach ($paragraphs as $p) {
            $body .= '<p style="margin:0 0 14px 0;">'.$this->fill($p, $vars).'</p>';
        }

        return [
            'subject' => strip_tags($this->fill($subject, ['{TAG}' => $this->serverTag])),
            'html' => $this->layout($this->fill($title, $vars), $this->fill($l['anrede'], $vars), $body, $footer),
            'headers' => $headers,
        ];
    }

    /**
     * Mail verschicken.
     */
    public function send(string $to, array $mail): bool
    {
        return (bool)\mail_smtp($to, $mail['subject'], $mail['html'], '', $mail['headers']);
    }

    /**
     * Inhaltsblöcke eines Lageberichts, jeweils nur mit aussagekräftigen Daten.
     */
    private function blocks(string $type, array $data): array
    {
        $l = $this->lang;
        $u = $data['universe'] ?? [];
        $n = fn (int $v): string => ExileService::formatNumber($v);
        $blocks = [];

        $lage = $l['block_lage'];
        $verlust = ($data['col_lost'] ?? 0) > 0
            ? strtr($l['block_verlust'], ['{LOST}' => $n($data['col_lost']), '{COL}' => $n($data['col'])])
            : '';
        $sektor = '';
        if (($data['from_sector'] ?? 0) > 1) {
            $sektor = strtr($data['sector_active'] > 0 ? $l['block_sektor'] : $l['block_sektor_leer'],
                ['{SECTOR}' => (int)$data['from_sector'], '{ACTIVE}' => $n($data['sector_active'])]);
        }
        $allianz = ($data['allytag'] ?? '') !== '' ? strtr($l['block_allianz'], ['{ALLY}' => $this->esc($data['allytag'])]) : '';
        $runde = ($u['round_date'] ?? '') !== ''
            ? strtr($l['block_runde'], ['{DATE}' => $this->esc($u['round_date']), '{WT}' => $n($u['wt']), '{ACTIVE}' => $n($u['active'])])
            : '';
        $aktive = strtr($l['block_aktive'], ['{WT}' => $n($u['wt'] ?? 0), '{ACTIVE}' => $n($u['active'] ?? 0)]);
        $eh = match ($u['eh'] ?? '') {
            'bald' => strtr($l['block_eh_bald'], ['{EHDATE}' => $this->esc($u['eh_date'])]),
            'laeuft' => $l['block_eh_laeuft'],
            default => '',
        };
        $reserve = (($data['reserve_m'] ?? 0) > 0 || ($data['reserve_d'] ?? 0) > 0)
            ? strtr($l['block_reserve'], ['{M}' => $n($data['reserve_m']), '{D}' => $n($data['reserve_d'])])
            : '';
        $schritt = !empty($data['movable']) ? $l['block_schritt_umzug'] : $l['block_schritt_sektor1'];

        //jeder Bericht setzt einen eigenen Schwerpunkt, damit sich die Mails nicht wiederholen
        $order = match ($type) {
            'bericht1' => [$lage, $verlust, trim($sektor.' '.$allianz), $reserve, $schritt],
            'bericht2' => [trim($runde.' '.$eh), trim($sektor.' '.$allianz), $verlust, $reserve, $schritt],
            'bericht3' => [$lage, trim($runde.' '.$eh), $reserve, $schritt],
            'neue_runde' => [trim($aktive.' '.$eh), $reserve, $schritt],
            default => [],
        };
        foreach ($order as $block) {
            if ($block !== '') {
                $blocks[] = $block;
            }
        }

        return $blocks;
    }

    private function layout(string $title, string $greeting, string $body, string $footer): string
    {
        $font = 'font-family:'.self::FONT.';';
        $image = $this->serverUrl !== ''
            ? '<img src="'.$this->esc($this->serverUrl).'/gp/g/berater5.png" width="96" height="192" alt="Fluxurion" style="display:block;border:0;">'
            : '';

        return '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
            .'<style>body{'.$font.'font-size:16px;color:#FFFFFF;background-color:#000000;} a{color:'.self::LINK.';}</style></head>'
            .'<body leftmargin="0" topmargin="0" marginheight="0" marginwidth="0" style="margin:0;padding:0;background-color:#000000;">'
            .'<table cellspacing="0" cellpadding="0" width="100%" border="0" bgcolor="#000000">'
            .'<tr><td width="100%" align="center" style="background-image:url(https://login.die-ewigen.com/img/bg.jpg);padding:20px 0;">'
            .'<table cellspacing="0" cellpadding="0" width="600" border="0" style="max-width:600px;background-image:url(https://login.die-ewigen.com/img/bgtr1.png);padding:10px;">'
            .'<tr>'
            .'<td width="110" valign="top" style="padding:10px 10px 10px 0;">'.$image.'</td>'
            .'<td align="left" valign="top" style="'.$font.'font-size:16px;line-height:1.5;color:#FFFFFF;padding:10px 0;">'
            .'<div style="color:'.self::LINK.';font-size:13px;">'.$this->esc($this->lang['absender']).'</div>'
            .'<h1 style="'.$font.'font-size:22px;font-weight:bold;color:#FFFFFF;margin:4px 0 18px 0;">'.$title.'</h1>'
            .'<p style="margin:0 0 14px 0;">'.$greeting.'</p>'
            .$body
            .'<p style="margin:0 0 22px 0;">'.$this->lang['gruss'].'</p>'
            .'<p style="margin:0 0 26px 0;"><a href="'.$this->esc($this->loginUrl).'" style="display:inline-block;padding:10px 22px;border:1px solid '.self::LINK.';color:'.self::LINK.';text-decoration:none;font-weight:bold;">'.$this->esc($this->lang['button']).'</a></p>'
            .'<div style="font-size:12px;line-height:1.4;color:#999999;">'.$footer.'</div>'
            .'</td></tr></table>'
            .'</td></tr></table></body></html>';
    }

    private function fill(string $text, array $vars): string
    {
        return strtr($text, array_map('strval', $vars));
    }

    private function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
