<?php 
class ci_editor_perfiles extends toba_ci
{
	protected $s__proyecto;
	protected $s__perfil_funcional = '';
	protected $s__arbol_cargado = false;
	
	function conf__arbol_perfiles($arbol) 
	{
		if (! isset($this->s__arbol_cargado) || !$this->s__arbol_cargado) {
			$catalogador = new toba_catalogo_items_perfil($this->s__proyecto, $this->s__perfil_funcional);
			$catalogador->cargar_todo();
			$raiz = $catalogador->buscar_carpeta_inicial();
			$arbol->set_datos(array($raiz), true);
			$this->s__arbol_cargado = true;
		}
	}
	
	function set_proyecto($proyecto)
	{
		$this->s__proyecto = $proyecto;
	}
	
	function set_perfil_funcional($perfil_funcional)
	{
		$this->s__perfil_funcional = $perfil_funcional;
	}
}

?>