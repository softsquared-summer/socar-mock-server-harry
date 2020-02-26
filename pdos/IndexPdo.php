<?php

//READ
function test(){

    $pdo = pdoSqlConnect();

    $query = "select fcmTocken, deviceType from User join (select status, startedAt, userNo from Reservation) Reservation on Reservation.userNo=User.no  where status='rented' and startedAt=date_format(now(),'%Y/%m/%d %H:%i:00');";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;$pdo = null;

    return $res;
}

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

function registerAccount($name, $residentNo, $gender, $phoneNo, $id, $encryptedPw,
                         $cardNo, $cardDate, $licenseType, $licenseRegion, $licenseNo, $licenseExpiryDate, $licenseDate)
{
    $pdo = pdoSqlConnect();
    try {
        $pdo->beginTransaction();
        $query = "INSERT INTO User (id, encryptedPw, name, birth, gender, phoneNo) VALUES (?, ?, ?, ?, ?, ?);";

        $st = $pdo->prepare($query);
        $st->execute([$id, $encryptedPw, $name, $residentNo, $gender, $phoneNo]);

        $query = "INSERT INTO Card (userNo, cardNum, cardDate) VALUES ((SELECT no from User where id=?), ?, ?);";

        $st = $pdo->prepare($query);
        $st->execute([$id, $cardNo, $cardDate]);

        $query = "INSERT INTO License (userNo, licenseType, licenseRegion, licenseNo, licenseExpiryDate, licenseDate) VALUES ((SELECT no from User where id=?), ?, ?, ?, ?, ?);";

        $st = $pdo->prepare($query);
        $st->execute([$id, $licenseType, $licenseRegion, $licenseNo, $licenseExpiryDate, $licenseDate]);

        $pdo->commit();
        $st = null;
        $pdo = null;

        return 'commitComplete';
    } catch (PDOException $e) {
        $pdo->rollback();
        return $e->getMessage();
    }
}

function printSocarzone($reservationStartTime, $reservationEndTime)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT Socarzone.no socarzoneNo, count(Car.no) carCount, Socarzone.latitude, Socarzone.longitude FROM Car
                LEFT JOIN (SELECT no, carNo, status from Reservation WHERE status != 'canceled' AND
                          (startedAt < ? AND ? < endedAt) OR ( ? < startedAt AND startedAt <  ? )) b
                ON Car.no=b.carNo
                JOIN (SELECT no, latitude, longitude FROM Socarzone) Socarzone ON Socarzone.no=Car.socarzoneNo
                WHERE b.carNo IS NULL GROUP BY Socarzone.no limit 0, 100;";

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
                          (startedAt < ? AND ? < endedAt) OR ( ? < startedAt AND startedAt <  ? )) b
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
                and status!='canceled' and ((startedAt < ? AND ? < endedAt) OR ( ? < startedAt AND startedAt < ?)) ) as exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $reservationStartTime, $reservationStartTime, $reservationStartTime, $reservationEndTime]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}

