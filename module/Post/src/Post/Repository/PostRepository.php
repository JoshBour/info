<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/6/2014
 * Time: 12:15 πμ
 */

namespace Post\Repository;


use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository{
    public function search($value){
        if(!empty($value)){
            $qb = $this->createQueryBuilder('p');
            $qb->where($qb->expr()->like('p.name','?1'))
                ->orWhere($qb->expr()->like('p.productNumber','?2'))
                ->setParameter(1, $value.'%')
                ->setParameter(2, $value.'%');
            return $qb->getQuery()->getResult();
        }else{
            throw new \InvalidArgumentException("The product's name can't be empty.");
        }
    }

    public function count()
    {
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p.postId)');
        $query = $query->getQuery();
        return $query->getSingleScalarResult();


    }
} 