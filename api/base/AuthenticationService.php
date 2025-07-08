<?php

require PROJECT_ROOT_PATH.'api/user/service/UserService.php';

/**
 * Service to handle the authentication and authorization.
 */
class AuthenticationService
{
    /**
     * Check if user has access to API.
     * @param string $userId the userId (session ums_user_id)
     * @return bool true if user allowed to use the API
     */
    public function isAllowedForAPIUsage(string $userId) :bool {
        $userService = new UserService();
        return $userService->isAPIUser($userId);
    }
}