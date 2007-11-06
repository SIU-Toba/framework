<?php

class toba_gtk_cargar_proyecto
{
	static $comp_req = array(
		'dlg_cargar_proyecto',
		'combo_proyecto',
		'edit_path', 'edit_proyecto'
	);
	protected $toba_instalador;
	protected $param_comando;
	protected $datos=null;
	protected $check_proyectos = array();
	protected $label_otro = '--- Otro ---';
	
	function __construct($toba_instalador, $param_comando)
	{
		$this->param_comando = $param_comando;
		$this->toba_instalador = $toba_instalador;
		$archivo = dirname(__FILE__).'/toba.glade';
    	$glade = new GladeXML($archivo, 'dlg_cargar_proyecto');
		foreach (self::$comp_req as $comp) {
			$this->comp[$comp] = $glade->get_widget($comp);
		}		
		$glade->signal_autoconnect_instance($this);
		
		$modelo = new GtkListStore(Gtk::TYPE_STRING, Gtk::TYPE_STRING);
		$opciones = toba_modelo_proyecto::get_lista();
		print_r($opciones);
		$iter = $modelo->append(array($this->label_otro, null));
		foreach($opciones as $path => $id) {
			$label = ($id == $path) ? $id : "$id ($path)";
			$modelo->append(array($label, $id));
		}
		$this->comp['combo_proyecto']->set_model($modelo);
		$this->comp['combo_proyecto']->set_active_iter($iter);			
		$this->evt__cambio_proyecto();
	}	
	
	function show()
	{
		$this->comp['dlg_cargar_proyecto']->run();
		return $this->datos;
	}
	
	
	function evt__seleccionar_carpeta($destino)
	{
		$dialogo = new GtkFileChooserDialog(
							'Seleccionar Carpeta del Proyecto',
							$this->comp['dlg_cargar_proyecto'],
							Gtk::FILE_CHOOSER_ACTION_SELECT_FOLDER,
							array(Gtk::STOCK_OPEN, Gtk::RESPONSE_OK)
					);
		if (Gtk::RESPONSE_OK == $dialogo->run()) {
			$this->comp['edit_path']->set_text($dialogo->get_filename());
		}
		$dialogo->destroy();
	}	
	
	function evt__cambio_proyecto()
	{
		$iter = $this->comp['combo_proyecto']->get_active_iter();
		if (isset($iter)) {
			$id_proyecto = $this->comp['combo_proyecto']->get_model()->get_value($iter, 1);
			if ($id_proyecto != '') {
				$path = $this->toba_instalador->get_instancia($this->param_comando[1])->get_path_proyecto($id_proyecto);
				$this->comp['edit_path']->set_text($path);
				$this->comp['edit_proyecto']->set_text($id_proyecto);
				$this->comp['edit_path']->set_sensitive(false);
				$this->comp['edit_proyecto']->set_sensitive(false);
			} else {
				$this->comp['edit_path']->set_sensitive(true);
				$this->comp['edit_proyecto']->set_sensitive(true);				
				$this->comp['edit_path']->set_text('');
				$this->comp['edit_proyecto']->set_text('');
			}
		}
	}
	
	function evt__aceptar()
	{
		$iter = $this->comp['combo_proyecto']->get_active_iter();
		$proyecto = $this->comp['edit_proyecto']->get_text();
		$path = null;
		if (isset($iter)) {
			$id_proyecto = $this->comp['combo_proyecto']->get_model()->get_value($iter, 1);
			if ($id_proyecto == '') {
				$path = $this->comp['edit_path']->get_text();
			}
		}
		$this->datos[] = $proyecto;
		$this->datos[] = $path;
		$this->comp['dlg_cargar_proyecto']->destroy();
	}
	
	function evt__cancelar()
	{
		$this->comp['dlg_cargar_proyecto']->destroy();		
	}
	
}

?>