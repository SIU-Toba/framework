<?
	if($editable = $this->zona->obtener_editable_propagado()){
	//--> Estoy navegando la ZONA con un editable...
		//$this->info();
		//$this->obtener_info_objetos();
		$abm = $this->cargar_objeto("objeto_mt_abms",0);
		if($abm > -1){
			$this->objetos[$abm]->procesar($editable);
			//Si el ABM acaba de procesar una BAJA, no tengo que mostrar la ZONA!
			if($this->objetos[$abm]->obtener_etapa()=="PM-D"){
				//Como el editable que se cargo ya no existe mas, solo muestro el ABM
				$this->objetos[$abm]->obtener_html();
				//Si la lista de la izquierda concuerda con la del EDITABLE eliminado
				//tengo que refrescarla para que refleje el estado de la base
				$this->zona->refrescar_listado_editable_apex();
			}else{
				$this->zona->cargar_editable();//Cargo el editable de la zona
				//$this->zona->info();
				$this->zona->obtener_html_barra_superior();
				$this->objetos[$abm]->obtener_html();
				include_once("editor_grupos_asignaritems.php");
				$this->zona->obtener_html_barra_inferior();
			}
		}else{
			echo ei_mensaje("No fue posible instanciar el ABM (1)");
		}
	}else{
	//--> NO estoy navegando en la ZONA con un editable
		$abm = $this->cargar_objeto("objeto_mt_mds",0);
		if($abm > -1){
			$this->objetos[$abm]->procesar();
			//Si la el ABM acaba de procesar un ALTA, tengo que cargar la ZONA!
			if($this->objetos[$abm]->obtener_etapa()=="PA"){
				//Si salio todo OK
				if($this->objetos[$abm]->obtener_estado_proceso()=="OK"){
					//Obtengo el ID del registro actual
					$clave_registro = $this->objetos[$abm]->obtener_clave();
					//ei_arbol($this->objetos[$abm]->obtener_datos());
					if($this->zona->cargar_editable($clave_registro)){
						//$this->zona->info();
						$this->zona->obtener_html_barra_superior();
						$this->objetos[$abm]->obtener_html();					
						include_once("editor_grupos_asignaritems.php");
						$this->zona->obtener_html_barra_inferior();
						//Si la lista de la izquierda concuerda con la del EDITABLE ingresado
						//tengo que refrescarla para que refleje el estado de la base
						$this->zona->refrescar_listado_editable_apex();
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