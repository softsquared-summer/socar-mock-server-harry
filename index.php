<?php
require './pdos/DatabasePdo.php';
require './pdos/IndexPdo.php';
require './vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

date_default_timezone_set('Asia/Seoul');
ini_set('default_charset', 'utf8mb4');

//에러출력하게 하는 코드
//error_reporting(E_ALL); ini_set("display_errors", 1);

//Main Server API
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    /* ******************   Test   ****************** */

    $r->addRoute('GET', '/', ['IndexController', 'index']);
    $r->addRoute('GET', '/test', ['IndexController', 'test']);

    $r->addRoute('POST', '/user', ['IndexController', 'register']);
    $r->addRoute('POST', '/jwt', ['MainController', 'createJwt']);
    $r->addRoute('GET', '/socarzones', ['IndexController', 'printSocarzone']);
    $r->addRoute('GET', '/socarzone/{socarzoneNo}/cars', ['IndexController', 'selectSocar']);
    $r->addRoute('GET', '/car/{carNo}/insurances', ['IndexController', 'selectInsurance']);
    //$r->addRoute('GET', '/reservation', ['IndexController', 'checkReservationInfo']);
    $r->addRoute('GET', '/car/{carNo}', ['IndexController', 'checkCarInfo']);
    $r->addRoute('GET', '/calculation', ['IndexController', 'checkPaymentInfo']);
    $r->addRoute('POST', '/reservation', ['IndexController', 'makeReservation']);
    $r->addRoute('GET', '/reservation/recent', ['IndexController', 'checkCloseReservation']);
    $r->addRoute('PATCH', '/reservation', ['IndexController', 'changeReservationStatus']);
    $r->addRoute('GET', '/menu', ['IndexController', 'printMenu']);
    $r->addRoute('GET', '/setup', ['IndexController', 'printSetup']);
    $r->addRoute('GET', '/user/id', ['IndexController', 'printId']);
    $r->addRoute('PATCH', '/user', ['IndexController', 'changeUserInfo']);
    $r->addRoute('GET', '/reservation/all', ['IndexController', 'printReservationList']);
    $r->addRoute('GET', '/reservation/{reservationNo}', ['IndexController', 'printReservationInfo']);
    $r->addRoute('DELETE', '/reservation/{reservationNo}', ['IndexController', 'cancelReservation']);
    $r->addRoute('GET', '/payment/{reservationNo}', ['IndexController', 'printPaymentInfo']);
    $r->addRoute('DELETE', '/user', ['IndexController', 'withdrawal']);
    $r->addRoute('PATCH', '/reservation/{reservationNo}/fellowPassenger', ['IndexController', 'addFellowPassenger']);




//    $r->addRoute('GET', '/users', 'get_all_users_handler');
//    // {id} must be a number (\d+)
//    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//    // The /{title} suffix is optional
//    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 로거 채널 생성
$accessLogs = new Logger('ACCESS_LOGS');
$errorLogs = new Logger('ERROR_LOGS');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// add records to the log
//$log->addInfo('Info log');
// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
//$log->addDebug('Debug log');
//$log->addError('Error log');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($routeInfo[1][0]) {
            case 'IndexController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/IndexController.php';
                break;
            case 'MainController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MainController.php';
                break;
            /*case 'EventController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/EventController.php';
                break;
            case 'ProductController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ProductController.php';
                break;
            case 'SearchController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/SearchController.php';
                break;
            case 'ReviewController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ReviewController.php';
                break;
            case 'ElementController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ElementController.php';
                break;
            case 'AskFAQController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/AskFAQController.php';
                break;*/
        }

        break;
}
