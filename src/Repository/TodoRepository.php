<?php

namespace App\Repository;

use App\Entity\Todo;
use App\SearchFormData\SearchFormData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Todo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Todo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Todo[]    findAll()
 * @method Todo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Todo::class);
    }


    public function findAllTodos(string $search = null, $order = null)
    {
        $query = $this->createQueryBuilder('t')
            ->select('t', 'tasks')
            ->leftJoin('t.tasks', 'tasks');
        if ($search) {
            $query = $query
                ->andWhere('(t.todoname  LIKE :search) OR (tasks.taskname LIKE :search)')
                ->setParameter('search', "%{$search}%");
        }

        if ($order) {
            if (mb_strtolower($order) === 'asc')
                $query = $query->orderBy('t.id', 'ASC');
            if (mb_strtolower($order) === 'desc')
                $query = $query->orderBy('t.id', 'DESC');
        }

        return $query->getQuery()
            ->getResult();
    }


    public function getSearchQuery(SearchFormData $searchData)
    {
        $query = $this
            ->createQueryBuilder('todo')
            ->select('todo', 'tasks')
            ->leftJoin('todo.tasks', 'tasks');

        if (!empty($searchData->q)) {
            $query = $query
                ->andWhere('(todo.todoname  LIKE :q) OR (tasks.taskname LIKE :q)')
                ->setParameter('q', "%{$searchData->q}%");
        }

        if (!empty($searchData->order)) {

            if (mb_strtolower($searchData->order) === 'asc')
                $query = $query->orderBy('todo.id', 'ASC');
            if (mb_strtolower($searchData->order) === 'desc')
                $query = $query->orderBy('todo.id', 'DESC');
        }

//        $query = $query->orderBy('car.id', 'DESC');


        return $query->getQuery()->getResult();
    }
}
