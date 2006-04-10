<?php
require_once('nucleo/componentes/info/interfaces.php');

class item_toba implements recorrible_como_arbol
{
	protected $datos;					//Datos básicos
	protected $nivel;					//Nivel del item en el arbol de items
	protected $grupos_acceso;			//Grupos que pueden acceder al item
	protected $camino;					//Arreglo de carpetas que componen la rama en donde pertenece el item
	protected $items_hijos=array();		//Arreglo de hijos 
	protected $padre=null;				//Objeto item padre
	protected $extra = '';
	protected $objeto_info;
	
	function __construct($datos = array())
	{
		$this->datos = $datos;
		$this->items_hijos = array();		
	}
	//------------------------------------PROPIEDADES --------------------------------------------------------			
	
	function id_padre() {	return $this->datos['padre']; }	

	function nivel() {	return $this->nivel; }
	
	function camino() { return $this->camino; }
	
	function nombre() { return $this->datos['nombre']; }
	
	function proyecto() { return $this->datos['proyecto']; }
	
	function tipo_solicitud() { return $this->datos["solicitud_tipo"]; }
	
	function crono() 
	{ 
		if (isset($this->datos['crono']))
			return $this->datos['crono'] == 1; 
	}
	
	function objetos() { return $this->datos["objetos"]; }
	
	function registra_solicitud()
	{ 
		if (isset($this->datos['registrar']))
			return $this->datos["registrar"]; 
	}
	
	function propietario() { return $this->datos['usuario']; }

	function grupos_acceso()
	{
		if (!isset($this->grupos_acceso)) {
			$sql = "
				SELECT g.usuario_grupo_acc
				FROM
					apex_usuario_grupo_acc_item g
				WHERE
					g.item = '{$this->get_id()}' AND
					g.proyecto = '{$this->proyecto()}'
			";
			$rs = toba::get_db('instancia')->Execute($sql);
			if (!$rs)
				throw new excepcion_toba("INFO DEL ITEM - [error] " . toba::get_db('instancia')->ErrorMsg()." - [sql] $sql");
			if ($rs->EOF)
				$this->grupos_acceso = array();
			else
				$this->grupos_acceso =  aplanar_matriz($rs->GetArray());
		}
		return $this->grupos_acceso;
	}
		
	//------------------------------------PREGUNTAS --------------------------------------------------------
	function grupo_tiene_permiso($grupo)
	{
		return in_array($grupo, $this->grupos_acceso());
	}
	
	function es_carpeta() { return $this->datos['carpeta']; }
	
	function es_de_menu() {	
		if ($this->tiene_padre()) {
			return $this->datos["menu"]; 
		} else {
			return true;	
		}
	}
	
	function es_publico() { return $this->datos['publico']; } 
	
	function es_buffer() 
	{ 
		return !($this->datos['act_buf']== 0 && $this->datos['act_buf_p']=="toba");
	}
	
	function es_patron()
	//--- Es un PATRON?? El patron <toba,especifico> representa la ausencia de PATRON	
	{
		return !($this->datos['act_pat']=="especifico" && $this->datos['act_pat_p']=="toba");
	}
	
	function es_accion()
	{
		return !$this->es_buffer() && !$this->es_patron();
	}

	function es_hijo_de($carpeta)
	{
		if ($this->get_id() == '')
			return false;
		return $this->datos['padre'] == $carpeta->get_id();
	}
	
