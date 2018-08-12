<?php

namespace App\Libraries;

use \App\Libraries\Router;
use \App\APIUser;

class ValidateUser {

    static private $users = [];

    static private function check_route_regex($username) {
        $users = self::$users;
        $routeisok = false;
        foreach($users[$username]['routes'] as $path) {
            $path = str_replace ('/','\/',$path);
            $path = str_replace ('*','.*',$path);
            $path = '/^'.$path.'$/';
            if (preg_match($path, Router::gen_route()) == 1) {
                $routeisok = true;
                break;
            }
        }
        return $routeisok;
    }

    static private function validate_credentials() {
        $users = self::$users;
        $ipisok = false;
        $routeisok = false;

        /* Check if private route */
        if (isset($_SERVER['PHP_AUTH_USER']) && array_key_exists($_SERVER['PHP_AUTH_USER'],$users) && $users[$_SERVER['PHP_AUTH_USER']]['user']->check_app_secret($_SERVER['PHP_AUTH_PW']) ) {
            // dd('got here!');
            foreach($users[$_SERVER['PHP_AUTH_USER']]['ips'] as $ip) {
                if (substr($_SERVER['REMOTE_ADDR'],0,strlen($ip)) == $ip) {
                    $ipisok = true;
                    break;
                }
            }
            if ($ipisok == true && isset($users[$_SERVER['PHP_AUTH_USER']]['routes'])) {
                $routeisok = self::check_route_regex($_SERVER['PHP_AUTH_USER']);
            }
        } else if (array_key_exists('public',$users)){ /* Check if public route */
            $routeisok = self::check_route_regex('public');
            $ipisok = $routeisok;
        }
        return ($ipisok && $routeisok);
    }

    static public function assert_valid_user($users) {
        self::$users = $users;
        if (!self::validate_credentials()){
            header('WWW-Authenticate: Basic realm="BU REST API"');
            header('HTTP/1.0 401 Unauthorized');
            header("Content-type: application/json");
            echo json_encode(array('error'=>'Unauthorized User'));
            exit();
        } else {
            return true;
        }
    }
}

?>
