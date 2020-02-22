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


function printSocarzone($reservationStartTime, $reservationEndTime)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT Socarzone.no socarzoneNo, count(Car.no) carCount, Socarzone.latitude, Socarzone.longitude FROM Car
                LEFT JOIN (SELECT no, carNo, status from Reservation WHERE status != 'canceled' AND
                          (startTime < ? AND ? < endTime) OR ( ? < startTime AND startTime <  ? )) b
                ON Car.no=b.carNo
                JOIN (SELECT no, latitude, longitude FROM Socarzone) Socarzone ON Socarzone.no=Car.socarzoneNo
                WHERE b.carNo IS NULL GROUP BY Socarzone.no;";

    $st = $pdo->prepare($query);
    $st->execute([$reservationStartTime,$reservationStartTime, $reservationStartTime, $reservationEndTime]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function printSocarzoneByModel($reservationStartTime, $reservationEndTime, $carModel)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT Socarzone.no socarzoneNo, count(Car.no) carCount, Socarzone.latitude, Socarzone.longitude FROM Car
                LEFT JOIN (SELECT no, carNo, status from Reservation WHERE status != 'canceled' AND
                          (startTime < ? AND ? < endTime) OR ( ? < startTime AND startTime <  ? )) b
                ON Car.no=b.carNo
                JOIN (SELECT no, latitude, longitude FROM Socarzone) Socarzone ON Socarzone.no=Car.socarzoneNo
                JOIN (SELECT no, model from CarModel) CarModel ON CarModel.no=Car.modelNo
                WHERE b.carNo IS NULL AND CarModel.model IN ( $carModel ) GROUP BY Socarzone.no;";

    $st = $pdo->prepare($query);
    $st->execute([$reservationStartTime,$reservationStartTime, $reservationStartTime, $reservationEndTime]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function checkReservation($id, $reservationStartTime, $reservationEndTime){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(select * from Reservation join (select no, id from User) User on User.no=Reservation.userNo where User.id=?
                and (startTime < ? AND ? < endTime) OR ( ? < startTime AND startTime < ?)) as exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $reservationStartTime, $reservationStartTime, $reservationStartTime, $reservationEndTime]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}

function selectInsurance($model, $insuranceTime)
{
    $pdo = pdoSqlConnect();
    $query = "select concat(format(specialCharge*?,0),'원') specialCost, concat(format(standardCharge*?,0),'원') standardCost, concat(format(lightCharge*?,0),'원') lightCost from Insurance
                join (select no, model from CarModel) CarModel on CarModel.no=Insurance.carModelNo where CarModel.model=?;";

    $st = $pdo->prepare($query);
    $st->execute([$insuranceTime, $insuranceTime, $insuranceTime, $model]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function checkCarInfo($carNo)
{
    $pdo = pdoSqlConnect();
    $query = "select Car.no carNo, profileUrl, model, manufacture, sizeType type, fuelType, shiftType, ridingLimit, safetyOption, convenienceOption, concat(format(weekendCharge/10,0),'(30분)') basicCharge,
        concat(distanceThreeCharge, '~', distanceOneCharge, '원/km') distanceCharge from CarModel
    join (select no, modelNo from Car) Car on Car.modelNo=CarModel.no
    join (select carModelNo, weekendCharge, distanceOneCharge, distanceThreeCharge from Charge) Charge on Charge.carModelNo=CarModel.no
    where Car.no=?;";

    $st = $pdo->prepare($query);
    $st->execute([$carNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
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
