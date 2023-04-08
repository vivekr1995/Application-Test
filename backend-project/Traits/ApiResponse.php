<?php

namespace Traits;

trait ApiResponse 
{
    
  /**
   * getSuccessResponse
   *
   * @param  mixed $data
   * @param  mixed $status
   * @param  mixed $code
   * @return void
   */
  public function getSuccessResponse($data, $status, $code)
  { 
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode(['success' =>$status,'data' => $data, 'code' => $code], $code);
  }

  
  /**
   * getErrorResponse
   *
   * @param  mixed $message
   * @param  mixed $status
   * @param  mixed $code
   * @return void
   */
  public function getErrorResponse($message,$status, $code)
  {
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode(['success' =>$status,'error' => $message, 'code' => $code], $code);
  }

}