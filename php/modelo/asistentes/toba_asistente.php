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
	protected $asumir_confirmacion = false;	//Permite asumir que las opciones de generacion booleanas se confirman afirmativamente
	protected $dr_molde;  //Datos relacion del cual surje o surgio el molde (para el caso en que aun se este construyendo)
	protected $moldes = array(); //Moldes utilizados en el asistente
	
	
	
	//Manejo de clases de consultas
	protected $archivos_consultas = array();
	
	function __construct($molde=null, $dr_molde=null)
	{
		if (isset($molde)) {
			$this->id_molde_proyecto = $molde['molde']['proyecto'];
			$this->id_molde = $molde['molde']['molde'];
			//Cargo el molde
			foreach (array_keys($molde) as $parte) {
				$this->$parte = $molde[$parte];
			}
		}
		if (isset($dr_molde)) {
			$this->dr_molde = $dr_molde;
		}
		if (! isset($this->id_molde_proyecto)) {
			$this->id_molde_proyecto = toba_contexto_info::get_proyecto();
		}
		$this->valores_predefinidos = toba_info_editores::get_opciones_predefinidas_molde();
	}	
	
	function registrar_molde(toba_molde_elemento $molde)
	{
		$this->moldes[] = $molde;	
	}
	
	######################################################################
	## Api para el CI del generador
	######################################################################

	//-----------------------------------------------------------
	//-- Armar MOLDE: Se construye el modelo de la operacion
	//-----------------------------------------------------------

	/**
	*	Indica si ya existe la informacion necesaria para disparar la generacion
	*	Hay que sobreescribirlo en cada asistente.
	*/
	function posee_informacion_completa()
	{
		$datos_molde = $this->dr_molde->tabla('molde')->get();	
		if (isset($datos_molde['carpeta_archivos']) && isset($datos_molde['prefijo_clases'])) {
			return true;	
		}
		return false;
	}	
	
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
	function ejecutar($id_item, $retorno_opciones_generacion=null, $con_transaccion  = true)
	{
		//Registro las opciones de generacion
		if (isset($retorno_opciones_generacion) && is_array($retorno_opciones_generacion)) {
			foreach ($retorno_opciones_generacion as $opcion) {
				$this->retorno_opciones_generacion[$opcion['opcion']] = $opcion['estado'];
			}
		}
		try {
			if ($con_transaccion) {abrir_transaccion();}
			//--Borra los actuales
			$op_actual = new toba_modelo_operacion($this->id_molde_proyecto, $id_item);
			$op_actual->eliminar_componentes_propios(false);
			//--Genera los nuevos
			$this->generar_elementos($id_item);
			if ($con_transaccion) {cerrar_transaccion();}
			toba::notificacion()->agregar('La generación se realizó exitosamente','info');
			return $this->log_elementos_creados;
		} catch (toba_error $e) {
			toba::logger()->error($e);
			toba::notificacion()->agregar("Fallo en la generación: ".$e->getMessage(), 'error');
			//Si viene con transaccion se aborta aca, sino se dispara la excepcion para
			// que aborte en el llamador
			if ($con_transaccion) {																					
				abortar_transaccion();																			  
			}else{
				throw $e;
			}
		}
	}
	
	function crear_item($nombre, $padre)
	{
		try {
			abrir_transaccion();
			$item = new toba_item_molde($this);
			$item->set_nombre($nombre);
			$item->set_carpeta_item($padre);
			$item->cargar_grupos_acceso_activos();
			$item->generar();
			$clave = $item->get_clave_componente_generado();
			cerrar_transaccion();
			return $clave;
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
		$pm = $this->dr_molde->tabla('molde')->get_fila_columna(0, 'punto_montaje');
		$item->set_punto_montaje($pm);		
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
		$opcion = array('opcion' => $id, 'texto' => $texto, 'ayuda' => $ayuda, 'estado'=> 1);
		$this->envio_opciones_generacion[] = $opcion;
	}
	
	/**
	*	Indica el valor que retorno de una opcion de generacion.
	*		Para ser utilizada por un asisntente derivado durante la genracion concreta
	*/
	function consultar_opcion_generacion($opcion)
	{
		if ($this->asumir_confirmacion) {
			return true;
		}
		if (isset($this->retorno_opciones_generacion[$opcion])) {
			return $this->retorno_opciones_generacion[$opcion];
		} else {
			throw new toba_error_asistentes("ASISTENTE: La opcion de generacion '$opcion' no existe!");	
		}
	}

	/**
	 * Permite asumir que las opciones de generacion booleanas se confirman afirmativamente
	 */
	function asumir_confirmaciones()
	{
		$this->asumir_confirmacion = true;
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

	function generar_efs($form, $filas, $es_filtro=false)
	{
		foreach ($filas as $fila) {
			$ef = $form->agregar_ef($fila['columna'], $fila['elemento_formulario']);
			$ef->set_etiqueta($fila['etiqueta']);
			if (! $es_filtro) {
				//-- Solo se tiene en cuenta lo obligatorio para los form
				$ef->set_propiedad('obligatorio', $fila['ef_obligatorio']);
			}			
			//Largo EDITABLEs
			if($fila['dt_largo']) {
				$ef->set_propiedad('edit_maximo', $fila['dt_largo']);
				if ($fila['dt_largo'] > 60) {
					$ef->set_propiedad('edit_tamano', 60);
				} else {
					$ef->set_propiedad('edit_tamano', $fila['dt_largo']);
				}
			}
			if (isset($fila['ef_carga_origen'])) {
				$ef->set_propiedad('carga_no_seteado', '-- Seleccione --');
				switch ($fila['ef_carga_origen']) {					
					case 'datos_tabla':
						if (!$fila['ef_carga_php_metodo']) {
							$metodo_recuperacion = 'get_descripciones';
						} else {
							$metodo_recuperacion = $fila['ef_carga_php_metodo'];
						}						
						//-- Se crea el molde del datos tabla y se progama para que se genere antes de generar el ef
						$molde_dt = $this->get_molde_datos_tabla($fila['ef_carga_tabla']);
						$ef->set_molde_datos_tabla_carga($molde_dt);

						//-- Setea propiedades del ef
						$ef->set_propiedad('carga_metodo', $metodo_recuperacion);						
						$ef->set_propiedad('carga_col_clave', $fila['ef_carga_col_clave']);
						$ef->set_propiedad('carga_col_desc', $fila['ef_carga_col_desc']);
						if (isset($fila['ef_carga_sql'])) {
							$molde_dt->crear_metodo_consulta($metodo_recuperacion, $fila['ef_carga_sql']);
						}											
						break;
						
					case 'consulta_php':
						//Metodo de CARGA
						if($fila['ef_carga_php_metodo']) {
							$ef->set_propiedad('carga_include',$fila['ef_carga_php_include']);
							$ef->set_propiedad('carga_clase',$fila['ef_carga_php_clase']);
							$ef->set_propiedad('carga_metodo',$fila['ef_carga_php_metodo']);
							$ef->set_propiedad('carga_col_clave',$fila['ef_carga_col_clave']);
							$ef->set_propiedad('carga_col_desc',$fila['ef_carga_col_desc']);
							if(isset($fila['ef_carga_sql'])){
								$this->crear_consulta_php($fila['ef_carga_php_include'],
														$fila['ef_carga_php_clase'],
														$fila['ef_carga_php_metodo'],
														$fila['ef_carga_sql']);
							}
						}
						break;
						
					default:
						throw new toba_error('No esta definida la acción para el método de carga '.$fila['ef_carga_origen']);
				}
			}
			//Procesar en JAVASCRIPT?
		}
	}
	
	/**
	 * Dado un molde de un datos_tabla, si no existe el componente lo crea y agrega las filas. Si ya existe actualiza los campos
	 */
	function generar_datos_tabla($molde_dt, $tabla, $filas)
	{
		$dt_actual = toba_info_editores::get_dt_de_tabla_fuente($tabla, $this->get_fuente());
		if (empty($dt_actual)) {		
			$molde_dt->crear($tabla);
			if (isset($filas)) {
				foreach( $filas as $fila ) {
					$col = $molde_dt->agregar_columna($fila['columna'], $fila['dt_tipo_dato']);
					if($fila['dt_pk']){
						$col->pk();
					}
					if($fila['dt_secuencia']){
						$col->set_secuencia($fila['dt_secuencia']);
					}
				}
			} else {
				//-- Si no se pasan filas explicitas, se descubren solas
				$molde_dt->actualizar_campos();
			}
		} else {
			$molde_dt->cargar($dt_actual['id']);
			$molde_dt->actualizar_campos();			
		}
	}

	/**
	 * Dado el nombre de una tabla, retorna el molde del datos_tabla ya sea representando a un comp. existente o creando uno nuevo
	 * @param string $tabla
	 * @return toba_datos_tabla_molde
	 * @todo: Puede pasar que el molde a crear ya haya sido creado previamente para esta operación, haria falta un indice de los moldes
	 */
	function get_molde_datos_tabla($tabla, $fuente=null)
	{
		$molde = null;
		if (isset($fuente)) {
			$this->molde['fuente'] = $fuente;
		}
		//Busco si existe algun dt cargado para la tabla
		//en el asistente actual
		foreach($this->moldes as $klave => $molde_existente){
			$es_dt = ($molde_existente->get_clase() == 'toba_datos_tabla');
			if ($es_dt  && $molde_existente->get_tabla_nombre() == $tabla){
				$molde = $this->moldes[$klave];
			}
		}
		//Si no encontre ningun dt entonces lo genero
		if ( is_null($molde)) {
					$molde = new toba_datos_tabla_molde($this);
					$this->generar_datos_tabla($molde, $tabla, null);					
					if (isset($this->dr_molde)) {		//Recupero el punto de montaje y se lo seteo si estoy en un asistente
						$pm = $this->dr_molde->tabla('molde')->get_fila_columna(0, 'punto_montaje');
						$molde->set_punto_montaje($pm);
					}					
		}
		return $molde;
	}


	function generar_datos_relacion($molde_dr)
	{
		$molde_dr->crear($this->molde['nombre']);
		$molde_dr->crear_relaciones();
	}
	
	function get_molde_datos_relacion($fuente = null)
	{
		if (isset($fuente)){
			$this->molde['fuente'] = $fuente;
		}		
		$pm = $this->dr_molde->tabla('molde')->get_fila_columna(0, 'punto_montaje');		
		
		$molde = new toba_datos_relacion_molde($this);
		$molde->set_punto_montaje($pm);
		$this->generar_datos_relacion($molde);
		
		return $molde;
	}

	//-- API para los elementos del molde ----------------------------------------

	function get_proyecto()
	{
		return $this->id_molde_proyecto;	
	}
	
	function get_carpeta_archivos()
	{
		if (isset($this->molde['carpeta_archivos'])) {
			return $this->molde['carpeta_archivos'];
		}
	}
	
	function get_carpeta_archivos_datos()
	{
		return 'datos';
	}	
	
	function get_id_elemento()
	{
		return $this->id_elementos++;
	}

	function get_fuente()
	{
		return $this->molde['fuente'];
	}	
	
	function tiene_fuente_definida()
	{
		return isset($this->molde['fuente']);
	}

	//-- Manejo de consultas_php ------------------------

	/**
	 * @return toba_codigo_metodo_php
	 */
	function crear_metodo_consulta($nombre, $sql, $parametros=null)
	{
		$param_metodo = isset($parametros)? array('$filtro=array()') : array();
		$metodo = new toba_codigo_metodo_php($nombre, $param_metodo);
		$fuente = $this->get_fuente();
		$sentencia_consulta = "return toba::db('$fuente')->consultar(\$sql);";
		if(!isset($parametros)){
			$php = 	"\$sql = \"$sql\";" . "\n" . $sentencia_consulta;
		}else{
			$php = "\$where = array();" . "\n";
			foreach($parametros as $id => $operador) {
				$php .= "if (isset(\$filtro['$id'])) {" . "\n";
				if($operador == 'LIKE' || $operador == 'ILIKE') {
					$php .= "\t\$where[] = \"$id $operador \".quote(\"%{\$filtro['$id']}%\");" . "\n";
				} else {
					$php .= "\t\$where[] = \"$id $operador \".quote(\$filtro['$id']);" . "\n";
				}
				$php .= "}" . "\n";
			}
			$php .=	"\$sql = \"$sql\";" . "\n";
			$php .= "if (count(\$where)>0) {" . "\n";
			$php .= "\t\$sql = sql_concatenar_where(\$sql, \$where);" . "\n";
			$php .= "}" . "\n";
			$php .= $sentencia_consulta;
		}
		$metodo->set_contenido($php);
		return $metodo;	
	}

	//----------------------------------------------------------------------
	//-- Creacion de archivos que no son extensiones.
	//----------------------------------------------------------------------

	function crear_consulta_php($include, $clase, $metodo, $sql, $parametros=null)
	{
		$metodo_php = $this->crear_metodo_consulta($metodo, $sql, $parametros);
		$this->agregar_archivo($include, $clase, $metodo_php);
	}

	function agregar_archivo($include, $clase, $metodo_php)
	{
		if(!isset($this->archivos_consultas[$include])){
			$this->archivos_consultas[$include] = new toba_codigo_clase($clase);
		}
		$this->archivos_consultas[$include]->agregar($metodo_php);
	}

	function generar_archivos_consultas()
	{
		foreach($this->archivos_consultas as $path_relativo => $clase) {
			//Control para que no hayan metodos duplicados, se hace aca
			//porque sino se pierde el acceso al editor. El control se deberia hacer en la carga misma
			if (count($clase->get_indice_metodos_php()) 
				!= count(array_unique($clase->get_indice_metodos_php()))) {
				throw new toba_error('Existen nombres de metodos duplicados!');
			}
			
			$path = $this->directorio_absoluto(). $path_relativo;				
			$existente = null;
			if( file_exists($path) && is_file($path) ) {
				$existente = toba_archivo_php::codigo_sacar_tags_php(file_get_contents($path));
			}			
			$php = $clase->generar_codigo($existente);
			toba_manejador_archivos::crear_archivo_con_datos($path, "<?php\n" . $php . "\n?>");
			$this->registrar_elemento_creado('Archivo consultas', $this->get_proyecto(),	$this->id_molde_proyecto );
		}
	}
	
	/**
	 * Determina el directorio absoluto utilizando el punto de montaje o el dir por defecto del proyecto
	 * @return string $path
	 * @ignore
	 */
	function directorio_absoluto()
	{
		$id_pm = $this->dr_molde->tabla('molde')->get_fila_columna(0, 'punto_montaje');
		if (! is_null($id_pm) && ($id_pm !== 0)) {
			$punto_montaje = toba_pms::instancia()->get_instancia_pm_proyecto($this->get_proyecto(), $id_pm);
			$path = $punto_montaje->get_path_absoluto() . '/';
		} else {
			$path = toba::instancia()->get_path_proyecto($this->id_molde_proyecto) . '/php/';
		}
		return $path;
	}
}
?>