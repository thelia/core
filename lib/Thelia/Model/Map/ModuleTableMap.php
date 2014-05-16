<?php

namespace Thelia\Model\Map;

use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;
use Thelia\Model\Module;
use Thelia\Model\ModuleQuery;


/**
 * This class defines the structure of the 'module' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class ModuleTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Thelia.Model.Map.ModuleTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'module';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Thelia\\Model\\Module';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Thelia.Model.Module';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 8;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 8;

    /**
     * the column name for the ID field
     */
    const ID = 'module.ID';

    /**
     * the column name for the CODE field
     */
    const CODE = 'module.CODE';

    /**
     * the column name for the TYPE field
     */
    const TYPE = 'module.TYPE';

    /**
     * the column name for the ACTIVATE field
     */
    const ACTIVATE = 'module.ACTIVATE';

    /**
     * the column name for the POSITION field
     */
    const POSITION = 'module.POSITION';

    /**
     * the column name for the FULL_NAMESPACE field
     */
    const FULL_NAMESPACE = 'module.FULL_NAMESPACE';

    /**
     * the column name for the CREATED_AT field
     */
    const CREATED_AT = 'module.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const UPDATED_AT = 'module.UPDATED_AT';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    // i18n behavior

    /**
     * The default locale to use for translations.
     *
     * @var string
     */
    const DEFAULT_LOCALE = 'en_US';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'Code', 'Type', 'Activate', 'Position', 'FullNamespace', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'code', 'type', 'activate', 'position', 'fullNamespace', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(ModuleTableMap::ID, ModuleTableMap::CODE, ModuleTableMap::TYPE, ModuleTableMap::ACTIVATE, ModuleTableMap::POSITION, ModuleTableMap::FULL_NAMESPACE, ModuleTableMap::CREATED_AT, ModuleTableMap::UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('ID', 'CODE', 'TYPE', 'ACTIVATE', 'POSITION', 'FULL_NAMESPACE', 'CREATED_AT', 'UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('id', 'code', 'type', 'activate', 'position', 'full_namespace', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Code' => 1, 'Type' => 2, 'Activate' => 3, 'Position' => 4, 'FullNamespace' => 5, 'CreatedAt' => 6, 'UpdatedAt' => 7, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'code' => 1, 'type' => 2, 'activate' => 3, 'position' => 4, 'fullNamespace' => 5, 'createdAt' => 6, 'updatedAt' => 7, ),
        self::TYPE_COLNAME       => array(ModuleTableMap::ID => 0, ModuleTableMap::CODE => 1, ModuleTableMap::TYPE => 2, ModuleTableMap::ACTIVATE => 3, ModuleTableMap::POSITION => 4, ModuleTableMap::FULL_NAMESPACE => 5, ModuleTableMap::CREATED_AT => 6, ModuleTableMap::UPDATED_AT => 7, ),
        self::TYPE_RAW_COLNAME   => array('ID' => 0, 'CODE' => 1, 'TYPE' => 2, 'ACTIVATE' => 3, 'POSITION' => 4, 'FULL_NAMESPACE' => 5, 'CREATED_AT' => 6, 'UPDATED_AT' => 7, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'code' => 1, 'type' => 2, 'activate' => 3, 'position' => 4, 'full_namespace' => 5, 'created_at' => 6, 'updated_at' => 7, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('module');
        $this->setPhpName('Module');
        $this->setClassName('\\Thelia\\Model\\Module');
        $this->setPackage('Thelia.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('CODE', 'Code', 'VARCHAR', true, 55, null);
        $this->addColumn('TYPE', 'Type', 'TINYINT', true, null, null);
        $this->addColumn('ACTIVATE', 'Activate', 'TINYINT', false, null, null);
        $this->addColumn('POSITION', 'Position', 'INTEGER', false, null, null);
        $this->addColumn('FULL_NAMESPACE', 'FullNamespace', 'VARCHAR', false, 255, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('OrderRelatedByPaymentModuleId', '\\Thelia\\Model\\Order', RelationMap::ONE_TO_MANY, array('id' => 'payment_module_id', ), 'RESTRICT', 'RESTRICT', 'OrdersRelatedByPaymentModuleId');
        $this->addRelation('OrderRelatedByDeliveryModuleId', '\\Thelia\\Model\\Order', RelationMap::ONE_TO_MANY, array('id' => 'delivery_module_id', ), 'RESTRICT', 'RESTRICT', 'OrdersRelatedByDeliveryModuleId');
        $this->addRelation('AreaDeliveryModule', '\\Thelia\\Model\\AreaDeliveryModule', RelationMap::ONE_TO_MANY, array('id' => 'delivery_module_id', ), 'CASCADE', 'RESTRICT', 'AreaDeliveryModules');
        $this->addRelation('ProfileModule', '\\Thelia\\Model\\ProfileModule', RelationMap::ONE_TO_MANY, array('id' => 'module_id', ), 'CASCADE', 'RESTRICT', 'ProfileModules');
        $this->addRelation('ModuleImage', '\\Thelia\\Model\\ModuleImage', RelationMap::ONE_TO_MANY, array('id' => 'module_id', ), 'CASCADE', 'RESTRICT', 'ModuleImages');
        $this->addRelation('CouponModule', '\\Thelia\\Model\\CouponModule', RelationMap::ONE_TO_MANY, array('id' => 'module_id', ), 'CASCADE', null, 'CouponModules');
        $this->addRelation('OrderCouponModule', '\\Thelia\\Model\\OrderCouponModule', RelationMap::ONE_TO_MANY, array('id' => 'module_id', ), 'CASCADE', null, 'OrderCouponModules');
        $this->addRelation('ModuleI18n', '\\Thelia\\Model\\ModuleI18n', RelationMap::ONE_TO_MANY, array('id' => 'id', ), 'CASCADE', null, 'ModuleI18ns');
        $this->addRelation('Coupon', '\\Thelia\\Model\\Coupon', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'Coupons');
        $this->addRelation('OrderCoupon', '\\Thelia\\Model\\OrderCoupon', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'OrderCoupons');
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
            'i18n' => array('i18n_table' => '%TABLE%_i18n', 'i18n_phpname' => '%PHPNAME%I18n', 'i18n_columns' => 'title, description, chapo, postscriptum', 'locale_column' => 'locale', 'locale_length' => '5', 'default_locale' => '', 'locale_alias' => '', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to module     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                AreaDeliveryModuleTableMap::clearInstancePool();
                ProfileModuleTableMap::clearInstancePool();
                ModuleImageTableMap::clearInstancePool();
                CouponModuleTableMap::clearInstancePool();
                OrderCouponModuleTableMap::clearInstancePool();
                ModuleI18nTableMap::clearInstancePool();
            }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {

            return (int) $row[
                            $indexType == TableMap::TYPE_NUM
                            ? 0 + $offset
                            : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
                        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? ModuleTableMap::CLASS_DEFAULT : ModuleTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     * @return array (Module object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = ModuleTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = ModuleTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + ModuleTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ModuleTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            ModuleTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = ModuleTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = ModuleTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ModuleTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(ModuleTableMap::ID);
            $criteria->addSelectColumn(ModuleTableMap::CODE);
            $criteria->addSelectColumn(ModuleTableMap::TYPE);
            $criteria->addSelectColumn(ModuleTableMap::ACTIVATE);
            $criteria->addSelectColumn(ModuleTableMap::POSITION);
            $criteria->addSelectColumn(ModuleTableMap::FULL_NAMESPACE);
            $criteria->addSelectColumn(ModuleTableMap::CREATED_AT);
            $criteria->addSelectColumn(ModuleTableMap::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.CODE');
            $criteria->addSelectColumn($alias . '.TYPE');
            $criteria->addSelectColumn($alias . '.ACTIVATE');
            $criteria->addSelectColumn($alias . '.POSITION');
            $criteria->addSelectColumn($alias . '.FULL_NAMESPACE');
            $criteria->addSelectColumn($alias . '.CREATED_AT');
            $criteria->addSelectColumn($alias . '.UPDATED_AT');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(ModuleTableMap::DATABASE_NAME)->getTable(ModuleTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(ModuleTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(ModuleTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new ModuleTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Module or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Module object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ModuleTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Thelia\Model\Module) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ModuleTableMap::DATABASE_NAME);
            $criteria->add(ModuleTableMap::ID, (array) $values, Criteria::IN);
        }

        $query = ModuleQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { ModuleTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { ModuleTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the module table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return ModuleQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Module or Criteria object.
     *
     * @param mixed               $criteria Criteria or Module object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ModuleTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Module object
        }

        if ($criteria->containsKey(ModuleTableMap::ID) && $criteria->keyContainsValue(ModuleTableMap::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ModuleTableMap::ID.')');
        }


        // Set the correct dbName
        $query = ModuleQuery::create()->mergeWith($criteria);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = $query->doInsert($con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

} // ModuleTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
ModuleTableMap::buildTableMap();
