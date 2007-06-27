<?php

class toba_gtk_registrar_base
{
	static $comp_req = array(
		'dlg_registrar_base'
	);
	
	function __construct()
	{
		$archivo = dirname(__FILE__).'/toba.glade';
    	$glade = new GladeXML($archivo, 'dlg_registrar_base');
		foreach (self::$comp_req as $comp) {
			$this->comp[$comp] = $glade->get_widget($comp);
		}		
		$glade->signal_autoconnect_instance($this);
	}	
	
	function show()
	{
		$this->comp['dlg_registrar_base']->run();
	}
	
	function evt__aceptar()
	{
		/*$config = inst_fact::config();		
		$config->set('path_instalaciones', $this->comp['edit_carp_defecto']->get_text());
		$config->set('grupo_desarrollo', $this->comp['edit_grupo']->get_value());
		$config->set('svn', 'url', $this->comp['edit_svn_url']->get_text());
		$config->set('svn', 'usuario', $this->comp['edit_svn_usuario']->get_text());
		$config->set_clave_svn($this->comp['edit_svn_clave']->get_text());
		$config->set('motor_bd','motor', $this->comp['combo_bd_motor']->get_active_text());
		$config->set('motor_bd','profile', $this->comp['edit_bd_profile']->get_text());
		$config->set('motor_bd','usuario', $this->comp['edit_bd_usuario']->get_text());
		$config->set('motor_bd','clave', $this->comp['edit_bd_clave']->get_text());
		$config->guardar();
*/
		$this->comp['dlg_registrar_base']->destroy();
	}
	
	function evt__cancelar()
	{
		$this->comp['dlg_registrar_base']->destroy();		
	}	
	
}

?>