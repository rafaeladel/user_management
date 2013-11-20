<?php
namespace Tsk\UserBundle\Tests\Functional;

use Tsk\UserBundle\Tests\Unit\Entity\ModelTestCase;

class UserControllerTest extends ModelTestCase
{
    public function testGetRegister()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertEquals('Tsk\UserBundle\Controller\UserController::getRegisterAction', $client->getRequest()->attributes->get("_controller"));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(6, $crawler->filter('input[type!="hidden"]')->count());
        $this->assertEquals(1, $crawler->filter("button[type='submit']")->count());
    }

    public function testPostRegister()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $input = array(
            "User[username]"  => "foo",
            "User[plain_password][first]"  => "123456",
            "User[plain_password][confirmation]"  => "123456",
            "User[email]"     => "foo@foo.com",
            "User[firstname]" => "Foo",
            "User[lastname]"  => "Bar",
        );

        $form = $crawler->selectButton('Submit')->form($input);
        $client->submit($form);
        $client->followRedirect();

        $user = $client->getContainer()->get('doctrine')->getRepository('TskUserBundle:User')->findOneByUsername('foo');
        $this->assertEquals('foo@foo.com', $user->getEmail());
        $this->assertTrue('123456' !== $user->getPassword());
        $this->assertEquals("ROLE_USER", $user->getRoles()[0]->getRole());
    }

    public function testPostRegisterError()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $input = array(
            "User[username]"  => "foo",
            "User[plain_password][first]"  => "123456",
            "User[plain_password][confirmation]"  => "1234",
            "User[email]"     => "foo@foo.com",
            "User[firstname]" => "Foo",
            "User[lastname]"  => "Bar",
        );

        $form = $crawler->selectButton('Submit')->form($input);
        $crawler = $client->submit($form);
        $this->assertEquals("The password mush match", $crawler->filter("ul")->text());
    }

    public function testGetLogin()
    {
        $client = static::createClient();
        $crawler = $client->request("GET", "/user/2");
        $crawler= $client->followRedirect();
        $this->assertEquals('Tsk\UserBundle\Controller\UserController::getLoginAction', $client->getRequest()->attributes->get("_controller"));
        $this->assertEquals(4, $crawler->filter('input[type!="hidden"]')->count());
    }

    public function testPostLogin()
    {
        $client = static::createClient();
        $crawler = $client->request("GET", "/login");
        $input = array(
            "_username" => "rafael",
            "_password" => "123456",
        );
        $form = $crawler->selectButton("Login")->form($input);
        $client->submit($form);
        $crawler = $client->followRedirect();
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Tsk\UserBundle\Controller\UserController::profileAction', $client->getRequest()->attributes->get("_controller"));

    }
}

?>