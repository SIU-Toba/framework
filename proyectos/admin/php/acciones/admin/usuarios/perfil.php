<?
	if($editable = $this->zona->obtener_editable_propagado()){
	//--> Estoy	navegando la ZONA con un editable...
		//$this->info();
		//$this->obtener_info_objetos();
		$abm = $this->cargar_objeto("objeto_mt_abms",0);
		if($abm	> -1){
			$this->objetos[$abm]->procesar($editable);
			//Si el	ABM	acaba de procesar una BAJA,	no tengo que mostrar la	ZONA!
			if($this->objetos[$abm]->obtener_etapa()=="PM-D"){
				//Como el editable que se cargo	ya no existe mas, solo muestro el ABM
				$this->objetos[$abm]->obtener_html();
				//Si la	lista de la	izquierda concuerda	con	la del EDITABLE	eliminado
				//tengo	que	refrescarla	para que refleje el	estado de la base
				$this->zona->refrescar_listado_editable_apex();
			}else{
				$this->zona->cargar_editable();//Cargo el editable de la zona
				//$this->zona->info();
				$this->zona->obtener_html_barra_superior();
				$this->objetos[$abm]->obtener_html();
				//--Restriccion	de DIMENSIONES
				ei_separador("Dimensiones RESTRINGIDAS");

				//-------------------------------------------------------------------
				//-[2]-	Cargo la lista de DIMENSIONES
				$listado = $this->cargar_objeto("objeto_cuadro",0);
				if($listado	> -1){
					//-[3]-	Cargo el ABM de	edicion	de ITEMs
					$abm_detalle = $this->cargar_objeto("objeto_mt_abms",1);
					if($abm_detalle	> -1){
						$cargar_ef = array("usuario_pd_proyecto"=>$editable[0],
											"usuario_pd"=>$editable[1]);
						$this->objetos[$abm_detalle]->cargar_estado_ef($cargar_ef);
						//proceso el evento	antes de cargar	la lista porque	si es un INSERT	
						//no va	a aparecer en el listado.
						$this->objetos[$abm_detalle]->procesar();
						//LISTA
						$where = array("(p.usuario_perfil_datos_proyecto = '".$editable[0]."')",
										"(p.usuario_perfil_datos = '".$editable[1]."')");
						$this->objetos[$listado]->cargar_datos($where);
						enter();
						$this->objetos[$listado]->obtener_html();		
						//FORMULARIO
						$this->objetos[$abm_detalle]->obtener_html();	
						//$this->objetos[$abm_detalle]->info_estado();
						//ei_arbol($this->objetos[$abm_detalle]->obtener_datos());
					}else{
						echo ei_mensaje("No	fue	posible	instanciar el ABM 2");
					}
				}else{
					echo ei_mensaje("No	fue	posible	instanciar el objeto LISTA","error");
				}
				//-------------------------------------------------------------------

				$this->zona->obtener_html_barra_inferior();
			}
		}else{
			echo ei_mensaje("No	fue	posible	instanciar el ABM (1)");
		}
	}else{
	//--> NO estoy navegando en	la ZONA	con	un editable
		$abm = $this->cargar_objeto("objeto_mt_abms",0);
		if($abm	> -1){
			$this->objetos[$abm]->procesar();
			//Si la	el ABM acaba de	procesar un	ALTA, tengo	que	cargar la ZONA!
			if($this->objetos[$abm]->obtener_etapa()=="PA"){
				//Si salio todo	OK
				if($this->objetos[$abm]->obtener_estado_proceso()=="OK"){
					//Obtengo el ID	del	registro actual
					$clave_registro	= $this->objetos[$abm]->obtener_clave();
					//ei_arbol($this->objetos[$abm]->obtener_datos());
					if($this->zona->cargar_editable($clave_registro)){
						//$this->zona->info();
						$this->zona->obtener_html_barra_superior();
						$this->objetos[$abm]->obtener_html();					
						//--Restriccion	de DIMENSIONES
						ei_separador("Dimensiones RESTRINGIDAS");
						//----------------------------------------------




						//----------------------------------------------
						$this->zona->obtener_html_barra_inferior();
						//Si la	lista de la	izquierda concuerda	con	la del EDITABLE	ingresado
						//tengo	que	refrescarla	para que refleje el	estado de la base
						$this->zona->refrescar_listado_editable_apex();
					}else{
						//ei_mensaje("No fue posible cargar	la zona","error");
					}
				}else{
					$this->objetos[$abm]->obtener_html();					
				}
			}else{
				$this->objetos[$abm]->obtener_html();
			}
		}else{
			echo ei_mensaje("No	fue	posible	instanciar el ABM (2)");
		}
	}
?>