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
				$this->subelementos[$a]= toba_constructor::get_info( $clave, $tipo, $this->carga_profundidad, null, false, $this->datos_resumidos );
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
		return toba::vinculador()->generar_solicitud( $editor_proyecto, $editor_item, $this->acceso_zona($parametros),
															false, false, null, true, 'central');
	}

	function get_archivo_subclase()
	{
		if (isset($this->datos['_info']['subclase_archivo'])) {
			return $this->datos['_info']['subclase_archivo'];
		}
		return null;		
	}
	
	/**
	 * Duplica un objeto y sus dependencias recursivamente
	 *
	 * @param array $nuevos_datos Datos a modificar en la base del objeto. Para anexar algo al nombre se utiliza el campo 'anexo_nombre'
	 * @param boolean/string $dir_subclases Si el componente tiene subclases clona los archivos, en caso afirmativo indicar la ruta destino (relativa)
	 * @param boolean $con_transaccion	Indica si la clonaci�n se debe inclu�r en una transaccion
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

		//Se le fuerza una inserci�n a los datos_tabla
		//Como la clave de los objetos son secuencias, esto garantiza claves nuevas
		$dr->forzar_insercion();
		if (!$con_transaccion) {
			$dr->get_persistidor()->desactivar_transaccion();	
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
			//-- SE CLONA
			$id_clon = $hijo->clonar($datos_objeto, $dir_subclases, $con_transaccion);
			//--- En el componente actual se reemplaza la dependencia por el clon
			$id_fila = $dr->tabla('dependencias')->get_id_fila_condicion(
								array('identificador' => $hijo->rol_en_consumidor()));
			$dr->tabla('dependencias')->modificar_fila(current($id_fila), 
								array('objeto_proveedor' => $id_clon['componente']));
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
			$path_origen = toba::instancia()->get_path_proyecto(toba_contexto_info::get_proyecto())."/php/";
			if (isset($proyecto_dest)) {
				$path_destino = toba::instancia()->get_path_proyecto($proyecto_dest)."/php/";
			} else {
				$path_destino = $path_origen;	
			}
			$dr->tabla('base')->set_fila_columna_valor(0, 'subclase_archivo', $nuevo_archivo);
			//--- Si el dir. destino no existe, se lo crea
			if (!file_exists($path_destino.$dir_subclases)) {
				toba_manejador_archivos::crear_arbol_directorios($path_destino.$dir_subclases);
			}
			copy($path_origen.$archivo, $path_destino.$nuevo_archivo);
		}
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
		$clase_corto = substr($this->datos['_info']['clase'], 7);		
		$iconos = array();
		$iconos[] = array(
				'imagen' => toba_recurso::imagen_toba($this->datos['_info']['clase_icono'], false),
				'ayuda' => "Objeto [wiki:Referencia/Objetos/$clase_corto $clase_corto]"
			);	
		return $iconos;
	}
	
	function get_utilerias()
	{
		$iconos = array();
		if (isset($this->datos['_info']['subclase_archivo'])) {
			// Administracion de la Subclase PHP}
			if (admin_util::existe_archivo_subclase($this->datos['_info']['subclase_archivo'])) {
				$iconos[] = $this->get_utileria_editor_abrir_php(array('proyecto'=>$this->proyecto, 'componente' =>$this->id ));
				$iconos[] = $this->get_utileria_editor_ver_php(array('proyecto'=>$this->proyecto, 'componente' =>$this->id ));
			} else {
				$iconos[] = $this->get_utileria_editor_ver_php(array('proyecto'=>$this->proyecto, 'componente' =>$this->id ), null, 'nucleo/php_inexistente.gif', false);
			}
		}
		// Instanciador
		if ( $this instanceof toba_ei_formulario_info || $this instanceof toba_ei_cuadro_info ) {
			$iconos[] = array(
				'imagen' => toba_recurso::imagen_toba("objetos/instanciar.gif", false),
				'ayuda' => 'Previsualizar el componente',
				'vinculo' => toba::vinculador()->generar_solicitud( toba_editor::get_id(), 3316, $this->acceso_zona(),
																		false, false, null, true, 'central')
			);
		}
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
		$vinculo = toba::vinculador()->crear_vinculo(toba_editor::get_id(),"/admin/objetos/php", $parametros, $opciones);
		$js = "toba.comunicar_vinculo('$vinculo')";
		return array(
			'imagen' => toba_recurso::imagen_proyecto($icono, false),
			'ayuda' => 'Abrir la [wiki:Referencia/Objetos/Extension extensi�n PHP] en el editor del escritorio.' .
					   '<br>Ver [wiki:Referencia/AbrirPhp Configuraci�n]',
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
		$vinculo = toba::vinculador()->crear_vinculo(toba_editor::get_id(),"/admin/objetos/php", $parametros, $opciones);
		return array( 'imagen' => toba_recurso::imagen_toba($icono, false),
				'ayuda' => 'Ver detalles de la [wiki:Referencia/Objetos/Extension extensi�n PHP]',
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
			$nombre = $this->get_clase_nombre();
		}
		return $nombre;
	}
	
	function get_clase_nombre()
	{
		return str_replace('objeto_', 'toba_', $this->datos['_info']['clase']);
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

	function get_molde_vacio()
	{
		return new toba_codigo_clase( $this->get_subclase_nombre(), $this->get_clase_nombre() );	
	}

	function get_nombre_instancia_abreviado()
	{
		return "componente";	
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