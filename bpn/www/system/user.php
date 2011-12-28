<?
class User {
    private $userid;

    private $username;
    private $password;	//hashed
    private $email;
    private $url;
    private $secret;

    public function __construct($userid=0) {
        $this->userid = $userid;
        $db = database::getInstance();

        if ($this->userid != 0) {
            $stmt = $db->prepare("SELECT `username`, `password`, `email`, `url`, `secret` FROM `users` WHERE `uid`=? LIMIT 1");
            $stmt->bind_param("i", $this->userid);
            $db->select($stmt);

            $stmt->bind_result($username, $password, $email, $url, $secret);
            if ($stmt->fetch()) {
                $this->username = $username;
                $this->password = $password;
                $this->email = $email;
                $this->url = $url;
                $this->secret = $secret;
            }
        }
    }

    public static function getUser($username, $input) {
        $db = database::getInstance();
        $stmt = $db->prepare("SELECT uid,password FROM users WHERE username=? LIMIT 1");
        $stmt->bind_param("s", $username);
        $db->select($stmt);

        $stmt->bind_result($id, $password);
        if ($stmt->fetch()) {
            if (self::ValidatePassword($input, $password)) {
                $stmt->close();
                return new User($id);
            }
        }

        return NULL;
    }

    public function __set($name, $value) {
        if ($name == "password") {
            $this->password = self::hash($value);
        }
        else
            $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }

    private static function hash($str) {
        $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); //get 256 random bits in hex
        $hash = hash("sha256", $salt . $str); //prepend the salt, then hash
        return $salt . $hash;
    }

    private static function ValidatePassword($password, $correctHash) {
        $salt = substr($correctHash, 0, 64);
        $validHash = substr($correctHash, 64, 64);
        $testHash = hash("sha256", $salt . $password);
        return $testHash === $validHash;
    }

    public function save() {
        $db = Database::getInstance();
        if ($this->userid == 0) {
            if ($this->url == null)
                $this->url = "";

            $secret = bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
            $stmt = $db->prepare("INSERT INTO `users` (`username`, `password`, `email`, `url`, `secret`) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $this->username, $this->password, $this->email, $this->url, $secret);
            $this->userid = $db->insert($stmt);
        } else {
            $stmt = $db->prepare("UPDATE `users` SET username=?, password=?, email=?, url=? WHERE uid=?");
            $stmt->bind_param('ssssi', $this->username, $this->password, $this->email, $this->url, $this->userid);
            $db->update($stmt);
        }
    }

    public function delete() {
        $db = Database::getInstance();
        $db->startTransaction();

        $stmt = $db->prepare("DELETE FROM order_notify WHERE order_id IN (SELECT order_id FROM orders WHERE uid=?)");
        $stmt->bind_param(i, $this->userid);
        $db->update($stmt);

        $stmt = $db->prepare("DELETE FROM order_address WHERE order_id IN (SELECT order_id FROM orders WHERE uid=?)");
        $stmt->bind_param(i, $this->userid);
        $db->update($stmt);

        $stmt = $db->prepare("DELETE FROM notifications_sent WHERE order_id IN (SELECT order_id FROM orders WHERE uid=?)");
        $stmt->bind_param(i, $this->userid);
        $db->update($stmt);

        $stmt = $db->prepare("DELETE FROM orders WHERE uid = ?");
        $stmt->bind_param(i, $this->userid);
        $db->update($stmt);

        $stmt = $db->prepare("DELETE FROM users WHERE uid = ?");
        $stmt->bind_param(i, $this->userid);
        $db->update($stmt);

        $db->commit();
    }
}
?>
