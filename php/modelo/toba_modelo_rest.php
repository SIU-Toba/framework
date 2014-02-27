<?php

class toba_modelo_rest extends toba_modelo_elemento
{
	protected $proyecto;

	
	function __construct(toba_modelo_proyecto $proyecto)
	{		
		$this->proyecto = $proyecto;		
		$this->db = $this->proyecto->get_db();
	}

    /**
     * @param toba_modelo_proyecto $proyecto
     * @return toba_ini
     */
    static function get_ini_server(toba_modelo_proyecto  $proyecto)
    {
        $directorio = $proyecto->get_dir_instalacion_proyecto(). "/rest_serv";		//Directorio perteneciente al servicio
        toba_manejador_archivos::crear_arbol_directorios($directorio, 0755);
        $ini = new toba_ini($directorio.'/rest.ini');
        return $ini;
    }

    /**
     * @param toba_modelo_proyecto $proyecto
     * @return toba_ini
     */
    static function get_ini_usuarios(toba_modelo_proyecto  $proyecto)
    {
        $directorio = $proyecto->get_dir_instalacion_proyecto(). "/rest_serv";		//Directorio perteneciente al servicio
        toba_manejador_archivos::crear_arbol_directorios($directorio, 0755);
        $ini = new toba_ini($directorio.'/rest_usuarios.ini');
        return $ini;
    }

	//------------------------------------------------------------------------------------------------------------------//

}
?>
