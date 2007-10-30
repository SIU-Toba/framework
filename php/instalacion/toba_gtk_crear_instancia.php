<?php

class toba_gtk_crear_instancia
{
	static $comp_req = array(
		'dlg_crear_instancia',
		'edit_id', 'check_tipo',
		'combo_base',
		'edit_usuario_id','edit_usuario_nombre','edit_usuario_clave',
		'box_proyectos'
	);
	protected $toba_instalador;
	protected $datos=null;
	protected $check_proyectos = array();
	
	function __construct($toba_instalador)
	{
		$this->toba_instalador = $toba_instalador;
		$archivo = dirname(__FILE__).'/toba.glade';
    	$glade = new GladeXML($archivo, 'dlg_crear_instancia');
		foreach (self::$comp_req as $comp) {
			$this->comp[$comp] = $glade->get_widget($comp);
		}		
		$glade->signal_autoconnect_instance($this);
		
		//---Bases disponibles	
		$store = new GtkListStore(Gtk::TYPE_STRING);
		$instalacion = $this->toba_instalador->get_instalacion();
		foreach( $instalacion->get_lista_bases() as $db ) {
			$param = $instalacion->get_parametros_base( $db );
			$store->append(array($db));	
		}
		$this->comp['combo_base']->set_model($store);
		$cellRenderer = new GtkCellRendererText();
		$this->comp['combo_base']->pack_start($cellRenderer);
		$this->comp['combo_base']->set_active(0);		
		
		//--- Pone los proyectos
		$proyectos = toba_modelo_proyecto::get_lista();
		foreach ($proyectos as $path => $id) {
			$label = ($path == $id) ? $id : "$id (Directorio $path)";
			$check = new GtkCheckButton($label);
			$check->set_use_underline(false);
			$this->check_proyectos[] = array($id, $path, $check);
			$check->show();
			$this->comp['box_proyectos']->pack_start($check);
		}
	}	
	
	function show()
	{
		$this->comp['dlg_crear_instancia']->run();
		return $this->datos;
	}
	
	function evt__aceptar()
	{
		$this->datos = array();
		//--- ID de instancia
		$this->datos[] = $this->comp['edit_id']->get_text();
		
		//--- Tipo de instancia
		if ($this->comp['check_tipo']->get_active()) {
			$this->datos[] = 'mini';			
		} else {
			$this->datos[] = 'normal';
		}
		
		//--- Base
		$this->datos[] = $this->comp['combo_base']->get_active_text();
		
		//--- Proyectos
		$proyectos = array();
		foreach ($this->check_proyectos as $info) {
			list($id, $path, $comp) = $info;
			if ($comp->get_active()) {
				$proyectos[$id] = $path;
			}
		}
		$this->datos[] = $proyectos;
		
		//-- Usuario
		$usuario['usuario'] = $this->comp['edit_usuario_id']->get_text();
		$usuario['nombre'] = $this->comp['edit_usuario_nombre']->get_text();
		$usuario['clave'] = $this->comp['edit_usuario_clave']->get_text();
		$this->datos[] = $usuario;
		$this->comp['dlg_crear_instancia']->destroy();
	}
	
	function evt__cancelar()
	{
		$this->comp['dlg_crear_instancia']->destroy();		
	}	
	
}

?>