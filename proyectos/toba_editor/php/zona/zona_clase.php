<?php
require_once("nucleo/lib/zona.php");

class zona_clase extends zona
{
	function cargar_info($editable=null)
	//Carga el EDITABLE que se va a manejar dentro de la ZONA
	{
		global $ADODB_FETCH_MODE, $db, $cronometro;		
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = 	"	SELECT	*
					FROM	apex_clase
					WHERE	proyecto='{$this->editable_id[0]}'
					AND		clase='{$this->editable_id[1]}';";
		$rs =& $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			throw new toba_excepcion("ZONA-CLASE: NO se pudo cargar el editable $proyecto,$item - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
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

	function obtener_html_barra_superior()
	//Genera el HTML de la BARRA
	{
		//global $cronometro;
		//$cronometro->marcar('basura',apex_nivel_nucleo);

		echo "<table  width='100%'  class='tabla-0'><tr>";
		echo "<td width='90%' class='barra-obj-tit1'>&nbsp;&nbsp;EDITOR de CLASES";
		//echo toba_recurso::imagen_apl("zona/objetos.gif",true);
		echo "</td>";
		$this->obtener_html_barra_vinculos();
		$this->obtener_html_barra_especifico();
		echo "<td  class='barra-obj-tit' width='15'>&nbsp;</td>";
		echo "</tr></table>\n";

		//Nombre de la operacion
		echo "<table  width='100%'  class='tabla-0'><tr>";
		echo "	<td width='250' class='barra-item-id'>";
		echo "&nbsp;".$this->editable_id[0]." - ".$this->editable_id[1]."&nbsp;";
		echo "</td>";
		echo "<td width='60%' class='barra-item-tit'>&nbsp;".$this->editable_info['descripcion_corta']."</td>";
		echo "</tr></table>\n";
		//$cronometro->marcar('ZONA: Barra SUPERIOR',apex_nivel_nucleo);
	}
//-----------------------------------------------------
}
?>