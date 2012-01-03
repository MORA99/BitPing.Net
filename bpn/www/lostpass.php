<?
require("system/shared.php");

if (isset($_GET["reset"])) {
    $reset = filter_var($_GET["reset"], FILTER_SANITIZE_STRING);
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT user_id FROM reset_pass_requests WHERE code=? LIMIT 1");
    $stmt->bind_param("s", $reset);
    $db->select($stmt);
    $stmt->bind_result($userid);

    if ($stmt->fetch()) {
        $user = new User($userid);
        $code = bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
        $user->password = $code;
        $user->save();

        $stmt = $db->prepare("DELETE FROM reset_pass_requests WHERE code=?");
        $stmt->bind_param("s", $reset);
        $db->update($stmt);

        mail($user->email, "New password for BitPing.Net",
                "Your password has been reset to ".$code." for your account at BitPing.Net\r\n\r\n".
                "Your username is ".$user->username."\r\n".
                "Please login and change your password as soon as possible\r\n\r\n".
                "All the best\r\nThe BitPing.Net robot"
                , "FROM: monitor@BitPing.Net");
    }

    header("Location: /?infomsg=passresetcomplete");
    die();
}

if (isset($_POST['resend'])) {
    sleep(1);

    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);

    if ($username != "") {
        $user = Users::getUserByUsername($username);
    } else if ($email != "") {
        $user = Users::getUserByEmail($email);
    } else {
        $user = null;
    }

    if ($user != null) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $code = bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
        $db = Database::getInstance();

        $stmt = $db->prepare("INSERT INTO `reset_pass_requests`
        (`user_id`, `code`, `requested_from`, `email`, `username`, `expires`) 
        VALUES 
        (?,?,?,?,?,DATE_ADD(NOW(),INTERVAL 90 MINUTE))");
        $stmt->bind_param("issss", $user->userid, $code, $ip, $email, $username);
        $db->insert($stmt);

        mail($user->email, "Password reset request for BitPing.Net",
                "Someone (probaly you) requested a password reset email for your account at BitPing.Net\r\n\r\n".
                "Your username is ".$user->username."\r\n".
                "The request was made from ".$ip."\r\n".
                "To reset your password please go to : http://bitping.net/lostpass.php?reset=".$code." within 1hour\r\n\r\n".
                "If you did not request this change, please ignore this email, the request will timeout in 1hour, and noone can reset the pass without the code shown above\r\n\r\n".
                "All the best\r\nThe BitPing.Net robot"
                , "FROM: monitor@BitPing.Net");
    }

    header("Location: /?infomsg=resetmsgsent");
    die();
}

$title=" - Lost password"; require("header.php");
?>
<body>
<? topbar("home"); ?>

    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>Lost password <small>Fill in email or username</small></h1>
            </div>
            <div class="row">
                <div class="span10">

                    <form method="POST">
                        <fieldset>

                            <div class="clearfix">
                                <label>Email-address</label>
                                <div class="input">
                                    <input type="text" name="email">
                                </div>
                            </div>

                            <div class="clearfix">
                                <label>Username</label>
                                <div class="input">
                                    <input type="text" name="username">
                                </div>
                            </div>

                            <div class="clearfix">
                                <div class="input">
                                    <input type="submit" name="resend" value="Send reset request email">
                                </div>
                            </div>
                        </fieldset>
                </div>
<? require("system-status.php"); ?>
            </div>
<? require("footer.php"); ?>
        </div>
</body>
</html>
