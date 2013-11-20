<?php
namespace Tsk\UserBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Tsk\UserBundle\Entity\User;
use Tsk\UserBundle\Utils\Encoder;

class EncoderSubscriber implements EventSubscriber
{
    protected $encoder;

    public function __construct(Encoder $ef)
    {
        $this->encoder = $ef;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     */
    public function getSubscribedEvents()
    {
        return array(
            'preUpdate',
            'prePersist',
        );
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->encode($args);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->encode($args);
    }

    protected function encode(LifecycleEventArgs $args)
    {
        $user = $args->getEntity();

        if($user instanceof User)
        {
            $encodedPassword = ($this->encoder->encode($user, $user->getPlainPassword(), $user->getSalt()));
            $user->setPassword($encodedPassword);
        }
    }
}
?>