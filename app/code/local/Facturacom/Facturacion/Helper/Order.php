<?php
/**
 * Order Helper for Facturacom Invoicing
 *
 * Class Index
 * – getOrderByNum
 * – getOrderEntity
 * – getOrderByID
 * – getOrderLines
 */
class Facturacom_Facturacion_Helper_Order extends Mage_Core_Helper_Abstract
{
    /**
     * Getting order by order number from Magento
     *
     * @param Int $orderNum
     * @return Object
     */
    public function getOrderByNum($orderNum){
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderNum)->getData();
        $orderData = array(
            'id'                => $order['entity_id'],
            'order_number'      => $order['increment_id'],
            'customer_email'    => $order['customer_email'],
            'total_tax'         => $order['tax_amount'],
            'total_discount'    => abs($order['discount_amount']),
            'total'             => $order['grand_total'],
            'status'            => $order['status'],
        );

        return (object) $orderData;
        // return (object) $order->getData();
    }

    /**
     * Getting entity order by order number from Magento
     *
     * @param Int $orderNum
     * @return Object
     */
    public function getOrderEntity($orderNum){
        $order = Mage::getModel('sales/order')->load($orderNum, 'increment_id');
        return $order;
    }

    /**
     * Getting order by ID from Magento
     *
     * @param Int $orderID
     * @return Object
     */
    public function getOrderByID($orderID){

    }

    /**
     * Getting order lines items by IDs from Magento
     *
     * @param Object $order
     * @return Object
     */
    public function getOrderLines($order){
        $line_items = array();
        $order_items_collection = $order->getItemsCollection()
                            ->addAttributeToSelect('*')
                            ->addAttributeToFilter('product_type', array('eq'=>'simple'))
                            ->load();

        foreach ($order_items_collection as $order_item) {

            $item = Mage::getModel('sales/order_item')->load($order_item->getId())->getData();
            // echo "<pre>";
            // var_dump($item);die;
            $line_row = array(
                'id'        => $item['item_id'],
                'name'      => $item['name'],
                'qty'       => $item['qty_ordered'],
                'price'     => $item['price'] + $item['tax_amount'],
                'discount'  => abs($item['discount_amount']),
            );
            array_push($line_items, $line_row);
        }

        $orderData = $order->getData();
        if($orderData['shipping_method']){
            $shipping = array(
                'id'    => $orderData['shipping_method'],
                'name'  => $orderData['shipping_description'],
                'qty'   => 1,
                'price' => $orderData['shipping_amount'] + $orderData['shipping_tax_amount'],
                'discount' => 0
            );
            array_push($line_items, $shipping);
        }
        return $line_items;
    }



}
