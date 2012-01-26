<?
require("../system/shared.php");

/*
event
username
order_id
address
signature (sha1(username.order_id.address.secret))

Events:
addAddress
RemoveAddress

Returns
OK
ERROR
NOTFOUND
BADSIG
*/


$event = filter_var($_REQUEST["event"], FILTER_SANITIZE_STRING);
$username = filter_var($_REQUEST["username"], FILTER_SANITIZE_STRING);
$order = filter_var($_REQUEST["order"],  FILTER_SANITIZE_STRING);
$addresses = filter_var($_REQUEST["address"],  FILTER_SANITIZE_STRING);
$sig = filter_var($_REQUEST["sig"],  FILTER_SANITIZE_STRING);

if (
$event == "" || $event === FALSE
||
$username == "" || $username === FALSE
||
$order == "" || $order === FALSE
||
$addresses == "" || $addresses === FALSE
||
$sig == "" || $sig === FALSE
)
{
	die("ERROR");
}

$db = Database::getInstance();

//Get user from username
$user = Users::getUserByUsername($username);
if ($username != NULL)
{
	//validate sig
	$checksig = sha1($event.$username.$order.$addresses.$user->secret);
	if ($sig != $checksig)
	{
		die("BADSIG");
	} else {
		//go
		$order = new Order($order);
		if ($order != NULL && $order->userid == $user->userid)
		{

		        $bc = new Bitcoin();


			foreach (explode(",",$addresses) as $address)
			{
				if ($bc->checkAddress($address))
				{
					switch ($event)
					{
						case "addAddress":
							$order->addAddress($address);
						break;

						case "removeAddress":
							$order->removeAddress($address);
						break;
					}
				} else {
					die("ERROR");
				}
			}
		}
	}
}

die("OK");
