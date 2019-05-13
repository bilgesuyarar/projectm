<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\EditProfileType;
use App\Form\Model\ChangePassword;
use App\Security\LoginFormAuthenticator;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Knp\Component\Pager\PaginatorInterface;




class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="edit_profile")
     */
    public function editProfile(Request $request,AuthenticationUtils $authenticationUtils, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Change successful!');
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
                return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
//        }

        }
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('user/edit_profile.html.twig', [
                'user'            => $user,
                'editProfileForm' => $form->createView(),
                'error'           => $error
            ]);

    }

    /**
     * @Route("/change-password", name="change_password")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $changePassword = new ChangePassword();
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $changePassword);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->addFlash('success', 'Change successful!');

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('newPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('blog_index');

        }
        return $this->render('user/change_password.html.twig', [
            'changePasswordForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/my-blogs", name="user_blogs")
     *
     */
    public function userBlogs()
    {
//        $user = $this->getUser();
//        $blogs = $user->getBlogs();
        return $this->render('user/user_blogs.html.twig');
    }


}
