<?php
/*
 * Created on 28.09.2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/**
* @version $Id: database.php,v 1.2 2009/03/09 04:01:39 cvs_Zerbe Exp $
* @package Joomla
* @subpackage Database
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

include_once "../../../inc/sv.inc.php";
include_once "../../../functions.php";
include_once "../../../inc/env.inc.php";

// Stelle sicher, dass eine Datenbankverbindung vorhanden ist
if (!isset($GLOBALS['dbi'])) {
    $GLOBALS['dbi'] = mysqli_connect(
        $GLOBALS['env_db_dieewigen_host'], 
        $GLOBALS['env_db_dieewigen_user'], 
        $GLOBALS['env_db_dieewigen_password'], 
        $GLOBALS['env_db_dieewigen_database']
    );
    
    if (!$GLOBALS['dbi']) {
        die("Verbindung zur Datenbank konnte nicht hergestellt werden: " . mysqli_connect_error());
    }
}

// no direct access
defined( 'DIRECT' ) or die( 'Restricted access' );

/**
* Database connector class
* @subpackage Database
* @package Joomla
*/
class database {
	/** @var string Internal variable to hold the query sql */
	var $_sql			= '';
	/** @var int Internal variable to hold the database error number */
	var $_errorNum		= 0;
	/** @var string Internal variable to hold the database error message */
	var $_errorMsg		= '';
	/** @var string Internal variable to hold the prefix used on all database tables */
	var $_table_prefix	= '';
	/** @var Internal variable to hold the connector resource */
	var $_resource		= '';
	/** @var Internal variable to hold the last query cursor */
	var $_cursor		= null;
	/** @var boolean Debug option */
	var $_debug			= 0;
	/** @var int The limit for the query */
	var $_limit			= 0;
	/** @var int The for offset for the limit */
	var $_offset		= 0;
	/** @var int A counter for the number of queries performed by the object instance */
	var $_ticker		= 0;
	/** @var array A log of queries */
	var $_log			= null;
	/** @var string The null/zero date string */
	var $_nullDate		= '0000-00-00 00:00:00';
	/** @var string Quote for named objects */
	var $_nameQuote		= '`';

	/**
	* Database object constructor
	* @param string Database host
	* @param string Database user name
	* @param string Database user password
	* @param string Database name
	* @param string Common prefix for all tables
	* @param boolean If true and there is an error, go offline
	*/
	function database( $user='', $pass='', $host='localhost', $db='', $table_prefix='', $goOffline=true ) {
		// Verwende globale Datenbankverbindung statt lokaler Verbindung
		$this->_resource = $GLOBALS['dbi'];
		
		// Überprüfe, ob die Datenbankverbindung existiert
		if (!$this->_resource) {
			$mosSystemError = 2;
			if ($goOffline) {
				$basePath = dirname( __FILE__ );
				echo "Fehler: Keine Datenbankverbindung verfügbar.";
				exit();
			}
		}
		
		// Datenbank auswählen falls erforderlich
		if ($db != '' && !mysqli_select_db($this->_resource, $db)) {
			$mosSystemError = 3;
			if ($goOffline) {
				$basePath = dirname( __FILE__ );
				echo "Fehler: Datenbank konnte nicht ausgewählt werden.";
				exit();
			}
		}
		
		$this->_table_prefix = $table_prefix;
		// mysqli_query($this->_resource, "SET NAMES 'utf8'");
		$this->_ticker = 0;
		$this->_log = array();
	}
	/**
	 * @param int
	 */
	function debug( $level ) {
		$this->_debug = intval( $level );
	}
	/**
	 * @return int The error number for the most recent query
	 */
	function getErrorNum() {
		return $this->_errorNum;
	}
	/**
	* @return string The error message for the most recent query
	*/
	function getErrorMsg() {
		return str_replace( array( "\n", "'" ), array( '\n', "\'" ), $this->_errorMsg );
	}
	/**
	* Get a database escaped string
	* @return string
	*/
	function getEscaped( $text ) {
		/*
		* Escape string using mysqli
		*/
		if (!$text) {
			return '';
		}
		
		$string = mysqli_real_escape_string($GLOBALS['dbi'], $text);
		return $string;
	}
	/**
	* Get a quoted database escaped string
	* @return string
	*/
	function Quote( $text ) {
		return '\'' . $this->getEscaped( $text ) . '\'';
	}
	/**
	 * Quote an identifier name (field, table, etc)
	 * @param string The name
	 * @return string The quoted name
	 */
	function NameQuote($s) {
		$q = $this->_nameQuote;
		if (strlen($q) == 1) {
			return $q . $s . $q;
		} else {
			return $q[0] . $s . $q[1];
		}
	}
	/**
	 * @return string The database prefix
	 */
	function getPrefix() {
		return $this->_table_prefix;
	}
	/**
	 * @return string Quoted null/zero date string
	 */
	function getNullDate() {
		return $this->_nullDate;
	}
	/**
	* Sets the SQL query string for later execution.
	*
	* This function replaces a string identifier <var>$prefix</var> with the
	* string held is the <var>_table_prefix</var> class variable.
	*
	* @param string The SQL query
	* @param string The offset to start selection
	* @param string The number of results to return
	* @param string The common table prefix
	*/
	function setQuery( $sql, $offset = 0, $limit = 0, $prefix='#__' ) {
		$this->_sql = $this->replacePrefix( $sql, $prefix );
		$this->_limit = intval( $limit );
		$this->_offset = intval( $offset );
	}

