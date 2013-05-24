<?php

class toba_datos_tabla_info extends toba_componente_info
{
	static function get_tipo_abreviado()
	{
		return "Tabla";		
	}
	
	function get_nombre_instancia_abreviado()
	{
		return "dt";	
	}		

	/**
	*	Retorna la metaclase correspondiente al AP del datos tabla
	*/
	function get_metaclase_subcomponente($subcomponente)
	{
		$datos = $this->datos['_info_estructura'];
		$datos['objeto'] = $this->datos['_info']['objeto'];
		$datos['proyecto'] = $this->datos['_info']['proyecto'];
		return new toba_ap_tabla_db_info($datos);
	}
	
	/**
	 * Duplica un objeto y sus dependencias recursivamente.
	 * En el caso del datos tabla, si el proyecto/fuente no difiere no se clona, se reusa
	 *
	 * @param array $nuevos_datos Datos a modificar en la base del objeto. Para anexar algo al nombre se utiliza el campo 'anexo_nombre'
	 * @param boolean/string $dir_subclases Si el componente tiene subclases clona los archivos, en caso afirmativo indicar la ruta destino (relativa)
	 * @param boolean $con_transaccion	Indica si la clonación se debe incluír en una transaccion
	 * @return array Clave del objeto que resulta del clonado
	 */
	function clonar($nuevos_datos, $dir_subclases=false, $con_transaccion = true)
	{
		$distinto = false;
		//-- Si difiere en el proyecto
		if (isset($nuevos_datos['fuente_datos_proyecto']) && $nuevos_datos['fuente_datos_proyecto'] != $this->datos['_info']['fuente_proyecto']) {
			$distinto = true;
		}
		//-- Si difiere en la fuente
		if (isset($nuevos_datos['fuente_datos']) && $nuevos_datos['fuente_datos'] != $this->datos['_info']['fuente']) {
			$distinto = true;
		}
		//Cambiar el punto de montaje no implica clonar, se tiene que hacer una personalizacion que cambie la subclase.
		
		if ($distinto) {
			return parent::clonar($nuevos_datos, $dir_subclases, $con_transaccion);
		} else {
			//Se retorna a si mismo, se reusa
			$clave = array();
			$clave['componente'] = $this->datos['_info']['objeto'];
			$clave['proyecto'] = $this->datos['_info']['proyecto'];
			return $clave;			
		}
	}
	
	//---------------------------------------------------------------------	
	//-- Recorrible como ARBOL
	//---------------------------------------------------------------------
	
	function get_utilerias()
	{
		//--- Mejora para el caso de que la query sea una unica
		if (isset($this->datos['_info']['ap_punto_montaje'])) {
			$this->datos['_info_estructura']['ap_punto_montaje'] = $this->datos['_info']['ap_punto_montaje'];
		}
		if (isset($this->datos['_info']['ap_clase'])) {
			$this->datos['_info_estructura']['ap_clase'] = $this->datos['_info']['ap_clase'];
		}
		if (isset($this->datos['_info']['ap_archivo'])) {
			$this->datos['_info_estructura']['ap_sub_clase_archivo'] = $this->datos['_info']['ap_archivo'];
		}		
		
		$iconos = array();
		if (isset($this->datos['_info_estructura']['ap_sub_clase_archivo'])) {			
			if (admin_util::existe_archivo_subclase($this->datos['_info_estructura']['ap_sub_clase_archivo'], $this->datos['_info_estructura']['ap_punto_montaje'])) {
				$iconos[] = toba_componente_info::get_utileria_editor_abrir_php(array('proyecto'=>$this->proyecto, 'componente' =>$this->id ),
																	'ap',
																	'reflexion/abrir_ap.gif' );				
				$iconos[] = toba_componente_info::get_utileria_editor_ver_php(array('proyecto'=>$this->proyecto, 'componente' =>$this->id ),
																	'ap',
																	'nucleo/php_ap.gif' );
			} else {
				$iconos[] = toba_componente_info::get_utileria_editor_ver_php(array('proyecto'=>$this->proyecto, 'componente' =>$this->id ),
																	'ap',
																	'nucleo/php_ap_inexistente.gif',
																	false );
			}
		}
		return array_merge($iconos, parent::get_utilerias());	
	}	

