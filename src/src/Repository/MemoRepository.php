<?php

namespace App\Repository;

use App\Entity\Memo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Memo>
 */
class MemoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Memo::class);
    }

    // 一覧検索と個別検索は継承元のメソッドを使用するので作らない

    // 保存するメソッド
    public function saveMemo($memo): void
    {
        $em = $this->getEntityManager();
        $em->persist($memo);
        $em->flush();
    }

    // 削除するメソッド
    public function deleteMemo($memo): void
    {
        $em = $this->getEntityManager();
        $em->remove($memo);
        $em->flush();
    }

    /**
     * @return Memo[]
     */
    public function findByPriority(int $priority): array
    {
        return $this->findBy(
            ['priority' => $priority],
            ['id' => 'DESC']
        );
    }
}
