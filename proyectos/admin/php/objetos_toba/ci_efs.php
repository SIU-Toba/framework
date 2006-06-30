<?php
require_once('nucleo/componentes/interface/objeto_ci.php'); 
require_once("nucleo/componentes/interface/efs/ef.php");
/*
	ATENCION: 
		El controlador tiene que implementar "get_dbr_efs()" 
		para que este CI puede obtener el DBR que utiliza para trabajar
	
	NOTAS:
		Lo ideal para la definicion de EFs seria que el metodo estatico get_parametros
		devuleva como el ef instanciado que permite cargar los valores de si mismo,
		esto permitiria lograr una mejor validacion
*/
class ci_efs extends objeto_ci
{
	protected $tabla;
	protected $seleccion_efs;
	protected $seleccion_efs_anterior;
	protected $importacion_efs;
	private $id_intermedio_efs;

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
			$ei[] = "efs_ini";
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
		unset($datos['estado']);
		unset($datos['solo_lectura']);
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

	function evt__efs_ini__carga()
	{
		/*
			Se podria usar el conocimiento de los obligatorios para 
			cambiarle el estilo a la linea del ML
		*/
		$parametros = $this->get_definicion_parametros();
		$fila = $this->get_tabla()->get_fila($this->seleccion_efs_anterior);
		$temp = array();
		$a=0;		
		foreach($parametros as $clave => $desc) {
			$imagen_ayuda = recurso::imagen_apl("descripcion.gif",true, null, null, $desc['descripcion']);
			$temp[$a]['clave'] = $clave;
			$temp[$a]['etiqueta'] = $desc['etiqueta'];
			$temp[$a]['valor'] = isset($fila[$clave]) ? $fila[$clave] : null;
			$temp[$a]['ayuda'] = $imagen_ayuda;
			$a++;	
		}
		return $temp;
		
		/*
		//Inicializacion del EF actual
		$x = $this->get_tabla()->get_fila_columna($this->seleccion_efs_anterior,"inicializacion");
		if(isset($x)){
			$inicializacion = parsear_propiedades($x, '_');
		}
		//Armo la lista
		$temp = array();
		$a=0;
		foreach($parametros as $clave => $desc){
			$imagen_ayuda = recurso::imagen_apl("descripcion.gif",true, null, null, $desc['descripcion']);
			$temp[$a]['clave'] = $clave;
			$temp[$a]['etiqueta'] = $desc['etiqueta'];
			$temp[$a]['valor'] = isset($inicializacion[$clave]) ? $inicializacion[$clave] : null;
			$temp[$a]['ayuda'] = $imagen_ayuda;
			$a++;	
		}
		return $temp;*/
	}
	
	function evt__efs_ini__modificacion($datos)
	{
		$campos = array();
		foreach ($datos as $param) {
			$campos[$param['clave']] = $param['valor'];
		}
		$this->get_tabla()->modificar_fila($this->seleccion_efs_anterior, $campos);
		/*
			ATENCION: Falta la validacion de que los campos obligatorios esten seteados
		*/
/*		
		//Primero se borran los parametros anteriores
		$this->get_tabla()->set_fila_columna_valor($this->seleccion_efs_anterior,"inicializacion","");
		
		$temp = array();
		foreach($datos as $parametro){
			if(trim($parametro['valor'])!=""){
				$temp[$parametro['clave']] = $parametro['valor'];
			}
		}
		if(count($temp)>0){
			$resultado = empaquetar_propiedades($temp, '_');
			//Tengo que validar que los obligatorios existan
			$this->get_tabla()->set_fila_columna_valor($this->seleccion_efs_anterior,"inicializacion",$resultado);
		}*/
	}
	
	function get_definicion_parametros()
	//Recupero la informacion de los parametros de un EF puntual
	{
		$ef = $this->get_tabla()->get_fila_columna( $this->seleccion_efs_anterior , "elemento_formulario");
		$parametros = call_user_func(array($ef,"get_parametros"));
		return $parametros;
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
		$diagrama = "digraph G {\nsize=\"7,7\";\n";		
		
		foreach ($this->get_tabla()->get_filas() as $ef) {
			$param = parsear_propiedades($ef['inicializacion'], '_');
			if (isset($param['dependencias'])) {
				foreach (explode(',', $param['dependencias']) as $dep) {
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
			if (isset($ef['inicializacion'])) {
				$param = parsear_propiedades($ef['inicializacion'], '_');			
				if (isset($param['dependencias'])) {
					return true;	
				}
			}
		}
		return false;
	}	
	
	
}
?>