<?php

namespace DieEwigen\Api\Model;

use DieEwigen\Api\Types\AlliancePartnerRequest;

class GetAlliancePartnerRequests
{
    const string GET_ALLY_ID_BY_LEADER =
        "SELECT id FROM de_allys WHERE leaderid = ?";

    const string GET_PARTNER_REQUESTS =
        "SELECT ally_id_antragsteller, ally_id_partner, antrag
         FROM de_ally_buendniss_antrag
         WHERE ally_id_partner = ?";

    /**
     * Returns all pending partnership requests for the alliance led by the given user.
     *
     * @param int $userId The user_id of the alliance leader (receiving side).
     * @return AlliancePartnerRequest[] Empty array if user is not a leader or no requests exist.
     */
    public function getRequests(int $userId): array
    {
        $allyRow = mysqli_execute_query(
            $GLOBALS['dbi'],
            $this::GET_ALLY_ID_BY_LEADER,
            [$userId]
        );

        $allyData = mysqli_fetch_assoc($allyRow);
        if ($allyData === null) {
            return [];
        }

        $allyId = (int)$allyData['id'];

        $requestRows = mysqli_execute_query(
            $GLOBALS['dbi'],
            $this::GET_PARTNER_REQUESTS,
            [$allyId]
        );

        $result = [];
        foreach (mysqli_fetch_all($requestRows, MYSQLI_ASSOC) as $row) {
            $result[] = new AlliancePartnerRequest(
                (int)$row['ally_id_antragsteller'],
                (int)$row['ally_id_partner'],
                (string)($row['antrag'] ?? ''),
            );
        }

        return $result;
    }
}
