<?php

namespace App\Repository;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends ServiceEntityRepository<Photo>
 *
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends ServiceEntityRepository
{
    private AlbumRepository $albumRepository;

    public function __construct(ManagerRegistry $registry, AlbumRepository $albumRepository)
    {
        parent::__construct($registry, Photo::class);
        $this->albumRepository = $albumRepository;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Photo $entity, bool $flush = true): void
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
    public function remove(Photo $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function addNewPhotos($datas)
    {
        $add = 0;
        $update = 0;
        foreach ($datas as $data) {
            $check = $this->findOneBy(['title' => $data['title']]);
            if (empty($check)) {
                $photo = new Photo();
                $photo->setAlbumId($this->albumRepository->find($data['albumId']));
                $photo->setThumbnailUrl($data['thumbnailUrl']);
                $photo->setTitle($data['title']);
                $photo->setUrl($data['url']);

                try {
                    $this->add($photo);
                    $add++;
                } catch (\Exception $exception) {
                    return new JsonResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $check->setUrl($data['url']);
                $check->setTitle($data['title']);
                $check->setThumbnailUrl('thumbnailUrl');

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
    //  * @return Photo[] Returns an array of Photo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Photo
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
