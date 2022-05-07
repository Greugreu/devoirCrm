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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
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

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/getUserAlbums/{id}', name:'getAlbums', methods: 'GET')]
    public function getUserAlbums(int $id): \Exception|Response
    {
        $datas = $this->apiService->getApiData('users', 'albums', $id);
        $add = 0;
        $update = 0;

        if (!empty($datas))
        {
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
        return new JsonResponse('User does not exist', Response::HTTP_NOT_FOUND);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/getUserTodos/{id}', name:'getUserTodos', methods: 'GET')]
    public function getUserTodos(int $id)
    {
        $datas = $this->apiService->getApiData('users', 'todos', $id);
        $add = 0;
        $update = 0;

        if (!empty($datas))
        {
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

        return new JsonResponse('User does not exist', Response::HTTP_NOT_FOUND);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/getUserPosts/{id}', name:'getUserPosts', methods: 'GET')]
    public function getUserPosts(int $id)
    {
        $datas = $this->apiService->getApiData('users', 'posts', $id);
        $add = 0;
        $update = 0;

        if (!empty($datas))
        {
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
        return new JsonResponse('User does not exist', Response::HTTP_NOT_FOUND);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/getAlbumPhotos/{id}', name:'getAlbumPhotos')]
    public function getAlbumPhotos(int $id)
    {
        $datas = $this->apiService->getApiData('albums', 'photos', $id);
        $add = 0;
        $update = 0;

        if (!empty($datas))
        {
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
        return new JsonResponse('Album does not exist', Response::HTTP_NOT_FOUND);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/getPostComments/{id}', name:'getPostComments')]
    public function getPostComments(int $id)
    {
        $datas = $this->apiService->getApiData('posts', 'comments', $id);
        $add = 0;
        $update = 0;

        if (!empty($datas))
        {
            foreach ($datas as $data) {
                $check = $this->commentRepository->findOneBy(['name' => $data['name']]);
                if (empty($check)) {
                    $comment = new Comment();
                    $comment->setName($data['name']);
                    $comment->setPostId($this->postRepository->findOneBy(['id' => $data['postId']]));
                    $comment->setEmail($data['email']);
                    $comment->setBody($data['body']);

                    try {
                        $this->commentRepository->add($comment);
                        $add++;
                    } catch (\Exception $exception) {
                        return $exception;
                    }
                } else {
                    $check->setPostId($this->postRepository->findOneBy(['id' => $data['postId']]));
                    $check->setName($data['name']);
                    $check->setEmail($data['email']);
                    $check->setBody($data['body']);

                    try {
                        $this->commentRepository->add($check);
                        $update++;
                    } catch (\Exception $exception) {
                        return $exception;
                    }
                }
            }
            return new JsonResponse('Ok: ' . $add . ' added -- ' . $update . ' updated' , Response::HTTP_OK);
        }

        return new JsonResponse('Post does not exist', Response::HTTP_NOT_FOUND);
    }
}
