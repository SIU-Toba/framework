<?php

class ci_serializacion_propiedades extends toba_testing_pers_ci
{
	protected $s__entero;
	protected $s__arreglo_basico;
	protected $s__objeto_automatico;	
	protected $s__objeto_manual;
	protected $s__arreglo_compuesto;
	
	function ini__operacion()
	{
		echo 'Iniciando Operacion';
		$this->s__entero = 8;
		$this->s__arreglo_basico = array( array('nombre' => 'pedro'));
		$this->s__objeto_manual = new objeto_manual('prueba', $this);
		$this->s__objeto_automatico = new objeto_automatico('prueba', $this);
		$this->s__arreglo_compuesto = array(
			'entero' => 8,
			'objeto' => array(
					'automatico' => new objeto_automatico('otro_automatico', $this),
					'manual' => new objeto_manual('otro_manual', $this),
				),
			'cadena' => 'hey'
		);
	}

	function post_configurar()
	{
		if ($this->s__entero !== 8) {
			throw new toba_error("El entero deberia ser 8");
		}
		if ($this->s__arreglo_basico !== array( array('nombre' => 'pedro'))) {
			throw new toba_error("El arreglo basico esta roto");
		}
		if ($this->s__objeto_manual->nombre !== 'prueba') {
			throw new toba_error("La propiedad del objeto almacenado difiere del actual!");
		}				
		if ($this->s__objeto_manual->ci !== $this) {
			throw new toba_error("El objeto almacenado difiere del actual!");
		}		
		if ($this->s__objeto_automatico->nombre !== 'prueba') {
			throw new toba_error("La propiedad del objeto almacenado difiere del actual!");
		}				
		if ($this->s__objeto_automatico->ci !== $this) {
			throw new toba_error("El objeto almacenado difiere del actual!");
		}
		if (count($this->s__arreglo_compuesto) !== 3
				|| $this->s__arreglo_compuesto['objeto']['automatico']->ci !== $this
				|| $this->s__arreglo_compuesto['objeto']['manual']->ci !== $this
				) {
			throw new toba_error("El arreglo compuesto almacenado difiere del actual!");
		}
		$this->pantalla()->agregar_notificacion('Ok');
	}
	
}

class objeto_manual
{
	public $nombre;
	public $ci;
	
	function __construct($nombre, $ci)
	{
		$this->nombre = $nombre;
		$this->ci = $ci;
	}
	
	function __sleep()
	{
		//Guarda el id del componente
		$this->id_ci = $this->ci->get_id();		
		$props = get_object_vars($this);
		unset($props['ci']);
		return array_keys($props);
	}
	
	function __wakeup()
	{
		$id = array('componente' => $this->id_ci[1], 'proyecto' => $this->id_ci[0]);
		$this->ci = toba_constructor::buscar_runtime($id);
	}

}

class objeto_automatico extends toba_serializar_propiedades 
{
	public $nombre;
	public $ci;
	
	function __construct($nombre, $ci)
	{
		$this->nombre = $nombre;
		$this->ci = $ci;
	}
}

?>