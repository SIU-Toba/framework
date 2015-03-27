<?php

abstract class toba_componente_info implements toba_nodo_arbol, toba_meta_clase
{
	protected $datos;
	protected $consumidor = null;				//elemento_toba que consume el elemento
	protected $datos_consumidor = null;			//Rol que cumple elemento en el consumidor
	protected $subelementos = array();
	protected $proyecto;
	protected $id;
	protected $carga_profundidad;
	protected $info_extra = "";
	protected $datos_resumidos;
	
	function __construct( $datos, $carga_profundidad=true, $datos_resumidos=false)
	{
		$this->carga_profundidad = $carga_profundidad;
		$this->datos = $datos;
		$this->datos_resumidos = $datos_resumidos;
		$this->id = $this->datos['_info']['objeto'];
		$this->proyecto = $this->datos['_info']['proyecto'];
		if ($this->carga_profundidad) {
			$this->cargar_dependencias();
		}
	}

	function cargar_dependencias()
	{
		//Si hay objetos asociados...
		if(	isset($this->datos['_info_dependencias']) && 
			count($this->datos['_info_dependencias']) > 0 )	{
			for ( $a=0; $a<count($this->datos['_info_dependencias']); $a++) {
				$clave['proyecto'] = $this->datos['_info_dependencias'][$a]['proyecto'];
				$clave['componente'] = $this->datos['_info_dependencias'][$a]['objeto'];
				$tipo = $this->datos['_info_dependencias'][$a]['clase'];
				$this->subelementos[$a]= toba_constructor::get_info( $clave, $tipo, $this->carga_profundidad, null, true, $this->datos_resumidos);
				$this->subelementos[$a]->set_consumidor($this, $this->datos['_info_dependencias'][$a] );
			}
		}
	}

	function set_consumidor($consumidor, $datos_consumidor)
	{
		$this->consumidor = $consumidor;
		$this->datos_consumidor = $datos_consumidor;
	}

	function tiene_consumidor()
	{
		return isset($this->datos_consumidor['identificador']);
	}
	
	function rol_en_consumidor()
	{
		return $this->datos_consumidor['identificador'];
	}
	
	function get_metadatos_subcomponente()
	{
		return array();
	}

	function acceso_zona($parametros = array())
	{
		$parametros[apex_hilo_qs_zona] = $this->proyecto . apex_qs_separador . $this->id;
		return $parametros;
	}

	function vinculo_editor($parametros = array())
	{
		$editor_item = $this->datos['_info']['clase_editor_item'];
		$editor_proyecto = $this->datos['_info']['clase_editor_proyecto'];
		return toba::vinculador()->get_url( $editor_proyecto, $editor_item, $this->acceso_zona($parametros),
										array(	'menu' => true,
										'celda_memoria' => 'central')
							);
	}

	function get_fuente_datos()
	{
		return $this->datos['_info']['fuente'];
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
		
		//--- Si tiene subclase, se copia el archivo y se cambia
		if ($dir_subclases !== false) {
			$proyecto_dest = isset($nuevos_datos['proyecto']) ? $nuevos_datos['proyecto'] : null;
			$this->clonar_subclase($dr, $dir_subclases, $proyecto_dest);
		}
		
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
		}
		//Se intenta acceder a las pantallas/db_registros para pasarle el nuevo punto de montaje
		if (isset($nuevos_datos['punto_montaje'])) {
			//Trato de setear el punto de montaje para las pantallas
			try {
				$dr->tabla('pantallas')->set_columna_valor('punto_montaje', $nuevos_datos['punto_montaje']);
			} catch (Exception $e) {
				
			}
			//Trato de setear el punto de montaje para las propiedades basicas
			try {
				$dr->tabla('prop_basicas')->set_columna_valor('punto_montaje', $nuevos_datos['punto_montaje']);
			} catch (Exception $e) {
				
			}
			//Trato de setear el punto de montaje para los efs
			try {
				$dr->tabla('efs')->set_columna_valor('punto_montaje', $nuevos_datos['punto_montaje']);
			} catch (Exception $e) {
				
			}
			//Trato de setear el punto de montaje para las columnas del filtro
			try {
				$dr->tabla('cols')->set_columna_valor('punto_montaje', $nuevos_datos['punto_montaje']);
			} catch (Exception $e) {
				
			}
			//Trato de setear el punto de montaje para las columnas externas del datos tabla
			try {
				$dr->tabla('externas')->set_columna_valor('punto_montaje', $nuevos_datos['punto_montaje']);
			} catch (Exception $e) {
				
			}
		}
		$dr->sincronizar();
		
