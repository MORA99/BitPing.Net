<? require("system/shared.php"); ?>
<?checklogin();?>
<? $title=" - Orders"; require("header.php"); ?>
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
				<th>ID</th>
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
                            <tr><td><?=$order->orderid?></td><td><a href="/member_edit_order.php?id=<?=$order->orderid?>"><?=$active?></a></td><td><?=$order->confirmations?></td><td><?=$types?></td><td><?=$addresses?></td></tr>
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



<hr>After creating a order you can use a API to add and remove addresses<br><br>
POST/GET to URL : https://bitping.net/c/api.php

Include these variables
<table>
<tr><td>event</td><td>addAddress<br>removeAddress</td></tr>
<tr><td>username</td><td>Your username</td></tr>
<tr><td>order</td><td>Your order id (see above)</td></tr>
<tr><td>address</td><td>A valid bitcoin address (several seperated with komma, ie. address1,address2,address3)</td></tr>
<tr><td>sig</td><td>A signature, generated as sha1(event.username.order.address.secret)<br>You can find your secret value on the API page</td></tr>
</table>

Returns a single message of OK ERROR or BADSIG.<br>
BADSIG means signature is invalid.<br>
ERROR means one or more arguments are invalid (maybe order belongs to other user)<br>
OK means OK :)<br><br>


                </div>
<? require("system-status.php"); ?>
            </div>
<? require("footer.php"); ?>
        </div>
</body>
</html>
