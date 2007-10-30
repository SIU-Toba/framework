<?php

class toba_gtk_registrar_base
{
	static $comp_req = array(
		'dlg_registrar_base',
		'radio_tipo_fuente', 'radio_tipo_aislada',
		'combo_instancia', 'combo_proyecto', 'combo_fuente',
		'edit_nombre', 'combo_bd_motor', 'edit_bd_profile', 'edit_bd_usuario', 'edit_bd_clave', 'combo_base', 'edit_base',
		'check_reutilizar', 'combo_reutilizar'
	);
	protected $datos=null;	
	/**
	 * @var toba_instalador
	 */
	protected $toba_instalador;
	
	function __construct($toba_instalador)
	{
		$this->toba_instalador = $toba_instalador;
		$archivo = dirname(__FILE__).'/toba.glade';
    	$glade = new GladeXML($archivo, 'dlg_registrar_base');
		foreach (self::$comp_req as $comp) {
			$this->comp[$comp] = $glade->get_widget($comp);
		}		
		$glade->signal_autoconnect_instance($this);
		$pixbuf = $this->comp['dlg_registrar_base']->render_icon(Gtk::STOCK_DIALOG_QUESTION, Gtk::ICON_SIZE_DIALOG);
		$this->comp['dlg_registrar_base']->set_icon($pixbuf);	

		$modelo = new GtkListStore(Gtk::TYPE_STRING);
		foreach($this->toba_instalador->get_instalacion()->get_lista_instancias() as $id_instancia) {
			$iter = $modelo->append(array($id_instancia));
		}
		$this->comp['combo_instancia']->set_model($modelo);		
		
		
		$modelo = new GtkListStore(Gtk::TYPE_STRING);
		foreach($this->toba_instalador->get_instalacion()->get_lista_bases() as $id_base) {
			$iter = $modelo->append(array($id_base));
		}		
		$this->comp['combo_reutilizar']->set_model($modelo);
		$this->evt__cambiar_tipo();
		$this->evt__tomar_definicion();		
	}	
	
	function show()
	{
		$this->comp['dlg_registrar_base']->run();
		return $this->datos;		
	}
	
	function evt__aceptar()
	{
		$this->datos[0] = $this->comp['edit_nombre']->get_text();
		if ($this->comp['check_reutilizar']->get_active()) {
			$combo = $this->comp['combo_reutilizar'];
			$iter = $combo->get_active_iter();
			if (isset($iter)) {
				$this->datos[1] = $combo->get_model()->get_value($iter, 0);
			}
		} else {
			$this->datos[1] = null;
		}
		$this->datos[2]['motor'] = $this->comp['combo_bd_motor']->get_active_text();
		$this->datos[2]['profile'] = $this->comp['edit_bd_profile']->get_text();
		$this->datos[2]['usuario'] = $this->comp['edit_bd_usuario']->get_text();
		$this->datos[2]['clave'] = $this->comp['edit_bd_clave']->get_text();
		$this->datos[2]['base'] = $this->comp['edit_base']->get_text();
		$this->comp['dlg_registrar_base']->destroy();
	}
	
	function evt__cancelar()
	{
		$this->comp['dlg_registrar_base']->destroy();		
	}	
	
	function evt__cambiar_instancia()
	{
		$combo = $this->comp['combo_instancia'];
		$iter = $combo->get_active_iter();
		$modelo = new GtkListStore(Gtk::TYPE_STRING);		
		if (isset($iter)) {
			$instancia = $combo->get_model()->get_value($iter, 0);
			foreach($this->toba_instalador->get_instancia($instancia)->get_lista_proyectos_vinculados() as $id_proyecto) {
				$iter = $modelo->append(array($id_proyecto));
			}
		}
		$this->comp['combo_proyecto']->set_model($modelo);
		$this->evt__cambiar_fuente();					
	}
	
	function evt__cambiar_proyecto()
	{
		$combo = $this->comp['combo_instancia'];
		$iter = $combo->get_active_iter();
		$modelo = new GtkListStore(Gtk::TYPE_STRING);		
		if (isset($iter)) {
			$instancia = $combo->get_model()->get_value($iter, 0);
		}
		$combo = $this->comp['combo_proyecto'];
		$iter = $combo->get_active_iter();
		$modelo = new GtkListStore(Gtk::TYPE_STRING);	
		if (isset($iter) && isset($instancia)) {
			$id_proyecto = $combo->get_model()->get_value($iter, 0);
			foreach($this->toba_instalador->get_instancia($instancia)->get_proyecto($id_proyecto)->get_indice_fuentes() as $fuente) {
				$iter = $modelo->append(array($fuente));
			}
		}
		$this->comp['combo_fuente']->set_model($modelo);
		$this->evt__cambiar_fuente();					
	}	
	
	function evt__cambiar_fuente()
	{
		$instancia = $this->comp['combo_instancia']->get_active_text();
		$proyecto = $this->comp['combo_proyecto']->get_active_text();
		$fuente = $this->comp['combo_fuente']->get_active_text();
		$this->comp['edit_nombre']->set_text("$instancia $proyecto $fuente");
	}
	
	function evt__cambiar_reutilizar()
	{
		$combo = $this->comp['combo_reutilizar'];
		$iter = $combo->get_active_iter();
		$modelo = new GtkListStore(Gtk::TYPE_STRING);		
		if (isset($iter)) {
			$base = $combo->get_model()->get_value($iter, 0);
			$parametros = $this->toba_instalador->get_instalacion()->get_parametros_base($base);
			$this->comp['edit_bd_usuario']->set_text($parametros['usuario']);			
			$this->comp['edit_bd_profile']->set_text($parametros['profile']);			
			//$this->comp['combo_bd_motor']->set_text($parametros['postgres7']);
			$this->comp['combo_bd_motor']->set_active(0);
			$this->comp['edit_bd_clave']->set_text($parametros['clave']);
			$this->comp['edit_base']->set_text($parametros['base']);
		}		
	}
	
	function evt__cambiar_tipo()
	{
		$radio = $this->comp['radio_tipo_fuente'];
		$es_tipo = $radio->get_active();
		$this->comp['combo_instancia']->set_sensitive($es_tipo);
		$this->comp['combo_proyecto']->set_sensitive($es_tipo);
		$this->comp['combo_fuente']->set_sensitive($es_tipo);
		$this->comp['edit_nombre']->set_sensitive(! $es_tipo);
	}
	
	function evt__tomar_definicion()
	{
		$tomar = $this->comp['check_reutilizar']->get_active();
		$this->comp['combo_reutilizar']->set_sensitive($tomar);
		$this->comp['combo_bd_motor']->set_sensitive(! $tomar);
		$this->comp['edit_bd_profile']->set_sensitive(! $tomar);
		$this->comp['edit_bd_usuario']->set_sensitive(! $tomar);
		$this->comp['edit_bd_clave']->set_sensitive(! $tomar);
		$this->comp['combo_base']->set_sensitive(! $tomar);
	}
	
	
}

?>