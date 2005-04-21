<?
/*
	Hay que ordenar la jerarquia de EIs, 
		- Todos tienen que heredar de EI
		- El cuadro tiene que heredar del EI cuadro.
		
		- El CI es un EI?????

*/
class objeto_ei extends objeto
{
	protected $observador;
	
	function __construct($id)
	{
		parent::__construct($id);	
	}	

}

?>