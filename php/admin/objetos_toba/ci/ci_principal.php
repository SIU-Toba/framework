<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once("admin/db/toba_dbt.php");

class ci_editor extends objeto_ci
{
	protected $db_tablas;
	protected $seleccion_pantalla;
	protected $seleccion_pantalla_anterior;
	protected $dependencias_asoc = array();
	private $id_intermedio_pantalla;	

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
		$propiedades[] = "dependencias_asoc";
		return $propiedades;
	}

	function get_dbt()
	//Acceso al db_tablas
	{
		if (! isset($this->db_tablas)) {
			$this->db_tablas = toba_dbt::objeto_ci();
			$this->db_tablas->cargar( array('proyecto'=>'toba', 'objeto'=>'1415') );
		}
		return $this->db_tablas;
	}

	// *******************************************************************
	// ******************* tab PROPIEDADES BASICAS  **********************
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
	// *******************  tab DEPENDENCIAS  ****************************
	// *******************************************************************
	/*
		Metodos necesarios para que el CI de eventos funcione
	*/
	function evt__salida__1()
	{
		// Armo la lista de dependencias disponibles para asociar a las pantallas
		$this->dependencias_asoc = array();
		if($registros = $this->get_dbt()->elemento('dependencias')->get_registros())
		{
			foreach($registros as $reg){
				$this->dependencias_asoc[ $reg['identificador'] ] = $reg['identificador'];
			}
		}
		// Si hay una seleccion, la anulo
		$this->dependencias['dependencias']->limpiar_seleccion();
	}

	function get_dbr_dependencias()
	{
		return $this->get_dbt()->elemento('dependencias');
	}
	
	// *******************************************************************
	// ******************* tab PANTALLAS  ********************************
	// *******************************************************************
	
	function get_lista_ei__2()
	{
		$ei[] = "pantallas_lista";
		if( isset($this->seleccion_pantalla) ){
			$ei[] = "pantallas";
			if(count($this->dependencias_asoc)>0){
				$ei[] = "pantallas_ei";			
			}
		}
		return $ei;	
	}

	function evt__post_cargar_datos_dependencias__2()
	{
		if( isset($this->seleccion_pantalla) ){
			//Agrego el evento "modificacion" y lo establezco como predeterminado
			$this->dependencias["pantallas"]->agregar_evento( eventos::modificacion(null, false), true );
		}
	}

	function evt__salida__2()
	{
		unset($this->seleccion_pantalla_anterior);
		unset($this->seleccion_pantalla);
	}

	//----------------------------------------------------------
	//-- Lista -------------------------------------------------
	//----------------------------------------------------------
	
	function evt__pantallas_lista__modificacion($registros)
	{
		/*
			Como en el mismo request es posible dar una columna de alta y seleccionarla,
			tengo que guardar el ID intermedio que el ML asigna en las columnas NUEVAS,
			porque ese es el que se pasa como parametro en la seleccion
		*/
		$dbr = $this->get_dbt()->elemento("pantallas");
		$orden = 1;
		foreach(array_keys($registros) as $id)
		{
			//Creo el campo orden basado en el orden real de las filas
			$registros[$id]['orden'] = $orden;
			$orden++;
			$accion = $registros[$id][apex_ei_analisis_fila];
			unset($registros[$id][apex_ei_analisis_fila]);
			switch($accion){
				case "A":
					$this->id_intermedio_pantalla[$id] = $dbr->agregar_registro($registros[$id]);
					break;	
				case "B":
					$dbr->eliminar_registro($id);
					break;	
				case "M":
					$dbr->modificar_registro($registros[$id], $id);
					break;	
			}
		}		
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
			// No se solicita asi del DBR porque array_multisort no conserva claves numericas
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
		if(isset($this->id_intermedio_pantalla[$id])){
			$id = $this->id_intermedio_pantalla[$id];
		}
		$this->seleccion_pantalla = $id;
	}

	//------------------------------------------------------
	//-- Informacion extendida de la pantalla  -------------
	//------------------------------------------------------

	function evt__pantallas__modificacion($datos)
	{
		$this->get_dbt()->elemento('pantallas')->modificar_registro($datos, $this->seleccion_pantalla_anterior);
	}
	
	function evt__pantallas__carga()
	{
		$this->seleccion_pantalla_anterior = $this->seleccion_pantalla;
		return $this->get_dbt()->elemento('pantallas')->get_registro($this->seleccion_pantalla_anterior);
	}

	function evt__pantallas__cancelar()
	{
		unset($this->seleccion_pantalla_anterior);
		unset($this->seleccion_pantalla);
	}

	//------------------------------------------------------
	//--- Asociacion de dependencias a pantallas  ----------
	//------------------------------------------------------

	function evt__pantallas_ei__modificacion($datos)
	{
		$deps = array();
		foreach($datos as $dato){
			$deps[] = $dato['dependencia'];
		}
		$this->get_dbt()->elemento('pantallas')->set_dependencias_pantalla($this->seleccion_pantalla_anterior, $deps);
	}
	
	function evt__pantallas_ei__carga()
	{
		/*
			Falta validar que todas las dependencias recuperadas aun existan
			sino, hay que eliminarlas
		*/
		if( $datos = $this->get_dbt()->elemento('pantallas')->get_dependencias_pantalla($this->seleccion_pantalla_anterior) )
		{
			$a=0;
			foreach($datos as $datos){
				$deps[$a]['dependencia'] = $datos;
				$a++;	
			}
			return $deps;
		}
	}

	function combo_dependencias()
	{
		$a=0;
		foreach( $this->dependencias_asoc as $dep => $info){
			$datos[$a]['id'] = $dep; 
			$datos[$a]['desc'] = $info; 
			$a++;
		}
		return $datos;
	}

	// *******************************************************************
	// *******************  tab EVENTOS  *********************************
	// *******************************************************************
	/*
		Metodos necesarios para que el CI de eventos funcione
	*/

	function evt__salida__3()
	{
		$this->dependencias['eventos']->limpiar_seleccion();
	}

	function get_dbr_eventos()
	{
		return $this->get_dbt()->elemento('eventos');
	}

	function get_eventos_estandar()
	{
		require_once('api/elemento_objeto_ci.php');
		return elemento_objeto_ci::get_lista_eventos_estandar();
	}

	// *******************************************************************
	// *******************  PROCESAMIENTO  *******************************
	// *******************************************************************
	
	function evt__procesar()
	{
		$this->get_dbt()->sincronizar();		
	}
	// *******************************************************************
}
?>