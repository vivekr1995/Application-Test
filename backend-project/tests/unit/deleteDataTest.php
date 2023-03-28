<?php

/**
 * deleteDataTest
 */
class deleteDataTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
        
    /**
     * _before
     *
     * @return void
     */
    protected function _before()
    {
    }
    
    /**
     * _after
     *
     * @return void
     */
    protected function _after()
    {
    }
    
    /**
     * testDeleteTest
     *
     * @return void
     */
    public function testDeleteTest()
    {

        $userControllerObject = $this->createMock(UserController::class);
        $userControllerObject->method("deleteUserData")->willReturn(true);

        $userControllerObject
        ->expects($this->once())
        ->method("deleteUserData")
        ->with(5);

        $result = $userControllerObject->deleteUserData(5);

        $this->assertTrue($result);
    }
   
    /**
     * testDeleteFail
     * If 0 is passed to deleteUserData funtion it will 
     * Delete user data from csv file
     * @return void
     */
    public function testDeleteFail()
    {
        $userControllerObject = $this->createMock(UserController::class);
        $userControllerObject->method("deleteUserData")->willReturn(false);

        $userControllerObject
        ->expects($this->once())
        ->method("deleteUserData")
        ->with(0);

        $result = $userControllerObject->deleteUserData(0);

        $this->assertFalse($result);
    }
}