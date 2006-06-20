<?/*
SISTEMA de COMUNICACION ASINCRONICA sin recargar la pagina del BROWSER

*** CODIGO PROVEEDOR *** (Resolucion de la respuesta)

El item que utiliza esta actividad DEBE utilizar "comunicacion_JS" como TIPO de PAGINA
*/

//1) Busco los parametros enviados
$parametros = $_POST['parametros'];

//2) Genero la respuesta
responder("Soy el servidor, recibi los parametros: $parametros");

?>