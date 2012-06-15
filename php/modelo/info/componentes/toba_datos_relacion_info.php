<?php

class toba_datos_relacion_info extends toba_componente_info
{
	static function get_tipo_abreviado()
	{
		return "Relación";		
	}
	
	function get_nombre_instancia_abreviado()
	{
		return "dr";	
	}	
	
	/**
	*	Retorna la metaclase correspondiente al AP del datos relacion
	*/
	function get_metaclase_subcomponente($subcomponente)
	{
		return new toba_ap_relacion_db_info($this->datos['_info_estructura']);
	}	
	
	//---------------------------------------------------------------------	
	//-- Recorrible como ARBOL
	//---------------------------------------------------------------------

	function get_utilerias($icono_nuevo=true)
	{
		//ei_arbol($this->datos);
		$iconos = array();
		if ($icono_nuevo) {
			$iconos[] = array(
				'imagen' => toba_recurso::imagen_toba("objetos/objeto_nuevo.gif", false),
				'ayuda' => "Crear una nueva tabla asociada a la relación",
				'vinculo' => toba::vinculador()->get_url(toba_editor::get_id(),"1000247",
									array(	'destino_tipo' => 'toba_datos_relacion', 
											'destino_proyecto' => $this->proyecto,
											'destino_id' => $this->id),
								array(	'menu' => true,
										'celda_memoria' => 'central')
							),
				'plegado' => true										
			);
		}
		//--- Mejora para el caso de que la query sea una unica
		if (isset($this->datos['_info']['punto_montaje'])) {
			$this->datos['_info_estructura']['punto_montaje'] = $this->datos['_info']['punto_montaje'];
		}
		if (isset($this->datos['_info']['ap_clase'])) {
			$this->datos['_info_estructura']['ap_clase'] = $this->datos['_info']['ap_clase'];
		}
		if (isset($this->datos['_info']['ap_archivo'])) {
			$this->datos['_info_estructura']['ap_archivo'] = $this->datos['_info']['ap_archivo'];
		}		
		if (isset($this->datos['_info_estructura']['ap_clase'])) {
			// Hay PHP asociado
			if ( admin_util::existe_archivo_subclase($this->datos['_info_estructura']['ap_archivo'], $this->datos['_info_estructura']['punto_montaje']) ) {
				$iconos[] = toba_componente_info::get_utileria_editor_abrir_php( array(	'proyecto'=>$this->proyecto,
																					'componente' =>$this->id ),
																			'ap',
																			'reflexion/abrir_ap.gif' );				
				$iconos[] = toba_componente_info::get_utileria_editor_ver_php( array(	'proyecto'=>$this->proyecto,
																					'componente' =>$this->id ),
																			'ap',
																			'nucleo/php_ap.gif' );

			} else {
				$iconos[] = toba_componente_info::get_utileria_editor_ver_php( array(	'proyecto'=>$this->proyecto,
																					'componente' =>$this->id ),
																			'ap',
																			'nucleo/php_ap_inexistente.gif',
																			false );
			}
		}		
		return array_merge($iconos, parent::get_utilerias());	
	}
	
	
	/**
	 * La clonacion del DR puede implicar clonar su AP
	 */
	protected function clonar_subclase($dr, $dir_subclases, $proyecto_dest)
	{
		parent::clonar_subclase($dr, $dir_subclases, $proyecto_dest);
		if (isset($this->datos['_info_estructura']['ap_archivo'])) {
			$archivo = $this->datos['_info_estructura']['ap_archivo'];
			$nuevo_archivo = $dir_subclases."/".basename($archivo);
			
			$id_pm_origen = $this->get_punto_montaje();						
			$id_pm_destino = $dr->tabla('base')->get_fila_columna(0, 'punto_montaje');							
			
			//Busco los directorios de copia utilizando los puntos de montaje
			$path_origen = $this->get_path_clonacion($id_pm_origen,$this->proyecto);
			$path_destino = $this->get_path_clonacion($id_pm_destino, $proyecto_dest, $path_origen);
			
			$dr->tabla('prop_basicas')->set_fila_columna_valor(0, 'ap_archivo', $nuevo_archivo);
			//--- Si el dir. destino no existe, se lo crea
			if (!file_exists($path_destino.$dir_subclases)) {
				toba_manejador_archivos::crear_arbol_directorios($path_destino.$dir_subclases);
			}
			if (! copy($path_origen.$archivo, $path_destino.$nuevo_archivo)) {
				throw new toba_error('No es posible copiar el archivo desde '.$path_origen.$archivo.' hacia '.$path_destino.$nuevo_archivo);
			}			
		}
	}	
	
