<?php
namespace DieEwigen\DE2\View\Hyperfunk;

/**
 * Formular zum Verfassen einer Hyperfunknachricht (hyperfunk.php, details.php).
 *
 * Abgeschickt wird immer an hyperfunk.php. Die Feldnamen erwartet dort die Verarbeitung:
 * zielsek, zielsys, betreff, nachricht, freund1..n und der Absendeknopf (antbut, sekmsg, allimsg, freundemsg).
 *
 * Verwendung:
 *   $form = new ComposeForm('antbut', 'Hyperfunknachricht absenden');
 *   echo $form->withCoordinates($se, $sy)->render();
 */
class ComposeForm
{
    //Formatierung: [vor, nach, Beschriftung, Tooltip]; ohne "nach" wird nur die Marke eingesetzt
    private const TAGS = array(
        array('[b]', '[/b]', '<b>B</b>', 'fett'),
        array('[i]', '[/i]', '<i>I</i>', 'kursiv'),
        array('[u]', '[/u]', '<u>U</u>', 'unterstrichen'),
        array('[CROT]', '', '<span class="hf-farbe" style="background: #F10505;"></span>', 'Rot (ab hier)'),
        array('[CGELB]', '', '<span class="hf-farbe" style="background: #FDFB59;"></span>', 'Gelb (ab hier)'),
        array('[CGRUEN]', '', '<span class="hf-farbe" style="background: #28FF50;"></span>', 'Gr&uuml;n (ab hier)'),
        array('[CW]', '', '<span class="hf-farbe" style="background: #FFFFFF;"></span>', 'Wei&szlig; (ab hier)'),
        array('[color=#]', '[/color]', 'Farbe', 'Farbe als Hexwert nach dem #, z. B. [color=#FF8800]'),
        array('[size=]', '[/size]', 'Gr&ouml;&szlig;e', 'Schriftgr&ouml;&szlig;e 1 bis 7, z. B. [size=4]'),
        array('[center]', '[/center]', 'Mitte', 'zentriert'),
        array('[url]', '[/url]', 'Link', 'Link, beginnt mit http:// oder https://'),
        array('[email]', '[/email]', '@', 'E-Mail-Adresse'),
    );

    //das Skript wird je Seitenaufruf nur einmal gebraucht
    private static bool $scriptRendered = false;

    private string $buttonName;
    private string $buttonText;
    private bool $coordinates = false;
    private string $sector = '';
    private string $system = '';
    private ?\mysqli_result $friends = null;
    private string $hint = '';
    private string $subject = '';
    private string $message = '';

    public function __construct(string $buttonName, string $buttonText)
    {
        $this->buttonName = $buttonName;
        $this->buttonText = $buttonText;
    }

    /**
     * Zielkoordinaten als Pflichtfelder, optional vorbelegt.
     */
    public function withCoordinates(string $sector = '', string $system = ''): self
    {
        $this->coordinates = true;
        $this->sector = $sector;
        $this->system = $system;
        return $this;
    }

    /**
     * Freunde als Empfänger zum Ankreuzen (Ergebnis mit sector, system, name).
     */
    public function withFriends(\mysqli_result $friends): self
    {
        $this->friends = $friends;
        return $this;
    }

    /**
     * Hinweis über dem Betreff (HTML).
     */
    public function withHint(string $html): self
    {
        $this->hint = $html;
        return $this;
    }

    /**
     * Betreff, schon fürs HTML-Attribut aufbereitet.
     */
    public function withSubject(string $html): self
    {
        $this->subject = $html;
        return $this;
    }

    /**
     * Text für das Eingabefeld (z. B. Zitat beim Antworten), wird unverändert eingesetzt.
     */
    public function withMessage(string $text): self
    {
        $this->message = $text;
        return $this;
    }

