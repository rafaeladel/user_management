<?php
namespace Tsk\UserBundle\Tests\Unit\Entity;

use Tsk\UserBundle\Entity\Role;
use Tsk\UserBundle\Entity\User;

class UserTest extends ModelTestCase
{
    public function testUserAddition()
    {
        //User is added by a Data Fixture.

        $container = static::createClient()->getContainer();
        $em = $container->get('doctrine')->getManager();

        $user = $em->getRepository("TskUserBundle:User")->findOneByUsername('rafael');

        $this->assertEquals("rafael", $user->getUsername());
        $this->assertTrue("123456" !== $user->getPassword());
        $this->assertEquals("ROLE_USER", $user->getRoles()[0]->getRole());
        $this->assertTrue($user->getIsActive());
    }
}
?>