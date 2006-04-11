<?
require_once("interfaces.php");

class info_item implements recorrible_como_arbol
{
	protected $subelementos = array();
	protected $proyecto;
	protected $id;
	protected $datos;
	protected $nivel;					//Nivel del item en el arbol de items
	protected $grupos_acceso;			//Grupos que pueden acceder al item
	protected $camino;					//Arreglo de carpetas que componen la rama en donde pertenece el item
	protected $items_hijos=array();		//Arreglo de hijos 
	protected $padre=null;				//Objeto item padre
	protected $info_extra = '';
	protected $carga_profundidad;
	
	
	function __construct( $datos, $carga_profundidad=true)
	{
		$this->datos = $datos;	
		$this->id = $this->datos['info']['item'];
		$this->proyecto = $this->datos['info']['item_proyecto'];
		$this->carga_profundidad = $carga_profundidad;
		if ($this->carga_profundidad) {
			$this->cargar_dependencias();
		}
		if($this->es_de_menu()) {
			$this->info_extra .= recurso::imagen_apl("items/menu.gif",true)." - Está incluído en el MENU";
		}
			
	}

	function cargar_dependencias()
	{
		//Si hay objetos asociados...
		if (count($this->datos['info_objetos'])>0)	{
			for ($a=0; $a<count($this->datos['info_objetos']); $a++) {
				$clave['proyecto'] = $this->datos['info_objetos'][$a]['objeto_proyecto'];
				$clave['componente'] = $this->datos['info_objetos'][$a]['objeto'];
				$this->subelementos[$a] = constructor_toba::get_info( $clave );
			}
		}
	}

	/**
	*	Crea una rama de items comenzando por la raiz
	*	Al asumir que los niveles son pocos se hace una consulta por nivel
	*	Quedan cargado en el objeto los ancestros de la rama
	*/
	function cargar_rama()
	{
		$item_ancestro = $this;
		while ($item_ancestro->get_id() != null) {
			$id = array('componente' => $item_ancestro->get_id_padre(), 
						'proyecto' => $item_ancestro->get_proyecto());
			$nodo = constructor_toba::get_info($id, 'item', false);
			$item_ancestro->set_padre($nodo);
			$item_ancestro = $nodo;
		}
	}	
	
	//---------------------------------------------------------------------	
	// Preguntas 
	//---------------------------------------------------------------------

	function es_carpeta() 
	{ 
		return $this->datos['info']['carpeta']; 
	}
	
	function es_de_menu() {	
		if ($this->id != '') {
			return $this->datos['info']['menu'];
		} else {
			return true;	
		}
	}	
	
	function es_publico() { return $this->datos['info']['publico']; } 

	function tipo_solicitud() { return $this->datos['info']['solicitud_tipo']; }
	
	function vinculo_editor()
	{
		if ($this->es_carpeta())
			$item_editor = "/admin/items/carpeta_propiedades";
		else
			$item_editor = "/admin/items/editor_items";		
		return toba::get_vinculador()->generar_solicitud("toba", $item_editor,
						array( apex_hilo_qs_zona => $this->proyecto .apex_qs_separador. $this->id),
						false, false, null, true, "central");
	}
	
	/**
	 * Recorre el item en profundidad buscando el objeto pasado por parametro
	 * La ejecución de este método es muy costosa ya que hace una query por objeto
	 */	
	function contiene_objeto($id)
	{
		foreach ($this->subelementos as $elem) {
			if ($elem->contiene_objeto($id)) {
				return true;
			}
		}
	}
	
	function get_id_padre() {	return $this->datos['info']['item_padre']; }	

	function get_nivel_prof() {	return $this->nivel; }
	
	function get_camino() { return $this->camino; }
	
	function get_nombre() { return $this->datos['info']['item_nombre']; }
	
	function get_proyecto() { return $this->datos['info']['item_proyecto']; }
	
	function crono() 
	{ 
		if (isset($this->datos['crono']))
			return $this->datos['crono'] == 1; 
	}
	
	function cant_objetos() { return $this->datos['info']['cant_dependencias']; }
	
	function registra_solicitud()
	{ 
		if (isset($this->datos['info']['registrar']))
			return $this->datos['info']["registrar"]; 
	}
	
	function propietario() { return $this->datos['info']['usuario']; }

	function grupos_acceso()
	{
		if (!isset($this->grupos_acceso)) {
			$sql = "
				SELECT g.usuario_grupo_acc
				FROM
					apex_usuario_grupo_acc_item g
				WHERE
					g.item = '{$this->get_id()}' AND
					g.proyecto = '{$this->get_proyecto()}'
			";
			$rs = toba::get_db('instancia')->consultar($sql);
			if (empty($rs))
				$this->grupos_acceso = array();
			else
				$this->grupos_acceso =  aplanar_matriz($rs);
		}
		return $this->grupos_acceso;
	}
		
