<?php 
class toba_rf_grupo_efs extends toba_rf_grupo
{
	function inicializar()
	{
		$this->nombre_largo = 'Elementos de Formulario';
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( 'objetos/formulario.gif', false),
				'ayuda' => 'Elementos de Formulario',
				);		
	}

}
?>