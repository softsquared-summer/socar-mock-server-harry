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
         * API No. 0
         * API Name : 테스트 API
         * 마지막 수정 날짜 : 20.02.14
         */

        case "register":
            echo "{
                    \"isSuccess\": true,
                    \"code\": 100,
                    \"message\": \"회원 가입 성공\"
                }";

            break;

        case "printSocarzone":
            echo "{
                    \"result\": {
                        \"useTime\": \"오늘 18:10 - 22:10\",
                        \"socarzone\": [
                        {
                            \"socarzoneNo\" : 1,
                            \"carCount\": 3,
                            \"latitude\": 36.6276675,
                            \"longitude\": \"127.455393899999\",
                        },
                        {
                            \"socarzoneNo\" : 2,
                            \"carCount\": 0,
                            \"latitude\": 36.6276675,
                            \"longitude\": \"127.455393899999\",
                        }
                    ],
                    \"isSuccess\": true,
                    \"code\": 100,
                    \"message\": \"조회 성공\"
                }
            }";
            break;

        case "selectSocar":
            echo "{
                \"result\": {
                    \"socarzoneAddress\": \"무지개마을KCC아파트 102동\",
                    \"useTime\": 30,
                    \"startTime\": \"20200215181000\",
                    \"endTime\": \"20200215221000\",
                    \"availableCar\": [
                        {
                            \"carNo\": 1,
                            \"profileUrl\": null,
                            \"model\": \"투싼\",
                            \"cost\": 3680,
                            \"schedule\": [
                                  {
                                       \"otherStartTime\": \"20200215131000\",
                                       \"otherEndTime\": \"20200215151000\"
                                  }
                            ]
            
                        },
                        {
                            \"carNo\": 2,
                            \"profileUrl\": null,
                            \"model\": \"모닝\",
                            \"cost\": 1680
                        },
                    ]
                },
                \"isSuccess\": true,
                \"code\": 100,
                \"message\": \"조회 성공\"
            }";
            break;

        case "selectInsurance":
            echo "{
                \"result\": {
                    \"specialCost\": \"8,410원\",
                    \"standardCost\": \"5,410원\",
                    \"lightCost\": \"3,790원\"        
                },
                \"isSuccess\": true,
                \"code\": 100,
                \"message\": \"조회 성공\"
            }";
            break;

        case "checkReservationInfo":
            echo "{
                    {
                        \"result\": {
                            \"carNo\": 1, 
                            \"model\": \"올뉴모닝\",
                            \"fuelType\": \"휘발유\",
                            \"safetyOption\": [
                                 \"에어백\",
                                 \"후방감지센서\",
                                 \"블랙박스\",
                                 \"네비게이션\"
                            ],
                            \"convenienceOption\": [
                                 \"에어컨\",
                                 \"열선시트\"
                            ],
                            \"profileUrl\": null,
                            \"distanceChargeOne\": \"170원\",
                            \"distanceChargeTwo\": \"150원\",
                            \"distanceChargeThree\": \"130원\",
                            \"insurance\": \"라이트\",
                            \"useTime\": \"총 30분 이용\",
                            \"startTime\": \"20200215181000\",
                            \"endTime\": \"20200215221000\",
                            \"socarzoneAddress\": \"문화공영주차장\",
                            \"totalCharge\": \"2,170원\"
                         }
                    },
                    \"isSuccess\": true,
                    \"code\": 100,
                    \"message\": \"조회 성공\"
                }";
            break;

        case "checkCarInfo":
            echo "{
                    {
                        \"result\": {
                            \"profileUrl\": null, 
                            \"model\": \"아반떼AD\",
                            \"manufacture\": \"현대자동차\",
                            \"type\": \"준중형\",
                            \"fuelType\": \"휘발유\",
                            \"shiftType\": \"자동 6단\",
                            \"ridingLimit\": 5,
                            \"safetyOption\": [
                                 \"에어백\",
                                 \"후방감지센서\",
                                 \"블랙박스\",
                                 \"네비게이션\"
                            ],
                            \"convenienceOption\": [
                                 \"에어컨\",
                                 \"열선시트\"
                            ],
                            \"totalCharge\": \"4,750원(30분)\",
                            \"distanceCharge\": \"190~140원/km\"
                         }
                    },
                    \"isSuccess\": true,
                    \"code\": 100,
                    \"message\": \"조회 성공\"
                }";
            break;

        case "checkPaymentInfo":
            echo "{
                \"result\": {
                    \"rentCharge\": \"2,520원\",
                    \"insuraceCharge\": \"2,170원\",
                    \"cardInfo\": \"개인(등록일 2020/02/13)\",
                    \"totalCharge\": \"2,170원\"    
                },
                \"isSuccess\": true,
                \"code\": 100,
                \"message\": \"조회 성공\"
            }";
            break;

        case "makeReservation":
            echo " {
                \"isSuccess\": true,
                \"code\": 100,
                \"message\": \"예약 성공\"
            }";
            break;

        case "checkCloseReservation":
            echo "{
                    {
                        \"result\": {
                            \"carNo\": 1,
                            \"model\": \"올뉴모닝\",
                            \"fuelType\": \"휘발유\",
                            \"safetyOption\": [
                                 \"에어백\",
                                 \"후방감지센서\",
                                 \"블랙박스\",
                                 \"네비게이션\"
                            ],
                            \"convenienceOption\": [
                                 \"에어컨\",
                                 \"열선시트\"
                            ],
                            \"socarzoneAddress\": \"문화공영주차장\",
                            \"startTime\": \"20200215181000\",
                            \"endTime\": \"20200215221000\",
                            \"status\": \"reservation\"
                         }
                    },
                    \"isSuccess\": true,
                    \"code\": 100,
                    \"message\": \"조회 성공\"
                }";
            break;

        case "changeReservationStatus":
            echo "{
                \"isSuccess\": true,
                \"code\": 100,
                \"message\": \"대여 성공\"
            }";
            break;

        case "printMenu":
            echo "{
                \"result\": {
                    \"userNo\": 1,
                    \"name\": \"김강혁\",
                    \"id\": \"abc@abc.com\",
                    \"level\": 1 
                },
                \"isSuccess\": true,
                \"code\": 100,
                \"message\": \"조회 성공\"
            }";
            break;

        case "printSetup":
            echo "{
                \"result\": {
                    \"userNo\": 1,
                    \"name\": \"김강혁\",
                    \"id\": \"abc@abc.com\",
                    \"phoneNumber\": \"010-0000-0000\",
                    \"profileUrl\": 
                },
                \"isSuccess\": true,
                \"code\": 100,
                \"message\": \"조회 성공\"
            }";
            break;

        case "printId":
            echo "{
                \"isSuccess\": true,
                \"code\": 100,
                \"message\": \"조회 성공\"
            }";
            break;

        case "changeUserInfo":
            echo "{
                \"isSuccess\": true,
                \"code\": 100,
                \"message\": \"변경 성공\"
            }";
            break;

        case "printReservationList":
            echo "{
                    \"result\": [
                       {
                           \"reservationNo\": 1,
                           \"status\": \"reservation\",
                           \"profileUrl\": null,
                           \"model\": \"올뉴모닝\",
                           \"rentAddress\": \"문화공영주차장\",
                           \"returnAddress\": \"문화공영주차장\",
                           \"reservationTime\": \"2/18 (화) 15:00-15:30\",
                           \"leftTime\": \"쏘카 이용 2일 21시간 29분 전\"
                       },
                       {
                           \"reservationNo\": 2,
                           \"status\": \"returned\",
                           \"profileUrl\": null,
                           \"plateNo\": \"69호9902\",
                           \"model\": \"아반떼AD\",
                           \"rentAddress\": \"테크노프라자 주차장\",
                           \"returnAddress\": \"테크노프라자 주차장\",
                           \"reservationTime\": \"4/16 (월) 18:40-19:40\",
                           \"driveDistance\": \"33km\"
                       }
                    ],
                    \"isSuccess\": true,
                    \"code\": 100,
                    \"message\": \"조회 성공\"
                }";
            break;

        case "printReservationInfo":
            echo "{
                    \"result\": {
                           \"reservationNo\": 1,
                           \"status\": \"reservation\",
                           \"model\": \"올뉴모닝\",
                           \"useTime\": \"2/18 (화) 15:00-02/17 (월) 15:20\",
                           \"fuelType\": \"휘발유\",
                            \"safetyOption\": [
                                 \"에어백\",
                                 \"후방감지센서\",
                                 \"블랙박스\",
                                 \"네비게이션\"
                            ],
                            \"convenienceOption\": [
                                 \"에어컨\",
                                 \"열선시트\"
                            ],
                           \"distanceCharge\": \"190~140원/km\",
                           \"rentAddress\": \"문화공영주차장\",
                           \"returnAddress\": \"문화공영주차장\",
                           \"insurace\": \"라이트\"
                       },
                    \"isSuccess\": true,
                    \"code\": 100,
                    \"message\": \"조회 성공\"
                }";
            break;

        case "cancelReservation":
            echo "{
                    \"isSuccess\": true,
                    \"code\": 100,
                    \"message\": \"취소 성공\"
                }";
            break;

        case "printPaymentInfo":
            echo "{
                \"result\": {
                       \"reservationNo\": 1,
                       \"status\": \"reservation\",
                       \"model\": \"아반떼 AD\",
                       \"totalCharge\": \"980원\",
                       \"beforeCharge\": \"260원\",
                       \"rentCharge\": \"130원\",
                       \"insuranceCharge\": \"130원\",
                       \"afterCharge\": \"720원\",
                       \"distanceCharge\": \"190~140원/km\"
                   },
                \"isSuccess\": true,
                \"code\": 100,
                \"message\": \"조회 성공\"
            }";
            break;


    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
