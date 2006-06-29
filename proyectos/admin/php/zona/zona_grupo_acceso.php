<?php
require_once("nucleo/lib/zona.php");

class zona_grupo_acceso extends zona
{
	function zona_grupo_acceso($id,$proyecto,&$solicitud)
	{
		$this->listado = "usuario";
		parent::zona($id,$proyecto,$solicitud);
	}

	function cargar_editable($editable=null)
	//Carga el EDITABLE que se va a manejar dentro de la ZONA
	{
		global $ADODB_FETCH_MODE, $db, $cronometro;		
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
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
			$clave[1] = $editable['usuario_grupo_acc'];
		}
		$sql = 	"	SELECT	*
					FROM	apex_usuario_grupo_acc
					WHERE	proyecto = '{$clave[0]}'
					AND		usuario_grupo_acc = '{$clave[1]}';";
		//echo $sql;
		$rs =& $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			throw new excepcion_toba("ZONA - GRUPO ACCESO: NO se pudo cargar el editable ".$clave[0].",".$clave[1]." - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
			return false;
		}elseif($rs->EOF){
			echo ei_mensaje("ZONA - GRUPO ACCESO: El editable solicitado no existe","info");
			return false;
		}else{
			$this->editable_info = current($rs->getArray());
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
		echo "<td width='60%' class='barra-item-tit'>&nbsp;".$this->editable_info['nombre']."</td>";
	}

	function obtener_html_barra_inferior()	
	{
		//echo "BARRA inferior<br>"	;	
	}
}
?>