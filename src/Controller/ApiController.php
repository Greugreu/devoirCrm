<?php

namespace App\Controller;

use App\Entity\Album;
use App\Repository\AlbumRepository;
use App\Repository\UserRepository;
use App\services\ApiService\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private ApiService $apiService;
    private UserRepository $userRepository;
    private AlbumRepository $albumRepository;

    public function __construct(ApiService $apiService, UserRepository $userRepository, AlbumRepository $albumRepository)
    {
        $this->apiService = $apiService;
        $this->userRepository = $userRepository;
        $this->albumRepository = $albumRepository;
    }

    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    #[Route('/api/getUserAlbums', name:'getAlbums')]
    public function getUserAlbums(): \Exception|Response
    {
        $user = $this->userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $datas = $this->apiService->getApiData('users', 'albums');
        foreach ($datas as $data)
        {
            $album = new Album();
            $album->setUserId($user);
            $album->setTitle($data['title']);
            try {
                $this->albumRepository->add($album);
            } catch (\Exception $exception) {
                return $exception;
            }
        }

        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
            'data' =>  $this->albumRepository->findAll()
        ]);
    }
}
