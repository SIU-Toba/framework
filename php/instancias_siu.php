<?
#-------------------------------------------------------------------------------
# INSTACIA de DESARROLLO
#-------------------------------------------------------------------------------

    $instancia["desarrollo"][apex_db_motor] = "postgres7";
    $instancia["desarrollo"][apex_db_profile] = "127.0.0.1";
    $instancia["desarrollo"][apex_db_usuario] = "dba";
    $instancia["desarrollo"][apex_db_clave] = "*dba-";
    $instancia["desarrollo"][apex_db_base] = "toba";

#-------------------------------------------------------------------------------
# COSTOS
#-------------------------------------------------------------------------------
    
    $instancia["toba_costos"][apex_db_motor] = "postgres7";
    $instancia["toba_costos"][apex_db_profile] = "127.0.0.1";
    $instancia["toba_costos"][apex_db_usuario] = "dba";
    $instancia["toba_costos"][apex_db_clave] = "*dba-";
    $instancia["toba_costos"][apex_db_base] = "toba";

    $instancia["costos"][apex_db_motor] = "postgres7";
    $instancia["costos"][apex_db_profile] = "127.0.0.1";
    $instancia["costos"][apex_db_usuario] = "dba";
    $instancia["costos"][apex_db_clave] = "*dba-";
    $instancia["costos"][apex_db_base] = "costos";

#-------------------------------------------------------------------------------
# COMECHINGONES
#-------------------------------------------------------------------------------

    $instancia["toba_comechingones"][apex_db_motor] = "postgres7";
    $instancia["toba_comechingones"][apex_db_profile] = "192.168.123.60";
    $instancia["toba_comechingones"][apex_db_usuario] = "dba";
    $instancia["toba_comechingones"][apex_db_clave] = "*dba-";
    $instancia["toba_comechingones"][apex_db_base] = "toba_comechingones";

    $instancia["comechingones"][apex_db_motor] = "postgres7";
    $instancia["comechingones"][apex_db_profile] = "192.168.123.60";
    $instancia["comechingones"][apex_db_usuario] = "dba";
    $instancia["comechingones"][apex_db_clave] = "*dba-";
    $instancia["comechingones"][apex_db_base] = "comechingonesII_sp21jmb";

#-------------------------------------------------------------------------------
# Constantes comunes a todas las intacias
#-------------------------------------------------------------------------------
define("apex_clave_get","x6B2AHNQRqUW9");//Parametros opacos por GET
define("apex_clave_db","dQFpBmBTJFF+By");//Informacion opaca en la base
#-------------------------------------------------------------------------------
?>