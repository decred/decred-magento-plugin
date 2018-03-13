<?php namespace Decred\Payments\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

use Decred\Payments\Model\Order;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getTable('decred_payments_order');

        if (!$installer->getConnection()->isTableExists($table)) {
            $table = $installer->getConnection()->newTable($table)
                ->setComment('Decred Payments Order Details')
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity'  => true,
                        'primary'   => true,
                        'nullable'  => false,
                        'unsigned'  => true,
                    ]
                )
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true ]
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => Order::STATUS_PENDING ]
                )
                ->addColumn(
                    'txid',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true  ]
                )
                ->addColumn(
                    'address',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false ]
                )
                ->addColumn(
                    'amount',
                    Table::TYPE_DECIMAL,
                    '16,8',
                    ['nullable' => false, 'unsigned' => true ]
                )
                ->addColumn(
                    'confirmations',
                    Table::TYPE_INTEGER,
                    null,
                    [ 'nullable' => true ]
                )
                ->addColumn(
                    'refund_address',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false ]
                )
                ->addColumn(
                    'base_total',
                    Table::TYPE_DECIMAL,
                    '13,4',
                    ['nullable' => false, 'unsigned' => true ]
                )
                ->addColumn(
                    'base_rate',
                    Table::TYPE_DECIMAL,
                    '24,12',
                    ['nullable' => false, 'unsigned' => true ]
                )
                ->addColumn(
                    'base_currency',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false ]
                )
                ->addColumn(
                    'timestamp',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true ]
                );

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }

}
