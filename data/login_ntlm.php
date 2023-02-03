<?php // by SiMM
$headers = apache_request_headers();     // получаем все заголовки клиента
if (!isset($headers['Authorization'])) { // если заголовка авторизации нет
  header('HTTP/1.0 401 Unauthorized');   // требуем от клиента авторизации
  header('WWW-Authenticate: NTLM');      // тип требуемой авторизации - NTLM
  exit;                                  // завершаем выполнение скрипта
} else { // заголовок авторизации от клиента пришёл
  if (substr($headers['Authorization'], 0, 5) == 'NTLM ') { // проверяем, что это NTLM-аутентификация
    $chain = base64_decode(substr($headers['Authorization'], 5)); // получаем декодированное значение
    $stage = ord($chain[8]); // номер этапа процесса идентификации
    switch ($stage) {
      case 3: { // этап 3
          $data = array();
          foreach (array('LM_resp', 'NT_resp', 'domain', 'user', 'host') as $k => $v) {
            extract(unpack('vlength/voffset', substr($chain, $k * 8 + 14, 4)));
            $val = substr($chain, $offset, $length);
            // echo "$k "."$v: ".($k<2 ? hex_dump($val) : iconv('UTF-16LE','UTF-8',$val))."<br>\r\n";
            $data[$v] = strtolower(($k < 2 ? hex_dump($val) : iconv('UTF-16LE', 'UTF-8', $val)));
          }
          // var_dump($data);
          auth_user($data["domain"], $data["user"], $data["host"]);
          exit;
        }
      case 1: // этап 1
        if ((ord($chain[13]) == 0x82) || ord($chain[13]) == 0xb2) { // проверяем признак NTLM 0x82 по смещению 13 в сообщении type-1:
          $chain = "NTLMSSP\x00" . // протокол
            "\x02" /* номер этапа */ . "\x00\x00\x00\x00\x00\x00\x00" .
            "\x28\x00" /* общая длина сообщения */ . "\x00\x00" .
            "\x01\x82" /* признак */ . "\x00\x00" .
            "\x00\x02\x02\x02\x00\x00\x00\x00" . // nonce
            "\x00\x00\x00\x00\x00\x00\x00\x00";
          header('HTTP/1.0 401 Unauthorized');
          header('WWW-Authenticate: NTLM ' . base64_encode($chain)); // отправляем сообщение type-2
          exit;
        } else {
          header('Content-Type: application/json; charset=utf-8');
          echo '{"result": "ERROR", "message": "NTLM protocol stage 1. Flag not 0x82, is ' . sprintf("0x%02x", ord($chain[13])) . '", "user": "NO USER", "role" : "none"}';
        }
    }
  } else {
    header('Content-Type: application/json; charset=utf-8');
    echo '{"result": "ERROR", "message": "Your Internet browser or proxy server is not compatible with NTLM", "user": "NO USER", "role" : "none"}';
  }
}

function hex_dump($str)
{ // вспомогательная функция, возвращает шестнадцатеричный дамп строки
  return substr(preg_replace('#.#s', 'sprintf("%02x ",ord("$0"))', $str), 0, -1);
}

function auth_user($domain, $user, $host)
{ // функция авторизации
  $file = file_get_contents('./login_data.json');
  $list = json_decode($file, TRUE);
  $role = "none";
  foreach ($list as $key => $value) {
    if ($user == $key) {
      $role = $value;
      break;
    }
  }
  // $role = 'admin';  
  if ($domain == 'uku' && $role != 'none') {
    session_start();
    $_SESSION['valid'] = true;
    $_SESSION['timeout'] = time();
    $_SESSION['user'] = $user;
    $_SESSION['role'] = $role;
    header('Content-Type: application/json; charset=utf-8');
    echo '{"result": "ОК", "message": "User ' . $domain . '\\\\' . $user . ' has logon", "user": "' . $user . '" , "role": "' . $role . '" , "host": "' . $host . '"}';
  } else {
    session_start();
    unset($_SESSION["valid"]);
    unset($_SESSION["timeout"]);
    unset($_SESSION["user"]);
    unset($_SESSION["role"]);
    header('Content-Type: application/json; charset=utf-8');
    echo '{"result": "ERROR", "message": "User ' . $domain . '\\\\' . $user . ' from host ' . $host . ' can not permission to access", "user": "NO USER" , "role": "none"}';
  }
}