function printSocarzoneAddress($socarzoneNo){
    $pdo = pdoSqlConnect();
    $query = "select address from Socarzone where no=?;";

    $st = $pdo->prepare($query);
    $st->execute([$socarzoneNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function printCars($socarzoneNo){
    $pdo = pdoSqlConnect();
    $query = "select Car.no carNo, profileUrl, model from Socarzone join (select no, socarzoneNo, modelNo from Car) Car on Car.socarzoneNo=Socarzone.no
                                join (select no, profileUrl, model from CarModel) CarModel on CarModel.no=Car.modelNo where Socarzone.no=?;";

    $st = $pdo->prepare($query);
    $st->execute([$socarzoneNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}


function selectInsurance($carNo, $insuranceTime)
{
    $pdo = pdoSqlConnect();
    $query = "select concat(format(specialCharge*?,0),'원') specialCost, concat(format(standardCharge*?,0),'원') standardCost, concat(format(lightCharge*?,0),'원') lightCost from Insurance
                join (select no, modelNo from Car) Car on Car.modelNo=Insurance.carModelNo where Car.no=?;";

    $st = $pdo->prepare($query);
    $st->execute([$insuranceTime, $insuranceTime, $insuranceTime, $carNo]);
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

function calculationPayment($chargeCriteria, $useTime, $carNo)
{
    $pdo = pdoSqlConnect();
    //$query = "select concat(format( $chargeCriteria*?,0),'원') as rentCharge, $chargeCriteria*? carculForTotal from Charge join (select no, modelNo from Car) Car on Car.modelNo=Charge.carModelNo where Car.no=?;";
    $query = "select $chargeCriteria*? rentCharge from Charge join (select no, modelNo from Car) Car on Car.modelNo=Charge.carModelNo where Car.no=?;";

    $st = $pdo->prepare($query);
    $st->execute([$useTime, $carNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function printInsuranceCharge($insurance, $insuranceTime, $carNo)
{
    $pdo = pdoSqlConnect();
    //$query = "select concat(format( $insurance*?,0),'원') insuranceCharge, $insurance*? carculForTotal from Insurance
//                join (select no, modelNo from Car) Car on Car.modelNo=Insurance.carModelNo where Car.no=?;";
    $query = "select $insurance*? insuranceCharge from Insurance
                join (select no, modelNo from Car) Car on Car.modelNo=Insurance.carModelNo where Car.no=?;";

    $st = $pdo->prepare($query);
    $st->execute([$insuranceTime, $carNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function cardInfo($id)
{
    $pdo = pdoSqlConnect();
    $query = "select createdAt from Card join (select no, id from User) User on User.no=Card.userNo where User.id=?;";

    $st = $pdo->prepare($query);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function checkSchedule($startMidnight, $endMidnight, $carNo){
    $pdo = pdoSqlConnect();
    $query = "select startedAt otherStartTime, endedAt otherEndTime from Reservation
            where carNo=? and status!='canceled' and ((startedAt < ? AND ? < endedAt) OR (? < startedAt AND startedAt < ?));";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$carNo, $startMidnight, $startMidnight, $startMidnight, $endMidnight]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return $res;

}


function checkLicenseExpiryDate($id){
    $pdo = pdoSqlConnect();
    $query = "select licenseExpiryDate from License join (select no, id from User) User on User.no=License.userNo where User.id=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return $res[0];

}


function printCarAddress($carNo){
    $pdo = pdoSqlConnect();
    $query = "select address from Socarzone join (select no, socarzoneNo from Car) Car on Car.socarzoneNo=Socarzone.no where Car.no=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$carNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return $res[0];

}
function makeReservation($userNo, $carNo, $startTime, $endTime, $insurance, $rentCharge, $insuranceCharge, $address)
{
    $pdo = pdoSqlConnect();
    try {
        $pdo->beginTransaction();
        $query = "INSERT INTO Reservation (userNo, carNo, status, startedAt, endedAt, rentZone, returnZone, insurance) VALUES
                                            (?, ?, 'reservation', ?, ?, ?, ?, ?);";

        $st = $pdo->prepare($query);
        $st->execute([$userNo, $carNo, $startTime, $endTime, $address, $address, $insurance]);

        $query = "INSERT INTO Payment (reservationNo, status, rentCharge, insuranceCharge, distanceCharge, couponDiscount) VALUES ((select max(no) from Reservation where userNo=?), 'beforeRent', ?, ?, 0, 0);";

        $st = $pdo->prepare($query);
        $st->execute([$userNo, $rentCharge, $insuranceCharge]);

        $pdo->commit();
        $st = null;
        $pdo = null;

        return 'commitComplete';
    } catch (PDOException $e) {
        $pdo->rollback();
        return $e->getMessage();
    }
}



function printRecentReservation($id){
    $pdo = pdoSqlConnect();
    $query = "select carNo, model, fuelType, safetyOption, convenienceOption, rentZone socarzoneAddress, startedAt startTime, endedAt endTime, status, licensePlateNo from Reservation
        join (select min(startedAt) min from Reservation where userNo=(select no from User where id=?)  and ((status='reservation' and startedAt > current_timestamp) or status='rented') ) Min on Min.min=Reservation.startedAt
        join (select no, licensePlateNo, modelNo from Car) Car on Car.no=Reservation.carNo
        join (select no, model, fuelType, safetyOption, convenienceOption from CarModel) CarModel on CarModel.no=Car.modelNo;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return $res[0];

}

function printUserInfo($id){
    $pdo = pdoSqlConnect();
    $query = "select no userNo, name, id, floor(totalDistance/100)+1 level, phoneNo phoneNumber, profileUrl from User where id=?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return $res[0];

}

function changePw($id, $encryptedPw)
{
    $pdo = pdoSqlConnect();
    $query = "update User set encryptedPw=? where id=?;";

    $st = $pdo->prepare($query);
    $st->execute([$encryptedPw, $id]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    //$res = $st->fetchAll();

    $st = null;
    $pdo = null;

    //return $res[0];
}







function pullEncPw($idx){
    $pdo = pdoSqlConnect();
    $query = "SELECT encryptedPw from User where id=?;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$idx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return $res[0];

}


function isValidUser($id, $pw){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE id= ? AND encryptedPw = ?) AS exist;";


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
