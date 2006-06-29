<?php
require_once('nucleo/componentes/interface/objeto_ci.php');
require_once('objetos/ei_filtro - ei_cuadro/dao.php');
  
class extension_ci extends objeto_ci
{
	function evt__cuadro__cant_reg()
	{
		return dao_importes::get_cantidad_importes();
	}
	
	function evt__cuadro__carga()
	{
		$datos = dao_importes::get_importes();
		$tamanio_pagina = $this->dependencia('cuadro')->get_tamanio_pagina();
		$pagina_actual = $this->dependencia('cuadro')->get_pagina_actual();
		$offset = ($pagina_actual - 1) * $tamanio_pagina;
		return array_slice($datos, $offset, $tamanio_pagina);
	}
}
?>