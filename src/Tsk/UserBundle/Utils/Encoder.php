<?php
namespace Tsk\UserBundle\Utils;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class Encoder
{
    private $ef;

    public function __construct(EncoderFactoryInterface $ef)
    {
        $this->ef = $ef;
    }
    public function encode($obj, $password, $salt)
    {
        $encoder = $this->ef->getEncoder($obj);
        $encoded = $encoder->encodePassword($password, $salt);
        return $encoded;
    }
}
?>