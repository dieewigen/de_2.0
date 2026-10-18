<?php
namespace DieEwigen\DE2\View;

//Strichsymbole der Oberfläche als SVG-Sprite, einmal je Seite ausgeben: Desktop-Ansicht (dm.php) und mobile Kommandokonsole
//(menu.php). Verwendung: <svg class="app-ico"><use href="#i-karte"/></svg>; Linien in der Textfarbe, Raster 24 x 24.
class Symbole
{
    public static function sprite(): string
    {
        return <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
	<symbol id="i-uebersicht" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></symbol>
	<symbol id="i-karte" viewBox="0 0 24 24"><path d="M3 6.5 9 4l6 2.5L21 4v13.5L15 20l-6-2.5L3 20z"/><path d="M9 4v13.5M15 6.5V20"/></symbol>
	<symbol id="i-hyperfunk" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1.8"/><path d="M8.5 15.5a5 5 0 0 1 0-7M15.5 8.5a5 5 0 0 1 0 7M5.6 18.4a9 9 0 0 1 0-12.8M18.4 5.6a9 9 0 0 1 0 12.8"/></symbol>
	<symbol id="i-nachrichten" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></symbol>
	<symbol id="i-technologien" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1.6"/><ellipse cx="12" cy="12" rx="9" ry="3.6"/><ellipse cx="12" cy="12" rx="9" ry="3.6" transform="rotate(60 12 12)"/><ellipse cx="12" cy="12" rx="9" ry="3.6" transform="rotate(120 12 12)"/></symbol>
	<symbol id="i-spezialisierung" viewBox="0 0 24 24"><circle cx="6" cy="5" r="2"/><circle cx="18" cy="5" r="2"/><circle cx="12" cy="19" r="2"/><path d="M6 7v2a4 4 0 0 0 4 4h4a4 4 0 0 0 4-4V7M12 13v4"/></symbol>
	<symbol id="i-ressourcen" viewBox="0 0 24 24"><ellipse cx="12" cy="6" rx="7" ry="2.6"/><path d="M5 6v4c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6V6M5 10v4c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6v-4M5 14v4c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6v-4"/></symbol>
	<symbol id="i-artefakte" viewBox="0 0 24 24"><path d="M7 3h10l4 6-9 12L3 9z"/><path d="M3 9h18M9.5 9 12 21l2.5-12M7 3l2.5 6M17 3l-2.5 6"/></symbol>
	<symbol id="i-auktion" viewBox="0 0 24 24"><path d="M12 3v18M6 21h12M12 6 5 8M12 6l7 2"/><path d="M2.5 14 5 8l2.5 6a2.5 2.5 0 0 1-5 0zM16.5 14 19 8l2.5 6a2.5 2.5 0 0 1-5 0z"/></symbol>
	<symbol id="i-missionen" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.4"/></symbol>
	<symbol id="i-vsysteme" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><ellipse cx="12" cy="12" rx="10.5" ry="3.2" transform="rotate(-22 12 12)"/></symbol>
	<symbol id="i-produktion" viewBox="0 0 24 24"><path d="M3 21V9l5 3.5V9l5 3.5V9l5 3.5V6h3v15z"/><path d="M7 17h2M11 17h2M15 17h2"/></symbol>
	<symbol id="i-flotten" viewBox="0 0 24 24"><path d="M12 2.5c3 2.6 4.2 6.3 4.2 10.5l2.8 3v3.5l-4-1.8H9l-4 1.8V16l2.8-3C7.8 8.8 9 5.1 12 2.5z"/><circle cx="12" cy="10" r="1.6"/><path d="m10 19.5 2 2 2-2"/></symbol>
	<symbol id="i-geheimdienst" viewBox="0 0 24 24"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/></symbol>
	<symbol id="i-sektor" viewBox="0 0 24 24"><path d="M12 2.5 20.2 7v10L12 21.5 3.8 17V7z"/><circle cx="12" cy="12" r="2"/></symbol>
	<symbol id="i-sektorstatus" viewBox="0 0 24 24"><path d="M12 2.5 20 5.5v6c0 4.8-3.4 8.6-8 10.5-4.6-1.9-8-5.7-8-10.5v-6z"/><path d="m8.8 12 2.2 2.2 4.4-4.4"/></symbol>
	<symbol id="i-allianz" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.4"/><circle cx="17" cy="9" r="2.6"/><path d="M2.5 19.5c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6M15.5 14.2c3.2 0 6 1.8 6 5.3"/></symbol>
	<symbol id="i-statistik" viewBox="0 0 24 24"><path d="M4 20v-7M10 20V5M16 20v-10M22 20H2"/></symbol>
	<symbol id="i-rangliste" viewBox="0 0 24 24"><path d="M8 4h8v5a4 4 0 0 1-8 0zM8 6H5a3 3 0 0 0 3 3.5M16 6h3a3 3 0 0 1-3 3.5M12 13v4M9 20h6M10 17h4"/></symbol>
	<symbol id="i-optionen" viewBox="0 0 24 24"><path d="M4 7h9M19 7h1M4 17h3M13 17h7"/><circle cx="16" cy="7" r="2.2"/><circle cx="10" cy="17" r="2.2"/></symbol>
	<symbol id="i-logout" viewBox="0 0 24 24"><path d="M14 4h5a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-5M10 8l-4 4 4 4M6 12h11"/></symbol>
	<symbol id="i-chat" viewBox="0 0 24 24"><path d="M4 5h16v10H9l-5 4z"/></symbol>
	<symbol id="i-einklappen" viewBox="0 0 24 24"><path d="m14 6-6 6 6 6"/></symbol>
	<symbol id="i-schliessen" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></symbol>
	<symbol id="i-aktualisieren" viewBox="0 0 24 24"><path d="M20 12a8 8 0 1 1-2.3-5.7M20 4v5h-5"/></symbol>
	<symbol id="i-heimat" viewBox="0 0 24 24"><path d="M3 11.5 12 4l9 7.5V20H3z"/><path d="M10 20v-6h4v6"/></symbol>
	<symbol id="i-countdown" viewBox="0 0 24 24"><circle cx="12" cy="13.5" r="7.5"/><path d="M12 9.5v4l2.5 2M9.5 2.5h5M12 2.5V6M18.5 6.5 20 5"/></symbol>
</svg>
SVG;
    }
}
