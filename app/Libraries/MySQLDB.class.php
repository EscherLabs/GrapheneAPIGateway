<?php

namespace App\Libraries;

class MySQLDB {

  private static $mysqli = null;
  private static $databases = [];

    public static function config_database($db_name, $db_config) {
        self::$databases[$db_name] = $db_config;
    }

    public static function connect($db_name) {
        if (is_null(self::$mysqli)) {
            self::$mysqli = new \mysqli(self::$databases[$db_name]->server, 
                                        self::$databases[$db_name]->user, 
                                        self::$databases[$db_name]->pass,
                                        self::$databases[$db_name]->name);
        }
    }

  private static function build_types(&$params) {
    $types = '';
    if (is_array($params)) {
      foreach($params as $key => $param) {        
        if(is_int($param)) {
            $types .= 'i';              //integer
        } elseif (is_float($param)) {
            $types .= 'd';              //double
        } elseif (is_string($param)) {
            $types .= 's';              //string
            //if (strtotime($param)) {
            //  $params[$key] = date('Y-m-d H:i:s', strtotime($param));
            //}
        } else {
            $types .= 'b';              //blob and unknown
        }
      }
    }
    return $types;
  }

  public static function query($query,$params=array()) 
  {
    $types = self::build_types($params);
    if (is_array($params)) {
      array_unshift($params,$types);
    }
    if($stmt = self::$mysqli->prepare($query)) 
    {
      if (sizeof($params)>1) {call_user_func_array(array($stmt,'bind_param'),$params);}
      $stmt->execute(); 
      $meta = $stmt->result_metadata();
      $fields = $results = array();
      while ($field = $meta->fetch_field()) { 
        $var = $field->name; 
        $$var = null; 
        $fields[$var] = &$$var; 
      }
      call_user_func_array(array($stmt,'bind_result'),$fields);
      $id_index = 0;
      while ($stmt->fetch()){ 
        $newfields = array();
        foreach($fields as $key => $value) {
          $newfields[$key] = $value;
        }
        $results[] = $newfields; 
      }
      $stmt->close();
    }  
    if (self::$mysqli->error) {
      throw new Exception(self::$mysqli->error);
    } else {
      return $results;
    }
  }


  public static function update($query,$params=array()) 
  {
    $types = self::build_types($params);
    array_unshift($params,$types);
    if($stmt = self::$mysqli->prepare($query)) 
    {
      if (sizeof($params)>1) {call_user_func_array(array($stmt,'bind_param'),$params);}
      $result = $stmt->execute(); 
      $meta = $stmt->result_metadata();
      $stmt->fetch();
      $stmt->close();
    }  
    if (self::$mysqli->error) {
      throw new Exception(self::$mysqli->error);
    }
  }

  public static function insert($query,$params=array()) 
  {
    $types = self::build_types($params);
    array_unshift($params,$types);
    if($stmt = self::$mysqli->prepare($query)) 
    {
      if (sizeof($params)>1) {$result = call_user_func_array(array($stmt,'bind_param'),$params);}
      $stmt->execute(); 
      $meta = $stmt->result_metadata();
      $stmt->fetch();
      $insert_id = $stmt->insert_id;
      $stmt->close();
    }
    if (self::$mysqli->error) {
      throw new Exception(self::$mysqli->error);
    } else {
      return $insert_id;
    }
  }
}

?>