	function grupo_tiene_permiso($grupo)
	{
		return in_array($grupo, $this->grupos_acceso());
	}
	
	function es_buffer() 
	{ 
		return !($this->datos['info']['act_buf']== 0 && $this->datos['info']['act_buf_p']=="toba");
	}
	
	function es_patron()
	//--- Es un PATRON?? El patron <toba,especifico> representa la ausencia de PATRON	
	{
		return !($this->datos['info']['act_pat']=="especifico" && $this->datos['info']['act_pat_p']=="toba");
	}
	
	function es_accion()
	{
		return !$this->es_buffer() && !$this->es_patron();
	}

	function es_hijo_de($carpeta)
	{
		if ($this->get_id() == '')
			return false;
		return $this->get_id_padre() == $carpeta->get_id();
	}
	
	function es_de_consola()
	{
		return $this->tipo_solicitud() == 'consola';	
	}
	
	/**
	 * Un item inaccesible es uno en el que:
	 * 	- Esta marcado por menu pero alguno de sus padres no lo esta
	 * 	- No tiene permisos y no es ni publico ni de consola
	 */	
	function es_inaccesible()
	{
		$grupos = $this->grupos_acceso();
		//--- Si no es de consola ni publico y no tiene grupos de acceso, no hay forma de accederlo
		$sin_grupo = (!$this->es_de_consola() && !$this->es_publico() && count($this->grupos_acceso()) == 0);
		if ($sin_grupo) {
			if (!$this->es_carpeta()) {
				$this->info_extra .= "El ítem es inaccesible porque no hay grupo de acceso que tenga permiso de accederlo.";
			}
			return true;
		}
		//--- Si es de menu y algun padre no lo es, no se va a mostrar en el mismo
		$es_de_menu = $this->es_de_menu();
		$padre = $this->get_padre();
		while ($padre != null) {
			if ($es_de_menu && ! $padre->es_de_menu()) {
				$this->info_extra .= "El ítem es inaccesible por menú porque la carpeta `{$padre->get_nombre()}` no se muestra en el mismo.";
				return true;
				break;
			}
			$padre = $padre->get_padre();
		}
		return false;
	}
	
	
	//---------------------------------------------------------------------	
	//-- Recorrible como ARBOL
	//---------------------------------------------------------------------
	
	function get_id()
	{
		return $this->id;	
	}
	
	function es_hoja()
	{
		return $this->datos['info']['cant_items_hijos'] == 0 && $this->cant_objetos() == 0;
	}

	function tiene_propiedades()
	{
		return true;
	}	
	
	function get_nombre_corto()
	{
		return $this->get_nombre();
	}
	
	function get_nombre_largo()
	{
		return $this->get_nombre();
	}
	
	function get_iconos()
	{
		$iconos = array();
		if ($this->es_carpeta()) {
			$iconos[] = array(
				'imagen' => recurso::imagen_apl("items/carpeta.gif", false),
				'ayuda' => "Carpeta que contiene operaciones.",
				);
		} else {
			$iconos[] = array(
				'imagen' => recurso::imagen_apl("items/item.gif", false),
				'ayuda' => "[wiki:Referencia/Item Item] que representa una operación del proyecto.",
				);
				
			if ($this->tipo_solicitud() == "consola") {
				$iconos[] = array(
								'imagen' => recurso::imagen_apl("solic_consola.gif",false),
								'ayuda' => 'Solicitud de Consola'
							);
			} elseif($this->tipo_solicitud()=="wddx") {
				$iconos[] = array(
								'imagen' => recurso::imagen_apl("solic_wddx.gif",false),
								'ayuda' => 'Solicitud WDDX'
							);
			}
			if($this->crono()){		
				$iconos[] = array(
					'imagen' => recurso::imagen_apl("cronometro.gif", false),
					'ayuda'=> "El ITEM se cronometra"
				);			
			}
			if($this->es_publico()){
				$iconos[] = array(
					'imagen' => recurso::imagen_apl("usuarios/usuario.gif", false),
					'ayuda'=> "ITEM público"
				);				
			}
			if($this->registra_solicitud() == 1){
				$iconos[] = array(
					'imagen' => recurso::imagen_apl("solicitudes.gif", false),
					'ayuda'=> "El ITEM se registra"
				);				
			}
		}
		return $iconos;
	}	
	
