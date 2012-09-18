<?php 
class toba_rf_grupo_eventos extends toba_rf_grupo
{
	function inicializar()
	{
		$this->nombre_largo = 'Grupo de Eventos';
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( 'evento.png', false),
				'ayuda' => 'Grupo de Eventos',
				);		
	}
	
}
?>