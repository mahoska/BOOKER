<?php 
require_once '../../server/api/sbooker/config.php';
require_once '../../server/api/sbooker/Library/Controller.php';
require_once '../../server/api/sbooker/Library/DbConnection.php';
require_once '../../server/api/sbooker/Library/Model.php';
require_once '../../server/api/sbooker/Model/RoomModel.php';
require_once '../../server/api/sbooker/Controller/Room.php';
require_once 'DbHelper.php';

class RoomTest extends PHPUnit_Framework_TestCase
{
    protected $objRoomController;
    
    protected $helper;

    protected function setUp()
    {
        $this->objRoomController = new Room();
        $this->helper = new DbHelper();
    }

    protected function tearDown()
    {
        $this->objRoomController = NULL;
        $this->helper = NULL;
    }

    public function testgetAllRoom()
    {
        $this->helper->executeQuery('INSERT INTO roomBooker (name) VALUES ("testRoom")');
        $roomId = $this->helper->pdo->lastInsertId();
        $res = $this->objRoomController->getAllRoom();
        $this->assertInternalType('array', $res);
        var_dump($res);
       // $this->assertTrue(count($res));
      //  $this->assertInternalType('array', $res['data']);
      //  $this->assertTrue(count($res['data']));
        $this->helper->executeQuery('DELETE FROM roomBooker WHERE id = '.$roomId);
         
        //$this->assertTrue(true);
    }
}

