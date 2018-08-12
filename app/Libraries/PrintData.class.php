<?php

namespace App\Libraries;

class PrintData {

  public static function JSON($data,$timeout=0) {
    header("Content-type: application/json");
    $output = json_encode($data);

    if ($output === false) {
      switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo '{"error":"No errors"}';
        break;
	case JSON_ERROR_DEPTH:
            echo '{"error":"Maximum stack depth exceeded"}';
        break;
	case JSON_ERROR_STATE_MISMATCH:
            echo '{"error":"Underflow or the modes mismatch"}';
        break;
	case JSON_ERROR_CTRL_CHAR:
            echo '{"error":"Unexpected control character found"}';
        break;
	case JSON_ERROR_SYNTAX:
            echo '{"error":"Syntax error, malformed JSON"}';
        break;
	case JSON_ERROR_UTF8:
            echo '{"error":"Malformed UTF-8 characters, possibly incorrectly encoded"}';
        break;
	default:
            echo '{"error":"Unknown error"}';
        break;
      }
    } else {
      echo $output;
    }
  }
  public static function JSONP($data,$callback) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST'); 
    header("content-type: application/json; charset=utf-8");
    echo $callback . '('.json_encode($data).')';
  }

}

?>