	/**
	 * La clonacion del DT puede implicar clonar su AP
	 */
	protected function clonar_subclase($dr, $dir_subclases, $proyecto_dest)
	{
		parent::clonar_subclase($dr, $dir_subclases, $proyecto_dest);
		if (isset($this->datos['_info_estructura']['ap_sub_clase_archivo'])) {
			$archivo = $this->datos['_info_estructura']['ap_sub_clase_archivo'];
			$nuevo_archivo = $dir_subclases."/".basename($archivo);
			
			$id_pm_origen = $this->get_punto_montaje();						
			$id_pm_destino = $dr->tabla('base')->get_fila_columna(0, 'punto_montaje');							
			
			//Busco los directorios de copia utilizando los puntos de montaje
			$path_origen = $this->get_path_clonacion($id_pm_origen,$this->proyecto);
			$path_destino = $this->get_path_clonacion($id_pm_destino, $proyecto_dest, $path_origen);
			
			$dr->tabla('prop_basicas')->set_fila_columna_valor(0, 'ap_archivo', $nuevo_archivo);
			 $dr->tabla('prop_basicas')->set_fila_columna_valor(0, 'punto_montaje', $id_pm_destino);
			 
			//--- Si el dir. destino no existe, se lo crea
			if (!file_exists($path_destino.$dir_subclases)) {
				toba_manejador_archivos::crear_arbol_directorios($path_destino.$dir_subclases);
			}
			if (! copy($path_origen.$archivo, $path_destino.$nuevo_archivo)) {
				throw new toba_error('No es posible copiar el archivo desde '.$path_origen.$archivo.' hacia '.$path_destino.$nuevo_archivo);
			}
		}
	}		
	
	//---------------------------------------------------------------------	
	//-- Generacion de METADATOS para otros componentes
	//---------------------------------------------------------------------

	private function is_col_fk($col) {
		foreach ($this->datos['_info_fks'] as $fk) {
			if ($fk['columna_ext'] == $col) {
				return true;
			}
		}
		return false;
	}

	/**
	*	Exporta la definicion de una manera entendible para el datos_tabla de la tabla 
	*		donde se guardan los EFs del ei_formulario
	*/
	function exportar_datos_efs($incluir_pk=false)
	{
		$datos = array();
		$a=0;
		foreach($this->datos['_info_columnas'] as $columna){
			// HACK: Evitamos que se carguen las columnas de las tablas extendidas
			// que son foreign keys para evitar repetición de columnas
			if ($this->is_col_fk($columna['columna'])) {
				continue;
			}

			if( (!$columna['pk']) || $incluir_pk) {
				$datos[$a]['identificador'] = $columna['columna'];
				$datos[$a]['columnas'] = $columna['columna'];
				$datos[$a]['etiqueta'] = ucfirst(  str_replace("_"," ",$columna['columna']) );
				if(isset($columna['secuencia']) && $columna['secuencia'] != ''){
					$datos[$a]['elemento_formulario'] = 'ef_fijo';
				}else{
					if($columna['no_nulo_db']) $datos[$a]['obligatorio'] = 1;
					switch($columna['tipo']){
						case 'E':
							$datos[$a]['elemento_formulario'] = 'ef_editable_numero';
							break;
						case 'N':
							$datos[$a]['elemento_formulario'] = 'ef_editable_numero';
							break;
						case 'L':
							$datos[$a]['elemento_formulario'] = 'ef_checkbox';
							break;
						case 'F':
							$datos[$a]['elemento_formulario'] = 'ef_editable_fecha';
							break;
						case 'B':
							$datos[$a]['elemento_formulario'] = 'ef_upload';
							break;
						case 'X':
							$datos[$a]['elemento_formulario'] = 'ef_editable_textarea';
							break;							
						default:
							$datos[$a]['elemento_formulario'] = 'ef_editable';
					}
				}
				// Si es editable, pongo el tamaño de campos
				if( $datos[$a]['elemento_formulario'] == 'ef_editable' ) {
					if($columna['largo'] > 0) {
						$datos[$a]['edit_maximo'] = $columna['largo'];
						if($columna['largo'] < 80) {
							$datos[$a]['edit_tamano'] = $columna['largo'];
						}else{
							$datos[$a]['edit_tamano'] = 80;
						}
					}
				}
				$datos[$a]['orden'] = $a+100;
				$a++;			
			}
		}
		return $datos;
	}

