<?php

abstract class toba_asistente
{
	protected $id_molde_proyecto;
	protected $id_molde;
	protected $item;		// Molde del item
	protected $ci;			// Shortcut al molde del CI
	protected $log_elementos_creados;
	protected $valores_predefinidos;
	protected $envio_opciones_generacion = array();
	protected $retorno_opciones_generacion = array();
	protected $bloqueos_generacion = array();
	protected $archivos_php;
	protected $id_elementos = 0;
	//Manejo de clases de consultas
	protected $archivos_consultas = array();
	
	function __construct($molde)
	{
		$this->id_molde_proyecto = $molde['molde']['proyecto'];
		$this->id_molde = $molde['molde']['molde'];
		//Cargo el molde
		foreach (array_keys($molde) as $parte) {
			$this->$parte = $molde[$parte];
		}
		$this->valores_predefinidos = toba_info_editores::get_opciones_predefinidas_molde();
	}	
	
	######################################################################
	## Api para el CI del generador
	######################################################################

	//-----------------------------------------------------------
	//-- Armar MOLDE: Se construye el modelo de la operacion
	//-----------------------------------------------------------

	/**
	* Se crea el molde y se deja a disposicion
	*/
	function preparar_molde()
	{
		$this->generar_base();
		$this->generar();
	}

	protected function generar_base()
	{
		$this->ci = new toba_ci_molde($this);
	}

	abstract protected function generar();

	//----------------------------------------------------------------------
	//-- Informacion sobre el molde
	//----------------------------------------------------------------------

	function get_opciones_generacion()
	{
		return $this->envio_opciones_generacion;
	}

	function get_bloqueos()
	{
		return $this->bloqueos_generacion;
	}

	//----------------------------------------------------------------------
	//-- Crear OPERACION: Se transforma el modelo a elementos toba concretos
	//----------------------------------------------------------------------

	/**
	*	Usa el molde para generar una operacion.
	*	Hay que definir los modos de regeneracion: no pisar archivos pero si metadatos, todo nuevo, etc.
	*/
	function crear_operacion($id_item, $retorno_opciones_generacion=null)
	{
		//Registro las opciones de generacion
		if(isset($retorno_opciones_generacion) && is_array($retorno_opciones_generacion) ) {
			foreach( $retorno_opciones_generacion as $opcion) {
				$this->retorno_opciones_generacion[$opcion['opcion']] = $opcion['estado'];
			}
		}
		try {
			abrir_transaccion();
			//--Borra los actuales
			$op_actual = new toba_modelo_operacion($this->id_molde_proyecto, $id_item);
			$op_actual->eliminar_componentes_propios(false);
			//--Genera los nuevos
			$this->generar_elementos($id_item);
			cerrar_transaccion();
			toba::notificacion()->agregar('La generación se realizó exitosamente','info');
			return $this->log_elementos_creados;
		} catch (toba_error $e) {
			toba::logger()->error($e);
			toba::notificacion()->agregar("Fallo en la generación: ".$e->getMessage(), 'error');
			abortar_transaccion();
		}
	}

	protected function generar_elementos($id_item)
	{
		//--- Carga el item acual y le asigna el ci creado
		$item = new toba_item_molde($this);
		$item->cargar($id_item);
		$item->set_ci($this->ci);
		$item->generar();
		
		$this->generar_archivos_consultas();
		$this->guardar_log_elementos_generados();
	}

	######################################################################
	## Primitivas para los asistentes derivados
	######################################################################

	//-- LOG de elementos creados ------------------------------

	function registrar_elemento_creado($tipo, $proyecto, $id )
	{
		static $a = 0;
		$this->log_elementos_creados[$a]['tipo'] = $tipo;
		$this->log_elementos_creados[$a]['proyecto'] = $proyecto;
		$this->log_elementos_creados[$a]['clave'] = $id;
		$a++;
	}

	/**
	*	Guarda el resultado de la generacion
	*/
	protected function guardar_log_elementos_generados()
	{
		$sql = "INSERT INTO apex_molde_operacion_log (proyecto, molde) VALUES ('$this->id_molde_proyecto','$this->id_molde')";
		ejecutar_fuente($sql);
		$id_generacion = recuperar_secuencia('apex_molde_operacion_log_seq');
		foreach( $this->log_elementos_creados as $elemento) {
			$sql = "INSERT INTO apex_molde_operacion_log_elementos (molde, generacion, tipo, proyecto, clave) VALUES ('$this->id_molde','$id_generacion','{$elemento['tipo']}','{$elemento['proyecto']}','{$elemento['clave']}')";
			ejecutar_fuente($sql);
		}
	}

