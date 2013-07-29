<?php

namespace Thelia\Model\Map;

use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;
use Thelia\Model\Stock;
use Thelia\Model\StockQuery;


/**
 * This class defines the structure of the 'stock' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class StockTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Thelia.Model.Map.StockTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'stock';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Thelia\\Model\\Stock';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Thelia.Model.Stock';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 10;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 10;

    /**
     * the column name for the ID field
     */
    const ID = 'stock.ID';

    /**
     * the column name for the COMBINATION_ID field
     */
    const COMBINATION_ID = 'stock.COMBINATION_ID';

    /**
     * the column name for the PRODUCT_ID field
     */
    const PRODUCT_ID = 'stock.PRODUCT_ID';

    /**
     * the column name for the INCREASE field
     */
    const INCREASE = 'stock.INCREASE';

    /**
     * the column name for the QUANTITY field
     */
    const QUANTITY = 'stock.QUANTITY';

    /**
     * the column name for the PROMO field
     */
    const PROMO = 'stock.PROMO';

    /**
     * the column name for the NEWNESS field
     */
    const NEWNESS = 'stock.NEWNESS';

    /**
     * the column name for the WEIGHT field
     */
    const WEIGHT = 'stock.WEIGHT';

    /**
     * the column name for the CREATED_AT field
     */
    const CREATED_AT = 'stock.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const UPDATED_AT = 'stock.UPDATED_AT';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'CombinationId', 'ProductId', 'Increase', 'Quantity', 'Promo', 'Newness', 'Weight', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'combinationId', 'productId', 'increase', 'quantity', 'promo', 'newness', 'weight', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(StockTableMap::ID, StockTableMap::COMBINATION_ID, StockTableMap::PRODUCT_ID, StockTableMap::INCREASE, StockTableMap::QUANTITY, StockTableMap::PROMO, StockTableMap::NEWNESS, StockTableMap::WEIGHT, StockTableMap::CREATED_AT, StockTableMap::UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('ID', 'COMBINATION_ID', 'PRODUCT_ID', 'INCREASE', 'QUANTITY', 'PROMO', 'NEWNESS', 'WEIGHT', 'CREATED_AT', 'UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('id', 'combination_id', 'product_id', 'increase', 'quantity', 'promo', 'newness', 'weight', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'CombinationId' => 1, 'ProductId' => 2, 'Increase' => 3, 'Quantity' => 4, 'Promo' => 5, 'Newness' => 6, 'Weight' => 7, 'CreatedAt' => 8, 'UpdatedAt' => 9, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'combinationId' => 1, 'productId' => 2, 'increase' => 3, 'quantity' => 4, 'promo' => 5, 'newness' => 6, 'weight' => 7, 'createdAt' => 8, 'updatedAt' => 9, ),
        self::TYPE_COLNAME       => array(StockTableMap::ID => 0, StockTableMap::COMBINATION_ID => 1, StockTableMap::PRODUCT_ID => 2, StockTableMap::INCREASE => 3, StockTableMap::QUANTITY => 4, StockTableMap::PROMO => 5, StockTableMap::NEWNESS => 6, StockTableMap::WEIGHT => 7, StockTableMap::CREATED_AT => 8, StockTableMap::UPDATED_AT => 9, ),
        self::TYPE_RAW_COLNAME   => array('ID' => 0, 'COMBINATION_ID' => 1, 'PRODUCT_ID' => 2, 'INCREASE' => 3, 'QUANTITY' => 4, 'PROMO' => 5, 'NEWNESS' => 6, 'WEIGHT' => 7, 'CREATED_AT' => 8, 'UPDATED_AT' => 9, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'combination_id' => 1, 'product_id' => 2, 'increase' => 3, 'quantity' => 4, 'promo' => 5, 'newness' => 6, 'weight' => 7, 'created_at' => 8, 'updated_at' => 9, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
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
        $this->setName('stock');
        $this->setPhpName('Stock');
        $this->setClassName('\\Thelia\\Model\\Stock');
        $this->setPackage('Thelia.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('COMBINATION_ID', 'CombinationId', 'INTEGER', false, null, null);
        $this->addForeignKey('PRODUCT_ID', 'ProductId', 'INTEGER', 'product', 'ID', true, null, null);
        $this->addColumn('INCREASE', 'Increase', 'FLOAT', false, null, null);
        $this->addColumn('QUANTITY', 'Quantity', 'FLOAT', true, null, null);
        $this->addColumn('PROMO', 'Promo', 'TINYINT', false, null, 0);
        $this->addColumn('NEWNESS', 'Newness', 'TINYINT', false, null, 0);
        $this->addColumn('WEIGHT', 'Weight', 'FLOAT', false, null, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Product', '\\Thelia\\Model\\Product', RelationMap::MANY_TO_ONE, array('product_id' => 'id', ), 'CASCADE', 'RESTRICT');
        $this->addRelation('AttributeCombination', '\\Thelia\\Model\\AttributeCombination', RelationMap::ONE_TO_MANY, array('id' => 'stock_id', ), null, null, 'AttributeCombinations');
        $this->addRelation('CartItem', '\\Thelia\\Model\\CartItem', RelationMap::ONE_TO_MANY, array('id' => 'stock_id', ), null, null, 'CartItems');
        $this->addRelation('ProductPrice', '\\Thelia\\Model\\ProductPrice', RelationMap::ONE_TO_MANY, array('id' => 'stock_id', ), null, null, 'ProductPrices');
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
        );
    } // getBehaviors()

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
        return $withPrefix ? StockTableMap::CLASS_DEFAULT : StockTableMap::OM_CLASS;
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
     * @return array (Stock object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = StockTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = StockTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + StockTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = StockTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            StockTableMap::addInstanceToPool($obj, $key);
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
            $key = StockTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = StockTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                StockTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(StockTableMap::ID);
            $criteria->addSelectColumn(StockTableMap::COMBINATION_ID);
            $criteria->addSelectColumn(StockTableMap::PRODUCT_ID);
            $criteria->addSelectColumn(StockTableMap::INCREASE);
            $criteria->addSelectColumn(StockTableMap::QUANTITY);
            $criteria->addSelectColumn(StockTableMap::PROMO);
            $criteria->addSelectColumn(StockTableMap::NEWNESS);
            $criteria->addSelectColumn(StockTableMap::WEIGHT);
            $criteria->addSelectColumn(StockTableMap::CREATED_AT);
            $criteria->addSelectColumn(StockTableMap::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.COMBINATION_ID');
            $criteria->addSelectColumn($alias . '.PRODUCT_ID');
            $criteria->addSelectColumn($alias . '.INCREASE');
            $criteria->addSelectColumn($alias . '.QUANTITY');
            $criteria->addSelectColumn($alias . '.PROMO');
            $criteria->addSelectColumn($alias . '.NEWNESS');
            $criteria->addSelectColumn($alias . '.WEIGHT');
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
        return Propel::getServiceContainer()->getDatabaseMap(StockTableMap::DATABASE_NAME)->getTable(StockTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(StockTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(StockTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new StockTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Stock or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Stock object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(StockTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Thelia\Model\Stock) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(StockTableMap::DATABASE_NAME);
            $criteria->add(StockTableMap::ID, (array) $values, Criteria::IN);
        }

        $query = StockQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { StockTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { StockTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the stock table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return StockQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Stock or Criteria object.
     *
     * @param mixed               $criteria Criteria or Stock object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(StockTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Stock object
        }

        if ($criteria->containsKey(StockTableMap::ID) && $criteria->keyContainsValue(StockTableMap::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.StockTableMap::ID.')');
        }


        // Set the correct dbName
        $query = StockQuery::create()->mergeWith($criteria);

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

} // StockTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
StockTableMap::buildTableMap();
