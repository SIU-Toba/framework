<?

interface ei
{
	public function inicializar();				//Inicializa al elemento
    public function agregar_observador();		//Se le agrega un observador al EI
    public function eliminar_observador();		//Se le extrae un observador al EI
    public function procesar_eventos();			//Sele pide al EI que procese informacion
	//public function recuperar_interaccion();	//El elemento recupera la interaccion con el cliente
    //public function validar_estado();			//Valida su estado interno
}

interface ci
{
	public function inicializar();
	public function procesar_eventos();
	public function registrar_evento($id, $evento, $parametros);
}

?>