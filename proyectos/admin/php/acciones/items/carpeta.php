<?
	if($editable = $this->zona->get_editable()){
	//--> Estoy navegando la ZONA con un editable...
		//$this->info();
		//$this->obtener_info_objetos();
		if($editable[1]!=""){//El item cargado no es la RAIZ
			$abm = $this->cargar_objeto("objeto_mt_abms",0);
			if($abm > -1){
				$this->objetos[$abm]->procesar($editable);
				//Si el ABM acaba de procesar una BAJA, no tengo que mostrar la ZONA!
				if($this->objetos[$abm]->obtener_etapa()=="PM-D"){
					//Como el editable que se cargo ya no existe mas, solo muestro el ABM
					$this->objetos[$abm]->obtener_html();
					//Si la lista de la izquierda concuerda con la del EDITABLE eliminado
					//tengo que refrescarla para que refleje el estado de la base
				}else{
/*	Si cambio la carpeta hay que refrescar el menu del costado.
	Hay que pedirle los datos al ABM y compararlos con los de la zona
	
					if(){
						if($this->objetos[$abm]->obtener_etapa()=="PM-U"){
							$this->zona->refrescar_listado_editable_apex();
						}
					}
*/
					//$this->zona->info();
					$this->zona->obtener_html_barra_superior();
					$this->objetos[$abm]->obtener_html();
					$this->zona->obtener_html_barra_inferior();
				}
			}else{
				echo ei_mensaje("No fue posible instanciar el ABM (1)");
			}
		}else{
			//La RAIZ no necesita editor de ningun tipo.
			//Queda igual en su zona, donde se pueden dejar notas, informacion, etc
			//(La raiz puede ser signigicativa en la generacion de manuales)
			$this->zona->obtener_html_barra_superior();
		}
	}else{
	//--> NO estoy navegando en la ZONA con un editable
		$abm = $this->cargar_objeto("objeto_mt_abms",0);
		if($abm > -1){
			//Seteo de parametros a la fuerza
			$parametros = toba::get_hilo()->obtener_parametros();
			if( (isset($parametros['padre_i'])) && 
				(isset($parametros['padre_p'])) ){
				$init = array(	"item" => $parametros['padre_i']."/",
								"padre" => array("padre_proyecto"=>$parametros['padre_p'],
													"padre"=> $parametros['padre_i']),
								"proyecto" => editor::get_proyecto_cargado() );
				//ei_arbol($init,"Inicializacion ABM");
				$this->objetos[$abm]->cargar_estado_ef($init);
			}
			$this->objetos[$abm]->procesar();
			//Si la el ABM acaba de procesar un ALTA, tengo que cargar la ZONA!
			if($this->objetos[$abm]->obtener_etapa()=="PA"){
				//Si salio todo OK
				if($this->objetos[$abm]->obtener_estado_proceso()=="OK"){
					//Obtengo el ID del registro actual
					$clave_registro = $this->objetos[$abm]->obtener_clave();
					if($this->zona->cargar_editable($clave_registro)){
						//$this->zona->info();
						$this->zona->obtener_html_barra_superior();
						$this->objetos[$abm]->obtener_html();					
						$this->zona->obtener_html_barra_inferior();
						//Si la lista de la izquierda concuerda con la del EDITABLE ingresado
						//tengo que refrescarla para que refleje el estado de la base
					}else{
						//ei_mensaje("No fue posible cargar la zona","error");
					}
				}else{
					$this->objetos[$abm]->obtener_html();					
				}
			}else{
				$this->objetos[$abm]->obtener_html();
			}
		}else{
			echo ei_mensaje("No fue posible instanciar el ABM (2)");
		}
	}
?>