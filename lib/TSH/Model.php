<?php
/**
* TSH Ltd
*
* @package    TSH_Model
*/

abstract class TSH_Model
{

	static $_TableName  = false;
	static $_ItemName 	= false;

	static public function TableName()
	{
		return static::$_TableName;
	}

	static public function ItemName()
	{
		return static::$_ItemName;
	}

	/**
	 * Transform a property name into a valid database column name for this model
	 *
	 * Column names are simply itemname_name, eg: person_name, file_created, etc
	 * 
	 * @param string $fieldName
	 */
	static public function FullFieldName($fieldName)
	{
		if (false !== stripos($fieldName, static::ItemName() . '_'))
		{
			return $fieldName;
		}
		return static::ItemName() . '_' . $fieldName;
	}

	static public function IdColName()
	{
		return static::FullFieldName('id');
	}

	static public function Find($idOrWhere = false, $params = array(), $limit = false)
	{
		$sql    = "SELECT * FROM " . static::TableName();

		if (is_numeric($idOrWhere))
		{
			$sql 	.= " WHERE " . static::IdColName() . " = :id";
			$params = array('id' => $idOrWhere);
		}
		else if (!empty($idOrWhere))
		{
			$sql .= " WHERE {$idOrWhere}";
		}

		if (false !== $limit)
		{
			$sql .= " LIMIT {$limit}";
		}

		if (is_numeric($idOrWhere))
		{
			return static::FindFirstBySql($sql, $params);
		}
		else
		{
			return static::FindBySql($sql, $params);
		}

		return false;
	}

	static public function FindPage($page, $numPerPage = 20, $where = 1, $params = array())
	{
		$sql    = "SELECT * FROM " . static::TableName();
		if (!empty($where))
		{
			$sql .= " WHERE {$where}";
		}

		if (false === ($result = TSH_Db::Get()->selectPage($page, $sql, $params, $numPerPage)))
		{
			return false;
		}

		$models = array();
		if (0 < count($result))
		{		
			foreach ($result as $row)
			{
				$models[] = static::CreateFromArray($row);
			}
		}
		return $models;
	}

	static public function FindBySql($sql, $params)
	{
		if (false == ($result = TSH_Db::Get()->select($sql, $params)))
		{
			return false;
		}

		$models = array();
		if (0 < count($result))
		{		
			foreach ($result as $row)
			{
				$models[] = static::CreateFromArray($row);
			}
		}
		return $models;
	}

	static public function FindFirstBySql($sql, $params)
	{
		if (false == ($result = TSH_Db::Get()->selectFirst($sql, $params)))
		{
			return false;
		}

		return static::CreateFromArray($result);
	}

	static public function CreateFromArray(array $array)
	{
		$model = new static();

		$idColName = static::IdColName();
		if (!isset($array[$idColName]))
		{
			throw new TSH_Exception('Unable to create model from array with missing id in TSH_Model::CreateFromArray()');
		}

		$id = $array[$idColName];
		unset($array[$idColName]);

		$model->setId($id);
		$model->setFromArray($array);

		return $model;
	}

	protected $_id   = false;
	protected $_data = array();

	public function __get($name)
	{
		return $this->field($name);
	}
	
	public function __set($name, $value)
	{
		$this->setField($name, $value);
	}

	public function setId($id)
	{
		if (!is_numeric($id))
		{
			throw new TSH_Exception('ID value is not numeric in TSH_Model::setId()');
		}

		$this->_id = $id;
	}

	public function setFromArray(array $array)
	{
		$idColName = static::IdColName();
		if (isset($array[$idColName]))
		{
			unset($array[$idColName]);
		}

		if (empty($array))
		{
			throw new TSH_Exception('Illegal empty array in TSH_Model::setFromArray()');
		}
		$this->_data = $array;
	}

	public function field($name)
	{
		$fullFieldName = static::FullFieldName($name);
		if ($fullFieldName == static::IdColName())
		{
			return $this->_id;
		}
		return $this->_data[$fullFieldName];
	}

	public function setField($name, $value)
	{
		$fullFieldName = static::FullFieldName($name);
		$this->_data[$fullFieldName] = $value;
		return $this;
	}

	public function getDataArray()
	{
		return $this->_data;
	}

	public function save()
	{
		$record = $this->getDataArray();

		if (false == $this->_id)
		{
			// Creating a new model
			if (false === ($result = TSH_Db::Get()->insert(static::TableName(), $record)))
			{
				throw new TSH_Exception('Unable to save model (insert)');
			}

			$this->_id = $result;
			return true;
		}
		else
		{
			// Updating an existing one
			$where  = static::IdColName() . ' = :id';
			$params = array('id' => $this->_id);

			if (false === ($result = TSH_Db::Get()->update(static::TableName(), $record, $where, $params)))
			{
				throw new TSH_Exception('Unable to save model (update)');
			}

			return true;
		}
	}

}
