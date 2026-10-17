# Kampf abspielen im Kampfbericht

**Stand 18. Oktober 2026, umgesetzt.** Der Kampftick zeichnet die Schüsse jedes Flottenkampfs auf, der Kampfbericht spielt sie auf Knopfdruck ab.

## Wie es gebaut ist

- `tickler/kt_systemkampf.php`: je Schuss `[Phase, Seite, Schütze, Ziel, Anteil]` als `ablauf` in jedem Bericht des Kampfes. Phase 1 EMP-Waffen (Anteil vom Angriffswert der getroffenen Klasse), Phase 2 konventionelle Waffen (Anteil von den Hitpoints), Reihenfolge wie gerechnet, Schüsse unter 0,1 % entfallen. Dazu `npc`, weil Angreifer NPC-Systeme nicht lähmen.
- `src/View/Kampfbericht.php`, `ablauf()`: Knopf „Kampf abspielen“ neben „Als Text kopieren“ und der versteckte Kasten mit den Klassen beider Seiten (Name, Anzahl, Balken). Ohne `ablauf` gibt es keinen Knopf: ältere Berichte, Server 2 bis zu seiner Folgerunde.
- `js/de_fn.js`: Abspiellogik (läuft von selbst, Pause, Schritt, Von vorn; bei `prefers-reduced-motion` kein Autostart). `gp/de-main.scss`: Block „Kampf abspielen“ hinter den Knöpfen des Berichts.
- Hilfe: Thema Kampf, Absatz „Kampf abspielen“.

## Patch Notes (Text für die Spieler)

**Update: Kampf abspielen im Kampfbericht**

Unter jedem neuen Kampfbericht eines Flottenkampfs (Nachrichten, Kampf & Spionage) gibt es den Knopf „Kampf abspielen“. Er zeigt Schritt für Schritt, wie der Kampf gelaufen ist: welche Klasse auf welche gefeuert hat und was dabei herauskam.

- Links stehen die Klassen der Angreifer, rechts die der Verteidiger, jede mit ihrer Anzahl und einem Balken.
- Phase 1, EMP-Waffen: Jede Klasse lähmt gegnerische Klassen in fester Reihenfolge, mit jedem weiteren Ziel schwächer. Beim Ziel füllt sich der gelähmte Anteil schraffiert. Gelähmte Einheiten feuern in Phase 2 nicht.
- Phase 2, konventionelle Waffen: Alle nicht gelähmten Einheiten feuern gleichzeitig. Beim Ziel füllt sich der zerstörte Anteil rot.
- Jeder Schritt hebt Schütze und Ziel hervor und nennt den Anteil, zum Beispiel „Guillotine (Angreifer) lähmt 21,4 % der Marauder (Verteidiger)“. Die Prozente gelten für die getroffene Klasse auf ihrer Seite, die genauen Stückzahlen stehen wie bisher in der Tabelle.
- Der Ablauf läuft von selbst. Mit „Pause“ hältst Du an, mit „Schritt“ gehst Du einzeln weiter, am Ende startet „Von vorn“ noch einmal. Ist auf Deinem Gerät „Bewegung reduzieren“ eingeschaltet, läuft nichts von allein, Du klickst Dich durch.
- Gegen die DX61a23 wirken die EMP-Waffen der Angreifer nicht, der Ablauf sagt das dazu.
- Den Ablauf gibt es für Kämpfe ab diesem Update, auch im Geheimdienst bei den Berichten anderer Spieler. Ältere Berichte haben den Knopf nicht.
- Der zweite Server bekommt das Update wie gewohnt zum nächsten Rundenstart.
