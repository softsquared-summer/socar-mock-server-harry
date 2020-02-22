<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "index":
            echo "test API Server";
            break;
        case "ACCESS_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/errors.log");
            break;
        /*
         * API No. 0
         * API Name : 테스트 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "test":
            http_response_code(200);

            $test = geocode();
            echo $test;

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 1
         * API Name : 회원가입 API
         * 마지막 수정 날짜 : 20.02.19
         */
        case "register":
            if($req->ToSAgreementOne!='Y' | $req->ToSAgreementTwo!='Y' | $req->ToSAgreementThree!='Y' | $req->ToSAgreementFour!='Y'){
                $res->isSuccess = FALSE;
                $res->code = 209;
                $res->message = "결제서비스 이용약관에 모두 동의해야 합니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            if($req->licenseAgreement!='Y'){
                $res->isSuccess = FALSE;
                $res->code = 212;
                $res->message = "운전면허 고유식별정보 수집 및 이용에 동의해야 합니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $checkName = preg_match("/^[가-힣]{4,12}$|^[a-zA-Z]{2,10}$/", $req->name);
            if ($checkName == false) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "이름은 한글2~4자, 영문 2~10자로 입력해야 합니다.";
                echo json_encode($res);
                return;
            }

            $checkBirth = preg_match("/^(?:[0-9]{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[1,2][0-9]|3[0,1]))$/", $req->residentNo);
            $checkGender = preg_match("/^[1-4]$/", $req->gender);
            if ($checkBirth == false | $checkGender==false) {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "주민번호가 올바르지 않습니다.";
                echo json_encode($res);
                return;
            }

            $checkPhoneNo=preg_match("/^(010|011|016|017|018|019)-?\d{3,4}-?\d{4}$/u", $req->phoneNo);
            if($checkPhoneNo==false) {
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "전화번호가 올바르지 않습니다";
                echo json_encode($res);
                return;
            }

            $checkId = preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $req->id);
            if ($checkId == false) {
                $res->isSuccess = FALSE;
                $res->code = 204;
                $res->message = "아이디(이메일) 형식이 올바르지 않습니다.";
                echo json_encode($res);
                return;
            }

            $checkPw = preg_match("/^(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^*()\-_=+\\\|\[\]{};:\'\",.<>\/?])*.{8,45}$/i", $req->pw);
            if ($checkPw == false) {
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "비밀번호는 영문, 숫자 포함 8자리 이상 입력해야 합니다.";
                echo json_encode($res);
                return;
            }

            $checkCardNo = preg_match("/^[0-9]{16}$/", $req->cardNo);
            if ($checkCardNo==false) {
                $res->isSuccess = FALSE;
                $res->code = 207;
                $res->message = "카드 번호가 올바르지 않습니다.";
                echo json_encode($res);
                return;
            }

            $checkCardDate = preg_match("/^((?:0[1-9]|1[0-2])\/(?:[0-9]{2}))$/", $req->cardDate);
            if (date("y") > substr($req->cardDate, 3, 2) ){
                $checkCardDate = false;
            } else if ( date("y") == substr($req->cardDate, 3, 2) & date("m") > substr($req->cardDate, 0, 2) ){
                $checkCardDate = false;
            }
            if ($checkCardDate == false) {
                $res->isSuccess = FALSE;
                $res->code = 208;
                $res->message = "카드유효 기간이 올바르지 않습니다.";
                echo json_encode($res);
                return;
            }

            if($req->licenseType!='1종보통' & $req->licenseType!='2종보통'){
                $res->isSuccess = FALSE;
                $res->code = 210;
                $res->message = "면허종류가 올바르지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if($req->licenseRegion!='서울' & $req->licenseRegion!='부산' & $req->licenseRegion!='경기' & $req->licenseRegion!='강원' & $req->licenseRegion!='충북' & $req->licenseRegion!='충남'
                & $req->licenseRegion!='전북' & $req->licenseRegion!='전남' & $req->licenseRegion!='경북' & $req->licenseRegion!='경남' & $req->licenseRegion!='제주' & $req->licenseRegion!='대구'
                & $req->licenseRegion!='광주' & $req->licenseRegion!='대전' & $req->licenseRegion!='울산'){
                $res->isSuccess = FALSE;
                $res->code = 210;
                $res->message = "면허지역이 올바르지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $checkLicenseNo=preg_match("/^\d{2}-\d{6}-\d{2}$/", $req->licenseNo);
            if($checkLicenseNo==false) {
                $res->isSuccess = FALSE;
                $res->code = 210;
                $res->message = "면허번호가 올바르지 않습니다";
                echo json_encode($res);
                return;
            }

            $checkLicenseDate = preg_match("/^(19|20)(?:[0-9]{2}\/(?:0[1-9]|1[0-2])\/(?:0[1-9]|[1,2][0-9]|3[0,1]))$/", $req->licenseDate);
            $checkLicenseExpiryDate = preg_match("/^(19|20)(?:[0-9]{2}\/(?:0[1-9]|1[0-2])\/(?:0[1-9]|[1,2][0-9]|3[0,1]))$/", $req->licenseExpiryDate);
            $licenseDateYear = substr($req->licenseDate, 0, 4);
            $licenseDateMonth = substr($req->licenseDate, 5, 2);
            $licenseDateDay = substr($req->licenseDate, 8, 2);
            $licenseExpiryDateYear = substr($req->licenseExpiryDate, 0, 4);
            $licenseExpiryDateMonth = substr($req->licenseExpiryDate, 5, 2);
            $licenseExpiryDateDay = substr($req->licenseExpiryDate, 8, 2);

            if ( $licenseDateYear > $licenseExpiryDateYear ){
                $checkLicenseExpiryDate = false;
            } else if ( $licenseDateYear == $licenseExpiryDateYear &  $licenseDateMonth > $licenseExpiryDateMonth){
                $checkLicenseExpiryDate = false;
            } else if ( $licenseDateYear == $licenseExpiryDateYear &  $licenseDateMonth == $licenseExpiryDateMonth & $licenseDateDay > $licenseExpiryDateDay){
                $checkLicenseExpiryDate = false;
            }
            if (date("yy") > $licenseExpiryDateYear ){
                $checkLicenseExpiryDate = false;
            } else if ( date("yy") == $licenseExpiryDateYear & date("m") > $licenseExpiryDateMonth ){
                $checkLicenseExpiryDate = false;
            } else if ( date("yy") == $licenseExpiryDateYear & date("m") == $licenseExpiryDateMonth & date("d") > $licenseExpiryDateDay ){
                $checkLicenseExpiryDate = false;
            }
            if ($checkLicenseDate == false | $checkLicenseExpiryDate == false) {
                $res->isSuccess = FALSE;
                $res->code = 211;
                $res->message = "적성검사 기간이 올바르지 않습니다.";
                echo json_encode($res);
                return;
            }

            $checkId = checkId($req->id);
            if ($checkId != null) {
                $res->isSuccess = FALSE;
                $res->code = 213;
                $res->message = "이미 존재하는 아이디입니다.";
                echo json_encode($res);
                return;
            }
            if($req->inviteCode!=null){
                $checkInviteCode = checkId($req->inviteCode);
                if ($checkInviteCode == null) {
                    $res->isSuccess = FALSE;
                    $res->code = 202;
                    $res->message = "존재하지 않는 추천인 아이디입니다.";
                    echo json_encode($res);
                    return;
                }
            }

            //나중에 트랜잭션 다시 시도
            registerAccount($req->name, $req->residentNo, $req->gender, $req->phoneNo, $req->id, $req->pw,
                $req->cardNo, $req->cardDate, $req->ToSAgreementOne, $req->ToSAgreementTwo, $req->ToSAgreementThree, $req->ToSAgreementFour,
                $req->licenseType, $req->licenseRegion, $req->licenseNo, $req->licenseExpiryDate, $req->licenseDate, $req->licenseAgreement);

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "회원 가입 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 3
         * API Name : 쏘카존 출력 API
         * 마지막 수정 날짜 : 20.02.21
         */
        case "printSocarzone":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $model = $_GET["model"];
            $startTime = $_GET["startTime"];
            $endTime = $_GET["endTime"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $reservationDay= "오늘 ";
            if ( 0<=date("i") & date("i")<10){
                $reservationStartTime= strtotime((date("H")).":20");
                $reservationEndTime= strtotime((date("H")).":50");
            } else  if ( 10<=date("i") & date("i")<20){
                $reservationStartTime= strtotime((date("H")).":30");
                if( date("H")==23 ){
                    $reservationEndTime= strtotime((date("H")+1).":00 +1 day");
                } else {
                    $reservationEndTime= strtotime((date("H")+1).":00");
                }
            } else  if ( 20<=date("i") & date("i")<30){
                $reservationStartTime= strtotime((date("H")).":40");
                if( date("H")==23 ){
                    $reservationEndTime= strtotime((date("H")+1).":10 +1 day");
                } else {
                    $reservationEndTime= strtotime((date("H")+1).":10");
                }
            } else  if ( 30<=date("i") & date("i")<40){
                $reservationStartTime= strtotime((date("H")).":50");
                if( date("H")==23 ) {
                    $reservationEndTime = strtotime((date("H") + 1) . ":20 +1 day");
                } else {
                    $reservationEndTime = strtotime((date("H") + 1) . ":20");
                }
            } else  if ( 40<=date("i") & date("i")<50){
                if( date("H")==23 ){
                    $reservationDay= "내일 ";
                    $reservationStartTime= strtotime((date("H")+1).":00 +1 day");
                    $reservationEndTime= strtotime((date("H")+1).":30 +1 day");
                } else {
                    $reservationStartTime= strtotime((date("H")+1).":00");
                    $reservationEndTime= strtotime((date("H")+1).":30");
                }
            } else  if ( 50<=date("i") & date("i")<60){
                if( date("H")==23 ){
                    $reservationDay= "내일 ";
                    $reservationStartTime = strtotime((date("H") + 1) . ":10 +1 day");
                    $reservationEndTime = strtotime((date("H") + 1) . ":40 +1 day");
                } else {
                    $reservationStartTime = strtotime((date("H") + 1) . ":10");
                    $reservationEndTime = strtotime((date("H") + 1) . ":40");
                }
            }


            $checkStartTime = preg_match("/^(?:[0-9]{2}-(?:0[1-9]|1[0-2])-(?:0[1-9]|[1,2][0-9]|3[0,1])\s(?:[0-1][0-9]|2[0-3])\:([0-5]0))$/", $startTime);
            $checkEndTime = preg_match("/^(?:[0-9]{2}-(?:0[1-9]|1[0-2])-(?:0[1-9]|[1,2][0-9]|3[0,1])(\s?)(?:[0-1][0-9]|2[0-3])\:([0-5]0))$/", $endTime);
            $afterThirtyFromStart = date("y-m-d H:i", strtotime($startTime."+30 minute"));

            if($startTime!=null & $endTime!=null & $checkStartTime==true & $checkEndTime==true & date("y-m-d H:i") < $afterThirtyFromStart & $afterThirtyFromStart <= date("y-m-d H:i", strtotime($endTime))  ){
                $reservationStartTime = strtotime($startTime);
                $reservationEndTime = strtotime($endTime);
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            if( checkReservation($data->id, date("Y-m-d H:i:s", $reservationStartTime), date("Y-m-d H:i:s", $reservationEndTime)) ){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "이미 예약한 시간대입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $useTime = $reservationDay.date("H:i", $reservationStartTime)." - ".date("H:i", $reservationEndTime);
            $res->result->useTime= $useTime;

            if($model != null){
                $carModelString = '\''.implode('\',\'', $model).'\'';
                $res->result->socarzone = printSocarzoneByModel(date("Y-m-d H:i:s", $reservationStartTime), date("Y-m-d H:i:s", $reservationEndTime), $carModelString);
            } else {
                echo isnull;
                $res->result->socarzone = printSocarzone(date("Y-m-d H:i:s", $reservationStartTime), date("Y-m-d H:i:s", $reservationEndTime));
            }

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 4
         * API Name : 쏘카 선택 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "selectSocar":

            $res->result->socarzoneAddress = "무지개마을KCC아파트 102동";
            $res->result->useTime = 30;
            $res->result->startTime=20200215181000;
            $res->result->endTime=20200215221000;
            $res->result->carList[0]->carNo =1;
            $res->result->carList[0]->available ="Y";
            $res->result->carList[0]->profileUrl =null;
            $res->result->carList[0]->model="투싼";
            $res->result->carList[0]->cost =3680;
            $res->result->carList[0]->schedule[0]->otherStartTime ="20200215131000";
            $res->result->carList[0]->schedule[0]->otherEndTime ="20200215151000";
            $res->result->carList[1]->carNo =2;
            $res->result->carList[1]->available ="Y";
            $res->result->carList[1]->profileUrl =null;
            $res->result->carList[1]->model="모닝";
            $res->result->carList[1]->cost =1680;

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 5
         * API Name : 보험 선택 API
         * 마지막 수정 날짜 : 20.02.21
         */
        case "selectInsurance":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $model = $_GET["model"];
            $startTime = $_GET["startTime"];
            $endTime = $_GET["endTime"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $checkStartTime = preg_match("/^(?:[0-9]{2}-(?:0[1-9]|1[0-2])-(?:0[1-9]|[1,2][0-9]|3[0,1])\s(?:[0-1][0-9]|2[0-3])\:([0-5]0))$/", $startTime);
            $checkEndTime = preg_match("/^(?:[0-9]{2}-(?:0[1-9]|1[0-2])-(?:0[1-9]|[1,2][0-9]|3[0,1])(\s?)(?:[0-1][0-9]|2[0-3])\:([0-5]0))$/", $endTime);
            if( $checkStartTime!=true | $checkEndTime!=true){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "조회 실패-기간이 올바르게 입력되지 않았습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $insuranceTime= ceil((strtotime($endTime)-strtotime($startTime))/3600);
            $res->result = selectInsurance($model, $insuranceTime);

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 6
         * API Name : 대여 정보 확인 API
         * 마지막 수정 날짜 : 20.02.22
         */
        case "checkReservationInfo":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $startTime = $_GET["startTime"];
            $endTime = $_GET["endTime"];
            $carNo= $_GET["model"];
            $insurance= $_GET["insurance"];


//            시간 가공, useTime은 sql밖에서 따로 집어넣기?
//            $res->result = checkReservationInfo($startTime, $endTime, $carNo, $insurance);
//            $res->result['safetyOption']= explode(',', $res->result['safetyOption']);
//            $res->result['convenienceOption']= explode(',', $res->result['convenienceOption']);
//            결과값 체크


            $res->result->carNo =1;
            $res->result->model = "올뉴모닝";
            $res->result->fuelType= "휘발유";
            $res->result->safetyOption[0]="에어백";
            $res->result->safetyOption[1]="후방감지센서";
            $res->result->safetyOption[2]="블랙박스";
            $res->result->safetyOption[3]="네비게이션";
            $res->result->convenienceOption[0]="에어컨";
            $res->result->convenienceOption[1]="열선시트";
            $res->result->profileUrl= null;
            $res->result->distanceChargeOne="170원";
            $res->result->distanceChargeTwo="150원";
            $res->result->distanceChargeThree="130원";
            $res->result->useTime="총 30분 이용";
            $res->result->startTime="20200215181000";
            $res->result->endTime="20200215221000";
            $res->result->socarzoneAddress="문화공영주차장";
            $res->result->totalCharge="2,170원";

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 7
         * API Name : 차량 정보 확인 API
         * 마지막 수정 날짜 : 20.02.22
         */
        case "checkCarInfo":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = checkCarInfo($vars["carNo"]);
            $res->result['safetyOption']= explode(',', $res->result['safetyOption']);
            $res->result['convenienceOption']= explode(',', $res->result['convenienceOption']);

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 8
         * API Name : 결제 정보 확인 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "checkPaymentInfo":

            $res->result->rentCharge="2,520원";
            $res->result->insuraceCharge="2,170원";
            $res->result->cardInfo="개인(등록일 2020/02/13)";
            $res->result->totalCharge="2,170원";

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 9
         * API Name : 차량 예약 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "makeReservation":
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "예약 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 10
         * API Name : 가까운 예약 확인 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "checkCloseReservation":

            $res->result->carNo =1;
            $res->result->model = "올뉴모닝";
            $res->result->fuelType= "휘발유";
            $res->result->safetyOption[0]="에어백";
            $res->result->safetyOption[1]="후방감지센서";
            $res->result->safetyOption[2]="블랙박스";
            $res->result->safetyOption[3]="네비게이션";
            $res->result->convenienceOption[0]="에어컨";
            $res->result->convenienceOption[1]="열선시트";
            $res->result->socarzoneAddress="문화공영주차장";
            $res->result->startTime="20200215181000";
            $res->result->endTime="20200215221000";
            $res->result->status="reservation";

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 11-1
         * API Name : 차량 대여 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "changeReservationStatus":
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "대여 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 12
         * API Name : 메뉴 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "printMenu":

            $res->result->userNo=1;
            $res->result->name= "김강혁";
            $res->result->id="abc@abc.com";
            $res->result->level=1;

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 13
         * API Name : 설정 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "printSetup":

            $res->result->userNo=1;
            $res->result->name= "김강혁";
            $res->result->id="abc@abc.com";
            $res->result->phoneNumber="010-0000-0000";
            $res->result->profileUrl=null;

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 14-1
         * API Name : 비밀번호 재설정-아이디 출력 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "printId":
            $res->result->id="abc@abc.com";

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 14-2
         * API Name : 비밀번호 재설정 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "changeUserInfo":
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "변경 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 15
         * API Name : 이용내역 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "printReservationList":

            $res->result[0]->reservationNo =1;
            $res->result[0]->status="reservation";
            $res->result[0]->profileUrl=null;
            $res->result[0]->model = "올뉴모닝";
            $res->result[0]->rentAddress="문화공영주차장";
            $res->result[0]->returnAddress="문화공영주차장";
            $res->result[0]->reservationTime="2/18 (화) 15:00-15:30";
            $res->result[0]->leftTime=="쏘카 이용 2일 21시간 29분 전";

            $res->result[1]->reservationNo =2;
            $res->result[1]->status="returned";
            $res->result[1]->profileUrl=null;
            $res->result[1]->plateNo= "69호9902";
            $res->result[1]->model = "아반떼AD";
            $res->result[1]->rentAddress="테크노프라자 주차장";
            $res->result[1]->returnAddress="테크노프라자 주차장";
            $res->result[1]->reservationTime="4/16 (월) 18:40-19:40";
            $res->result[1]->driveDistance="33km";

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
        * API No. 16
        * API Name : 대여정보 API
        * 마지막 수정 날짜 : 20.02.17
        */
        case "printReservationInfo":

            $res->result->reservationNo =1;
            $res->result->status="reservation";
            $res->result->model = "올뉴모닝";
            $res->result->useTime="2/18 (화) 15:00-02/17 (월) 15:20";
            $res->result->fuelType= "휘발유";
            $res->result->safetyOption[0]="에어백";
            $res->result->safetyOption[1]="후방감지센서";
            $res->result->safetyOption[2]="블랙박스";
            $res->result->safetyOption[3]="네비게이션";
            $res->result->convenienceOption[0]="에어컨";
            $res->result->convenienceOption[1]="열선시트";
            $res->result->distanceCharge="190~140원/km";
            $res->result->rentAddress="문화공영주차장";
            $res->result->returnAddress="문화공영주차장";
            $res->result->insurace="라이트";

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 17
         * API Name : 예약 취소 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "cancelReservation":
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "취소 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 18
         * API Name : 결제내역 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "printPaymentInfo":

            $res->result->reservationNo =1;
            $res->result->status="reservation";
            $res->result->model = "아반떼AD";
            $res->result->totalCharge="980원";
            $res->result->beforeCharge="260원";
            $res->result->rentCharge="130원";
            $res->result->insuranceCharge="130원";
            $res->result->afterCharge="720원";
            $res->result->distanceCharge="190~140원/km";

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
