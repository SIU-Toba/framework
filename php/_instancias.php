<?
#-------------------------------------------------------------------------------
# INSTACIA de DESARROLLO
#-------------------------------------------------------------------------------

    $instancia["desarrollo"][apex_db_motor] = "postgres7";
    $instancia["desarrollo"][apex_db_profile] = "";
    $instancia["desarrollo"][apex_db_usuario] = "";
    $instancia["desarrollo"][apex_db_clave] = "";
    $instancia["desarrollo"][apex_db_base] = "toba";

#-------------------------------------------------------------------------------
# INSTACIA de PRUEBAS
#-------------------------------------------------------------------------------

    $instancia["prueba"][apex_db_motor] = "postgres7";
    $instancia["prueba"][apex_db_profile] = "";
    $instancia["prueba"][apex_db_usuario] = "";
    $instancia["prueba"][apex_db_clave] = "";
    $instancia["prueba"][apex_db_base] = "prueba";

#-------------------------------------------------------------------------------
# INSTACIA de PRODUCCION
#-------------------------------------------------------------------------------

    $instancia["produccion"][apex_db_motor] = "postgres7";
    $instancia["produccion"][apex_db_profile] = "";
    $instancia["produccion"][apex_db_usuario] = "";
    $instancia["produccion"][apex_db_clave] = "";
    $instancia["produccion"][apex_db_base] = "toba_produccion";
  
#-------------------------------------------------------------------------------
# Constantes comunes a todas las intacias
#-------------------------------------------------------------------------------
define("apex_clave_get","x6B2AHNQRqUW9");//Parametros opacos por GET
define("apex_clave_db","dQFpBmBTJFF+By");//Informacion opaca en la base
define('apex_id_grupo_desarrollo', 0);		//Identifica univocamente al grupo de desarrollo
											//Permite que varios grupos trabajen sobre un mismo proyecto en forma remota
											//si que las secuencias de las tablas de Toba de cada uno entren en conflicto
#-------------------------------------------------------------------------------
?>