	/**
	 * This function replaces a string identifier <var>$prefix</var> with the
	 * string held is the <var>_table_prefix</var> class variable.
	 *
	 * @param string The SQL query
	 * @param string The common table prefix
	 * @author thede, David McKinnis
	 */
	function replacePrefix( $sql, $prefix='#__' ) {
		$sql = trim( $sql );

		$escaped = false;
		$quoteChar = '';

		$n = strlen( $sql );

		$startPos = 0;
		$literal = '';
		while ($startPos < $n) {
			$ip = strpos($sql, $prefix, $startPos);
			if ($ip === false) {
				break;
			}

			$j = strpos( $sql, "'", $startPos );
			$k = strpos( $sql, '"', $startPos );
			if (($k !== FALSE) && (($k < $j) || ($j === FALSE))) {
				$quoteChar	= '"';
				$j			= $k;
			} else {
				$quoteChar	= "'";
			}

			if ($j === false) {
				$j = $n;
			}

			$literal .= str_replace( $prefix, $this->_table_prefix, substr( $sql, $startPos, $j - $startPos ) );
			$startPos = $j;

			$j = $startPos + 1;

			if ($j >= $n) {
				break;
			}

			// quote comes first, find end of quote
			while (TRUE) {
				$k = strpos( $sql, $quoteChar, $j );
				$escaped = false;
				if ($k === false) {
					break;
				}
				$l = $k - 1;
				while ($l >= 0 && $sql[$l] == '\\') {
					$l--;
					$escaped = !$escaped;
				}
				if ($escaped) {
					$j	= $k+1;
					continue;
				}
				break;
			}
			if ($k === FALSE) {
				// error in the query - no end quote; ignore it
				break;
			}
			$literal .= substr( $sql, $startPos, $k - $startPos + 1 );
			$startPos = $k+1;
		}
		if ($startPos < $n) {
			$literal .= substr( $sql, $startPos, $n - $startPos );
		}
		return $literal;
	}
	/**
	* @return string The current value of the internal SQL vairable
	*/
	function getQuery() {
		return "<pre>" . htmlspecialchars( $this->_sql ) . "</pre>";
	}
	/**
	* Execute the query
	* @return mixed A database resource if successful, FALSE if not.
	*/
	function query() {
		global $mosConfig_debug;
		if ($this->_limit > 0 || $this->_offset > 0) {
			$this->_sql .= "\nLIMIT $this->_offset, $this->_limit";
		}
		if ($this->_debug) {
			$this->_ticker++;
	  		$this->_log[] = $this->_sql;
		}
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		
		// Versuche zuerst, die Datenbank mit mysqli_execute_query zu verwenden
		if (function_exists('mysqli_execute_query') && isset($GLOBALS['dbi'])) {
		    try {
		        $this->_cursor = mysqli_execute_query($GLOBALS['dbi'], $this->_sql);
		        return $this->_cursor;
		    } catch (Exception $e) {
		        // Fallback auf alte Methode
		    }
		}
		
		// Fallback zur alten Methode
		$this->_cursor = mysqli_query($this->_resource, $this->_sql);
		if (!$this->_cursor) {
			$this->_errorNum = mysqli_errno($this->_resource);
			$this->_errorMsg = mysqli_error($this->_resource)." SQL=$this->_sql";
			if ($this->_debug) {
				trigger_error(mysqli_error($this->_resource), E_USER_NOTICE);
				//echo "<pre>" . $this->_sql . "</pre>\n";
				if (function_exists('debug_backtrace')) {
					foreach(debug_backtrace() as $back) {
						if (@$back['file']) {
							echo '<br />'.$back['file'].':'.$back['line'];
						}
					}
				}
			}
			return false;
		}
		return $this->_cursor;
	}

	/**
	 * @return int The number of affected rows in the previous operation
	 */
	function getAffectedRows() {
		return mysqli_affected_rows($GLOBALS['dbi']);
	}

