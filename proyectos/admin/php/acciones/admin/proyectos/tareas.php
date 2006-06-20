<?
	if($editable =	$this->zona->obtener_editable_propagado())
	{
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
		$abms = $this->cargar_objeto("objeto_mt_abms",0); 
		if($abms > -1){	
			$filtro = $this->cargar_objeto("objeto_filtro",0);	
			if($filtro > -1){
				$cuadro = $this->cargar_objeto("objeto_cuadro",0);	
				if($cuadro > -1){ 
				//------------------------
					//$this->objetos[$filtro]->mostrar_memoria(); 
					$cargar_ef	= array(	"proyecto"=>$editable[0]);	
					$this->objetos[$abms]->cargar_estado_ef($cargar_ef);	
					$this->objetos[$abms]->procesar();
					$etapa =	$this->objetos[$abms]->obtener_etapa();
					//SI el abm	no	esta en modo MODIFICACION,	muestro el resto de los	elementos
					if(($etapa!="SM"))
					{
						$status_filtro	= $this->objetos[$filtro]->validar_estado();	
						if( $status_filtro[0] )	
						{ 
							enter();	
							$this->objetos[$filtro]->obtener_interface_vertical(); 
							$where =	$this->objetos[$filtro]->obtener_where();//echo	$whe							$cuadro = $this->cargar_objeto("objeto_cuadro",0);	
							//ei_arbol($where);
							$this->objetos[$cuadro]->cargar_datos($where);	
							enter(); 
							$this->objetos[$cuadro]->obtener_html();	
						}else{ 
							$this->objetos[$filtro]->mostrar_info_proceso(); 
						} 
					}
					$this->objetos[$abms]->obtener_html();
					//ei_arbol($this->objetos[$abms]->obtener_datos(),"DATOS");
					enter();
				//------------------------
				}else{ 
					 echo	ei_mensaje("No	se	pudo crear el cuadro"); 
				} 
			}else{ 
				echo ei_mensaje("No se pudo crear el filtro"); 
			}
		}else{ 
			echo ei_mensaje("No se pudo crear el abm"); 
		}

		$this->zona->obtener_html_barra_inferior();

	}else{
		echo ei_mensaje("No se explicito	el	ELEMENTO	a editar","error");
	}
?>
