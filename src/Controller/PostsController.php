<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    #[Route('/posts', name: 'app_posts')]
    public function index(): Response
    {
        $datas = $this->postRepository->findAll();
        return $this->render('posts/index.html.twig', [
            'controller_name' => 'PostsController',
            'datas' => $datas
        ]);
    }
}
