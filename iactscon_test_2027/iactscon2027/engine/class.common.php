<?php

/*********************************************************************************
 * This PHP file defines the library classes.
 * 
 * Class MySQLManager - Defines and Wraps the different MySQL functions
 *
 * Class Thumbnail - Defines different functions thumbnail and image creation 
 *                      
 * Class CMS - Defines and Wraps the different common site activities. 
 *
 **********************************************************************************/

/***************************************************************************
 *                             class.MySQLManager
 *                           --------------------
 *   begin                : Wednessday, Jan 10, 2007
 *   copyright            : Encoders
 *
 *   $Id: class.common.php, v 1.1 2007/01/10 11:10:01 PM
 *
 ***************************************************************************/

class MySQLManager
{
	/**
	 * Name:				__construct() [class constructor]
	 * Params:			varchar sqlserver, varchar sqluser, varchar sqlpassword, varchar database, boolean persistency
	 * Returns:			null
	 * Description:		Create an instance of the class 'MySQLManager' and make a database connection. 
	 *
	 */
	function __construct($sqlserver, $sqluser, $sqlpassword, $database, $persistency = true)
	{
		
		$this->logTable	   			= '_query_log_' . date("Ym") . "_";
		$this->persistency  		= $persistency;
		$this->user 				= $sqluser;
		$this->password 			= $sqlpassword;
		$this->server 				= $sqlserver;
		$this->dbname 				= $database;
 
		$this->db_connect_id 		= NULL;
		$this->db_connect_host_info = NULL;

		$this->db_connect_errno 	= NULL;
		$this->db_connect_err 		= NULL;

		$this->queryCounter 		= 0;
		$this->query_old_store		= array();

		$this->query_operation_type	= NULL;

		$this->query_to_execute		= NULL;
		$this->query_result			= NULL;

		$this->numrows				= NULL;
		$this->fetchrow				= NULL;
		$this->insertedId			= NULL;
		$this->affectedRow			= NULL;

		$this->query_stmt			= NULL;
		$this->params_for_query		= NULL;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				sql_connect()
	 * Params:			null
	 * Returns:			connection object/false
	 * Description:		Makes a database connection.
	 *
	 */
	function sql_connect($runConfigs = false)
	{
		$this->db_connect_id = mysqli_connect($this->server, $this->user, $this->password, $this->dbname);
     
		if (mysqli_connect_error()) {
			$this->db_connect_errno = mysqli_connect_errno();
			$this->db_connect_err = mysqli_connect_error();
			$this->kill_sql("Database Connection Error : " . $this->db_connect_errno . " <br> '<span style='font-weight:normal;'>" . nl2br($this->db_connect_err) . "</span>'");
		}

		$this->db_connect_host_info = mysqli_get_host_info($this->db_connect_id);
        mysqli_set_charset($this->db_connect_id, "utf8mb4");

		if ($runConfigs) {
			$this->sql_query("SET SQL_BIG_SELECTS=1");
			$this->sql_query("SET CHARACTER SET utf8");
			$this->sql_query("SET SESSION collation_connection ='utf8_general_ci'");
			$this->sql_query("SET character_set_results=utf8");
		}

		return $this->db_connect_id;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				sql_close()
	 * Params:			null
	 * Returns:			null
	 * Description:		Closes the database connection.
	 *
	 */
	function sql_close()
	{
		if ($this->db_connect_id) {
			if ($this->query_result) {
				@mysqli_free_result($this->query_result);
			}
			$result = @mysqli_close($this->db_connect_id);
			return $result;
		} else {
			return false;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				sql_query_old()
	 * Params:			varchar query
	 * Returns:			result set
	 * Description:		Runs a SQl Query (old way).
	 */
	function sql_query_old($query = "")
	{
		if ($query == "") {
			$this->kill_sql("Query not set.");
		}
		$this->sql_freeresult();
		$this->query_operation_type = 'OLD';

		$qCounter = $this->queryCounter;
		$this->query_old_store[$qCounter] = array();

		$this->query_to_execute = $query;

		$queryStartTime = $this->_microtime();
		$this->query_result = @mysqli_query($this->db_connect_id, $query);
		$queryEndTime = $this->_microtime();
		$queryExecutionTime = floatval($queryEndTime) - floatval($queryStartTime);
		// $this->_insert_database_log($query, $queryExecutionTime);


		$this->query_old_store[$qCounter]['_RESULT_'] = $this->query_result;
		if ($this->query_result) {
			if ($this->_startsWith(strtoupper($this->query_to_execute), 'SELECT')) {
				$this->numrows =  @mysqli_num_rows($this->query_result);
				$this->query_old_store[$qCounter]['_NUMROWS_'] = $this->numrows;
				while ($row = @mysqli_fetch_array($this->query_result)) {
					$q_rows[] = $row;
				}
				$this->fetchrow = $q_rows;
				$this->query_old_store[$qCounter]['_RECORDS_'] = $this->fetchrow;
				$this->query_old_store[$qCounter]['_RECORDS_POINTER_'] = 0;
			} elseif ($this->_startsWith(strtoupper($this->query_to_execute), 'INSERT')) {
				$this->insertedId = @mysqli_insert_id($this->db_connect_id);
				$this->query_old_store[$qCounter]['_INSERT_ID_'] = $this->insertedId;
			} elseif ($this->_startsWith(strtoupper($this->query_to_execute), 'UPDATE')) {
				$this->affectedRow = @mysqli_affected_rows($this->db_connect_id);
				$this->query_old_store[$qCounter]['_AFFECTED_ROW_'] = $this->affectedRow;
			}
			$this->queryCounter++;
			return "#QRESORSE:" . $qCounter;
		} else {
			$this->kill_sql("Cannot execute query <br> '<span style='font-weight:normal;'>" . nl2br($query) . "</span>'");
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				sql_query()
	 * Params:			varchar query
	 * Returns:			result set
	 * Description:		Closes the database connection.
	 *
	 */
	function sql_query($query = "")
	{
		if ($query == "") {
			$this->kill_sql("Query not set.");
		}
		$this->sql_freeresult();
		$this->query_operation_type = 'REGULAR';
		$this->query_to_execute = $query;

		$queryStartTime = $this->_microtime();
		$this->query_result = @mysqli_query($this->db_connect_id, $query);
		$queryEndTime = $this->_microtime();
		$queryExecutionTime = floatval($queryEndTime) - floatval($queryStartTime);

		if ($this->query_result) {
			if ($this->_startsWith(strtoupper(trim($this->query_to_execute)), 'SELECT')) {
				$this->numrows =  @mysqli_num_rows($this->query_result);
				while ($row = @mysqli_fetch_array($this->query_result)) {
					$q_rows[] = $row;
				}
				$this->fetchrow = $q_rows;
			} elseif ($this->_startsWith(strtoupper(trim($this->query_to_execute)), 'INSERT')) {
				$this->insertedId = @mysqli_insert_id($this->db_connect_id);
			} elseif ($this->_startsWith(strtoupper(trim($this->query_to_execute)), 'UPDATE')) {
				$this->affectedRow = @mysqli_affected_rows($this->db_connect_id);
			}
			// $this->_insert_database_log($query, $queryExecutionTime);
			return true;
		} else {
			// $this->_insert_database_log($query, $queryExecutionTime);
			$this->kill_sql("Cannot execute query <br> '<span style='font-weight:normal;'>" . nl2br($query) . "</span>'");
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				execSQL()
	 * Params:			varchar query
	 * Returns:			result set
	 * Description:		Prepares A SQL Query and Executes it.
	 *
	 */
	function execSQL($query, $params)
	{
		if ($query == "") {
			$this->kill_sql("Query not set.");
		}
		$this->sql_freeresult();
		$this->query_operation_type = 'PREPARED';
		$this->query_stmt = @mysqli_prepare($this->db_connect_id, $query);
		$this->query_to_execute = trim($query);
		$this->params_for_query = $params;

		if (!$this->query_stmt) {
			$this->kill_sql("There is an error in statement <br> '<span style='font-weight:normal;'>" . nl2br($query) . "</span>'");
		} else {
			@call_user_func_array(array($this->query_stmt, 'bind_param'), $this->_refValues($params));

			$queryStartTime = $this->_microtime();
			if (@mysqli_stmt_execute($this->query_stmt)) {

				$queryEndTime = $this->_microtime();
				$queryExecutionTime = floatval($queryEndTime) - floatval($queryStartTime);

				if ($this->_startsWith(strtoupper($this->query_to_execute), 'SELECT')) {

					$this->query_result = mysqli_stmt_result_metadata($this->query_stmt);
					while ($field = @mysqli_fetch_field($this->query_result)) {
						$parameters[] = &$row[$field->name];
					}

					@call_user_func_array(array($this->query_stmt, 'bind_result'), $this->_refValues($parameters));

					while (@mysqli_stmt_fetch($this->query_stmt)) {
						$x = array();
						foreach ($row as $key => $val) {
							$x[$key] = $val;
						}
						$results[] = $x;
					}
					$this->fetchrow = $results;
					$this->numrows =  sizeof($results);
				} elseif ($this->_startsWith(strtoupper($this->query_to_execute), 'INSERT')) {
					$this->insertedId = @mysqli_insert_id($this->db_connect_id);
					$this->affectedRow = @mysqli_stmt_affected_rows($this->query_stmt);
				} elseif ($this->_startsWith(strtoupper($this->query_to_execute), 'UPDATE')) {
					$this->affectedRow = @mysqli_stmt_affected_rows($this->query_stmt);
				}

				@mysqli_stmt_close($this->query_stmt);

				// $this->_insert_database_log($query, $queryExecutionTime);
				return true;
			} else {
				$error = $this->sql_error();
				$this->kill_sql("Cannot execute query <br> '<span style='font-weight:normal;'>" . nl2br($query) . "</span>'<br>" . $error['message']);
			}
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				query_operation_type()
	 * Params:			void
	 * Returns:			string operation type
	 * Description:		Returns the sql operation type.
	 *
	 */
	function query_operation_type()
	{
		return $this->query_operation_type;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				sql_numrows()
	 * Params:			varchar queryId
	 * Returns:			int rows
	 * Description:		Executes a Database query.
	 *
	 */
	function sql_numrows()
	{
		return $this->numrows;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:				sql_fetchrow()
	 * Params:			null
	 * Returns:			array
	 * Description:		Returns a row of the resultset.
	 *
	 */
	function sql_fetchrow($query_id = '')
	{
		if ($queryId != '' && $this->_startsWith($queryId, '#QRESORSE')) {
			$exp = explode(":", $queryId);
			if (is_numeric($exp[1])) {
				$res = intval($exp[1]);
				$qData = $this->query_old_store[$res];
				$pointer = $qData['_RECORDS_POINTER_'];
				$data = $qData['_RECORDS_'][$pointer];
				if (!empty($data)) {
					$this->query_old_store[$res]['_RECORDS_POINTER_'] = $pointer + 1;
					return $data;
				} else {
					return false;
				}
			} else {
				$this->kill_sql("Invalid resource '<span style='font-weight:normal;'>" . $queryId . "</span>'");
			}
		} else {
			return $this->fetchrow;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				sql_affectedrows()
	 * Params:			null
	 * Returns:			int row count
	 * Description:		Returns the affected row count.
	 *
	 */
	function sql_affectedrows()
	{
		return $this->affectedRow;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:				sql_insert_id()
	 * Params:			null
	 * Returns:			int row count
	 * Description:		Returns the ID generated in the last query.
	 *
	 */
	function sql_insert_id()
	{
		return $this->insertedId;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:				sql_freeresult()
	 * Params:			null
	 * Returns:			null
	 * Description:		frees the result set.
	 *
	 */
	function sql_freeresult($query_id = '')
	{
		if ($query_id != '') {
			unset($this->query_old_store[$query_id]);
		}

		if (isset($this->query_result) && $this->query_result != NULL) {
			@mysqli_free_result($this->query_result);
		}

		$this->query_operation_type	= NULL;

		$this->query_to_execute		= NULL;
		$this->query_result			= NULL;

		$this->numrows				= NULL;
		$this->fetchrow				= NULL;
		$this->insertedId			= NULL;
		$this->affectedRow			= NULL;

		$this->query_stmt			= NULL;
		$this->params_for_query		= NULL;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				sql_error()
	 * Params:			null
	 * Returns:			null
	 * Description:		Returns the mysql error.
	 *
	 */
	function sql_error($query_id = 0)
	{
		$result["message"] = @mysqli_error($this->db_connect_id);
		$result["code"] = @mysqli_errno($this->db_connect_id);
		return $result;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:			kill_sql()
	 * Params:		varchar
	 * Returns:		void 
	 * Description:	stops execution
	 */
	function kill_sql($message)
	{
		$kill = "\n<div align=\"center\"  style = \"BORDER: 1px solid; FONT-WEIGHT: bold; FONT-SIZE: 10px; COLOR: #0000CC; BACKGROUND-COLOR: #ffffff; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif; padding:3px;\">\n";
		$kill .= "<B>" . $message . "</B><br/>\n";
		$kill .= "</div>\n";
		$kill .= "\n<h3>ERROR</h3>";
		$kill .= "\n<div align=\"left\"  style = \"BORDER: 1px solid; FONT-WEIGHT: bold; FONT-SIZE: 10px; COLOR: #0000CC; BACKGROUND-COLOR: #EEEEEE; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif; padding:3px;\">\n";
		$kill .= "<pre>";
		echo $kill;
		print_r($this->db_connect_id);
		$kill = "</pre><br/>\n";
		$kill .= "</div>\n";
		echo $kill;
		$this->stacktrace();
		die("<br><br>--EOC--");
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:			stacktrace()
	 * Params:		varchar
	 * Returns:		void 
	 * Description:	echos a stacktrace
	 */
	function stacktrace()
	{
		$st = "\n<h3>Stacktrace</h3>";
		$st .= "\n<div align=\"left\"  style = \"BORDER: 1px solid; FONT-WEIGHT: normal; FONT-SIZE: 10px; COLOR: #000000; BACKGROUND-COLOR: #EEEEEE; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif; padding:3px;\">\n";
		$st .= "<pre>";
		echo $st;
		$stack = array_reverse(debug_backtrace());
		$stdata = array();
		foreach ($stack as $k => $v) {
			foreach ($v as $ky => $vl) {
				if (strtolower(trim($ky)) != 'args' && strtolower(trim($ky)) != 'object') {
					$stdata[$k][$ky] = $vl;
				}
			}
		}
		print_r($stdata);
		$st = "</pre><br/>\n";
		$st .= "</div>\n";
		echo $st;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:			_refValues()
	 * Params:		array
	 * Returns:		array 
	 * Description:	converts params to referencial values
	 */
	function _refValues($arr)
	{
		if (strnatcmp(phpversion(), '5.3') >= 0) //Reference is required for PHP 5.3+
		{
			$refs = array();
			foreach ($arr as $key => $value)
				$refs[$key] = &$arr[$key];
			return $refs;
		}
		return $arr;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/*
	* Name:    		_startsWith()
	* Params:		varchar,varchar
	* Returns:		boolean
	* Description:	check whether a string starts with a certain char sequence.
	*
	*/
	function _startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:   		microtime_float()
	 * Params:		null
	 * Returns:		void
	 * Description:	calculate microtime.
	 */
	function _microtime()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				_insert_database_log()
	 * Params:			void
	 * Returns:			
	 * Description:		
	 *
	 */
	function _insert_database_log($query_sql, $queryTime = -1)
	{
		$creatQuery	=	"CREATE TABLE IF NOT EXISTS `" . $this->logTable . "` (
								  `id` bigint(255) NOT NULL AUTO_INCREMENT,
								  `queryTime` varchar(30) DEFAULT NULL,
								  `ipAddress` varchar(50) DEFAULT NULL,
								  `queryOpType` varchar(50) DEFAULT NULL,
								  `queryType` varchar(30) DEFAULT NULL,
								  `query` longtext DEFAULT NULL,
								  `queryParams` longtext DEFAULT NULL,
								  `queryExecTime` varchar(255) DEFAULT -1,
								  `phpSessionId` varchar(100) DEFAULT NULL,	
								  `scriptFileName` varchar(100) DEFAULT NULL,	
								  `backTrace` blob DEFAULT NULL,
								  PRIMARY KEY (`id`)
								) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
		@mysqli_query($this->db_connect_id, $creatQuery);

		if ($this->_startsWith(strtoupper($query_sql), 'SELECT')) {
			$query_sql_type	=	'SELECT';
		} else if ($this->_startsWith(strtoupper($query_sql), 'INSERT')) {
			$query_sql_type	=	'INSERT';
		} else if ($this->_startsWith(strtoupper($query_sql), 'UPDATE')) {
			$query_sql_type	=	'UPDATE';
		} else if ($this->_startsWith(strtoupper($query_sql), 'DELETE')) {
			$query_sql_type	=	'DELETE';
		} else if ($this->_startsWith(strtoupper($query_sql), 'CREATE TABLE')) {
			$query_sql_type	=	'CREATE TABLE';
		}

		if ($this->params_for_query == '') {
			$queryParams = '';
		} elseif (is_array($this->params_for_query)) {
			$queryParams = serialize($this->params_for_query);
		} else {
			$queryParams = $this->params_for_query;
		}

		$stack = array_reverse(debug_backtrace());
		$stdata = array();
		foreach ($stack as $k => $v) {
			foreach ($v as $ky => $vl) {
				if (strtolower(trim($ky)) != 'args' && strtolower(trim($ky)) != 'object') {
					$stdata[$k][$ky] = $vl;
				}
			}
		}

		$setQuery	=	"INSERT INTO `" . $this->logTable . "`
								 SET `queryTime`			=	'" . date('Y-m-d H:i:s') . "',
									 `ipAddress`			=	'" . $_SERVER['REMOTE_ADDR'] . "',
									 `queryOpType`			=	'" . $this->query_operation_type . "',
									 `queryType`			=	'" . $query_sql_type . "',
									 `query`				=	'" . addslashes($query_sql) . "',
									 `queryParams`			=	'" . $queryParams . "',
									 `queryExecTime`		=	'" . $queryTime . "',
									 `phpSessionId`			=	'" . session_id() . "',
									 `scriptFileName`		=	'" . $_SERVER['SCRIPT_FILENAME'] . "',
									 `backTrace`			=	'" . serialize($stdata) . "'";
		mysqli_query($this->db_connect_id, $setQuery);
	}
}
// End of class MySQLManager

/***************************************************************************
 *                             	class.CMS
 *                            ----------------
 *   begin                : Saturday, Dec 03, 2006
 *   copyright            : Encoders
 *
 *   $Id: class.common.php,v 1.0 2006/12/03 11:10:01 PM 
 *
 ***************************************************************************/

class CMS
{

	/**
	 * Name:				__construct() [class constructor]
	 * Params:			null
	 * Returns:			null
	 * Description:		Create an instance of the class 'CMS' and make a database connection.
	 * Access:			System
	 */
	function __construct()
	{
		global $cfg;
		$GLOBALS['nb'] = $cfg;

		$this->pageAccess_session 		= '_' . $serviceTag . '_Access_';
		$this->pageSection_session 		= '_' . $serviceTag . '_Sections_';

		$this->sessionMGMT_session 		= '_' . $serviceTag . '__SESS10N__';
		$this->pageTrail_session 		= '_' . $serviceTag . '_PGTRAIL_';

		$this->meta_records_table 		= "_meta_records_" . date("Ym") . "_";
		$this->session_records_table 	= "_sess_records_" . date("Ymd") . "_";
		$this->page_trail_table 		= "_page_trail_" . date("Ymd") . "_";
		$this->mail_history_table 		= "_mail_history_" . date("Ym") . "_";
		$this->sms_history_table 		= "_sms_history_" . date("Ym") . "_";

		if (trim(session_id()) == '') {
			session_id($this->getRandom(10, 'alphanum') . date('YmdHis'));
		}

		$this->currentDB = "__default__";
		$this->queryManager[$this->currentDB] = new MySQLManager($cfg['DB_SERVER'], $cfg['DB_SERVER_USERNAME'], $cfg['DB_SERVER_PASSWORD'], $cfg['DB_DATABASE'], false);
		$this->defaultQueryManager = $this->queryManager[$this->currentDB];
		$this->defaultQueryManager->sql_connect();
		if (!$this->defaultQueryManager->db_connect_id) {
			$this->kill("Could not connect to the database " . $this->queryManager["__default__"]->dbname);
		}

		$this->session = array();
		$sql = "CREATE TABLE IF NOT EXISTS `" . $this->session_records_table . "` ( `id` bigint(255) NOT NULL AUTO_INCREMENT,
																			    `sessionId` varchar(255) NOT NULL,
																			    `session_data` blob DEFAULT NULL,
																			    PRIMARY KEY (`id`)
																			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->defaultQueryManager->sql_query($sql);


		$this->ip = $this->_ipCheck();
		$this->pgTrail = $this->_trail();

		$this->getSession();
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:			__call()
	 * Params:		null
	 * Returns:		void 
	 * Description:	calls the methods of other functions directly through this function
	 * Access:		System
	 */
	function __call($method, $args)
	{
		if (method_exists($this, $method)) {
			return call_user_func_array(array($this, $method), $args);
		} else if ($this->defaultQueryManager != NULL && method_exists($this->defaultQueryManager, $method)) {
			return call_user_func_array(array($this->defaultQueryManager, $method), $args);
		} elseif ($this->eMailManager != NULL && method_exists($this->eMailManager, $method)) {
			return call_user_func_array(array($this->eMailManager, $method), $args);
		} elseif ($this->smsManager != NULL && method_exists($this->smsManager, $method)) {
			return call_user_func_array(array($this->smsManager, $method), $args);
		} else {
			$this->kill("unknown function " . $method);
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	
	//		DATABASE RELATED FUNCTIONS
	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:				addDatabase()
	 * Params:			varchar index, varchar sqlserver, varchar sqluser, varchar sqlpassword, varchar database
	 * Returns:			void 
	 * Description:		Create a new instance of the class 'MySQLManager' and make a database connection.
	 * Access:			Public 
	 */
	function addDatabase($index, $sqlserver, $sqluser, $sqlpassword, $database)
	{
		$this->queryManager[$index] = new MySQLManager($sqlserver, $sqluser, $sqlpassword, $database, false);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:				clearDBConnections()
	 * Params:			null
	 * Returns:			void 
	 * Description:		closes all DB Connections
	 * Access:			Public 
	 */
	function clearDBConnections()
	{
		foreach ($this->queryManager as $index => $conOb) {
			$conOb->sql_close();
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:				setDatabase()
	 * Params:			varchar index
	 * Returns:			void 
	 * Description:		sets the working Database. 
	 * Access:			Public 
	 */
	function setDatabase($index)
	{
		$this->clearDBConnections();
		$this->currentDB = $index;
		$this->defaultQueryManager = $this->queryManager[$this->currentDB];
		$this->defaultQueryManager->sql_connect();
		if (!$this->defaultQueryManager->db_connect_id) {
			$this->kill("Could not connect to the database " . $this->queryManager[$index]->dbname);
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:				resetDatabase()
	 * Params:			varchar index
	 * Returns:			void 
	 * Description:		resets the working Database to the default. 
	 * Access:			Public 
	 */
	function resetDatabase()
	{
		$this->clearDBConnections();
		$this->currentDB = "__default__";
		$this->defaultQueryManager = $this->queryManager[$this->currentDB];
		$this->defaultQueryManager->sql_connect();
		if (!$this->defaultQueryManager->db_connect_id) {
			$this->kill("Could not connect to the database " . $this->queryManager["__default__"]->dbname);
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:				revertDatabase()
	 * Params:			varchar index
	 * Returns:			void 
	 * Description:		sets the Database to the previously working Database. 
	 * Access:			Public 
	 */
	function revertDatabase()
	{
		$this->clearDBConnections();
		$this->currentDB = $this->previousDB;
		$this->previousDB = "__default__";
		$this->defaultQueryManager = $this->queryManager[$this->currentDB];
		$this->defaultQueryManager->sql_connect();
		if (!$this->defaultQueryManager->db_connect_id) {
			$this->kill("Could not connect to the database " . $this->queryManager["__default__"]->dbname);
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// 		QUERY RELATED FUNCTIONS
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				sql_query()
	 * Params:			varchar query
	 * Returns:			result set
	 * Description:		Wrapper function to MySQLManager::sql_query
	 * Access:			Public
	 */
	function sql_query($query = "")
	{
		$testQ = strtoupper(trim($query));
		if ($this->_startsWith($testQ, 'SELECT')) {
			$this->kill("Using improper function sql_query for <br><br><i style='font-weight:normal;'>" . $query . "</i><br><br>use sql_select instead");
		} else if ($this->_startsWith($testQ, 'INSERT')) {
			$this->kill("Using improper function sql_query for <br><br><i style='font-weight:normal;'>" . $query . "</i><br><br>use sql_insert instead");
		} else if ($this->_startsWith($testQ, 'UPDATE')) {
			$this->kill("Using improper function sql_query for <br><br><i style='font-weight:normal;'>" . $query . "</i><br><br>use sql_update instead");
		} else {
			$this->_sql_query($query);
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				_sql_query()
	 * Params:			varchar query
	 * Returns:			result set
	 * Description:		Wrapper function to MySQLManager::sql_query
	 * Access:			Private
	 */
	function _sql_query($queryContent = "")
	{
		$query  = trim($queryContent);
		if ($this->_startsWith(strtoupper(trim($query)), 'SELECT')) {
			$dataArray = false;
			$resource = $this->defaultQueryManager->sql_query($query);
			$maxRow = $this->defaultQueryManager->sql_numrows();
			if ($maxRow > 0) {
				$dataArray = array();
				$cnt = 0;
				foreach ($this->defaultQueryManager->sql_fetchrow() as $k => $row) {
					$dataArray[$cnt] = array();
					foreach ($row as $key => $value) {
						$dataArray[$cnt][$key] = stripslashes($value);
					}
					$cnt++;
				}
			}
			return $dataArray;
		} else {
			$result = $this->defaultQueryManager->sql_query($query);
			return $result;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				sql_insert()
	 * Params:			varchar query
	 * Returns:			newly inserted Id
	 * Description:		Takes a insert query OR an predefined associated array to insert data into database table.
	 * Access:			Public
	 */
	function sql_insert($queryContent = "")
	{
		if (is_array($queryContent)) {
			if (array_key_exists('QUERY', $queryContent) && $this->_startsWith(strtoupper(trim($queryContent['QUERY'])), 'INSERT')) {
				$query = $queryContent['QUERY'];
				$param = array();
				if (array_key_exists('PARAM', $queryContent)) {
					$param[0] = '';
					$paramCount = 0;
					foreach ($queryContent['PARAM'] as $k => $dta) {
						$param[0] .= $dta['TYP'];
						$param[]   = $dta['DATA'];
						$paramCount++;
					}
					if ($paramCount != strlen($param[0]) || substr_count($query, '?') != strlen($param[0])) {
						$this->kill("Parameter DataType Mismatch for `sql_insert` for <br><br><i style='font-weight:normal;'>" . $query . "</i><br><br><span style='font-weight:normal;'>" . $this->_getValueOf($param) . "</span><br><br>Please refer to the user manual");
					}
				}
				$this->defaultQueryManager->execSQL($query, $param);
			} else {
				$this->kill("Improper array structure for `sql_insert` for <br><br><i style='font-weight:normal;'><pre>" . $this->_getValueOf($queryContent) . "</pre></i><br><br>Please refer to the user manual");
			}
		} else if ($this->_startsWith(strtoupper(trim($queryContent)), 'INSERT')) {
			$this->kill("Not supposed to implement standard query for `sql_insert` in <br><br><i style='font-weight:normal;'><pre>" . $queryContent . "</pre></i><br><br>use prepared statement.");
		} else {
			$this->kill("Improper function call `sql_insert` for <br><br><i style='font-weight:normal;'>" . $queryContent . "</i><br><br>Please refer to the user manual");
		}
		$affectedRow = $this->defaultQueryManager->sql_insert_id();
		return $affectedRow;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				sql_update()
	 * Params:			varchar query
	 * Returns:			no. of affected rows
	 * Description:		Takes a update query OR an predefined associated array to insert data into database table.
	 * Access:			Public
	 */
	function sql_update($queryContent = "")
	{
		if (is_array($queryContent)) {
			if (array_key_exists('QUERY', $queryContent) && $this->_startsWith(strtoupper(trim($queryContent['QUERY'])), 'UPDATE')) {
				$query = $queryContent['QUERY'];
				$param = array();
				if (array_key_exists('PARAM', $queryContent)) {
					$param[0] = '';
					$paramCount = 0;
					foreach ($queryContent['PARAM'] as $k => $dta) {
						$param[0] .= $dta['TYP'];
						$param[]   = $dta['DATA'];
						$paramCount++;
					}
					if ($paramCount != strlen($param[0]) || substr_count($query, '?') != strlen($param[0])) {
						$this->kill("Parameter DataType Mismatch for `sql_insert` for <br><br><i style='font-weight:normal;'>" . $query . "</i><br><br><span style='font-weight:normal;'>" . $this->_getValueOf($param) . "</span><br><br>Please refer to the user manual");
					}
				}
				$this->defaultQueryManager->execSQL($query, $param);
			} else {
				$this->kill("Improper array structure for `sql_update` for <br><br><i style='font-weight:normal;'><pre>" . $this->_getValueOf($queryContent) . "</pre></i><br><br>Please refer to the user manual");
			}
		} else if ($this->_startsWith(strtoupper(trim($queryContent)), 'UPDATE')) {
			$this->kill("Not supposed to implement standard query for `sql_update` in <br><br><i style='font-weight:normal;'><pre>" . $queryContent . "</pre></i><br><br>use prepared statement.");
		} else {
			$this->kill("Improper function call `sql_update` for <br><br><i style='font-weight:normal;'>" . $queryContent . "</i><br><br>Please refer to the user manual");
		}
		$affectedRow = $this->defaultQueryManager->sql_affectedrows();
		return $affectedRow;
	}

	function sql_delete($queryContent = "")
	{
		if (is_array($queryContent)) {
			if (array_key_exists('QUERY', $queryContent) && $this->_startsWith(strtoupper(trim($queryContent['QUERY'])), 'DELETE')) {
				$query = $queryContent['QUERY'];
				$param = array();
				if (array_key_exists('PARAM', $queryContent)) {
					$param[0] = '';
					$paramCount = 0;
					foreach ($queryContent['PARAM'] as $k => $dta) {
						$param[0] .= $dta['TYP'];
						$param[]   = $dta['DATA'];
						$paramCount++;
					}
					if ($paramCount != strlen($param[0]) || substr_count($query, '?') != strlen($param[0])) {
						$this->kill("Parameter DataType Mismatch for `sql_insert` for <br><br><i style='font-weight:normal;'>" . $query . "</i><br><br><span style='font-weight:normal;'>" . $this->_getValueOf($param) . "</span><br><br>Please refer to the user manual");
					}
				}
				$this->defaultQueryManager->execSQL($query, $param);
			} else {
				$this->kill("Improper array structure for `sql_update` for <br><br><i style='font-weight:normal;'><pre>" . $this->_getValueOf($queryContent) . "</pre></i><br><br>Please refer to the user manual");
			}
		} else if ($this->_startsWith(strtoupper(trim($queryContent)), 'DELETE')) {
			$this->kill("Not supposed to implement standard query for `sql_update` in <br><br><i style='font-weight:normal;'><pre>" . $queryContent . "</pre></i><br><br>use prepared statement.");
		} else {
			$this->kill("Improper function call `sql_update` for <br><br><i style='font-weight:normal;'>" . $queryContent . "</i><br><br>Please refer to the user manual");
		}
		$affectedRow = $this->defaultQueryManager->sql_affectedrows();
		return $affectedRow;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				sql_select()
	 * Params:			varchar query
	 * Returns:			result array
	 * Description:		Wrapper Class to MySQLManager::sql_query
	 * Access:			Public
	 */
	function sql_select($queryContent = "", $format = true)
	{

		if (is_array($queryContent)) {
			if (array_key_exists('QUERY', $queryContent) && $this->_startsWith(strtoupper(trim($queryContent['QUERY'])), 'SELECT')) {
				$query = $queryContent['QUERY'];
				$param = array();
				if (array_key_exists('PARAM', $queryContent)) {
					$param[0] = '';
					$paramCount = 0;
					foreach ($queryContent['PARAM'] as $k => $dta) {
						$param[0] .= $dta['TYP'];
						$param[]   = $dta['DATA'];
						$paramCount++;
					}
					if ($paramCount != strlen($param[0]) || substr_count($query, '?') != strlen($param[0])) {
						$this->kill("Parameter DataType Mismatch for `sql_select` for <br><br><i style='font-weight:normal;'>" . $query . "</i><br><br><span style='font-weight:normal;'>" . $this->_getValueOf($param) . "</span><br><br>Please refer to the user manual");
					}
				}

				$this->defaultQueryManager->execSQL($query, $param);

				if ($format) {
					$dataArray = $this->_format_select(0);
				} else {
					$dataArray = $this->defaultQueryManager->sql_fetchrow();
				}
			} else {
				$this->kill("Improper array structure for `sql_select` for <br><br><i style='font-weight:normal;'><pre>" . $this->_getValueOf($queryContent) . "</pre></i><br><br>Please refer to the user manual");
			}
		} else if ($this->_startsWith(strtoupper(trim($queryContent)), 'SELECT')) {
			$this->kill("Not supposed to implement standard query for `sql_select` in <br><br><i style='font-weight:normal;'><pre>" . $queryContent . "</pre></i><br><br>use prepared statement.");
		} else {
			$this->kill("Improper function call `sql_select` for <br><br><i style='font-weight:normal;'>" . $queryContent . "</i><br><br>Please refer to the user manual");
		}
		return $dataArray;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				sql_select_paginate()
	 * Params:			int index, varchar query, int limit
	 * Returns:			result array
	 * Description:		returns a paginated result set, works in close co-ordination with pagination related functions
	 * Access:			Public
	 */
	function sql_select_paginated($ind, $queryContent = "", $limit = 10, $selectedPageNo = "")
	{
		if (is_array($queryContent)) {

			$dataArray = array();
			if (array_key_exists('QUERY', $queryContent) && $this->_startsWith(strtoupper(trim($queryContent['QUERY'])), 'SELECT')) {

				$this->isPaginate[$ind] = true;
				$this->paginateSQL[$ind] = $queryContent;
				$this->pgLimit[$ind] = ($limit) ? $limit : 10;

				$dispPgNos = $this->getSession("_PGNO_");
				$setPGno = $dispPgNos[$thisPgNam][$ind];

				$thisPgNam = $this->getPageName();
				$prevPgDet = $this->getTrail(1);
				$prevPgNam = $prevPgDet['FILENAME'];
				$prevPgReq = $prevPgDet['REQUESTS'];

				/*if($this->isProcessPageOf($prevPgNam,$thisPgNam)||$prevPgReq['show']=='add'||$prevPgReq['show']=='edit'||$prevPgReq['show']=='view') {
					$this->pageno[$ind] = ($setPGno)?($setPGno):0; 
				} else if($pageName!=$previousPage["FILENAME"]) {
					$this->pageno[$ind] = 0;			
				} else*/ {
					$this->pageno[$ind] = (isset($_REQUEST['_pgn' . $ind . '_']) && $_REQUEST['_pgn' . $ind . '_'] != 0) ? $_REQUEST['_pgn' . $ind . '_'] : "0";
				}
				$dispPgNos[$pageName][$ind] = $this->pageno[$ind];
				$this->setSession("_PGNO_", $dispPgNos);

				$pageNoforOffset = $this->pageno[$ind];
				if ($selectedPageNo != '' && is_numeric($selectedPageNo)) {
					$pageNoforOffset = intval($selectedPageNo);
				}

				$offset = $pageNoforOffset * $this->pgLimit[$ind];
				if ($reset) {
					$offset = 0;
				}
				$limitTxt = " LIMIT " . $offset . ", " . $this->pgLimit[$ind];

				//echo 'limitTxt='. $limitTxt;

				//full query
				$this->sql_select($queryContent, false);
				$this->pgRecCnt[$ind] = $this->sql_numrows();

				//query with LIMIT tag
				$sql = $queryContent['QUERY'];
				$sql = $sql . $limitTxt;
				$queryContent['QUERY'] = $sql;

				//echo '<pre>'; print_r($queryContent);
				$unNumbered_data = $this->sql_select($queryContent, false);

				// formatting serial Nos
				$cnt = 0;
				foreach ($unNumbered_data as $k => $row) {
					$dataArray[$cnt] = array();
					$dataArray[$cnt]['__SRL__'] = $offset + $cnt + 1;
					foreach ($row as $key => $value) {
						$dataArray[$cnt][$key] = stripslashes($value);
					}
					$cnt++;
				}
			} else {
				$this->kill("Improper array structure for `sql_select_paginate` for <br><br><i style='font-weight:normal;'><pre>" . $this->_getValueOf($queryContent) . "</pre></i><br><br>Please refer to the user manual");
			}
		} else if ($this->_startsWith(strtoupper(trim($queryContent)), 'SELECT')) {
			$this->kill("Not supposed to implement standard query for `sql_select_paginate` in <br><br><i style='font-weight:normal;'><pre>" . $queryContent . "</pre></i><br><br>use prepared statement.");
		} else {
			$this->kill("Improper function call `sql_select_paginate` for <br><br><i style='font-weight:normal;'>" . $queryContent . "</i><br><br>Please refer to the user manual");
		}
		return $dataArray;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				_format_select()
	 * Params:			object resource
	 * Returns:			result array
	 * Description:		Wrapper Class to MySQLManager::sql_query
	 * Access:			Private
	 */
	function _format_select($offset = -1)
	{
		$dataArray = false;
		$maxRow = $this->defaultQueryManager->sql_numrows();
		if ($maxRow > 0) {
			$dataArray = array();
			$cnt = 0;
			foreach ($this->defaultQueryManager->sql_fetchrow() as $k => $row) {
				$dataArray[$cnt] = array();
				if ($offset >= 0) $dataArray[$cnt]['__SRL__'] = $offset + $cnt + 1;
				foreach ($row as $key => $value) {
					$dataArray[$cnt][$key] = stripslashes($value);
				}
				$cnt++;
			}
		}
		return $dataArray;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				sql_numrows()
	 * Params:			varchar resultset
	 * Returns:			int
	 * Description:		returns the number of rows in a resultset. 
	 * Access:			Public
	 */
	function sql_numrows()
	{
		return $this->defaultQueryManager->sql_numrows();
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	
	//  	PAGINATION RELATED FUNCTIONS
	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:   		pagination()
	 * Params:		varchar index, varchar sql, int row limit
	 * Returns:		void
	 * Description:	returns a paginated result set
	 */
	function pagination($ind, $sql, $limit = 50, $reset = false)
	{
		$this->kill("Using deprecated function pagination.<br><br>use sql_select_paginated instead.");
		/*$this->isPaginate[$ind] = true;
		$this->paginateSQL[$ind] = $sql;
		$this->pgLimit[$ind] = ($limit)?$limit:10;
							
		$dispPgNos = $this->getSession("_PGNO_");	
		$setPGno = $dispPgNos[$thisPgNam][$ind];
		
		$thisPgNam = $this->getPageName();
		$prevPgDet = $this->getTrail(1);
		$prevPgNam = $prevPgDet['FILENAME'];
		$prevPgReq = $prevPgDet['REQUESTS'];
		
		if($this->isProcessPageOf($prevPgNam,$thisPgNam)||$prevPgReq['show']=='add'||$prevPgReq['show']=='edit'||$prevPgReq['show']=='view'){
			$this->pageno[$ind] = ($setPGno)?($setPGno):0; 
		} else if($pageName!=$previousPage["FILENAME"]){
			$this->pageno[$ind] = 0;			
		} else {
			$this->pageno[$ind] = (isset($_REQUEST['_pgn'.$ind.'_']) && $_REQUEST['_pgn'.$ind.'_']!=0)?$_REQUEST['_pgn'.$ind.'_']:"0";
		}		
		$dispPgNos[$pageName][$ind] = $this->pageno[$ind];		
		$this->setSession("_PGNO_",$dispPgNos);
		
		$offset = $this->pageno[$ind]*$this->pgLimit[$ind];
		if($reset) {
			$offset = 0;
		}
		$limitTxt = " LIMIT ".$offset.", ".$this->pgLimit[$ind];
		//full query
		$res=$this->defaultQueryManager->sql_query($sql);
		$this->pgRecCnt[$ind] = $this->defaultQueryManager->sql_numrows($res);
		//query with LIMIT tag
		$sql = $sql. $limitTxt ;
		return $this->sql_select($sql);*/
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:   		paginate()
	 * Params:		varchar index, varchar style class name
	 * Returns:		void
	 * Description:	returns a paginate sequence
	 */
	function paginate($ind, $class)
	{
		if ($this->isPaginate[$ind]) {
			$currentPage = $_SERVER['PHP_SELF'];
			// develop query string minus page vars			
			if (!empty($_POST)) {
				$params = $_POST;
				foreach ($params as $key => $value) {
					if (trim($key) != "_pgn" . $ind . "_") {
						if ($key != "m") {
							$queryString .= "&" . htmlentities($key . "=" . $value);
						}
					}
				}
			}
			if (!empty($_GET)) {
				$params = $_GET;
				foreach ($params as $key => $value) {
					if (trim($key) != "_pgn" . $ind . "_") {
						if ($key != "m") {
							$queryString .= "&" . htmlentities($key . "=" . $value);
						}
					}
				}
			}

			$totalPages = ceil($this->pgRecCnt[$ind] / $this->pgLimit[$ind]);
			$pageNum = $this->pageno[$ind];

			// build page navigation
			if ($totalPages > 1) {
				$navigation  = "<div class='" . $class . "'>";
				$navigation .= "<a>" . ($pageNum + 1) . " of " . $totalPages . " Pages</a>";

				$upper_limit = $pageNum + 3;
				$lower_limit = $pageNum - 3;

				if ($pageNum > 0) {
					// Show if not first page			
					if (($pageNum - 2) > 0) {
						$first       = $currentPage . "?_pgn" . $ind . "_=0" . $queryString;
						$navigation .= "<a href='" . $first . "'>First</a> ";
					}
					$prev            = $currentPage . "?_pgn" . $ind . "_=" . max(0, $pageNum - 1) . $queryString;
					$navigation     .= "<a href='" . $prev . "'><<</a> ";
				} // Show if not first page

				// get in between pages
				for ($i = 0; $i < $totalPages; $i++) {
					$pageNo = $i + 1;
					if ($i == $pageNum) {
						$navigation .= "<a class='selected'>" . $pageNo . "</a>";
					} elseif ($i !== $pageNum && $i < $upper_limit && $i > $lower_limit) {
						$noLink      = $currentPage . "?_pgn" . $ind . "_=" . $i . $queryString;
						$navigation .= "<a href='" . $noLink . "'>" . $pageNo . "</a>";
					} elseif (($i - $lower_limit) == 0) {
						$navigation .=  "";
					}
				}

				if (($pageNum + 1) < $totalPages) {
					// Show if not last page
					$next = $currentPage . "?_pgn" . $ind . "_=" . min($totalPages, $pageNum + 1) . $queryString;
					$navigation .= "<a href='" . $next . "'>>></a> ";
					if (($pageNum + 3) < $totalPages) {
						$last = $currentPage . "?_pgn" . $ind . "_=" . ($totalPages - 1) . $queryString;
						$navigation .= "<a href='" . $last . "'>Last</a>";
					}
				} // Show if not last page 
				$navigation  .= "</div>";
			} // end if total pages is greater than one

			return $navigation;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:   		paginateRecInfo()
	 * Params:		varchar index, varchar style class name
	 * Returns:		void
	 * Description:	returns a paginate sequence
	 */
	function paginateRecInfo($ind, $msg = 'Showing {lowerLimit} to {upperLimit} of {totalCount} entries')
	{
		$totalRecordCounts = $this->pgRecCnt[$ind];

		if ($totalRecordCounts > 0) {

			$limits = $this->pgLimit[$ind];
			$pageNum = $this->pageno[$ind] + 1;

			$lowerLimit = (($pageNum - 1) * $limits) + 1;
			$upperLimit = $pageNum * $limits;

			if ($upperLimit > $totalRecordCounts) {
				$upperLimit = $totalRecordCounts;
			}
			return str_replace('{lowerLimit}', $lowerLimit, str_replace('{upperLimit}', $upperLimit, str_replace('{totalCount}', $totalRecordCounts, $msg)));
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:   		paginatedDetailsInfo()
	 * Params:		varchar index, varchar style class name
	 * Returns:		void
	 * Description:	returns a paginate sequence
	 */
	function paginatedDetailsInfo($ind)
	{
		$ret = array();
		$ret['TOTAL_RECORDS'] = $this->pgRecCnt[$ind];
		$ret['TOTAL_PAGES'] = ceil($this->pgRecCnt[$ind] / $this->pgLimit[$ind]);
		$ret['CURRENT_PAGE'] = $this->pageno[$ind];
		$ret['QUERY'] = $this->paginateSQL[$ind];
		return $ret;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	////////////////////////////////////////////////////////////////////////////////////////////////////	
	//  	PAGE TRAIL RELATED FUNCTIONS
	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:				_trail()
	 * Params:			null
	 * Returns:			array 
	 * Description:		Returns the last 50+1 pages traversed
	 * Access:			Private 
	 */
	function _trail()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `" . $this->page_trail_table . "` ( `id` bigint(255) NOT NULL AUTO_INCREMENT,
																		   `sessionId` varchar(255) NOT NULL,
																		   `request_uri` text NOT NULL,
																		   `http_referer` text NOT NULL,
																		   `filename` varchar(255) NOT NULL,
																		   `filetype` varchar(255) NOT NULL,
																		   `requests` blob DEFAULT NULL,
																		    PRIMARY KEY (`id`)
																		  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->defaultQueryManager->sql_query($sql);

		$pagerec["REQUEST_URI"] = $_SERVER['REQUEST_URI'];
		$pagerec["HTTP_REFERER"] = $_SERVER['HTTP_REFERER'];
		$pagerec["FILENAME"] = basename($_SERVER['PHP_SELF']);
		$pagerec["REQUESTS"] = $_REQUEST;

		if (strpos($pagerec["FILENAME"], ".process.")) {
			$pagerec["FILETYPE"] = "Process";
		} else {
			$pagerec["FILETYPE"] = "Show";
		}
		// echo "<pre>"; print_r($pagerec);die;


		$qry = "INSERT INTO `" . $this->page_trail_table . "` 
						SET `sessionId` = '" . session_id() . "',
							`request_uri` = '" . base64_encode($pagerec["REQUEST_URI"]) . "',
							`http_referer` = '" . base64_encode($pagerec["HTTP_REFERER"]) . "',
							`filename` = '" . base64_encode($pagerec["FILENAME"]) . "',
							`filetype` = '" . base64_encode($pagerec["FILETYPE"]) . "',
							`requests` = '" . base64_encode(serialize($pagerec["REQUESTS"])) . "';";
		$res = $this->defaultQueryManager->sql_query($qry);
		return $pagerec;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				getTrail()
	 * Params:			int the nth page before this page
	 * Returns:			array 
	 * Description:		returns the details of the nth page before this page 
	 * Access:			Public 
	 */
	function getTrail($prev = 0)
	{
		if ($prev > 0) {
			$qry = "SELECT * FROM ( SELECT * FROM `" . $this->page_trail_table . "` WHERE `sessionId` = '" . session_id() . "' ORDER BY `id` DESC LIMIT " . ($prev + 1) . " ) sub ORDER BY `id` ASC LIMIT 1;";
			$res = $this->_sql_query($qry);
			if ($res) {
				$data = $res[0];
				$pagerec["REQUEST_URI"] = base64_decode($data['request_uri']);
				$pagerec["HTTP_REFERER"] = base64_decode($data['http_referer']);
				$pagerec["FILENAME"] = base64_decode($data['filename']);
				$pagerec["FILETYPE"] = base64_decode($data['filetype']);
				$pagerec["REQUESTS"] = unserialize(base64_decode($data['requests']));
				return $pagerec;
			} else {
				return false;
			}
		} else {
			return $this->pgTrail;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				getPageName()
	 * Params:			void
	 * Returns:			varchar
	 * Description:		returns the present Page Name.
	 * Access:			Public 
	 */
	function getPageName()
	{
		$thispage = $this->getTrail();
		return $thispage["FILENAME"];
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				getProcessPageName()
	 * Params:			void
	 * Returns:			varchar
	 * Description:		returns the probable process page for present Page Name.
	 * Access:			Public 
	 */
	function getProcessPageName()
	{
		$pageName = $this->getPageName();
		$components = explode('.', $pageName);
		$ext = $components[sizeof($components) - 1];
		array_pop($components);
		$processPage = implode('.', $components) . '.process.' . $ext;
		return $processPage;
	}


	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				getRequestUri()
	 * Params:			void
	 * Returns:			varchar
	 * Description:		returns the REQUEST_URI.
	 * Access:			Public 
	 */
	function getRequestUri()
	{
		$thispage = $this->getTrail();
		return $thispage["REQUEST_URI"];
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				getHttpReferer()
	 * Params:			void
	 * Returns:			varchar
	 * Description:		returns the HTTP_REFERER.
	 * Access:			Public 
	 */
	function getHttpReferer()
	{
		$thispage = $this->getTrail();
		return $thispage["HTTP_REFERER"];
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				getRequestParams()
	 * Params:			void
	 * Returns:			varchar
	 * Description:		returns the REQUESTS.
	 * Access:			Public 
	 */
	function getRequestParams($format = true)
	{
		$thispage = $this->getTrail();
		if (!$format) {
			return $thispage["REQUESTS"];
		} else {
			return $this->_getValueOf($thispage["REQUESTS"]);
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:				getSessionParams()
	 * Params:			void
	 * Returns:			varchar
	 * Description:		returns the SESSIONS.
	 * Access:			Public 
	 */
	function getSessionParams($format = true)
	{
		$thispage = $this->getTrail();
		if (!$format) {
			return $thispage["SESSIONS"];
		} else {
			return _getValueOf($thispage["SESSIONS"]);
		}
	}


	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:				isProcessPageOf()
	 * Params:			varchar processPage, varchar showPage
	 * Returns:			boolean 
	 * Description:		checks whether the page is a processpage
	 * Access:			Public 
	 */
	function isProcessPageOf($processPage, $showPage)
	{
		if (strpos($processPage, ".process.")) {
			$pr = explode(".", $processPage);
			$sh = explode(".", $showPage);
			if ($pr[sizeof($pr) - 1] != "php") return false;
			if ($sh[sizeof($sh) - 1] != "php") return false;
			$prs = implode(".", array_slice($pr, 0, (sizeof($pr) - 2)));
			$shs = implode(".", array_slice($sh, 0, (sizeof($sh) - 1)));
			if ($prs == $shs) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////


	////////////////////////////////////////////////////////////////////////////////////////////////////
	// 		SESSION RELATED FUNCTIONS
	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			isSession()
	 * Params:		varchar sessionVariable
	 * Returns:		boolean
	 * Description:	returns whether the session is set or not.
	 *
	 */
	function isSession($sessionVariable)
	{
		global $cfg;
		if ($cfg['__USRWORKDOMAIN__'] == '') {
			$domain = 0;
		} else {
			$domain = $cfg['__USRWORKDOMAIN__'];
		}
		return ($this->session[$domain][$sessionVariable] && isset($this->session[$domain][$sessionVariable]));
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			getSession()
	 * Params:		varchar sessionVariable
	 * Returns:		varchar
	 * Description:	returns the sesseion variable.
	 *
	 */
	function getSession($sessionVariable = '')
	{
		global $cfg;
		if ($cfg['__USRWORKDOMAIN__'] == '') {
			$domain = 0;
		} else {
			$domain = $cfg['__USRWORKDOMAIN__'];
		}

		if (empty($this->session)) {
			$qry = "SELECT session_data FROM `" . $this->session_records_table . "` WHERE `sessionId` = '" . session_id() . "';";
			$res = $this->_sql_query($qry);
			if ($res) {
				$data = $res[0];
				if (trim($data['session_data']) != '') {
					$sessData = unserialize(base64_decode($data['session_data']));
				} else {
					$sessData = array();
				}
				$this->session = $sessData;
			} else {
				$qry = "INSERT INTO `" . $this->session_records_table . "` SET `sessionId` = '" . session_id() . "';";
				$res = $this->defaultQueryManager->sql_query($qry);
			}
		}

		if (trim($sessionVariable) == '') {
			return $this->session[$domain];
		} else {
			return $this->session[$domain][$sessionVariable];
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			setSession()
	 * Params:		varchar sessionVariable, varchar value
	 * Returns:		void
	 * Description:	Sets the sesseion variable.
	 */
	function setSession($sessionVariable, $value)
	{
		global $cfg;
		if ($cfg['__USRWORKDOMAIN__'] == '') {
			$domain = 0;
		} else {
			$domain = $cfg['__USRWORKDOMAIN__'];
		}
		if (trim($sessionVariable) != '') {
			if ($this->isSession($this->sessionMGMT_session)) {
				$sessVars = $this->getSession($this->sessionMGMT_session);
			} else {
				$sessVars = array();
			}

			$this->session[$domain][$sessionVariable] = $value;

			if (!in_array($sessionVariable, $sessVars))
				$sessVars[] = $sessionVariable;
			$this->session[$domain][$this->sessionMGMT_session] = $sessVars;

			$qry = "UPDATE `" . $this->session_records_table . "` SET `session_data` = '" . base64_encode(serialize($this->session)) . "' WHERE `sessionId` = '" . session_id() . "';";
			$res = $this->defaultQueryManager->sql_query($qry);
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			removeSession()
	 * Params:		varchar sessionVariable
	 * Returns:		void
	 * Description:	Removes the sesseion variable.
	 *
	 */
	function removeSession($sessionVariable)
	{
		global $cfg;
		if ($cfg['__USRWORKDOMAIN__'] == '') {
			$domain = 0;
		} else {
			$domain = $cfg['__USRWORKDOMAIN__'];
		}
		unset($this->session[$domain][$sessionVariable]);
		$qry = "UPDATE `" . $this->session_records_table . "` SET `session_data` = '" . base64_encode(serialize($this->session)) . "' WHERE `sessionId` = '" . session_id() . "';";
		$res = $this->defaultQueryManager->sql_query($qry);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			removeAllSession()
	 * Params:		null
	 * Returns:		null
	 * Description:	Removes all the sesseion variable.
	 *
	 */
	function removeAllSession()
	{
		global $cfg;
		if ($cfg['__USRWORKDOMAIN__'] == '') {
			$domain = 0;
		} else {
			$domain = $cfg['__USRWORKDOMAIN__'];
		}
		$this->session[$domain] = array();
		$qry = "UPDATE `" . $this->session_records_table . "` SET `session_data` = '" . base64_encode(serialize($this->session)) . "' WHERE `sessionId` = '" . session_id() . "';";
		$res = $this->defaultQueryManager->sql_query($qry);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////////////////////////
	//		DOCUMENT HEADER GENERATOR AND RELATED FUNCTIONS
	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:    		generateXMLHeader()
	 * Params:		void
	 * Returns:		null
	 * Description:	generates the XML header.
	 *
	 */
	function generateXMLHeader()
	{
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		header("Content-type: text/xml");
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:    		generateHTMLHeader()
	 * Params:		void
	 * Returns:		null
	 * Description:	generates the HTML header.
	 *
	 */
	function generateHTMLHeader()
	{
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		header("Content-type: text/html");
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:    		generateJSONHeader()
	 * Params:		void
	 * Returns:		null
	 * Description:	generates the JSON header.
	 *
	 */
	function generateJSONHeader()
	{
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		header("Content-type: application/json");
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:    		del_cache()
	 * Params:		null
	 * Returns:		void
	 * Description:	stops file from being cached .
	 */
	function del_cache()
	{
		// Date in the past
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		// always modified
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		// HTTP/1.1
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		// HTTP/1.0
		header("Pragma: no-cache");
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			Redirect()
	 * Params:		varchar request url , optional (varchar message , int time)
	 * Returns:		void
	 * Description:	Work's just like PHP header function. But difference is that it's print an
	 *				table with meta tag due to an error occure redirect url.
	 */
	function redirect($url, $message = '', $time = '1')
	{
		$new_dir = @(@dirname(@$_SERVER['PHP_SELF']) == "/") ? "" : @dirname(@$_SERVER['PHP_SELF']);

		$http = "http";
		if (isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == "on") $http = "https";

		if ($this->_startsWith(strtolower(trim($url)), 'http://') || $this->_startsWith(strtolower(trim($url)), 'https://')) {
			$newURl = $url;
		} else {
			$newURl = @trim(@str_replace("\/", "/", $http . "://" . @$_SERVER['HTTP_HOST'] . @$new_dir . "/" . @$url));
		}

		if (!headers_sent()) {
			header('Location:' . $newURl);
		} else {
			$message = ($message == '') ? 'Redirecting....' : $message;
			$re_err = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
			$re_err .= "<html>\n";
			$re_err .= "<head>\n";
			$re_err .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
			$re_err .= "<meta http-equiv='Refresh' content='1;url=" . $newURl . "'>\n";
			$re_err .= "<title>Redirect</title>\n";
			$re_err .= "</head>\n<body>\n";
			$re_err .= "<br/>\n<div align=\"center\">\n";
			$re_err .= "<font face=\"Verdana\" size=\"4\" style=\"color: #000000;\"><B>" . $message . "</B></font>\n<br/>\n";
			$re_err .= "<font face=\"Verdana\" size=\"2\" style=\"color: #000000;\">If page does not refresh automatically in 5 seconds please click on this link </font>&nbsp;";
			$re_err .= "<a href='" . $newURl . "' style=\"color: #174A81;\"><font face=\"Verdana\" size=\"2\">" . $newURl . "</font></a>\n";
			$re_err .= "</div>\n";
			$re_err .= "</body>";
			$re_err .= "</html>";
			die($re_err);
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////////////////////////	
	//		MAIL FUNCTIONS	
	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			__recordMailHistory()
	 * Params:		void
	 * Returns:		boolean
	 * Description:	Creates the mail history table and records.
	 *
	 */
	function __recordMailHistory($mode, $to_name, $to_email, $form_name, $form_email, $subject, $message, $altTextMessage = '', $cc = '', $bcc = '', $replytoName = '', $replytoEmail = '')
	{
		$sql = "CREATE TABLE IF NOT EXISTS `" . $this->mail_history_table . "` ( `id` bigint(255) NOT NULL AUTO_INCREMENT,																			 
																			 `sendTime` timestamp NOT NULL,
																			 `sendIp` varchar(255) NOT NULL,
																			 `sendSessionId` varchar(255) NOT NULL,
																			 `mailFrom` varchar(255) NOT NULL,
																			 `mailTo` varchar(255) NOT NULL,
																			 `cc` varchar(255) NOT NULL,
																			 `bcc` varchar(255) NOT NULL,
																			 `replyTo` varchar(255) NOT NULL,
																			 `subject` varchar(255) NOT NULL,
																			 `mainMessage` text,
																			 `altMessage` text,
																			 `sendMode` varchar(50) NOT NULL, 
																			 `sendStatus` varchar(50) NOT NULL, 
																			 `response` text,
																			 `responseTime` datetime NOT NULL,
																			  PRIMARY KEY (`id`)
																			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->defaultQueryManager->sql_query($sql);

		if (trim($replytoEmail) === '') {
			$replytoName 	= $form_name;
			$replytoEmail 	= $form_email;
		}

		$sql = "INSERT INTO `" . $this->mail_history_table . "` SET `sendTime` 		= '" . date("Y-m-d H:i:s") . "',
																`sendIp` 		= '" . addslashes($this->ip) . "',
																`sendSessionId`	= '" . session_id() . "',
																`mailFrom` 		= '" . $form_name . '<' . $form_email . '>' . "',
																`mailTo` 		= '" . $to_name . '<' . $to_email . '>' . "',
																`cc` 			= '" . $cc . "',
																`bcc` 			= '" . $bcc . "',
																`replyTo` 		= '" . $replytoName . '<' . $replytoEmail . '>' . "',
																`subject` 		= '" . addslashes($subject) . "',
																`mainMessage` 	= '" . addslashes($message) . "',
																`altMessage` 	= '" . addslashes($altTextMessage) . "',
																`sendMode`  	= '" . $mode . "',
																`sendStatus` 	= 'PRE-RUN',
																`responseTime` 	= '" . date("Y-m-d H:i:s") . "'";
		$this->defaultQueryManager->sql_query($sql);
		return $this->defaultQueryManager->sql_insert_id();
	}

	/**
	 * Name:			__updateMailHistory()
	 * Params:		void
	 * Returns:		boolean
	 * Description:	updates the mail history table 
	 *
	 */
	function __updateMailHistory($id, $response)
	{
		$sql = "UPDATE `" . $this->mail_history_table . "` SET  `sendStatus`  	= '" . addslashes($response['STATUS']) . "',
															`response`  	= '" . addslashes($this->_getValueOf($response)) . "',
															`responseTime` 	= '" . date("Y-m-d H:i:s") . "'
													 WHERE  `id` 			= '" . $id . "'";
		$this->defaultQueryManager->sql_query($sql);
	}

	/**
	 * Name:			setDefaultMailSender()
	 * Params:		varchar route -- options BASIC, STANDARD, SMTP, GOOGLE
	 * Returns:		void
	 * Description:	set the default mail sender route
	 */
	function setDefaultMailSender($route = "BASIC")
	{
		$this->mailSenderRoute = $route;
		$this->defaultMailSenderRoute = $route;
	}

	/**
	 * Name:			setMailSender()
	 * Params:		varchar route -- options BASIC, STANDARD, SMTP, GOOGLE
	 * Returns:		void
	 * Description:	set the mail sender route
	 */
	function setMailSender($route = "BASIC")
	{
		$this->mailSenderRoute = $route;
	}

	/**
	 * Name:			resetMailSender()
	 * Params:		void
	 * Returns:		void
	 * Description:	set the mail sender route
	 */
	function resetMailSender()
	{
		$this->mailSenderRoute = $this->defaultMailSenderRoute;
	}

	/**
	 * Name:			send_mail()
	 * Params:		varchar to_name, varchar to_email, varchar form_name, varchar form_email,
	 *				varchar subject, varchar message, varchar altTextMessage, varchar cc, varchar bcc,
	 * Returns:		void
	 * Description:	wrapper file to sends mail
	 *				send_mail('Sam', 'sam@yahoo.com', 'test', 'test@test.com','this is subject', 'This is test mail...','This is alternae textmail...');
	 */
	function send_mail($to_name, $to_email, $subject, $message, $altTextMessage = '', $cc = null, $bcc = '', $form_name = '', $form_email = '', $replyTo_name = '', $replyTo_email = '', $attachment = '')
	{
		global  $cfg;
		if (trim($form_email == '')) {
			$form_name  = _GENERAL_MAIL_FROM_NAME_;
			$form_email = _GENERAL_MAIL_FROM_EMAIL_;
		} else if ($form_email == 'SC_SENDER_EMAIL') {
			$form_name  = _GENERAL_MAIL_FROM_NAME_;
			$form_email = 'SC_SENDER_EMAIL';
		}

		$ret  = array('STATUS' => 'PRE-RUN');
		switch ($this->mailSenderRoute) {
			case "GOOGLE":
				if (trim($replyTo_email == '')) {
					$replyTo_name  = _GENERAL_MAIL_REPLYTO_NAME_;
					$replyTo_email = _GENERAL_MAIL_REPLYTO_EMAIL_;
				}
				$rec  = $this->__recordMailHistory($this->mailSenderRoute, $to_name, $to_email, $form_name, $form_email, $subject, $message, $altTextMessage, $cc, $bcc, $replyTo_name, $replyTo_email);
				$ret  = $this->googleMailSend(_GOOGLE_USERNAME_, _GOOGLE_PASSWORD_, $to_name, $to_email, $form_name, $form_email, $subject, $message, $altTextMessage, $cc, $bcc, $replyTo_name, $replyTo_email);
				$this->__updateMailHistory($rec, $ret);
				break;
			case "STANDARD":
				if (trim($replyTo_email == '')) {
					$replyTo_name  = _GENERAL_MAIL_REPLYTO_NAME_;
					$replyTo_email = _GENERAL_MAIL_REPLYTO_EMAIL_;
				}
				$rec  = $this->__recordMailHistory($this->mailSenderRoute, $to_name, $to_email, $form_name, $form_email, $subject, $message, $altTextMessage, $cc, $bcc, $replyTo_name, $replyTo_email);
				$ret  = $this->standardMailSend($to_name, $to_email, $form_name, $form_email, $subject, $message, $altTextMessage, $cc, $bcc, $replyTo_name, $replyTo_email);
				$this->__updateMailHistory($rec, $ret);
				break;
			case "SMTP":
				if (trim($replyTo_email == '')) {
					$replyTo_name  = _SMTP_REPLYTO_NAME_;
					$replyTo_email = _SMTP_REPLYTO_EMAIL_;
				}
				$rec  = $this->__recordMailHistory($this->mailSenderRoute, $to_name, $to_email, $form_name, $form_email, $subject, $message, $altTextMessage, $cc, $bcc, $replyTo_name, $replyTo_email);
				$ret  = $this->smtpMailSend(_SMTP_HOST_, _SMTP_USERNAME_, _SMTP_PASSWORD_, $to_name, $to_email, $form_name, $form_email, $subject, $message, $altTextMessage, $cc, $bcc, $replyTo_name, $replyTo_email);
				$this->__updateMailHistory($rec, $ret);
				break;
			case "SENDGRID":
				$rec  = $this->__recordMailHistory($this->mailSenderRoute, $to_name, $to_email, $form_name, $form_email, $subject, $message, $altTextMessage, $cc, $bcc);
				//$ret  = $this->sendgridMailSend($to_name, $to_email, $form_name, $form_email, $subject, $message);
				$ret  = $this->sendgridMailSendV3($to_name, $to_email, $form_name, $form_email, $subject, $message, $cc, $attachment);
				$this->__updateMailHistory($rec, $ret);
				break;
			case "BASIC":
				$rec  = $this->__recordMailHistory($this->mailSenderRoute, $to_name, $to_email, $form_name, $form_email, $subject, $message, $altTextMessage, $cc, $bcc);
				$ret  = $this->basicMailSend($to_name, $to_email, $form_name, $form_email, $subject, $message, $cc, $bcc);
				$this->__updateMailHistory($rec, $ret);
				break;
			default:
				$rec  = $this->__recordMailHistory("DEFAULT", $to_name, $to_email, $form_name, $form_email, $subject, $message, $altTextMessage, $cc, $bcc);
				$ret  = $this->basicMailSend($to_name, $to_email, $form_name, $form_email, $subject, $message, $cc, $bcc);
				$this->__updateMailHistory($rec, $ret);
				break;
		}
		return $ret;
	}

	/**
	 * Name:			basicMailSend()
	 * Params:		varchar to_name, varchar to_email, varchar form_name, varchar form_email,
	 *				varchar subject, varchar message, varchar cc, varchar bcc,
	 * Returns:		void
	 * Description:	basic mail sender using php mail function
	 */
	function basicMailSend($to_name, $to_email, $form_name, $form_email, $subject, $message, $cc = '', $bcc = '')
	{
		/* To send HTML mail, you can set the Content-type header. */
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= "From: " . $form_name . "<" . $form_email . "> \r\n";

		if ($cc != "") $headers .= "Cc: " . $cc . "\n";

		if ($bcc != "") $headers .= "Bcc: " . $bcc . "\n";

		$output = $message;
		$output = wordwrap($output, 72);

		if (mail($to_email, $subject, $output, $headers)) {
			$ret['STATUS'] 	= 'SUCCESS';
		} else {
			$ret['STATUS'] 	= 'FAIL';
			$ret['MESSAGE'] = 'Message could not be sent.';
		}
		return $ret;
	}

	/**
	 * Name:			standardMailSend()
	 * Params:		varchar to_name, varchar to_email, varchar form_name, varchar form_email,
	 *				varchar subject, varchar message, varchar altTextMessage, varchar cc, varchar bcc,
					varchar replytoName, varchar replytoEmail,
	 * Returns:		void
	 * Description:	standard mail sender using mail class
	 */
	function standardMailSend($to_name, $to_email, $form_name, $form_email, $subject, $message, $altTextMessage, $cc = '', $bcc = '', $replytoName = '', $replytoEmail = '')
	{
		global  $cfg;
		$ret  = array('STATUS' => 'PRE-RUN');
		$mail = new PHPMailer();
		try {
			$mail->CharSet = 'UTF-8';
			$mail->setFrom($form_email, $form_name); 					//Set who the message is to be sent from
			if ($replytoEmail !== '') {
				if ($replytoName == '') {
					$replytoName = $form_name;
				}
				$mail->addReplyTo($replytoEmail, $replytoName); 		//Set an alternative reply-to address
			}

			$mail->addAddress($to_email, $to_name); 					//Set who the message is to be sent to

			$mail->isHTML(true);                            			// Set email format to HTML

			$mail->Subject = $subject;
			$mail->Body    = $message;
			$mail->AltBody = $altTextMessage;

			if (!$mail->send()) {
				$ret['STATUS'] 	= 'FAIL';
				$ret['ERROR'] 	= 'ERROR: ' . $mail->ErrorInfo;
			} else {
				$ret['STATUS'] 		= 'SUCCESS';
				$ret['MESSAGE_ID'] 	= $mail->getLastMessageID();
				$ret['MIME'] 	   	= $mail->getMailMIME();
			}
		} catch (phpmailerException $e) {
			$ret['STATUS'] 	= 'FAIL';
			$ret['ERROR'] 	= 'ERROR-EXCEPTION: ' . $e->errorMessage();
		} catch (Exception $e) {
			$ret['STATUS'] 	= 'FAIL';
			$ret['ERROR'] 	= 'ERROR-EXCEPTION: ' . $e->getMessage();
		}
		return $ret;
	}

	/**
	 * Name:			smtpMailSend()
	 * Params:		varchar hostName, varchar accountUsername, varchar accountPassword,
					varchar to_name, varchar to_email, varchar form_name, varchar form_email,
	 *				varchar subject, varchar message, varchar altTextMessage, varchar cc, varchar bcc,
					varchar replytoName, varchar replytoEmail,
	 * Returns:		void
	 * Description:	mail sender through SMTP using mail class
	 */
	function smtpMailSend($hostName, $accountUsername, $accountPassword, $to_name, $to_email, $form_name, $form_email, $subject, $message, $altTextMessage = '', $cc = '', $bcc = '', $replytoName = '', $replytoEmail = '')
	{
		global  $cfg;

		$ret  = array('STATUS' => 'PRE-RUN');
		$mail = new PHPMailer();
		try {
			//$mail->SMTPDebug = 3;                         // Enable verbose debug output
			$mail->isSMTP();                                // Set mailer to use SMTP
			$mail->Host 		= $hostName;         		// Specify main and backup SMTP servers
			$mail->SMTPAuth 	= true;                     // Enable SMTP authentication
			$mail->Username 	= $accountUsername; 		// SMTP username
			$mail->Password 	= $accountPassword;   		// SMTP password
			$mail->Port 		= 25;                      // TCP port to connect to

			$mail->setFrom($form_email, $form_name);			// Who the message is to be sent from
			if ($replytoEmail !== '') {
				if ($replytoName == '') {
					$replytoName = $form_name;
				}
				$mail->addReplyTo($replytoEmail, $replytoName); // Add a replyto
			}

			$mail->addAddress($to_email, $to_name);          // Add a recipient

			$mail->isHTML(true);                            // Set email format to HTML

			$mail->Subject = $subject;
			$mail->Body    = $message;
			$mail->AltBody = $altTextMessage;

			if (!$mail->send()) {
				$ret['STATUS'] 	= 'FAIL';
				$ret['MESSAGE'] = 'Message could not be sent.';
				$ret['ERROR'] 	= $mail->ErrorInfo;
			} else {
				$ret['STATUS'] 	= 'SUCCESS';
				$ret['MESSAGE_ID'] 	= $mail->getLastMessageID();
				$ret['MIME'] 	   	= $mail->getMailMIME();
			}
		} catch (phpmailerException $e) {
			$ret['STATUS'] 	= 'FAIL';
			$ret['ERROR'] 	= 'ERROR-EXCEPTION: ' . $e->errorMessage();
		} catch (Exception $e) {
			$ret['STATUS'] 	= 'FAIL';
			$ret['ERROR'] 	= 'ERROR-EXCEPTION: ' . $e->getMessage();
		}
		return $ret;
	}

	/**
	 * Name:			smtpMailSend()
	 * Params:		varchar accountUsername, varchar accountPassword,
					varchar to_name, varchar to_email, varchar form_name, varchar form_email,
	 *				varchar subject, varchar message, varchar altTextMessage, varchar cc, varchar bcc,
					varchar replytoName, varchar replytoEmail,
	 * Returns:		void
	 * Description:	mail sender through Google Mail using mail class
	 */
	function googleMailSend($accountUsername, $accountPassword, $to_name, $to_email, $form_name, $form_email, $subject, $message, $altTextMessage = '', $cc = '', $bcc = '', $replytoName = '', $replytoEmail = '')
	{
		global  $cfg;

		$ret  = array('STATUS' => 'PRE-RUN');
		$mail = new PHPMailer();
		try {
			//$mail->SMTPDebug = 3;                         // Enable verbose debug output
			$mail->isSMTP();                                // Set mailer to use SMTP
			$mail->Host 		= 'smtp.gmail.com';         // Specify main and backup SMTP servers
			$mail->SMTPAuth 	= true;                     // Enable SMTP authentication
			$mail->Username 	= $accountUsername; 		// SMTP username
			$mail->Password 	= $accountPassword;   		// SMTP password
			$mail->SMTPSecure 	= 'tls';                    // Enable TLS encryption, `ssl` also accepted
			$mail->Port 		= 587;                      // TCP port to connect to

			$mail->setFrom($form_email, $form_name);			// Who the message is to be sent from
			if ($replytoEmail !== '') {
				if ($replytoName == '') {
					$replytoName = $form_name;
				}
				$mail->addReplyTo($replytoEmail, $replytoName); // Add a replyto
			}

			$mail->addAddress($to_email, $to_name);          // Add a recipient

			$mail->isHTML(true);                            // Set email format to HTML

			$mail->Subject = $subject;
			$mail->Body    = $message;
			$mail->AltBody = $altTextMessage;

			if (!$mail->send()) {
				$ret['STATUS'] 	= 'FAIL';
				$ret['MESSAGE'] = 'Message could not be sent.';
				$ret['ERROR'] 	= $mail->ErrorInfo;
			} else {
				$ret['STATUS'] 	= 'SUCCESS';
				$ret['MESSAGE_ID'] 	= $mail->getLastMessageID();
				$ret['MIME'] 	   	= $mail->getMailMIME();
			}
		} catch (phpmailerException $e) {
			$ret['STATUS'] 	= 'FAIL';
			$ret['ERROR'] 	= 'ERROR-EXCEPTION: ' . $e->errorMessage();
		} catch (Exception $e) {
			$ret['STATUS'] 	= 'FAIL';
			$ret['ERROR'] 	= 'ERROR-EXCEPTION: ' . $e->getMessage();
		}
		return $ret;
	}

	/**
	 * Name:			sendgridMailSend()
	 * Params:		varchar accountUsername, varchar accountPassword,
					varchar to_name, varchar to_email, varchar form_name, varchar form_email,
	 *				varchar subject, varchar message, varchar altTextMessage, varchar cc, varchar bcc,
					varchar replytoName, varchar replytoEmail,
	 * Returns:		void
	 * Description:	mail sender through Google Mail using mail class
	 */
	function sendgridMailSend($toname, $toemail, $fromname, $fromemail, $subject, $message, $attachments = "")
	{
		global $cfg;

		$url  = $cfg['SENDGRID_URL'];
		$user = $cfg['SENDGRID_USERNAME'];
		$pass = $cfg['SENDGRID_PASSWORD']; //"flynewsletter@2Gmail";

		$ret  = array('STATUS' => 'PRE-RUN');

		$params = array(
			'api_user'  => $user,
			'api_key'   => $pass,
			'to'        => $toemail,
			'subject'   => $subject,
			'html'      => $message,
			'text'      => $message,
			'fromname'  => $fromname,
			'from'      => $fromemail
		);

		if ($attachments != "") {
			$attch = unserialize($attachments);
			foreach ($attch as $k => $val) {
				$params['files'][$k] = $val;
			}
		}

		/*echo '<h1>Params</h1><pre>';
		print_r($params);
		echo '</pre>';*/

		$request =  $url . 'api/mail.send.json';

		if ($_SERVER['HTTP_HOST'] != 'localhost') {
			// Generate curl request
			$session = curl_init($request);
			curl_setopt($session, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
			// Tell curl to use HTTP POST
			curl_setopt($session, CURLOPT_POST, true);
			// Tell curl that this is the body of the POST
			curl_setopt($session, CURLOPT_POSTFIELDS, $params);
			// Tell curl not to return headers, but do return the response
			curl_setopt($session, CURLOPT_HEADER, false);
			// Tell PHP not to use SSLv3 (instead opting for TLS)
			curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

			// obtain response
			$response = curl_exec($session);
			/*echo '<h1>response</h1><pre>';
			print_r($response);
			echo '</pre>';*/
			curl_close($session);

			$ret['STATUS'] 	= 'SUCCESS';
		}
		return $response;
	}

	/*function sendgridMailSendV3($toname, $toemail, $fromname, $fromemail, $subject, $message, $attachments=""){		
		global $cfg;
		
		$url  = $cfg['SENDGRID_URL'];
		$user = $cfg['SENDGRID_USERNAME'];
		$pass = $cfg['SENDGRID_PASSWORD'];//"flynewsletter@2Gmail";
		$apiKey = "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";
		
		$JSON  = '{';
		$JSON .=  '"personalizations": [';
		$JSON .=   '{';
		$JSON .=  	 '"to": [';
		$JSON .=  		'{';
		$JSON .=  			'"email": "'.$toemail.'",';
		$JSON .=  			'"name": "'.$toname.'"';
		$JSON .=  		'}';
		$JSON .=  	  '],';
		$JSON .=  	 '"subject": "'.$subject.'"';
		$JSON .=   '}';
		$JSON .=  '],';
		$JSON .=  '"from": {';
		$JSON .=  	'"email": "'.$fromemail.'",';
		$JSON .=  	'"name": "'.$fromname.'"';
		$JSON .=  '},';
		$JSON .=  '"reply_to": {';
		$JSON .=  	'"email": "'.$fromemail.'",';
		$JSON .=  	'"name": "'.$fromname.'"';
		$JSON .=  '},';
		
		if($attachments!=""){			
		  $JSON .='"attachments": [';				
			$attch = unserialize($attachments);
			$c=0;
			foreach($attch as $k=>$val)
			{
		  	  $data = file_get_contents($val['path']);
			  $base64data = base64_encode($data);
			  if($c>0) $JSON .=',';	
			  $JSON .='{';	
			  $JSON .=  '"content": "'.$base64data.'",';
			  $JSON .=  '"type": "'.$val['type'].'",';
			  $JSON .=  '"filename": "'.$k.'"';
			  $JSON .='}';	
			  $c++;
			}
		  $JSON .='],';	
		}
		
		$JSON .=  '"content": [';
		$JSON .=   '{';
		$JSON .=  	'"type": "text/html",';
		$JSON .=  	'"value": "'.str_replace('"',"'",$message).'"';
		$JSON .=   '}';
		$JSON .=  ']';
		$JSON .= '}';
		
		
		
		//echo 'test-->'.$JSON;
		//var_dump(json_decode($JSON));
		
		$HEADER = array(
					'Authorization: Bearer '.$apiKey,
					'Content-Type: application/json'
				  );
		
		
		$request =  'https://api.sendgrid.com/v3/mail/send';//$url.'api/mail.send.json';		
		
		if($_SERVER['HTTP_HOST']!='localhost')
		{
			// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
			$ch = curl_init();		
			curl_setopt($ch, CURLOPT_URL, $request);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $JSON);
			curl_setopt($ch, CURLOPT_POST, 1);	
			curl_setopt($ch, CURLOPT_HTTPHEADER, $HEADER);
			
			$response = curl_exec($ch);
			if (curl_errno($ch)) {
				$response = curl_error($ch);
			}
			curl_close ($ch);
		}		
		return $response;
	}*/

	function sendgridMailSendV3($toname, $toemail, $fromname, $fromemail, $subject, $message, $ccs = null, $attachments = "")
	{
		global $cfg;

		$url  = $cfg['SENDGRID_URL'];
		$user = $cfg['SENDGRID_USERNAME']; //'sales@ruedakolkata.com';
		$pass = $cfg['SENDGRID_PASSWORD']; //"flynewsletter@2Gmail"; //'mails_sendGrid21';
		// $apiKey = "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";
		$apiKey = "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";



		require 'vendor/autoload.php'; // If you're using Composer (recommended)
		// Comment out the above line if not using Composer
		require("sendgrid-php.php");
		// If not using Composer, uncomment the above line and
		// download sendgrid-php.zip from the latest release here,
		// replacing <PATH TO> with the path to the sendgrid-php.php file,
		// which is included in the download:
		// https://github.com/sendgrid/sendgrid-php/releases

		$email = new \SendGrid\Mail\Mail();
		//$email->setFrom("neocon2022@gmail.com", "NEOCON 2022");
		if ($fromemail == 'SC_SENDER_EMAIL') {
			$email->setFrom($cfg['SC_SENDER_EMAIL'], $cfg['EMAIL_CONF_NAME']);
		} else {
			$email->setFrom($cfg['EMAIL_CONF_EMAIL_US'], $cfg['EMAIL_CONF_NAME']);
		}
        // Set Reply-To if provided
		if ($fromemail == 'SC_SENDER_EMAIL' && !empty($cfg['SC_REPLY_EMAIL'])) {
			$email->setReplyTo($cfg['SC_REPLY_EMAIL'], $cfg['EMAIL_CONF_NAME']);
		} elseif (!empty($cfg['EMAIL_CONF_REPLY_US'])) {
			$email->setReplyTo($cfg['EMAIL_CONF_REPLY_US'], $cfg['EMAIL_CONF_NAME']);
		}
		$email->setSubject($subject);
		$email->addTo($toemail, $toname);
		foreach ($ccs as $ccmail) {
			$email->addCc($ccmail['email'], $ccmail['name']);
		}
		//$email->addContent("text/plain", "and easy to do anywhere, even with PHP");
		$email->addContent(
			"text/html",
			$message
		);


		if (!empty($attachments) && is_array($attachments)) {
			// for url file format
			// foreach ($attachments as $url) {
			// 	// Fetch file from remote URL
			// 	$fileContent = @file_get_contents($url);

			// 	if ($fileContent !== false) {
			// 		$encodedContent = base64_encode($fileContent);

			// 		$attachment = new \SendGrid\Mail\Attachment();
			// 		$attachment->setContent($encodedContent);
			// 		$attachment->setType("application/pdf"); // auto-detect type
			// 		$attachment->setFilename(basename(parse_url($url, PHP_URL_PATH))); // filename from URL
			// 		$attachment->setDisposition("attachment");

			// 		$email->addAttachment($attachment);
			// 	} else {
			// 		error_log("Could not fetch file: $url");
			// 	}
			// }

			// for generated pdf 
			foreach ($attachments as $key=> $pdfAttachment) {
				$attachment = new SendGrid\Mail\Attachment();
				$attachment->setContent($pdfAttachment);
				// $attachment->setType("image/jpeg");
				// $attachment->setFilename("Certificate.jpeg");
				$attachment->setType("application/pdf");
				$attachment->setFilename("Workshop_Certificate_".($key+1).".pdf");
				$attachment->setDisposition("attachment");
				$email->addAttachment($attachment);
			}
		}
		else if($attachments) {
			$attachment = new SendGrid\Mail\Attachment();
			$attachment->setContent($attachments);
			// $attachment->setType("image/jpeg");
			// $attachment->setFilename("Certificate.jpeg");
			$attachment->setType("application/pdf");
			$attachment->setFilename("Certificate.pdf");
			$attachment->setDisposition("attachment");
			$email->addAttachment($attachment);
		}

		// $sendgrid = new \SendGrid('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');
		$sendgrid = new \SendGrid('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');
		try {
			$response = $sendgrid->send($email);
			// print $response->statusCode() . "\n";
			// print_r($response->headers());
			// print $response->body() . "\n";
			return 'Success';
		} catch (Exception $e) {
			//echo 'Caught exception: '. $e->getMessage() ."\n";
			return 'Error';
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////////////////////////	
	//		SMS FUNCTIONS	
	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			__recordSMSHistory()
	 * Params:		void
	 * Returns:		boolean
	 * Description:	Creates the sms history table and records.
	 *
	 */
	function __recordSMSHistory($provider, $senderId, $tonumber, $message, $route)
	{
		$sql = "CREATE TABLE IF NOT EXISTS `" . $this->sms_history_table . "` (  `id` bigint(255) NOT NULL AUTO_INCREMENT,																			 
																			 `sendTime` timestamp NOT NULL,
																			 `sendIp` varchar(255) NOT NULL,
																			 `sendSessionId` varchar(255) NOT NULL,
																			 `sendSmsTo` varchar(255) NOT NULL,																			
																			 `mainMessage` text,
																			 `senderID` varchar(255) NOT NULL,
																			 `provider` varchar(50) NOT NULL, 
																			 `route` varchar(255) NOT NULL,
																			 `sendStatus` varchar(255) NOT NULL,
																			 `response` text DEFAULT NULL,
																			 `responseTime` timestamp NULL DEFAULT NULL,
																			  PRIMARY KEY (`id`)
																			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->defaultQueryManager->sql_query($sql);

		if (trim($replytoEmail) === '') {
			$replytoName 	= $form_name;
			$replytoEmail 	= $form_email;
		}

		$sql = " INSERT INTO `" . $this->sms_history_table . "` SET `sendTime` 		= '" . date("Y-m-d H:i:s") . "',
																`sendIp` 		= '" . addslashes($this->ip) . "',
																`sendSessionId`	= '" . session_id() . "',
																`sendSmsTo` 	= '" . addslashes($tonumber) . "',
																`mainMessage` 	= '" . addslashes($message) . "',
																`senderID` 		= '" . addslashes($senderId) . "',																
																`provider` 		= '" . addslashes($provider) . "',
																`route` 		= '" . addslashes($route) . "',
																`sendStatus` 	= 'PRE-RUN'";
		$rs = $this->defaultQueryManager->sql_query($sql);
		return $this->defaultQueryManager->sql_insert_id();
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			__updateSMSHistory()
	 * Params:		void
	 * Returns:		boolean
	 * Description:	Updates the sms history table.
	 *
	 */
	function __updateSMSHistory($id, $response)
	{

		$sql = "UPDATE `" . $this->sms_history_table . "` SET  `sendStatus`  	= '" . addslashes($response['STATUS']) . "',
															`response`  	= '" . addslashes($this->_getValueOf($response)) . "',
															`responseTime` 	= '" . date("Y-m-d H:i:s") . "'
													 WHERE  `id` 			= '" . $id . "'";
		$this->defaultQueryManager->sql_query($sql, false);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			setDefaultSMSSender()
	 * Params:		varchar provider -- options ARGOS
	 * Returns:		void
	 * Description:	set the default SMS sender route
	 */
	function setDefaultSMSSender($provider = "ARGOS")
	{
		$this->SMSProvider = $provider;
		$this->defaultSMSProvider = $provider;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			resetSMSSender()
	 * Params:		void
	 * Returns:		void
	 * Description:	set the SMS sender route
	 */
	function resetSMSSender()
	{
		$this->SMSProvider = $this->defaultSMSProvider;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			send_sms()
	 * Params:		varchar to_number, varchar message, varchar route
	 * Returns:		void
	 * Description:	wrapper file to sends mail
	 *				send_sms('9088765432', 'message');
	 */
	function send_sms($tonumber, $message, $route = 'Informative')
	{
		global  $cfg;
		switch ($this->SMSProvider) {
			case "ARGOS":
				$rec  = $this->__recordSMSHistory($this->SMSProvider, $cfg['ARGOS_SENDERID'], $tonumber, $message, $route);
				$ret  = $this->argosSMSsend($tonumber, $message, $route, $cfg['ARGOS_SENDERID'], $cfg['ARGOS_USERNAME'], $cfg['ARGOS_PASSWORD']);
				$this->__updateSMSHistory($rec, $ret);
				break;
			default:
				$rec  = $this->__recordSMSHistory('DEFAULT', $cfg['ARGOS_SENDERID'], $tonumber, $message, $route);
				$ret  = $this->argosSMSsend($tonumber, $message, $route, $cfg['ARGOS_SENDERID'], $cfg['ARGOS_USERNAME'], $cfg['ARGOS_PASSWORD']);
				$this->__updateSMSHistory($rec, $ret);
				break;
		}
		return $ret;
	}



	/**
	 * Name:			argosSMSsend()
	 * Params:		varchar to_number, varchar message, varchar route
	 * Returns:		void
	 * Description:	wrapper file to sends mail
	 *				send_sms('9088765432', 'message');
	 */
	function argosSMSsend($tonumber, $message, $route = 'Trans', $senderid = '', $username = '', $password = '')
	{
		global $cfg, $mycms;

		$param        		  = array();
		$param['username']    = $username;
		$param['password']    = $password;
		$param['senderid']    = $senderid;
		$param['text']        = $message;
		$param['route']       = $route;
		$param['type']        = "text";
		$param['datetime']    = date('Y-m-d H:i:s');

		$recipients   = array($tonumber);
		$post         = 'to=' . implode(';', $recipients);

		foreach ($param as $key => $val) {
			$post    .= '&' . $key . '=' . rawurlencode($val);
		}
		$url          = "http://malert.in/new/api/api_http.php";

		$ret  = array('STATUS' => 'PRE-RUN');

		$logfileName = 'logs/log.sms.' . date("Ymd") . '.txt';
		file_put_contents($logfileName, date("Y-m-d H:i:s.u") . ":: DATA :: SMS URL => " . $url . '?' . $post . PHP_EOL, FILE_APPEND | LOCK_EX);

		if ($_SERVER['HTTP_HOST'] != 'localhost') {
			$ch           = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Connection: close"));

			$result       			= curl_exec($ch);
			$ret['RESPONSE'] 		= $result;
			if (curl_errno($ch)) {
				$result = "cURL ERROR: " . curl_errno($ch) . " " . curl_error($ch);
				$ret['STATUS'] 			= 'FAIL';
			} else {
				$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
				switch ($returnCode) {
					case 200:
						$ret['STATUS'] 	= 'SUCCESS';
						break;

					default:
						$result = "HTTP ERROR: " . $returnCode;
						$ret['STATUS'] 			= 'FAIL';
						$ret['COMPOSED_URL'] 	= $url . '?' . $post;
						break;
				}
			}
			curl_close($ch);
		}

		file_put_contents($logfileName, date("Y-m-d H:i:s.u") . ":: DATA :: SMS RESPONSE => " . $result . PHP_EOL, FILE_APPEND | LOCK_EX);
		return $ret;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////////////////////////
	// ONSCREEN MESSAGE DISPLAY FUNCTIONS
	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:    		setDisplayMessage()
	 * Params:		varchar message details
	 * Returns:		null 
	 * Description:	This function sets the display message...
	 */
	function setDisplayMessage($msg)
	{
		$this->onscreen_display_message = $msg;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:    		getDisplayMessage()
	 * Params:		null
	 * Returns:		varchar message details
	 * Description:	This function gets the display message...
	 */
	function getDisplayMessage()
	{
		return @$this->onscreen_display_message;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////
	//  FILE FUNCTIONS
	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:    		uploadFile()
	 * Params:		fileobject, varchar, varchar, boolean(optional)
	 * Returns:		null
	 * Description:	uploads a file.
	 *
	 */
	function uploadFile($tempFile, $uploadFile, $path, $name, $removeExisting = false)
	{
		if ($tempFile != '') {
			$components = explode(".", $uploadFile);
			$fileExt 	= strtolower($file_ext[count($file_ext) - 1]);

			$filename = $name . "." . $fileExt;

			if (!$this->_endsWith($path, '/')) $path .= '/';

			$file = $path . $filename;

			chmod($file, 0777);
			if (removeExisting) {
				@unlink($file);
			}
			if (move_uploaded_file($fileobject['tmp_name'], $file)) {
				copy($file, 0777);
				return $filename;
			} else {
				return false;
			}
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////
	//  MISC UTILITY FUNCTIONS
	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:    		getRandom()
	 * Params:		int(optional), varchar(optional)
	 * Returns:		null
	 * Description:	generates a random no. or string based on choice of characters.
	 *				choices are : alpha - only small case alphabets
	 *							: alphacaps - only capital case alphabets
	 *							: num - only numeric
	 *							: alphanum - numeric and  small case alphabets
	 *							: alphanumcaps - numeric, small case alphabets and capital case alphabets
	 *							: all - all available characters
	 *
	 */
	function getRandom($length = 6, $seeds = 'alphanum')
	{

		// Possible seeds
		$seedings['alpha'] = 'abcdefghijklmnopqrstuvwxyz';
		$seedings['alphacaps'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$seedings['num'] = '0123456789';
		$seedings['snum'] = '123456789';
		$seedings['alphanum'] = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$seedings['alphanumcaps'] = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$seedings['all'] = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&*(){}[]|:<>?';

		// Choose seed
		if (isset($seedings[$seeds])) {
			$seeds = $seedings[$seeds];
		}

		// Seed generator
		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		mt_srand($seed);

		// Generate
		$str = '';
		$seeds_count = strlen($seeds);

		for ($i = 0; $i < $length; $i++) {
			$str .= $seeds{
				mt_rand(0, $seeds_count - 1)};
		}
		return $str;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:			flush()
	 * Params:		null
	 * Returns:		void 
	 * Description:	flushes the buffer and stops execution
	 */
	function flushAll()
	{
		global $obst;
		if ($obst) {
			@ob_flush();
			@flush();
			@ob_end_flush();
			exit();
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:			kill()
	 * Params:		varchar
	 * Returns:		void 
	 * Description:	stops execution
	 */
	function kill($message = "", $expand = false)
	{
		if (trim($message) != "") {
			$kill = "\n<div align=\"center\"  style = \"BORDER: 1px solid; FONT-WEIGHT: bold; FONT-SIZE: 10px; COLOR: #cc3333; BACKGROUND-COLOR: #ffffff; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif; padding:3px;\">\n";
			$kill .= "<B>" . $message . "</B><br/>\n";
			$kill .= "</div>\n";
			echo $kill;
		}
		$this->stacktrace($expand);
		die("<br><br>--EOC--");
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:			stacktrace()
	 * Params:		varchar
	 * Returns:		void 
	 * Description:	echos a stacktrace
	 */
	function stacktrace($expand = false)
	{
		$st = "\n<h3>Stacktrace</h3>";
		$st .= "\n<div align=\"left\"  style = \"BORDER: 1px solid; FONT-WEIGHT: normal; FONT-SIZE: 10px; COLOR: #000000; BACKGROUND-COLOR: #EEEEEE; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif; padding:3px;\">\n";
		$st .= "<pre>";
		echo $st;
		$stack = array_reverse(debug_backtrace());
		$stdata = array();
		foreach ($stack as $k => $v) {
			foreach ($v as $ky => $vl) {
				if ($expand || (strtolower(trim($ky)) != 'args' && strtolower(trim($ky)) != 'object')) {
					$stdata[$k][$ky] = $vl;
				}
			}
		}
		print_r($stdata);
		$st = "</pre><br/>\n";
		$st .= "</div>\n";
		echo $st;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:   		microtime_float()
	 * Params:		null
	 * Returns:		void
	 * Description:	calculate microtime.
	 */
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:    		_ipCheck()
	 * Params:		null
	 * Returns:		varchar ip
	 * Description:	This function will try to find out if user is coming behind proxy server. 
	 *               Why is this important?
	 *               If you have high traffic web site, it might happen that you receive lot 
	 *               of traffic from the same proxy server (like AOL). In that case, the script 
	 *               would count them all as 1 user.
	 *               This function tryes to get real IP address.
	 *               Note that getenv() function doesn't work when PHP is running as ISAPI module
	 * Access:		Private
	 */
	function _ipCheck()
	{
		if (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_X_FORWARDED')) {
			$ip = getenv('HTTP_X_FORWARDED');
		} elseif (getenv('HTTP_FORWARDED_FOR')) {
			$ip = getenv('HTTP_FORWARDED_FOR');
		} elseif (getenv('HTTP_FORWARDED')) {
			$ip = getenv('HTTP_FORWARDED');
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			encoded()
	 * Params:		varchar statement
	 * Returns:		void
	 * Description:	It's return an encode form of a statement
	 *
	 */
	function encoded($str)
	{
		return str_replace(array('=', '+', '/'), '', base64_encode(base64_encode($str)));
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			decoded()
	 * Params:		varchar statement
	 * Returns:		void
	 * Description:	Returns decode form of an encoded statement.
	 *
	 */
	function decoded($str)
	{
		return base64_decode(base64_decode($str));
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	////////////////////////////////////////////////////////////////////////////////////////////////////
	//  DATE FUNCTIONS
	////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Name:			cDate()
	 * Params:		varchar(optional), mixed(optional), varchar(optional)
	 * Returns:		formated Date String
	 * Description:	Converts date to Date object and returns the formated date string
	 *
	 */
	function cDate($format = 'd-m-Y H:i:s', $date = '', $interval = '')
	{
		if (gettype($date) === 'object' && get_class($date) === 'DateTime') {
			$iDate = $date;
		} else {
			$iDate = date_create($date);
		}

		if (trim($interval) !== '') {
			$components = explode(" ", trim($interval));
			$quantity = intval($components[0]);
			$unit = $components[sizeof($components) - 1];
			if ($quantity < 0) {
				$interval = ((-1) * $quantity) . ' ' . $unit;
				$iDate = $this->__cDateSub($date, $interval);
			} else if ($quantity > 0) {
				$interval = $quantity . ' ' . $unit;
				$iDate = $this->__cDateAdd($date, $interval);
			}
		}
		return date_format($iDate, $format);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:			__cDateAdd()
	 * Params:		mixed,  varchar
	 * Returns:		Date Time Object
	 * Description:	Adds a interval to a Date
	 *
	 */
	function __cDateAdd($date, $interval)
	{
		$components = explode(" ", trim($interval));
		$quantity = $components[0];
		$unit = $components[sizeof($components) - 1];

		$year = $this->cDate('Y', $date);
		$month = $this->cDate('m', $date);
		$day = $this->cDate('d', $date);
		$hour = $this->cDate('H', $date);
		$minute = $this->cDate('i', $date);
		$seconds = $this->cDate('s', $date);

		switch ($unit) {
			case 'second':
			case 'seconds':
				$sec = $seconds;
				$nsec = intval($sec) + intval($quantity);
				if ($nsec < 10) $nsec = '0' . $nsec;
				if ($nsec > 59) {
					$sec = $nsec % 60;
					$amin =  floor($nsec / 60);
					if ($sec < 10) $sec = '0' . $sec;
					return $this->__cDateAdd(date_create($year . $month . $day . $hour . $minute . $sec), $amin . " minutes");
				} else {
					return date_create($year . $month . $day . $hour . $minute . $nsec);
				}
				break;
			case 'minute':
			case 'minutes':
				$min = $minute;
				$nmin = intval($min) + intval($quantity);
				if ($nmin < 10) $nmin = '0' . $nmin;
				if ($nmin > 59) {
					$min = $nmin % 60;
					$ahr =  floor($nmin / 60);
					if ($min < 10) $min = '0' . $min;
					return $this->__cDateAdd(date_create($year . $month . $day . $hour . $min . $seconds), $ahr . " hours");
				} else {
					return date_create($year . $month . $day . $hour . $nmin . $seconds);
				}
				break;
			case 'hour':
			case 'hours':
				$hr = $hour;
				$nhr = intval($hr) + intval($quantity);
				if ($nhr < 10) $nhr = '0' . $nhr;
				if ($nhr > 23) {
					$hr = $nhr % 24;
					$aday =  floor($nhr / 24);
					if ($hr < 10) $hr = '0' . $hr;
					return $this->__cDateAdd(date_create($year . $month . $day . $hr . $minute . $seconds), $aday . " days");
				} else {
					return date_create($year . $month . $day . $nhr . $minute . $seconds);
				}
				break;
			case 'day':
			case 'days':
				$dy = $day;
				$ndy = intval($dy) + intval($quantity);
				if ($ndy < 10) $ndy = '0' . $ndy;
				$daysInMonth = $this->__daysInMonth($date);
				if ($ndy > $daysInMonth) {
					$extraDays = $ndy - $daysInMonth;
					$nDate = $this->__cDateAdd(date_create($year . $month . '01' . $hour . $minute . $seconds), "1 month");
					return $this->__cDateAdd($nDate, ($extraDays - 1) . " days");
				} else {
					return date_create($year . $month . $ndy . $hour . $minute . $seconds);
				}
				break;
			case 'month':
			case 'months':
				$daysInMonth = $this->__daysInMonth($date);
				$mn = $month;
				$nmn = intval($mn) + intval($quantity);
				if ($nmn < 10) $nmn = '0' . $nmn;
				if ($nmn > 12) {
					$mn = $nmn % 12;
					$ayear =  floor($nmn / 12);
					if ($mn < 10) $mn = '0' . $mn;
					if ($daysInMonth == $day) {
						$day = $this->__daysInMonth($year . $mn . '01');
					}
					return $this->__cDateAdd(date_create($year . $mn . $day . $hour . $minute . $seconds), $ayear . " years");
				} else {
					if ($daysInMonth == $day) {
						$day = $this->__daysInMonth($year . $nmn . '01');
					}
					return date_create($year . $nmn . $day . $hour . $minute . $seconds);
				}
				break;
			case 'year':
			case 'years':
				$yr = $year;
				$nyr = intval($yr) + intval($quantity);
				return date_create($nyr . $month . $day . $hour . $minute . $seconds);
				break;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:			__cDateSub()
	 * Params:		mixed,  varchar
	 * Returns:		Date Time Object
	 * Description:	Sbstracts a interval from a Date
	 *
	 */
	function __cDateSub($date, $interval)
	{
		$components = explode(" ", trim($interval));
		$quantity = $components[0];
		$unit = $components[sizeof($components) - 1];

		$year = $this->cDate('Y', $date);
		$month = $this->cDate('m', $date);
		$day = $this->cDate('d', $date);
		$hour = $this->cDate('H', $date);
		$minute = $this->cDate('i', $date);
		$seconds = $this->cDate('s', $date);

		switch ($unit) {
			case 'second':
			case 'seconds':
				$sec = $seconds;
				$nsec = intval($sec) - intval($quantity);
				if ($nsec < 10 && $nsec > 0) $nsec = '0' . $nsec;
				if ($nsec < 0) {
					$lessSec = ((-1) * $nsec) - 1;
					$nDate = $this->__cDateSub(date_create($year . $month . $day . $hour . $minute . '59'), "1 minute");
					return $this->__cDateSub($nDate, ($lessSec) . " seconds");
				} else {
					return date_create($year . $month . $day . $hour . $minute . $nsec);
				}
				break;
			case 'minute':
			case 'minutes':
				$min = $minute;
				$nmin = intval($min) - intval($quantity);
				if ($nmin < 10 && $nmin > 0) $nmin = '0' . $nmin;
				if ($nmin < 0) {
					$lessMin = ((-1) * $nmin) - 1;
					$nDate = $this->__cDateSub(date_create($year . $month . $day . $hour . '59' . $seconds), "1 hour");
					return $this->__cDateSub($nDate, ($lessMin) . " minutes");
				} else {
					return date_create($year . $month . $day . $hour . $nmin . $seconds);
				}
				break;
			case 'hour':
			case 'hours':
				$hr = $hour;
				$nhr = intval($hr) - intval($quantity);
				if ($nhr < 10 && $nhr > 0) $nhr = '0' . $nhr;
				if ($nhr < 0) {
					$lessHr = ((-1) * $nhr) - 1;
					$nDate = $this->__cDateSub(date_create($year . $month . $day . '23' . $minute . $seconds), "1 day");
					return $this->__cDateSub($nDate, ($lessHr) . " hours");
				} else {
					return date_create($year . $month . $day . $nhr . $minute . $seconds);
				}
				break;
			case 'day':
			case 'days':
				$dy = $day;
				$ndy = intval($dy) - intval($quantity);
				if ($ndy < 10 && $ndy > 0) $ndy = '0' . $ndy;
				if ($ndy < 1) {
					$lessDay = (-1) * $ndy;
					$nDate = $this->__cDateSub(date_create($year . $month . $this->__daysInMonth($date) . $hour . $minute . $seconds), "1 month");
					return $this->__cDateSub($nDate, ($lessDay) . " days");
				} else {
					return date_create($year . $month . $ndy . $hour . $minute . $seconds);
				}
				break;
			case 'month':
			case 'months':
				$daysInMonth = $this->__daysInMonth($date);
				$mn = $month;
				$nmn = intval($mn) - intval($quantity);
				if ($nmn < 10 && $nmn > 0) $nmn = '0' . $nmn;
				if ($nmn < 1) {
					$lessMonth = (-1) * $nmn;
					if ($daysInMonth == $day) {
						$day = '31';
					}
					$nDate = $this->__cDateSub(date_create($year . '12' . $day . $hour . $minute . $seconds), "1 year");
					return $this->__cDateSub($nDate, ($lessMonth) . " months");
				} else {
					if ($daysInMonth == $day) {
						$day = $this->__daysInMonth($year . $nmn . '01');
					}
					return date_create($year . $nmn . $day . $hour . $minute . $seconds);
				}
				break;
			case 'year':
			case 'years':
				$yr = $year;
				$nyr = intval($yr) - intval($quantity);
				return date_create($nyr . $month . $day . $hour . $minute . $seconds);
				break;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	 * Name:			__daysInMonth()
	 * Params:		mixed
	 * Returns:		no of days in the month for the date
	 * Description:	returns no of days in the month for the date
	 *
	 */
	function __daysInMonth($date)
	{
		$month =  $this->cDate('m', $date);
		$ret = 0;
		switch ($month) {
			case 1:
			case 3:
			case 5:
			case 7:
			case 8:
			case 10:
			case 12:
				$ret = 31;
				break;
			case 4:
			case 6:
			case 9:
			case 11:
				$ret = 30;
				break;
			case 2:
				$year = $this->cDate('Y', $date);
				if ($year % 4 == 0) $ret = 29;
				else $ret = 28;
				break;
		}
		return $ret;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////	

	////////////////////////////////////////////////////////////////////////////////////////////////////
	//  PRIVATE UTILITY FUNCTIONS
	////////////////////////////////////////////////////////////////////////////////////////////////////		

	/**
	 * Name:				_getValueOf()
	 * Params:			varchar
	 * Returns:			varchar 
	 * Description:		returns a value in string form
	 * Access:			Private 
	 */
	function _getValueOf($value)
	{
		if (is_array($value)) {
			$valueString = '';
			foreach ($value as $key => $val) {
				$valueString .= (($valueString == '') ? '' : ', ') . '[' . $key . ']=' . $this->_getValueOf($val);
			}
			return ' Array(' . $valueString . ')';
		} else {
			return $value;
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/*
	* Name:    		_startsWith()
	* Params:		varchar,varchar
	* Returns:		boolean
	* Description:	check whether a string starts with a certain char sequence.
	*
	*/
	function _startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////

	/*
	* Name:    		_endsWith()
	* Params:		varchar,varchar
	* Returns:		boolean
	* Description:	check whether a string starts with a certain char sequence.
	*
	*/
	function _endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, -$length) === $needle);
	}
}
// End of class CMS
