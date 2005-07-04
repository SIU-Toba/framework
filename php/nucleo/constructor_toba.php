<?
/*
	Creacion de componentes

		Este elemento tiene que recuperar la definicion del objeto e insertarsela.
			- El objeto no debe saber de donde vino la definicion
			- El objeto tiene que permitir crear su definicion despues del constructor
				- set_metadatos()			// Setear los metadatos en bloque
				- validar_metadatos()		// Validar que los metadatos esten OK
				- inicializar()				// Crear estructuras internas basadas en metadatos
				- set_metadato_x()			// Funciones que setean cosas especificas
			- El objeto necesita un metodo para agregar una dependencia
		Tabla de objetos creados? (un canal para que un objeto le pueda mandar un mensaje a otro)

*/
class constructor_toba
{
	static function get_objeto($id, $parametros=null, $clase=null, $archivo=null)
	{
		if(!isset($archivo) || !isset($clase))
		{
			//Busco la informacion que necesaria para crearlo
			$info = self::get_info_objeto($id);
			if(!isset($info['clase_archivo']) || !isset($info['clase'])){
				//Utilizo la subclase
				$archivo = $info['subclase_archivo'];
				$clase = $info['subclase'];
			}
			elseif(!isset($info['clase_archivo']) || !isset($info['clase'])){
				//Utilizo la clase
				$archivo = $info['clase_archivo'];
				$clase = $info['clase'];
			}
		}
		require_once($archivo);
		$objeto = new $clase($id, $parametros);
		return $objeto;
	}

	static function get_info_objeto()
	/*
		Todavia no se necesito el caso
	*/
	{
		return $info;		
	}

}



?>