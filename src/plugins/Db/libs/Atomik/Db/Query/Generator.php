<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/** Atomik_Db_Query_Generator_Interface */
require_once 'Atomik/Db/Query/Generator/Interface.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Query_Generator implements Atomik_Db_Query_Generator_Interface
{
	/** @var Atomik_Db_Query */
	protected $_query;
	
	/** @var array */
	protected $_info;
	
	/**
	 * Returns the query as an SQL string
	 * 
	 * @return string
	 */
	public function generate(Atomik_Db_Query $query)
	{
		$this->_query = $query;
		$this->_info = $query->getInfo();
		
		$sql = 'SELECT '
		     . implode(', ', $this->_info['fields'])
		     . $this->_buildFromPart()
		     . $this->_buildJoinPart()
		     . $this->_buildWherePart()
		     . $this->_buildGroupByPart()
		     . $this->_buildOrderByPart()
		     . $this->_buildLimitPart();
		
		return trim($sql);
	}
	
	/**
	 * Builds the FROM part
	 * 
	 * @return string
	 */
	protected function _buildFromPart()
	{
		$sql = '';
		
		if (count($this->_info['from'])) {
			$tables = array();
			foreach ($this->_info['from'] as $fromInfo) {
				$fromSql = $fromInfo['table'];
				if (!empty($fromInfo['alias'])) {
					$fromSql .= ' AS ' . $fromInfo['alias'];
				}
				$tables[] = $fromSql;
			}
			$sql = ' FROM ' . implode(', ', $tables);
		}
		
		return $sql;
	}
	
	/**
	 * Builds the JOIN part
	 * 
	 * @return string
	 */
	protected function _buildJoinPart()
	{
		$sql = '';
		
		if (count($this->_info['join'])) {
			foreach ($this->_info['join'] as $joinInfo) {
				$sql .= ' ' . trim(strtoupper($joinInfo['type'])) 
					  . ' JOIN ' . $joinInfo['table']
					  . (!empty($joinInfo['alias']) ? ' AS ' . $joinInfo['alias'] : '')
					  . ' ON ' . $joinInfo['on'];
			}
		}
		
		return $sql;
	}
	
	/**
	 * Builds the WHERE part
	 * 
	 * @return string
	 */
	protected function _buildWherePart()
	{
		$sql = '';
		
		$where = $this->_query->getConditionString();
		if (!empty($where)) {
			$sql = ' WHERE ' . $where;
		}
		
		return $sql;
	}
	
	/**
	 * Builds the GROUP BY part
	 * 
	 * @return string
	 */
	protected function _buildGroupByPart()
	{
		$sql = '';
		
		if (count($this->_info['groupBy'])) {
			$sql = ' GROUP BY ' . implode(', ', $this->_info['groupBy']);
			if (count($this->_info['having'])) {
				$sql .= ' HAVING ' . $this->_query->_concatConditions($this->_info['having']);
			}
		}
		
		return $sql;
	}
	
	/**
	 * Builds the ORDER BY part
	 * 
	 * @return string
	 */
	protected function _buildOrderByPart()
	{
		$sql = '';
		
		if (is_string($this->_info['orderBy'])) {
			return ' ORDER BY ' . $this->_info['orderBy'];
		}
		
		if (count($this->_info['orderBy'])) {
			$fields = array();
			foreach ($this->_info['orderBy'] as $field => $direction) {
				$fieldSql = $field;
				if (!empty($direction)) {
					$fieldSql .= ' ' . $direction;
				}
				$fields[] = $fieldSql;
			}
			$sql = ' ORDER BY ' . implode(', ', $fields);
		}
		
		return $sql;
	}
	
	/**
	 * Builds the LIMIT part
	 * 
	 * @return string
	 */
	protected function _buildLimitPart()
	{
		$sql = '';
		
		if (!empty($this->_info['limit'])) {
			$sql = ' LIMIT ' . $this->_info['limit']['offset'] . ', ' . $this->_info['limit']['length'];
		}
		
		return $sql;
	}
}
