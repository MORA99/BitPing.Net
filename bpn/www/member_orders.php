<? require("system/shared.php"); ?>
<?checklogin();?>
<? require("header.php"); ?>
<body>
    <? topbar("orders", true); ?>

    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>Your orders</h1>
            </div>
            <div class="row">
                <div class="span10">

                    <?
                    $orders = Orders::getOrdersForUsername(USERNAME);

                    if (count($orders) > 0) {
                        ?>

                    <table class="zebra-striped">
                        <thead>
                            <tr>
                                <th>Active?</th>
                                <th>Confirmations needed</th><th>Notification types</th>
                                <th>Addresses monitored</th>
                            </tr>
                        </thead>
                        <tbody>
    <?
                                foreach ($orders as $order) {
                                    $active = ($order->active)?"Yes":"No";
                                    $addresses = count($order->addresses);
                                    $types = array();
                                    foreach ($order->notifications as $type) {
                                        $types[] = $type->name;
                                    }
                                    $types = implode("<br>", $types);
                                    ?>
                            <tr><td><a href="/member_edit_order.php?id=<?=$order->orderid?>"><?=$active?></a></td><td><?=$order->confirmations?></td><td><?=$types?></td><td><?=$addresses?></td></tr>
                                    <?
                                }
    ?>
                        </tbody>
                    </table>
                                <?
}
?>         
                    <div class="clearfix">
                        <div class="input">
                            <input type="button" value="Create new order" onClick="window.location='member_create_order.php';">
                        </div>
                    </div>

                </div>
<? require("system-status.php"); ?>
            </div>
<? require("footer.php"); ?>
        </div>
</body>
</html>
