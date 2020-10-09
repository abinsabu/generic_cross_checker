<?php
namespace GENE\GenCrosschecker\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

// \TYPO3\CMS\Core\Database\DatabaseConnection

/**
 * *************************************************************
 *
 * Copyright notice
 *
 * (c) 2020
 *
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 * *************************************************************
 */

/**
 * The repository for ContentCheckers
 */
class ContentCheckerRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * getConnected
     *
     * @return Object
     */
    public function getConnected($extensionConfiguration)
    {   
        $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['External'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default'];
        $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['External']['dbname'] = $extensionConfiguration['remote_databaseName'];
        $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['External']['host'] = $extensionConfiguration['remote_hostName'];
        $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['External']['password'] = $extensionConfiguration['remote_password'];
        $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['External']['user'] = $extensionConfiguration['remote_userName'];
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionByName('External')->createQueryBuilder();
        
        return $connection;
    }

    /**
     * function doGetTableList
     *
     * @return array
     */
    public function doGetTableList($dbObj, $extensionConfiguration)
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getConnectionByName('External');
            $res = $connection->query('show tables')->fetchall();
            $i = 0;
            while ($i < count($res)) {
                $tableValue = $res[$i]['Tables_in_'.$extensionConfiguration['remote_databaseName']];
                $tableList[] = $tableValue;
                $i = $i+1;
            }
            return $tableList;
    }

    /**
     * function doGetContents
     *
     * @return array
     */
    public function doGetContents($dbObj, $requestParams, $dbList)
    {
        $queryOptions = [];
        foreach ($requestParams['externalDbTables'] as $_key => $_value) {
            $queryOptions = [];
            $queryOptions = $this->doPrepareQuery($dbObj, $_value, $requestParams ,$dbList[$_value]);
            if (! empty($queryOptions)) {
                $connection = GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getConnectionForTable($dbList[$_value]);
                $queryBuilder = $connection->createQueryBuilder();
                $result = $queryBuilder->select('tstamp AS rangetstamp', 'crdate AS rangecrdate', 'uid', 'pid')
                        ->from($dbList[$_value])
                        ->where($queryOptions['where'])
                        ->orderBy('tstamp',' DESC')
                        ->execute()
                        ->fetchall();
                $dataResult[$_value] = $result;
            }
        }
        return $dataResult;
    }

    /**
     * function doPrepareQuery
     *
     * @return array
     */
    public function doPrepareQuery($dbObj, $_value, $requestParams, $table)
    {
        $filterDateTo = time();
        if (! empty($requestParams['filterDateTo'])) {
            $filterDateTo = strtotime($requestParams['filterDateTo']);
        }
        $filterDateFrom = strtotime($requestParams['filterDateFrom']);
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($table);
        $i = 0;
        $select = "SHOW COLUMNS FROM " . $table;
        $res = $queryBuilder->query($select)->fetchall();
        while ($i < count($res)) {
            $field = $res[$i]['Field'];
            switch ($field) {
                case 'crdate':
                case 'tstamp':
                case 'modification_date':
                    if (! empty($queryOptions['select'])) {
                        $queryOptions['select'] .= ', '.$field;
                        $queryOptions['where'] .= ' OR  '.$field.'  BETWEEN ' .
                            $filterDateFrom . ' AND ' . $filterDateTo;
                        $queryOptions['orderby'] = $queryOptions['orderby'] . ' , '.$field.' DESC';
                    } else {
                        $queryOptions['select'] = ''.$field;
                        $queryOptions['where'] = $field.'  BETWEEN ' . $filterDateFrom . ' AND ' .
                             $filterDateTo;
                        $queryOptions['orderby'] =$field.' DESC';
                    }
                    break;
            }
            $i = $i + 1;
        }
        return $queryOptions;
    }
}
