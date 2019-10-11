<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Contributor;
use AppBundle\Helper\NoticeVisibility;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ContributorRepository extends BaseRepository
{

    protected $noticeRepository;

    public function __construct(EntityManagerInterface $entityManager, NoticeRepository $noticeRepository)
    {
        parent::__construct($entityManager);

        $this->noticeRepository = $noticeRepository;
    }

    public function getAllEnabledWithAtLeastOneContribution()
    {
        $activeContributorsQuery = $this->noticeRepository->repository->createQueryBuilder('n')
            ->select('IDENTITY(n.contributor)')->distinct()
            ->where('n.visibility = :visibility');

        $mainQuery = $this->repository->createQueryBuilder('c');
        return $mainQuery
            ->where('c.enabled = true')
            ->andWhere($mainQuery->expr()->in('c.id', $activeContributorsQuery->getDQL()))
            ->setParameter('visibility', NoticeVisibility::PUBLIC_VISIBILITY())
            ->getQuery()->execute();
    }

    /**
     * @return string
     */
    public function getResourceClassName()
    {
        return Contributor::class;
    }

    public static function getOrderedList(EntityRepository $er)
    {
        return $er->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC');
    }

}
