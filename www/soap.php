<?
################################################################################
#	Acceso a una solicitud soap
###############################################################################
# Usuario anonimo. Si no se desee permitir accesos anonimos, dejar vacio ("")
define("apex_pa_usuario_anonimo","toba");//Escribir el nombre de un usuario existente en la base
#(Una misma sesion no deberia manejarse desde dos puntos de acceso)
define("apex_pa_ID",$_SERVER["SCRIPT_FILENAME"]);
# apex_pa_instancia: Instancia a la que el punto de acceso debe conectarse
define("apex_pa_instancia","desarrollo"); //OLMEDO1
# apex_proyecto: Proyecto PRIMARIO
define("apex_pa_proyecto","toba");  //Tiene que ser un proyecto si o si
# apex_nombre: Nombre del sistema
define("apex_pa_registrar_solicitud","db");// VALORES POSIBLES: nunca, siempre, db
#---- Guarda la cronometracion de la generacion del item
define("apex_pa_registrar_cronometro","db");//VALORES POSIBLES: nunca, siempre, db
#---- FORZAR Pagina INICIAL. Atencion al SEPARADOR
define("apex_pa_item_inicial","toba||/red/echo");//Pagina inicial. 
#---- NIVEL de ACCESO minimo permitido
define("apex_pa_nivel_acceso_item","0");//Nivel de ITEMs que se pueden solicitar
define("apex_pa_nivel_acceso_usuario","0");//Nivel de USUARIOS
# administrador
define("apex_pa_administrador","jbordon@siu.edu.ar");
################################################################################
//Llamo al script que genera la SOLICITUD!!!
require_once("nucleo/acceso_soap.php"); 
################################################################################
?>
