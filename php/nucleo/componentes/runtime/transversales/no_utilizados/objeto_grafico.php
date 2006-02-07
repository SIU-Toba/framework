<?php
require_once('nucleo/componentes/runtime/objeto.php');

//error_reporting(E_ALL ^ E_NOTICE);

class objeto_grafico extends objeto
{
	var $definicion;
	var $datos;
	
	function objeto_grafico($id)
	{
		parent::objeto($id);
		//Armo el array de definiciones parseando el campo INICIALIZACION
		$this->definicion = parsear_propiedades($this->info_grafico["inicializacion"]);
		$this->definicion["tipo"] = $this->info_grafico["grafico"];
	}
//--------------------------------------------------------------------------------

	function obtener_definicion_db()
/*
 	@@acceso:
	@@desc: 
*/
	{
		$sql = parent::obtener_definicion_db();
		$sql["info_grafico"]["sql"] = "SELECT	grafico,
													sql,
													inicializacion
										FROM	apex_objeto_grafico
										WHERE	objeto_grafico_proyecto='".$this->id[0]."'
										AND 	objeto_grafico='".$this->id[1]."';";
		$sql["info_grafico"]["tipo"]="1";
		$sql["info_grafico"]["estricto"]="1";
		return $sql;
	}
//--------------------------------------------------------------------------------------------

	function cargar_datos($where=null,$from=null)
	{
		global $db,$ADODB_FETCH_MODE;
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
        return true;
	}
//--------------------------------------------------------------------------------

	function obtener_html()
	//Genera el grafico.
	{
		if( $this->info_proceso_indice > 0 ){
			$this->mostrar_info_proceso();
		}else{
			include_once("nucleo/browser/interface/grafico.php");
		    $grafico =& new grafico();
			$grafico->cargar_definicion($this->definicion);
			echo "<div align='center'>\n";			
			echo "<table class='objeto-base'>\n";
			echo "<tr><td>";
			$this->barra_superior($this->definicion["titulo"]);
			echo "</td></tr>\n";
			echo "<tr><td>";
			echo "<TABLE width='100%' class='tabla-0'>";
			echo "<tr><td>\n";
		    $grafico->incrustar_imagen();	
			echo "</td></tr>\n";
			echo "</table>\n";
			echo "</td></tr>\n";
			echo "</table>\n";
			echo "</div>\n";
		}
	}

//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
//-----------  Formateo de series y ejes para GRAFICOS PARTICULARES  -------------
//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------

	function formatear_datos_torta()
	//Formateo de datos para graficos de tipo TORTA
	//El formato esperado es un RECORDSET de dos columnas: categoria/valor.
	{
		if(count($this->datos)>0){
			for($a=0;$a<count($this->datos);$a++)
			{
				$eje_a[$a] = $this->datos[$a][0];
				$serie[$a] = $this->datos[$a][1];
			}
			$this->definicion["eje_a"] = $eje_a;
			$this->definicion["serie"] = $serie;
		}else{
			$this->registrar_info_proceso("No se obtuvieron DATOS","error");		
		}
	}
//--------------------------------------------------------------------------------

	function formatear_datos_bar()
	//Formateo de datos para graficos de tipo BAR
	//El formato esperado es un RECORDSET de dos columnas: categoria/valor.
	{
		for($a=0;$a<count($this->datos);$a++)
		{
			$eje_a[$a] = $this->datos[$a][0];
			$serie[$a] = $this->datos[$a][1];
		}
		$this->definicion["eje_a"] = $eje_a;
		$this->definicion["serie"] = $serie;
	}
//--------------------------------------------------------------------------------

}
?>
