<?php
require 'function.php';
include "/var/www/html/api-server/pdos/DatabasePdo.php";

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));

function cron(){
    $pdo = pdoSqlConnect();
    $query = "UPDATE Reservation SET status='rented', rentedAt=date_format(now(),'%Y/%m/%d %H:%i:00') where status='reservation' and startedAt=date_format(now(),'%Y/%m/%d %H:%i:00');";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $query = "select fcmToken from User join (select status, startedAt, userNo from Reservation) Reservation on Reservation.userNo=User.no where status='rented' and startedAt=date_format(now(),'%Y/%m/%d %H:%i:00');";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;
    return $res;
}

$fcm = cron();
$fcmRes=json_decode(json_encode($fcm));

for($i=0;;$i++){
    if(!$fcmRes[$i]->{'fcmToken'}){
        break;
    }
    //sendFcm($fcmRes[$i]->{'fcmToken'});
}

//return $res;
?>