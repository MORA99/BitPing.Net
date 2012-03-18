<? require("system/shared.php"); ?>
<?checklogin();?>
<? $title=" - History"; require("header.php"); ?>
<body>
    <? topbar("history", true); ?>

    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>Your notification history <small>over the last 50 notifications</small></h1>
            </div>
            <div class="row">
                <div class="span10">

                    <?
                    $notifications = Notifications::getNotificationsForUsername(USERNAME);

                    if (count($notifications) > 0) {
                        ?>

                    <table class="zebra-striped">
                        <thead>
                            <tr>
				<th>Order</th>
                                <th>Timestamp</th>
				<th>Address</th>
				<th>Amount</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
    <?
                                foreach ($notifications as $notification) {
				$btc = rtrim(number_format($notification->value / 100000000, 8),0);
    ?>
                            <tr>
				<td><?=$notification->orderid?></td>
				<td><?=$notification->timestamp?></a></td>
				<td><?=$notification->address?></td>
				<td><?=$btc?></td>
				<td><?
				$order = new Order($notification->orderid);
                                foreach ($order->notifications as $type) {
                 	               echo $type->name."<br>";
                                }
				?></td>
			    </tr>
    <?
                                }
    ?>
                        </tbody>
                    </table>
                                <?
}
?>

                </div>
<? require("system-status.php"); ?>
            </div>
<? require("footer.php"); ?>
        </div>
</body>
</html>
