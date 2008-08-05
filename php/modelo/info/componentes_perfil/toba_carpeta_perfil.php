<?php
/*
	* Javascript de seleccion en cascada
	* Inspeccionar los nodos para saber si alguno esta activado, si es asi tiene que grabarse tambien.

*/
class toba_carpeta_perfil extends toba_elemento_perfil 
{
	protected $icono = "nucleo/carpeta.gif";
	protected $carpeta = true;

	function permiso_activo()
	{
		foreach($this->hijos as $hijo) {
			if( $hijo->permiso_activo() ) return true;
		}
		return false;
	}
	
	//------------------------------------------------
	//----------- Interface FORM
	
	function sincronizar()
	{
		foreach($this->hijos as $hijo) {
			$hijo->sincronizar();
		}
		if ($this->permiso_activo() ) {
			$this->acceso_actual = 1;	
		} else {
			$this->acceso_actual = 0;
		}
		parent::sincronizar();	
	}

	function set_grupo_acceso($acceso)
	{
		foreach($this->hijos as $hijo) {
			$hijo->set_grupo_acceso($acceso);
		}
		parent::set_grupo_acceso($acceso);
	}
	
	function cargar_estado_post($id){}
	
	function get_input($id)
	{
		$id_input = $id.'_carpeta';
		//$img_marcar = toba_recurso::imagen_toba('aplicar.png', false);
		$html = '';		
		//$html .= "<img src='$img_marcar' id='".$id_input."_img' onclick='marcar(\"$id_input\")' />";
		$html .= "<input type='checkbox' value='1' id='$id_input' name='$id_input' onclick='marcar(\"$id_input\", this.value)' />";		
		return $html;
	}

}

?>