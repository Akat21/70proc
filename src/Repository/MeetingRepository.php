<?php

namespace App\Repository;

use App\Entity\Meeting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Meeting>
 *
 * @method Meeting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Meeting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Meeting[]    findAll()
 * @method Meeting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meeting::class);
    }


    public function findInRoom(string $room, \DateTimeInterface $now): ?Meeting
    {
        $extendedNow = new \DateTime($now->format('Y-m-d H:i:s'));
        $extendedNow->modify('-15 minutes'); // @todo add to config // -15min so it's same as end+15min

        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.room = :room')
            ->setParameter('room', $room)
            ->andWhere('m.start <= :now')
            ->andWhere('m.stop >= :extendedNow')
            ->setParameter('now', $now)
            ->setParameter('extendedNow', $extendedNow)
            ->orderBy('m.start', 'ASC')
            ->setMaxResults(1)
        ;

        $query = $qb->getQuery();
        $meeting = $query->getOneOrNullResult();

        return $meeting;
    }

    public function save(Meeting $meeting, bool $flush = false): void
    {
        $this->getEntityManager()->persist($meeting);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
