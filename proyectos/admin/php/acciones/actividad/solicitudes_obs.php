<?
# ########################################################################### 
// Adjunto al where el codigo de sesion browser
$where = null;

 if($solicitud = $this->hilo->obtener_parametro("solicitud")){
     $where[] = "sob.solicitud = '$solicitud'";
     }

 $cuadro = $this->cargar_objeto("objeto_cuadro", 0);
 if($cuadro > -1)
{
     $this->objetos[$cuadro]->cargar_datos($where);
     enter();
     $this->objetos[$cuadro]->obtener_html();
     enter();
     }else{
     echo ei_mensaje("No se pudo cargar el cuadro asociado");
     }
?> 		
