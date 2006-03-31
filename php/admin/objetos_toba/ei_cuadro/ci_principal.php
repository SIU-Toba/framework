<?php
require_once('admin/objetos_toba/ci_editores_toba.php');
require_once('nucleo/componentes/info/info_ei_cuadro.php');

class ci_principal extends ci_editores_toba
{
	//Columnas
	protected $seleccion_columna;
	protected $seleccion_columna_anterior;
	private $id_intermedio_columna;
	protected $columna_especifica;
	protected $cortes_control;
	protected $importacion_cols;
	protected $clase_actual = 'objeto_ei_cuadro';
	
	function __construct($id)
	{
		parent::__construct($id);
		$col = toba::get_hilo()->obtener_parametro('columna');
		//¿Se selecciono un ef desde afuera?
		if (isset($col)) {
			$this->columna_especifica = $col;
			$id_interno = $this->get_entidad()->tabla("columnas")->get_id_fila_condicion(array('clave'=>$col));
			if (count($id_interno) == 1) {
				$this->evt__columnas_lista__seleccion($id_interno[0]);			
			} else {
				throw new excepcion_toba("No se encontro la columna $col.");
			}

		}
	}	
	
	function get_etapa_actual()
	{
		if (isset($this->columna_especifica)) {
			return 2;	//Si se selecciono una columna desde afuera va a la pantalla de edición de las columnas
		} 
		return parent::get_etapa_actual();
	}	
	
	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_entidad()->tabla('columnas')->info(true),"COLUMNAS");
		//ei_arbol($this->get_estado_sesion(),"Estado sesion");
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "seleccion_columna";
		$propiedades[] = "seleccion_columna_anterior";
		$propiedades[] = "cortes_control";
		$propiedades[] = "importacion_cols";
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
	//*******************  COLUMNAS  *************************************
	//*******************************************************************
	
	function evt__entrada__2()
	{
		//--- Armo la lista de DEPENDENCIAS disponibles
		$this->cortes_control = array();
		if($registros = $this->get_entidad()->tabla('cortes')->get_filas())
		{
			foreach($registros as $reg){
				$this->cortes_control[ $reg['identificador'] ] = $reg['identificador'];
			}
		}
	}
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
			if( count($this->cortes_control) > 0 ){
				$ei[] = "columna_corte";			
			}
		}else{
			$ei[] = "columnas_importar";
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
			$this->dependencia("columnas_lista")->set_fila_protegida($this->seleccion_columna_anterior);
		}else{
			$this->dependencia("columnas_importar")->colapsar();
		}
		if (isset($this->seleccion_columna)) {
			$this->dependencia("columnas_lista")->seleccionar($this->seleccion_columna);
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
		$dbr = $this->get_entidad()->tabla("columnas");
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
					$this->id_intermedio_columna[$id] = $dbr->nueva_fila($registros[$id]);
					break;	
				case "B":
					$dbr->eliminar_fila($id);
					break;	
				case "M":
					$dbr->modificar_fila($id, $registros[$id]);
					break;	
			}
		}
	}
	
	function evt__columnas_lista__carga()
	{
		if($datos_dbr = $this->get_entidad()->tabla('columnas')->get_filas() )
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
		$this->get_entidad()->tabla('columnas')->modificar_fila($this->seleccion_columna_anterior, $datos);
	}
	
	function evt__columnas__aceptar($datos)
	{
		$this->get_entidad()->tabla('columnas')->modificar_fila($this->seleccion_columna_anterior, $datos);
		$this->evt__columnas__cancelar();
	}

	function evt__columnas__carga()
	{
		$this->seleccion_columna_anterior = $this->seleccion_columna;
		return $this->get_entidad()->tabla('columnas')->get_fila($this->seleccion_columna_anterior);
	}

	function evt__columnas__cancelar()
	{
		unset($this->seleccion_columna);
		unset($this->seleccion_columna_anterior);
	}

	//-----------------------------------------
	//---- EI: Participacion en los CORTES de CONTROL del cuadro
	//-----------------------------------------

	function evt__columna_corte__carga()
	{
		$cortes_asociados = $this->get_entidad()->tabla('columnas')->get_cortes_columna($this->seleccion_columna_anterior);
		$datos = null;
		$a=0;
		foreach( $this->cortes_control as $corte){
			$datos[$a]['identificador'] = $corte; 
			if(is_array($cortes_asociados)){
				if(in_array($corte, $cortes_asociados)){
					$datos[$a]['total'] = 1;
				}else{
					$datos[$a]['total'] = 0;
				}
			}else{
				$datos[$a]['total'] = 0;
			}
			$a++;
		}
		return $datos;
	}

	function evt__columna_corte__modificacion($datos)
	{
		$cortes = array();
		foreach($datos as $dato){
			if($dato['total'] == "1")	$cortes[] = $dato['identificador'];
		}
		$this->get_entidad()->tabla('columnas')->set_cortes_columna($this->seleccion_columna_anterior, $cortes);
	}

	//---------------------------------
	//---- EI: IMPORTAR definicion ----
	//---------------------------------

	function evt__columnas_importar__importar($datos)
	{
		$this->importacion_cols = $datos;
		if(isset($datos['datos_tabla'])){
			$clave = array( 'proyecto' => toba::get_hilo()->obtener_proyecto(),
							'componente' => $datos['datos_tabla'] );
			$dt = constructor_toba::get_info( $clave, 'datos_tabla' );
			$datos = $dt->exportar_datos_columnas($datos['pk']);
			//ei_arbol($datos);
			$cols = $this->get_entidad()->tabla("columnas");
			foreach($datos as $col){
				try{
					$cols->nueva_fila($col);
				}catch(excepcion_toba $e){
					toba::get_cola_mensajes()->agregar("Error agregando la columna '{$col['clave']}'. " . $e->getMessage());
				}
			}
		}
	}

	function evt__columnas_importar__carga()
	{
		if(isset($this->importacion_cols)){
			return $this->importacion_cols;
		}
	}

	//*******************************************************************
	//*******************  EVENTOS  ************************************
	//*******************************************************************
	/*
		Metodos necesarios para que el CI de eventos funcione
	*/

	function get_modelos_evento()
	{
		return info_ei_cuadro::get_modelos_evento();
	}

	function get_eventos_estandar($modelo)
	{
		return info_ei_cuadro::get_lista_eventos_estandar($modelo);
	}

	function evt__salida__3()
	{
		$this->dependencia('eventos')->limpiar_seleccion();
	}

	function get_dbr_eventos()
	{
		return $this->get_entidad()->tabla('eventos');
	}

	//*******************************************************************
	//*****************  CORTES de CONTROL  *****************************
	//*******************************************************************
	
	function evt__prop_cortes__carga()
	{
		return $this->get_entidad()->tabla("prop_basicas")->get();
	}

	function evt__prop_cortes__modificacion($datos)
	{
		$this->get_entidad()->tabla("prop_basicas")->set($datos);
	}

	function evt__cortes__modificacion($datos)
	{
		$this->get_entidad()->tabla('cortes')->procesar_filas($datos);
	}
	
	function evt__cortes__carga()
	{
		if($datos_dbr = $this->get_entidad()->tabla('cortes')->get_filas() )
		{
			for($a=0;$a<count($datos_dbr);$a++){
				$orden[] = $datos_dbr[$a]['orden'];
			}
			array_multisort($orden, SORT_ASC , $datos_dbr);
			for($a=0;$a<count($datos_dbr);$a++){
				$id_dbr = $datos_dbr[$a][apex_db_registros_clave];
				unset( $datos_dbr[$a][apex_db_registros_clave] );
				$datos[ $id_dbr ] = $datos_dbr[$a];
			}
			return $datos;
		}
	}

}
?>