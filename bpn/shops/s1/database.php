<?
class Database {
    private static $instance;
    private $dblink;

    private function error($msg) {
        mail(SYS_ADMIN, "SQL error at BPN", "Msg : ".$msg, "FROM: robot@BitPing.Net");
    }

    public static function getInstance() {
        if (!isset(self::$instance))
            self::$instance = new Database();
        return self::$instance;
    }

    private function __construct() {
        $this->dblink = new mysqli(DB_HOST,DB_USER,DB_PASS,DB);

        if (mysqli_connect_errno()) {
            $this->error(mysqli_connect_error());
            die('Database connection failed ... try again later');
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
            $this->error("SQL error - $sql - ".$this->dblink->error);
            die();
        }
        return $result;
    }

    public function query($sql) {
        $res = $this->dblink->query($sql);
	if (!$res)
	{
	    printf("Errormessage: %s\n", $this->dblink->error);
	}
	return $res;
    }

    public function insert($stmt) {
        if ($stmt->execute() === false) {
            $this->error('execute() failed: ' . htmlspecialchars($stmt->error));
            die();
        }

        return $stmt->insert_id;
    }

    public function update($stmt) {
        if ($stmt->execute() === false) {
            $this->error('execute() failed: ' . htmlspecialchars($stmt->error));
            die();
        }
        return $stmt->insert_id;
    }

    public function select($stmt) {
        if ($stmt->execute() === false) {
            $this->error('execute() failed: ' . htmlspecialchars($stmt->error));
            die();
        }
        $stmt->store_result();
        return $result;
    }
}
?>
