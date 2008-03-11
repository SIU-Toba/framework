<?php 
class ci_restricciones_funcionales extends toba_ci
{
	protected $s__arbol_cargado;
	
	function ini__operacion() 
	{
		
	}

	function conf__arbol(arbol_restricciones_funcionales $arbol) 
	{
		if (! isset($this->s__arbol_cargado) || !$this->s__arbol_cargado) {
			$catalogador = new toba_catalogo_restricciones_funcionales( 'toba_referencia', 33 );
			$raiz = $catalogador->cargar();
			$arbol->set_datos($raiz, true);
			$this->s__arbol_cargado = true;
		}
	}
}

?>