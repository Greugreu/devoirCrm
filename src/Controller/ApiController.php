<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\Todo;
use App\Entity\User;
use App\Repository\AlbumRepository;
use App\Repository\CommentRepository;
use App\Repository\PhotoRepository;
use App\Repository\PostRepository;
use App\Repository\TodoRepository;
use App\Repository\UserRepository;
use App\services\ApiService\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiController extends AbstractController
{
    private ApiService $apiService;
    private UserRepository $userRepository;
    private AlbumRepository $albumRepository;
    private TodoRepository $todoRepository;
    private PostRepository $postRepository;
    private PhotoRepository $photoRepository;
    private CommentRepository $commentRepository;

    public function __construct(ApiService $apiService, UserRepository $userRepository,
                                AlbumRepository $albumRepository, TodoRepository $todoRepository,
                                PostRepository $postRepository, PhotoRepository $photoRepository,
                                CommentRepository $commentRepository)
    {
        $this->apiService = $apiService;
        $this->userRepository = $userRepository;
        $this->albumRepository = $albumRepository;
        $this->todoRepository = $todoRepository;
        $this->postRepository = $postRepository;
        $this->photoRepository = $photoRepository;
        $this->commentRepository = $commentRepository;
    }

    #[Route('/api/getUsers', name:'getApiUsers', methods:'GET')]
    public function getApiUsers()
    {
        $datas = $this->apiService->getApiData('users','');

        if (!empty($datas))
        {
            return $this->userRepository->addNewUser($datas);
        }

        return new JsonResponse('Bad Request' , Response::HTTP_BAD_REQUEST);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/getUserAlbums/{id}', name:'getAlbums', methods: 'GET')]
    public function getUserAlbums(int $id): \Exception|Response
    {
        $datas = $this->apiService->getApiData('users', 'albums', $id);

        if (!empty($datas))
        {
            return $this->albumRepository->addNewAlbum($datas);
        }
        return new JsonResponse('User does not exist', Response::HTTP_NOT_FOUND);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/getUserTodos/{id}', name:'getUserTodos', methods: 'GET')]
    public function getUserTodos(int $id)
    {
        $datas = $this->apiService->getApiData('users', 'todos', $id);

        if (!empty($datas))
        {
            return $this->todoRepository->addNewTodos($datas);
        }

        return new JsonResponse('User does not exist', Response::HTTP_NOT_FOUND);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/getUserPosts/{id}', name:'getUserPosts', methods: 'GET')]
    public function getUserPosts(int $id)
    {
        $datas = $this->apiService->getApiData('users', 'posts', $id);

        if (!empty($datas))
        {
            return $this->postRepository->addNewPosts($datas);
        }
        return new JsonResponse('User does not exist', Response::HTTP_NOT_FOUND);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/getPostComments/{id}', name:'getPostComments')]
    public function getPostComments(int $id)
    {
        $datas = $this->apiService->getApiData('posts', 'comments', $id);

        if (!empty($datas))
        {
            return $this->commentRepository->addNewComments($datas);
        }

        return new JsonResponse('Post does not exist', Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/getAlbumsPhotos/{id}', name:'getAlbumsPhotos')]
    public function getAlbumsPhotos(int $id)
    {
        $datas = $this->apiService->getApiData('albums', 'photos', $id);

        if (!empty($datas)) {
            return $this->photoRepository->addNewPhotos($datas);
        }
        return new JsonResponse('Album does not exist', Response::HTTP_NOT_FOUND);
    }


}
