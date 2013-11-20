<?php
namespace Tsk\UserBundle\Tests\Unit\Repository;

use Tsk\UserBundle\Entity\Role;
use Tsk\UserBundle\Tests\Unit\Entity\ModelTestCase;

class RoleRepositoryTest extends ModelTestCase
{
    public function testGetRoleIfExists()
    {
        $container = static::createClient()->getContainer();
        $em = $container->get('doctrine')->getManager();
        $repo = $em->getRepository('TskUserBundle:Role');

        $dbRole = $repo->getRoleIfExists("ROLE_ADMIN");
        $this->assertEquals("ROLE_ADMIN", $dbRole->getRole());

        $falseRole = $repo->getRoleIfExists("ROLE_DOES_NOT_EXIST");
        $this->assertEquals(null, $falseRole);
    }
}
?>