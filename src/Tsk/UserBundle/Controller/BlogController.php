<?php
namespace Tsk\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    public function indexAction()
    {
        return $this->render("TskUserBundle:Blog:index.html.twig");
    }
}
?>