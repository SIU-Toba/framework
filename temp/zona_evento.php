<?php
require_once("nucleo/browser/zona/zona.php");

class zona_evento extends zona
{
	function zona_evento($id,$proyecto,&$solicitud)
	{
		$this->listado = "evento";
		parent::zona($id,$proyecto,$solicitud);
	}

	function cargar_editable($editable=null)
	//Carga el EDITABLE que se va a manejar dentro de la ZONA
	{
		global $ADODB_FETCH_MODE, $db, $cronometro;		
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
//    ei_arbol($editable);

		if(!isset($editable)){
			if(!isset($this->editable_propagado)){
				ei_mensaje("No se especifico el editable a cargar","error");
				return false;
			}else{
				//Los editables se propagan como arrays comunes
				$clave[0] = $this->editable_propagado[0];
			}
		}else{
			//Cuando se cargan explicitamente (generalmente desde el ABM que maneja la EXISTENCIA del EDITABLE)
			//Las claves de los registros que los ABM manejan son asociativas
			$clave[0] = $editable['cod_evento'];
		}
    $db["siu-quilmes"][apex_db_con]->debug = true; 
		$sql = 	"	SELECT	*
					FROM	con_eventos
					WHERE	cod_evento='{$clave[0]}'";  

        // ATENCION!!: La fuente de datos es totalmente dependiente del proyecto.
        abrir_fuente_datos("siu-quilmes");                                        
        
		    $rs =& $db["siu-quilmes"][apex_db_con]->Execute($sql); 
        if(!$rs){
			monitor::evento("bug","ZONA-EVENTO: NO se pudo cargar el editable ".$clave[0]." - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
			return false;
		}elseif($rs->EOF){
			echo ei_mensaje("ZONA-EVENTO: El editable solicitado no existe","info");
			return false;
		}else{
			$this->editable_info = current($rs->getArray());
			//ei_arbol($this->editable_info,"EDITABLE");
			$this->editable_id = array( $clave[0]);
			$this->editable_cargado = true;
			return true;
		}	
	}

	function obtener_html_barra_info()
	//Muestra la seccion INFORMATIVA (izquierda) de la barra
	{
        $descripcion = $this->editable_info['descripcion'];
        $vinculo_listado = $this->solicitud->vinculador->obtener_vinculo_a_item('siu-quilmes', 
                                                           '/abms/eventos/listado', NULL, true);
		echo "	<td width='250' class='barra-item-id'>";
        echo $vinculo_listado;
		echo "</td>";
		echo "<td width='90%' class='barra-item-tit'><strong>EVENTO: $descripcion</strong></td>";
	}	
	
    function obtener_html_barra_especifico()
	//Esto es especifico de cada EDITABLE
	{	
	}
    
    
	function obtener_html_barra_inferior()	
	{

	}
}
?>