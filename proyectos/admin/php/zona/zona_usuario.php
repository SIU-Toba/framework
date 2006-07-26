<?php
require_once("nucleo/lib/zona.php");

class zona_usuario extends zona
{
	function cargar_descripcion($editable=null)
	//Carga el EDITABLE que se va a manejar dentro de la ZONA
	{
		$sql = 	"	SELECT	*
					FROM	apex_usuario
					WHERE	usuario='{$this->editable_id[0]}'";
		$rs = toba::get_db()->consultar($sql);
		if(!$rs){
			echo ei_mensaje("ZONA-USUARIO: El editable solicitado no existe","info");
			return false;
		}else{
			$this->editable_info = $rs[0];
			//ei_arbol($this->editable_info,"EDITABLE");
			$this->editable_id = array( $this->editable_id[0]);
			$this->editable_cargado = true;
			return true;
		}	
	}

	function obtener_html_barra_info()
	//Muestra la seccion INFORMATIVA (izquierda) de la barra
	{
		echo "	<td width='250' class='barra-item-id'>";
		echo "&nbsp;".$this->editable_id[0]."&nbsp;";
//		echo "&nbsp;".$this->editable_id[1]."&nbsp;";
//		echo "&nbsp;".$this->editable_id[1]."@".$this->editable_id[0]."&nbsp;";
		echo "</td>";
		echo "<td width='60%' class='barra-item-tit'>&nbsp;".$this->editable_info['nombre']."</td>";
	}	
	
	function obtener_html_barra_inferior()	
	{
		//echo "BARRA inferior<br>"	;	
	}
}
?>