	//------------------------------------------------------------------------
	//------ METACLASE -------------------------------------------------------
	//------------------------------------------------------------------------

	function get_molde_subclase()
	{
		$molde = $this->get_molde_vacio();
		
		//-- Validacion
		$doc = "Ventana para validaciones de toda la relación, se ejecuta justo antes de la sincronización";
		$comentarios = array(
		 	$doc,
		 	"El proceso puede ser abortado con un toba_error, el mensaje se muestra al usuario",
		 );		
		$metodo = new toba_codigo_metodo_php('evt__validar', array(), $comentarios);
		$metodo->set_doc($doc);
		$molde->agregar($metodo);		

		return $molde;
	}

	/**
	 * Duplica un objeto y sus dependencias recursivamente
	 *
	 * @param array $nuevos_datos Datos a modificar en la base del objeto. Para anexar algo al nombre se utiliza el campo 'anexo_nombre'
	 * @param boolean/string $dir_subclases Si el componente tiene subclases clona los archivos, en caso afirmativo indicar la ruta destino (relativa)
	 * @param boolean $con_transaccion	Indica si la clonación se debe incluír en una transaccion
	 * @return array Clave del objeto que resulta del clonado
	 */
	function clonar($nuevos_datos, $dir_subclases=false, $con_transaccion = true)
	{
		//Se busca el id del datos_relacion de la clase
		$id_dr = toba_info_editores::get_dr_de_clase($this->datos['_info']['clase']);

		//Se construye el objeto datos_relacion
		$componente = array('proyecto' => $id_dr[0], 'componente' => $id_dr[1]);
		$dr = toba_constructor::get_runtime($componente);
		$dr->inicializar();

		//Se carga con el id_origen
		$dr->cargar(array('proyecto' => $this->proyecto, 'objeto' => $this->id));
		foreach ($nuevos_datos as $campo => $valor) {
			if ($campo == 'anexo_nombre') {
				$campo = 'nombre';
				$valor = $valor . $dr->tabla('base')->get_fila_columna(0, $campo);
			}
			$dr->tabla('base')->set_fila_columna_valor(0, $campo, $valor);
		}

		//Se le fuerza una inserción a los datos_tabla
		//Como la clave de los objetos son secuencias, esto garantiza claves nuevas
		$dr->forzar_insercion();
		if (!$con_transaccion) {
			$dr->persistidor()->desactivar_transaccion();
		}
		
		//-- Punto de montaje tambien se propaga
		if (isset($nuevos_datos['punto_montaje'])) {
			$dr->tabla('prop_basicas')->set_columna_valor('punto_montaje', $nuevos_datos['punto_montaje']);
		}			

		//--- Si tiene subclase, se copia el archivo y se cambia
		if ($dir_subclases !== false) {
			$proyecto_dest = isset($nuevos_datos['proyecto']) ? $nuevos_datos['proyecto'] : null;
			$this->clonar_subclase($dr, $dir_subclases, $proyecto_dest);
		}

		$dep_nuevas = array();
		$dep_viejas = array();
		//--- Se reemplazan los datos y se clonan los hijos
		foreach ($this->subelementos as $hijo) {
			//-- Si se especifico un proyecto, se propaga
			$datos_objeto = array();
			if (isset($nuevos_datos['proyecto'])) {
				$datos_objeto['proyecto'] = $nuevos_datos['proyecto'];
			}
			//-- Si se especifica un anexo de nombre, se propaga
			if (isset($nuevos_datos['anexo_nombre'])) {
				$datos_objeto['anexo_nombre'] = $nuevos_datos['anexo_nombre'];
			}
			//-- La fuente tambien se propaga
			if (isset($nuevos_datos['fuente_datos_proyecto'])) {
				$datos_objeto['fuente_datos_proyecto'] = $nuevos_datos['fuente_datos_proyecto'];
			}
			if (isset($nuevos_datos['fuente_datos'])) {
				$datos_objeto['fuente_datos'] = $nuevos_datos['fuente_datos'];
			}
			//-- Punto de montaje tambien se propaga
			if (isset($nuevos_datos['punto_montaje'])) {
				$datos_objeto['punto_montaje'] = $nuevos_datos['punto_montaje'];
			}				

			//-- SE CLONA
			$id_clon = $hijo->clonar($datos_objeto, $dir_subclases, $con_transaccion);
			//--- En el componente actual se reemplaza la dependencia por el clon
			$id_fila = $dr->tabla('dependencias')->get_id_fila_condicion(
								array('identificador' => $hijo->rol_en_consumidor()));
			$dr->tabla('dependencias')->modificar_fila(current($id_fila),
								array('objeto_proveedor' => $id_clon['componente']));

			//Aca obtengo la informacion de metadatos de la tabla recien clonada y guardo tambien
			//la info de la tabla actual.
			$index = $hijo->get_id();
			$dep_nuevas[$index] = toba_constructor::get_info( $id_clon, $hijo->get_clase_nombre());
			$dep_viejas[$index] = $hijo;	
		}
		//Si hay dependencias clonadas entonces regenero las relaciones entre tablas y entre columnas.
		if (! empty($dep_nuevas)) {
			$this->clonar_relacion_tablas($dep_nuevas, $dr->tabla('relaciones'));
			$this->clonar_relacion_columnas($dep_nuevas, $dep_viejas, $dr->tabla('columnas_relacion'));
		}
		$dr->sincronizar();

		//Se busca la clave del nuevo objeto
		$clave = $dr->tabla('base')->get_clave_valor(0);
		$clave['componente'] = $clave['objeto'];
		return $clave;
	}

