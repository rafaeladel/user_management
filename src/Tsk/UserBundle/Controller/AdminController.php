<?php
namespace Tsk\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tsk\UserBundle\Entity\Role;

class AdminController extends Controller
{
    public function listUsersAction()
    {
        $bag = array();
        $users = $this->getDoctrine()->getRepository("TskUserBundle:User")->getAllUsersWithRoles();
        foreach($users as $user)
        {
            $data = array();
            $deleteForm = $this->createDeleteForm($user->getUsername())->createView();
            $activateForm = $this->createActivateForm($user->getUsername())->createView();
            $makeAdminForm = $this->createMakeUserAdminForm($user->getUsername())->createView();
            $data["user_data"] = $user;
            foreach($user->getRoles() as $role)
            {
                $data["user_roles"] = $role->getRole();
            }
            $data["activate_form"] = $activateForm;
            $data["delete_form"] = $deleteForm;
            $data["make_admin_form"] = $makeAdminForm;
            $bag[] = $data;
        }
        return $this->render("TskUserBundle:Admin:list.html.twig", array("bag" => $bag));
    }

    public function changeStateAction(Request $request, $username, $target)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("TskUserBundle:User")->findOneByUsername($target);
        if(!$user)
        {
            throw $this->createNotFoundException("User not found!");
        }
        $user->setIsActive(!$user->getIsActive());
        $em->persist($user);
        $em->flush();
        if($request->isXmlHttpRequest())
        {
            $response = array("code" => 200, "userState" => $user->getIsActive() ? "Yes": "No");
            return new Response(json_encode($response), 200, array('Content-Type' => 'application/json'));
        }
        return $this->redirect($this->generateUrl("tsk_users_list", array("username" => $username)));
    }

    public function deleteUserAction(Request $request, $username, $target)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("TskUserBundle:User")->findOneByUsername($target);
        $em->remove($user);
        $em->flush();
        if($request->isXmlHttpRequest())
        {
            $response = array("code" => 200);
            return new Response(json_encode($response), 200, array('Content-Type' => 'application/json'));
        }
        return $this->redirect($this->generateUrl("tsk_users_list", array("username" => $username)));
    }
    public function makeUserAdminAction(Request $request, $username, $target)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("TskUserBundle:User")->findOneByUsername($target);
        $admin_role = $em->getRepository("TskUserBundle:Role")->getRoleIfExists("ROLE_ADMIN");
        if(!$admin_role)
        {
            $admin_role = new Role();
            $admin_role->setRole("ROLE_ADMIN");
        }
        $user->addRole($admin_role);
        $em->persist($user);
        $em->flush();
        if($request->isXmlHttpRequest())
        {
            $response = array("code" => 200);
            return new Response(json_encode($response), 200, array('Content-Type' => 'application/json'));
        }
        return $this->redirect($this->generateUrl("tsk_users_list", array("username" => $username)));
    }

    public function createDeleteForm($username)
    {
        $builder = $this->createFormBuilder()
                        ->add($username, "hidden")
                        ->add("delete", "submit");
        return $builder->getForm();
    }

    public function createActivateForm($username)
    {
        $builder = $this->createFormBuilder()
                        ->add($username, "hidden")
                        ->add("activate", "submit");
        return $builder->getForm();
    }

    public function createMakeUserAdminForm($username)
    {
        $builder = $this->createFormBuilder()
                ->add($username, "hidden")
                ->add("make_admin", "submit");
        return $builder->getForm();
    }
}
?>