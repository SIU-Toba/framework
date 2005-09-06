<?
require_once("elemento.php");
require_once("nucleo/lib/manejador_archivos.php");

class elemento_objeto extends elemento implements recorrible_como_arbol
{
	
	protected $datos_clase;		//Información relacionada con la clase del objeto
	
	function __construct()
	{
		$this->tipo = "objeto";	
		parent::__construct();
	}
	
	function set_datos_clase($datos_clase)
	{
		$this->datos_clase = $datos_clase;
	}
		
	function cargar_db($proyecto, $elemento)
	//Amplio la definicion de las tablas
	{
		//busco las tablas que dependen del tipo de clase
		$sql = "SELECT c.plan_dump_objeto as plan
				FROM apex_clase c,
				apex_objeto o
				WHERE (o.clase_proyecto = c.proyecto)
				AND (o.clase = c.clase)
				AND (o.objeto = '$elemento')
				AND (o.proyecto = '$proyecto')";
		$temp = consultar_fuente($sql,"instancia",null,true);
		$plan_dumpeo = parsear_propiedades( $temp[0]['plan'] );
		//Las agrego en la lista de tablas a dumpear
		$indice = count($this->tablas);
		foreach($plan_dumpeo as $tabla => $clave)
		{
			$temp['tabla'] = $tabla;
			//Parche para que funcione la tabla apex_objeto_eventos
			if (($clave != 'objeto') || (get_class($this)=="elemento_objeto_datos_tabla") )
				$temp['columna_clave_proyecto'] = $clave . "_proyecto";
			else
				$temp['columna_clave_proyecto'] = "proyecto";
			$temp['columna_clave'] = $clave;
			
			//Parche para que funcione la tabla apex_objeto_eventos			
			if ($tabla != 'apex_objeto_eventos')
				$temp['obligatoria'] = "1";
			else
				$temp['obligatoria'] = "0";
			$this->tablas[$indice]=$temp;
			$indice++;
		}
		//Llamo al padre para que cargue los datos
		parent::cargar_db($proyecto, $elemento);
	}

	function cargar_db_subelementos()
	{
		//Si hay objetos asociados...
		if(isset($this->datos['apex_objeto_dependencias']))	{
			for($a=0;$a<count($this->datos['apex_objeto_dependencias']);$a++) {
				$proyecto = $this->datos['apex_objeto_dependencias'][$a]['proyecto'];
				$objeto = $this->datos['apex_objeto_dependencias'][$a]['objeto_proveedor'];
				$this->subelementos[$a]= $this->construir_objeto($proyecto, $objeto);
				$this->subelementos[$a]->set_consumidor($this, $this->datos['apex_objeto_dependencias'][$a]);				
			}
		}
	}
	
	//-------------------------------------------
	
	function exportar_sql()
	{
		$cabecera = "-- Exportacion: ". date("d/M/Y") . "\n";
		$clase = $this->datos['apex_objeto'][0]['clase'];
		$dbt = call_user_func(array("toba_dbt",$clase));
		$dbt->cargar(array( "proyecto"=>$this->proyecto, "objeto"=>$this->id));
		$sql = $dbt->get_sql_inserts();
		$data = $cabecera . implode("\n",$sql);
		//ATENCION: El path se debe diferenciar por proyecto
		$path = toba::get_hilo()->obtener_proyecto_path() . "/sql/exportacion/$clase/{$this->id}.sql";
		manejador_archivos::crear_archivo_con_datos($path, $data);
	}
	

	//ATENCION: Ahora estan como variables locales, sacar los metodos
	function id_objeto()
	{
		return $this->datos['apex_objeto'][0]['objeto'];
	}
	
	function id_proyecto()
	{
		return $this->datos['apex_objeto'][0]['proyecto'];	
	}
	
	function rol_en_consumidor()
	{
		return $this->rol_en_consumidor['identificador'];
	}
	
	//---- Recorrido como arbol
	function hijos()
	{
		return $this->subelementos;
	}
	
	function es_hoja()
	{
		return (count($this->subelementos) == 0);
	}
	
	function tiene_propiedades()
	{
		return false;
	}
	
	function nombre_corto()
	{
		$nombre_objeto = $this->datos['apex_objeto'][0]['nombre'];
		if (isset($this->rol_en_consumidor['identificador']))
			$nombre = $this->rol_en_consumidor['identificador'];
		else
			$nombre = $nombre_objeto; 
		return $nombre;
	}
	