	/**
	 * Reconecta las relaciones entre las tablas recien clonadas
	 * @param array $dep_nuevas Objetos toba_datos_tabla_info con la informacion de las nuevas tablas
	 * @param toba_datos_tabla $tabla_dr Objeto que representa la tabla de relaciones
	 */
	function clonar_relacion_tablas($dep_nuevas, $tabla_dr)
	{
		$relaciones = $tabla_dr->get_filas(null, true, false);
		foreach($relaciones as $id_fila => $relacion){
			//Obtengo los ids de las tablas padre e hija.
			$tabla_padre = $relacion['padre_objeto'];
			$tabla_hija = $relacion['hijo_objeto'];
			//Genero la nueva fila con los datos modificados
			$nva_fila = array('padre_objeto' => $dep_nuevas[$tabla_padre]->get_id(), 'hijo_objeto'  => $dep_nuevas[$tabla_hija]->get_id());
			$tabla_dr->modificar_fila($id_fila, $nva_fila);
		}
	}

	/**
	 * Reconecta las columnas relacionadas entre las tablas recien clonadas
	 * @param array $dep_nuevas Objetos toba_datos_tabla_info con la informacion de la nuevas tablas
	 * @param array $dep_viejas Objetos toba_datos_tabla_info con la informacion de las originales
	 * @param toba_datos_tabla $tabla_dr Objeto que representa la tabla de relacion de columnas
	 */
	function clonar_relacion_columnas($dep_nuevas, $dep_viejas, $tabla_dr)
	{
		$relaciones_disponibles = $tabla_dr->get_filas(null, true, false);
		foreach($relaciones_disponibles as $id_fila  => $rel_columnas){
			//Obtengo los ids de las tablas padre e hija.
			$tabla_padre = $rel_columnas['padre_objeto'];
			$tabla_hija = $rel_columnas['hijo_objeto'];

			//Obtengo los datos originales de las columnas padre e hija.
			$original_padre = current($dep_viejas[$tabla_padre]->get_info_columnas(array('col_id' => $rel_columnas['padre_clave'])));
			$original_hijo = current($dep_viejas[$tabla_hija]->get_info_columnas(array('col_id' => $rel_columnas['hijo_clave'])));

			//Ahora busco los datos para la misma columna pero entre los nuevos
			$nuevo_padre = current($dep_nuevas[$tabla_padre]->get_info_columnas(array('columna' => $original_padre['columna'])));
			$nuevo_hijo = current($dep_nuevas[$tabla_hija]->get_info_columnas(array('columna' => $original_hijo['columna'])));
			//Genero la nueva fila con los datos modificados
			$nva_fila = array('padre_objeto' => $nuevo_padre['objeto'], 'padre_clave' => $nuevo_padre['col_id'], 'hijo_objeto' => $nuevo_hijo['objeto'],  'hijo_clave' => $nuevo_hijo['col_id']);
			$tabla_dr->modificar_fila($id_fila, $nva_fila);
		}
	}
}
?>