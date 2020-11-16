<?php

namespace PrestaShop\Module\PsAccounts\Repository;

use Context;
use Db;
use DbQuery;
use Employee;
use mysqli_result;
use PDOStatement;
use PrestaShopDatabaseException;
use Product;
use SpecificPrice;

class ProductRepository
{
    /**
     * @var Context
     */
    private $context;
    /**
     * @var Db
     */
    private $db;

    public function __construct(Db $db, Context $context)
    {
        $this->db = $db;
        $this->context = $context;

        if (!$this->context->employee instanceof Employee) {
            if (($employees = Employee::getEmployees()) !== false) {
                $this->context->employee = new Employee($employees[0]['id_employee']);
            }
        }
    }

    /**
     * @param int $shopId
     *
     * @return DbQuery
     */
    private function getBaseQuery($shopId)
    {
        $query = new DbQuery();

        $query->from('product_shop', 'ps')
            ->leftJoin('product', 'p', 'ps.id_product = p.id_product')
            ->leftJoin('product_attribute_shop', 'pas', 'pas.id_product = ps.id_product AND pas.id_shop = ' . (int) $shopId)
            ->leftJoin('product_attribute', 'pa', 'pas.id_product_attribute = pa.id_product_attribute')
            ->leftJoin('product_lang', 'pl', 'pl.id_product = ps.id_product')
            ->innerJoin('lang', 'l', 'pl.id_lang = l.id_lang AND l.active = 1')
            ->leftJoin('category_lang', 'cl', 'ps.id_category_default = cl.id_category AND cl.id_lang = pl.id_lang')
            ->leftJoin('stock_available', 'sa', 'sa.id_product = p.id_product AND sa.id_product_attribute = IFNULL(pas.id_product_attribute, 0) AND sa.id_shop = ' . (int) $shopId)
            ->leftJoin('manufacturer', 'm', 'p.id_manufacturer = m.id_manufacturer')

            ->leftJoin('product_attribute_combination', 'pac', 'pac.id_product_attribute = pas.id_product_attribute')
            ->leftJoin('attribute', 'a', 'a.id_attribute = pac.id_attribute')
            ->leftJoin('attribute_group_lang', 'agl', 'agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = l.id_lang')
            ->leftJoin('attribute_lang', 'al', 'al.id_attribute = pac.id_attribute AND al.id_lang = l.id_lang')

            ->leftJoin('feature_product', 'fp', 'fp.id_product = ps.id_product')
            ->leftJoin('feature_lang', 'fl', 'fl.id_feature = fp.id_feature AND fl.id_lang = l.id_lang')
            ->leftJoin('feature_value_lang', 'fvl', 'fvl.id_feature_value = fp.id_feature_value AND fvl.id_lang = l.id_lang')

            ->leftJoin('image_shop', 'imgs', 'imgs.id_product = ps.id_product AND imgs.id_shop = ps.id_shop')
            ->leftJoin('product_attribute_image', 'pai', 'pai.id_product_attribute = pas.id_product_attribute')

            ->where('ps.id_shop = ' . (int) $shopId)
            ->where('pl.id_shop = ' . (int) $shopId)

            ->groupBy('ps.id_product, pas.id_product_attribute, l.id_lang')
            ->orderBy('p.id_product, pas.id_product_attribute');

        return $query;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param string $langIso
     *
     * @return array|bool|mysqli_result|PDOStatement|resource|null
     *
     * @throws PrestaShopDatabaseException
     */
    public function getProducts($offset, $limit, $langIso = null)
    {
        $query = $this->getBaseQuery($this->context->shop->id)
            ->select('p.id_product, IFNULL(pas.id_product_attribute, 0) as id_attribute,
            pl.name, pl.description, pl.description_short, pl.link_rewrite, l.iso_code, cl.name as default_category,
            ps.id_category_default, IFNULL(pa.reference, p.reference) as reference, IFNULL(pa.upc, p.upc) as upc,
            IFNULL(pa.ean13, p.ean13) as ean, IFNULL(pa.isbn, p.isbn) as isbn,
            ps.condition, ps.visibility, ps.active, sa.quantity, m.name as manufacturer,
            (p.weight + IFNULL(pas.weight, 0)) as weight, (ps.price + IFNULL(pas.price, 0)) as price_tax_excl,
            p.date_add as created_at, p.date_upd as updated_at,
            IFNULL(GROUP_CONCAT(DISTINCT agl.name, ":", al.name SEPARATOR ";"), "") as attributes,
            IFNULL(GROUP_CONCAT(DISTINCT fl.name, ":", fvl.value SEPARATOR ";"), "") as features,
            GROUP_CONCAT(DISTINCT imgs.id_image, ":", IFNULL(imgs.cover, 0) SEPARATOR ";") as images,
            GROUP_CONCAT(DISTINCT pai.id_image SEPARATOR ";") as attribute_images');

        if ($langIso !== null && is_string($langIso)) {
            $query->where('l.iso_code = "' . pSQL($langIso) . '"');
        }

        $query->limit($limit, $offset);

        return $this->db->executeS($query);
    }

    /**
     * @param int $offset
     * @param string $langIso
     *
     * @return int
     *
     * @throws PrestaShopDatabaseException
     */
    public function getRemainingProductsCount($offset, $langIso = null)
    {
        $products = $this->getProducts(0, 1000000000, $langIso);

        if (!is_array($products) || empty($products)) {
            return 0;
        }

        return (int) (count($products) - $offset);
    }

    /**
     * @param int $productId
     * @param int $attributeId
     *
     * @return float
     */
    public function getPriceTaxExcluded($productId, $attributeId)
    {
        return Product::getPriceStatic($productId, false, $attributeId, 6, null, false, false);
    }

    /**
     * @param int $productId
     * @param int $attributeId
     *
     * @return float
     */
    public function getPriceTaxIncluded($productId, $attributeId)
    {
        return Product::getPriceStatic($productId, true, $attributeId, 6, null, false, false);
    }

    /**
     * @param int $productId
     * @param int $attributeId
     *
     * @return float
     */
    public function getSalePriceTaxExcluded($productId, $attributeId)
    {
        return Product::getPriceStatic($productId, false, $attributeId, 6);
    }

    /**
     * @param int $productId
     * @param int $attributeId
     *
     * @return float
     */
    public function getSalePriceTaxIncluded($productId, $attributeId)
    {
        return Product::getPriceStatic($productId, true, $attributeId, 6);
    }

    /**
     * @param int $productId
     * @param int $attributeId
     *
     * @return string
     */
    public function getSaleDate($productId, $attributeId)
    {
        $specific_price = SpecificPrice::getSpecificPrice(
            (int) $productId,
            $this->context->shop->id,
            0,
            0,
            0,
            1,
            $attributeId
        );

        if (is_array($specific_price) && array_key_exists('to', $specific_price)) {
            return $specific_price['from'] . '/' . $specific_price['to'];
        }

        return '';
    }
}