	/**
	*	Exporta la definicion de una manera entendible para el datos_tabla de la tabla 
	*		donde se guardan las columnas del ei_cuadro
	*/
	function exportar_datos_columnas($incluir_pk=false)
	{
		$datos = array();
		$a=0;
		foreach($this->datos['_info_columnas'] as $columna){
			if( ((!$columna['pk']) || $incluir_pk) && $columna['secuencia'] == '' ){
				$datos[$a]['clave'] = $columna['columna'];
				$datos[$a]['titulo'] = ucfirst(  str_replace("_"," ",$columna['columna']) );
				switch($columna['tipo']){
					case 'E':
						$datos[$a]['estilo'] = '0';//numero-1
						break;
					case 'N':
						$datos[$a]['estilo'] = '0';
						break;
					default:
						$datos[$a]['estilo'] = '4';	//texto-1
				}
				$datos[$a]['orden'] = $a;
				$a++;			
			}
		}
		return $datos;
	}

	/**
	 *  Obtiene la informacion de metadatos de las columnas del dt, puede filtrar por
	 * condiciones particulares
	 * @param array $id Arreglo asociativo de condiciones ('nombre' => 'valor')
	 * @return array
	 */
	function get_info_columnas($id = array())
	{
		$resultado = $this->datos['_info_columnas'];
		if (! empty($id)){
			$cond = array_keys($id);
			foreach($resultado as $klave => $columna){
				$valido = true;
				foreach($cond as $col){
					$valido = $valido && ($columna[$col] == $id[$col]);
				}
				if (! $valido){
					unset($resultado[$klave]);
				}
			}//fe
		}
		return $resultado;
	}


	//------------------------------------------------------------------------
	//------ METACLASE -------------------------------------------------------
	//------------------------------------------------------------------------

	function get_molde_subclase()
	{
		$molde = $this->get_molde_vacio();

		//-- Validar Ingreso
		$doc = "Ventana de validacion que se invoca cuando se crea o modifica una fila en memoria. Lanzar una excepcion en caso de error";
		$comentarios = array(
			$doc,
			'@param array $fila Datos de la fila',
			'@param mixed $id Id. interno de la fila, si tiene (en el caso modificacion de la fila)'			
		);
		$metodo = new toba_codigo_metodo_php('evt__validar_ingreso', array('$fila','$id=null'), $comentarios);
		$metodo->set_doc($doc);
		$molde->agregar($metodo);
		
		//-- Validar Foña
		$doc = "Ventana de validacion que se invoca antes de sincronizar una fila con la base";
		$comentarios = array(
			$doc,
			"El proceso puede ser abortado con un toba_error, el mensaje se muestra al usuario",
			'@param array $fila Asociativo clave-valor de la fila a validar'
		);
		$metodo = new toba_codigo_metodo_php('evt__validar_fila', array('$fila'), $comentarios);
		$metodo->set_doc($doc);
		$molde->agregar($metodo);		
		
		return $molde;
	}
}
?>