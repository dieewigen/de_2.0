<?php
namespace DieEwigen\DE2\Model\Alliance;

//Allianzdaten und -aktionen der Allianzseiten: Historie, ID und Tag, Bewerbungen. Ersetzt ally/allyfunctions.inc.php und
//den Bewerbungsteil von ally/ally.menu.inc.php. Die Benachrichtigung beim Zurückziehen braucht notifyUser() aus
//lib/basefunctions.lib.php (allymain.php bindet es ein).
class Allianz
{
    //Eintrag in die Allianzhistorie, optional auch in den Allianzchat; nur einfache Formatierung (safe_basic_html)
    public static function historie(\mysqli $db, $allytag, $entry, bool $insertChat = false): void
    {
        $allytag = (string)$allytag;
        $entry = \safe_basic_html(html_entity_decode((string)$entry));
        $timestamp = time();
        $datum = date("d.m.Y - H:i", $timestamp);
        $allyId = self::idFuerTag($db, $allytag);
        mysqli_execute_query($db, "INSERT INTO de_ally_history SET allytag=?, allyid=?, entry=?, timestamp=?, displaydate=?", [$allytag, $allyId, $entry, $timestamp, $datum]);
        if ($insertChat) {
            \insert_chat_msg($allyId, 1, '', $allytag.'-'.$entry);
        }
    }

    //$allytag darf wie früher leer sein, dann gibt es keine Allianz
    public static function idFuerTag(\mysqli $db, $allytag): ?int
    {
        $row = mysqli_fetch_assoc(mysqli_execute_query($db, "SELECT id FROM de_allys WHERE allytag=?", [(string)$allytag]));

        return $row ? (int)$row['id'] : null;
    }

    public static function tagFuerId(\mysqli $db, $allyId): ?string
    {
        $row = mysqli_fetch_assoc(mysqli_execute_query($db, "SELECT allytag FROM de_allys WHERE id=?", [(int)$allyId]));

        return $row ? $row['allytag'] : null;
    }

    //offene Bewerbung eines Spielers ohne Allianz: ['ally_id', 'allytag']; gibt es die Allianz nicht mehr, wird die
    //Bewerbung wie bisher still bereinigt (Gebühr zurück, Antrag weg) und null geliefert
    public static function offeneBewerbung(\mysqli $db, int $userId): ?array
    {
        $antrag = mysqli_fetch_assoc(mysqli_execute_query($db, "SELECT ally_id FROM de_ally_antrag WHERE user_id=?", [$userId]));
        if (!$antrag) {
            return null;
        }
        $ally = mysqli_fetch_assoc(mysqli_execute_query($db, "SELECT allytag FROM de_allys WHERE id=?", [$antrag['ally_id']]));
        if (!$ally) {
            self::bewerbungLoeschen($db, $userId);

            return null;
        }

        return array('ally_id' => (int)$antrag['ally_id'], 'allytag' => $ally['allytag']);
    }

    //Bewerbung zurückziehen: Leader und Co-Leader erfahren es, die Gebühr kommt zurück.
    //Ergebnis: gutgeschriebene Tronic, null wenn keine Bewerbung vorlag
    public static function bewerbungZurueckziehen(\mysqli $db, int $userId, string $spielername): ?int
    {
        $antrag = mysqli_fetch_assoc(mysqli_execute_query($db, "SELECT ally_id FROM de_ally_antrag WHERE user_id=?", [$userId]));
        if (!$antrag) {
            return null;
        }
        $ally = mysqli_fetch_assoc(mysqli_execute_query($db, "SELECT leaderid, coleaderid1, coleaderid2, coleaderid3 FROM de_allys WHERE id=?", [$antrag['ally_id']]));
        if ($ally) {
            $text = 'Eine Bewerbung wurde zur&uuml;ckgezogen. Spielername: '.$spielername;
            \notifyUser($ally['leaderid'], $text, "6");
            foreach (array('coleaderid1', 'coleaderid2', 'coleaderid3') as $feld) {
                if ($ally[$feld] > 0) {
                    \notifyUser($ally[$feld], $text, "6");
                }
            }
        }

        return self::bewerbungLoeschen($db, $userId);
    }

    //Gebühr zurückbuchen, Antrag und Allianzzuordnung entfernen; Ergebnis: Tronic
    private static function bewerbungLoeschen(\mysqli $db, int $userId): int
    {
        $tronic = 0;
        $t = mysqli_fetch_assoc(mysqli_execute_query($db, "SELECT id, amount FROM de_transactions WHERE user_id=? AND type='C.A.R.S.' AND identifier='reg_fee' AND name='Tronic'", [$userId]));
        if ($t) {
            $tronic = (int)$t['amount'];
            if ($tronic > 0) {
                mysqli_execute_query($db, "UPDATE de_user_data SET restyp05=restyp05+? WHERE user_id=?", [$tronic, $userId]);
            }
            mysqli_execute_query($db, "DELETE FROM de_transactions WHERE id = ?", [$t['id']]);
        }
        mysqli_execute_query($db, "UPDATE de_user_data SET allytag='', status=0 WHERE user_id = ?", [$userId]);
        mysqli_execute_query($db, "DELETE FROM de_ally_antrag WHERE user_id = ?", [$userId]);

        return $tronic;
    }
}
