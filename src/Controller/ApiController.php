<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\Todo;
use App\Entity\User;
use App\Repository\AlbumRepository;
use App\Repository\PhotoRepository;
use App\Repository\PostRepository;
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
    private TodoRepository $todoRepository;
    private PostRepository $postRepository;
    private PhotoRepository $photoRepository;

    public function __construct(ApiService $apiService, UserRepository $userRepository,
                                AlbumRepository $albumRepository, TodoRepository $todoRepository,
                                PostRepository $postRepository, PhotoRepository $photoRepository)
    {
        $this->apiService = $apiService;
        $this->userRepository = $userRepository;
        $this->albumRepository = $albumRepository;
        $this->todoRepository = $todoRepository;
        $this->postRepository = $postRepository;
        $this->photoRepository = $photoRepository;
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/getUserAlbums', name:'getAlbums')]
    public function getUserAlbums(): \Exception|Response
    {
        $datas = $this->apiService->getApiData('users', 'albums');
        $add = 0;
        $update = 0;

        foreach ($datas as $data)
        {
            $check = $this->albumRepository->findOneBy(['title' => $data['title']]);
            if (empty($check)){
                $album = new Album();
                $album->setUserId($this->userRepository->findOneBy(['id' => $data['userId']]));
                $album->setTitle($data['title']);
                try {
                    $this->albumRepository->add($album, true);
                    $add++;
                } catch (\Exception $exception) {
                    return $exception;
                }
            } else {
                $check->setUserId($this->userRepository->findOneBy(['id' => $data['userId']]));
                $check->setTitle($data['title']);
                try {
                    $this->albumRepository->add($check);
                    $update++;
                } catch (\Exception $exception) {
                    return new JsonResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        }

        return new JsonResponse('Ok: ' . $add . ' added -- ' . $update . ' updated' , Response::HTTP_OK);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/getUserTodos', name:'getUserTodos')]
    public function getUserTodos()
    {
        $datas = $this->apiService->getApiData('users', 'todos');
        $add = 0;
        $update = 0;

        foreach ($datas as $data)
        {
            $check = $this->todoRepository->findOneBy(['title' => $data['title']]);
            if (empty($check)){
                $todo = new Todo();
                $todo->setTitle($data['title']);
                $todo->setUserId($this->userRepository->findOneBy(['id' => $data['userId']]));
                $todo->setCompleted($data['completed']);

                try {
                    $this->todoRepository->add($todo);
                    $add++;
                } catch (\Exception $exception) {
                    return $exception;
                }
            } else {
                $check->setTitle($data['title']);
                $check->setUserId($this->userRepository->findOneBy(['id' => $data['userId']]));
                $check->setCompleted($data['completed']);

                try {
                    $this->todoRepository->add($check);
                    $update++;
                } catch (\Exception $exception) {
                    return $exception;
                }
            }
        }

        return new JsonResponse('Ok: ' . $add . ' added -- ' . $update . ' updated' , Response::HTTP_OK);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/getUserPosts', name:'getUserPosts')]
    public function getUserPosts()
    {
        $datas = $this->apiService->getApiData('users', 'posts');
        $add = 0;
        $update = 0;

        foreach ($datas as $data)
        {
            $check = $this->postRepository->findOneBy(['title' => $data['title']]);
            if (empty($check)){
                $post = new Post();
                $post->setUserId($this->userRepository->findOneBy(['id' => $data['userId']]));
                $post->setTitle($data['title']);
                $post->setBody($data['body']);

                try {
                    $this->postRepository->add($post);
                    $add++;
                } catch (\Exception $exception) {
                    return $exception;
                }
            } else {
                $check->setUserId($this->userRepository->findOneBy(['id' => $data['userId']]));
                $check->setTitle($data['title']);
                $check->setBody($data['body']);

                try {
                    $this->postRepository->add($check);
                    $update++;
                } catch (\Exception $exception){
                    return $exception;
                }
            }
        }

        return new JsonResponse('Ok: ' . $add . ' added -- ' . $update . ' updated' , Response::HTTP_OK);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/getAlbumPhotos', name:'getAlbumPhotos')]
    public function getAlbumPhotos()
    {
        $datas = $this->apiService->getApiData('posts', 'comments');
        $add = 0;
        $update = 0;

        foreach ($datas as $data) {
            $check = $this->photoRepository->findOneBy(['title' => $data['title']]);
            if (empty($check)) {
                $photo = new Photo();
                $photo->setTitle($data['title']);
                $photo->setAlbumId($this->albumRepository->findOneBy(['id' => $data['albumId']]));
                $photo->setUrl($data['url']);
                $photo->setThumbnailUrl('thumbnailUrl');

                try {
                    $this->photoRepository->add($photo);
                    $add++;
                } catch (\Exception $exception) {
                    return $exception;
                }
            } else {
                $check->setAlbumId($this->albumRepository->findOneBy(['id' => $data['albumId']]));
                $check->setTitle($data['title']);
                $check->setUrl($data['url']);
                $check->setThumbnailUrl('thumbnailUrl');

                try {
                    $this->photoRepository->add($check);
                    $update++;
                } catch (\Exception $exception) {
                    return $exception;
                }
            }
        }
        return new JsonResponse('Ok: ' . $add . ' added -- ' . $update . ' updated' , Response::HTTP_OK);
    }





}
