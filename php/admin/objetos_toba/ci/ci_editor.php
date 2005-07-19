<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once("admin/objetos_toba/autoload.php");

class ci_editor extends objeto_ci
{
	protected $db_tablas;
	protected $seleccion_pantalla;
	protected $seleccion_pantalla_anterior;

/*
	function __construct($id)
	{
		parent::__construct($id);	
		$this->db_tablas = $this->get_dbt();
	}
*/

	function destruir()
	{
		parent::destruir();
		ei_arbol($this->get_dbt()->elemento('pantallas')->info(true),"PANTALLAS");
		ei_arbol($this->get_estado_sesion(),"Estado sesion");
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
			$this->db_tablas = new dbt_ci($this->info['fuente']);
		}
		return $this->db_tablas;
	}

	//-------------------------------------------------------------------
	//--- Comportamiento de las pantallas
	//-------------------------------------------------------------------

	//*****************  PROPIEDADES BASICAS  ***************************

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

	//******************  DEPENDENCIAS  ***********************************

	function evt__dependencias__modificacion($datos)
	{
		$this->get_dbt()->elemento('dependencias')->procesar_registros($datos);
	}

	function evt__dependencias__carga()
	{
		return  $this->get_dbt()->elemento('dependencias')->get_registros(null,true);	
	}

	//*******************  PANTALLAS  *************************************
	
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
		//Establesco la 'posicion' de la fila segun el orden de aparicion
		$a=1;
		foreach(array_keys($datos) as $id){
			$datos[$id]['posicion'] = $a;
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
				$orden[] = $datos_dbr[$a]['posicion'];
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

	//Atencion, las dependencias tambien necesitan ORDEN!
	
	
	
	
	function evt__procesar()
	{
	}
	
	
	
	
/*


	function evt__cancelar()
	{
	}

	function evt__inicializar()
	{
	}

	function evt__limpieza_memoria()
	{
	}

	function evt__post_recuperar_interaccion()
	{
	}

	function evt__validar_datos()
	{
	}

	function evt__error_proceso_hijo()
	{
	}

	function evt__pre_cargar_datos_dependencias()
	{
	}

	function evt__post_cargar_datos_dependencias()
	{
	}

	//----------------------------- base -----------------------------

	function evt__dependencias__carga()
	{
		//if isset($this->datos_dependencias)
		//	return $this->datos_dependencias;
	}

	//----------------------------- pantallas -----------------------------
	function evt__pantallas__carga()
	{
		//if isset($this->datos_pantallas)
		//	return $this->datos_pantallas;
	}

	function evt__pantallas__baja()
	{
	}

	function evt__pantallas__cancelar()
	{
	}

	//----------------------------- pantallas_ei -----------------------------
	function evt__pantallas_ei__carga()
	{
		//if isset($this->datos_pantallas_ei)
		//	return $this->datos_pantallas_ei;
	}

	function evt__pantallas_ei__modificacion($registros)
	{
		//$this->datos_pantallas_ei = $registros;	
	}

	//----------------------------- pantallas_lista -----------------------------
	function evt__pantallas_lista__carga()
	{
		//if isset($this->datos_pantallas_lista)
		//	return $this->datos_pantallas_lista;
	}

	function evt__pantallas_lista__modificacion($registros)
	{
		//$this->datos_pantallas_lista = $registros;	
	}

	//----------------------------- prop_basicas -----------------------------
*/
}
?>