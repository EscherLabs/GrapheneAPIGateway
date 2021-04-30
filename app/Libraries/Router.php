<?php
namespace App\Libraries;

class Router {
    static private $routes = [];

    static public function get_pwd() {
        $PWD = explode('/', $_SERVER['REQUEST_URI']);
        unset($PWD[0]);
        $PWD = array_values($PWD);
        foreach ($PWD as $key => $piece) {
            $PWD[$key] = urldecode($piece);
        }
        $last = $PWD[count($PWD) - 1];
        $PWD[count($PWD) - 1] = substr($last, 0, strpos($last, '?') !== false ? strpos($last, '?') : strlen($last));
        return $PWD;
    }

    static private function fetch_args($path, $help_params) {
        $PWD = self::get_pwd();
        $args = array_slice($PWD, count(explode('/', $path)) - 1);
        if (count($args) > 0 && $args[count($args) - 1] === '') {
            unset($args[count($args) - 1]);
        }
        $path = $args;

        if (isset($help_params['required']) && is_array($help_params['required'])){
            $required = $help_params['required'];
        } else {
            $required = [];
        }

        $missing_args = [];
        $args = [];
        parse_str(file_get_contents("php://input") , $_POST);

        foreach ($required as $arg_index => $arg_name) {
            if (!(isset($path[$arg_index]) || isset($_GET[$arg_name]) || isset($_POST[$arg_name]))) {
                $missing_args[] = $arg_name;
            } else if (isset($path[$arg_index])) {
                $args[$arg_name] = $path[$arg_index];
            }
        }
        $args = array_merge($_POST, $_GET, $args);

        if (count($missing_args) > 0) {
            return response(json_encode(['error' => 'You must provide: ' . implode(',', $missing_args) ]) , 400)->header('Content-type', 'application/json');
        } else {
            return $args;
        }
    }

    static public function add_route($path, $class, $function, $argsinfo, $verb) {
        self::$routes[$path][$verb] = ['basepath' => $path, 'class' => $class, 'function' => $function, 'helppath' => '', 'argsinfo' => $argsinfo];
    }

    static public function handle_route() {
        $PWD = self::get_pwd();
        $thisRoute = '/' . implode('/', $PWD);
        $wrong_verb = false;
        foreach (self::$routes as $path => $path_info) {
            /* Match Path (Must either end or have a slash) */
            $path_regex = '/^' . str_replace('/', '\/', $path) . '(\/|\z)/';
            if (preg_match($path_regex, $thisRoute) == 1) {
                foreach ($path_info as $verb => $path_info_info) {
                    if ($_SERVER['REQUEST_METHOD'] == $verb || $verb == 'ALL') {
                        $args = self::fetch_args($path, $path_info_info['argsinfo']);
                        if (!is_array($args)) { /* Got an Error */
                            return $args;
                        } else {
                            config(['app.args' => $args]);
                        }
                        $routeHandlerFunction = $path_info_info['function'];
                        ob_start();
                        $routeHandlerClass = new $path_info_info['class']();
                        $return = $routeHandlerClass->$routeHandlerFunction();
                        $output = ob_get_clean();
                        if (is_null($return) && strlen($output)) {
                            // Nothing was returned from the parent function, and there was stdout text
                            return response($output, 200)->header('Content-type', 'text/plain');
                        } else if (!is_null($return)) {
                            return $return;
                        }
                        return true;
                    } else {
                        $wrong_verb = true;
                    }
                }
            }
        }
        if ($wrong_verb) {
            return response(json_encode(['error' => $_SERVER['REQUEST_METHOD'] . ' Method not allowed']), 405)->header('Content-type', 'application/json');
        }
        return response(json_encode(['error' => 'API call does not exist']), 404)
            ->header('Content-type', 'application/json');
    }

}