<?php
require_once('admin/objetos_toba/ci_editores_toba.php'); 

class ci_principal extends ci_editores_toba
{
	protected $db_tablas;

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

	function evt__base__carga()
	{
		return $this->get_entidad()->tabla("base")->get();
	}

	function evt__base__modificacion($datos)
	{
		$this->get_entidad()->tabla("base")->set($datos);
	}

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
	
	function evt__post_cargar_datos_dependencias__2()
	{
		//Agrego el evento de cargar de la DB
		$evt = eventos::evento_estandar("leer_db","Cargar COLUMNAS tabla",true,null,null,true,false);
		$this->dependencias["columnas"]->agregar_evento( $evt );
	}
	
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
		abrir_fuente_datos($id_fuente, $proyecto);
		$fuente = toba::get_fuente_datos($id_fuente);
		try{
			return $fuente->obtener_definicion_columnas($tabla);
		}catch(excepcion_toba $e){
			toba::get_cola_mensajes()->agregar( $e->getMessage() );
		}
	}	

	//*******************************************************************
	//** PROCESAR  ******************************************************
	//*******************************************************************/

	function evt__procesar()
	{
		//Seteo los datos asociados al uso de este editor
		$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"proyecto",toba::get_hilo()->obtener_proyecto() );
		//$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"proyecto","toba_testing" );
		$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"clase_proyecto", "toba" );
		$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"clase", "objeto_datos_tabla" );
		//Sincronizo el DBT
		$this->get_entidad()->sincronizar();	
	}
	//-------------------------------------------------------------------
}
?>