<?php

namespace PrestaShop\Module\PsAccounts\Repository;

use Db;
use DbQuery;

class GoogleTaxonomyRepository
{
    /**
     * @var Db
     */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    /**
     * @return DbQuery
     */
    public function getBaseQuery()
    {
        $query = new DbQuery();

        $query->from('fb_category_match', 'cm');

        return $query;
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getTaxonomyCategories($offset, $limit)
    {
        $query = $this->getBaseQuery();

        $query->select('cm.id_category, cm.google_category_id')
            ->limit($limit, $offset);

        return $this->db->executeS($query);
    }

    /**
     * @param int $offset
     *
     * @return int
     */
    public function getRemainingTaxonomyRepositories($offset)
    {
        $query = $this->getBaseQuery();

        $query->select('(COUNT(cm.id_category) - ' . (int) $offset . ') as count');

        return (int) $this->db->getValue($query);
    }
}