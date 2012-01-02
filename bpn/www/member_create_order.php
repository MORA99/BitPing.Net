<?
require("system/shared.php");
checklogin();

if (isset($_POST["create"])) {
    $user = Users::getActiveUser();

    //create order
    $active = isset($_POST["active"]);
    $notifications = $_POST["notifications"];
    if (!is_array($notifications))
        $notifications = array();

    for ($i=0; $i<count($notifications); $i++)
        $notifications[$i] = filter_var($notifications[$i], FILTER_SANITIZE_NUMBER_INT);

    $addresses = explode("\n", str_replace("\r", "", filter_var($_POST["addresses"], FILTER_SANITIZE_STRING)));
    $confirmations = filter_var($_POST["confirmations"], FILTER_SANITIZE_NUMBER_INT);

    orders::createOrder($user->userid, $active, $confirmations, $notifications, $addresses);

    header("Location: /member_orders.php");
    die();
}

?>

<? require("header.php"); ?>
<body>
<? topbar("orders", true); ?>

    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>Create new order</h1>
            </div>
            <div class="row">
                <div class="span10">

                    <form method="POST">
                        <fieldset>
                            <div class="clearfix">
                                <label>Active</label>
                                <div class="input">
                                    <input name="active" type="checkbox" value="1" checked>
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Confirmations needed</label>
                                <div class="input">
                                    <select name="confirmations">
                                        <option value="1" selected>1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                    </select>
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Notification methods</label>
                                <div class="input">
                                    <input name="notifications[]" type="checkbox" value="1" checked> Email<br>
                                    <input name="notifications[]" type="checkbox" value="2" checked> HTTP POST
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Addresses<br>1 per line</label>
                                <div class="input">
                                    <textarea name="addresses" rows=25 style="width: 300px;"></textarea>
                                </div>
                            </div>

                            <div class="clearfix">
                                <div class="input">
                                    <input type="submit" name="create" value="Create">
                                </div>
                            </div>

                        </fieldset>
                    </form>

                </div>
<? require("system-status.php"); ?>
            </div>
<? require("footer.php"); ?>
        </div>
</body>
</html>
