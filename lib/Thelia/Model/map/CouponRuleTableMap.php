<?php

namespace Thelia\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'coupon_rule' table.
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
class CouponRuleTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Thelia.Model.map.CouponRuleTableMap';

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
        $this->setName('coupon_rule');
        $this->setPhpName('CouponRule');
        $this->setClassname('Thelia\\Model\\CouponRule');
        $this->setPackage('Thelia.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('COUPON_ID', 'CouponId', 'INTEGER', 'coupon', 'ID', true, null, null);
        $this->addColumn('CONTROLLER', 'Controller', 'VARCHAR', false, 255, null);
        $this->addColumn('OPERATION', 'Operation', 'VARCHAR', false, 255, null);
        $this->addColumn('VALUE', 'Value', 'FLOAT', false, null, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', true, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Coupon', 'Thelia\\Model\\Coupon', RelationMap::MANY_TO_ONE, array('coupon_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // CouponRuleTableMap
