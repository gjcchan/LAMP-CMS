<?php

	include_once('Date.php');
	include_once('systemConstants.php');
	include_once('logUtils.php');

	// SQL statement constructor functions //
	// Parameter format:
	// $table:    self-explanatory
	// $args:     array(array([fieldName1], [value1], [dontUseQuotes1]),
	//					array([fieldName2], [value2], [dontUseQuotes2]),
	//					array([fieldName3], [value3], [dontUseQuotes3]),
	//					...)
	// $criteria: array(array([fieldName1], [value1], [operator1], [dontUseQuotes1]),
	//					array([fieldName2], [value2], [operator2], [dontUseQuotes2]),
	//					array([fieldName3], [value3], [operator3], [dontUseQuotes3]),
	//					...)
	// set dontUseQuotes to true if the value you are specifying is to be taken literally ie. array('PV', 'PV + 100', true), otherwise, the dontUseQuotes parameter is optional
	// operator can be any comparison operator (ie. =, <>, <, >, <=, >=, LIKE, etc)
	// $disjoin:  true if you want to OR the criteria, false if you want to AND the criteria (optional - ANDs criteria by default)
	//
	// example usage:
	//		$args[] = array('userID', $_SESSION['sessionUID']);
	//		$args[] = array('lessonID', $contentRow['lessonID']);
	//		$args[] = array('initialAccess', $today);
	//		$mysqlDB->do_query($mysqlDB->do_insert('lessonCompleteTbl', $args));

	class Database {
		private $mysql_link;
		private $printStatements = false;
		private $printErrors = true;
		private $insertDeletion = false;
		private $lockedTables = array();
		private $tablesLocked = false;
		private $SQLhost;
		private $SQLport;
		private $SQLusername;
		private $SQLpass;
		private $SQLdatabase;
		private $useSSL;
		private $SSLCAFile;
		private $SSLCertFile;
		private $SSLKeyFile;
		private $connected = false;

		function Database($SQLhost, $SQLport, $SQLusername, $SQLpass, $SQLdatabase, $insertDeletion = false, $useSSL = false, $SSLCAFile = '', $SSLCertFile = '', $SSLKeyFile = '') {
			$this->SQLhost = $SQLhost;
			$this->SQLusername = $SQLusername;
			$this->SQLpass = $SQLpass;
			$this->SQLdatabase = $SQLdatabase;
			$this->SQLport = $SQLport;
			$this->insertDeletion = $insertDeletion;
			$this->useSSL = $useSSL;
			$this->SSLCAFile = $SSLCAFile;
			$this->SSLCertFile = $SSLCertFile;
			$this->SSLKeyFile = $SSLKeyFile;

			$this->printErrors = (SYSTEM_TYPE == DEVELOPMENT);
			$this->connect();
			register_shutdown_function(array(&$this, 'destruct'));
		}

		function destruct() {
			if ($this->connected) {
				mysqli_commit($this->mysql_link);

				$this->unlockTables();

				if (!mysqli_close($this->mysql_link)) {
					printf("Can't close connection to DB. Error: %s" . LINEBREAK, mysqli_connect_error());
					exit();
				}
			}
		}

		public function getDatabaseName() {
			return $this->SQLdatabase;
		}

		public function setAutoCommit($autoCommit) {
			mysqli_autocommit($this->mysql_link, $autoCommit);
		}

		public function getInsertDeletion() {
			return $this->insertDeletion;
		}

		public function setInsertDeletion($insertDeletion) {
			$this->insertDeletion = $insertDeletion;
		}

		public function setPrintStatements($printStatements) {
			$this->printStatements = $printStatements;
		}

		public function setPrintErrors($printErrors) {
			$this->printErrors = $printErrors;
		}

		public function createInsertSQL($table, $args) {
			foreach ($args AS $arg) {
				$fieldList .= ', `' . mysqli_escape_string($this->mysql_link, stripslashes($arg[0])) . '`';

				if (isDateTime($arg[1]) && isset($_SESSION['sessionTimeZone']))
					$arg[1] = standardizeDate($arg[1], $_SESSION['sessionTimeZone']);

				if ($arg[2])
					$valueList .= ', ' . mysqli_escape_string($this->mysql_link, stripslashes($arg[1]));
				else
					$valueList .= ', "' . mysqli_escape_string($this->mysql_link, stripslashes($arg[1])) . '"';
			}

			$sql = 'INSERT INTO ' . $table . ' (' . substr($fieldList, 2) . ') VALUES (' . substr($valueList, 2) . ')';
			return $sql;
		}

		public function createInsertOnDuplicateUpdateSQL($table, $args) {
			foreach ($args AS $arg) {
				$fieldList .= ', `' . mysqli_escape_string($this->mysql_link, stripslashes($arg[0])) . '`';

				if (isDateTime($arg[1]) && isset($_SESSION['sessionTimeZone']))
					$arg[1] = standardizeDate($arg[1], $_SESSION['sessionTimeZone']);

				if ($arg[2])
					$valueList .= ', ' . mysqli_escape_string($this->mysql_link, stripslashes($arg[1]));
				else
					$valueList .= ', "' . mysqli_escape_string($this->mysql_link, stripslashes($arg[1])) . '"';

				if ($arg[2])
					$fieldValueList .= ', ' . mysqli_escape_string($this->mysql_link, stripslashes($arg[0])) . ' = ' . mysqli_escape_string($this->mysql_link, stripslashes($arg[1]));
				else
					$fieldValueList .= ', ' . mysqli_escape_string($this->mysql_link, stripslashes($arg[0])) . ' = "' . mysqli_escape_string($this->mysql_link, stripslashes($arg[1])) . '"';
			}

			$sql = 'INSERT INTO ' . $table . ' (' . substr($fieldList, 2) . ') VALUES (' . substr($valueList, 2) . ') ON DUPLICATE KEY UPDATE ' . substr($fieldValueList, 2);
			return $sql;
		}

		public function createDeleteSQL($table, $criteria, $disjoin = false) {
			if ($disjoin)
				$joiner = ' OR ';
			else
				$joiner = ' AND ';

			foreach ($criteria AS $criterion) {
				if (isDateTime($criterion[1]) && isset($_SESSION['sessionTimeZone']))
					$criterion[1] = standardizeDate($criterion[1], $_SESSION['sessionTimeZone']);

				if ($criterion[3])
					$criteriaList .= $joiner . '`' . mysqli_escape_string($this->mysql_link, stripslashes($criterion[0])) . '` ' . mysqli_escape_string($this->mysql_link, stripslashes($criterion[2])) . ' ' . mysqli_escape_string($this->mysql_link, stripslashes($criterion[1]));
				else
					$criteriaList .= $joiner . '`' . mysqli_escape_string($this->mysql_link, stripslashes($criterion[0])) . '` ' . mysqli_escape_string($this->mysql_link, stripslashes($criterion[2])) . ' "' . mysqli_escape_string($this->mysql_link, stripslashes($criterion[1])) . '"';
			}

			$sql = 'DELETE FROM ' . $table;

			if (count($criteria) > 0)
				$sql .= ' WHERE ' . substr($criteriaList, strlen($joiner));

			return $sql;
		}

		public function createUpdateSQL($table, $args, $criteria, $disjoin = false) {
			if ($disjoin)
				$joiner = ' OR ';
			else
				$joiner = ' AND ';

			foreach ($args AS $arg) {
				if (isDateTime($arg[1]) && isset($_SESSION['sessionTimeZone']))
					$arg[1] = standardizeDate($arg[1], $_SESSION['sessionTimeZone']);

				if ($arg[2])
					$fieldValueList .= ', `' . mysqli_escape_string($this->mysql_link, stripslashes($arg[0])) . '` = ' . mysqli_escape_string($this->mysql_link, stripslashes($arg[1]));
				else
					$fieldValueList .= ', `' . mysqli_escape_string($this->mysql_link, stripslashes($arg[0])) . '` = "' . mysqli_escape_string($this->mysql_link, stripslashes($arg[1])) . '"';
			}

			foreach ($criteria AS $criterion) {
				if (isDateTime($criterion[1]) && isset($_SESSION['sessionTimeZone']))
					$criterion[1] = standardizeDate($criterion[1], $_SESSION['sessionTimeZone']);

				if ($criterion[3])
					$criteriaList .= $joiner . '`' . mysqli_escape_string($this->mysql_link, stripslashes($criterion[0])) . '` ' . mysqli_escape_string($this->mysql_link, stripslashes($criterion[2])) . ' ' . mysqli_escape_string($this->mysql_link, stripslashes($criterion[1]));
				else
					$criteriaList .= $joiner . '`' . mysqli_escape_string($this->mysql_link, stripslashes($criterion[0])) . '` ' . mysqli_escape_string($this->mysql_link, stripslashes($criterion[2])) . ' "' . mysqli_escape_string($this->mysql_link, stripslashes($criterion[1])) . '"';
			}

			$sql = 'UPDATE ' . $table . ' SET ' . substr($fieldValueList, 2);

			if (count($criteria) > 0)
				$sql .= ' WHERE ' . substr($criteriaList, strlen($joiner));

			return $sql;
		}
		// SQL statement constructor functions //

		/* batch/transactional queries
		 * expected parameter: an array of SQL statements to be executed
		 * try to execute statements on InnoDB tables first as any statements executed on non-InnoDB tables before a failure WILL NOT be rolled back
		 * returns array with insert ids generated by each query in order upon success, redirects to system error page and writes backtrace to log file on failure so make sure that ONLY non-template pages (ie form handlers) use this function
		 * TYPICAL USAGE:
		 * transactionalQuery(array(do_insert(...), do_insert(...), do_update(...), do_update(...)));
		 */

		public function transactionalQuery($queries) {
			for ($i = 0; $i < count($queries); $i++) {
				$this->execute_query($queries[$i]);

				if ($this->printStatements)
					$insertedIDs[] = $i;
				else
					$insertedIDs[] = mysqli_insert_id($this->mysql_link);
			}

			return $insertedIDs;
		}

		//for singular inserts or difficult inserts
		public function do_insert($sql) {
			$this->execute_query($sql);

			if ($this->printStatements)
				return rand(1, 1000000);
			else
				return mysqli_insert_id($this->mysql_link);
		}

		//for singular queries, select queries, difficult updates, and deletes
		public function do_query($sql) {
			if (stristr($sql, 'SELECT') && isset($_SESSION['sessionTimeZone'])) {
				if (preg_match_all('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $sql, $matchArray)) {
					$replaceDates = array();

					foreach ($matchArray[0] AS $localizedDate)
						$replaceDates[$localizedDate] = standardizeDate($localizedDate, $_SESSION['sessionTimeZone']);

					foreach ($replaceDates AS $needle => $replace)
						$sql = str_replace($needle, $replace, $sql);
				}
			}

			$result = $this->execute_query($sql);

			if (stristr($sql, 'SELECT'))
				$result = new mysqlResult($result);

			return $result;
		}

		public function lockTables($tablesArray, $lockType) {
			if (!is_array($tablesArray) || count($tablesArray) == 0) {
				return;
			}

			if (!eregi('READ', $lockType) && !eregi('WRITE', $lockType)) {
				echo 'Lock Type must be READ or WRITE';
				exit;
			}

			$this->lockedTables = $tablesArray;

			$sql = 'LOCK TABLES';

			$count = 0;

			foreach ($tablesArray AS $table) {
				if ($count > 0)
					$sql .= ',';

				$sql .= ' ' . $table . ' ' . $lockType;
				$count++;
			}

			$this->do_query($sql);
			$this->tablesLocked = true;
		}

		public function unlockTables() {
			if (count($this->lockedTables) > 0) {
				$sql = 'UNLOCK TABLES';
				$this->do_query($sql);
			}

			$this->tablesLocked = false;
		}

		public function commitQueries() {
			mysqli_commit($this->mysql_link);
		}

		public function rollbackQueries() {
			mysqli_rollback($this->mysql_link);
		}

		public function tableExists($tableName) {
			$tableExistsSQL = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . $this->SQLdatabase . '" AND TABLE_NAME = "' . $tableName . '"';

			return ($this->do_query($tableExistsSQL)->num_rows);
		}

		private function connect() {
			$this->mysql_link = mysqli_init();

			if ($this->useSSL) {
				if (!file_exists($this->SSLKeyFile)) {
					echo "SSL Key file doesn't exist" . LINEBREAK;
					exit;
				}

				if (!file_exists($this->SSLCertFile)) {
					echo "SSL Key Cert doesn't exist" . LINEBREAK;
					exit;
				}

				if (!file_exists($this->SSLCAFile)) {
					echo "SSL CA file doesn't exist" . LINEBREAK;
					exit;
				}

				mysqli_ssl_set($this->mysql_link, $this->SSLKeyFile, $this->SSLCertFile, $this->SSLCAFile, NULL, NULL);
			}

			if (!mysqli_real_connect($this->mysql_link, $this->SQLhost, $this->SQLusername, $this->SQLpass, $this->SQLdatabase, $this->SQLport)) {
				printf("Can't connect to DB. Error: %s" . LINEBREAK, mysqli_connect_error());
				debug_print_backtrace();
				exit();
			}

			mysqli_autocommit($this->mysql_link, FALSE);

			$this->do_query('SET GROUP_CONCAT_MAX_LEN = 1000000');

			$this->connected = true;
		}

		private function execute_query($sql) {
			if ($this->printStatements) {
				$debug = array_reverse(debug_backtrace());

				echo LINEBREAK;

				foreach ($debug AS $line)
					echo basename($line['file']) . ' ' . $line['line'] . LINEBREAK;

				if (stristr($sql, 'SELECT')) {
					echo $sql . LINEBREAK . LINEBREAK;

					$result = @mysqli_query($this->mysql_link, $sql);

					//mysql server has gone away.  Possibly due to timeout.  So try to reconnect and execute query again
					if (mysqli_errno($this->mysql_link) == 2013) {
						$this->connect();
						$result = @mysqli_query($this->mysql_link, $sql);
					}

					if (!$result) {
						//write to error log
						$errorParams = array(('Database: ' . $this->SQLdatabase),
											 ('Failed statement: ' . $sql),
											 ('MySQL says: ' . mysqli_errno($this->mysql_link) . ' - ' . mysqli_error($this->mysql_link)));

						errorOccurred($errorParams, $this->printErrors);

						mysqli_rollback($this->mysql_link);

						if ($this->tablesLocked)
							$this->unlockTables();

						if (!$this->printErrors)
							header("Location: systemerror");
						
						exit;
					}
				}
				else {
					echo $sql . LINEBREAK . LINEBREAK;
					$result = true;
				}
			}
			else {
				$result = @mysqli_query($this->mysql_link, $sql);

				if (mysqli_errno($this->mysql_link) == 2013) {
					$this->connect();
					$result = @mysqli_query($this->mysql_link, $sql);
				}

				if (!$result) {
					//write to error log
					$errorParams = array(("Database: " . $this->SQLdatabase),
										 ("Failed statement: " . $sql),
										 ("MySQL says: " . mysqli_errno($this->mysql_link) . " - " . mysqli_error($this->mysql_link)));

					errorOccurred($errorParams, $this->printErrors);

					mysqli_rollback($this->mysql_link);

					if ($this->tablesLocked)
						$this->unlockTables();

					if (!$this->printErrors)
						header("Location: systemerror");

					exit;
				}
				else if (strtoupper(substr($sql, 0, 11)) == 'DELETE FROM' && $this->insertDeletion) {
					$deletionSQL = 'INSERT INTO deletionTbl (deletionSQL) VALUES ("' . mysqli_escape_string($this->mysql_link, $sql) . '")';
					@mysqli_query($this->mysql_link, $deletionSQL);

					//mysql server has gone away.  Possibly due to timeout.  So try to reconnect and execute query again
					if (mysqli_errno($this->mysql_link) == 2013) {
						$this->connect();
						@mysqli_query($this->mysql_link, $deletionSQL);
					}
				}
			}

			return $result;
		}
	}

	class mysqlResult {
		private $resultSet;
		public $num_rows;

		function __construct($resultSet) {
			$this->resultSet = $resultSet;
			$this->num_rows = $resultSet->num_rows;
		}
		
		function __destruct() {
		}
		
		function fetch_assoc() {
			$row = $this->resultSet->fetch_assoc();
			
			if (is_array($row) && isset($_SESSION['sessionTimeZone'])) {
				foreach ($row AS $key => $value) {
					// check if value matches datetime format.  Convert to local timezone
					if (isDateTime($value))
						$row[$key] = localizeDate($value, $_SESSION['sessionTimeZone']);
				}
			}

			return $row;
		}

		function fetch_array() {
			return $this->fetch_assoc();
		}

		function getResultSet() {
			return $this->resultSet;
		}

		function data_seek($pos) {
			return $this->resultSet->data_seek($pos);
		}
	}

	function localizeDate($date, $localTimeZone) {
		global $timezones, $companyTimeZone;

		$date = date("Y-m-d H:i:s", strtotime($date));

		if ($companyTimeZone == $localTimeZone)
			return $date;

		if (!array_key_exists($localTimeZone, $timezones))
			return $date;

		$tmpDate = new Date($date);

		$tmpDate->setTZByID($companyTimeZone);
		$tmpDate->convertTZByID($localTimeZone);

		$date = $tmpDate->format("%Y-%m-%d %H:%M:%S"); 

		return $date;
	}

	function standardizeDate($date, $localTimeZone) {
		global $timezones, $companyTimeZone;

		$date = date("Y-m-d H:i:s", strtotime($date));

		if ($companyTimeZone == $localTimeZone)
			return $date;

		if (!array_key_exists($localTimeZone, $timezones))
			return $date;

		$tmpDate = new Date($date);

		$tmpDate->setTZByID($localTimeZone);

		$tmpDate->convertTZByID($companyTimeZone);

		$date = $tmpDate->format("%Y-%m-%d %H:%M:%S");

		return $date;
	}

	// converts one timezone to the other.  Assumes that the date passed has already been localized to localTimeZone
	function convertTimeZone($date, $localTimeZone, $convertToTimeZone) {
		global $timezones;

		$date = date("Y-m-d H:i:s", strtotime($date));

		$dateArray = array("year" => '',
						   "mon" => '',
						   "mday" => '',
						   "hours" => '',
						   "minutes" => '',
						   "seconds" => '',
						   "timezone" => '',
						   "month" => '',
						   "weekday" => '',
						   "shortMon" => '');

		if ($localTimeZone == $convertToTimeZone) {
			$dateArray = getDate(strtotime($date));
			$dateArray['timezone'] = date("T", strtotime($date));
			$dateArray['shortMon'] = date("M", strtotime($date));

			if ($dateArray['mon'] < 10)
				$dateArray['mon'] = "0" . $dateArray['mon'];

			if ($dateArray['mday'] < 10)
				$dateArray['mday'] = "0" . $dateArray['mday'];

			if ($dateArray['hours'] < 10)
				$dateArray['hours'] = "0" . $dateArray['hours'];

			if ($dateArray['minutes'] < 10)
				$dateArray['minutes'] = "0" . $dateArray['minutes'];

			if ($dateArray['seconds'] < 10)
				$dateArray['seconds'] = "0" . $dateArray['seconds'];

			return $dateArray;
		}

		if (!array_key_exists($convertToTimeZone, $timezones)) {
			$dateArray = getDate(strtotime($date));
			$dateArray['timezone'] = date("T", strtotime($date));
			$dateArray['shortMon'] = date("M", strtotime($date));

			if ($dateArray['mon'] < 10)
				$dateArray['mon'] = "0" . $dateArray['mon'];

			if ($dateArray['mday'] < 10)
				$dateArray['mday'] = "0" . $dateArray['mday'];

			if ($dateArray['hours'] < 10)
				$dateArray['hours'] = "0" . $dateArray['hours'];

			if ($dateArray['minutes'] < 10)
				$dateArray['minutes'] = "0" . $dateArray['minutes'];

			if ($dateArray['seconds'] < 10)
				$dateArray['seconds'] = "0" . $dateArray['seconds'];

			return $dateArray;
		}

		$tmpDate = new Date($date);

		$tmpDate->setTZByID($localTimeZone);

		$tmpDate->convertTZByID($convertToTimeZone);

		$date = $tmpDate->format("%Y-%m-%d %H:%M:%S");
		$timezone = $tmpDate->format("%Z");
		$shortMon = $tmpDate->format("%b");

		$dateArray = getDate(strtotime($date));
		$dateArray['timezone'] = $timezone;
		$dateArray['shortMon'] = $shortMon;

		if ($dateArray['mon'] < 10)
			$dateArray['mon'] = "0" . $dateArray['mon'];

		if ($dateArray['mday'] < 10)
			$dateArray['mday'] = "0" . $dateArray['mday'];

		if ($dateArray['hours'] < 10)
			$dateArray['hours'] = "0" . $dateArray['hours'];

		if ($dateArray['minutes'] < 10)
			$dateArray['minutes'] = "0" . $dateArray['minutes'];

		if ($dateArray['seconds'] < 10)
			$dateArray['seconds'] = "0" . $dateArray['seconds'];

		return $dateArray;
	}
	
	function isDateTime($value) {
		return preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value);
	}

?>