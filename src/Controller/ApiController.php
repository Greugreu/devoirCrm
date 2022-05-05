<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Todo;
use App\Entity\User;
use App\Repository\AlbumRepository;
use App\Repository\TodoRepository;
use App\Repository\UserRepository;
use App\services\ApiService\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiController extends AbstractController
{
    private ApiService $apiService;
    private UserRepository $userRepository;
    private AlbumRepository $albumRepository;
    private ?User $currentUser;
    private TodoRepository $todoRepository;

    public function __construct(ApiService $apiService, UserRepository $userRepository,
                                AlbumRepository $albumRepository, TodoRepository $todoRepository)
    {
        $this->apiService = $apiService;
        $this->userRepository = $userRepository;
        $this->albumRepository = $albumRepository;
        $this->todoRepository = $todoRepository;
        $this->currentUser = $this->userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
    }

    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/getUserAlbums', name:'getAlbums')]
    public function getUserAlbums(): \Exception|Response
    {
        $datas = $this->apiService->getApiData('users', 'albums');
        foreach ($datas as $data)
        {
            $album = new Album();
            $album->setUserId($this->currentUser);
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

    #[Route('/api/getUserTodos', name:'getUserTodos')]
    public function getUserTodos()
    {
        $datas = $this->apiService->getApiData('users', 'todos');
        foreach ($datas as $data)
        {
            $todo = new Todo();
            $todo->setTitle($data['title']);
            $todo->setUserId($this->currentUser);
            $todo->setCompleted($data['completed']);

            try {
                $this->todoRepository->add($todo);
            } catch (\Exception $exception) {
                return $exception;
            }
        }

        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
            'data' =>  $this->todoRepository->findAll()
        ]);
    }


}
