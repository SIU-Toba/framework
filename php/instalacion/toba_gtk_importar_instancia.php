<?php

class toba_gtk_importar_instancia
{
	static $comp_req = array(
		'dlg_importar_instancia',
		'btn_directorio',
		'edit_toba', 'combo_instancia', 'check_reemplazar'
	);
	protected $toba_instalador;
	protected $param_comando;
	protected $datos=null;
	protected $check_proyectos = array();
	
	function __construct($toba_instalador, $param_comando)
	{
		$this->param_comando = $param_comando;		
		$this->toba_instalador = $toba_instalador;
		$archivo = dirname(__FILE__).'/toba.glade';
    	$glade = new GladeXML($archivo, 'dlg_importar_instancia');
		foreach (self::$comp_req as $comp) {
			$this->comp[$comp] = $glade->get_widget($comp);
		}		
		$glade->signal_autoconnect_instance($this);
		$this->comp['edit_toba']->set_text(toba_dir());		
		$this->evt__cambio_directorio();
	}	
	
	function show()
	{
		$this->comp['dlg_importar_instancia']->run();
		return $this->datos;
	}
	
	function evt__seleccionar_carpeta($destino)
	{
		$dialogo = new GtkFileChooserDialog(
							'Seleccionar Carpeta Toba',
							$this->comp['dlg_importar_instancia'],
							Gtk::FILE_CHOOSER_ACTION_SELECT_FOLDER,
							array(Gtk::STOCK_OPEN, Gtk::RESPONSE_OK)
					);
		if (Gtk::RESPONSE_OK == $dialogo->run()) {
			$this->comp['edit_toba']->set_text($dialogo->get_filename());
		}
		$dialogo->destroy();
	}	

	function evt__cambio_directorio()
	{
		$modelo = new GtkListStore(Gtk::TYPE_STRING);
		$path = $this->comp['edit_toba']->get_text().'/instalacion';
		$instancia = $this->toba_instalador->get_instancia($this->param_comando[1]);
		foreach($instancia->get_lista($path) as $id_instancia) {
			$iter = $modelo->append(array($id_instancia));
		}
		$this->comp['combo_instancia']->set_model($modelo);	
		if (isset($iter)) {
			$this->comp['combo_instancia']->set_active_iter($iter);
		}

	}

	function evt__aceptar()
	{
		$iter = $this->comp['combo_instancia']->get_active_iter();
		if (isset($iter)) {
			$this->datos = array();
			$this->datos[] = $this->comp['combo_instancia']->get_model()->get_value($iter, 0);
			$this->datos[] = $this->comp['edit_toba']->get_text();
			$this->datos[] = $this->comp['check_reemplazar']->get_active();
		}				
		$this->comp['dlg_importar_instancia']->destroy();
	}
	
	function evt__cancelar()
	{
		$this->comp['dlg_importar_instancia']->destroy();		
	}
	
}

?>