<?php
require_once("objeto_mt_s.php");
require_once("objeto_ut_formulario.php");

/*
	Mo se pueden modificar CLAVES!!
*/

class objeto_mt_abms extends objeto_mt_s
/*
 	@@acceso: nucleo
	@@desc: Descripcion
*/
{
	var $etapa_actual;		// interno | string | Etapa en la que se encuentra la transaccion
	var $submit_eli;		// interno | string | Etiqueta del boton de ELIMINAR
	var $submit_mod;		// interno | string | Etiqueta del boton de MODIFICACION
	var $submit_limpiar; 	// interno | string | Etiqueta del boton de LIMPIAR
	
	function objeto_mt_abms($id)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{
		parent::objeto_mt_s($id);
		$this->submit_eli = "&Eliminar";
		$this->submit_mod = "&Modificar";
		$this->submit_limpiar = "&Limpiar formulario";
	}
	//-------------------------------------------------------------------------------

	function destruir()
	{
		parent::destruir();
		$this->memorizar();
	}
	//-------------------------------------------------------------------------------

	function info_definicion()
/*
 	@@acceso: actividad
	@@desc: Da informacion sobre la DEFINICION objeto
*/
	{
		return $this->dependencias["formulario"]->info_definicion();
	}
	//-------------------------------------------------------------------------------

	function exportar_definicion_php()
/*
 	@@acceso:
	@@desc: 
*/
	{
		return $this->dependencias["formulario"]->exportar_definicion_php();
	}
	//-------------------------------------------------------------------------------

	function cargar_dependencias()
/*
 	@@acceso: interno
	@@desc: Carga los UT de los que depende este MT y los INICIALIZA
	@@pendiente: Inhabilitar la posibilidad de que se modifique la clave del padre.
*/
	{
		$this->dependencias["formulario"] =& new objeto_ut_formulario($this->id);
		$parametro["nombre_formulario"] = $this->nombre_formulario;
		$this->dependencias["formulario"]->inicializar($parametro);
	}



	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------  PROCESOS  -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

    function procesar($clave=null, $memorizar=false)
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
			$this->dependencias["formulario"]->limpiar_interface();
			$this->procesar_etapa_SA();
		}else{
			if(isset($this->canal_recibidos))// ( 1 ) Se recibio una CLAVE por el CANAL!
			{
				//La entrada por el CANAL  fuerza el estado SM, mas alla de la memoria
				$clave = explode(apex_qs_separador,$this->canal_recibidos);		
				$clave = $this->dependencias["formulario"]->formatear_clave($clave);
				//ei_arbol($clave,"CLAVE CANAL");
				$this->procesar_etapa_SM($clave);
			}
			else 							// ( 2 ) El CANAL se encuentra VACIO
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
                                //Si solicite que la clave fuera memorizada tengo que seguir en el estado "SM" 
                                //porque el parametro viene por memoria
                                if ($memorizar === true)     
                                {
                                    $this->procesar_etapa_SM($this->memoria['clave']);
                                }
                                else
                                {
    								$this->procesar_etapa_SA();
                                }
							}
						}
					}
	        	}else 							// ( 2.2 ) NO Existe un ESTADO PREVIO
				{
					if(isset($clave)){ //	Se paso el ID de un registro como parametro???
						$this->procesar_etapa_SM($clave);
					}else{
						$this->procesar_etapa_SA();
					}
		        }
		    }
		}
		//Memorizo el estado para la proxima instanciacion
		$this->memorizar();
		$this->procesar_daos();
	}
	//-------------------------------------------------------------------------------
	
	function procesar_daos()
	{
		if( $dao_form = $this->dependencias["formulario"]->obtener_consumo_dao() ){
			/*
			foreach($dao_form as $ef => $dao){
				$sentencia = "\$datos = \$this->cn->{$dao}();";
				//echo $sentencia;
				eval($sentencia);
				//ei_arbol($datos,"DATOS $ef");
				//El cuadro carga sus daos de otra forma
				$this->dependencias[$dep]->ejecutar_metodo_ef($ef,"cargar_datos",$datos);
			}*/
		}		
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
			//Se pueden modificar las claves?
			$this->control_modificacion_claves();
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
		$this->dependencias["formulario"]->procesar_dependencias();
		$this->pre_insert();
        if( $this->validar_estado() ) // Validacion OK
        {
            if( $this->iniciar_transaccion() ) //Comienzo la TRANSACCION
            {
                //-[1]- Inserto el MAESTRO
                $sql = $this->dependencias["formulario"]->obtener_sql("insert");
                
                //ei_arbol($sql,"Datos maestro");
                if($this->on_insert($sql) && $this->ejecutar_sql($sql) ){//MAESTRO OK
                    $this->post_insert();
                    //Recupero secuencias en el maestro
                    $this->dependencias["formulario"]->actualizacion_post_insert();
                    //Obtengo la clave
                    $clave_maestro = $this->dependencias["formulario"]->obtener_clave();
                    $this->memoria["clave"] = $clave_maestro;
                    $this->estado_proceso = "OK";//-------------> Termino todo OK
                    $this->memoria["proxima_etapa"] = "PM";
                    $this->finalizar_transaccion();
                    if($this->dependencias["formulario"]->info_formulario["auto_reset"]){
                        unset($this->memoria);
                        $this->memoria["proxima_etapa"] = "PA";
                        $this->dependencias["formulario"]->limpiar_interface();//Limpio el FORM
                    }else{
                        //Se pueden modificar las claves?
                        $this->control_modificacion_claves();
                    }
                }else{ //ERROR
                    $this->abortar_transaccion("Error INSERTANDO el registro");
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
		$this->dependencias["formulario"]->procesar_dependencias();		
        if( $_POST[$this->submit]==$this->submit_mod)//            ( 1 ) MODIFICAR
        {
            $this->etapa_actual = "PM-U";
            if( $this->validar_estado() ) // Validacion OK
            {
                if( $this->iniciar_transaccion() ) //Comienzo la TRANSACCION
                {
                    $this->pre_update();
                    $sql = $this->dependencias["formulario"]->obtener_sql("update");
      
                    //---Empieza la ejecucion
                    if($this->on_update($sql) && $this->ejecutar_sql($sql) ){
                        $clave = $this->dependencias["formulario"]->obtener_clave();
                        $this->memoria["clave"] = $clave;
						$this->post_update();
                        $this->finalizar_transaccion();
                        if($this->dependencias["formulario"]->info_formulario["auto_reset"]){
                            unset($this->memoria);
                            $this->memoria["proxima_etapa"] = "PA";
                            $this->dependencias["formulario"]->limpiar_interface();//Limpio el FORM
                        }else{
                            //Se pueden modificar las claves?
                            $this->control_modificacion_claves();
                        }
                    }else{ //ERROR UPDATE
                        $this->memoria["proxima_etapa"] = "PM";
                        //Se pueden modificar las claves?
                        $this->control_modificacion_claves();
                        $this->abortar_transaccion("Error MODIFICANDO el registro");
                        $this->estado_proceso = "ERROR";
                    }

                }else{  //La transaccion no se inicio
                    $this->memoria["proxima_etapa"] = "PM";
                    $this->estado_proceso = "ERROR";
                    //Se pueden modificar las claves?
                    $this->control_modificacion_claves();
                }
            }
            else{   // Error en la validacion
                $this->memoria["proxima_etapa"] = "PM";
                $this->estado_proceso = "ERROR";
                //Se pueden modificar las claves?
                $this->control_modificacion_claves();
            }
        }
        elseif(( $_POST[$this->submit]==$this->submit_eli)//        ( 2 ) ELIMINAR
            && ($this->dependencias["formulario"]->permitir_eliminar() ))
        {
            $this->etapa_actual = "PM-D";
            if( $this->iniciar_transaccion() ) //Comienzo la TRANSACCION
            {
                $this->pre_delete();
                $sql = $this->dependencias["formulario"]->obtener_sql("delete");
                
                if($this->on_delete($sql) && $this->ejecutar_sql($sql) ){ //OK
                    $this->finalizar_transaccion();// Fin TRANSACCION
                    $this->estado_proceso = "OK";//-------------> Termino todo OK
                    $this->dependencias["formulario"]->limpiar_interface();
                    $this->memoria["proxima_etapa"] = "PA";
                }else{ 
                        //ERROR Eliminando
                        $this->memoria["proxima_etapa"] = "PM";
                        $this->abortar_transaccion("Error ELIMINANDO el registro");
                        $this->estado_proceso = "ERROR";
						$this->control_modificacion_claves();						
                }
				$this->post_delete();

            }else{  //La transaccion no se inicio
                $this->memoria["proxima_etapa"] = "PM";
                $this->estado_proceso = "ERROR";
				$this->control_modificacion_claves();
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
        if (isset($this->memoria["clave"])) 
        {
    		return $this->memoria["clave"];	
        }
		return null;	
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
	@@retorno: boolean | true si se cargo el registro, false en el caso contrario
*/
	{
		//Cargo el MAESTRO
		$status = $this->dependencias['formulario']->cargar_db( $clave );
		return $status;
	}

	//-------------------------------------------------------------------------------
	//----------  EMULACION de UT_FORMULARIO   --------------------------------------
	//-------------------------------------------------------------------------------
	
	function limpiar_interface()
	{
		return $this->dependencias["formulario"]->limpiar_interface();
	}

	function cargar_estado_ef($datos_ef)
/*
 	@@acceso: actividad
	@@desc: Carga el estado de de un EF de una UT
*/
	{
		$this->dependencias["formulario"]->cargar_estado_ef($datos_ef);
	}	
	//-------------------------------------------------------------------------------
	
	function obtener_datos()
/*
 	@@acceso: actividad
	@@desc: Carga el estado de de un EF de una UT
*/
	{
		return $this->dependencias["formulario"]->obtener_datos();
	}	
	//-------------------------------------------------------------------------------
	
	function obtener_nombres_ef()
/*
 	@@acceso: actividad
	@@desc: Carga el estado de de un EF de una UT
*/
	{
		return $this->dependencias["formulario"]->obtener_nombres_ef();
	}	
	//-------------------------------------------------------------------------------

	function	ejecutar_metodo_ef($ef,	$metodo,	$parametro=null)
/*
	@@acceso: actividad
	@@desc: Esto sirve para	comunicarse	con EF que pueden	cambiar en tiempo	de	ejecucion
	@@desc: EJ:	un	combo	que necesita cambiar	una propiedad del	WHERE	segun	la	solicitud
	@@param:	string |	elemento	de	formulario a llamar
	@@param:	string |	metodo a	llamar en el EF
	@@param:	array	| Argumentos de la funcion
*/
	{
		$this->dependencias["formulario"]->ejecutar_metodo_ef($ef,$metodo,$parametro);
	}
	//-------------------------------------------------------------------------------

	function control_modificacion_claves()
/*
	@@acceso: actividad
	@@desc: Inhabilita la modificacion de claves.
*/
	{
		//Si las claves no se pueden modificar, inhabilito los EF que las contienen
		if($this->dependencias["formulario"]->info_formulario["ev_mod_clave"]!=1){
			$this->dependencias["formulario"]->inhabilitar_modificacion_claves();
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------------------------  SALIDA  ------------------------------------
	//-------------------------------------------------------------------------------
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
			$acceso = tecla_acceso("&Agregar");
			echo form::submit($this->submit,$acceso[0],"abm-input", '', $acceso[1]);
		}elseif($this->memoria["proxima_etapa"]=="PM"){
			//Esto es para solucionar un BUG del IE con los <button>
			echo form::hidden($this->submit, '');
			if($this->dependencias["formulario"]->info_formulario["ev_mod_limpiar"]){
				$acceso = tecla_acceso($this->submit_limpiar);
				echo "&nbsp;&nbsp;" . form::button("boton", $acceso[0] ,"onclick=\"document.location.href='".$this->solicitud->vinculador->generar_solicitud(null,null,array($this->flag_no_propagacion=>1),true)."';\"",
													"abm-input", $acceso[1]);
			}
			$acceso = tecla_acceso($this->submit_mod);
			echo "&nbsp;&nbsp;" . form::submit($this->submit."_mod", $acceso[0], "abm-input", 
								  "onclick='{$this->nombre_formulario}.{$this->submit}.value = \"{$this->submit_mod}\"'", $acceso[1]);
			if($this->dependencias["formulario"]->permitir_eliminar()===true) {
				$acceso = tecla_acceso($this->submit_eli);
				echo  "&nbsp;&nbsp;";
				echo form::submit($this->submit."_eli", $acceso[0], "abm-input-eliminar", 
								" onclick='eliminar_{$this->nombre_formulario}=1; {$this->nombre_formulario}.{$this->submit}.value = \"{$this->submit_eli}\"' ",
								$acceso[1]);
			}
		}else{
			echo "Atencion: la proxima etapa no se encuentra definida!";
		}
		echo "</td></tr>\n";
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------
	
    function pre_insert()
/*
    @@acceso: interno
    @@desc: Realiza operaciones previas a la insercion... se debe redefinir en los hijos.
*/
    {
        return true;
    }

    //-------------------------------------------------------------------------------

    function post_insert()
/*
    @@acceso: interno
    @@desc: Realiza operaciones posteriores a la insercion... se debe redefinir en los hijos.
*/
    {
        return true;
    }

    //-------------------------------------------------------------------------------

    function on_insert(& $sql)
/*
    @@acceso: interno
    @@desc: Realiza operaciones posteriores a la insercion... se debe redefinir en los hijos.
*/
    {
        return true;
    }
    
    //-------------------------------------------------------------------------------

    function pre_delete()
/*
    @@acceso: interno
    @@desc: Realiza operaciones previas a la eliminacion... se debe redefinir en los hijos.
*/
    {
        return true;
    }

    //-------------------------------------------------------------------------------

    function post_delete()
/*
    @@acceso: interno
    @@desc: Realiza operaciones posteriores a la modificacion ... se debe redefinir en los hijos.
*/
    {
        return true;
    }

    //-------------------------------------------------------------------------------

    function on_delete(& $sql)
/*
    @@acceso: interno
    @@desc: Realiza operaciones durante la eliminacion... se debe redefinir en los hijos.
*/
    {
        return true;
    }
    
    //-------------------------------------------------------------------------------

    function pre_update()
/*
    @@acceso: interno
    @@desc: Realiza operaciones previas a la modificacion... se debe redefinir en los hijos.
*/
    {
        return true;
    }

    //-------------------------------------------------------------------------------

    function post_update()
/*
    @@acceso: interno
    @@desc: Realiza operaciones posteriores a la modificacion ... se debe redefinir en los hijos.
*/
    {
        return true;
    }

    //-------------------------------------------------------------------------------

    function on_update(& $sql)
/*
    @@acceso: interno
    @@desc: Realiza operaciones durante la modificacion ... se debe redefinir en los hijos.
*/
    {
        return true;
    }
 	
}
?>