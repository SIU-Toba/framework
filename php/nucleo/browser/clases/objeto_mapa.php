<?php
require_once("objeto.php");	//Ancestro de todos los 

class objeto_mapa extends objeto
{
	
	function __construct($id)
	{
		parent::objeto($id);
	}
	//------------------------------------------------------------------------

	function obtener_definicion_db()
	{
		$sql = parent::obtener_definicion_db();
		//------------- Cuadro ----------------
		$sql["info_mapa"]["sql"] = "SELECT	sql		as	sql,			
								descripcion				as	descripcion
					 FROM		apex_objeto_mapa
					 WHERE	objeto_mapa_proyecto='".$this->id[0]."'	
					 AND		objeto_mapa='".$this->id[1]."';";
		$sql["info_mapa"]["estricto"]="1";
		$sql["info_mapa"]["tipo"]="1";
		return $sql;
	}
	//------------------------------------------------------------------------

	function obtener_html()
	{
		echo $this->info_mapa['descripcion'];	
	}
	//------------------------------------------------------------------------

	function cargar_datos($where=null,$from=null)
	{
/*		global $db,$ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$sql = sql_agregar_clausulas_where($this->info_grafico["sql"],$where);
		$sql = sql_agregar_tablas_from($sql,$from);
    	$rs = $db[$this->info["fuente"]][apex_db_con]->Execute($sql);
		if(!$rs){//SQL mal formado
			$this->observar("error","OBJETO GRAFICO [cargar_datos] - No se genero un recordset [SQL] $sql - [ERROR] " . 
							$db[$this->info["fuente"]][apex_db_con]->ErrorMsg(),true,true,true);
		}
		if($rs->EOF){//NO existe el registro
			$this->registrar_info_proceso("No hay registros");
		}
		$this->datos = $rs->getArray();
		//ei_arbol($this->datos,"DATOS");
		//Llamo a la formateadora dependiente del tipo de grafico
		$formateador = "formatear_datos_" . $this->info_grafico["grafico"];
		$this->$formateador();
        return true;*/
	}
	//--------------------------------------------------------------------------------
}
?>
