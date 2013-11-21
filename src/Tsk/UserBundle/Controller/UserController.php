<?php
namespace Tsk\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Tsk\UserBundle\Entity\Role;
use Tsk\UserBundle\Entity\User;
use Tsk\UserBundle\Forms\UserEditType;
use Tsk\UserBundle\Forms\UserType;
use Tsk\UserBundle\Utils\Encoder;

class UserController extends Controller
{
    public function profileAction($username)
    {
        $user = $this->getDoctrine()->getRepository("TskUserBundle:User")->findOneByUsername($username);
        if(!$user)
        {
            throw $this->createNotFoundException("User not found!");
        }
        return $this->render("TskUserBundle:User:profile.html.twig");
    }

    public function getEditAction($username)
    {
        $user = $this->getDoctrine()->getRepository("TskUserBundle:User")->findOneByUsername($username);
        if(!$user)
        {
            throw $this->createNotFoundException("User not found!");
        }
        $form = $this->createForm(new UserEditType(), $user);
        return $this->render("TskUserBundle:User:edit.html.twig", array(
            "form" => $form->createView(),
        ));
    }

    public function postEditAction(Request $request, $username)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("TskUserBundle:User")->findOneByUsername($username);
        if(!$user)
        {
            throw $this->createNotFoundException("User not found!");
        }
        $form = $this->createForm(new UserEditType(), $user);
        $form->handleRequest($request);

        if($form->isValid())
        {
//            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl("tsk_user_profile", array("username"=> $user->getUsername())));
        }

        return $this->render("TskUserBundle:User:edit.html.twig", array("form" => $form->createView()));
    }

    public function getChangePasswordAction($username)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("TskUserBundle:User")->findOneByUsername($username);
        if(!$user)
        {
            throw $this->createNotFoundException("User not found!");
        }
        $form = $this->createPasswordChangeForm();
        return $this->render("TskUserBundle:User:change_password.html.twig", array(
            "form"      => $form->createView(),
        ));
    }

    public function postChangePasswordAction(Request $request, $username)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("TskUserBundle:User")->findOneByUsername($username);
        if(!$user)
        {
            throw $this->createNotFoundException("User not found!");
        }
        $form = $this->createPasswordChangeForm();
        $form->handleRequest($request);
        $old_password = $form->get('old_password')->getData();
        $new_password = $form->get("new_password")->getData();
        if($form->isValid())
        {
            $old_password_encoded = $this->get("tsk_encoder")->encode($user, $old_password, $user->getSalt());
            if($old_password_encoded == $user->getPassword())
            {
                $user->setPlainPassword($new_password);
                $em->flush();
                return $this->redirect($this->generateUrl("tsk_user_profile", array("username"=>$user->getUsername())));
            }
            //FOR DEBUGGING ONLY !
            else { return new Response($old_password_encoded."\n".$user->getPassword());}
        }

        return $this->render("TskUserBundle:User:change_password.html.twig", array("username" => $user->getUsername(), "form"=>$form->createView()));
    }

    public function getLoginAction(Request $request)
    {
        $session = $request->getSession();
        if($this->container->get('security.context')->isGranted("ROLE_USER"))
        {
            $token = $this->container->get('security.context')->getToken();
            return $this->redirect($this->container->get('router')->generate("tsk_user_profile", array($token->getUsername())));
        }

        if($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR))
        {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        else
        {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        return $this->render("TskUserBundle:User:login.html.twig", array(
            "last_username" => $session->get(SecurityContext::LAST_USERNAME),
            "error"     => $error,
        ));
    }

    public function getRegisterAction()
    {
        $user = new User($this->container);
        $form = $this->createForm(new UserType(), $user);
        $error = null;
        return $this->render("TskUserBundle:User:register.html.twig", array("form" => $form->createView(), "error_msg" => $error));
    }

    public function postRegisterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = new User($this->container);
        $form = $this->createForm(new UserType(), $user);
        $form->handleRequest($request);

        $username = $form->get("username")->getData();
        $email = $form->get("email")->getData();

        $foundByUsername = $em->getRepository("TskUserBundle:User")->findOneByUsername($username);
        if($foundByUsername)
        {
            $error = "Username already exists.";
            return $this->render("TskUserBundle:User:register.html.twig", array("form" => $form->createView(), "error_msg" => $error));

        }

        $foundByEmail = $em->getRepository("TskUserBundle:User")->findOneByEmail($email);
        if($foundByEmail)
        {
            $error = "Email already exists.";
            return $this->render("TskUserBundle:User:register.html.twig", array("form" => $form->createView(), "error_msg" => $error));
        }

        $error = null;

        $role = $em->getRepository("TskUserBundle:Role")->getRoleIfExists("ROLE_USER");
        if(!$role)
        {
            $role = new Role();
            $role->setRole("ROLE_USER");
        }
        $user->addRole($role);

        if($form->isValid())
        {
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('tsk_user_profile', array('username' => $user->getUsername())));

        }

        return $this->render("TskUserBundle:User:register.html.twig", array("form" => $form->createView(), "error_msg" => $error));
    }

    public function createPasswordChangeForm()
    {
        $builder = $this->createFormBuilder()
                        ->add('old_password', 'password', array("label" => "Old Password"))
                        ->add('new_password', 'repeated', array(
                            'type'              =>  'password',
                            'invalid_message'   =>  'The password mush match',
                            'required'          =>  true,
                            'first_name'        =>  'first',
                            'second_name'       =>  'confirmation',
                            'first_options'     =>  array('label' => 'New Password'),
                            'second_options'    =>  array('label' => 'Confirm Password'),
                        ))
                        ->add('submit', 'submit');
        return $builder->getForm();
    }
}
?>