<?php

/**
  V3.40 7 April 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	
  Set tabs to 4 for best viewing.
 
*/

class ADODB2_mssql extends ADODB_DataDict {
	
	function ActualType($meta)
	{
		switch(strtoupper($meta)) {
		case 'C': return 'VARCHAR';
		case 'X': return 'TEXT';
		
		case 'C2': return 'NVARCHAR';
		case 'X2': return 'NTEXT';
		
		case 'B': return 'IMAGE';
			
		case 'D': return 'DATETIME';
		case 'T': return 'DATETIME';
		case 'L': return 'BIT';
		
		case 'I': return 'INT'; 
		case 'I1': return 'TINYINT';
		case 'I2': return 'SMALLINT';
		case 'I4': return 'INT';
		case 'I8': return 'BIGINT';
		
		case 'F': return 'REAL';
		case 'N': return 'NUMERIC';
		default:
			return $meta;
		}
	}
	
	
	function AddColumnSQL($tabname, $flds)
	{	
		if ($this->schema) $tabname = $this->schema.'.'.$tabname;
		$f = array();
		list($lines,$pkey) = $this->_GenFields($flds);
		$s = "ALTER TABLE $tabname $this->addCol";
		foreach($lines as $v) {
			$f[] = "\n $v";
		}
		$s .= implode(',',$f);
		$sql[] = $s;
		return $sql;
	}
	
	function AlterColumnSQL($tabname, $flds)
	{
		if ($this->schema) $tabname = $this->schema.'.'.$tabname;
		$sql = array();
		list($lines,$pkey) = $this->_GenFields($flds);
		foreach($lines as $v) {
			$sql[] = "ALTER TABLE $tabname $this->alterCol $v";
		}

		return $sql;
	}
	
	function DropColumnSQL($tabname, $flds)
	{
		if ($this->schema) $tabname = $this->schema.'.'.$tabname;
		if (!is_array($flds)) $flds = explode(',',$flds);
		$f = array();
		$s = "ALTER TABLE $tabname";
		foreach($flds as $v) {
			$f[] = "\n$this->dropCol $v";
		}
		$s .= implode(',',$f);
		$sql[] = $s;
		return $sql;
	}
	
	// return string must begin with space
	function _CreateSuffix($fname,$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint)
	{	
		$suffix = '';
		if (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
		if ($fautoinc) $suffix .= ' IDENTITY(1,1)';
		if ($fnotnull) $suffix .= ' NOT NULL';
		if ($fconstraint) $suffix .= ' '.$fconstraint;
		return $suffix;
	}
	
	/*
CREATE TABLE 
    [ database_name.[ owner ] . | owner. ] table_name 
    ( { < column_definition > 
        | column_name AS computed_column_expression 
        | < table_constraint > ::= [ CONSTRAINT constraint_name ] }

            | [ { PRIMARY KEY | UNIQUE } [ ,...n ] 
    ) 

[ ON { filegroup | DEFAULT } ] 
[ TEXTIMAGE_ON { filegroup | DEFAULT } ] 

< column_definition > ::= { column_name data_type } 
    [ COLLATE < collation_name > ] 
    [ [ DEFAULT constant_expression ] 
        | [ IDENTITY [ ( seed , increment ) [ NOT FOR REPLICATION ] ] ]
    ] 
    [ ROWGUIDCOL] 
    [ < column_constraint > ] [ ...n ] 

< column_constraint > ::= [ CONSTRAINT constraint_name ] 
    { [ NULL | NOT NULL ] 
        | [ { PRIMARY KEY | UNIQUE } 
            [ CLUSTERED | NONCLUSTERED ] 
            [ WITH FILLFACTOR = fillfactor ] 
            [ON {filegroup | DEFAULT} ] ] 
        ] 
        | [ [ FOREIGN KEY ] 
            REFERENCES ref_table [ ( ref_column ) ] 
            [ ON DELETE { CASCADE | NO ACTION } ] 
            [ ON UPDATE { CASCADE | NO ACTION } ] 
            [ NOT FOR REPLICATION ] 
        ] 
        | CHECK [ NOT FOR REPLICATION ] 
        ( logical_expression ) 
    } 

< table_constraint > ::= [ CONSTRAINT constraint_name ] 
    { [ { PRIMARY KEY | UNIQUE } 
        [ CLUSTERED | NONCLUSTERED ] 
        { ( column [ ASC | DESC ] [ ,...n ] ) } 
        [ WITH FILLFACTOR = fillfactor ] 
        [ ON { filegroup | DEFAULT } ] 
    ] 
    | FOREIGN KEY 
        [ ( column [ ,...n ] ) ] 
        REFERENCES ref_table [ ( ref_column [ ,...n ] ) ] 
        [ ON DELETE { CASCADE | NO ACTION } ] 
        [ ON UPDATE { CASCADE | NO ACTION } ] 
        [ NOT FOR REPLICATION ] 
    | CHECK [ NOT FOR REPLICATION ] 
        ( search_conditions ) 
    } 


	*/
	
	/*
	CREATE [ UNIQUE ] [ CLUSTERED | NONCLUSTERED ] INDEX index_name 
    ON { table | view } ( column [ ASC | DESC ] [ ,...n ] ) 
		[ WITH < index_option > [ ,...n] ] 
		[ ON filegroup ]
		< index_option > :: = 
		    { PAD_INDEX | 
		        FILLFACTOR = fillfactor | 
		        IGNORE_DUP_KEY | 
		        DROP_EXISTING | 
		    STATISTICS_NORECOMPUTE | 
		    SORT_IN_TEMPDB  
		}
*/
	function _IndexSQL($idxname, $tabname, $flds, $idxoptions)
	{
		if (isset($idxoptions['REPLACE'])) $sql[] = "DROP INDEX $idxname";
		if (isset($idxoptions['UNIQUE'])) $unique = ' UNIQUE';
		else $unique = '';
		if (is_array($flds)) $flds = implode(', ',$flds);
		if (isset($idxoptions['CLUSTERED'])) $clustered = ' CLUSTERED';
		else $clustered = '';
		
		$s = "CREATE$unique$clustered INDEX $idxname ON $tabname ($flds)";
		if (isset($idxoptions[$this->upperName])) $s .= $idxoptions[$this->upperName];
		$sql[] = $s;
		
		return $sql;
	}
}
?>