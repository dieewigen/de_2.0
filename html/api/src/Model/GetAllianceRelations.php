<?php

namespace DieEwigen\Api\Model;

use DieEwigen\Api\Types\AlliancePartner;
use DieEwigen\Api\Types\AllianceWar;

class GetAllianceRelations
{
    const string GET_PARTNERSHIPS = "SELECT ally_id_1, ally_id_2 FROM de_ally_partner";
    const string GET_WARS = "SELECT ally_id_angreifer, ally_id_angegriffener, friedensangebot FROM de_ally_war";

    /**
     * Retrieve all alliance relations (partnerships and wars).
     *
     * @return array associative array with keys 'partnerships' and 'wars'.
     */
    public function getAllianceRelations(): array
    {
        $partnerships = [];
        $partnerQuery = mysqli_execute_query($GLOBALS['dbi'], $this::GET_PARTNERSHIPS);
        foreach (mysqli_fetch_all($partnerQuery, MYSQLI_ASSOC) as $row) {
            $partnerships[] = new AlliancePartner((int)$row['ally_id_1'], (int)$row['ally_id_2']);
        }

        $wars = [];
        $warQuery = mysqli_execute_query($GLOBALS['dbi'], $this::GET_WARS);
        foreach (mysqli_fetch_all($warQuery, MYSQLI_ASSOC) as $row) {
            $wars[] = new AllianceWar(
                (int)$row['ally_id_angreifer'],
                (int)$row['ally_id_angegriffener'],
                $row['friedensangebot'] != 0,
                $row['friedensangebot']
            );
        }

        return ['partnerships' => $partnerships, 'wars' => $wars];
    }
}
