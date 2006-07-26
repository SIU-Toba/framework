<?php
require_once('nucleo/componentes/interface/objeto_ci.php'); 
require_once("nucleo/componentes/interface/efs/ef.php");
/*
	ATENCION: 
		El controlador tiene que implementar "get_dbr_efs()" 
		para que este CI puede obtener el DBR que utiliza para trabajar
*/
class ci_efs extends objeto_ci
{
	protected $tabla;
	protected $seleccion_efs;
	protected $seleccion_efs_anterior;
	protected $importacion_efs;
	private $id_intermedio_efs;
	protected $mecanismos_carga = array('carga_metodo', 'carga_sql', 'carga_lista');
	protected $modificado = false;

	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_tabla()->elemento('efss')->info(true),"efsS");
		//ei_arbol($this->get_estado_sesion(),"Estado sesion");
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "seleccion_efs";
		$propiedades[] = "seleccion_efs_anterior";
		$propiedades[] = "importacion_efs";
		return $propiedades;
	}

	function get_tabla()
	//Acceso al db_tablas
	{
		if (! isset($this->tabla)) {
			$this->tabla = $this->controlador->get_dbr_efs();
		}
		return $this->tabla;
	}

	function mostrar_efs_detalle()
	{
		if( isset($this->seleccion_efs) ){
			return true;	
		}
		return false;
	}

	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		if(! $this->mostrar_efs_detalle() ){
			unset($eventos['cancelar']);
			unset($eventos['aceptar']);
		}		
		return $eventos;
	}

	function evt__cancelar()
	{
		$this->limpiar_seleccion();	
	}

	function evt__aceptar()
	{
		$this->limpiar_seleccion();	
	}

	function get_lista_ei()
	{
		$ei[] = "efs_lista";
		if( $this->mostrar_efs_detalle() ){
			$ei[] = "efs";
			$param_carga = $this->get_definicion_parametros(true);			
			$param_varios = $this->get_definicion_parametros(false);
			if (! empty($param_varios)) {			
				$ei[] = 'param_varios';
			}
			if (! empty($param_carga)) {
				$ei[] = 'param_carga';
			}
		}else{
			$ei[] = "efs_importar";
			if ($this->hay_cascadas()) {
				$ei[] = "esquema_cascadas";
			}			
		}
		return $ei;	
	}
	
	function evt__post_cargar_datos_dependencias()
	{
		if( $this->mostrar_efs_detalle() ){
			//Protejo la efs seleccionada de la eliminacion
			$this->dependencia("efs_lista")->set_fila_protegida($this->seleccion_efs_anterior);
		}else{
			$this->dependencia("efs_importar")->colapsar();
		}
		if (isset($this->seleccion_efs)) {
			$this->dependencia("efs_lista")->seleccionar($this->seleccion_efs);
		}
	}
	
	function limpiar_seleccion()
	{
		unset($this->seleccion_efs);
		unset($this->seleccion_efs_anterior);
	}

	//-------------------------------
	//---- EI: Lista de efss ----
	//-------------------------------
	
	function evt__efs_lista__modificacion($registros)
	{
		/*
			Como en el mismo request es posible dar una efs de alta y seleccionarla,
			tengo que guardar el ID intermedio que el ML asigna en las efss NUEVAS,
			porque ese es el que se pasa como parametro en la seleccion
		*/
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
					//Por defecto el campo 'columnas' es igual a 'identificador'
					$registros[$id]['columnas'] = $registros[$id]['identificador'];
					$this->id_intermedio_efs[$id] = $this->get_tabla()->nueva_fila($registros[$id]);
					break;	
				case "B":
					$this->get_tabla()->eliminar_fila($id);
					break;	
				case "M":
					$this->get_tabla()->modificar_fila($id, $registros[$id]);
					break;	
			}
		}
	}
	
	function evt__efs_lista__carga()
	{
		if($datos_dbr = $this->get_tabla()->get_filas() )
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

	function evt__efs_lista__seleccion($id)
	{
		if(isset($this->id_intermedio_efs[$id])){
			$id = $this->id_intermedio_efs[$id];
		}
		$this->seleccion_efs = $id;
	}

	/**
	*	Selecciona un ef por su identificador real
	*/	
	function seleccionar_ef($id)
	{
		$id_interno = $this->get_tabla()->get_id_fila_condicion(array('identificador'=>$id));
		if (count($id_interno) == 1) {
			$this->evt__efs_lista__seleccion(current($id_interno));
		} else {
			throw new excepcion_toba("No se encontro el ef $id.");
		}
	}

	//-----------------------------------------
	//---- EI: Info detalla de un EF ----------
	//-----------------------------------------

	function evt__efs__modificacion($datos)
	{
		$this->get_tabla()->modificar_fila($this->seleccion_efs_anterior, $datos);
	}
	
	function evt__efs__carga()
	{
		$this->seleccion_efs_anterior = $this->seleccion_efs;
		return $this->get_tabla()->get_fila($this->seleccion_efs_anterior);
	}

	function evt__efs__cancelar()
	{
		unset($this->seleccion_efs);
		unset($this->seleccion_efs_anterior);
	}

	//-----------------------------------------
	//---- EI: Inicializacion del EF ----------
	//-----------------------------------------
	
	function get_mecanismos_carga()
	{
		$param = $this->get_definicion_parametros(true);		
		$tipos = array();
		if (in_array('carga_metodo', $param)) {
			$tipos[] = array('carga_metodo', 'M�todo PHP');
		}
		if (in_array('carga_sql', $param)) {		
			$tipos[] = array('carga_sql', 'Consulta SQL');
		}
		if (in_array('carga_lista', $param)) {
			$tipos[] = array('carga_lista', 'Lista de Opciones');
		}
		return $tipos;
	}
	
	function get_posibles_maestros()
	{
		$filas = $this->get_tabla()->get_filas(null, true);
		$posibles = array();
		foreach ($filas as $clave => $datos) {
			if ($clave != $this->seleccion_efs) {
				$posibles[] = array($datos['identificador'], $datos['identificador']);
			}
		}
		return $posibles;
	}
	
	function get_definicion_parametros($carga = false)
	{
		$ef = $this->get_tipo_ef();
		$metodo = ($carga) ? "get_lista_parametros_carga" : "get_lista_parametros";
		$parametros = call_user_func(array($ef, $metodo));
		return $parametros;
	}
	
	function get_tipo_ef()
	{
		return $this->get_tabla()->get_fila_columna( $this->seleccion_efs, "elemento_formulario");
	}
	
	function set_parametros($parametros)
	{
		$this->get_tabla()->modificar_fila($this->seleccion_efs_anterior, $parametros);
	}

	//---------------------------------
	//---- PARAMETROS VARIOS
	//---------------------------------
	
	function evt__param_varios__carga()
	{
		$fila = $this->get_tabla()->get_fila($this->seleccion_efs_anterior);
				
		//--- Se desactivan los efs que no forman parte de la definicion
		$param = $this->get_definicion_parametros();
		$todos = $this->dependencia('param_varios')->get_nombres_ef();
		$efs_a_desactivar = array();
		foreach ($todos as $disponible) {
			if (! in_array($disponible, $param) ) {
				$efs_a_desactivar[] = $disponible;
				if (isset($this->parametros[$disponible])) {
					unset($this->parametros[$disponible]);	
				}
			}
		}
		
		//-- Si es un popup no eliminar la carpeta (es cosmetico)
		if (! in_array('popup_item', $efs_a_desactivar)) {
			array_borrar_valor($efs_a_desactivar, 'popup_carpeta');	
			//-- Si esta seteado el item, buscar la carpeta asociada
			if (isset($fila['popup_item']) && isset($fila['popup_proyecto'])) {
				$fila['popup_carpeta'] = dao_editores::get_carpeta_de_item(	$fila['popup_item'], 
																			$fila['popup_proyecto']);
			}
		}
		$this->dependencia('param_varios')->desactivar_efs($efs_a_desactivar);
		return $fila;
	}
	
	function evt__param_varios__modificacion($datos)
	{
		$this->modificado = true;
		$this->set_parametros($datos);
	}
	
	//---------------------------------
	//---- PARAMETROS de CARGA
	//---------------------------------

	function evt__param_carga__carga()
	{
		$lista_param = $this->get_definicion_parametros(true);
		$fila = $this->get_tabla()->get_fila($this->seleccion_efs_anterior);
		
		//---Desactiva los efs que no pertenecen a los parametros
		$todos = $this->dependencia('param_carga')->get_nombres_ef();
		foreach ($todos as $disponible) {
			if (! in_array($disponible, $lista_param) &&
					$disponible != 'mecanismo' &&
					$disponible != 'estatico') {
				if (isset($fila[$disponible])) {
					unset($fila[$disponible]);	
				}						
				$this->dependencia('param_carga')->desactivar_efs($disponible);
			}
		}
		
		//---Determina el mecanismo
		foreach ($this->mecanismos_carga as $mec) {
			if (isset($fila[$mec])) {
				$fila['mecanismo'] = $mec;
				break;
			}
		}
		//--- Si el mecanismo es un metodo php y es estatico chequear el checkbox
		if (isset($fila['mecanismo']) && $fila['mecanismo'] == 'carga_metodo') {
			$tiene_clase = (isset($fila['carga_clase']) && $fila['carga_clase'] != '');
			$tiene_include = (isset($fila['carga_include']) && $fila['carga_include'] != '');
			if ($tiene_clase || $tiene_include) {
				$fila['estatico'] = 1;
			}
		}
		return $fila;
	}
	
	function evt__param_carga__modificacion($datos)
	{
		$this->modificado = true;		
		$actual = $datos['mecanismo'];
		foreach ($this->mecanismos_carga as $valor_mec) {
			if ($valor_mec != $actual && isset($datos[$valor_mec])) {
				unset($datos[$valor_mec]);
			}
		}
		if ($datos['mecanismo'] != null) {
			unset($datos['mecanismo']);
			unset($datos['estatico']);
		} else {
			//--- Limpia los valores
			$datos = array();	
			foreach ($this->mecanismos_carga as $mec) {
				$datos[$mec] = null;
			}
		}
		$this->set_parametros($datos);
	}
			
		
	//---------------------------------
	//---- EI: IMPORTAR definicion ----
	//---------------------------------

	function evt__efs_importar__importar($datos)
	{
		$this->importacion_efs = $datos;
		if(isset($datos['datos_tabla'])){
			$clave = array( 'proyecto' => editor::get_proyecto_cargado(),
							'componente' => $datos['datos_tabla'] );
			$dt = constructor_toba::get_info( $clave, 'datos_tabla' );
			$datos = $dt->exportar_datos_efs($datos['pk']);
			foreach($datos as $ef){
				try{
					$this->get_tabla()->nueva_fila($ef);
				}catch(excepcion_toba $e){
					toba::get_cola_mensajes()->agregar("Error agregando el EF '{$ef['identificador']}'. " . $e->getMessage());
				}
			}
		}
	}

	function evt__efs_importar__carga()
	{
		if(isset($this->importacion_efs)){
			return $this->importacion_efs;
		}
	}

	
	//---------------------------------
	//---- EI: Cascadas		 ----
	//---------------------------------	
	function evt__esquema_cascadas__carga()
	{
		$diagrama = "digraph G {\nsize=\"7,7\";rankdir=LR;\n";		
		$diagrama .= "node [shape=record];\n";
		foreach ($this->get_tabla()->get_filas() as $ef) {
			$maestros = trim($ef['carga_maestros']);
			if ($maestros != '') {
				foreach (explode(',', $maestros) as $dep) {
					$diagrama .= $dep.'->'.$ef['identificador'].";\n";
				}
			}
		}
		$diagrama .= " }";
		return $diagrama;
	}
	
	function hay_cascadas()
	{
		foreach ($this->get_tabla()->get_filas() as $ef) {
			if (isset($ef['carga_maestros']) && trim($ef['carga_maestros']) != '') {
				return true;
			}
		}
		return false;
	}	
	
	
}
?>