<?php

namespace App\Controller;

use App\Repository\TodoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodosController extends AbstractController
{
    private TodoRepository $todoRepository;

    public function __construct(TodoRepository $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }

    #[Route('/todos', name: 'app_todos')]
    public function index(): Response
    {
        $datas = $this->todoRepository->findAll();

        return $this->render('todos/index.html.twig', [
            'controller_name' => 'TodosController',
            'datas' => $datas
        ]);
    }
}
