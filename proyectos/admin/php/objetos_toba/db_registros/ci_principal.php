<?php
require_once('objetos_toba/ci_editores_toba.php'); 

class ci_principal extends ci_editores_toba
{
	protected $db_tablas;
	protected $clase_actual = 'objeto_datos_tabla';	

	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_entidad()->tabla('efss')->info(true),"efsS");
		//ei_arbol($this->get_estado_sesion(),"Estado sesion");
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		return $propiedades;
	}

	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function evt__prop_basicas__carga()
	{
		return $this->get_entidad()->tabla("prop_basicas")->get();
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_entidad()->tabla("prop_basicas")->set($datos);
		
	}

	//*******************************************************************
	//**  COLUMNAS  *****************************************************
	//*******************************************************************
	
	function evt__columnas__carga()
	{
		return $this->get_entidad()->tabla('columnas')->get_filas(null,true);	
	}

	function evt__columnas__modificacion($datos)
	{
		$this->get_entidad()->tabla('columnas')->procesar_filas($datos);
	}

	//-- Generacion automatica de columnas!!
	
	function evt__columnas__leer_db()
	{
		$columnas = $this->obtener_definicion_columnas();
		//ei_arbol($columnas);		
		$dbr = $this->get_entidad()->tabla("columnas");
		for($a=0;$a<count($columnas);$a++){
			try{
				$dbr->nueva_fila($columnas[$a]);
			}catch(excepcion_toba $e){
				toba::get_cola_mensajes()->agregar("Error agregando la COLUMNA '{$columnas[$a]['columna']}'. " . $e->getMessage());
			}
		}
	}

	function obtener_definicion_columnas()
	//Utilizo ADODB para recuperar los metadatos
	{
		//-[ 1 ]- Obtengo datos
		$tabla = $this->get_entidad()->tabla("prop_basicas")->get_fila_columna(0,"tabla");
		$reg = $this->get_entidad()->tabla("base")->get();
		$proyecto = $reg['fuente_datos_proyecto'];
		$id_fuente = $reg['fuente_datos'];
		$fuente = toba::get_db($id_fuente);
		try{
			return $fuente->obtener_definicion_columnas($tabla);
		}catch(excepcion_toba $e){
			toba::get_cola_mensajes()->agregar( $e->getMessage() );
		}
	}	

}
?>