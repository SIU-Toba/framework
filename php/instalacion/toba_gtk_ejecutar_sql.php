<?php

class toba_gtk_ejecutar_sql
{
	static $comp_req = array(
		'dlg_ejecutar_sql', 'edit_archivo'
	);
	protected $toba_instalador;
	protected $datos=null;
		
	function __construct($toba_instalador)
	{
		$this->toba_instalador = $toba_instalador;
		$archivo = dirname(__FILE__).'/toba.glade';
    	$glade = new GladeXML($archivo, 'dlg_ejecutar_sql');
		foreach (self::$comp_req as $comp) {
			$this->comp[$comp] = $glade->get_widget($comp);
		}		
		$glade->signal_autoconnect_instance($this);
		
		$pixbuf = $this->comp['dlg_ejecutar_sql']->render_icon(Gtk::STOCK_DIALOG_QUESTION, Gtk::ICON_SIZE_DIALOG);
		$this->comp['dlg_ejecutar_sql']->set_icon($pixbuf);		
	}	
	
	function show()
	{
		$this->comp['edit_archivo']->set_title('Seleccione un archivo SQL a ejecutar');
		$this->comp['edit_archivo']->set_current_folder(toba_dir());
		$this->comp['dlg_ejecutar_sql']->run();
		return $this->datos;
	}
	
	function evt__aceptar()
	{
		$this->datos = $this->comp['edit_archivo']->get_filename();
		$this->comp['dlg_ejecutar_sql']->destroy();
	}
	
	function evt__cancelar()
	{
		$this->comp['dlg_ejecutar_sql']->destroy();		
	}	
	
}

?>