<?php

header('Content-Type: application/json; charset=utf-8');

require("get_card_sql.php");

echo '{"result": "OK", "card":' . json_encode($card, JSON_UNESCAPED_UNICODE) . '}';


