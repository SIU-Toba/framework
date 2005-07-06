<?php
include("db_registros_s.php");

class db_registros_s_i extends db_registros_s
{
	var $tabla;

	function __construct($id, $tabla, $fuente)
	{
		$this->tabla = $tabla;
		$definicion = $this->generar_definicion($fuente);
		parent::__construct($definicion, $fuente);
	}
	//-------------------------------------------------------------------------------

	function generar_definicion($fuente)
	{
		global $db;
		$metadatos = $db[$fuente][apex_db]->obtener_metadatos( $this->tabla );
		$metadatos = $this->filtrar_metadatos( $metadatos );
		//Busco las claves
		foreach($metadatos['constraints'] as $constraint){
			if($constraint['contype']=="p"){
				$pk = $constraint['conkey'];
				break;
			}
		}
		$num_col_pk = explode(",",substr($pk,1,strlen($pk)-2));
		//ei_arbol($numcols_pk,"PK");
		
		//Creo la definicion
		//ATENCION: Faltan lo UNIQUE
		$definicion['tabla'] = $this->tabla;
		$secuencia = 0;
		for($a=0;$a<count($metadatos['columnas']);$a++)
		{
			//Nombre columna
			$definicion['columna'][$a]['nombre'] = $metadatos['columnas'][$a]['columna'];
			if(in_array($metadatos['columnas'][$a]['num_col'], $num_col_pk)){
				//Es una clave
				$definicion['columna'][$a]['pk'] = 1;
			}
			//-<2>- SECUENCIAS
			if(preg_match("/nextval/",$metadatos['columnas'][$a]['default'])){
				$definicion['secuencia'][$secuencia]['col']=$metadatos['columnas'][$a]['columna'];
				$temp = preg_split("|\"|", $metadatos['columnas'][$a]['default']);
				$definicion['columna'][$a]['secuencia'] = $temp[1];
			}
			//-<3>- NO DUPLICADOS
			if($metadatos['columnas'][$a]['not_null']=="t"){
				$definicion['columna'][$a]['no_nulo'] = 1;
			}
		}
		//ei_arbol($definicion);
		//ei_arbol($metadatos);
		return $definicion;
	}
	//-------------------------------------------------------------------------------

	function filtrar_metadatos($metadatos)
	//Permite que un hijo filtre metadatos
	//Esto es para los casos
	{
		return $metadatos;
	}
	//-------------------------------------------------------------------------------
}
?>