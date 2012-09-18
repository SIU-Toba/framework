<?php 
class toba_rf_grupo_pantallas extends toba_rf_grupo
{
	function inicializar()
	{
		$this->nombre_largo = 'Grupo de Pantallas';
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( 'objetos/pantalla.gif', false),
				'ayuda' => 'Grupo de Pantallas',
				);		
	}
	
}
?>