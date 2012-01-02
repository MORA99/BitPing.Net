<?
class Users {
    public static function isUsernameAvaliable($user) {
        $db = database::getInstance();
        $stmt = $db->prepare("SELECT uid FROM users WHERE username=? LIMIT 1");
        $stmt->bind_param("s", $user);
        $db->select($stmt);

        $stmt->bind_result($id);
        if ($stmt->fetch()) {
            $stmt->close();
            return false;
        }

        return true;
    }

    public static function isEmailInUse($email) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT uid FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $db->select($stmt);
        if ($stmt->fetch())
            return true;
        return false;
    }

    public static function getActiveUser() {
        return self::getUserByUsername($_SESSION["AUTH_USER_NAME"]);
    }

    public static function getUserByUsername($username) {
        $db = database::getInstance();
        $stmt = $db->prepare("SELECT uid FROM users WHERE username=? LIMIT 1");
        $stmt->bind_param("s", $username);
        $db->select($stmt);

        $stmt->bind_result($id);
        if ($stmt->fetch()) {
            $stmt->close();
            return new User($id);
        }

        return NULL;
    }

    public static function getUserByEmail($email) {
        $db = database::getInstance();
        $stmt = $db->prepare("SELECT uid FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $db->select($stmt);

        $stmt->bind_result($id);
        if ($stmt->fetch()) {
            $stmt->close();
            return new User($id);
        }

        return NULL;
    }
}
?>
