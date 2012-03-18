<?php
class Notify {
    public $id;
    public $name;
    public $desc;
    public $price;

    public function __construct($id) {
        $this->id = $id;
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT `name`, `desc`, `price` FROM notify_options WHERE notify_id=? LIMIT 1");
        $stmt->bind_param("i", $id);
        $db->select($stmt);
        $stmt->bind_result($name, $desc, $price);

        if ($stmt->fetch()) {
            $this->name = $name;
            $this->desc = $desc;
            $this->price = $price;
        }
    }
}
?>
