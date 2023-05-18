<?php
declare(strict_types=1);

namespace Kosher\OrderImport\Service\OrderFile;

use Kosher\WineStore\Query\GetWineStoreIdQuery;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Xml\Parser;

class OrderXmlFileAdjustmentService
{
    private int $lastPaymentTransactionId;
    private int $lastOrderInvoiceItemId;
    private int $lastOrderInvoiceId;
    private int $lastOrderShipmentItemId;
    private int $lastOrderShipmentId;
    private int $lastOrderItemId;
    private int $lastStatusHistoryId;
    private int $lastPaymentId;
    private int $shippingAddressId = 0;
    private int $billingAddressId = 0;
    private int $customerId;
    private int $lastOrderId;
    private array $completeOrders = [];

    /**
     * @var Parser
     */
    private Parser $parser;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var RewriteImportOrderFileService
     */
    private RewriteImportOrderFileService $rewriteImportOrderFileService;

    /**
     * @var GetWineStoreIdQuery
     */
    private GetWineStoreIdQuery $getWineStoreIdQuery;

    /**
     * @param Parser $parser
     * @param ResourceConnection $resourceConnection
     * @param RewriteImportOrderFileService $rewriteImportOrderFileService
     * @param GetWineStoreIdQuery $getWineStoreIdQuery
     */
    public function __construct(
        Parser $parser,
        ResourceConnection $resourceConnection,
        RewriteImportOrderFileService $rewriteImportOrderFileService,
        GetWineStoreIdQuery $getWineStoreIdQuery
    ) {
        $this->parser = $parser;
        $this->resourceConnection = $resourceConnection;
        $this->rewriteImportOrderFileService = $rewriteImportOrderFileService;
        $this->getWineStoreIdQuery = $getWineStoreIdQuery;
    }

    /**
     * @param array $dataFile
     * @return void
     * @throws FileSystemException
     */
    public function execute(array $dataFile): void
    {
        $path = $dataFile['path'] . $dataFile['file'];
        $arrayData = $this->parser->load($path)->xmlToArray();
        $this->getCompleteOrders($arrayData);
        $this->getLastOrderId();
        $this->getLastAddressId();
        $this->getLastPaymentId();
        $this->getLastStatusHistoryId();
        $this->getLastOrderItemId();
        $this->lastOrderShipmentId();
        $this->lastOrderShipmentItemId();
        $this->lastOrderInvoiceId();
        $this->lastOrderInvoiceItemId();
        $this->lastPaymentTransactionId();
        $this->orderAdjustmentInfo();

        $this->rewriteImportOrderFileService->execute($path, $this->completeOrders);
    }

    /**
     * @param array $orders
     * @return void
     */
    private function getCompleteOrders(array $orders): void
    {
        $order = $orders['records']['order'];
        foreach ($order as $key => $item) {
            if ($order[$key]['fields']['status'] !== 'complete') {
                unset($order[$key]);
            }
        }

        $this->completeOrders = $order;
    }

    /**
     * @return void
     */
    private function orderAdjustmentInfo(): void
    {
        $i = 1;
        foreach ($this->completeOrders as $key => $order) {
            $this->lastOrderId += $i;
            $this->completeOrders[$key]['fields']['entity_id'] = $this->lastOrderId;
            $this->completeOrders[$key]['fields']['store_id'] = (int)$this->getWineStoreIdQuery->execute();
            if (!empty($order['fields']['customer_id'])) {
                $customerMail = $order['fields']['customer_email'];
                $this->customerId = $this->getCustomerId($customerMail);
                $this->completeOrders[$key]['fields']['customer_id'] = $this->customerId;
            }

            $this->addressAdjustment($key, $order, $i);
            $this->paymentAdjustment($key, $i);
            if (!empty($order['statuseshistory']['statushistory'])) {
                $this->statusHistoryAdjustment($key, $order);
            }

            $this->orderItemsAdjustment($key, $order);
            $this->orderShipmentAdjustment($key, $order);
            $this->invoiceOrderAdjustment($key, $order);
            $this->paymentTransactionAdjustment($key, $order);
            $this->completeOrders[$key]['fields']['billing_address_id'] = $this->billingAddressId;
            $this->completeOrders[$key]['fields']['shipping_address_id'] = $this->shippingAddressId;
            $this->completeOrders[$key]['fields']['quote_id'] = null;
            $this->completeOrders[$key]['fields']['quote_address_id'] = null;
            $i++;
        }
    }

