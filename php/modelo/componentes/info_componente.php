<?
require_once("interfaces.php");
require_once("nucleo/lib/manejador_archivos.php");
require_once("nucleo/lib/reflexion/clase_php.php");
require_once('modelo/consultas/dao_editores.php');

class info_componente implements recorrible_como_arbol, meta_clase
{
	protected $datos;
	protected $consumidor = null;				//elemento_toba que consume el elemento
	protected $datos_consumidor = null;			//Rol que cumple elemento en el consumidor
	protected $subelementos = array();
	protected $proyecto;
	protected $id;
	protected $carga_profundidad;
	protected $info_extra = "";
	
	function __construct( $datos, $carga_profundidad=true)
	{
		$this->carga_profundidad = $carga_profundidad;
		$this->datos = $datos;
		$this->id = $this->datos['info']['objeto'];
		$this->proyecto = $this->datos['info']['proyecto'];
		if ($this->carga_profundidad) {
			$this->cargar_dependencias();
		}
	}

	function cargar_dependencias()
	{
		//Si hay objetos asociados...
		if(	isset($this->datos['info_dependencias']) && 
			count($this->datos['info_dependencias']) > 0 )	{
			for ( $a=0; $a<count($this->datos['info_dependencias']); $a++) {
				$clave['proyecto'] = $this->datos['info_dependencias'][$a]['proyecto'];
				$clave['componente'] = $this->datos['info_dependencias'][$a]['objeto'];
				$this->subelementos[$a]= constructor_toba::get_info( $clave );
				$this->subelementos[$a]->set_consumidor($this, $this->datos['info_dependencias'][$a] );
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
		$editor_item = $this->datos['info']['clase_editor_item'];
		$editor_proyecto = $this->datos['info']['clase_editor_proyecto'];
		return toba::get_vinculador()->generar_solicitud( $editor_proyecto, $editor_item, $this->acceso_zona($parametros),
															false, false, null, true, 'central');
	}

	function get_archivo_subclase()
	{
		if (isset($this->datos['info']['subclase_archivo'])) {
			return $this->datos['info']['subclase_archivo'];
		}
		return null;		
	}
	
	/**
	 * Duplica un objeto y sus dependencias recursivamente
	 *
	 * @param array $nuevos_datos Datos a modificar en la base del objeto. Para anexar algo al nombre se utiliza el campo 'anexo_nombre'
	 * @param boolean/string $dir_subclases Si el componente tiene subclases clona los archivos, en caso afirmativo indicar la ruta destino (relativa)
	 * @param boolean $con_transaccion	Indica si la clonación se debe incluír en una transaccion
	 * @return array Clave del objeto que resulta del clonado
	 * @todo El path absoluto de los archivos clonados se basa en la presuncion de que es $toba_dir/proyectos/$proyecto, esto seguramente cambia
	 */
	function clonar($nuevos_datos, $dir_subclases=false, $con_transaccion = true)
	{
		//Se busca el id del datos_relacion de la clase
		$id_dr = dao_editores::get_dr_de_clase($this->datos['info']['clase']);
		
		//Se construye el objeto datos_relacion
		$componente = array('proyecto' => $id_dr[0], 'componente' => $id_dr[1]);
		$dr = constructor_toba::get_runtime($componente);
		$dr->conectar_fuente();
		$dr->configuracion();
		
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
			$dr->get_persistidor()->desactivar_transaccion();	
		}
		
		//--- Si tiene subclase, se copia el archivo y se cambia
		if ($dir_subclases !== false && isset($this->datos['info']['subclase_archivo'])) {
			$archivo = $this->datos['info']['subclase_archivo'];
			$nuevo_archivo = $dir_subclases."/".basename($archivo);
			$path_origen = toba_dir()."/proyectos/".editor::get_proyecto_cargado()."/php/";
			if (isset($nuevos_datos['proyecto'])) {
				$path_destino = toba_dir()."/proyectos/".$nuevos_datos['proyecto']."/php/";
			} else {
				$path_destino = $path_origen;	
			}
			$dr->tabla('base')->set_fila_columna_valor(0, 'subclase_archivo', $nuevo_archivo);
			//--- Si el dir. destino no existe, se lo crea
			if (!file_exists($path_destino.$dir_subclases)) {
				manejador_archivos::crear_arbol_directorios($path_destino.$dir_subclases);
			}
			copy($path_origen.$archivo, $path_destino.$nuevo_archivo);
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
		return $this->datos['info']['cant_dependencias'] == 0;
	}
	
	function tiene_propiedades()
	{
		return false;
	}
	
	function get_nombre_corto()
	{
		$nombre_objeto = $this->datos['info']['nombre'];
		if ($this->tiene_consumidor())
			$nombre = $this->rol_en_consumidor();
		else
			$nombre = $nombre_objeto; 
		return $nombre;
	}
	
	function get_nombre_largo()
	{
		$nombre_objeto = $this->datos['info']['nombre'];
		if ($this->tiene_consumidor())
			$nombre = "$nombre_objeto<br>Rol: ".$this->rol_en_consumidor();
		else
			$nombre = $nombre_objeto; 
		return $nombre;
	}
	
	function get_nombre()
	{
		return $this->datos['info']['nombre'];	
	}
	
	function get_iconos()
	{
		$clase_corto = substr($this->datos['info']['clase'], 7);		
		$iconos = array();
		$iconos[] = array(
				'imagen' => recurso::imagen_apl($this->datos['info']['clase_icono'], false),
				'ayuda' => "Objeto [wiki:Referencia/Objetos/$clase_corto $clase_corto]"
			);	
		if(isset($this->datos['info']['instanciador_item'])) {
			$iconos[] = array(
				'imagen' => recurso::imagen_apl("items/simular.gif", false),
				'ayuda' => "Simula la ejecución de este ".$this->datos_clase['clase'],
				'vinculo' => toba::get_vinculador()->generar_solicitud($this->datos['info']['clase_instanciador_proyecto'],
																		$this->datos['info']['clase_instanciador_item'],
																		$this->acceso_zona(),
																		false, false, null, true, "central")
			);	
		}
		return $iconos;
	}
	
	function get_utilerias()
	{
		$iconos = array();
		if (isset($this->datos['info']['subclase_archivo'])) {
			$parametros = $this->acceso_zona();
			$opciones = array('servicio' => 'ejecutar', 'zona' => true, 'celda_memoria' => 'ajax');
			$vinculo = toba::get_vinculador()->crear_vinculo("admin","/admin/objetos/php", $parametros, $opciones);
			$js = "toba.comunicar_vinculo('$vinculo')";
			$iconos[] = array(
				'imagen' => recurso::imagen_apl('reflexion/abrir.gif', false),
				'ayuda' => 'Abrir la [wiki:Referencia/Objetos/Extension extensión PHP] en el editor del escritorio.' .
						   '<br>Ver [wiki:Referencia/AbrirPhp Configuración]',
				'vinculo' => "javascript: $js;",
				'target' => '',
				'plegado' => false
			);
			$iconos[] = array(
				'imagen' => recurso::imagen_apl('php.gif', false),
				'ayuda' => 'Ver detalles de la [wiki:Referencia/Objetos/Extension extensión PHP]',
				'vinculo' => toba::get_vinculador()->generar_solicitud('admin','/admin/objetos/php', $this->acceso_zona(),
																		false, false, null, true, 'central'),
				'plegado' => true
			);
		}
		if (isset($this->datos['info']['clase_editor_proyecto'])) {
			$ayuda = null;
			if (in_array($this->datos['info']['clase'], dao_editores::get_clases_validas())) {
				require_once("datos_editores.php");
				$metodo = "get_pantallas_".$this->datos['info']['clase'];
				$pantallas = call_user_func(array("datos_editores", $metodo));
				//-- Se incluye un vinculo a cada pantalla encontrada
				$ayuda = "<div style=float:right>";
				foreach ($pantallas as $pantalla) {
					$img = ($pantalla['imagen'] != '') ? $pantalla['imagen'] : "objetos/fantasma.gif";
					$vinculo = $this->vinculo_editor(array('etapa' => $pantalla['identificador']));
					$ayuda .= '<a href='.$vinculo.' target='.apex_frame_centro.
								" title='".$pantalla['etiqueta']."'>".
								recurso::imagen_apl($img, true).
								'</a> ';
				}
				$ayuda .= "</div>";
				$ayuda = str_replace("'", "\\'", $ayuda);
			}
			$iconos[] = array(
				'imagen' => recurso::imagen_apl("objetos/editar.gif", false),
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
	//-- EVENTOS
	//---------------------------------------------------------------------

	function eventos_predefinidos()
	{
		return array();
	}	

	//---------------------------------------------------------------------	
	//-- METACLASE
	//---------------------------------------------------------------------

	//---- ANALISIS de EVENTOS

	function es_evento($metodo)
	{
		return (ereg("^evt(.*)", $metodo)); //evt seguido de cualquier cosa	
	}	

	function es_evento_valido($metodo)
	{
		if (ereg("^evt__(.*)", $metodo))
			return true; //evt__ seguido de cualquier cosa
		foreach ($this->subelementos as $elemento) {
			if ($elemento->es_evento_valido($metodo))
				return true;
		}	
		return false;
	}
	
	function es_evento_predefinido($metodo)
	{
		if (! $this->es_evento_valido($metodo))
			return false;
	
		if ($this->tiene_consumidor()) {
			//Debe buscar cosas de tipo 'evt__id__evento'?
			$id = $this->rol_en_consumidor();
			ereg("^evt__".$id."__(.*)", $metodo, $detalle);
			if (count($detalle) == 2 && in_array($detalle[1], $this->eventos_predefinidos()))
				return true;
		} else {
			//Debe buscar cosas de tipo 'evt__evento'		
			ereg("^evt__(.*)", $metodo, $detalle);
			if (count($detalle) == 2 && in_array($detalle[1], $this->eventos_predefinidos()))
				return true;		
		}
		//Los hijos lo tienen?
		foreach ($this->subelementos as $elemento) {
			if ($elemento->es_evento_predefinido($metodo))
				return true;
		}
		return false;
	}

	function es_evento_sospechoso($metodo)
	{
		//Busca cosas como evt___
		if (ereg("^evt___(.*)", $metodo)) {	
			return true;
		}	
		if ($this->tiene_consumidor()) {
			$id = $this->rol_en_consumidor();

			//Busca cosas como evt__id_evento
			ereg("^evt__".$id."_([^_].*)", $metodo, $detalle);
			if (count($detalle) == 2 && in_array($detalle[1], $this->eventos_predefinidos()))
				return true;
				
			//Busca cosas como evt__id___evento
			ereg("^evt__".$id."__[_*](.*)", $metodo, $detalle);
			if (count($detalle) == 2)
				return true;			
		}
		foreach ($this->subelementos as $elemento) {
			if ($elemento->es_evento_sospechoso($metodo))
				return true;
		}			
		return false;		
	}
	
	//--- GENERACION de PHP ---
	
	function generar_cuerpo_clase($opciones)
	{
		$this->set_nivel_comentarios($opciones['nivel_comentarios']);
		$cuerpo = '';
		if ($opciones['basicos']) {
			foreach ($this->generar_metodos() as $metodo_basico) {
				$cuerpo .= $metodo_basico."\n";
			}
		}
		return $cuerpo;
	}

	public function generar_metodos()
	{
		return array();
	}
	
	//----  FILTRO de COMENTARIOS  --------------

	public function set_nivel_comentarios($nivel)
	{
		$this->nivel_comentarios = $nivel;	
		foreach ($this->subelementos as $elemento) {
			$elemento->set_nivel_comentarios($nivel);
		}					
	}
	
	function filtrar_comentarios($metodos)
	//Elimina aquellos niveles de comentarios superiores al dado
	//nivel 0: ninguno
	//nivel 1: recomendados
	//nivel 2: explicativos
	//nivel 3: charlatanes
	{
		if (is_array($metodos)) {
			$nuevos = array();
			foreach ($metodos as $metodo) {
				$nuevos[] = $this->filtrar_comentarios_metodo($metodo);
			}
			return $nuevos;
		} else { //es un único metodo
			return $this->filtrar_comentarios_metodo($metodos);
		}
	}
	
	function filtrar_comentarios_metodo($metodo)
	{
		$nivel_maximo = 3;
		//Sacar las marcas de los inferiores
		for ($i = 0; $i <= $this->nivel_comentarios; $i++) {
			$metodo = str_replace("!#c$i", "", $metodo);
		}
		$lineas = '';
		foreach( explode("\n", $metodo) as $linea) {
			//Eliminar las lineas donde estan los superiores
			$eliminada = false;
			for ($i = $this->nivel_comentarios+1; $i<=$nivel_maximo; $i++) {
				if (strpos($linea, "!#c$i") !== false)
					$eliminada = true;
			}
			if (!$eliminada)
				$lineas .= $linea."\n";
		}
		//Se elimina un salto
		$lineas = substr($lineas, 0, -1);
		return $lineas;
	}
}
?>