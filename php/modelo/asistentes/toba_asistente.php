<?php

abstract class toba_asistente
{
	protected $id_molde_proyecto;
	protected $id_molde;
	protected $item;		// Molde del item
	protected $ci;			// Shortcut al molde del CI
	protected $log_elementos_creados;
	protected $opciones;
	
	function __construct($molde)
	{
		$this->id_molde_proyecto = $molde['molde']['proyecto'];
		$this->id_molde = $molde['molde']['molde'];
		//Cargo el molde
		foreach (array_keys($molde) as $parte) {
			$this->$parte = $molde[$parte];
		}
		$this->opciones = toba_info_editores::get_opciones_generacion();
	}	
	
	//-----------------------------------------------------------
	//-- Armar MOLDE: Se construye el modelo de la operacion
	//-----------------------------------------------------------

	/**
	* Se crea el molde 
	*/
	function generar_molde()
	{
		$this->generar_base();
		$this->generar();
	}

	function generar_base()
	{
		$this->item = new toba_item_molde($this);
		$this->ci = $this->item->ci();
		$this->item->set_nombre($this->molde['nombre']);
		$this->item->set_carpeta_item($this->molde['carpeta_item']);
		$this->item->cargar_grupos_acceso_activos();
	}

	abstract function generar();

	//----------------------------------------------------------------------
	//-- Crear OPERACION: Se transforma el modelo a elementos toba concretos
	//----------------------------------------------------------------------

	/**
	*	Usa el molde para generar una operacion.
	*	Hay que definir los modos de regeneracion: no pisar archivos pero si metadatos, todo nuevo, etc.
	*/
	function crear_operacion($forzar_regeneracion=false)
	{
		if(  $this->existe_generacion_previa() ) {
			if ($forzar_regeneracion) {
				$this->borrar_generacion_previa();
			} else {
				throw new toba_error('');
			}
		}
		$this->generar_elementos();
		toba::notificacion()->agregar('La generación se realizó exitosamente','info');
	}

	function generar_elementos()
	{
		try {
			abrir_transaccion();
			$this->item->generar();
			$this->guardar_log_elementos_generados();
			cerrar_transaccion();
		} catch (toba_error $e) {
			toba::notificacion()->agregar($e->getMessage());
			abortar_transaccion();
		}
	}

	function existe_generacion_previa()
	{
		//a nivel a archivos hay que preguntarle a la operacion que va a crear
		//Leer en this->molde_molde_resultado
		return false;	
	}

	protected function borrar_generacion_previa()
	{
		
	}

	//---------------------------------------------------
	//-- API para los elementos
	//---------------------------------------------------

	function get_proyecto()
	{
		return $this->id_molde_proyecto;	
	}
	
	function get_carpeta_archivos()
	{
		return $this->molde['carpeta_archivos'];
	}

	//---------------------------------------------------
	//-- LOG de elementos creados
	//---------------------------------------------------

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

	//---------------------------------------------------
	//-- Primitivas para los hijos
	//---------------------------------------------------

	function get_opcion($opcion)
	{
		if(isset($this->opcion[$opcion])){
			return 	$this->opcion[$opcion];
		}
		return null;
	}

	function generar_efs($form, $filas)
	{
		foreach( $filas as $fila ) {
			$ef = $form->agregar_ef($fila['columna'], $fila['elemento_formulario']);
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
		$tabla->set_tabla($nombre);
		foreach( $filas as $fila ) {
			$col = $tabla->agregar_columna($fila['columna'], $fila['dt_tipo_dato']);
			if($fila['dt_pk']){
				$col->pk();
			}
			if($fila['dt_secuencia']){
				$col->set_secuencia($fila['dt_secuencia']);
			}
		}
	}

	//-- Manejo de consultas_php ------------------------

	function crear_consulta_dt($tabla, $sql)
	{
		$clase = $this->molde['prefijo_clases']. 'dt';
		$tabla->extender($clase, $clase . '.php');
		//$tabla->php()->
	}

	function crear_consulta_php($include, $clase, $metodo, $sql, $parametros='')
	{
		/*
		$archivo = new toba_archivo_php($path);
		if(!$archivo->existe()) {
		}
		if(!$archivo->contiene_clase($clase)){
		}
		if(!$contiene->contiene_metodo($metodo)){
		}*/

		$path = toba::proyecto()->get_path() . '/php/' . $include;
		$php = "<?php\nclass $clase\n{\n";
		$php .= "
	function $metodo($parametros)
	{
		\$sql = \"$sql\";
		return consultar_fuente(\$sql);
	}\n";
		$php .= "\n}\n?>";
		toba_manejador_archivos::crear_archivo_con_datos($path, $php);
	}
}
?>