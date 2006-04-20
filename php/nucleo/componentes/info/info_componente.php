<?
require_once("interfaces.php");
require_once("nucleo/lib/manejador_archivos.php");
require_once("nucleo/lib/reflexion/clase_php.php");

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
			$vinculo = toba::get_vinculador()->crear_vinculo("toba","/admin/objetos/php", $parametros, $opciones);
			$js = "toba.comunicar_vinculo(\"$vinculo\")";
			$iconos[] = array(
				'imagen' => recurso::imagen_apl('reflexion/abrir.gif', false),
				'ayuda' => 'Abrir la [wiki:Referencia/Objetos/Extension extensión PHP] en el editor del escritorio.' .
						   '<br>Ver [wiki:Referencia/AbrirPhp Configuración]',
				'vinculo' => "javascript: $js;",
				'target' => '',
				'plegado' => true
			);
			$iconos[] = array(
				'imagen' => recurso::imagen_apl('php.gif', false),
				'ayuda' => 'Ver detalles de la [wiki:Referencia/Objetos/Extension extensión PHP]',
				'vinculo' => toba::get_vinculador()->generar_solicitud('toba','/admin/objetos/php', $this->acceso_zona(),
																		false, false, null, true, 'central'),
				'plegado' => true																		
			);
		}
		if (isset($this->datos['info_eventos'])) {
			$iconos[] = array(
				'imagen' => recurso::imagen_apl('reflexion/evento.gif', false),
				'ayuda' => 'Editar los [wiki:Referencia/Eventos eventos del objeto].',
				'vinculo' => $this->vinculo_editor(array('etapa' => 3)),
				'plegado' => true
			);
		}
		if(isset($this->datos['info']['clase_editor_proyecto'])) {
			$iconos[] = array(
				'imagen' => recurso::imagen_apl("objetos/editar.gif", false),
				'ayuda' => "Editar propiedades del OBJETO",
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