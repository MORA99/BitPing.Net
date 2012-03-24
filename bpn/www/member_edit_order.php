<?php
require("system/shared.php");

checklogin();

$orderid = filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT);
$order = new Order($orderid);

checkOrderAccess($order);

if (isset($_POST["delete"])) {
    $order->delete();
    header("Location: ./member_orders.php");
    die();
}

if (isset($_POST["save"])) {
    $confirmations = filter_var($_POST["confirmations"],FILTER_SANITIZE_NUMBER_INT);
    $active = (isset($_POST["active"]));
    $order->confirmations = filter_var($confirmations, FILTER_SANITIZE_NUMBER_INT);
    $order->active = $active;

    $addresses = explode("\n", str_replace("\r", "", filter_var($_POST["addresses"], FILTER_SANITIZE_STRING)));
    $notifications = $_POST["notifications"];
    if (!is_array($notifications))
        $notifications = array();

    for ($i=0; $i<count($notifications); $i++)
        $notifications[$i] = filter_var($notifications[$i], FILTER_SANITIZE_NUMBER_INT);

    $order->setAddresses($addresses);
    $order->setNotifications($notifications);

    $order->save();

    header("Location: ./member_orders.php");
    die();
}

$title=" - Edit order"; require("header.php");
?>
<body>
<?php
topbar("orders", true);

    $activecheck = ($order->active)?' checked ':'';

    $cs0 = ($order->confirmations==0)?' selected ':'';
    $cs1 = ($order->confirmations==1)?' selected ':'';
    $cs2 = ($order->confirmations==2)?' selected ':'';
    $cs3 = ($order->confirmations==3)?' selected ':'';
    $cs4 = ($order->confirmations==4)?' selected ':'';
    $cs5 = ($order->confirmations==5)?' selected ':'';
    $cs6 = ($order->confirmations==6)?' selected ':'';

    $notify = array();
    foreach ($order->notifications as $type)
        $notify[] = $type->id;
    $nc1 = (in_array(1, $notify))?' checked ':'';
    $nc2 = (in_array(2, $notify))?' checked ':'';
    $nc3 = (in_array(3, $notify))?' checked ':'';
    ?>
    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>Edit order</h1>
            </div>
            <div class="row">
                <div class="span10">

                    <form method="POST">
                        <fieldset>
                            <div class="clearfix">
                                <label>Active</label>
                                <div class="input">
                                    <input name="active" type="checkbox" value="1" <?php echo $activecheck?>>
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Confirmations needed</label>
                                <div class="input">
                                    <select name="confirmations">
                                        <option value="0"<?php echo $cs0?>>0</option>
                                        <option value="1"<?php echo $cs1?>>1</option>
                                        <option value="2"<?php echo $cs2?>>2</option>
                                        <option value="3"<?php echo $cs3?>>3</option>
                                        <option value="4"<?php echo $cs4?>>4</option>
                                        <option value="5"<?php echo $cs5?>>5</option>
                                        <option value="6"<?php echo $cs6?>>6</option>
                                    </select>
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Notification methods</label>
                                <div class="input">
                                    <input name="notifications[]" type="checkbox" value="1" <?php echo $nc1?>> Email<br>
                                    <input name="notifications[]" type="checkbox" value="2" <?php echo $nc2?>> HTTP POST (Set url in profile)<br>
				    <input name="notifications[]" type="checkbox" value="3" <?php echo $nc3?>> Pubnub (See API for details)
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Addresses<br>1 per line</label>
                                <div class="input">
                                    <textarea name="addresses" rows=25 style="width: 325px;"><?
foreach ($order->addresses as $address)        
    echo $address->address."\n";
                                        ?></textarea>
                                </div>
                            </div>

                            <div class="clearfix">
                                <div class="input">
                                    <input type="submit" name="save" value="Save">&nbsp;<input type="submit" name="delete" value="Delete">
                                </div>
                            </div>

                        </fieldset>
                    </form>

                </div>
<?php require("system-status.php"); ?>
            </div>
                <?php require("footer.php"); ?>
        </div>
</body>
</html>
