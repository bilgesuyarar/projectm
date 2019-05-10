<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Comment;
use App\Form\BlogType;
use App\Form\ChangeBlogStatusType;
use App\Form\ChangeStatusType;
use App\Form\CommentType;
use App\Form\SearchType;
use App\Repository\BlogRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Blog;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;



class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/blogs", name="admin_blogs")
     */
    public function showBlogs(Request $request, BlogRepository $blogRepository)
    {

        $blogs = $blogRepository->findAllOrderByDate();
        $blogs= $blogs->getResult();
        return $this->render('admin/admin_blogs.html.twig', [
            'blogs' => $blogs
        ]);
    }

    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function showUsers(Request $request, UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        return $this->render('admin/admin_users.html.twig',[
            'users' => $users
        ]);
    }
    /**
     * @Route("/admin/blogs/delete/{id}", name="delete_blog")
     */
    public function deleteBlog(Blog $blog)
    {
        if (!$blog) {
            throw $this->createNotFoundException('No guest found');
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($blog);
        $em->flush();

        return $this->redirectToRoute('admin_blogs');


    }

    /**
     * @Route("/admin/blogs/make-active/{id}", name="blog_status_active")
     */
    public function makeActive(Blog $blog)
    {
        if (!$blog) {
            throw $this->createNotFoundException('No guest found');
        }
        $blog->setStatus('active');
        $em = $this->getDoctrine()->getEntityManager();
//        $em->remove($blog);
        $em->flush();

        return $this->redirectToRoute('admin_blogs');

    }/**
     * @Route("/admin/blogs/make-passive/{id}", name="blog_status_passive")
     */
    public function makePassive(Blog $blog)
    {
        if (!$blog) {
            throw $this->createNotFoundException('No guest found');
        }
        $blog->setStatus('passive');
        $em = $this->getDoctrine()->getEntityManager();
//        $em->remove($blog);
        $em->flush();

        return $this->redirectToRoute('admin_blogs');

    }
}
