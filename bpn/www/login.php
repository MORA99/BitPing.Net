<?
require("system/shared.php");

$username = filter_var($_POST["user"], FILTER_SANITIZE_STRING);
$password = $_POST["pass"];

function logLogin($username, $success)
{
    $db = Database::getInstance();
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $db->prepare("INSERT INTO logins (`ip`, `username`, `success`, `datetime`) VALUES (?, ?, ?, NOW())");
    $success = ($success)?1:0;
    $stmt->bind_param("ssi", $ip, $username, $success);
    $db->insert($stmt);
}

if ($username != "" && $password != "") {
    $user = User::getUser($username, $password);
    if ($user != NULL) {
        $_SESSION["AUTH_USER_NAME"] = $user->username;
        $_SESSION["AUTH_FROM_IP"] = $_SERVER['REMOTE_ADDR'];
        logLogin($user->username, true);
        sleep(1);
        header("Location: /member_start.php");
        die();
    } else {
        logLogin($username, false);
        sleep(rand(1,5));
        header("Location: /?errmsg=loginfailure");
    }
}
logLogin($username, false);
header("Location: /?errmsg=loginfailure");
?>
