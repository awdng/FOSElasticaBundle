<?php

namespace FOS\ElasticaBundle\Doctrine\Phpcr;

use FOS\ElasticaBundle\Doctrine\AbstractElasticaToModelTransformer;
use Doctrine\ODM\PHPCR\Query\Query;


/**
 * Maps Elastica documents with Doctrine objects
 * This mapper assumes an exact match between
 * elastica documents ids and doctrine object ids
 */
class ElasticaToDocumentTransformer extends AbstractElasticaToModelTransformer
{
    const ENTITY_ALIAS = 'o';

    /**
     * Fetch objects for theses identifier values
     *
     * @param array $identifierValues ids values
     * @param Boolean $hydrate whether or not to hydrate the objects, false returns arrays
     * @return array of objects or arrays
     */
    protected function findByIdentifiers(array $identifierValues, $hydrate)
    {
        if (empty($identifierValues)) {
            return array();
        }

        $documentManager = $this->registry
            ->getManagerForClass($this->objectClass);

        return $documentManager->findMany(null, $identifierValues)->toArray();
    }

    /**
     * Retrieves a query builder to be used for querying by identifiers
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getEntityQueryBuilder()
    {
        $repository = $this->registry
            ->getManagerForClass($this->objectClass)
            ->getRepository($this->objectClass);

        return $repository->{$this->options['query_builder_method']}(static::ENTITY_ALIAS);
    }
}
