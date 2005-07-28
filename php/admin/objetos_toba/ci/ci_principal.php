<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once("admin/db/toba_dbt.php");

class ci_editor extends objeto_ci
{
	protected $db_tablas;
	protected $seleccion_pantalla;
	protected $seleccion_pantalla_anterior;

	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_dbt()->elemento('pantallas')->info(true),"PANTALLAS");
		//ei_arbol($this->get_estado_sesion(),"Estado sesion");
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "db_tablas";
		$propiedades[] = "seleccion_pantalla";
		$propiedades[] = "seleccion_pantalla_anterior";
		return $propiedades;
	}

	function get_dbt()
	//Acceso al db_tablas
	{
		if (! isset($this->db_tablas)) {
			$this->db_tablas = toba_dbt::objeto_ci();
		}
		return $this->db_tablas;
	}

	// *******************************************************************
	// *******************  PROPIEDADES BASICAS  *************************
	// *******************************************************************

	function evt__base__carga()
	{
		return $this->get_dbt()->elemento("base")->get();
	}

	function evt__base__modificacion($datos)
	{
		$this->get_dbt()->elemento("base")->set($datos);
	}

	function evt__prop_basicas__carga()
	{
		return $this->get_dbt()->elemento("prop_basicas")->get();
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_dbt()->elemento("prop_basicas")->set($datos);
	}

	// *******************************************************************
	// *******************  PANTALLAS  ***********************************
	// *******************************************************************
	
	function get_lista_ei__2()
	{
		$ei[] = "pantallas_lista";
		if( isset($this->seleccion_pantalla) ){
			$ei[] = "pantallas";
			$ei[] = "pantallas_ei";
		}
		return $ei;	
	}
	
	//-- Lista
	
	function evt__pantallas_lista__modificacion($datos)
	{
		//Establesco la 'orden' de la fila segun el orden de aparicion
		$a=1;
		foreach(array_keys($datos) as $id){
			$datos[$id]['orden'] = $a;
			$a++;
		}
		//ei_arbol($datos,"DATOS a guardar");
		$this->get_dbt()->elemento('pantallas')->procesar_registros($datos);		
	}
	
	function evt__pantallas_lista__carga()
	{
		if($datos_dbr = $this->get_dbt()->elemento('pantallas')->get_registros() )
		{
			//Ordeno los registros segun la 'posicion'
			//ei_arbol($datos_dbr,"Datos para el ML: PRE proceso");
			for($a=0;$a<count($datos_dbr);$a++){
				$orden[] = $datos_dbr[$a]['orden'];
			}
			array_multisort($orden, SORT_ASC , $datos_dbr);
			//EL formulario_ml necesita necesita que el ID sea la clave del array
			//No se solicita asi del DBR porque array_multisort no conserva claves numericas
			// y las claves internas del DBR lo son
			for($a=0;$a<count($datos_dbr);$a++){
				$id_dbr = $datos_dbr[$a][apex_db_registros_clave];
				unset( $datos_dbr[$a][apex_db_registros_clave] );
				$datos[ $id_dbr ] = $datos_dbr[$a];
			}
			//ei_arbol($datos,"Datos para el ML: POST proceso");
			return $datos;
		}
	}

	function evt__pantallas_lista__seleccion($id)
	{
		$this->seleccion_pantalla = $id;
	}

	//-- Info pantalla

	function evt__pantallas__modificacion($datos)
	{
		ei_arbol($datos, "hola" . $this->seleccion_pantalla_anterior);
		$this->get_dbt()->elemento('pantallas')->modificar_registro($datos, $this->seleccion_pantalla_anterior);
	}
	
	function evt__pantallas__carga()
	{
		$this->seleccion_pantalla_anterior = $this->seleccion_pantalla;
		return $this->get_dbt()->elemento('pantallas')->get_registro($this->seleccion_pantalla_anterior);
	}

	function combo_dependencias()
	{
		return null;		
	}

	// *******************************************************************
	// *******************  EVENTOS  ************************************
	// *******************************************************************
	/*
		Metodos necesarios para que el CI de eventos funcione
	*/

	function get_dbr_dependencias()
	{
		return $this->get_dbt()->elemento('dependencias');
	}
	
	// *******************************************************************
	// *******************  DEPENDENCIAS  ********************************
	// *******************************************************************
	/*
		Metodos necesarios para que el CI de eventos funcione
	*/

	function get_eventos_estandar()
	{
		$evento[0]['identificador'] = "seleccion";
		$evento[0]['etiqueta'] = "";
		$evento[0]['imagen_recurso_origen'] = "apex";
		$evento[0]['imagen'] = "doc.gif";	
		return $evento;
	}

	function evt__salida__3()
	{
		$this->dependencias['eventos']->limpiar_seleccion();
	}

	function get_dbr_eventos()
	{
		return $this->get_dbt()->elemento('eventos');
	}
	
	// *******************************************************************
	// *******************  PROCESAMIENTO  *******************************
	// *******************************************************************
	
	function evt__procesar()
	{
	}
	// *******************************************************************
}
?>