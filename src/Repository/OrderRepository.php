<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function add(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOne($id)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT o.id, 
                o.order_number, 
                o.paid_purchase, 
                o.status, 
                u.name as UserName,
                u.id as UserId,
                p.id as ProductId,
                p.price as ProductPrice,
                p.description as ProductDescription
            FROM App\Entity\Order o
            INNER JOIN o.user u
            INNER JOIN o.product p
            WHERE o.id = :id'
        )->setParameter('id', $id);

        return $query->getResult();
    }

    public function findAll()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT o.id, 
                o.order_number, 
                o.paid_purchase, 
                o.status, 
                u.name as UserName,
                u.id as UserId,
                p.id as ProductId,
                p.price as ProductPrice,
                p.description as ProductDescription
            FROM App\Entity\Order o
            INNER JOIN o.user u
            INNER JOIN o.product p'
        );

        return $query->getResult();
    }

    public function findAllActive($status)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT o.id, 
                o.order_number, 
                o.paid_purchase, 
                o.status, 
                u.name as UserName,
                u.id as UserId,
                p.id as ProductId,
                p.price as ProductPrice,
                p.description as ProductDescription
            FROM App\Entity\Order o
            INNER JOIN o.user u
            INNER JOIN o.product p
            WHERE o.status = :stt'
        )->setParameter('stt', $status);

        return $query->getResult();
    }
    public function findAllpaid($paid)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT o.id, 
                o.order_number, 
                o.paid_purchase, 
                o.status, 
                u.name as UserName,
                u.id as UserId,
                p.id as ProductId,
                p.price as ProductPrice,
                p.description as ProductDescription
            FROM App\Entity\Order o
            INNER JOIN o.user u
            INNER JOIN o.product p
            WHERE o.paid_purchase = :paid'
        )->setParameter('paid', $paid);

        return $query->getResult();
    }
}