	//-- Manejo de opciones ------------------------------

	/**
	*	Acceso a los valores predefinidos globales
	*/
	function get_valor_predefinido($opcion)
	{
		if(isset($this->valores_predefinidos[$opcion])){
			return 	$this->valores_predefinidos[$opcion];
		}
		return null;
	}
	
	/**
	*	Setea una opcion de generacion. Para ser utilizada por un asistente
	*		derivado durante la preparacion del molde.
	*/
	function agregar_opcion_generacion($id, $texto, $ayuda=null)
	{
		$opcion = array(	'opcion'	=> $id,
							'texto'		=> $texto,
							'ayuda'		=> $ayuda,
							'estado'	=> 1 );
		$this->envio_opciones_generacion[] = $opcion;
	}
	
	/**
	*	Indica el valor que retorno de una opcion de generacion.
	*		Para ser utilizada por un asisntente derivado durante la genracion concreta
	*/
	function consultar_opcion_generacion($opcion)
	{
		if(isset($this->retorno_opciones_generacion[$opcion])) {
			return $this->retorno_opciones_generacion[$opcion];
		} else {
			throw new toba_error_asistentes("ASISTENTE: La opcion de generacion '$opcion' no existe!");	
		}
	}

	/**
	*	Agrega una falla bloqueante del molde. Se debe reportar durante la preparacion del molde.
	*/
	function agregar_bloqueo_generacion($bloqueo)
	{
		$this->bloqueos_generacion[] = $bloqueo;
	}
	
	//----------------------------------------------------------------------
	//-- Primitivas para la construccion de elementos
	//----------------------------------------------------------------------

	function generar_efs($form, $filas)
	{
		foreach( $filas as $fila ) {
			$ef = $form->agregar_ef($fila['columna'], $fila['elemento_formulario']);
			$ef->set_etiqueta($fila['etiqueta']);
			//Largo EDITABLEs
			if($fila['dt_largo']){
				$ef->set_propiedad('edit_tamano',$fila['dt_largo']);
				$ef->set_propiedad('edit_maximo',$fila['dt_largo']);
			}
			//Metodo de CARGA
			if($fila['ef_carga_php_metodo']) {
				$ef->set_propiedad('carga_include',$fila['ef_carga_php_include']);
				$ef->set_propiedad('carga_clase',$fila['ef_carga_php_clase']);
				$ef->set_propiedad('carga_metodo',$fila['ef_carga_php_metodo']);
				$ef->set_propiedad('carga_col_clave',$fila['ef_carga_col_clave']);
				$ef->set_propiedad('carga_col_desc',$fila['ef_carga_col_desc']);
				if(isset($fila['ef_carga_sql'])){
					$this->crear_consulta_php(	$fila['ef_carga_php_include'],
												$fila['ef_carga_php_clase'],
												$fila['ef_carga_php_metodo'],
												$fila['ef_carga_sql'] );
				}
			}
			//Procesar en JAVASCRIPT?
		}
	}
	
	function generar_datos_tabla($tabla, $nombre, $filas)
	{
		$dt_actual = toba_info_editores::get_dt_de_tabla_fuente($nombre, $this->get_fuente());
		if (empty($dt_actual)) {		
			$tabla->crear($nombre);
			foreach( $filas as $fila ) {
				$col = $tabla->agregar_columna($fila['columna'], $fila['dt_tipo_dato']);
				if($fila['dt_pk']){
					$col->pk();
				}
				if($fila['dt_secuencia']){
					$col->set_secuencia($fila['dt_secuencia']);
				}
			}
		} else {
			$tabla->cargar($dt_actual['id']);
			$tabla->actualizar_campos();
		}
	}

	//-- API para los elementos del molde ----------------------------------------

	function get_proyecto()
	{
		return $this->id_molde_proyecto;	
	}
	
	function get_carpeta_archivos()
	{
		return $this->molde['carpeta_archivos'];
	}
	
