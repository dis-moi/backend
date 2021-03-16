<?php

namespace App\Repository;

use App\Entity\DomainName;
use Doctrine\ORM\EntityManagerInterface;

class DomainNameRepository extends BaseRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
    }

    public function getClassName()
    {
        return DomainName::class;
    }

    /**
     * @return DomainName?
     */
    public function findByName(string $domainName): ?DomainName
    {
        return $this->repository->findOneBy([
      'name' => $domainName,
    ]);
    }

    public function findOrCreate(string $domainName): DomainName
    {
        $existing = $this->findByName($domainName);
        if ($existing) {
            return $existing;
        } else {
            $newDomain = new DomainName($domainName);
            $this->entityManager->persist($newDomain);
            $this->entityManager->flush();

            return $newDomain;
        }
    }

    /**
     * @param string $host an hostname, for example: www.pref.okinawa.jp
     */
    public function findMostSpecificFromHost(string $host): ?DomainName
    {
        $domainParts = explode('.', $host);

        $existingDomainName = null;
        while (count($domainParts) > 0) {
            /** @var DomainName|null $existingDomainName */
            $existingDomainName = $this->findByName(join('.', $domainParts));
            if ($existingDomainName) {
                return $existingDomainName;
            } else {
                array_shift($domainParts);
            }
        }

        return null;
    }
}
