<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 

class ci_relaciones extends objeto_ci
{
	protected $tabla;
	protected $seleccion_relacion;
	protected $seleccion_relacion_anterior;
	private $id_intermedio_relaciones;

	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_tabla()->elemento('efss')->info(true),"efsS");
		//ei_arbol($this->get_estado_sesion(),"Estado sesion");
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "seleccion_relacion";
		$propiedades[] = "seleccion_relacion_anterior";
		return $propiedades;
	}

	function get_tabla()
	//Acceso al datos_tablas de RELACIONES
	{
		if (! isset($this->tabla)) {
			$this->tabla = $this->controlador->get_tabla_relaciones();
		}
		return $this->tabla;
	}

	function mostrar_detalle_relacion()
	{
		if( isset($this->seleccion_relacion) ){
			return true;	
		}
		return false;
	}

	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		if( $this->mostrar_detalle_relacion() ){
			$eventos += eventos::evento_estandar('cancelar_edicion',"C&ancelar");
		}		
		return $eventos;
	}

	function evt__cancelar_edicion()
	{
		$this->limpiar_seleccion();	
	}

	function get_lista_ei()
	{
		$ei[] = "relaciones_lista";
		if( $this->mostrar_detalle_relacion() ){
			$ei[] = "relaciones_columnas";
		}
		return $ei;	
	}
	
	function evt__post_cargar_datos_dependencias()
	{
		if( $this->mostrar_efs_detalle() ){
			//Protejo la efs seleccionada de la eliminacion
			$this->dependencias["efs_lista"]->set_fila_protegida($this->seleccion_relacion_anterior);
			//Agrego el evento "modificacion" y lo establezco como predeterminado
			$this->dependencias["efs"]->agregar_evento( eventos::modificacion(null, false), true );
		}
		if (isset($this->seleccion_relacion)) {
			$this->dependencias["efs_lista"]->seleccionar($this->seleccion_relacion);
		}
	}
	
	function limpiar_seleccion()
	{
		unset($this->seleccion_relacion);
		unset($this->seleccion_relacion_anterior);
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
					$this->id_intermedio_relaciones[$id] = $this->get_tabla()->nueva_fila($registros[$id]);
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
		if(isset($this->id_intermedio_relaciones[$id])){
			$id = $this->id_intermedio_relaciones[$id];
		}
		$this->seleccion_relacion = $id;
	}
	
	function seleccionar_ef($id)
	/**
	*	Selecciona un ef por su identificador real
	*/
	{
		$id_interno = $this->get_tabla()->get_id_fila_condicion(array('identificador'=>$id));
		if (count($id_interno) == 1) {
			$this->evt__efs_lista__seleccion($id_interno[0]);
		} else {
			throw new excepcion_toba("No se encontro el ef $id.");
		}
	}

	//-----------------------------------------
	//---- EI: Info detalla de un EF ----------
	//-----------------------------------------

	function evt__efs__modificacion($datos)
	{
		$this->get_tabla()->modificar_fila($this->seleccion_relacion_anterior, $datos);
	}
	
	function evt__efs__carga()
	{
		$this->seleccion_relacion_anterior = $this->seleccion_relacion;
		return $this->get_tabla()->get_fila($this->seleccion_relacion_anterior);
	}

	function evt__efs__cancelar()
	{
		unset($this->seleccion_relacion);
		unset($this->seleccion_relacion_anterior);
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
		//Inicializacion del EF actual
		$x = $this->get_tabla()->get_fila_columna($this->seleccion_relacion_anterior,"inicializacion");
		if(isset($x)){
			$inicializacion = parsear_propiedades($x);
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
		return $temp;
	}
	
	function evt__efs_ini__modificacion($datos)
	{
		/*
			ATENCION: Falta la validacion de que los campos obligatorios esten seteados
		*/
		$temp = array();
		foreach($datos as $parametro){
			if(trim($parametro['valor'])!=""){
				$temp[$parametro['clave']] = $parametro['valor'];
			}
		}
		if(count($temp)>0){
			$resultado = empaquetar_propiedades($temp); //echo "<pre> $resultado </pre>";
			//Tengo que validar que los obligatorios existan
			$this->get_tabla()->set_fila_columna_valor($this->seleccion_relacion_anterior,"inicializacion",$resultado);
		}
	}
	
	function get_definicion_parametros()
	//Recupero la informacion de los parametros de un EF puntual
	{
		$ef = $this->get_tabla()->get_fila_columna( $this->seleccion_relacion_anterior , "elemento_formulario");
		$parametros = call_user_func(array($ef,"get_parametros"));
		return $parametros;
	}

	//-------------------------------------------------------------------

	//*******************************************************************
	//**  RELACIONEs  *************************************************
	//*******************************************************************

	/*
		Esta funcion y su contrapartida en DAO hacen cosas raras relacionadas
		con la idiosincracia actual del sistema cascadas ( no se soporta que
		los combos disparen parametros multiples )...
		
			La forma ideal de trabajar este tema seria con elementos toba
			que sepan como estan formados
	*/

	function get_lista_tablas()
	{
		$filas = $this->get_entidad()->tabla('dependencias')->get_filas();
		for($a=0;$a<count($filas);$a++){
			$datos[$a]['objeto'] = $filas[$a]['identificador']. "," .$filas[$a]['objeto_proveedor'];
			$datos[$a]['desc'] = $filas[$a]['descripcion'];
		}
		return $datos;
	}
	
	function get_columnas_padre(){}
	
	function get_columnas_hija(){}
	
	//-------------------------------------------------------------
	//-- FORM
	//-------------------------------------------------------------
	
	function rel_form_a_fila($datos)
	//Convierte el contenido del form a una fila
	{
		$fila['identificador'] = $datos['identificador'];
		$fila['cascada'] = $datos['cascada'];
		$fila['orden'] = $datos['orden'];
		//-- PADRE --
		$padre = explode(",",$datos['padre']);
		$fila['padre_id'] = $padre[0];
		$fila['padre_proyecto'] = toba::get_hilo()->obtener_proyecto();
		$fila['padre_objeto'] = $padre[1];
		$fila['padre_clave'] = implode(",",$datos['padre_columnas']);
		//-- HIJO --
		$hijo = explode(",",$datos['hija']);
		$fila['hijo_id'] = $hijo[0];
		$fila['hijo_proyecto'] = toba::get_hilo()->obtener_proyecto();
		$fila['hijo_objeto'] = $hijo[1];
		$fila['hijo_clave'] = implode(",",$datos['hija_columnas']);
		return $fila;
	}
	
	function rel_fila_a_form($fila)
	{
		$datos['padre'] = $fila['padre_id'] . "," . $fila['padre_objeto'];
		$datos['padre_columnas'] = explode(",", $fila['padre_clave']);
		$datos['hija'] = $fila['hijo_id'] . "," . $fila['hijo_objeto'];
		$datos['hija_columnas'] = explode(",", $fila['hijo_clave']);
		$datos['identificador'] = $fila['identificador'];
		$datos['cascada'] = $fila['cascada'];
		$datos['orden'] = $fila['orden'];
		return $datos;
	}


	function evt__rel_form__alta($datos)
	{
		$fila = $this->rel_form_a_fila($datos);
		//-- VALIDACIONES --
		//Cantidad de claves equivalente
		//Padre e hijo distinto
		//Estrella
		$this->get_tabla()->nueva_fila($fila);
	}
	
	function evt__rel_form__carga()
	{
		if(isset($this->seleccion_relacion)){
			$this->seleccion_relacion_anterior = $this->seleccion_relacion;
			$fila = $this->get_tabla()->get_fila($this->seleccion_relacion_anterior);
			return $this->rel_fila_a_form($fila);
		}
	}

	function evt__rel_form__baja()
	{
		$this->get_tabla()->eliminar_fila($this->seleccion_relacion_anterior);
		$this->evt__rel_form__cancelar();
	}
	
	function evt__rel_form__modificacion($datos)
	{
		$fila = $this->rel_form_a_fila($datos);
		$this->get_tabla()->modificar_fila($this->seleccion_relacion_anterior, $fila);
		$this->evt__rel_form__cancelar();
	}
	
	function evt__rel_form__cancelar()
	{
		unset($this->seleccion_relacion_anterior);
		unset($this->seleccion_relacion);
		$this->dependencias["rel_cuadro"]->deseleccionar();
	}

	function evt__salida__relacion()
	{	
		echo "HOLA";
		$this->evt__rel_form__cancelar();
	}

	//-------------------------------------------------------------
	//-- Cuadro
	//-------------------------------------------------------------

	function evt__rel_cuadro__seleccion($id)
	{
		$this->seleccion_relacion = $id;
	}

	function evt__rel_cuadro__carga()
	{
		return $this->get_tabla()->get_filas();
	}
	//-------------------------------------------------------------

}
?>