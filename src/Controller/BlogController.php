<?php

namespace App\Controller;

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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Blog;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="blog_index")
     */
    public function index(Request $request, BlogRepository $blogRepository, UserRepository $userRepository, PaginatorInterface $paginator)
    {
        $blogs = $blogRepository->findAllActiveOrderByDate();
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchKey = $form->get('search')->getData();
            if(!$searchKey) $searchKey = '';
            $blogs = $blogRepository->findBySearch($searchKey);
        }
        $allBlogs = $paginator->paginate(
            $blogs,
            $request->query->getInt('page', 1), 5
        );
        $totalBlogs = count($allBlogs);
        return $this->render('blog/index.html.twig', [
            'blogs'      => $allBlogs,
            'totalBlogs' => $totalBlogs,
            'searchBar'  => $form->createView()
        ]);
    }
    /**
     * @Route("/add", name="add_blog")
     * @return Response
     */
    public function addBlog(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $blog = new Blog;
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Blog added successfully!');
            $blog = $form->getData();
            $blog->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($blog);
            $em->flush();

            return $this->redirectToRoute('blog_index');
        }
        return $this->render('blog/add_blog_form.html.twig', [
            'addBlogForm' => $form->createView(),
        ]);


    }
    /**
     * @Route("/blog/{id}", name="blog_show" , requirements={"id"="\d+"})
     */
    public function showBlog(Request $request, Blog $blog, CommentRepository $commentRepository)
    {
        $comment = new Comment();
        if (!$blog) {
            throw $this->createNotFoundException(
                'No blog found with id: ' . $blog->getId()
            );
        }
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $blog->addComment($comment);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
        }

        $comments = $commentRepository->findBy(['blog' => $blog]);

        $user = $blog->getUser();
        $totalComments = count($blog->getComments());
        return $this->render('blog/show_blog.html.twig', [
            'blog'                 => $blog,
            'addCommentForm'       => $form->createView(),
            'comments'             => $comments,
            'totalComments'        => $totalComments,
            'user'                 => $user
        ]);
    }
}