		//Se busca la clave del nuevo objeto
		$clave = $dr->tabla('base')->get_clave_valor(0);
		$clave['componente'] = $clave['objeto'];
		return $clave;
	}
	
	protected function clonar_subclase($dr, $dir_subclases, $proyecto_dest)
	{
		if (isset($this->datos['_info']['subclase_archivo'])) {
			$archivo = $this->datos['_info']['subclase_archivo'];
			$nuevo_archivo = $dir_subclases."/".basename($archivo);
			
			$id_pm_origen = $this->get_punto_montaje();						
			$id_pm_destino = $dr->tabla('base')->get_fila_columna(0, 'punto_montaje');							
			
			//Busco los directorios de copia utilizando los puntos de montaje
			$path_origen = $this->get_path_clonacion($id_pm_origen,$this->proyecto);
			$path_destino = $this->get_path_clonacion($id_pm_destino, $proyecto_dest, $path_origen);
		
			$dr->tabla('base')->set_fila_columna_valor(0, 'subclase_archivo', $nuevo_archivo);
			//--- Si el dir. destino no existe, se lo crea
			if (!file_exists($path_destino.$dir_subclases)) {
				toba_manejador_archivos::crear_arbol_directorios($path_destino.$dir_subclases);
			}
			if (! copy($path_origen.$archivo, $path_destino.$nuevo_archivo)) {
				throw new toba_error('No es posible copiar el archivo desde '.$path_origen.$archivo.' hacia '.$path_destino.$nuevo_archivo);
			}				
		}
	}

	protected function get_path_clonacion($id_punto, $proyecto, $path_default='')
	{
		$path_final = $path_default;
		$pm = toba_pms::instancia()->get_instancia_pm_proyecto($proyecto, $id_punto);		//Instancio el pm para el proyecto
		if (! is_null($pm)) {
			$path_final = $pm->get_path_absoluto(). '/';								//Si existe recupero el path al punto, sino uso el generico del proyecto
		} elseif (isset($proyecto)) {
			$path_final = toba::instancia()->get_path_proyecto($proyecto).'/php/';	
		}		
		return $path_final;		
	}
	
	//---------------------------------------------------------------------	
	//-- Recorrible como ARBOL
	//---------------------------------------------------------------------

	function get_id()
	{
		return $this->id;	
	}
	
	function get_hijos()
	{
		return $this->subelementos;
	}
	
	function get_padre()
	{
		return null;
	}
	
	function es_hoja()
	{
		return $this->datos['_info']['cant_dependencias'] == 0;
	}
	
	function tiene_propiedades()
	{
		return false;
	}
	
	function get_nombre_corto()
	{
		$nombre_objeto = $this->datos['_info']['nombre'];
		if ($this->tiene_consumidor())
			$nombre = $this->rol_en_consumidor();
		else
			$nombre = $nombre_objeto; 
		return $nombre;
	}
	
	function get_nombre_largo()
	{
		$nombre_objeto = $this->datos['_info']['nombre'];
		if ($this->tiene_consumidor())
			$nombre = "$nombre_objeto<br>Rol: ".$this->rol_en_consumidor();
		else
			$nombre = $nombre_objeto; 
		return $nombre;
	}
	
	function get_nombre()
	{
		return $this->datos['_info']['nombre'];	
	}
	
	function get_iconos()
	{
		$clase_corto = substr($this->datos['_info']['clase'], 5);
		$iconos = array();
		$iconos[] = array(
				'imagen' => toba_recurso::imagen_toba($this->datos['_info']['clase_icono'], false),
				'ayuda' => "Componente [wiki:Referencia/Objetos/$clase_corto $clase_corto]"
			);	
		return $iconos;
	}
	
	function get_utilerias()
	{
		$iconos = array();
		if (isset($this->datos['_info']['subclase_archivo'])) {
			// Administracion de la Subclase PHP}
			if (admin_util::existe_archivo_subclase($this->datos['_info']['subclase_archivo'], $this->datos['_info']['punto_montaje'])) {
				$iconos[] = $this->get_utileria_editor_abrir_php(array('proyecto'=>$this->proyecto, 'componente' =>$this->id ));
				$iconos[] = $this->get_utileria_editor_ver_php(array('proyecto'=>$this->proyecto, 'componente' =>$this->id ));
			} else {
				$iconos[] = $this->get_utileria_editor_ver_php(array('proyecto'=>$this->proyecto, 'componente' =>$this->id ), null, 'nucleo/php_inexistente.gif', false);
			}
		}
		/*
		// Instanciador
		if ( $this instanceof toba_ei_formulario_info || $this instanceof toba_ei_cuadro_info ) {
			$iconos[] = array(
				'imagen' => toba_recurso::imagen_toba("objetos/instanciar.gif", false),
				'ayuda' => 'Previsualizar el componente',
				'vinculo' => toba::vinculador()->generar_solicitud( toba_editor::get_id(), 3316, $this->acceso_zona(),
																		false, false, null, true, 'central')
			);
		}
		*/
		//Editor
		if (isset($this->datos['_info']['clase_editor_proyecto'])) {
			$ayuda = null;
			if (in_array($this->datos['_info']['clase'], toba_info_editores::get_lista_tipo_componentes())) {
				$metodo = "get_pantallas_".$this->datos['_info']['clase'];
				$pantallas = call_user_func(array("toba_datos_editores", $metodo));
				//-- Se incluye un vinculo a cada pantalla encontrada
				$ayuda = "<div class='editor-lista-vinculos'>";
				foreach ($pantallas as $pantalla) {
					$img = ($pantalla['imagen'] != '') ? $pantalla['imagen'] : "objetos/fantasma.gif";
					$origen = ($pantalla['imagen'] != '') ? $pantalla['imagen_recurso_origen'] : 'apex';
					$vinculo = $this->vinculo_editor(array('etapa' => $pantalla['identificador']));
					$tag_img = ($origen == 'apex') ? toba_recurso::imagen_toba($img, true) : toba_recurso::imagen_proyecto($img, true);
					$ayuda .= '<a href='.$vinculo.' target='.apex_frame_centro.
								" title='".$pantalla['etiqueta']."'>".
								$tag_img.
								'</a> ';
				}
				$ayuda .= "</div>";
				$ayuda = str_replace("'", "\\'", $ayuda);
			}
			$iconos[] = array(
				'imagen' => toba_recurso::imagen_toba("objetos/editar.gif", false),
				'ayuda' => $ayuda,
				'vinculo' => $this->vinculo_editor()
			);
		}
		return $iconos;	
	}

	function get_info_extra()
	{
		return $this->info_extra;	
	}
	
	function set_info_extra($info)
	{
		$this->info_extra .= $info;	
	}
	
	function tiene_hijos_cargados()
	{
		return !$this->es_hoja() && count($this->subelementos) != 0;
	}
	
	function contiene_objeto($id)
	{
		if ($id == $this->get_id()) {
			return true;	
		}
		foreach ($this->subelementos as $elem) {
			if ($elem->contiene_objeto($id)) {
				return true;
			}
		}
	}
	
	//---------------------------------------------------------------------
	// ACCESO al EDITOR PHP
	//---------------------------------------------------------------------

	static function get_utileria_editor_parametros($id_componente, $subcomponente=null)
	{
		$parametros[apex_hilo_qs_zona] = $id_componente['proyecto'] . apex_qs_separador . $id_componente['componente'];
		if($subcomponente) {
			$parametros['subcomponente'] = $subcomponente;
		}
		return $parametros;		
	}
	
	static function get_utileria_editor_abrir_php($id_componente, $subcomponente=null, $icono='reflexion/abrir.gif')
	{
		$parametros = self::get_utileria_editor_parametros($id_componente, $subcomponente);
		$opciones = array('servicio' => 'ejecutar', 'zona' => true, 'celda_memoria' => 'ajax', 'menu' => true);
		$vinculo = toba::vinculador()->get_url(toba_editor::get_id(),"3463", $parametros, $opciones);
		$js = "toba.comunicar_vinculo('$vinculo')";
		return array(
			'imagen' => toba_recurso::imagen_proyecto($icono, false),
			'ayuda' => 'Abrir la [wiki:Referencia/Objetos/Extension extensión PHP] en el editor del escritorio.' .
					   '<br>Ver [wiki:Referencia/AbrirPhp Configuración]',
			'vinculo' => "javascript: $js;",
			'js' => $js,
			'target' => '',
			'plegado' => false
		);
	}

	static function get_utileria_editor_ver_php($id_componente, $subcomponente=null, $icono = 'nucleo/php.gif', $plegado = true)
	{
		$parametros = self::get_utileria_editor_parametros($id_componente, $subcomponente);
		$opciones = array('zona' => true, 'celda_memoria' => 'central', 'menu' => true);//validar' => false,
		$vinculo = toba::vinculador()->get_url(toba_editor::get_id(),"3463", $parametros, $opciones);
		return array( 'imagen' => toba_recurso::imagen_toba($icono, false),
				'ayuda' => 'Ver o editar la [wiki:Referencia/Objetos/Extension extensión PHP]',
				'vinculo' => $vinculo,
				'plegado' => $plegado
		);		
	}

	//---------------------------------------------------------------------	
	//-- METACLASE
	//---------------------------------------------------------------------

	/**
	 * Retorna el nombre de la clase o la subclase asociada al componente
	 */
	function get_clase_nombre_final()
	{
		$nombre = $this->get_subclase_nombre();
		if ($nombre == '') {
			$nombre = $this->get_clase_extendida_nombre();
		}
		return $nombre;
	}
	
	function get_clase_nombre()
	{
		return  str_replace('objeto_', 'toba_', $this->datos['_info']['clase']);	// Se deja esta línea para que conserve el mismo comportamiento
	}
	
	function get_clase_extendida_nombre()
	{
		$id_proyecto = toba_contexto_info::get_proyecto();
		$id_instancia = toba::instancia()->get_id();
		$proyecto = toba_modelo_catalogo::instanciacion()->get_proyecto($id_instancia, $id_proyecto);
		
		if ($proyecto->tiene_clases_proyecto_extendidas()) {
			$replacement = $id_proyecto.'_pers_';
		} elseif ($proyecto->tiene_clases_toba_extendidas()) {
			$replacement = $id_proyecto.'_';
		} else {
			$replacement = 'toba_';
		}
		$aux = str_replace('objeto_', 'toba_', $this->datos['_info']['clase']);	// Se deja esta línea para que conserve el mismo comportamiento
		return str_replace('toba_', $replacement, $aux);		
	}

	function get_clase_archivo()
	{
		return $this->datos['_info']['clase_archivo'];	
	}
	
	function get_subclase_nombre()
	{
		return $this->datos['_info']['subclase'];
	}

	function get_subclase_archivo()
	{
		return $this->datos['_info']['subclase_archivo'];	
	}

	function get_punto_montaje()
	{
		return $this->datos['_info']['punto_montaje'];
	}

	function get_molde_vacio()
	{
		$molde = new toba_codigo_clase( $this->get_subclase_nombre(), $this->get_clase_extendida_nombre() );
		//-- Ini 
		$doc = 'Se ejecuta al inicio de todos los request en donde participa el componente';
		$metodo = new toba_codigo_metodo_php('ini', array(), array($doc));
		$metodo->set_doc($doc);
		$molde->agregar($metodo);		
		return $molde;
	}

	function get_nombre_instancia_abreviado()
	{
		return "componente";	
	}	
	
	function set_subclase($nombre, $archivo, $pm)
	{
		$db = toba_contexto_info::get_db();
		$nombre = $db->quote($nombre);
		$archivo = $db->quote($archivo);
		$pm = $db->quote($pm);
		$id = $db->quote($this->id);
		$sql = "
			UPDATE apex_objeto
			SET 
				subclase = $nombre,
				subclase_archivo = $archivo,
				punto_montaje = $pm
			WHERE
					proyecto = '{$this->proyecto}'
				AND	objeto = $id
		";
		toba::logger()->debug($sql);
		$db->ejecutar($sql);
	}
	
	function cambiar_clase_origen($nombre_clase)
	{
		$this->datos['_info']['clase'] = $nombre_clase;
	}
	
	//---------------------------------------------------------------------	
	//-- Preguntas sobre EVENTOS
	//---------------------------------------------------------------------

	function eventos_predefinidos()
	{
		return array();
	}	

	static function get_eventos_internos(toba_datos_relacion $dr)
	{
		$eventos = array();
		return $eventos;
	}
}
?>