	function nombre_largo()
	{
		$nombre_objeto = $this->datos['apex_objeto'][0]['nombre'];
		if (isset($this->rol_en_consumidor['identificador']))
			$nombre = "$nombre_objeto\nRol: ".$this->rol_en_consumidor['identificador'];
		else
			$nombre = $nombre_objeto; 
		return $nombre;
	}
	
	function id()
	{
		return $this->datos['apex_objeto'][0]['objeto'];	
	}
	
	function iconos()
	{
		$iconos = array();
		$iconos[] = array(
			'imagen' => recurso::imagen_apl($this->datos_clase['icono'], false),
			'ayuda' => $this->datos_clase['descripcion_corta'],
			);	

		return $iconos;
	}
	
	function utilerias()
	{
		$iconos = array();
		$param_editores = array(apex_hilo_qs_zona=>$this->id_proyecto().apex_qs_separador.$this->id_objeto());
		if (isset($this->datos['apex_objeto'][0]["subclase_archivo"])) {
			$param_abrir_php = $param_editores;
			$param_abrir_php['evento'] = "abrir";
			$iconos[] = array(
				'imagen' => recurso::imagen_apl("reflexion/abrir.gif", false),
				'ayuda' => "Abrir la [wiki:Referencia/Objetos/Extension#Abrirlaextensi%C3%B3n extensión PHP] en el editor del sistema.", 
				'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/objetos/php", $param_abrir_php,
				false, false, null, true)
			);
			$iconos[] = array(
				'imagen' => recurso::imagen_apl("php.gif", false),
				'ayuda' => "Ver detalles de la extensión",
				'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/objetos/php", $param_editores,
																		false, false, null, true)
			);
		}		
		if(isset($this->datos_clase["editor_proyecto"])) {
			$iconos[] = array(
				'imagen' => recurso::imagen_apl("objetos/editar.gif", false),
				'ayuda' => "Editar propiedades del OBJETO",
				'vinculo' => toba::get_vinculador()->generar_solicitud($this->datos_clase["editor_proyecto"],
																		$this->datos_clase["editor_item"], $param_editores,
																		false, false, null, true)
			);
		}

		return $iconos;	
	}	
	
	
	//---- Manejo de eventos
	function es_evento($metodo)
	{
		return (ereg("^evt(.*)", $metodo)); //evt seguido de cualquier cosa	
	}	
	
	protected function hay_evento($nombre)
	{
		foreach ($this->datos['apex_objeto_eventos'] as $evento) {
			if ($evento['identificador'] == $nombre) {
				return true;
			}
		}
		return false;	
	}	
	
	function eventos_predefinidos()
	{
		return array();
	}	
	
	function es_evento_predefinido($metodo)
	{
		if (! $this->es_evento_valido($metodo))
			return false;
	
		if (isset($this->rol_en_consumidor['identificador'])) {
			//Debe buscar cosas de tipo 'evt__id__evento'?
			$id = $this->rol_en_consumidor['identificador'];
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

	function es_evento_sospechoso($metodo)
	{
		//Busca cosas como evt___
		if (ereg("^evt___(.*)", $metodo)) {	
			return true;
		}	
		if (isset($this->rol_en_consumidor['identificador'])) {
			$id = $this->rol_en_consumidor['identificador'];

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
	
	//---- Generación de código	
	function set_nivel_comentarios($nivel)
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
	
	function generar_metodos_basicos()
	{
		$basicos = array();
		$basicos[] = "\t".
'function mantener_estado_sesion()
	!#c2//Declarar todas aquellas propiedades de la clase que se desean persistir automáticamente
	!#c2//entre los distintos pedidos de página en forma de variables de sesión.
	{
		$propiedades = parent::mantener_estado_sesion();
		!#c1//$propiedades[] = "nombre_de_la_propiedad_a_persistir";
		return $propiedades;
	}
';
		$basicos[] = "\t".
'function extender_objeto_js()
	!#c3//Se puede cambiar el comportamiento de una pantalla redefiniendo métodos en el javascript asociado a este objeto
	!#c2//La sintaxis para redefinir métodos javascript es:
	!#c2//	echo "{$this->objeto_js}.metodo = function(parametros) { cuerpo }";
	{
	}
';
		return $this->filtrar_comentarios($basicos);
	}
	
	function generar_constructor()
	{
		$constructor = 
'	function __construct($id)
	{
		parent::__construct($id);
	}
';
		return $this->filtrar_comentarios($constructor);
	}
	
	function generar_eventos($solo_basicos)
	{
		return array();
	}

}
?>