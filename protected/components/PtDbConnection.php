<?php
class PtDbConnection extends CDbConnection{
	
	public $tmpDb;
	public $tmpTable;
	
	private $_threadId;
	private $_serverId;

	
	public function getThreadId()
	{
		if($this->_threadId!==null)
		return $this->_threadId;
		
		$sql = "SELECT CONNECTION_ID()";
		return $this->_threadId = $this->createCommand($sql)->queryScalar();
	}
	
	
	public function getServerId()
	{
		if($this->_serverId!==null)
		return $this->_serverId;
		
		$sql = "SELECT @@global.server_id";
		return $this->createCommand($sql)->queryScalar();
		
	}
	
	/**
	 * Sets a table name of a temporary table
	 * @param string $name The table name
	 */
	public function setTmpTableName($name)
	{
		if($this->tmpTable[$name])
		return $this->tmpTable[$name];
		
		return $this->tmpTable[$name] = $this->tmpDb.".tmp_".$name."_".$this->threadId."_".$this->serverId;
	}

	/**
	 * Drops any 'real' temp tables
	 */
	public function dropTmpTables()
	{
		$tables = implode(',',$this->tmpTable);
		$sql = "DROP TABLE IF EXISTS $tables";
		$this->createCommand($sql)->execute();
	}
	
}