	function get_id_elemento()
	{
		return $this->id_elemento++;
	}

	function get_fuente()
	{
		return $this->molde['fuente'];
	}	

	//-- Manejo de consultas_php ------------------------

	function crear_consulta_dt($tabla, $metodo, $sql, $parametros=null)
	{
		$param_metodo = isset($parametros)? array('$filtro=array()') : null;
		$clase = $this->molde['prefijo_clases']. 'dt';
		$tabla->extender($clase, $clase . '.php');
		$metodo = $this->crear_metodo_consulta($metodo, $sql, $param_metodo);
		$tabla->php()->agregar($metodo);		
	}

	function crear_metodo_consulta($nombre, $sql, $parametros=null)
	{
		$param_metodo = isset($parametros)? array('$filtro=array()') : array();
		$metodo = new toba_codigo_metodo_php($nombre, $param_metodo);
		if(!isset($parametros)){
			$php = 	"\$sql = \"$sql\";" . salto_linea() .
					"return consultar_fuente(\$sql);";
		}else{
			$php = "\$where = array();" . salto_linea();
			foreach($parametros as $id => $operador) {
				$php .= "if(isset(\$filtro['$id'])) {" . salto_linea();
				if($operador == 'LIKE') {
					$php .= "\t\$where[] = \"$id $operador '\".\$filtro['$id'].\"%'\";" . salto_linea();
				} else {
					$php .= "\t\$where[] = \"$id $operador '\".\$filtro['$id'].\"'\";" . salto_linea();
				}
				$php .= "}" . salto_linea();
			}
			$php .=	"\$sql = \"$sql\";" . salto_linea();
			$php .= "if(count(\$where)>0) {" . salto_linea();
			$php .= "\t\$sql = sql_concatenar_where(\$sql, \$where);" . salto_linea();
			$php .= "}" . salto_linea();
			$php .= "return consultar_fuente(\$sql);";
		}
		$metodo->set_contenido($php);
		return $metodo;	
	}

	//----------------------------------------------------------------------
	//-- Creacion de archivos que no son extensiones.
	//----------------------------------------------------------------------

	function crear_consulta_php($include, $clase, $metodo, $sql, $parametros=null)
	{
		$archivo = $this->agregar_archivo($include, $clase);
		$metodo_php = $this->crear_metodo_consulta($metodo, $sql, $parametros);
		$metodo_php->identar(1);
		$this->archivos_consultas[$include]['metodos'][] = $metodo_php;
		/*
		if( $archivo->existe() && $archivo->contiene_metodo($metodo) ) {
			//ATENCION: Se va a sobreescribir un metodo.
			$id_opcion = 'consulta__' . $include . '__' . $clase . '__' . $metodo;
			$this->agregar_opcion_generacion($id_opcion, "Sobreescribir metodo: '$metodo' en el archivo '$include'");
		}
		*/
	}

	function agregar_archivo($include, $clase)
	{
		if(!isset($this->archivos_consultas[$include]['archivo'])){
			$archivo = new toba_archivo_php($include);
			if( $archivo->existe() ) {
				if( $archivo->contiene_codigo_php() ) {
					if( ! $archivo->contiene_clase($clase) ) {
						//Hay codigo PHP que no es de la clase... error bloqueante!
							
					}
				}
			}
			$this->archivos_consultas[$include]['archivo'] = $archivo;
			$this->archivos_consultas[$include]['clase'] = $clase;
		}
		return $this->archivos_consultas[$include]['archivo'];
	}

	function generar_archivos_consultas()
	{
		foreach($this->archivos_consultas as $id => $contenido) {
			$php = "<?php\nclass {$contenido['clase']}\n{\n";
			foreach($contenido['metodos'] as $metodo ) {
				$php .= $metodo->get_codigo();
			}
			$php .= "\n}\n?>";
			$path = toba::instancia()->get_path_proyecto( $this->get_proyecto() ) . '/php/' . $id;
			toba_manejador_archivos::crear_arbol_directorios(dirname($path));
			toba_manejador_archivos::crear_archivo_con_datos($path, $php);
			$this->registrar_elemento_creado(	'Archivo consultas', 
												$this->get_proyecto(),
												$path );
		}
	}
}
?>