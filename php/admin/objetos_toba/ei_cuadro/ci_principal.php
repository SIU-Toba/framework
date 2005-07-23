<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once("admin/db/toba_dbt.php");

class ci_principal extends objeto_ci
{
	protected $db_tablas;
	//Columnas
	protected $seleccion_columna;
	protected $seleccion_columna_anterior;
	private $id_intermedio_columna;

	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_dbt()->elemento('columnas')->info(true),"COLUMNAS");
		//ei_arbol($this->get_estado_sesion(),"Estado sesion");
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "db_tablas";
		$propiedades[] = "seleccion_columna";
		$propiedades[] = "seleccion_columna_anterior";
		$propiedades[] = "seleccion_evento";
		$propiedades[] = "seleccion_evento_anterior";
		return $propiedades;
	}

	function get_dbt()
	//Acceso al db_tablas
	{
		if (! isset($this->db_tablas)) {
			$this->db_tablas = toba_dbt::objeto_ei_cuadro();
			//$this->db_tablas->cargar( array('proyecto'=>'toba', 'objeto'=>'1387') );
		}
		return $this->db_tablas;
	}

	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

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

	//*******************************************************************
	//*******************  COLUMNAS  *************************************
	//*******************************************************************
	
	function mostrar_columna_detalle()
	{
		if( isset($this->seleccion_columna) ){
			return true;	
		}
		return false;
	}

	function get_lista_ei__2()
	{
		$ei[] = "columnas_lista";
		if( $this->mostrar_columna_detalle() ){
			$ei[] = "columnas";
		}
		return $ei;	
	}
	
	function evt__salida__2()
	{
		unset($this->seleccion_columna);
		unset($this->seleccion_columna_anterior);
	}

	function evt__post_cargar_datos_dependencias__2()
	{
		if( $this->mostrar_columna_detalle() ){
			//Protejo la columna seleccionada de la eliminacion
			$this->dependencias["columnas_lista"]->set_fila_protegida($this->seleccion_columna_anterior);
			//Agrego el evento "modificacion" y lo establezco como predeterminado
			$this->dependencias["columnas"]->agregar_evento( eventos::modificacion(null, false), true );
		}
	}

	//-------------------------------
	//---- EI: Lista de columnas ----
	//-------------------------------
	
	function evt__columnas_lista__modificacion($registros)
	{
		/*
			Como en el mismo request es posible dar una columna de alta y seleccionarla,
			tengo que guardar el ID intermedio que el ML asigna en las columnas NUEVAS,
			porque ese es el que se pasa como parametro en la seleccion
		*/
		$dbr = $this->get_dbt()->elemento("columnas");
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
					$this->id_intermedio_columna[$id] = $dbr->agregar_registro($registros[$id]);
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
	
	function evt__columnas_lista__carga()
	{
		if($datos_dbr = $this->get_dbt()->elemento('columnas')->get_registros() )
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

	function evt__columnas_lista__seleccion($id)
	{
		if(isset($this->id_intermedio_columna[$id])){
			$id = $this->id_intermedio_columna[$id];
		}
		$this->seleccion_columna = $id;
	}

	//-----------------------------------------
	//---- EI: Info detalla de una COLUMNA ----
	//-----------------------------------------

	function evt__columnas__modificacion($datos)
	{
		$this->get_dbt()->elemento('columnas')->modificar_registro($datos, $this->seleccion_columna_anterior);
	}
	
	function evt__columnas__carga()
	{
		$this->seleccion_columna_anterior = $this->seleccion_columna;
		return $this->get_dbt()->elemento('columnas')->get_registro($this->seleccion_columna_anterior);
	}

	function evt__columnas__cancelar()
	{
		unset($this->seleccion_columna);
		unset($this->seleccion_columna_anterior);
	}

	//*******************************************************************
	//*******************  EVENTOS  ************************************
	//*******************************************************************
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

	//*******************************************************************
	//*******************  PROCESAMIENTO  *******************************
	//*******************************************************************

	function evt__procesar()
	{
		/*
			CONTROLES:

				Hay que controlar que la clave este incluida entre las columnas,
				en el caso en que no se este utilizando un db_registros.
		*/
		$this->get_dbt()->sincronizar();
	}
	//-------------------------------------------------------------------
}
?>