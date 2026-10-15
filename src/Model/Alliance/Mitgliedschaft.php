<?php
namespace DieEwigen\DE2\Model\Alliance;

//Rolle eines Spielers in seiner Allianz für die Allianzseiten: Leader, Co-Leader, Mitglied oder keine. Ersetzt den
//Rollenteil von ally/ally.menu.inc.php; die Prüfreihenfolge ist wie früher Leader vor Co-Leader vor Mitglied.
//Ohne Allianz kennt die Mitgliedschaft die offene Bewerbung (Allianz::offeneBewerbung).
class Mitgliedschaft
{
    public const LEADER = 'leader';
    public const COLEADER = 'coleader';
    public const MITGLIED = 'mitglied';
    public const KEINE = 'keine';

    private string $rolle;
    private ?array $bewerbung;

    private function __construct(string $rolle, ?array $bewerbung)
    {
        $this->rolle = $rolle;
        $this->bewerbung = $bewerbung;
    }

    public static function fuer(\mysqli $db, int $userId): self
    {
        $r = mysqli_execute_query($db, "SELECT id FROM de_allys WHERE leaderid=?", [$userId]);
        if (mysqli_num_rows($r) >= 1) {
            return new self(self::LEADER, null);
        }
        $r = mysqli_execute_query($db, "SELECT id FROM de_allys WHERE coleaderid1=? OR coleaderid2=? OR coleaderid3=?", [$userId, $userId, $userId]);
        if (mysqli_num_rows($r) >= 1) {
            return new self(self::COLEADER, null);
        }
        $row = mysqli_fetch_assoc(mysqli_execute_query($db, "SELECT count(*) AS cnt FROM de_user_data WHERE user_id=? AND status=1", [$userId]));
        if ($row['cnt'] > 0) {
            return new self(self::MITGLIED, null);
        }

        return new self(self::KEINE, Allianz::offeneBewerbung($db, $userId));
    }

    public function rolle(): string
    {
        return $this->rolle;
    }

    public function istLeader(): bool
    {
        return $this->rolle === self::LEADER;
    }

    public function istCoLeader(): bool
    {
        return $this->rolle === self::COLEADER;
    }

    public function istMitglied(): bool
    {
        return $this->rolle === self::MITGLIED;
    }

    public function hatAllianz(): bool
    {
        return $this->rolle !== self::KEINE;
    }

    //Seiten für die Führung (Leader ernennen, Co-Leader, Entlassen, Löschen) dürfen Leader und Co-Leader öffnen
    public function darfLeaderseite(): bool
    {
        return $this->istLeader() || $this->istCoLeader();
    }

    //offene Bewerbung ohne Allianz: ['ally_id' => …, 'allytag' => …], sonst null
    public function bewerbung(): ?array
    {
        return $this->bewerbung;
    }

    //hat der Spieler die Position (Spaltenname in de_allys) in der Allianz mit diesem Tag? (Allianzflotten)
    public static function hatPosition(\mysqli $db, $position, $allytag, $userId): bool
    {
        if (!in_array($position, array('leaderid', 'coleaderid1', 'coleaderid2', 'coleaderid3', 'fleetcommander1', 'fleetcommander2'), true)) {
            return false;
        }
        $row = mysqli_fetch_assoc(mysqli_execute_query($db, "SELECT $position AS wer FROM de_allys WHERE allytag = ?", [(string)$allytag]));

        return $row !== null && (int)$row['wer'] === (int)$userId;
    }
}
