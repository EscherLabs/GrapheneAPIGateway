<?php

namespace App\Libraries;

class Router
{
  static private $routes = array();
  static private $PWD = null;

  static public function get_pwd() {
      if (is_null(self::$PWD)) {
        $myPWD = explode ('/',$_SERVER['REQUEST_URI']);
        $PWD = array();
        if (count($myPWD) > 0) {
            foreach($myPWD as $key => $piece) {
                if ($key!=0 || $key!='') {
                $PWD[]=urldecode($piece);
                }
            }
        }
        if (!isset($PWD[0])) {
            $PWD = array('');
        }
        $last = explode('?',$PWD[count($PWD)-1]);
        $PWD[count($PWD)-1] = $last[0];
        self::$PWD = $PWD;
        return $PWD;
    } else {
        return self::$PWD;
    }
}

  static private function fetch_args($path,$help_params) {
    if (isset($help_params['required']) && is_array($help_params['required'])) {
      $required = $help_params['required'];
    } else {
      $required = array();
    }
    if (isset($help_params['optional']) && is_array($help_params['optional'])) {
      $optional = $help_params['optional'];
    } else {
      $optional = array();
    }

    $missing_args = array();
    $args = array();

    parse_str(file_get_contents("php://input"),$_POST);

    foreach($required as $arg_index => $arg_name) {
      if (!(isset($path[$arg_index]) || isset($_GET[$arg_name]) || isset($_POST[$arg_name]))) {
        $missing_args[] = $arg_name;
      } else if (isset($path[$arg_index])) {
        $args[$arg_name] = $path[$arg_index];
      }
    }
    $args = array_merge($_POST,$_GET,$args);

    if (count($missing_args)>0) {
      $error_msg = 'You must provide: ';
      foreach($missing_args as $missing_arg) {
        $error_msg .= $missing_arg.', ';
      }
      $error_msg = rtrim($error_msg,', '); 
      PrintData::JSON(array('error'=>$error_msg));
      exit();
    } else {
      return $args;
    }
  }

  static public function path_to_regex($path) 
  {
    // Change Simple Route Into Searchable Regex
    // /a/b/c/* --> /^\/a\/b\/c\/.*$/
    // /a/b/c --> /^\/a\/b\/c$/
    $path = str_replace ('/','\/',$path);
    $path = str_replace ('*','.*',$path);
    $path = '/^'.$path.'$/';
    return $path;
  }

  static public function add_user($username, $password, $ips, $routes) {
    global $users;
    $users[$username] = [
      'pass'=>$password,
      'ips'=>$ips,
      'routes'=>$routes,
    ];
  }

  static public function add_route($path, $class, $function='_infoText', $helptext=NULL, $helppath=NULL)
  {
    $verb = 'all';
    $base = $path;
    $path = self::path_to_regex($path);
    if ($function == '_infoText' && $helptext == NULL) { $helptext = 'Print This Info Text'; } 
    else if ($helptext == NULL) { $helptext = 'No Help Text Available'; }

    $argsinfo=NULL;
    if (is_array($helppath)) {
      $argsinfo = $helppath;
      $helppath_derived = rtrim($base,'*');
      if (isset($helppath['verb'])) {
        $verb = $helppath['verb'];
        $helptext .= "\n\tHTTP Verb: ".$verb;
      }
      if (isset($helppath['required']) && is_array($helppath['required'])) {
        $helptext .= "\n\tRequired Params: ";
        foreach($helppath['required'] as $index => $arg) {
          $helppath_derived .= '['.$arg.']/';
          $helptext .= $arg.', ';
        }
        $helppath_derived = rtrim($helppath_derived,'/');
        $helptext = rtrim($helptext,', ');
      }
      if (isset($helppath['optional']) && is_array($helppath['optional'])) {
        $helptext .= "\n\tOptional Params: ";
        $helppath_derived.='?';
        foreach($helppath['optional'] as $index => $arg) {
          if (is_string($index)) { $arg = $index; }
          $helppath_derived .= $arg.'=[]&';
          $helptext .= $arg.', ';
        }
        $helptext = rtrim($helptext,', ');
        $helppath_derived = rtrim($helppath_derived,'&');
      }
      $helppath = $helppath_derived;
    }
    self::$routes[$path][$verb] = array('basepath'=>$base, 'class'=>$class, 'function'=>$function, 'helptext' =>$helptext, 'helppath' => $helppath, 'argsinfo'=>$argsinfo);
  }

  static public function print_route_info($thisRoute) {
    header("Content-type: text/plain");
    $ret = false;
    foreach(self::$routes as $route) {
      foreach($route as $verb => $route_info) {
        if (stristr($route_info['basepath'], $thisRoute)) {
          echo ($route_info["helppath"]?$route_info["helppath"]:$route_info["basepath"]) . ":\n\t" . $route_info["helptext"] . "\n\n";
          $ret = true;
        }
      }
    }
    return $ret;
  }

  static public function gen_route()
  {
    $PWD = self::get_pwd();
    $thisRoute = '';
    foreach ($PWD as $dir)
    {
      $thisRoute .= '/'.$dir;
    }
    if ($thisRoute == '') {
      $thisRoute = '/';
    }
    return $thisRoute;
  }

  static private function get_args($path_regex)
  {
    $args = NULL;
    $PWD = self::get_pwd();
    $thing = count(explode('/',$path_regex))-4;
    if ($thing < count($PWD) && stristr($path_regex,'*')!==false )
    {
      $args = array_slice($PWD,$thing);
    }
    if (is_array($args) && $args[count($args)-1]=='') {
      unset($args[count($args)-1]);
    }
    return $args;
  }

  static public function handle_route()
  {
    $PWD = self::get_pwd();
    $thisRoute = self::gen_route($PWD);
    foreach (self::$routes as $path_regex => $path_info)
    {
      if (preg_match($path_regex, $thisRoute) == 1)
      {
        foreach($path_info as $verb => $path_info_info) {
          if($_SERVER['REQUEST_METHOD'] == $verb || $verb == 'all') {
            $routeHandlerClass = new $path_info_info['class'];
            $routeHandlerFunction = $path_info_info['function'];
            ob_start( );
            if (is_null($path_info_info['argsinfo'])) {
              $return = $routeHandlerClass->$routeHandlerFunction(self::get_args($path_regex));
            } else {
              $return = $routeHandlerClass->$routeHandlerFunction(self::fetch_args(self::get_args($path_regex),$path_info_info['argsinfo']));
            }
            $output = ob_get_clean();
            if (strlen($output)) {
              echo $output;
            } else if (isset($return)){
              return $return;
              // PrintData::JSON($return);
            }
            return true;
          }
        }  
      }
    }
    if (self::print_route_info($thisRoute)) {
      return true; 
    } else {
      header("HTTP/1.0 404 Not Found");
      PrintData::JSON(array("error"=>"API call does not exist"));
      return false;
    }
  }

}

?>
