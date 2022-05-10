<?php

namespace App\Repository;

use App\Entity\Album;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends ServiceEntityRepository<Album>
 *
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumRepository extends ServiceEntityRepository
{
    private UserRepository $userRepository;

    public function __construct(ManagerRegistry $registry, UserRepository $userRepository)
    {
        parent::__construct($registry, Album::class);
        $this->userRepository = $userRepository;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Album $entity, bool $flush = true): void
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
    public function remove(Album $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function addNewAlbum($datas)
    {
        $add = 0;
        $update = 0;

        foreach ($datas as $data)
        {
            $check = $this->findOneBy(['title' => $data['title']]);
            if (empty($check)){
                $album = new Album();
                $album->setUserId($this->userRepository->findOneBy(['id' => $data['userId']]));
                $album->setTitle($data['title']);
                try {
                    $this->add($album, true);
                    $add++;
                } catch (\Exception $exception) {
                    return new JsonResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $check->setUserId($this->userRepository->findOneBy(['id' => $data['userId']]));
                $check->setTitle($data['title']);
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
    //  * @return Album[] Returns an array of Album objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Album
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
