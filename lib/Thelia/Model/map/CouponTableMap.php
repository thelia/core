<?php

namespace Thelia\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'coupon' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Thelia.Model.map
 */
class CouponTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Thelia.Model.map.CouponTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('coupon');
        $this->setPhpName('Coupon');
        $this->setClassname('Thelia\\Model\\Coupon');
        $this->setPackage('Thelia.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('CODE', 'Code', 'VARCHAR', true, 45, null);
        $this->addColumn('ACTION', 'Action', 'VARCHAR', true, 255, null);
        $this->addColumn('VALUE', 'Value', 'FLOAT', true, null, null);
        $this->addColumn('USED', 'Used', 'TINYINT', false, null, null);
        $this->addColumn('AVAILABLE_SINCE', 'AvailableSince', 'TIMESTAMP', false, null, null);
        $this->addColumn('DATE_LIMIT', 'DateLimit', 'TIMESTAMP', false, null, null);
        $this->addColumn('ACTIVATE', 'Activate', 'TINYINT', false, null, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', true, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CouponRule', 'Thelia\\Model\\CouponRule', RelationMap::ONE_TO_MANY, array('id' => 'coupon_id', ), 'CASCADE', null, 'CouponRules');
    } // buildRelations()

} // CouponTableMap