	function query_batch($abort_on_error=true, $p_transaction_safe=false) {
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		if ($p_transaction_safe) {
			$si = mysqli_get_server_info($GLOBALS['dbi']);
			preg_match_all("/(\d+)\.(\d+)\.(\d+)/i", $si, $m);
			if ($m[1] >= 4) {
				$this->_sql = 'START TRANSACTION;' . $this->_sql . '; COMMIT;';
			} else if ($m[2] >= 23 && $m[3] >= 19) {
				$this->_sql = 'BEGIN WORK;' . $this->_sql . '; COMMIT;';
			} else if ($m[2] >= 23 && $m[3] >= 17) {
				$this->_sql = 'BEGIN;' . $this->_sql . '; COMMIT;';
			}
		}
		$query_split = preg_split("/[;]+/", $this->_sql);
		$error = 0;
		foreach ($query_split as $command_line) {
			$command_line = trim($command_line);
			if ($command_line != '') {
				$this->_cursor = mysqli_query($GLOBALS['dbi'], $command_line);
				if (!$this->_cursor) {
					$error = 1;
					$this->_errorNum .= mysqli_errno($GLOBALS['dbi']) . ' ';
					$this->_errorMsg .= mysqli_error($GLOBALS['dbi']) . " SQL=$command_line <br />";
					if ($abort_on_error) {
						return $this->_cursor;
					}
				}
			}
		}
		return $error ? false : true;
	}

	/**
	* Diagnostic function
	*/
	function explain() {
		$temp = $this->_sql;
		$this->_sql = "EXPLAIN $this->_sql";
		$this->query();

		if (!($cur = $this->query())) {
			return null;
		}
		$first = true;

		$buf = "<table cellspacing=\"1\" cellpadding=\"2\" border=\"0\" bgcolor=\"#000000\" align=\"center\">";
		$buf .= $this->getQuery();
		while ($row = mysqli_fetch_assoc($cur)) {
			if ($first) {
				$buf .= "<tr>";
				foreach ($row as $k=>$v) {
					$buf .= "<th bgcolor=\"#ffffff\">" . htmlspecialchars($k) . "</th>";
				}
				$buf .= "</tr>";
				$first = false;
			}
			$buf .= "<tr>";
			foreach ($row as $k=>$v) {
				$buf .= "<td bgcolor=\"#ffffff\">" . htmlspecialchars($v) . "</td>";
			}
			$buf .= "</tr>";
		}
		$buf .= "</table><br />&nbsp;";
		mysqli_free_result($cur);

		$this->_sql = $temp;

		return "<div style=\"background-color:#FFFFCC\" align=\"left\">$buf</div>";
	}
	/**
	* @return int The number of rows returned from the most recent query.
	*/
	function getNumRows($cur=null) {
		return mysqli_num_rows($cur ? $cur : $this->_cursor);
	}

