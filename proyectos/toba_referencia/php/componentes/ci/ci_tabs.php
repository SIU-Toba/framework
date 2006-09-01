<?php 

class ci_tabs extends toba_ci
{
	function extender_objeto_js()
	{
		echo "	{$this->objeto_js}.evt__b_ocultar_js = function () {
			this.dependencia('verticales').mostrar_tab('b', false);
			this.dependencia('horizontales').mostrar_tab('b', false);
		} 
		";
		echo "	{$this->objeto_js}.evt__b_mostrar_js = function () {
			this.dependencia('verticales').mostrar_tab('b', true);
			this.dependencia('horizontales').mostrar_tab('b', true);
		}";
	}
	
	function evt__c_ocultar_php()
	{
		$this->dep('verticales')->pantalla()->eliminar_tab('c');
		$this->dep('horizontales')->pantalla()->eliminar_tab('c');
	}
}
?>