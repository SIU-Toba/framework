<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
//echo "<pre>";
/*	echo "
	
* Ver las notas SIN DESTINATARIO atachadas a elementos del TOBA

(n CUADROS)

carac: paginado, mas nuevo primero
where: MENSAJES asociados a elementos del TOBA que no tienen destinatario
		(campo usuario_destino = NULL)
Todos podrian ser de una clase subheredada con:
	- Callback para marcar como leido.

Listados: 

	tabla: apex_nota_item		(link al editor del elemento)
	tabla: apex_nota_objeto		(link al editor del elemento)
	tabla: apex_nota_clase		(link al editor del elemento)
	tabla: apex_nota_nucleo		(link al editor del elemento)

";*/	

		$where[] = "usuario_destino is null";


		//-- LISTADO de ITEMS

		$lista_items = $this->cargar_objeto("objeto_cuadro", 3);
		if($lista_items > -1 ){

			$this->objetos[$lista_items]->cargar_datos($where);
			enter();
			$this->objetos[$lista_items]->obtener_html();

		}else{
			echo ei_mensaje("No es posible mostrar el LISTADO");
		}
		

/*

		$lista = $this->cargar_objeto("objeto_cuadro",1);
		if($lista > -1){
			$this->objetos[$lista]->cargar_datos($where);
			enter();
			$this->objetos[$lista]->obtener_html();
		}else{
			echo ei_mensaje("No es posible mostrar la CARTELERA");
		}
		$lista = $this->cargar_objeto("objeto_cuadro",2);
		if($lista > -1){
			$this->objetos[$lista]->cargar_datos($where);
			enter();
			$this->objetos[$lista]->obtener_html();
		}else{
			echo ei_mensaje("No es posible mostrar la CARTELERA");
		}
		$lista = $this->cargar_objeto("objeto_cuadro",3);
		if($lista > -1){
			$this->objetos[$lista]->cargar_datos($where);
			enter();
			$this->objetos[$lista]->obtener_html();
		}else{
			echo ei_mensaje("No es posible mostrar la CARTELERA");
		}	
		$lista = $this->cargar_objeto("objeto_cuadro",4);
		if($lista > -1){
			$this->objetos[$lista]->cargar_datos($where);
			enter();
			$this->objetos[$lista]->obtener_html();
		}else{
			echo ei_mensaje("No es posible mostrar la CARTELERA");
		}	
		$this->zona->obtener_html_barra_inferior();
*/

	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}
?>