	function vinculo_editor()
	{
		if ($this->es_carpeta())
			$item_editor = "/admin/items/carpeta_propiedades";
		else
			$item_editor = "/admin/items/editor_items";		
		return toba::get_vinculador()->generar_solicitud("toba", $item_editor,
						array( apex_hilo_qs_zona => $this->proyecto() .apex_qs_separador. $this->get_id()),
						false, false, null, true, "central");
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
				$this->extra = "El ítem es inaccesible porque no hay grupo de acceso que tenga permiso de accederlo.";
			}
			return true;
		}
		//--- Si es de menu y algun padre no lo es, no se va a mostrar en el mismo
		$es_de_menu = $this->es_de_menu();
		$padre = $this->get_padre();
		while ($padre != null) {
			if ($es_de_menu && ! $padre->es_de_menu()) {
				$this->extra = "El ítem es inaccesible por menú porque la carpeta `{$padre->nombre()}` no se muestra en el mismo.";
				return true;
				break;
			}
			$padre = $padre->get_padre();
		}
		return false;
	}
	
	/**
	 * Recorre el item en profundidad buscando el objeto pasado por parametro
	 * La ejecución de este método es muy costosa ya que hace una query por objeto
	 */
	function contiene_objeto($id)
	{
		$id_info = array('componente' => $this->get_id(), 'proyecto' => $this->proyecto());
		$info = constructor_toba::get_info($id_info, "item");
		return $info->contiene_objeto($id);
	}
	
	//------------------------------------ CAMBIO DE ESTADO --------------------------------------------------------
	
	function set_nivel($nivel) { $this->nivel = $nivel; }	
	
	function set_camino($camino) {
		$this->camino = $camino;
	}
	
	function otorgar_permiso($grupo)
	{
		$sql = "INSERT INTO apex_usuario_grupo_acc_item (usuario_grupo_acc, proyecto, item) 
				VALUES ('$grupo', '{$this->proyecto()}', '{$this->get_id()}')";
		if(toba::get_db('instancia')->Execute($sql) === false)
			throw new excepcion_toba("Ha ocurrido un error CREANDO los permisos - " .toba::get_db('instancia')->ErrorMsg());
	}

	//------------------------------------RECORRIDOS--------------------------------------------------------	
	function get_id()
	{
		return $this->datos['item'];
	}
	
	function get_nombre_corto() { 
		return $this->nombre();
	}

	function get_nombre_largo() { 
		return $this->nombre();
	}	

	function es_hoja()
	{
		return $this->datos['cant_hijos'] == 0 && $this->datos['objetos'] == 0;
	}
	
	function tiene_propiedades()
	{
		return true;
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
			return $this->cargar_info()->get_hijos();
		}
	}
	
	function tiene_hijos_cargados()
	{
		if ($this->es_carpeta() && ! $this->es_hoja() && count($this->items_hijos) == 0) {
			return false;	
		}
		if (!$this->es_carpeta() && ! isset($this->objeto_info)) {
			return false;
		}
		return true;
	}
	
	function get_info_extra()
	{
		return $this->extra;
	}
	
	function get_iconos()
	{
		$iconos = array();
		if ($this->es_carpeta()) {
			$iconos[] = array(
				'imagen' => recurso::imagen_apl("items/carpeta.gif", false),
				'ayuda' => "Editar propiedades de la carpeta",
				'vinculo' => $this->vinculo_editor()
				);

		} else {
			$iconos[] = array(
				'imagen' => recurso::imagen_apl("items/item.gif", false),
				'ayuda' => "Editar propiedades del ITEM",
				'vinculo' => $this->vinculo_editor()
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
			} else {
				$iconos[] = array(
								'imagen' => recurso::imagen_apl("items/instanciar.gif",false),
								'ayuda' => 'Ejecutar el ITEM',
								'vinculo' => toba::get_vinculador()->generar_solicitud($this->proyecto(), $this->get_id(), 
												null,false,false,null,true, "central")
							);
			}
		}
		return $iconos;
	}
	
	function get_utilerias()
	{
		$utilerias = array();
		if ($this->es_carpeta()) {	
			if($this->es_de_menu()) {
				$utilerias[] = array(
					'imagen' => recurso::imagen_apl("items/menu.gif",false),
					'ayuda' => "La CARPETA esta incluido en el MENU del PROYECTO",
					'vinculo' => null
				);
			}
			// Ordenamiento, Nueva carpeta, nuevo item
/*			
			$utilerias[] = array(
				'imagen' => recurso::imagen_apl("items/carpeta_ordenar.gif", false),
				'ayuda'=> "Ordena alfabéticamente los items incluídos en esta CARPETA",
				'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/items/carpeta_ordenar", 
								array("padre_p"=>$this->proyecto(), "padre_i"=>$this->get_id()) )
			);
*/
			$utilerias[] = array(
				'imagen' => recurso::imagen_apl("items/carpeta_nuevo.gif", false),
				'ayuda'=> "Crear SUBCARPETA en esta rama del CATALOGO",
				'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/items/carpeta_propiedades", 
								array("padre_p"=>$this->proyecto(), "padre_i"=>$this->get_id()),false,false,null,true, "central" )
			);
			$utilerias[] = array(
				'imagen' => recurso::imagen_apl("items/item_nuevo.gif", false),
				'ayuda'=> "Crear ITEM hijo en esta rama del CATALOGO",
				'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/items/editor_items", 
								array("padre_p"=>$this->proyecto(), "padre_i"=>$this->get_id()),false,false,null,true, "central" )
			);			

		} else { //Es un item común
			if($this->crono()){		
				$utilerias[] = array(
					'imagen' => recurso::imagen_apl("cronometro.gif", false),
					'ayuda'=> "El ITEM se cronometra"
				);			
			}
			if($this->es_publico()){
				$utilerias[] = array(
					'imagen' => recurso::imagen_apl("usuarios/usuario.gif", false),
					'ayuda'=> "ITEM público"
				);				
			}
			if($this->registra_solicitud() == 1){
				$utilerias[] = array(
					'imagen' => recurso::imagen_apl("solicitudes.gif", false),
					'ayuda'=> "El ITEM se registra"
				);				
			}
			if ($this->es_de_menu()) {
				$utilerias[] = array(
					'imagen' => recurso::imagen_apl("items/menu.gif", false),
					'ayuda'=> "El ITEM esta incluido en el MENU del PROYECTO"
				);	
			}
/*			//ID del objeto
			$utilerias[] = array(
				'imagen' => recurso::imagen_apl("nota.gif", false),
				'ayuda' => $this->get_id()
			);*/
		}
		return $utilerias;
	}
	
	//------------------------------------DEFINICION ESTATICA--------------------------------------------------------	
	static function definicion_campos()
	{
		return "
				i.orden							as orden,
				i.proyecto						as proyecto,
				i.item		 					as item,
				i.padre		 					as padre,
				i.nombre	 					as nombre,
				i.carpeta						as carpeta,
				i.menu							as menu,
				i.usuario						as usuario,
				i.actividad_buffer_proyecto 	as act_buf_p,
				i.actividad_buffer				as act_buf,
				i.actividad_patron_proyecto		as act_pat_p,
				i.actividad_patron				as act_pat,
				i.actividad_accion				as act_acc,
				i.publico						as publico,
				i.solicitud_registrar			as registrar,
				i.solicitud_registrar_cron		as crono,
				i.solicitud_tipo				as solicitud_tipo";
	}
	
	static function definicion_tabla()
	{
		return "apex_item i";
	}
	
	//------------------------------------CARGAS PARTICULARES--------------------------------------------------------		
	function cargar_por_id($proyecto, $id)
	{
		$sql = "SELECT {$this->definicion_campos()} FROM {$this->definicion_tabla()} WHERE 
				i.item = '$id' AND i.proyecto = '$proyecto'";
		$rs = toba::get_db('instancia')->Execute($sql);
		if (!$rs || $rs->EOF)
			throw new excepcion_toba("ITEM Carga - [error] " . toba::get_db('instancia')->ErrorMsg()." - [sql] $sql");
		else
			$this->datos = $rs->fields;
	}	
	
	function cargar_info()
	{
		if (!isset($this->objeto_info)) {
			//--- Hay que retornar los objetos hijos
			$id_info = array('componente' => $this->get_id(), 'proyecto' => $this->proyecto());
			$this->objeto_info = constructor_toba::get_info($id_info, "item");
		}
		return $this->objeto_info;
	}
	
	/**
	*	Crea una rama de items comenzando por la raiz
	*	Al asumir que los niveles son pocos se hace una consulta por nivel
	*	Quedan cargado en el objeto los ancestros de la rama
	*/
	function cargar_rama($proyecto=null, $id=null)
	{
		if (isset($proyecto) && isset($id)) {
			$this->cargar_por_id($proyecto, $id);			
		} 
		$item_ancestro = $this;
		while ($item_ancestro->get_id() != null) {
			$nodo = new item_toba();
			$nodo->cargar_por_id($this->proyecto(), $item_ancestro->id_padre());
			$item_ancestro->set_padre($nodo);
			$item_ancestro = $nodo;
		}
	}
	
}



?>