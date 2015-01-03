<?php

namespace FOS\ElasticaBundle\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder as Builder;
use FOS\ElasticaBundle\Doctrine\AbstractProvider;
use FOS\ElasticaBundle\Exception\InvalidArgumentTypeException;

class Provider extends AbstractProvider
{
    const ENTITY_ALIAS = 'a';

    /**
     * Disables logging and returns the logger that was previously set.
     *
     * @return mixed
     */
    protected function disableLogging()
    {
        $configuration = $this->managerRegistry
            ->getManagerForClass($this->objectClass)
            ->getConnection()
            ->getConfiguration();

        $logger = $configuration->getLoggerCallable();
        $configuration->setLoggerCallable(null);

        return $logger;
    }

    /**
     * Reenables the logger with the previously returned logger from disableLogging();
     *
     * @param mixed $logger
     * @return mixed
     */
    protected function enableLogging($logger)
    {
        $configuration = $this->managerRegistry
            ->getManagerForClass($this->objectClass)
            ->getConnection()
            ->getConfiguration();

        $configuration->setLoggerCallable($logger);
    }

    /**
     * @see FOS\ElasticaBundle\Doctrine\AbstractProvider::countObjects()
     */
    protected function countObjects($queryBuilder)
    {
        if (!$queryBuilder instanceof Builder) {
            throw new InvalidArgumentTypeException($queryBuilder, 'Doctrine\ODM\PHPCR\Query\Builder');
        }

        return count($queryBuilder->getQuery()->getResult());
    }

    /**
     * @see FOS\ElasticaBundle\Doctrine\AbstractProvider::fetchSlice()
     */
    protected function fetchSlice($queryBuilder, $limit, $offset)
    {
        if (!$queryBuilder instanceof Builder) {
            throw new InvalidArgumentTypeException($queryBuilder, 'Doctrine\ODM\PHPCR\Query\Builder');
        }

        $result = $queryBuilder
            ->getQuery()
            ->getResult()
            ->toArray();

        return array_slice($result, $offset, $limit);
    }

    /**
     * @see FOS\ElasticaBundle\Doctrine\AbstractProvider::createQueryBuilder()
     */
    protected function createQueryBuilder()
    {
        return $this->managerRegistry
            ->getManagerForClass($this->objectClass)
            ->getRepository($this->objectClass)
            ->{$this->options['query_builder_method']}(static::ENTITY_ALIAS);
    }
}
