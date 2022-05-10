<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends AbstractController
{
    private CommentRepository $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    #[Route('/comments', name: 'app_comments')]
    public function index(): Response
    {
        $datas = $this->commentRepository->findAll();
        return $this->render('comments/index.html.twig', [
            'controller_name' => 'CommentsController',
            'datas' => $datas
        ]);
    }
}
