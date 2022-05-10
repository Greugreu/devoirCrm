<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    private AlbumRepository $albumRepository;

    public function __construct(AlbumRepository $albumRepository)
    {
        $this->albumRepository = $albumRepository;
    }

    #[Route('/album', name: 'app_album')]
    public function index(): Response
    {
        $datas = $this->albumRepository->findAll();
        return $this->render('album/index.html.twig', [
            'controller_name' => 'AlbumController',
            'datas' => $datas
        ]);
    }
}
