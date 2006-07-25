<?php
require_once("nucleo/lib/zona.php");

class zona_fuente extends zona
{
	function zona_fuente($id,$proyecto,&$solicitud)
	{
		$this->listado = "datos";
		parent::zona($id,$proyecto,$solicitud);
	}

	function cargar_editable($editable=null)
	//Carga el EDITABLE que se va a manejar dentro de la ZONA
	{
		if(!isset($editable)){
			if(!isset($this->editable_propagado)){
				ei_mensaje("No se especifico el editable a cargar","error");
				return false;
			}else{
				//Los editables se propagan como arrays comunes
				$clave[0] = $this->editable_propagado[0];
				$clave[1] = $this->editable_propagado[1];
			}
		}else{
			//Cuando se cargan explicitamente (generalmente desde el ABM que maneja la EXISTENCIA del EDITABLE)
			//Las claves de los registros que los ABM manejan son asociativas
			$clave[0] = $editable['proyecto'];
			$clave[1] = $editable['fuente_datos'];
		}
		$sql = 	"	SELECT	*
					FROM	apex_fuente_datos
					WHERE	proyecto='{$clave[0]}'
					AND		fuente_datos='{$clave[1]}';";
		//echo $sql;
		$rs = toba::get_db()->consultar($sql);
		if(!$rs) {
			echo ei_mensaje("ZONA-FUENTE: El editable solicitado no existe","info");
			return false;
		}else{
			$this->editable_info = $rs[0];
			//ei_arbol($this->editable_info,"EDITABLE");
			$this->editable_id = array( $clave[0],$clave[1] );
			$this->editable_cargado = true;
			return true;
		}	
	}

	function obtener_html_barra_info()
	//Muestra la seccion INFORMATIVA (izquierda) de la barra
	{
		echo "	<td width='250' class='barra-item-id'>";
//		echo "&nbsp;".$this->editable_id[0]."&nbsp;";
		echo "&nbsp;".$this->editable_id[1]."&nbsp;";
//		echo "&nbsp;".$this->editable_id[1]."@".$this->editable_id[0]."&nbsp;";
		echo "</td>";
		echo "<td width='60%' class='barra-item-tit'>&nbsp;".$this->editable_info['descripcion_corta']."</td>";
	}

	function obtener_html_barra_inferior()	
	{
		//echo "BARRA inferior<br>"	;	
	}
}
?>