<?php

class toba_gtk_crear_usuario
{
	static $comp_req = array(
		'dlg_crear_usuario',
		'edit_usuario_id','edit_usuario_nombre','edit_usuario_clave',
	);
	protected $toba_instalador;
	protected $datos=null;
		
	function __construct($toba_instalador)
	{
		$this->toba_instalador = $toba_instalador;
		$archivo = dirname(__FILE__).'/toba.glade';
    	$glade = new GladeXML($archivo, 'dlg_crear_usuario');
		foreach (self::$comp_req as $comp) {
			$this->comp[$comp] = $glade->get_widget($comp);
		}		
		$glade->signal_autoconnect_instance($this);
		
		$pixbuf = $this->comp['dlg_crear_usuario']->render_icon(Gtk::STOCK_DIALOG_QUESTION, Gtk::ICON_SIZE_DIALOG);
		$this->comp['dlg_crear_usuario']->set_icon($pixbuf);		
	}	
	
	function show()
	{
		$this->comp['dlg_crear_usuario']->run();
		return $this->datos;
	}
	
	function evt__aceptar()
	{
		//-- Usuario
		$usuario['usuario'] = $this->comp['edit_usuario_id']->get_text();
		$usuario['nombre'] = $this->comp['edit_usuario_nombre']->get_text();
		$usuario['clave'] = $this->comp['edit_usuario_clave']->get_text();
		$this->datos = $usuario;

		$this->comp['dlg_crear_usuario']->destroy();
	}
	
	function evt__cancelar()
	{
		$this->comp['dlg_crear_usuario']->destroy();		
	}	
	
}

?>