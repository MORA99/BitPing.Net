<?php
class Order {
    private $orderid;
    private $active;
    private $userid;
    private $confirmations;
    private $notifications;
    private $addresses;

    public function __construct($orderid=0) {
        $this->orderid = $orderid;

        if ($this->orderid != 0) {
            $db = Database::getInstance();
            $this->notifications = array();
            $this->addresses = array();

            $stmt = $db->prepare("SELECT `active`, `uid`, `confirmations` FROM orders WHERE order_id = ? LIMIT 1");
            $stmt->bind_param("i", $this->orderid);
            $db->select($stmt);
            $stmt->bind_result($active, $uid, $confirmations);

            if ($stmt->fetch()) {
                $this->active = ($active==1);
                $this->userid = $uid;
                $this->confirmations = $confirmations;
                $stmt->close();

                $stmt = $db->prepare("SELECT notify_id FROM order_notify WHERE order_id = ?");
                $stmt->bind_param("i", $this->orderid);
                $db->select($stmt);
                $stmt->bind_result($id);
                while ($stmt->fetch()) {
                    $this->notifications[] = new Notify($id);
                }

                $stmt = $db->prepare("SELECT address FROM order_address WHERE order_id = ?");
                $stmt->bind_param("i", $this->orderid);
                $db->select($stmt);
                $stmt->bind_result($address);
                while ($stmt->fetch()) {
                    $this->addresses[] = new Address($address);
                }
            }
        }
    }

    public function __get($name) {
        return $this->$name;
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function setAddresses($addresses) {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM order_address WHERE order_id = ?");
        $stmt->bind_param("i", $this->orderid);
        $db->update($stmt);

        $stmt = $db->prepare("INSERT IGNORE INTO order_address (order_id, address) VALUES (?, ?)");
        $bc = new Bitcoin();
        foreach ($addresses as $address) {
            if ($bc->checkAddress($address)) {
                $stmt->bind_param("is", $this->orderid, $address);
                $db->update($stmt);
            }
        }
        $this->addresses = $addresses;
    }

    public function setNotifications($notifications) {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM order_notify WHERE order_id = ?");
        $stmt->bind_param("i", $this->orderid);
        $db->update($stmt);

        $stmt = $db->prepare("INSERT INTO order_notify (order_id, notify_id) VALUES (?, ?)");
        foreach ($notifications as $type) {
            $stmt->bind_param("ii", $this->orderid, $type);
            $db->update($stmt);
        }
        $this->notifications = $notifications;
    }

    public function save() {
        $active = ($this->active)?1:0;
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE orders SET active=?, confirmations=? WHERE order_id = ?");
        $stmt->bind_param("iii", $active, $this->confirmations, $this->orderid);
        $db->update($stmt);
    }

    public function delete() {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM orders WHERE order_id = ? LIMIT 1");
        $stmt->bind_param('i', $this->orderid);
        $db->update($stmt);
    }

   public function addAddress($address)
   {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT IGNORE INTO `order_address` (`order_id`, `address`) VALUES (?,?)");
        $stmt->bind_param('is', $this->orderid, $address);
        $db->update($stmt);
   }

   public function removeAddress($address)
   {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM `order_address` WHERE `order_id` = ? AND address = ? LIMIT 1");
        $stmt->bind_param('is', $this->orderid, $address);
        $db->update($stmt);
   }
}
?>
