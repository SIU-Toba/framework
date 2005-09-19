<?
require_once("conversion_toba.php");

class conversion_0_8_3_fotos extends conversion_toba
{
	function get_version()
	{
		return "0.8.3.fotos";	
	}

	/**
	*	Se migran las fotos del catalogo de items a un lugar comun de fotos del administrador.
	*
	*/
	function cambio_migra_fotos_del_catalogo_items()
	{
		$sql = "
			INSERT INTO apex_admin_album_fotos
			(
				proyecto,
				usuario,
				foto_tipo,
				foto_nombre,
				foto_nodos_visibles,
				foto_opciones
			) 
			SELECT
				proyecto,
				usuario,
				'cat_item',
				foto_nombre,
				foto_nodos_visibles,
				foto_opciones
			FROM 
				apex_arbol_items_fotos
			WHERE
				proyecto = '{$this->proyecto}'
		";
		$this->ejecutar_sql($sql,"instancia");
		
		$sql = "
			DELETE FROM apex_arbol_items_fotos WHERE proyecto = '{$this->proyecto}'
		";
		$this->ejecutar_sql($sql,"instancia");		
	}
}