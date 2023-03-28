<?php

namespace Traits;

trait ApiResponse 
{
    
  /**
   * successResponse
   *
   * @param  mixed $data
   * @param  mixed $status
   * @param  mixed $code
   * @return void
   */
  public function successResponse($data, $status, $code)
  { 
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode(['success' =>$status,'data' => $data], $code);
  }

  
  /**
   * errorResponse
   *
   * @param  mixed $message
   * @param  mixed $status
   * @param  mixed $code
   * @return void
   */
  public function errorResponse($message,$status, $code)
  {
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode(['success' =>$status,'error' => $message, 'code' => $code], $code);
  }

}