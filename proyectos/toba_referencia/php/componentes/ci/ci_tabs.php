<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_tabs extends toba_ci
{
	function conf()
	{
		$this->s__instituciones_habilitadas = array();
		foreach ($this->s__instituciones_habilitadas as $institucion) {
		    $nombre_ef = 'pertenencia' . $institucion['cod_interno_institucion'];
		    $nuevos_efs[] = array('identificador' => $nombre_ef);
		}
		$this->dep('horizontales')->pantalla()->tab('b')->ocultar();
		$this->dep('verticales')->pantalla()->tab('b')->ocultar();
		//$this->dep('horizontales')->pantalla()->tab('c')->desactivar();
		//$this->dep('verticales')->pantalla()->tab('c')->desactivar();
	}

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "	{$id_js}.evt__b_ocultar_js = function () {
			this.dependencia('verticales').ocultar_tab('b');
			this.dependencia('horizontales').ocultar_tab('b');
			return false;
		} 
		";
		echo "	{$id_js}.evt__b_mostrar_js = function () {
			this.dependencia('verticales').mostrar_tab('b');
			this.dependencia('horizontales').mostrar_tab('b');
			return false;
		}
		";
		echo "	{$id_js}.evt__c_desactivar_js = function () {
			this.dependencia('verticales').desactivar_tab('c');
			this.dependencia('horizontales').desactivar_tab('c');
			return false;
		} 
		";
		echo "	{$id_js}.evt__c_activar_js = function () {
			this.dependencia('verticales').activar_tab('c');
			this.dependencia('horizontales').activar_tab('c');
			return false;
		}
		";
	}
	
	function evt__c_ocultar_php()
	{
		$this->dep('verticales')->pantalla()->eliminar_tab('c');
		$this->dep('horizontales')->pantalla()->eliminar_tab('c');
	}
	
	function evt__modificar()
	{
		$this->dep('verticales')->pantalla()->tab('c')->set_etiqueta('Cambio de ETIQUETA');
		$this->dep('verticales')->pantalla()->tab('c')->set_imagen('usuarios/usuario.gif');
		$this->dep('horizontales')->pantalla()->tab('a')->set_etiqueta('Cambio de ETIQUETA');
		$this->dep('horizontales')->pantalla()->tab('a')->set_imagen('borrar.gif');
	}
}


?>