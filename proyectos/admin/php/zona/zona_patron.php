<?php
require_once("zona_editor.php");

class zona_patron extends zona_editor
{
	function cargar_info($editable=null)
	//Carga el EDITABLE que se va a manejar dentro de la ZONA
	{
		global $ADODB_FETCH_MODE, $db, $cronometro;		
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = 	"	SELECT	*
					FROM	apex_patron
					WHERE	proyecto ='{$this->editable_id[0]}'
					AND		patron ='{$this->editable_id[1]}';";
		$rs =& $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			throw new excepcion_toba("ZONA-CLASE: NO se pudo cargar el editable $proyecto,$item - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
			return false;
		}elseif($rs->EOF){
			echo ei_mensaje("ZONA-CLASE: El editable solicitado no existe","info");
			return false;
		}else{
			$this->editable_info = current($rs->getArray());
			//ei_arbol($this->editable_info,"EDITABLE");
			$this->editable_id = array( $this->editable_id[0],$this->editable_id[1] );
			$this->editable_cargado = true;
			return true;
		}	
	}

}
?>