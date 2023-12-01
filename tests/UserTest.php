<?php

/* TEST THE CODE -> DB CONNECTION AND RESTAPI */
class UserTest extends \PHPUnit\Framework\TestCase
{
    public function testDBonnect()
    {
        $database = new App\Database("localhost", "skydatasystems", "root", "");
        $gateway = new App\UserGateway($database);
        $result = $gateway->getAll();
        $this->assertNotEmpty($result);
    }

    public function testDBErrorConnect()
    {
        $database = new App\Database("localhost", "skydatasystems", "root", "WRONGPW");
        $gateway = new App\UserGateway($database);
        /* $result = $gateway->getAll();
        $this->assertEmpty($result); */
    }

    public function testLogin()
    {
        $database = new App\Database("localhost", "skydatasystems", "root", "");
        $gateway = new App\UserGateway($database);
        $result = $gateway->login("t.gee@hotmail.de");
        $this->assertNotEmpty($result);
    }

    /*  public function testCreate()
    {
        $database = new App\Database("localhost", "skydatasystems", "root", "");
        $gateway = new App\UserGateway($database);
        $arr = array("email"=>"t.gee@hotmail.dea", "name" =>"Tom","passwort" =>"Tom","telefon" =>"1231231231",
        "place" =>"berlin","postcode" =>"12207","terms" =>true );
        fwrite(STDERR, print_r($arr, TRUE));
        $result = $gateway->create($arr);
        $this->assertNotEmpty($result);
    } */


    /*  public function testDelete()
    {
        $database = new App\Database("localhost", "skydatasystems", "root", "");
        $gateway = new App\UserGateway($database);
        $result = $gateway->delete(200);
        $this->assertNotEmpty($result);
    } */
}
