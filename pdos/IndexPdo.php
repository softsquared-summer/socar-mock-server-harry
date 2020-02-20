<?php

//READ

function checkId($id)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM User WHERE id=?;";

    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function registerAccount($name, $residentNo, $gender, $phoneNo, $id, $pw,
                         $cardNo, $cardDate, $ToSAgreementOne, $ToSAgreementTwo, $ToSAgreementThree, $ToSAgreementFour,
                         $licenseType, $licenseRegion, $licenseNo, $licenseExpiryDate, $licenseDate, $licenseAgreement)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO User (id, pw, name, birth, gender, phoneNo) VALUES (?, ?, ?, ?, ?, ?);";

    $st = $pdo->prepare($query);
    $st->execute([$id, $pw, $name, $residentNo, $gender, $phoneNo]);

    $query = "INSERT INTO Card (userNo, cardNum, cardDate, ToSAgreementOne, ToSAgreementTwo, ToSAgreementThree, ToSAgreementFour) VALUES ((SELECT no from User where id=?), ?, ?, ?, ?, ?, ?);";

    $st = $pdo->prepare($query);
    $st->execute([$id, $cardNo, $cardDate, $ToSAgreementOne, $ToSAgreementTwo, $ToSAgreementThree, $ToSAgreementFour]);

    $query = "INSERT INTO License (userNo, licenseType, licenseRegion, licenseNo, licenseExpiryDate, licenseDate, licenseAgreement) VALUES ((SELECT no from User where id=?), ?, ?, ?, ?, ?, ?);";

    $st = $pdo->prepare($query);
    $st->execute([$id, $licenseType, $licenseRegion, $licenseNo, $licenseExpiryDate, $licenseDate, $licenseAgreement]);

    $st = null;
    $pdo = null;
}

function printSocarzone()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT Socarzone.no socarzoneNo, count(Car.no) carCount, Socarzone.latitude, Socarzone.longitude FROM Car
                LEFT JOIN (SELECT no, carNo from Reservation
                WHERE (startTime < '2020-02-20 21:50:00' AND '2020-02-20 21:50:00' < endTime) OR ('2020-02-20 21:50:00' < startTime AND startTime < '2020-02-20 22:20:00' )) b
                ON Car.no=b.carNo
                
                JOIN (SELECT no, latitude, longitude FROM Socarzone) Socarzone ON Socarzone.no=Car.socarzoneNo
                WHERE b.carNo IS NULL GROUP BY Socarzone.no;";

    $st = $pdo->prepare($query);
    $st->execute([]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}










function isValidUser($id, $pw){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE id= ? AND pw = ?) AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $pw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}


// CREATE
//    function addMaintenance($message){
//        $pdo = pdoSqlConnect();
//        $query = "INSERT INTO MAINTENANCE (MESSAGE) VALUES (?);";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message]);
//
//        $st = null;
//        $pdo = null;
//
//    }


// UPDATE
//    function updateMaintenanceStatus($message, $status, $no){
//        $pdo = pdoSqlConnect();
//        $query = "UPDATE MAINTENANCE
//                        SET MESSAGE = ?,
//                            STATUS  = ?
//                        WHERE NO = ?";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message, $status, $no]);
//        $st = null;
//        $pdo = null;
//    }

// RETURN BOOLEAN
//    function isRedundantEmail($email){
//        $pdo = pdoSqlConnect();
//        $query = "SELECT EXISTS(SELECT * FROM USER_TB WHERE EMAIL= ?) AS exist;";
//
//
//        $st = $pdo->prepare($query);
//        //    $st->execute([$param,$param]);
//        $st->execute([$email]);
//        $st->setFetchMode(PDO::FETCH_ASSOC);
//        $res = $st->fetchAll();
//
//        $st=null;$pdo = null;
//
//        return intval($res[0]["exist"]);
//
//    }
