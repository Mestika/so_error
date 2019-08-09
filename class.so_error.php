<?php

class SO_Error {

  public $errors = array();
  public $error_data = array();

  public function __construct( $err='' , $soft=true) {
    if ( !isset($err['code'])&&empty($err['code'])&&empty($err)  )
      return;

    if(is_string($err)&&!empty($err)){
      $err = ['code' => $err];
    }

    $args = so_parse_args( $err , [
      'code' => '',
      'msg' => '',
      'data' => '',
    ]);

    $code = $args['code'];

    $this->errors[$code]['type'] = ($soft) ? 'soft' : 'hard';
    $this->errors[$code]['messages'][] = $args['msg'];

    $callee = debug_backtrace()[1]['function'];
    if($callee){
      $this->errors[$code]['callee'] = $callee;
    }

    if ( ! empty($args['data']) )
      $this->error_data[$code] = $args['data'];
  }

  public function get_error_codes() {
    if ( empty($this->errors) )
      return array();

    return array_keys($this->errors);
  }

  public function get_error_code() {
    $codes = $this->get_error_codes();
    if ( empty($codes) )
      return '';
    return $codes[0];
  }

  public function get_error_messages($code = '') {
    // Return all messages if no code specified.
    if ( empty($code) ) {
      $all_messages = array();

      foreach ( (array) $this->errors as $code['messages'] => $messages )
        $all_messages = array_merge($all_messages, $messages);
      return $all_messages;
    }

    if ( isset($this->errors[$code]['messages']) )
      return $this->errors[$code]['messages'];
    else
      return array();
  }

  public function get_error_message($code = '') {
    if ( empty($code) )
      $code = $this->get_error_code();

    $messages = $this->get_error_messages($code);

    if ( empty($messages) )
      return '';
    return $messages[0];
  }

  public function get_error_data($code = '') {
    if ( empty($code) )
      $code = $this->get_error_code();

    if ( isset($this->error_data[$code]) )
      return $this->error_data[$code];
  }

  public function add($err='', $soft=true) {
    if ( !isset($err['code'])&&empty($err['code'])&&empty($err)  )
      return;

    if(is_string($err)&&!empty($err)){
      $err = ['code' => $err];
    }

    $args = so_parse_args( $err , [
      'code' => '',
      'msg' => '',
      'data' => '',
    ]);

    $code = $args['code'];

    $this->errors[$code]['type'] = ($soft) ? 'soft' : 'hard';
    $this->errors[$code]['msg'][] = $args['msg'];

    if ( ! empty($args['data']) )
      $this->error_data[$code] = $args['data'];
  }

  public function add_data($data, $code = '') {
    if ( empty($code) )
      $code = $this->get_error_code();

    $this->error_data[$code] = $data;
  }

  public function remove( $code ) {
    unset( $this->errors[ $code ] );
    unset( $this->error_data[ $code ] );
  }
}

if(!function_exists("is_so_error")){
  function is_so_error( $thing ) {
    return ( $thing instanceof SO_Error );
  }
}
