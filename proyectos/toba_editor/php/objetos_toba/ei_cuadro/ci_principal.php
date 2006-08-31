<?php
require_once('objetos_toba/ci_editores_toba.php');
require_once('modelo/componentes/info_ei_cuadro.php');

class ci_principal extends ci_editores_toba
{
	//Columnas
	protected $s__seleccion_columna;
	protected $s__seleccion_columna_anterior;
	private $id_intermedio_columna;
	protected $s__cortes_control;
	protected $s__importacion_cols;
	protected $clase_actual = 'objeto_ei_cuadro';
	
	function ini()
	{
		parent::ini();
		$col = toba::hilo()->obtener_parametro('columna');
		//¿Se selecciono un ef desde afuera?
		if (isset($col)) {
			$this->set_pantalla(2);
			echo "ACA";
			$id_interno = $this->get_entidad()->tabla("columnas")->get_id_fila_condicion(array('clave'=>$col));
			if (count($id_interno) == 1) {
				$this->evt__columnas_lista__seleccion($id_interno[0]);			
			} else {
				throw new toba_excepcion("No se encontro la columna $col.");
			}
		}
	}
	
	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function conf__prop_basicas()
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

	function mostrar_columna_detalle()
	{
		if( isset($this->s__seleccion_columna) ){
			return true;	
		}
		return false;
	}
	
	function evt__2__entrada()
	{
		//--- Armo la lista de DEPENDENCIAS disponibles
		$this->s__cortes_control = array();
		if($registros = $this->get_entidad()->tabla('cortes')->get_filas())
		{
			foreach($registros as $reg){
				$this->s__cortes_control[ $reg['identificador'] ] = $reg['identificador'];
			}
		}
	}

	function conf__2($pantalla)
	{
		if( $this->mostrar_columna_detalle() ){
			$pantalla->eliminar_dep('columnas_importar');
			$existen_cortes = count($this->s__cortes_control) > 0;
			if( ! $existen_cortes ){
				$pantalla->eliminar_dep('columna_corte');
			}
			$this->dependencia('columnas_lista')->set_fila_protegida($this->s__seleccion_columna);
			$this->dependencia("columnas_lista")->seleccionar($this->s__seleccion_columna);
		}else{
			$pantalla->eliminar_dep('columnas');
			$pantalla->eliminar_dep('columna_corte');
			$this->dependencia('columnas_importar')->colapsar();
		}
	}
	
	function evt__2__salida()
	{
		unset($this->s__seleccion_columna);
		unset($this->s__seleccion_columna_anterior);
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
	
	function conf__columnas_lista()
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
		$this->s__seleccion_columna = $id;
	}

	//-----------------------------------------
	//---- EI: Info detalla de una COLUMNA ----
	//-----------------------------------------

	function evt__columnas__modificacion($datos)
	{
		$this->get_entidad()->tabla('columnas')->modificar_fila($this->s__seleccion_columna_anterior, $datos);
	}
	
	function evt__columnas__aceptar($datos)
	{
		$this->get_entidad()->tabla('columnas')->modificar_fila($this->s__seleccion_columna_anterior, $datos);
		$this->evt__columnas__cancelar();
	}

	function conf__columnas()
	{
		$this->s__seleccion_columna_anterior = $this->s__seleccion_columna;
		return $this->get_entidad()->tabla('columnas')->get_fila($this->s__seleccion_columna_anterior);
	}

	function evt__columnas__cancelar()
	{
		unset($this->s__seleccion_columna);
	}

	//-----------------------------------------
	//---- EI: Participacion en los CORTES de CONTROL del cuadro
	//-----------------------------------------

	function conf__columna_corte()
	{
		$cortes_asociados = $this->get_entidad()->tabla('columnas')->get_cortes_columna($this->s__seleccion_columna_anterior);
		$datos = null;
		$a=0;
		foreach( $this->s__cortes_control as $corte){
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
		$this->get_entidad()->tabla('columnas')->set_cortes_columna($this->s__seleccion_columna_anterior, $cortes);
	}

	//---------------------------------
	//---- EI: IMPORTAR definicion ----
	//---------------------------------

	function evt__columnas_importar__importar($datos)
	{
		$this->s__importacion_cols = $datos;
		if(isset($datos['datos_tabla'])){
			$clave = array( 'proyecto' => toba_editor::get_proyecto_cargado(),
							'componente' => $datos['datos_tabla'] );
			$dt = constructor_toba::get_info( $clave, 'datos_tabla' );
			$datos = $dt->exportar_datos_columnas($datos['pk']);
			//ei_arbol($datos);
			$cols = $this->get_entidad()->tabla("columnas");
			foreach($datos as $col){
				try{
					$cols->nueva_fila($col);
				}catch(toba_excepcion $e){
					toba::notificacion()->agregar("Error agregando la columna '{$col['clave']}'. " . $e->getMessage());
				}
			}
		}
	}

	function conf__columnas_importar()
	{
		if(isset($this->s__importacion_cols)){
			return $this->s__importacion_cols;
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

	function evt__3__salida()
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
	
	function conf__prop_cortes()
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
	
	function conf__cortes()
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