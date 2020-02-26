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


//            $idx = $_GET["idx"];
//            test($idx);

//               $test= test();
//            $fcmRes = json_decode(json_encode($test));
//            $fcmTocken= $fcmRes[0]->{'fcmToken'};
//            echo $fcmTocken;
//            echo $fcmRes[0]->{'deviceType'};

            //sendFcm('');

//            //echo $fcmRes;
//            echo $fcmRes[0]->{'fcmTocken'};



            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 1
         * API Name : 회원가입 API
         * 마지막 수정 날짜 : 20.02.24
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

            //면허 유효 끝 시간이 면혀 유효 시작 시간과 현재 시간 이후인지 검사
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

            //비밀번호 암호화, 회원가입.카드등록.면허등록이 합쳐서 transaction 실행 후 체크
            $encryptedPw= password_hash($req->pw, PASSWORD_DEFAULT);
            $transactionCheck = registerAccount($req->name, $req->residentNo, $req->gender, $req->phoneNo, $req->id, $encryptedPw,
                $req->cardNo, $req->cardDate, $req->licenseType, $req->licenseRegion, $req->licenseNo, $req->licenseExpiryDate, $req->licenseDate);
            if( $transactionCheck=='commitComplete') {
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "회원 가입 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "회원 가입 실패";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }




        /* >> 출력에 시간이 걸린대서 우선 100개로 limit
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

            //default 이용 시간(현재 시간+10+a분 ~ 4시간뒤)
            $reservationDay= "오늘 ";
            if ( 0<=date("i") & date("i")<10){
                $reservationStartTime= strtotime((date("H")).":20");
                $reservationEndTime= strtotime((date("H")).":20 +4 hour");
            } else  if ( 10<=date("i") & date("i")<20){
                $reservationStartTime= strtotime((date("H")).":30");
                $reservationEndTime= strtotime((date("H")).":30 +4 hour");
            } else  if ( 20<=date("i") & date("i")<30){
                $reservationStartTime= strtotime((date("H")).":40");
                $reservationEndTime= strtotime((date("H")).":40 +4 hour");
            } else  if ( 30<=date("i") & date("i")<40) {
                $reservationStartTime= strtotime((date("H")).":50");
                $reservationEndTime = strtotime((date("H")) . ":50 +4 hour");
            } else  if ( 40<=date("i") & date("i")<50){
                if( date("H")==23 ) {
                    $reservationDay = "내일 ";
                }
                $reservationStartTime= strtotime((date("H")).":00 +1 hour");
                $reservationEndTime= strtotime((date("H")).":00 +4 hour");
            } else  if ( 50<=date("i") & date("i")<60){
                if( date("H")==23 ) {
                    $reservationDay = "내일 ";
                }
                $reservationStartTime= strtotime((date("H")).":10 +1 hour");
                $reservationEndTime= strtotime((date("H")).":10 +4 hour");
            }

            //시작, 끝 시간이 올바르게 입력됐을 시, 시작 시간이 현재 시간보다 뒤이며 시작,끝 시간이 30분 이상 차이나는지 검사 후 이용 시간으로 설정 + 끝 시간이 시작 시간으로부터 14일 초과 여부 검사
            $checkStartTime = preg_match("/^(?:[0-9]{2}-(?:0[1-9]|1[0-2])-(?:0[1-9]|[1,2][0-9]|3[0,1])\s(?:[0-1][0-9]|2[0-3])\:([0-5]0))$/", $startTime);
            $checkEndTime = preg_match("/^(?:[0-9]{2}-(?:0[1-9]|1[0-2])-(?:0[1-9]|[1,2][0-9]|3[0,1])(\s?)(?:[0-1][0-9]|2[0-3])\:([0-5]0))$/", $endTime);
            $afterThirtyFromStart = date("y-m-d H:i", strtotime($startTime."+30 minute"));
            $afterFourteenFromStart = date("y-m-d H:i", strtotime($startTime."+14 day"));

            $day = array("일","월","화","수","목","금","토");
            if($startTime!=null & $endTime!=null & $checkStartTime==true & $checkEndTime==true & date("y-m-d H:i") <  date("y-m-d H:i", strtotime($startTime))
                & $afterThirtyFromStart <= date("y-m-d H:i", strtotime($endTime)) & date("y-m-d H:i", strtotime($endTime)) < $afterFourteenFromStart ){
                $reservationStartTime = strtotime($startTime);
                $reservationEndTime = strtotime($endTime);
                $reservationDay= date("m/d", strtotime($startTime))."(".$day[date('w', strtotime($startTime))].") ";
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

            //배열로 들어온 model값을 변환해서 mysql의 in으로 전달
            if($model != null){
                $modelArray = explode(',',$model);
                $carModelString = '\''.implode('\',\'', $modelArray).'\'';
                $res->result->socarzone = printSocarzoneByModel(date("Y-m-d H:i:s", $reservationStartTime), date("Y-m-d H:i:s", $reservationEndTime), $carModelString);
            } else {
                $res->result->socarzone = printSocarzone(date("Y-m-d H:i:s", $reservationStartTime), date("Y-m-d H:i:s", $reservationEndTime));
            }

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 4
         * API Name : 쏘카 조회 API
         * 마지막 수정 날짜 : 20.02.25
         */
        case "selectSocar":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $startTime = $_GET["startTime"];
            $endTime = $_GET["endTime"];
            $model = $_GET["model"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result->adress= json_decode(json_encode(printSocarzoneAddress($vars['socarzoneNo'])))->{'address'};

            //default 시간. 3번 api에서 최종 시간 출력한 후 가져오면 삭제해도 됨
            $reservationDay= "오늘 ";
            if ( 0<=date("i") & date("i")<10){
                $reservationStartTime= strtotime((date("H")).":20");
                $reservationEndTime= strtotime((date("H")).":20 +4 hour");
            } else  if ( 10<=date("i") & date("i")<20){
                $reservationStartTime= strtotime((date("H")).":30");
                $reservationEndTime= strtotime((date("H")).":30 +4 hour");
            } else  if ( 20<=date("i") & date("i")<30){
                $reservationStartTime= strtotime((date("H")).":40");
                $reservationEndTime= strtotime((date("H")).":40 +4 hour");
            } else  if ( 30<=date("i") & date("i")<40) {
                $reservationStartTime= strtotime((date("H")).":50");
                $reservationEndTime = strtotime((date("H")) . ":50 +4 hour");
            } else  if ( 40<=date("i") & date("i")<50){
                if( date("H")==23 ) {
                    $reservationDay = "내일 ";
                }
                $reservationStartTime= strtotime((date("H")).":00 +1 hour");
                $reservationEndTime= strtotime((date("H")).":00 +4 hour");
            } else  if ( 50<=date("i") & date("i")<60){
                if( date("H")==23 ) {
                    $reservationDay = "내일 ";
                }
                $reservationStartTime= strtotime((date("H")).":10 +1 hour");
                $reservationEndTime= strtotime((date("H")).":10 +4 hour");
            }

            $checkStartTime = preg_match("/^(?:[0-9]{2}-(?:0[1-9]|1[0-2])-(?:0[1-9]|[1,2][0-9]|3[0,1])\s(?:[0-1][0-9]|2[0-3])\:([0-5]0))$/", $startTime);
            $checkEndTime = preg_match("/^(?:[0-9]{2}-(?:0[1-9]|1[0-2])-(?:0[1-9]|[1,2][0-9]|3[0,1])(\s?)(?:[0-1][0-9]|2[0-3])\:([0-5]0))$/", $endTime);
            $afterThirtyFromStart = date("y-m-d H:i", strtotime($startTime."+30 minute"));
            $afterFourteenFromStart = date("y-m-d H:i", strtotime($startTime."+14 day"));

            $day = array("일","월","화","수","목","금","토");
            if($startTime!=null & $endTime!=null & $checkStartTime==true & $checkEndTime==true & date("y-m-d H:i") <  date("y-m-d H:i", strtotime($startTime))
                & $afterThirtyFromStart <= date("y-m-d H:i", strtotime($endTime)) & date("y-m-d H:i", strtotime($endTime)) < $afterFourteenFromStart ){
//                $reservationStartTime = strtotime($startTime); date로 넣으면 값 이상해짐
//                $reservationEndTime = strtotime($endTime);
                $reservationDay= date("m/d", strtotime($startTime))." (".$day[date('w', strtotime($startTime))].") ";
            }

            $useTime = (strtotime($endTime)-strtotime($startTime))/60;
            $useDay=floor($useTime/1440);
            $useHour=floor(($useTime%1440)/60);
            $useMinute=floor(($useTime%1440)/60);

            if ( $useDay != 0) {
                if ($useHour != 0) {
                    if ($useMinute != 0) {
                        $res->result->useTime = "총 " . $useDay . "일 " . $useHour . "시간 " . $useMinute . "0분 " . "이용";
                    } else {
                        $res->result->useTime = "총 " . $useDay . "일 " . $useHour . "시간 " . "이용";
                    }
                } else {
                    if ($useMinute != 0) {
                        $res->result->useTime = "총 " . $useDay . "일 " . $useMinute . "0분 " . "이용";
                    } else {
                        $res->result->useTime = "총 " . $useDay . "일 " . "이용";
                    }
                }
            } else {
                if ($useHour != 0) {
                    if ($useMinute != 0) {
                        $res->result->useTime = "총 " . $useHour . "시간 " . $useMinute . "0분 " . "이용";
                    } else {
                        $res->result->useTime = "총 " .  $useHour . "시간 " . "이용";
                    }
                } else {
                    $res->result->useTime = "총 " . $useMinute . "0분 " . "이용";
                }
            }

            $res->result->startTime= $reservationDay. date("H:i", strtotime($startTime));
            //date("Y-m-d H:i:s", $reservationStartTime), date("Y-m-d H:i:s", $reservationEndTime);

            $carList =printCars($vars['socarzoneNo']);
            $res->result->carList= $carList;

            $encodedCarList = json_encode($carList);
            $decodedCarList = json_decode($encodedCarList);



            $midnight = date("y-m-d", strtotime($endTime)) . " 00:00:00";
            //이용 시간이 10시간 이하면 요일과 이용 시간으로 요금 계산, 10시간 이상이면 요일과 이용 일수로 요금 계산
            $useTime = (strtotime($endTime) - strtotime($startTime)) / 60;
            if ($useTime < 600) {
                if (floor((strtotime($endTime) - strtotime($startTime)) / 86400) == 0) { // 시작날짜와 끝날짜가 같은지 확인
                    if (date("w", strtotime($startTime)) == 6 | date("w", strtotime($startTime)) == 0) {
                        $chargeCriteria = 'weekendTenMinuteCharge';
                        $finalUseTime = $useTime / 10;
                    } else {
                        $chargeCriteria = 'weekdayTenMinuteCharge';
                        $finalUseTime = $useTime / 10;
                    }
                } else { //자정기준으로 시작날짜 시간과 요금(주말,평일), 끝날짜 시간과 요금(주말,평일) 계산
                    if (date("w", strtotime($startTime)) == 6 | date("w", strtotime($startTime)) == 0) {
                        $chargeCriteria = 'weekendTenMinuteCharge';
                        $firstDayUseTime = (strtotime($midnight) - strtotime($startTime)) / 60;
                    } else {
                        $chargeCriteria = 'weekdayTenMinuteCharge';
                        $firstDayUseTime = (strtotime($midnight) - strtotime($startTime)) / 60;
                    }
                    if (date("w", strtotime($endTime)) == 6 | date("w", strtotime($endTime)) == 0) {
                        $chargeCriteria = 'weekendTenMinuteCharge';
                        $lastDayUseTime = (strtotime($endTime) - strtotime($midnight)) / 60;
                    } else {
                        $chargeCriteria = 'weekdayTenMinuteCharge';
                        $lastDayUseTime = (strtotime($endTime) - strtotime($midnight)) / 60;
                    }
                    $finalUseTime= $firstDayUseTime + $lastDayUseTime;
                }
            } else {
                if (floor((strtotime($endTime) - strtotime($startTime)) / 86400) == 0) {
                    if (date("w", strtotime($startTime)) == 6 | date("w", strtotime($startTime)) == 0) {
                        $chargeCriteria = 'weekendCharge';
                    } else {
                        $chargeCriteria = 'weekdayCharge';
                    }
                    $finalUseTime=1;
                } else { //이틀 이상일 경우 주말, 평일별 일 수와 요금 계산
                    $calDate = floor((strtotime($endTime) - strtotime($startTime)) / 86400) - 1;
                    $startDay = date("w", strtotime($startTime));
                    if ($startDay == 0) {
                        $cntSun = floor(($startDay + $calDate) / 7) + 1;
                    } else {
                        $cntSun = floor(($startDay + $calDate) / 7);
                    }

                    if (($startDay + $calDate) % 7 == 6) {
                        $cntSat = floor(($startDay + $calDate) / 7) + 1;
                    } else {
                        $cntSat = floor(($startDay + $calDate) / 7);
                    }

                    $cntWeekend = $cntSun + $cntSat;
                    $cntWeekday = $calDate + 1 - $cntWeekend;

                    $forForCal = 'Y';//이틀 이상일 경우를 체크하는 변수. 이틀 이상이면 carNo 활용을 위해 기존 코드를 for문 내로 옮김.
                }
            }
            $startMidnight = date("y-m-d", strtotime($startTime)) . " 00:00:00";
            $endMidnight = date("y-m-d", strtotime($endTime. "+1 day")) . " 00:00:00";

            for($i=0; ;$i++) {
                if (!$decodedCarList[$i]->{'carNo'}) {
                    break;
                }
                $carNo = $decodedCarList[$i]->{'carNo'};

                if($forForCal == 'Y'){
                    $totalWeekendCharge = calculationPayment('weekendCharge', $cntWeekend, $carNo);
                    $totalWeekdayCharge = calculationPayment('weekdayCharge', $cntWeekday, $carNo);

                    $encodedTotalWeekendCharge = json_encode($totalWeekendCharge);
                    $decodedTotalWeekendCharge = json_decode($encodedTotalWeekendCharge);
                    $encodedTotalWeekdayCharge = json_encode($totalWeekdayCharge);
                    $decodedTotalWeekdayCharge = json_decode($encodedTotalWeekdayCharge);

                    $chargeCriteria= $decodedTotalWeekendCharge->{'rentCharge'} + $decodedTotalWeekdayCharge->{'rentCharge'};
                    $finalUseTime=1;
                    //$decodedTotalCalculationPayment->rentCharge = $decodedTotalWeekendCharge->{'rentCharge'} + $decodedTotalWeekdayCharge->{'rentCharge'};
                }

                $totalCalculationPayment = calculationPayment($chargeCriteria, $finalUseTime, $carNo);
                $encodedTotalCalculationPayment = json_encode($totalCalculationPayment);
                $decodedTotalCalculationPayment = json_decode($encodedTotalCalculationPayment);

                $res->result->carList[$i]['cost'] = number_format($decodedTotalCalculationPayment->{'rentCharge'}) . "원";

                $checkAvailable = (checkSchedule( date("Y-m-d H:i:s", $reservationStartTime), date("Y-m-d H:i:s", $reservationEndTime), $carNo));
                if($checkAvailable!=null ) {
                    $res->result->carList[$i]['available'] = 'N';
                } else {
                    $res->result->carList[$i]['available'] = 'Y';
                }

                $checkSchedule = (checkSchedule($startMidnight, $endMidnight, $carNo));
                if($checkSchedule!=null ) {
                    $res->result->carList[$i]['schedule'] = $checkSchedule;
                }


            }

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 5
         * API Name : 보험 조회 API
         * 마지막 수정 날짜 : 20.02.21
         */
        case "selectInsurance":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
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
            $res->result = selectInsurance($vars["carNo"], $insuranceTime);

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
//        case "checkReservationInfo":
//
//            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//
//            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
//                $res->isSuccess = FALSE;
//                $res->code = 201;
//                $res->message = "유효하지 않은 토큰입니다";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                addErrorLogs($errorLogs, $res, $req);
//                return;
//            }
//            $startTime = $_GET["startTime"];
//            $endTime = $_GET["endTime"];
//            $carNo= $_GET["model"];
//            $insurance= $_GET["insurance"];


//            +시간 가공, useTime / 결과값 체크
//            $res->result = checkReservationInfo($startTime, $endTime, $carNo, $insurance);
//            $res->result['safetyOption']= explode(',', $res->result['safetyOption']);
//            $res->result['convenienceOption']= explode(',', $res->result['convenienceOption']);

//
//            $res->result->carNo =1;
//            $res->result->model = "올뉴모닝";
//            $res->result->fuelType= "휘발유";
//            $res->result->safetyOption[0]="에어백";
//            $res->result->safetyOption[1]="후방감지센서";
//            $res->result->safetyOption[2]="블랙박스";
//            $res->result->safetyOption[3]="네비게이션";
//            $res->result->convenienceOption[0]="에어컨";
//            $res->result->convenienceOption[1]="열선시트";
//            $res->result->profileUrl= null;
//            $res->result->distanceChargeOne="170원";
//            $res->result->distanceChargeTwo="150원";
//            $res->result->distanceChargeThree="130원";
//            $res->result->useTime="총 30분 이용";
//            $res->result->startTime="20200215181000";
//            $res->result->endTime="20200215221000";
//            $res->result->socarzoneAddress="문화공영주차장";
//            $res->result->totalCharge="2,170원";
//
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "조회 성공";
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            break;


        /*
         * API No. 7
         * API Name : 차량 정보 조회 API
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
         * 마지막 수정 날짜 : 20.02.25
         */
        case "checkPaymentInfo":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $startTime = $_GET["startTime"];
            $endTime = $_GET["endTime"];
            $carNo = $_GET["carNo"];
            $insuranceType = $_GET["insurance"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }


            //4번 api에서 가져오면 삭제해도 됨
            $midnight = date("y-m-d", strtotime($endTime)) . " 00:00:00";
            //이용 시간이 10시간 이하면 요일과 이용 시간으로 요금 계산, 10시간 이상이면 요일과 이용 일수로 요금 계산
            $useTime = (strtotime($endTime) - strtotime($startTime)) / 60;
            if ($useTime < 600) {
                if (floor((strtotime($endTime) - strtotime($startTime)) / 86400) == 0) { // 시작날짜와 끝날짜가 같은지 확인
                    if (date("w", strtotime($startTime)) == 6 | date("w", strtotime($startTime)) == 0) {
                        $chargeCriteria = 'weekendTenMinuteCharge';
                        $finalUseTime = $useTime / 10;
                    } else {
                        $chargeCriteria = 'weekdayTenMinuteCharge';
                        $finalUseTime = $useTime / 10;
                    }
                } else { //자정기준으로 시작날짜 시간과 요금(주말,평일), 끝날짜 시간과 요금(주말,평일) 계산
                    if (date("w", strtotime($startTime)) == 6 | date("w", strtotime($startTime)) == 0) {
                        $chargeCriteria = 'weekendTenMinuteCharge';
                        $firstDayUseTime = (strtotime($midnight) - strtotime($startTime)) / 60;
                    } else {
                        $chargeCriteria = 'weekdayTenMinuteCharge';
                        $firstDayUseTime = (strtotime($midnight) - strtotime($startTime)) / 60;
                    }
                    if (date("w", strtotime($endTime)) == 6 | date("w", strtotime($endTime)) == 0) {
                        $chargeCriteria = 'weekendTenMinuteCharge';
                        $lastDayUseTime = (strtotime($endTime) - strtotime($midnight)) / 60;
                    } else {
                        $chargeCriteria = 'weekdayTenMinuteCharge';
                        $lastDayUseTime = (strtotime($endTime) - strtotime($midnight)) / 60;
                    }
                }
            } else {
                if (floor((strtotime($endTime) - strtotime($startTime)) / 86400) == 0) {
                    if (date("w", strtotime($startTime)) == 6 | date("w", strtotime($startTime)) == 0) {
                        $chargeCriteria = 'weekendCharge';
                    } else {
                        $chargeCriteria = 'weekdayCharge';
                    }
                    $finalUseTime=1;
                } else { //이틀 이상일 경우 주말, 평일별 일 수와 요금 계산
                    $calDate = floor((strtotime($endTime) - strtotime($startTime)) / 86400) - 1;
                    $startDay = date("w", strtotime($startTime));
                    if ($startDay == 0) {
                        $cntSun = floor(($startDay + $calDate) / 7) + 1;
                    } else {
                        $cntSun = floor(($startDay + $calDate) / 7);
                    }

                    if (($startDay + $calDate) % 7 == 6) {
                        $cntSat = floor(($startDay + $calDate) / 7) + 1;
                    } else {
                        $cntSat = floor(($startDay + $calDate) / 7);
                    }

                    $cntWeekend = $cntSun + $cntSat;
                    $cntWeekday = $calDate + 1 - $cntWeekend;

                    $totalWeekendCharge = calculationPayment('weekendCharge', $cntWeekend, $carNo);
                    $totalWeekdayCharge = calculationPayment('weekdayCharge', $cntWeekday, $carNo);

                    $encodedTotalWeekendCharge = json_encode($totalWeekendCharge);
                    $decodedTotalWeekendCharge = json_decode($encodedTotalWeekendCharge);
                    $encodedTotalWeekdayCharge = json_encode($totalWeekdayCharge);
                    $decodedTotalWeekdayCharge = json_decode($encodedTotalWeekdayCharge);

                    $chargeCriteria= $decodedTotalWeekendCharge->{'rentCharge'} + $decodedTotalWeekdayCharge->{'rentCharge'};
                    $finalUseTime=1;

                    //$decodedTotalCalculationPayment->rentCharge = $decodedTotalWeekendCharge->{'rentCharge'} + $decodedTotalWeekdayCharge->{'rentCharge'};
                }
            }
            $totalCalculationPayment = calculationPayment($chargeCriteria, $finalUseTime, $carNo);
            $encodedTotalCalculationPayment = json_encode($totalCalculationPayment);
            $decodedTotalCalculationPayment = json_decode($encodedTotalCalculationPayment);

            $res->result->rentCharge= number_format($decodedTotalCalculationPayment->{'rentCharge'})."원";


            $insuranceTime= ceil($useTime/720); // 보험 요금은 12시간 단위로 적용
            $insurance= $insuranceType."Charge";
            $totalInsuranceCharge = printInsuranceCharge($insurance, $insuranceTime, $carNo);

            $encodedTotalInsuranceCharge = json_encode($totalInsuranceCharge);
            $decodedTotalInsuranceCharge = json_decode($encodedTotalInsuranceCharge);
            $res->result->insuranceCharge= number_format($decodedTotalInsuranceCharge->{'insuranceCharge'})."원";

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $cardInfo = cardInfo($data->id);
            $encodedCardInfo = json_encode($cardInfo);
            $decodedCardInfo = json_decode($encodedCardInfo);

            $res->result->cardInfo= "개인(등록일 ".date("y/m/d", strtotime($decodedCardInfo->{'createdAt'})).")";
            $res->result->totalCharge= number_format($decodedTotalInsuranceCharge->{'insuranceCharge'}+$decodedTotalCalculationPayment->{'rentCharge'})."원";

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 9
         * API Name : 차량 예약 API
         * 마지막 수정 날짜 : 20.02.25
         */
        case "makeReservation":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if($req->reservationAgreementOne!='Y' | $req->reservationAgreementTwo!='Y' | $req->reservationAgreementThree!='Y' | $req->reservationAgreementFour!='Y'){
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "모든 약관에 모두 동의해야 합니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            if( json_decode(json_encode(checkLicenseExpiryDate($data->id)))->{'licenseExpiryDate'} < date("Y-m-d")) {
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "면허를 갱신해야 합니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if( checkReservation($data->id, date("Y-m-d H:i:s", strtotime($req->startTime)), date("Y-m-d H:i:s", strtotime($req->endTime))) ){
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "이미 예약한 시간대입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $startTime= $req->startTime;
            $endTime= $req->endTime;
            $carNo= $req->carNo;

            //이전 api로부터 가지고 있거나, 입력받도록 변경?
            $midnight = date("y-m-d", strtotime($endTime)) . " 00:00:00";
            //이용 시간이 10시간 이하면 요일과 이용 시간으로 요금 계산, 10시간 이상이면 요일과 이용 일수로 요금 계산
            $useTime = (strtotime($endTime) - strtotime($startTime)) / 60;
            if ($useTime < 600) {
                if (floor((strtotime($endTime) - strtotime($startTime)) / 86400) == 0) { // 시작날짜와 끝날짜가 같은지 확인
                    if (date("w", strtotime($startTime)) == 6 | date("w", strtotime($startTime)) == 0) {
                        $chargeCriteria = 'weekendTenMinuteCharge';
                        $finalUseTime = $useTime / 10;
                    } else {
                        $chargeCriteria = 'weekdayTenMinuteCharge';
                        $finalUseTime = $useTime / 10;
                    }
                } else { //자정기준으로 시작날짜 시간과 요금(주말,평일), 끝날짜 시간과 요금(주말,평일) 계산
                    if (date("w", strtotime($startTime)) == 6 | date("w", strtotime($startTime)) == 0) {
                        $chargeCriteria = 'weekendTenMinuteCharge';
                        $firstDayUseTime = (strtotime($midnight) - strtotime($startTime)) / 60;
                    } else {
                        $chargeCriteria = 'weekdayTenMinuteCharge';
                        $firstDayUseTime = (strtotime($midnight) - strtotime($startTime)) / 60;
                    }
                    if (date("w", strtotime($endTime)) == 6 | date("w", strtotime($endTime)) == 0) {
                        $chargeCriteria = 'weekendTenMinuteCharge';
                        $lastDayUseTime = (strtotime($endTime) - strtotime($midnight)) / 60;
                    } else {
                        $chargeCriteria = 'weekdayTenMinuteCharge';
                        $lastDayUseTime = (strtotime($endTime) - strtotime($midnight)) / 60;
                    }
                }
            } else {
                if (floor((strtotime($endTime) - strtotime($startTime)) / 86400) == 0) {
                    if (date("w", strtotime($startTime)) == 6 | date("w", strtotime($startTime)) == 0) {
                        $chargeCriteria = 'weekendCharge';
                    } else {
                        $chargeCriteria = 'weekdayCharge';
                    }
                    $finalUseTime=1;
                } else { //이틀 이상일 경우 주말, 평일별 일 수와 요금 계산
                    $calDate = floor((strtotime($endTime) - strtotime($startTime)) / 86400) - 1;
                    $startDay = date("w", strtotime($startTime));
                    if ($startDay == 0) {
                        $cntSun = floor(($startDay + $calDate) / 7) + 1;
                    } else {
                        $cntSun = floor(($startDay + $calDate) / 7);
                    }

                    if (($startDay + $calDate) % 7 == 6) {
                        $cntSat = floor(($startDay + $calDate) / 7) + 1;
                    } else {
                        $cntSat = floor(($startDay + $calDate) / 7);
                    }

                    $cntWeekend = $cntSun + $cntSat;
                    $cntWeekday = $calDate + 1 - $cntWeekend;

                    $totalWeekendCharge = calculationPayment('weekendCharge', $cntWeekend, $carNo);
                    $totalWeekdayCharge = calculationPayment('weekdayCharge', $cntWeekday, $carNo);

                    $encodedTotalWeekendCharge = json_encode($totalWeekendCharge);
                    $decodedTotalWeekendCharge = json_decode($encodedTotalWeekendCharge);
                    $encodedTotalWeekdayCharge = json_encode($totalWeekdayCharge);
                    $decodedTotalWeekdayCharge = json_decode($encodedTotalWeekdayCharge);

                    $chargeCriteria= $decodedTotalWeekendCharge->{'rentCharge'} + $decodedTotalWeekdayCharge->{'rentCharge'};
                    $finalUseTime=1;

                    //$decodedTotalCalculationPayment->rentCharge = $decodedTotalWeekendCharge->{'rentCharge'} + $decodedTotalWeekdayCharge->{'rentCharge'};
                }
            }
            $totalCalculationPayment = calculationPayment($chargeCriteria, $finalUseTime, $carNo);
            $encodedTotalCalculationPayment = json_encode($totalCalculationPayment);
            $decodedTotalCalculationPayment = json_decode($encodedTotalCalculationPayment);

            $rentCharge= $decodedTotalCalculationPayment->{'rentCharge'};


            $insuranceTime= ceil($useTime/720); // 보험 요금은 12시간 단위로 적용
            $insurance= $req->insurance."Charge";
            $totalInsuranceCharge = printInsuranceCharge($insurance, $insuranceTime, $carNo);

            $encodedTotalInsuranceCharge = json_encode($totalInsuranceCharge);
            $decodedTotalInsuranceCharge = json_decode($encodedTotalInsuranceCharge);
            $insuranceCharge= $decodedTotalInsuranceCharge->{'insuranceCharge'};


            //select 감소 용도
            $userNo= json_decode(json_encode(printUserInfo($data->id)))->{'userNo'};
            $address= json_decode(json_encode(printCarAddress($req->carNo)))->{'address'};

            $transactionCheck= makeReservation($userNo, $req->carNo, date("Y-m-d H:i:s", strtotime($req->startTime)), date("Y-m-d H:i:s", strtotime($req->endTime)), $req->insurance, $rentCharge, $insuranceCharge, $address);

            if( $transactionCheck=='commitComplete') {
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "예약 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "예약 실패";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }


        /*
         * API No. 10
         * API Name : 가까운 예약 확인 API
         * 마지막 수정 날짜 : 20.02.25
         */
        case "checkCloseReservation":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $printRecentReservation = printRecentReservation($data->id);
            if( !$printRecentReservation ){
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "예약이 존재하지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }


            $res->result = $printRecentReservation;
            $res->result['safetyOption']= explode(',', $res->result['safetyOption']);
            $res->result['convenienceOption']= explode(',', $res->result['convenienceOption']);


            $day = array("일","월","화","수","목","금","토");
            if (date("y-m-d")==date("y-m-d", strtotime($res->result['startTime']))){
                $reservationDay= "오늘 ";
            } else if (date("y-m-d")==date("y-m-d", strtotime($res->result['startTime'] ."-1 day"))) {
                $reservationDay= "내일 ";
            } else {
                $reservationDay= date("m/d", strtotime($res->result['startTime']))." (".$day[date('w', strtotime($res->result['startTime']))].") ";
            }
            if (date("y-m-d")==date("y-m-d", strtotime($res->result['endTime']))){
                $reservationEndDay= "오늘 ";
            } else if (date("y-m-d")==date("y-m-d", strtotime($res->result['endTime'] ."-1 day"))) {
                $reservationEndDay= "내일 ";
            } else {
                $reservationEndDay= date("m/d", strtotime($res->result['endTime']))." (".$day[date('w', strtotime($res->result['endTime']))].") ";
            }
            $endTime=$res->result['endTime'];

            $res->result['startTime']= $reservationDay.date("H:i", strtotime($res->result['startTime']))." 부터";
            $res->result['endTime']= $reservationDay.date("H:i", strtotime($res->result['endTime']));


            $status= json_decode(json_encode($printRecentReservation))->{'status'};
            if ($status=='reservation'){
                unset($res->result['licensePlateNo']);
            }

            $useTime = floor((strtotime($endTime)-strtotime(date("Y-m-d H:i:s")))/60);
            $useDay=floor($useTime/1440);
            $useHour=floor(($useTime%1440)/60);
            $useMinute=floor(($useTime%1440)/60);
            if( $status=='rented') {
                if ($useDay != 0) {
                    if ($useHour != 0) {
                        if ($useMinute != 0) {
                            $res->result['useTime'] = "이용시간 " . $useDay . "일 " . $useHour . "시간 " . $useMinute . "분 " . "남음";
                        } else {
                            $res->result['useTime'] = "이용시간 " . $useDay . "일 " . $useHour . "시간 " . "남음";
                        }
                    } else {
                        if ($useMinute != 0) {
                            $res->result['useTime'] = "이용시간 " . $useDay . "일 " . $useMinute . "분 " . "남음";
                        } else {
                            $res->result['useTime'] = "이용시간 " . $useDay . "일 " . "이용";
                        }
                    }
                } else {
                    if ($useHour != 0) {
                        if ($useMinute != 0) {
                            $res->result['useTime'] = "이용시간 " . $useHour . "시간 " . $useMinute . "분 " . "남음";
                        } else {
                            $res->result['useTime'] = "이용시간 " . $useHour . "시간 " . "남음";
                        }
                    } else {
                        $res->result['useTime'] = "이용시간 " . $useMinute . "분 " . "남음";
                    }
                }
            }

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 11
         * API Name : 차량 반납 API
         * 마지막 수정 날짜 : 20.02.26
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
         * 마지막 수정 날짜 : 20.02.26
         */
        case "printMenu":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $res->result = printUserInfo($data->id);
            unset($res->result['phoneNumber']);
            unset($res->result['profileUrl']);

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /* 12번api와 합칠 수 있도록 같은 함수로 구현
         * API No. 13
         * API Name : 설정 API
         * 마지막 수정 날짜 : 20.02.26
         */
        case "printSetup":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $res->result = printUserInfo($data->id);
            unset($res->result['level']);

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;



        /*
         * API No. 14
         * API Name : 비밀번호 재설정 API
         * 마지막 수정 날짜 : 20.02.26
         */
        case "changeUserInfo":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $checkPw = preg_match("/^(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^*()\-_=+\\\|\[\]{};:\'\",.<>\/?])*.{8,45}$/i", $req->newPw);
            if ($checkPw == false) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "비밀번호는 영문, 숫자 포함 8자리 이상 입력해야 합니다.";
                echo json_encode($res);
                return;
            }

            $data = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $pullEncPw= pullEncPw($data->id);
            IF( password_verify($req->newPw, $pullEncPw['encryptedPw'])){
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "기존과 같은 비밀번호입니다.";
                echo json_encode($res);
                return;
            }

            $encryptedPw= password_hash($req->newPw, PASSWORD_DEFAULT);
            changePw($data->id, $encryptedPw);

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
