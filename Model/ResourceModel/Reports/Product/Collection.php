<?php
/**
 * Faonni
 *  
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade module to newer
 * versions in the future.
 * 
 * @package     Faonni_ProductMostSold
 * @copyright   Copyright (c) 2016 Karliuka Vitalii(karliuka.vitalii@gmail.com) 
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Faonni\ProductMostSold\Model\ResourceModel\Reports\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Sales\Model\Order;

/**
 * Catalog product most sold items collection
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Collection extends ProductCollection
{
    /**
     * Add orders count
     *
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function addOrdersCount($from='', $to='')
    {
        $connection = $this->getConnection();
		$this->getSelect()
			->join(
				['i' => $this->getTable('sales_order_item')],
				'e.entity_id = i.product_id',
				['ordered_qty' => 'SUM(i.qty_ordered)']
			)
			->join(
				['o' => $this->getTable('sales_order')],
				'o.entity_id = i.order_id AND ' . 
				$connection->quoteInto("o.state <> ?", Order::STATE_CANCELED),
				[]
			)			
			->where('i.parent_item_id IS NULL' )
			->group('e.entity_id')
			->order('ordered_qty ' . self::SORT_ORDER_DESC);

        if ($from != '' && $to != '') {
            $this->getSelect()
				->where('i.created_at >= ?', $from)
				->where('i.created_at <= ?', $to);
        }
        return $this;
    }
}
