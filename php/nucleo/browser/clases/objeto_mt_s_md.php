<?php
require_once("objeto_mt_s.php");    //Ancestro de todos los OE

class objeto_mt_mds extends objeto_mt_s
/*
    @@acceso: nucleo
    @@desc: Descripcion
*/
{
    var $ut_detalle;        // interno | array | Lista de UTs que que seran manejados como detalle
    var $etapa_actual;      // interno | string | Etapa en la que se encuentra la transaccion
    var $submit_eli;        // interno | string | Etiqueta del boton de ELIMINAR
    var $submit_mod;        // interno | string | Etiqueta del boton de MODIFICACION

    function objeto_mt_mds($id)
/*
    @@acceso: nucleo
    @@desc: Muestra la definicion del OBJETO
*/
    {
        parent::objeto_mt_s($id);
        $this->submit_eli = "Eliminar";
        $this->submit_mod = "Modificar";
    }
    //-------------------------------------------------------------------------------

    function cargar_dependencias()
/*
    @@acceso: interno
    @@desc: Carga los UT de los que depende este MT y los INICIALIZA
    @@pendiente: Inhabilitar la posibilidad de que se modifique la clave del padre.
*/
    {
        //Cargo las dependencias
        parent::cargar_dependencias();
        //Armo la lista de detalles
        if(is_array($this->indice_dependencias)){
            $this->ut_detalle = array();
            foreach(array_keys($this->indice_dependencias) as $dep){
                //La clase tiene definido en sus metadatos las
                //dependencias detalle como: detalle_1, detalle_2, etc.
                if(strpos($dep,"detalle")!==false){
                    //Atencion, se supone que ya estan ordenados como corresponde
                    $this->ut_detalle[] = $dep;
                }
            }
        }
    }
    //-------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------
    //--------------------------------  PROCESOS  -----------------------------------
    //-------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------

    function procesar($clave=null)
/*
    @@acceso: actividad
    @@desc: Procesa la transaccion. Determina en que etapa esta y ejecuta los pasos adecuados para resolverla
    @@param: array | CLAVE del registro del MAESTRO que se desea cargar
*/
    {
        //El flag de no propagacion del estado, lleva al MT al estado inicial
        if($this->solicitud->hilo->obtener_parametro($this->flag_no_propagacion))
        {
            $this->borrar_memoria();
            $this->procesar_etapa_SA();
        }else{
            if(isset($this->canal_recibidos))// ( 1 ) Se recibio una CLAVE por el CANAL!
            {
                //La entrada por el CANAL  fuerza el estado SM, mas alla de la memoria
                $clave = explode(apex_qs_separador,$this->canal_recibidos);
                //ei_arbol($clave,"CLAVE CANAL");
                $this->procesar_etapa_SM($clave);
            }
            else                            // ( 2 ) El CANAL se encuentra VACIO
            {
                if(isset($this->memoria["proxima_etapa"])) // ( 2.1 ) Exite un ESTADO PREVIO
                {
                    if($this->memoria["proxima_etapa"]=="PA"){// ( 2.1.1 ) Procesar ALTA
                        if( $this->controlar_activacion() === true ){
                            $this->procesar_etapa_PA($clave);
                        }else{
                            //Se activo OTRO elemento de la INTERFACE
                            //La intanciacion previa del objeto preparo la situacion para realizar el ALTA
                            //Pero no se envio el formulario. Retorno al estado de Solicitud de ALTA
                            $this->procesar_etapa_SA();
                        }
                    }elseif ($this->memoria["proxima_etapa"]=="PM"){// ( 2.1.2 ) Procesar MODIFICACION
                        if( $this->controlar_activacion() === true ){
                            $this->procesar_etapa_PM();
                        }else{
                            //Se activo OTRO elemento de la INTERFACE
                            //( La intanciacion previa del objeto preparo la situacion para realizar la MODIFICACION
                            //Pero no se envio el formulario. Retorno al estado de Solicitud de MODIFICACION
                            //Mientras se activan otros objetos, reprodusco el estado )
                            if(isset($clave)){
                                //El estado se mantiene por seteo directo
                                $this->procesar_etapa_SM($clave);
                            }else{
                                $this->procesar_etapa_SA();
                            }
                        }
                    }
                }else                           // ( 2.2 ) NO Existe un ESTADO PREVIO
                {
                    if(isset($clave)){ //   Se paso el ID de un registro como parametro???
                        $this->procesar_etapa_SM($clave);
                    }else{
                        $this->procesar_etapa_SA();
                    }
                }
            }
        }
        //Memorizo el estado para la proxima instanciacion
        $this->memorizar();
    }

    //-------------------------------------------------------------------------------
    //----------------------------------  ETAPAS  -----------------------------------
    //-------------------------------------------------------------------------------

    function procesar_etapa_SA()
/*
    @@acceso: interno
    @@desc: SOLICITUD de ALTA. Estado INICIAL por defecto
    @@param:
*/
    {
        $this->etapa_actual = "SA";
        $this->memoria["proxima_etapa"] = "PA";
        $this->estado_proceso = "OK";
    }
    //-------------------------------------------------------------------------------

    function procesar_etapa_SM($clave)
/*
    @@acceso: interno
    @@desc: Etapa SOLICITUD de MODIFICACION. Busca el registro de la base y lo carga en la interface
    @@param:
*/
    {
        $this->etapa_actual = "SM";
        //SI no se activa la siguente instanciacion, hay que recordar que se esta modificando
        if ( $this->cargar_db($clave) ){
            $this->memoria["clave"] = $clave;
            $this->memoria["proxima_etapa"] = "PM";
            //Puede ser que reglas nuevas no se cumplan en datos viejos!
            //No paro la ejecucion, pero que se muestren los mensajes.
            $this->validar_estado();
            $this->estado_proceso = "OK";
        }else{
            $this->procesar_etapa_SA();
        }
    }
    //-------------------------------------------------------------------------------

    function procesar_etapa_PA()
/*
    @@acceso: interno
    @@desc: Etapa PROCESAR ALTA
*/
    {
        $this->etapa_actual = "PA";
        $this->cargar_post();
        if( $this->validar_estado() ) // Validacion OK
        {
            if( $this->iniciar_transaccion() ) //Comienzo la TRANSACCION
            {
                //-[1]- Inserto el MAESTRO
                $sql = $this->dependencias["maestro"]->obtener_sql("insert");
                //ei_arbol($sql,"Datos maestro");
                if( $this->ejecutar_sql($sql) ){//MAESTRO OK
                    //Recupero secuencias en el maestro
                    $this->dependencias["maestro"]->actualizacion_post_insert();
                    //Obtengo la clave
                    $clave_maestro = $this->dependencias["maestro"]->obtener_clave();
                    $this->memoria["clave"] = $clave_maestro;
                    //Asigno la clave del MAESTRO a los DETALLES
                    $this->ut_detalle_asignar_clave_maestro($clave_maestro);
                    //-[2]- Inserto los DETALLES
                    if($this->ut_detalle_procesar_sql()){
                        //Recupero secuencias en los DETALLES
                        $this->finalizar_transaccion();// Fin TRANSACCION
                        foreach($this->ut_detalle as $detalle){
                            $this->dependencias[$detalle]->actualizacion_post_insert();
                        }
                        $this->estado_proceso = "OK";//-------------> Termino todo OK
                        $this->memoria["proxima_etapa"] = "PM";
                    }else{ //ERROR DETALLE
                        $this->abortar_transaccion("Error INSERTANDO los DETALLES");
                        $this->estado_proceso = "ERROR";
                        $this->memoria["proxima_etapa"] = "PA";
                    }
                }else{ //ERROR MAESTRO
                    $this->abortar_transaccion("Error INSERTANDO el registro MAESTRO");
                    $this->estado_proceso = "ERROR";
                    $this->memoria["proxima_etapa"] = "PA";
                }
            }else{  //La transaccion no se inicio
                $this->estado_proceso = "ERROR";
                $this->memoria["proxima_etapa"] = "PA";
            }
        }
        else{   // Error en la validacion
            $this->memoria["proxima_etapa"] = "PA";
            $this->estado_proceso = "ERROR";
        }
    }
    //-------------------------------------------------------------------------------

    function procesar_etapa_PM()
/*
    @@acceso: interno
    @@desc: PROCESAR MODIFICACION
*/
    {
        $this->etapa_actual = "PM";
        $this->cargar_post();
        if( $_POST[$this->submit]==$this->submit_mod )//            ( 1 ) MODIFICAR
        {
            $this->etapa_actual = "PM-U";
            if( $this->validar_estado() ) // Validacion OK
            {
                if( $this->iniciar_transaccion() ) //Comienzo la TRANSACCION
                {
                    //-[1]- Modifico el MAESTRO
                    $clave = $this->memoria['clave'];
                    $sql = $this->dependencias["maestro"]->obtener_sql("update");
                    //ei_arbol($sql,"Datos maestro");
                    if( $this->ejecutar_sql($sql) ){ //MAESTRO OK
                        $clave_maestro = $this->dependencias["maestro"]->obtener_clave();
                        //Asigno la clave del MAESTRO a los DETALLES
                        $this->ut_detalle_asignar_clave_maestro($clave);
                        if($this->ut_detalle_procesar_sql()){
                            $this->finalizar_transaccion();// Fin TRANSACCION
                            $this->estado_proceso = "OK";//-------------> Termino todo OK
                            $this->memoria["proxima_etapa"] = "PM";
                        }else{ //ERROR DETALLE
                            $this->memoria["proxima_etapa"] = "PM";
                            $this->abortar_transaccion("Error MODIFICANDO los DETALLES");
                            $this->estado_proceso = "ERROR";
                        }
                    }else{ //ERROR MAESTRO
                        $this->memoria["proxima_etapa"] = "PM";
                        $this->abortar_transaccion("Error MODIFICANDO el registro MAESTRO");
                        $this->estado_proceso = "ERROR";
                    }
                }else{  //La transaccion no se inicio
                    $this->memoria["proxima_etapa"] = "PM";
                    $this->estado_proceso = "ERROR";
                }
            }
            else{   // Error en la validacion
                $this->memoria["proxima_etapa"] = "PM";
                $this->estado_proceso = "ERROR";
            }
        }
        elseif(( $_POST[$this->submit]==$this->submit_eli)//        ( 2 ) ELIMINAR
            && ($this->dependencias["maestro"]->permitir_eliminar() ))
        {
            $this->etapa_actual = "PM-D";
            if( $this->iniciar_transaccion() ) //Comienzo la TRANSACCION
            {
                //Intento ELIMINAR el MAESTRO
                $sql = $this->dependencias["maestro"]->obtener_sql("delete");
                //ei_arbol($sql,"Datos maestro");
                if( $this->ejecutar_sql($sql) ){ //MAESTRO OK
                    //ATENCION: se supone que los hijos se borrar con un CASCADE!!!
                    $this->finalizar_transaccion();// Fin TRANSACCION
                    $this->estado_proceso = "OK";//-------------> Termino todo OK
                    //Limpio la interface de todos las UT utilizadas
                    foreach(array_keys($this->dependencias) as $ut){
                        $this->dependencias[$ut]->limpiar_interface();
                    }
                    $this->memoria["proxima_etapa"] = "PA";
                }else{ //ERROR Eliminando MAESTRO
                    $this->memoria["proxima_etapa"] = "PM";
                    $this->abortar_transaccion("Error ELIMINANDO el registro MAESTRO");
                    $this->estado_proceso = "ERROR";
                }
            }else{  //La transaccion no se inicio
                $this->memoria["proxima_etapa"] = "PM";
                $this->estado_proceso = "ERROR";
            }
        }
    }
    //-------------------------------------------------------------------------------

    function obtener_etapa()
/*
    @@acceso: actividad
    @@desc: Indica cual es la ETAPA actual
    @@retorno: string | Etapa actual ( SA / SM / PA / PM-U / PM-D )
*/
    {
        return $this->etapa_actual;
    }
    //-------------------------------------------------------------------------------

    function obtener_clave()
/*
    @@acceso: actividad
    @@desc: Indica cual es la CLAVE (del maestro) que se esta procesando
    @@retorno: array | Clave que se esta procesando
*/
    {
        return $this->memoria["clave"];
    }

    //-------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------
    //-------------------------  CONTROL de las UT  ---------------------------------
    //-------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------

    function cargar_db($clave)
/*
    @@acceso: interno
    @@desc: Carga el estado de cada UT desde la base. Deja registrado en que etapa se encuentra
    @@param: array | Array POSICIONAL con los valores que tona la clave
    @@retorno: boolean | true si se cargo el MAESTRO, false en el caso contrario
*/
    {
        //Cargo el MAESTRO
        $status = $this->dependencias['maestro']->cargar_db( $clave );
        if($status){
            //Cargo los detalles
            foreach($this->ut_detalle as $detalle){
                $carga_ok = $this->dependencias[$detalle]->cargar_db( $clave );
                if($carga_ok){
                    $this->memoria["ut_estado"][$detalle] = "update";
                }else{
                    $this->memoria["ut_estado"][$detalle] = "insert";
                }
            }
        }
        $this->memorizar();
        return $status;
    }

    //-------------------------------------------------------------------------------
    //--------------  Procesos sobre las UT en rol de DETALLE  ----------------------
    //-------------------------------------------------------------------------------

    function ut_detalle_asignar_clave_maestro($clave)
/*
    @@acceso: interno
    @@desc: Asigna la clave de la UT-MAESTRO en las UT-DETALLE
*/
    {
        foreach($this->ut_detalle as $detalle){
            $this->dependencias[$detalle]->establece_clave_maestro($clave);
        }
    }
    //-------------------------------------------------------------------------------

    function ut_detalle_procesar_sql()
/*
    @@acceso: interno
    @@desc: Procesa las UT-DETALLE
*/
    {
        foreach($this->ut_detalle as $detalle){
            if($this->etapa_actual=="PA"){
                $tipo_sql = "insert";
            }elseif($this->etapa_actual=="PM-U"){
                //En una modificacion del MAESTRO, los DETALLES pueden ser INSERT o UPDATE!
                if(isset($this->memoria["ut_estado"][$detalle])){
                    $tipo_sql = $this->memoria["ut_estado"][$detalle];
                }else{
                    $this->registrar_info_proceso("No esta definido el tipo de ACCION SQL a desarrollar sobre la UT $detalle","error");
                    return false;
                }
            }
            $sql = $this->dependencias[$detalle]->obtener_sql($tipo_sql);
            if(is_array($sql)){
                if( $this->ejecutar_sql($sql) ){
                    if($this->etapa_actual=="PA"){
                        $this->memoria["ut_estado"][$detalle] = "update";
                    }
                }else{
                    return false;
                }
            }
        }
        return true;
    }

    //-------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------
    //---------------------------------  SALIDA  ------------------------------------
    //-------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------

    function obtener_interface()
/*
    @@acceso: interno
    @@desc: Devuelve la interface del Marco Transaccional
*/
    {
        if(is_object($this->dependencias['maestro'])){
            echo "<table class='tabla-0'>\n";
            echo "<tr><td>";
            //Creo el HTML del MAESTRO
            $this->dependencias['maestro']->obtener_html();
            //Creo el HTML del DETALLE
            if(is_array($this->ut_detalle)){
                foreach($this->ut_detalle as $detalle){
                    $this->dependencias[$detalle]->obtener_html();
                }
            }
            echo "</td></tr>\n";
            echo "<tr><td>";
            echo "</table>\n";
        }else{
            echo ei_mensaje("No se definio una dependencia de TIPO 'maestro'");
        }
    }
    //-------------------------------------------------------------------------------

    function obtener_botones()
/*
    @@acceso: interno
    @@desc: Genera los botones de ABM
*/
    {
        echo "<table class='tabla-0' align='center' width='100%'>\n";
        echo "<tr><td class='abm-zona-botones'>";
        if($this->memoria["proxima_etapa"]=="PA"){
            echo form::submit($this->submit,"Agregar","abm-input");
        }elseif($this->memoria["proxima_etapa"]=="PM"){
            if($this->dependencias["maestro"]->info_formulario["ev_mod_limpiar"]){
                echo "&nbsp;&nbsp;" . form::button("boton","Limpiar formulario","onclick=\"document.location.href='".$this->solicitud->vinculador->generar_solicitud(null,null,array($this->flag_no_propagacion=>1),true)."';\"","abm-input");
            }
            echo "&nbsp;&nbsp;" . form::submit($this->submit, $this->submit_mod, "abm-input");
            if($this->dependencias["maestro"]->permitir_eliminar()===true)
                echo  form::submit($this->submit, $this->submit_eli, "abm-input-eliminar", " onclick='eliminar_{$this->nombre_formulario}=1' ");
        }else{
            echo "Atencion: la proxima etapa no se encuentra definida!";
        }
        echo "</td></tr>\n";
        echo "</table>\n";
    }
    //-------------------------------------------------------------------------------
}
?>