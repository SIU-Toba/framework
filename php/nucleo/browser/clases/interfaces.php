<?

interface recorrible_como_arbol
{
	public function hijos();
	public function es_hoja();
	
	public function iconos();
	public function utilerias();
	
	public function nombre_corto();
	public function nombre_largo();	
	public function id();
	public function tiene_propiedades();
	
}

interface ei
{
	public function inicializar();				//Inicializa al elemento
    public function agregar_observador();		//Se le agrega un observador al EI
    public function eliminar_observador();		//Se le extrae un observador al EI
    public function procesar_eventos();			//Sele pide al EI que procese informacion
	//public function recuperar_interaccion();	//El elemento recupera la interaccion con el cliente
    //public function validar_estado();			//Valida su estado interno
}


?>