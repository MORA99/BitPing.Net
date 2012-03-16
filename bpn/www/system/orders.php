<?php
class Orders {
    public static function createOrder($userid, $active, $confirmations, $notifications, $addresses) {
        $db = Database::getInstance();
        $db->startTransaction();

        $stmt = $db->prepare("INSERT INTO orders (uid, confirmations) VALUES (?, ?)");
        $stmt->bind_param("ii", $userid, $confirmations);
        $order_id = $db->insert($stmt);

        if ($order_id != null) {
            $stmt = $db->prepare("INSERT INTO order_notify (order_id, notify_id) VALUES (?, ?)");
            foreach ($notifications as $notification) {
                $stmt->bind_param("ii", $order_id, $notification);
                $db->insert($stmt);
            }

            $stmt = $db->prepare("INSERT INTO order_address (order_id, address) VALUES (?, ?)");
            $bc = new Bitcoin();
            foreach ($addresses as $address) {
                if ($bc->checkAddress($address)) {
                    $stmt->bind_param("is", $order_id, $address);
                    $db->insert($stmt);
                }
            }

            $db->commit();
        } else {
            $db->rollback();
        }
    }

    public static function getOrdersForUsername($username) {
        $db = Database::getInstance();
        $sql = "SELECT order_id FROM orders JOIN users ON users.uid = orders.uid WHERE users.username = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $username);
        $db->select($stmt);
        $stmt->bind_result($order_id);

        while ($stmt->fetch()) {
            $orders[] = new Order($order_id);
        }
        if ( isset ( $orders ) ) return $orders;
	else return NULL;
    }
}
?>
