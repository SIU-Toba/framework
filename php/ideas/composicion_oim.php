<?

class composicion_oim extends composicion
{
	function __construct()
	{
		$this->rep['item'] = new representacion_item();
		$this->rep['objeto'] = new representacion_objeto();
		//Definicion de callbacks
		//Definicion de metodos para obtener instancias
	}
	
}


?>