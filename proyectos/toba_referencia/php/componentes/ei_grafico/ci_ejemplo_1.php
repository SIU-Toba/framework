<?php
class ci_ejemplo_1 extends toba_ci
{
	protected $s__fuente;
	//-----------------------------------------------------------------------------------
	//---- grafico ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__grafico(toba_ei_grafico $grafico)
	{
		$grafico->conf()->canvas__set_titulo('Gráfico de torta');
		if (isset($this->s__fuente) && $this->s__fuente == 'verdana') {
			$grafico->conf()->canvas()->title->SetFont(FF_VERDANA, FS_BOLD, 14);
		} else {
			$grafico->conf()->canvas()->title->SetFont(FF_FONT2, FS_BOLD);
		}
		
		/**********************PRIMER SERIE********************************/
		$datos = array(13, 5, 3, 15, 10);
		$leyendas = array('lunes', 'martes', 'miercoles', 'jueves', 'viernes');
		
		$grafico->conf()
				->serie__agregar('nombre', $datos)    // Automáticamente setea la serie actual
				->serie__set_leyendas($leyendas)
				->serie__set_titulo('Semana uno')
				->serie__set_tema('water')
				->serie__set_centro(0.2);


		/**********************SEGUNDA SERIE********************************/
		foreach (array_keys($datos) as $key) {
			$datos[$key] = rand(1, 13);
		}
		
		$grafico->conf()
				->serie__agregar('otro_nombre', $datos)
				->serie__set_titulo('Semana dos')
				->serie__set_tema('water')
				->serie__separar_porciones(array(1, 3))
				->serie__set_centro(0.6);
	}

	//-----------------------------------------------------------------------------------
	//---- form -------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__form__modificacion($datos)
	{
		$this->s__fuente = $datos['fuente'];
	}
    
	function conf__form(form_torta_fuente $form)
	{
        if (isset($this->s__fuente) && $this->s__fuente == 'verdana') {
            $form->ef('fuente')->set_estado('verdana');
        } else {
            $form->ef('fuente')->set_estado('default');
        }
	}

}
?>