    public function render(): string
    {
        $html = '<form action="hyperfunk.php" method="post" class="mod hf hf-formular" onsubmit="return hfPruefen(this)">';
        $html .= '<div class="hf-felder">';
        if ($this->coordinates) {
            $html .= '<div class="ally-feld"><span class="mod-typ">Zielkoordinaten</span><span class="hf-koords">';
            $html .= '<input name="zielsek" id="zielsek" class="mod-eingabe" inputmode="numeric" autocomplete="off" placeholder="Sek." required value="' . $this->sector . '">';
            $html .= '<i>:</i>';
            $html .= '<input name="zielsys" id="zielsys" class="mod-eingabe" inputmode="numeric" autocomplete="off" placeholder="Sys." required value="' . $this->system . '">';
            $html .= '</span></div>';
        } elseif ($this->friends !== null) {
            $html .= '<div class="ally-feld ally-feld-breit"><span class="mod-typ">Empf&auml;nger</span><div class="hf-freunde">';
            $counter = 1;
            while ($row = mysqli_fetch_array($this->friends)) {
                $html .= '<label class="hf-haken"><input type="checkbox" name="freund' . $counter . '" value="' . $row['sector'] . ':' . $row['system'] . '">' . $row['sector'] . ':' . $row['system'] . ' (' . $row['name'] . ')</label>';
                $counter++;
            }
            $html .= '</div></div>';
        }
        if ($this->hint !== '') {
            $html .= '<div class="ally-hinweis ally-feld-breit">' . $this->hint . '</div>';
        }
        $html .= '<label class="ally-feld hf-betreff"><span class="mod-typ">Betreff</span><input name="betreff" class="mod-eingabe" autocomplete="off" value="' . $this->subject . '"></label>';
        $html .= '</div>';

        $html .= '<div class="hf-leiste">';
        foreach (self::TAGS as $tag) {
            $html .= '<button type="button" class="hf-tag" data-vor="' . $tag[0] . '" data-nach="' . $tag[1] . '" title="' . $tag[3] . '" onclick="hfTag(this)">' . $tag[2] . '</button>';
        }
        $html .= '<a href="hfnlegende.php" target="_blank" class="hf-tag" title="Alle Formatierungen">?</a>';
        $html .= '</div>';

        $html .= '<textarea name="nachricht" id="nachricht" rows="12" class="mod-eingabe hf-text" oninput="hfZaehlen(this)">' . $this->message . '</textarea>';
        $html .= '<div class="hf-fuss"><span class="hf-zaehler" aria-live="polite"></span>';
        $html .= '<button type="submit" name="' . $this->buttonName . '" value="' . $this->buttonText . '" class="mod-btn">' . $this->buttonText . '</button></div>';
        $html .= '</form>';

        if (!self::$scriptRendered) {
            self::$scriptRendered = true;
            $html .= self::script();
        }
        return $html;
    }

    //Formatierungsknöpfe (umschließen den markierten Text), Zeichenzähler, Längenprüfung vor dem Absenden
    private static function script(): string
    {
        return '<script>
function hfTag(k){
    var t = k.form.elements["nachricht"], vor = k.getAttribute("data-vor"), nach = k.getAttribute("data-nach");
    var a = t.selectionStart, e = t.selectionEnd, mitte = t.value.substring(a, e);
    if (nach !== "" && mitte === "") { mitte = " "; }
    t.value = t.value.substring(0, a) + vor + mitte + nach + t.value.substring(e);
    t.focus();
    t.selectionStart = a + vor.length;
    t.selectionEnd = a + vor.length + mitte.length;
    hfZaehlen(t);
}
function hfZaehlen(t){
    var z = t.form.querySelector(".hf-zaehler"), n = t.value.length;
    z.textContent = n.toLocaleString("de-DE") + " / 10.000 Zeichen";
    z.classList.toggle("hf-zuviel", n > 10000);
}
function hfPruefen(f){
    var t = f.elements["nachricht"];
    hfZaehlen(t);
    if (t.value.length > 10000) {
        f.querySelector(".hf-zaehler").textContent = "Zu lang: " + t.value.length.toLocaleString("de-DE") + " / 10.000 Zeichen";
        t.focus();
        return false;
    }
    return true;
}
document.querySelectorAll(".hf-text").forEach(hfZaehlen);
</script>';
    }
}
