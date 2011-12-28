<?
class Database {
    private static $instance;
    private $dblink;

    public static function getInstance() {
        if (!isset(self::$instance))
            self::$instance = new Database();
        return self::$instance;
    }

    private function __construct() {
        $this->dblink = new mysqli('localhost','bpn','PASS','bpn');

        if (mysqli_connect_errno()) {
            die('Connect failed: '. mysqli_connect_error());
        }

        $this->dblinkAbe = new mysqli('localhost','bpn','PASS','abe');

        if (mysqli_connect_errno()) {
            die('Connect failed: '. mysqli_connect_error());
        }
    }

    public function startTransaction() {
        $this->dblink->query("START TRANSACTION");
    }

    public function commit() {
        $this->dblink->query("COMMIT");
    }

    public function rollback() {
        $this->dblink->query("ROOLBACK");
    }

    public function prepare($sql) {
        $result = $this->dblink->prepare($sql);
        if ($result === false) {
            echo "SQL error - $sql - ".$this->dblink->error;
            die();
        }
        return $result;
    }

    public function query($sql) {
        return $this->dblink->query($sql);
    }

    public function queryAbe($sql) {
        return $this->dblinkAbe->query($sql);
    }

    public function prepareAbe($sql) {
        return $this->dblinkAbe->prepare($sql);
    }

    public function insert($stmt) {
        if ($stmt->execute() === false)
            die('execute() failed: ' . htmlspecialchars($stmt->error));

        return $stmt->insert_id;
    }

    public function update($stmt) {
        if ($stmt->execute() === false)
            die('execute() failed: ' . htmlspecialchars($stmt->error));
        return $stmt->insert_id;
    }

    public function select($stmt) {
        if ($stmt->execute() === false)
            die('execute() failed: ' . htmlspecialchars($stmt->error));
        $stmt->store_result();
        return $result;
    }
}
?>
