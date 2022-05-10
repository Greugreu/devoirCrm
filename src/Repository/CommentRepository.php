<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    private $postRepository;

    public function __construct(ManagerRegistry $registry, PostRepository $postRepository)
    {
        parent::__construct($registry, Comment::class);
        $this->postRepository = $postRepository;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Comment $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Comment $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function addNewComments($datas)
    {
        $add = 0;
        $update = 0;
        foreach ($datas as $data) {
            $check = $this->findOneBy(['name' => $data['name']]);
            if (empty($check)) {
                $comment = new Comment();
                $comment->setName($data['name']);
                $comment->setPostId($this->postRepository->find($data['postId']));
                $comment->setEmail($data['email']);
                $comment->setBody($data['body']);

                try {
                    $this->add($comment);
                    $add++;
                } catch (\Exception $exception) {
                    return new JsonResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $check->setName($data['name']);
                $check->setEmail($data['email']);
                $check->setBody($data['body']);
                $check->setPostId($this->postRepository->find($data['postId']));

                try {
                    $this->add($check);
                    $update++;
                } catch (\Exception $exception) {
                    return new JsonResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        }
        return new JsonResponse('Ok: ' . $add . ' added -- ' . $update . ' updated' , Response::HTTP_OK);

    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
