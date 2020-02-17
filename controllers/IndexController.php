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
            echo "API Server";
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
         * API No. 1
         * API Name : 회원가입 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "register":
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "회원 가입 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 3
         * API Name : 쏘카존 출력 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "printSocarzone":

            $res->result->useTime = "오늘 18:10 - 22:10";
            $res->result->socarzone[0]->socarzoneNo = 1;
            $res->result->socarzone[0]->carCount = 3;
            $res->result->socarzone[0]->latitude = 36.6276675;
            $res->result->socarzone[0]->longitude = 127.455393899999;
            $res->result->socarzone[1]->socarzoneNo = 2;
            $res->result->socarzone[1]->carCount = 0;
            $res->result->socarzone[1]->latitude = 36.6276675;
            $res->result->socarzone[1]->longitude = 127.455393899999;

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
            $res->result->availableCar[0]->carNo =1;
            $res->result->availableCar[0]->profileUrl =null;
            $res->result->availableCar[0]->model="투싼";
            $res->result->availableCar[0]->cost =3680;
            $res->result->availableCar[0]->schedule[0]->otherStartTime ="20200215131000";
            $res->result->availableCar[0]->schedule[0]->otherEndTime ="20200215151000";
            $res->result->availableCar[1]->carNo =2;
            $res->result->availableCar[1]->profileUrl =null;
            $res->result->availableCar[1]->model="모닝";
            $res->result->availableCar[1]->cost =1680;

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 5
         * API Name : 보험 선택 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "selectInsurance":

            $res->result->specialCost="8,410원";
            $res->result->standardCost="5,410원";
            $res->result->lightCost="3,790원";

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 6
         * API Name : 대여 정보 확인 API
         * 마지막 수정 날짜 : 20.02.17
         */
        case "checkReservationInfo":

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
         * 마지막 수정 날짜 : 20.02.17
         */
        case "checkCarInfo":

            $res->result->carNo =1;
            $res->result->profileUrl= null;
            $res->result->model = "아반떼AD";
            $res->result->manufacture="현대자동차";
            $res->result->type="준중형";
            $res->result->fuelType= "휘발유";
            $res->result->shiftType="자동 6단";
            $res->result->ridingLimit= 5;
            $res->result->safetyOption[0]="에어백";
            $res->result->safetyOption[1]="후방감지센서";
            $res->result->safetyOption[2]="블랙박스";
            $res->result->safetyOption[3]="네비게이션";
            $res->result->convenienceOption[0]="에어컨";
            $res->result->convenienceOption[1]="열선시트";
            $res->result->totalCharge="4,750원(30분";
            $res->result->distanceCharge="190~140원/km";

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