	/**
	* This method loads the first field of the first row returned by the query.
	*
	* @return The value returned in the query or null if the query failed.
	*/
	function loadResult() {
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($row = mysqli_fetch_row($cur)) {
			$ret = $row[0];
		}
		mysqli_free_result($cur);
		return $ret;
	}
	/**
	* Load an array of single field results into an array
	*/
	function loadResultArray($numinarray = 0) {
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = mysqli_fetch_row($cur)) {
			$array[] = $row[$numinarray];
		}
		mysqli_free_result($cur);
		return $array;
	}
	/**
	* Load a assoc list of database rows
	* @param string The field name of a primary key
	* @return array If <var>key</var> is empty as sequential list of returned records.
	*/
	function loadAssocList($key='') {
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = mysqli_fetch_assoc($cur)) {
			if ($key) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		mysqli_free_result($cur);
		return $array;
	}
	/**
	* This global function loads the first row of a query into an object
	*
	* If an object is passed to this function, the returned row is bound to the existing elements of <var>object</var>.
	* If <var>object</var> has a value of null, then all of the returned query fields returned in the object.
	* @param string The SQL query
	* @param object The address of variable
	*/
	function loadObject(&$object) {
		if ($object != null) {
			if (!($cur = $this->query())) {
				return false;
			}
			if ($array = mysqli_fetch_assoc($cur)) {
				mysqli_free_result($cur);
				mosBindArrayToObject($array, $object, null, null, false);
				return true;
			} else {
				return false;
			}
		} else {
			if ($cur = $this->query()) {
				if ($object = mysqli_fetch_object($cur)) {
					mysqli_free_result($cur);
					return true;
				} else {
					$object = null;
					return false;
				}
			} else {
				return false;
			}
		}
	}
	/**
	* Load a list of database objects
	* @param string The field name of a primary key
	* @return array If <var>key</var> is empty as sequential list of returned records.
	* If <var>key</var> is not empty then the returned array is indexed by the value
	* the database key.  Returns <var>null</var> if the query fails.
	*/
	function loadObjectList($key='') {
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = mysqli_fetch_object($cur)) {
			if ($key) {
				$array[$row->$key] = $row;
			} else {
				$array[] = $row;
			}
		}
		mysqli_free_result($cur);
		return $array;
	}
	/**
	* @return The first row of the query.
	*/
	function loadRow() {
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($row = mysqli_fetch_row($cur)) {
			$ret = $row;
		}
		mysqli_free_result($cur);
		return $ret;
	}
	/**
	* Load a list of database rows (numeric column indexing)
	* @param string The field name of a primary key
	* @return array If <var>key</var> is empty as sequential list of returned records.
	* If <var>key</var> is not empty then the returned array is indexed by the value
	* the database key.  Returns <var>null</var> if the query fails.
	*/
	function loadRowList($key='') {
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = mysqli_fetch_row($cur)) {
			if ($key) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		mysqli_free_result($cur);
		return $array;
	}
	/**
	* Document::db_insertObject()
	*
	* { Description }
	*
	* @param [type] $keyName
	* @param [type] $verbose
	*/
	function insertObject( $table, &$object, $keyName = NULL, $verbose=false ) {
		$fmtsql = "INSERT INTO $table ( %s ) VALUES ( %s ) ";
		$fields = array();
		foreach (get_object_vars( $object ) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}
			$fields[] = $this->NameQuote( $k );
			$values[] = $this->Quote( $v );
		}
		$this->setQuery( sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) ) );
		($verbose) && print "$sql<br />\n";
		if (!$this->query()) {
			return false;
		}
		$id = mysqli_insert_id($GLOBALS['dbi']);
		($verbose) && print "id=[$id]<br />\n";
		if ($keyName && $id) {
			$object->$keyName = $id;
		}
		return true;
	}

	/**
	* Document::db_updateObject()
	*
	* { Description }
	*
	* @param [type] $updateNulls
	*/
	function updateObject( $table, &$object, $keyName, $updateNulls=true ) {
		$fmtsql = "UPDATE $table SET %s WHERE %s";
		$tmp = array();
		foreach (get_object_vars( $object ) as $k => $v) {
			if( is_array($v) or is_object($v) or $k[0] == '_' ) { // internal or NA field
				continue;
			}
			if( $k == $keyName ) { // PK not to be updated
				$where = $keyName . '=' . $this->Quote( $v );
				continue;
			}
			if ($v === NULL && !$updateNulls) {
				continue;
			}
			if( $v == '' ) {
				$val = "''";
			} else {
				$val = $this->Quote( $v );
			}
			$tmp[] = $this->NameQuote( $k ) . '=' . $val;
		}
		$this->setQuery( sprintf( $fmtsql, implode( ",", $tmp ) , $where ) );
		return $this->query();
	}

	/**
	* @param boolean If TRUE, displays the last SQL statement sent to the database
	* @return string A standised error message
	*/
	function stderr( $showSQL = false ) {
		return "DB function failed with error number $this->_errorNum"
		."<br /><font color=\"red\">$this->_errorMsg</font>"
		.($showSQL ? "<br />SQL = <pre>$this->_sql</pre>" : '');
	}

	function insertid() {
		return mysqli_insert_id($GLOBALS['dbi']);
	}

	function getVersion() {
		return mysqli_get_server_info($GLOBALS['dbi']);
	}

	/**
	 * @return array A list of all the tables in the database
	 */
	function getTableList() {
		$this->setQuery( 'SHOW TABLES' );
		return $this->loadResultArray();
	}
	/**
	 * @param array A list of table names
	 * @return array A list the create SQL for the tables
	 */
	function getTableCreate( $tables ) {
		$result = array();

		foreach ($tables as $tblval) {
			$this->setQuery( 'SHOW CREATE table ' . $this->getEscaped( $tblval ) );
			$rows = $this->loadRowList();
			foreach ($rows as $row) {
				$result[$tblval] = $row[1];
			}
		}

		return $result;
	}
	/**
	 * @param array A list of table names
	 * @return array An array of fields by table
	 */
	function getTableFields( $tables ) {
		$result = array();

		foreach ($tables as $tblval) {
			$this->setQuery( 'SHOW FIELDS FROM ' . $tblval );
			$fields = $this->loadObjectList();
			foreach ($fields as $field) {
				$result[$tblval][$field->Field] = preg_replace("/[(0-9)]/",'', $field->Type );
			}
		}

		return $result;
	}

	/**
	* Fudge method for ADOdb compatibility
	*/
	function GenID( $foo1=null, $foo2=null ) {
		return '0';
	}
}

// Verbesserte sqlescape-Funktion mit mysqli_real_escape_string
function sqlescape($d) { 
    global $GLOBALS;
    if (isset($GLOBALS['dbi'])) {
        return '"' . mysqli_real_escape_string($GLOBALS['dbi'], $d) . '"';
    } else {
        // Fallback zur alten Methode
        return '"' . addslashes($d) . '"';
    }
}
?>