<?php
namespace Tsk\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tsk\UserBundle\Entity\Role;

class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    function load(ObjectManager $em)
    {
        $roleuser = new Role();
        $roleuser->setRole("ROLE_USER");
        $this->setReference("user_role", $roleuser);

        $roleadmin = new Role();
        $roleadmin->setRole("ROLE_ADMIN");
        $this->setReference("user_admin", $roleadmin);

        $em->persist($roleuser);
        $em->persist($roleadmin);
        $em->flush();
    }

    /**
     * Get the order of this fixture
     */
    function getOrder()
    {
        return 1;
    }
}
?>