	function get_utilerias()
	{
/*		
		$utilerias = array();
		if ($this->es_carpeta()) {	
		} else { //Es un item común
			if ($this->es_de_menu()) {
				$utilerias[] = array(
					'imagen' => recurso::imagen_apl("items/menu.gif", false),
					'ayuda'=> "El ITEM esta incluido en el MENU del PROYECTO"
				);	
			}
		}
	
*/
		$utilerias = array();
		if ($this->es_carpeta()) {	
			// Ordenamiento, Nueva carpeta, nuevo item
/*			
			$utilerias[] = array(
				'imagen' => recurso::imagen_apl("items/carpeta_ordenar.gif", false),
				'ayuda'=> "Ordena alfabéticamente los items incluídos en esta CARPETA",
				'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/items/carpeta_ordenar", 
								array("padre_p"=>$this->get_proyecto(), "padre_i"=>$this->get_id()) )
			);
*/
			$utilerias[] = array(
				'imagen' => recurso::imagen_apl("items/carpeta_nuevo.gif", false),
				'ayuda'=> "Crear SUBCARPETA en esta rama del CATALOGO",
				'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/items/carpeta_propiedades", 
								array("padre_p"=>$this->get_proyecto(), "padre_i"=>$this->get_id()),false,false,null,true, "central" )
			);
			$utilerias[] = array(
				'imagen' => recurso::imagen_apl("items/item_nuevo.gif", false),
				'ayuda'=> "Crear ITEM hijo en esta rama del CATALOGO",
				'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/items/editor_items", 
								array("padre_p"=>$this->get_proyecto(), "padre_i"=>$this->get_id()),false,false,null,true, "central" )
			);			

		} else { //Es un item común
			$utilerias[] = array(
				'imagen' => recurso::imagen_apl("objetos/objeto_nuevo.gif", false),
				'ayuda' => "Crear un objeto asociado al item",
				'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/objetos_toba/crear",
									array('destino_tipo' =>'item', 
											'destino_proyecto' => $this->proyecto,
											'destino_id' => $this->id ),
											false, false, null, true, "central")
			);
		}
		if (!$this->es_carpeta() && 
				$this->tipo_solicitud() != 'consola' &&
				$this->tipo_solicitud() !="wddx") {
			$utilerias[] = array(
							'imagen' => recurso::imagen_apl("items/instanciar.gif",false),
							'ayuda' => 'Ejecutar el ITEM',
							'vinculo' => toba::get_vinculador()->generar_solicitud($this->get_proyecto(), $this->get_id(), 
											null,false,false,null,true, "central")
						);			
			
		}		
		$utilerias[] = array(
			'imagen' => recurso::imagen_apl("objetos/editar.gif", false),
			'ayuda' => "Editar propiedades del ITEM",
			'vinculo' => $this->vinculo_editor()
		);				
		return $utilerias;
	}	
	
	function agregar_hijo($item)
	{
		$this->items_hijos[$item->get_id()] = $item;
	}
		
	function quitar_hijo($item)
	{
		unset($this->items_hijos[$item->get_id()]);
	}	
	
	function set_padre($carpeta)
	{
		$this->padre = $carpeta;
	}

	function get_padre()
	{
		return $this->padre;
	}
	
	function tiene_padre()
	{
		return $this->padre !== null;	
	}

	function get_hijos()
	{
		if ($this->es_carpeta()) {
			return $this->items_hijos;
		} else {
			return $this->subelementos;
		}
	}	
	
	function tiene_hijos_cargados()
	{
		if ($this->es_carpeta() && ! $this->es_hoja() && count($this->items_hijos) == 0) {
			return false;	
		}
		if (!$this->es_carpeta() && ! $this->carga_profundidad) {
			return false;
		}
		return true;
	}
	

	function get_info_extra()
	{
		return $this->info_extra;
	}	
	
	//------------------------------------ CAMBIO DE ESTADO --------------------------------------------------------

	
	function set_nivel($nivel) { $this->nivel = $nivel; }	
	
	function set_camino($camino) {
		$this->camino = $camino;
	}
	
	function otorgar_permiso($grupo)
	{
		$sql = "INSERT INTO apex_usuario_grupo_acc_item (usuario_grupo_acc, proyecto, item) 
				VALUES ('$grupo', '{$this->get_proyecto()}', '{$this->get_id()}')";
		if(toba::get_db('instancia')->Execute($sql) === false)
			throw new excepcion_toba("Ha ocurrido un error CREANDO los permisos - " .toba::get_db('instancia')->ErrorMsg());
	}	
}
?>