<?php

class  toba_registro_conflicto_valor_original extends toba_registro_conflicto
{
	protected $columna;
	protected $valor_actual;
	
	function  __construct($registro, $columna, $valor_actual)
	{
		parent::__construct($registro);
		$this->tipo = toba_registro_conflicto::warning;
		$this->numero = 5;
		$this->columna = $columna;
		$this->valor_actual = ($valor_actual == toba_personalizacion::nulo) ? 'null' : $valor_actual;
	}

	function get_descripcion()
	{
		$valor_original = ($this->registro->get_valor_original($this->columna) == toba_personalizacion::nulo)
							? 'null'
							: $this->registro->get_valor_original($this->columna);
		
		$valor_cambiado = $this->registro->get_valor($this->columna);
		return	"[W:$this->numero] El update se hizo de <$this->columna:$valor_original> "
				."a <$this->columna:$valor_cambiado>. Ahora el valor original de la "
				."columna es <$this->columna:$this->valor_actual>. El registro afectado tiene clave "
				."<{$this->registro->get_clave()}> en la tabla <{$this->registro->get_tabla()}>";
	}
}
?>