    /**
     * @param int $orderKey
     * @param array $order
     * @return void
     */
    private function paymentTransactionAdjustment(int $orderKey, array $order): void
    {
        if (empty($order['paymentstransaction']['paymenttransaction'])) {
            return;
        }

        $this->lastPaymentTransactionId++;
        $this->completeOrders[$orderKey]['paymentstransaction']['paymenttransaction']['transaction_id'] = $this->lastPaymentTransactionId;
        $this->completeOrders[$orderKey]['paymentstransaction']['paymenttransaction']['parent_id'] = $this->lastOrderInvoiceId;
        $this->completeOrders[$orderKey]['paymentstransaction']['paymenttransaction']['order_id'] = $this->lastOrderId;
        $this->completeOrders[$orderKey]['paymentstransaction']['paymenttransaction']['payment_id'] = $this->lastPaymentId;
    }

    /**
     * @return void
     */
    private function lastPaymentTransactionId(): void
    {
        $this->lastPaymentTransactionId = $this->getLastRecordId('sales_payment_transaction', 'transaction_id');
    }

    /**
     * @param int $orderKey
     * @param array $order
     * @return void
     */
    private function invoiceOrderAdjustment(int $orderKey, array $order): void
    {
        if (empty($order['invoices']['invoice'])) {
            return;
        }

        $this->lastOrderInvoiceId++;
        $invoices = $order['invoices']['invoice'];
        foreach ($invoices as $key => $invoice) {
            if ($key == 'fields') {
                $this->completeOrders[$orderKey]['invoices']['invoice'][$key]['entity_id'] = $this->lastOrderInvoiceId;
                $this->completeOrders[$orderKey]['invoices']['invoice'][$key]['billing_address_id'] = $this->billingAddressId;
                $this->completeOrders[$orderKey]['invoices']['invoice'][$key]['shipping_address_id'] = $this->shippingAddressId;
                $this->completeOrders[$orderKey]['invoices']['invoice'][$key]['order_id'] = $this->lastOrderId;
            }

            if ($key == 'items') {
                foreach ($invoice['item'] as $number => $itemInvoice) {
                    $this->lastOrderInvoiceItemId++;
                    if (is_array($itemInvoice)) {
                        $sku = $itemInvoice['sku'];
                        $this->completeOrders[$orderKey]['invoices']['invoice'][$key]['item'][$number]['entity_id'] = $this->lastOrderInvoiceItemId;
                        $this->completeOrders[$orderKey]['invoices']['invoice'][$key]['item'][$number]['parent_id'] = $this->lastOrderInvoiceId;
                        $this->completeOrders[$orderKey]['invoices']['invoice'][$key]['item'][$number]['product_id'] = $this->getProductIdBySku($sku);
                        $order_item_id = $this->completeOrders[$orderKey]['items']['item'][$number]['item_id'];
                        $this->completeOrders[$orderKey]['invoices']['invoice'][$key]['item'][$number]['order_item_id'] = $order_item_id;
                    }

                    if ($number == 'entity_id') {
                        $this->completeOrders[$orderKey]['invoices']['invoice'][$key]['item'][$number] = $this->lastOrderInvoiceItemId;
                    }

                    if ($number == 'parent_id') {
                        $this->completeOrders[$orderKey]['invoices']['invoice'][$key]['item'][$number] = $this->lastOrderInvoiceId;
                    }

                    if ($number == 'product_id') {
                        $sku = $invoice['item']['sku'];
                        $this->completeOrders[$orderKey]['invoices']['invoice'][$key]['item'][$number] = $this->getProductIdBySku($sku);
                    }

                    if ($number == 'order_item_id') {
                        $order_item_id = $this->completeOrders[$orderKey]['items']['item']['item_id'];
                        $this->completeOrders[$orderKey]['invoices']['invoice'][$key]['item'][$number] = $order_item_id;
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    private function lastOrderInvoiceId(): void
    {
        $this->lastOrderInvoiceId = $this->getLastRecordId('sales_invoice');
    }

    /**
     * @return void
     */
    private function lastOrderInvoiceItemId(): void
    {
        $this->lastOrderInvoiceItemId = $this->getLastRecordId('sales_invoice_item');
    }

    /**
     * @param int $orderKey
     * @param array $order
     * @return void
     */
    private function orderShipmentAdjustment(int $orderKey, array $order): void
    {
        if (empty($order['shipments']['shipment'])) {
            return;
        }

        $orderShipment = $order['shipments']['shipment'];
        $this->lastOrderShipmentId++;
        foreach ($orderShipment as $key => $shipment) {
            $this->lastOrderShipmentItemId++;
            if ($key == 'fields') {
                $this->completeOrders[$orderKey]['shipments']['shipment'][$key]['entity_id'] = $this->lastOrderShipmentId;
                $this->completeOrders[$orderKey]['shipments']['shipment'][$key]['order_id'] = $this->lastOrderId;
                $this->completeOrders[$orderKey]['shipments']['shipment'][$key]['customer_id'] = $this->customerId;
                $this->completeOrders[$orderKey]['shipments']['shipment'][$key]['shipping_address_id'] = $this->shippingAddressId;
                $this->completeOrders[$orderKey]['shipments']['shipment'][$key]['billing_address_id'] = $this->billingAddressId;
            }
            if ($key == 'items') {
                $this->completeOrders[$orderKey]['shipments']['shipment'][$key]['item']['entity_id'] = $this->lastOrderShipmentItemId;
                $this->completeOrders[$orderKey]['shipments']['shipment'][$key]['item']['parent_id'] = $this->lastOrderShipmentId;
                $orderItems = $this->completeOrders[$orderKey]['items']['item'];
                if (!empty($orderItems[0])) {
                    $product_id = $orderItems[0]['product_id'];
                    $item_id = $orderItems[0]['item_id'];
                } else {
                    $product_id = $orderItems['product_id'];
                    $item_id = $orderItems['item_id'];
                }

                $this->completeOrders[$orderKey]['shipments']['shipment'][$key]['item']['product_id'] = $product_id;
                $this->completeOrders[$orderKey]['shipments']['shipment'][$key]['item']['order_item_id'] = $item_id;
            }
        }
    }

    /**
     * @return void
     */
    private function lastOrderShipmentItemId(): void
    {
        $this->lastOrderShipmentItemId = $this->getLastRecordId('sales_shipment_item');
    }

    /**
     * @return void
     */
    private function lastOrderShipmentId(): void
    {
        $this->lastOrderShipmentId = $this->getLastRecordId('sales_shipment');
    }

    /**
     * @param int $orderKey
     * @param array $order
     * @return void
     */
    private function orderItemsAdjustment(int $orderKey, array $order): void
    {
        $orderItems = $order['items']['item'];
        foreach ($orderItems as $key => $item) {
            $this->lastOrderItemId++;
            if (is_array($item)) {
                $this->completeOrders[$orderKey]['items']['item'][$key]['item_id'] = $this->lastOrderItemId;
                $this->completeOrders[$orderKey]['items']['item'][$key]['order_id'] = $this->lastOrderId;
                $this->completeOrders[$orderKey]['items']['item'][$key]['quote_item_id'] = null;
                $sku = $item['sku'];
                $this->completeOrders[$orderKey]['items']['item'][$key]['product_id'] = $this->getProductIdBySku($sku);
            }

            if ($key == 'quote_item_id') {
                $this->completeOrders[$orderKey]['items']['item']['quote_item_id'] = null;
            }
            if ($key == 'item_id') {
                $this->completeOrders[$orderKey]['items']['item']['item_id'] = $this->lastOrderItemId;
            }
            if ($key == 'order_id') {
                $this->completeOrders[$orderKey]['items']['item']['order_id'] = $this->lastOrderId;
            }
            if ($key == 'product_id') {
                $sku = $orderItems['sku'];
                $this->completeOrders[$orderKey]['items']['item']['product_id'] = $this->getProductIdBySku($sku);
            }
        }
    }

    /**
     * @param string $sku
     * @return int
     */
    private function getProductIdBySku(string $sku): int
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from('catalog_product_entity', 'entity_id')->where('sku = ?', $sku);

        return (int)$connection->fetchOne($select);
    }

    /**
     * @return void
     */
    private function getLastOrderItemId(): void
    {
        $this->lastOrderItemId = $this->getLastRecordId('sales_order_item', 'item_id');
    }

    /**
     * @param int $orderKey
     * @param array $order
     * @return void
     */
    private function statusHistoryAdjustment(int $orderKey, array $order): void
    {
        $statusHistory = $order['statuseshistory']['statushistory'];
        foreach ($statusHistory as $key => $history) {
            $this->lastStatusHistoryId += 1;
            if ($key == 'parent_id') {
                $this->completeOrders[$orderKey]['statuseshistory']['statushistory']['parent_id'] = $this->lastOrderId;
            }
            if ($key == 'entity_id') {
                $this->completeOrders[$orderKey]['statuseshistory']['statushistory']['entity_id'] = $this->lastStatusHistoryId;
            }

            if (is_array($history)) {
                $this->completeOrders[$orderKey]['statuseshistory']['statushistory'][$key]['parent_id'] = $this->lastOrderId;
                $this->completeOrders[$orderKey]['statuseshistory']['statushistory'][$key]['entity_id'] = $this->lastStatusHistoryId;
            }
        }
    }

    /**
     * @return void
     */
    private function getLastStatusHistoryId(): void
    {
        $this->lastStatusHistoryId = $this->getLastRecordId('sales_order_status_history');
    }

    /**
     * @return void
     */
    private function getLastOrderId(): void
    {
        $this->lastOrderId = $this->getLastRecordId('sales_order');
    }

    /**
     * @param int $orderKey
     * @param int $count
     * @return void
     */
    private function paymentAdjustment(int $orderKey, int $count): void
    {
        $this->lastPaymentId += $count;
        $this->completeOrders[$orderKey]['payments']['payment']['entity_id'] = $this->lastPaymentId;
        $this->completeOrders[$orderKey]['payments']['payment']['parent_id'] = $this->lastOrderId;
    }

    /**
     * @return void
     */
    private function getLastPaymentId(): void
    {
        $this->lastPaymentId = $this->getLastRecordId('sales_order_payment');
    }

    /**
     * @param int $orderKey
     * @param array $order
     * @param int $addressCount
     * @return void
     */
    private function addressAdjustment(int $orderKey, array $order, int $addressCount): void
    {
        $orderAddress = $order['addresses']['address'];
        $this->billingAddressId += $addressCount;
        $this->shippingAddressId = $this->billingAddressId + 1;
        $customerEmail = $orderAddress[0]['email'];
        $this->customerId = $this->getCustomerId($customerEmail);
        foreach ($orderAddress as $key => $address) {
            if ($address['address_type'] == 'billing') {
                $this->completeOrders[$orderKey]['addresses']['address'][$key]['entity_id'] = $this->billingAddressId;
                $this->completeOrders[$orderKey]['addresses']['address'][$key]['parent_id'] = $this->lastOrderId;
                $this->completeOrders[$orderKey]['addresses']['address'][$key]['customer_id'] = $this->customerId;
                $this->completeOrders[$orderKey]['addresses']['address'][$key]['quote_address_id'] = null;
            }

            if ($address['address_type'] == 'shipping') {
                $this->completeOrders[$orderKey]['addresses']['address'][$key]['entity_id'] = $this->shippingAddressId;
                $this->completeOrders[$orderKey]['addresses']['address'][$key]['parent_id'] = $this->lastOrderId;
                $this->completeOrders[$orderKey]['addresses']['address'][$key]['customer_id'] = $this->customerId;
                $this->completeOrders[$orderKey]['addresses']['address'][$key]['quote_address_id'] = null;
            }
        }
    }

    /**
     * @return void
     */
    private function getLastAddressId(): void
    {
        if ($this->billingAddressId == 0) {
            $this->billingAddressId = $this->getLastRecordId('sales_order_address');
        }
    }

    /**
     * @param string $table
     * @param string $coll
     * @return int
     */
    private function getLastRecordId(string $table, string $coll = 'entity_id'): int
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from($table, $coll)->order($coll . ' desc');
        $entity = array_first($connection->fetchAll($select));

        return (int)$entity[$coll];
    }

    /**
     * @param string $email
     * @return int
     */
    private function getCustomerId(string $email): int
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from('customer_entity', 'entity_id')->where('email = ?', $email);

        return (int)$connection->fetchOne($select);
    }
}
