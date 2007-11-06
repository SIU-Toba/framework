<?php

class toba_gtk_importar_proyecto
{
	static $comp_req = array(
		'dlg_importar_proyecto',
		'edit_toba', 'edit_proyecto'
	);
	protected $toba_instalador;
	protected $datos=null;
	protected $check_proyectos = array();
	
	function __construct($toba_instalador)
	{
		$this->toba_instalador = $toba_instalador;
		$archivo = dirname(__FILE__).'/toba.glade';
    	$glade = new GladeXML($archivo, 'dlg_importar_proyecto');
		foreach (self::$comp_req as $comp) {
			$this->comp[$comp] = $glade->get_widget($comp);
		}		
		$glade->signal_autoconnect_instance($this);
	}	
	
	function show()
	{
		$this->comp['dlg_importar_proyecto']->run();
		return $this->datos;
	}
	
	
	function evt__seleccionar_carpeta($destino)
	{
		$dialogo = new GtkFileChooserDialog(
							'Seleccionar Carpeta Toba',
							$this->comp['dlg_importar_proyecto'],
							Gtk::FILE_CHOOSER_ACTION_SELECT_FOLDER,
							array(Gtk::STOCK_OPEN, Gtk::RESPONSE_OK)
					);
		if (Gtk::RESPONSE_OK == $dialogo->run()) {
			$this->comp['edit_toba']->set_text($dialogo->get_filename());
		}
		$dialogo->destroy();
	}	
	
	function evt__aceptar()
	{
		$this->datos = array();
		$this->datos[] = $this->comp['edit_proyecto']->get_text();
		$this->datos[] = $this->comp['edit_toba']->get_text();
		$this->comp['dlg_importar_proyecto']->destroy();
	}
	
	function evt__cancelar()
	{
		$this->comp['dlg_importar_proyecto']->destroy();		
	}
	
}

?>