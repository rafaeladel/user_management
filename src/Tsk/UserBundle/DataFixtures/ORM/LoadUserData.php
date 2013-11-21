<?php
namespace Tsk\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tsk\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    function load(ObjectManager $em)
    {
        if($this->container === null){
            return false;
        }

        $user1 = new User();
        $user1->setUsername("rafael");
        $user1->setPlainPassword("123456");
        $user1->setEmail("rafael@rafael.com");
        $user1->setFirstname("Rafael");
        $user1->setLastname("Adel");
        $user1->addRole($em->merge($this->getReference("user_role")));

        $user2 = new User();
        $user2->setUsername("Adel");
        $user2->setPlainPassword("7891011");
        $user2->setEmail("adel@adel.com");
        $user2->setFirstname("Adel");
        $user2->setLastname("Moshreky");
        $user2->addRole($em->merge($this->getReference("user_admin")));

        $em->persist($user1);
        $em->persist($user2);
        $em->flush();
    }

    /**
     * Get the order of this fixture
     */
    function getOrder()
    {
        return 2;
    }

    /**
     * Sets the Container.
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
?>