<?php
session_start();
// счетчик запросов
if (!isset($_SESSION['request_count'])) {
    $_SESSION['request_count'] = 0;
}
$_SESSION['request_count']++;

function checkBrackets(string $string) : bool{
    $stack = [];
    for ($i = 0; $i < strlen($string); $i++) {
        $b = $string[$i];
        if($b == '('){
            $stack[] = $b;
        } else if($b == ')'){
            if(empty($stack)) return false;
            array_pop($stack);
        }
    }
    return empty($stack);
}

function checkString(string $string) : bool{
    if(empty($string)){
        return false;
    }
    return checkBrackets($string);
}
// имя хоста
$hostname = gethostname();

$string = $_POST['string'] ?? '';
if (checkString($string)) {
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Все хорошо. На этот раз. Скобки расставлены верно',
        'string' => $string,
        'backend' => $hostname,
        'session_requests' => $_SESSION['request_count']
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Все очень плохо. Ты дурак? Скобки расставить не можешь..',
        'string' => $string,
        'backend' => $hostname,
        'session_requests' => $_SESSION['request_count']
    ]);
}

