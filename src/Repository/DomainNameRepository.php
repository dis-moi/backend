<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DomainName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DomainNameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DomainName::class);
    }

    /**
     * @return DomainName?
     */
    public function findByName(string $domainName): ?DomainName
    {
        return $this->findOneBy([
            'name' => $domainName,
        ]);
    }

    public function findOrCreate(string $domainName): DomainName
    {
        $existing = $this->findByName($domainName);
        if ($existing) {
            return $existing;
        }
        $newDomain = new DomainName($domainName);
        $this->getEntityManager()->persist($newDomain);
        $this->getEntityManager()->flush();

        return $newDomain;
    }

    /**
     * @param string $host an hostname, for example: www.pref.okinawa.jp
     */
    public function findMostSpecificFromHost(string $host): ?DomainName
    {
        $domainParts = explode('.', $host);

        $existingDomainName = null;
        while (\count($domainParts) > 0) {
            /** @var DomainName|null $existingDomainName */
            $existingDomainName = $this->findByName(implode('.', $domainParts));
            if ($existingDomainName) {
                return $existingDomainName;
            }
            array_shift($domainParts);
        }

        return null;
    }
}
