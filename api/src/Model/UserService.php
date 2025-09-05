<?php
namespace DieEwigen\Api\Model;

/**
 * Service wich provides and manages the data of the user.
 */
class UserService
{
    const IS_API_USER_SQL = "SELECT user_id FROM de_user_data where user_id = ? and npc = 2";
    const GET_COORDS_SQL = "SELECT sector, `system` FROM de_user_data where user_id = ?";

    /**
     * Check if the user is a API User.
     * @param string $userId the ums user id;
     * @return bool true if the user is a API user.
     */
    public function isAPIUser(int $userId) :bool {
        $stmt = mysqli_prepare($GLOBALS['dbi'],self::IS_API_USER_SQL);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows() == 1;
    }

    /**
     * Returns the coordinates of the any user
     * @param int $userId the id of any existing user
     * @return array an array with 0=sector, 1=system
     */
    public function getCoordinates(int $userId) :array {
        $stmt = mysqli_prepare($GLOBALS['dbi'],self::GET_COORDS_SQL);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_row();
        return [$row[0], $row[1]];
    }

}
