<?php 
class toba_rf_grupo_columnas extends toba_rf_grupo
{
	function inicializar()
	{
		$this->nombre_largo = 'Columnas';
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( 'fuente.png', false),
				'ayuda' => "Columnas",
			);		
	}

}
?>