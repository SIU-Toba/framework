<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();

		$lista = $this->cargar_objeto("objeto_cuadro",0);
		if($lista > -1){
			$this->objetos[$lista]->cargar_datos();
			enter();
			$this->objetos[$lista]->obtener_html();
		}else{
			echo ei_mensaje("No es posible mostrar la CARTELERA");
		}
		$lista = $this->cargar_objeto("objeto_cuadro",1);
		if($lista > -1){
			$this->objetos[$lista]->cargar_datos();
			enter();
			$this->objetos[$lista]->obtener_html();
		}else{
			echo ei_mensaje("No es posible mostrar la CARTELERA");
		}
		$lista = $this->cargar_objeto("objeto_cuadro",2);
		if($lista > -1){
			$this->objetos[$lista]->cargar_datos();
			enter();
			$this->objetos[$lista]->obtener_html();
		}else{
			echo ei_mensaje("No es posible mostrar la CARTELERA");
		}
		$lista = $this->cargar_objeto("objeto_cuadro",3);
		if($lista > -1){
			$this->objetos[$lista]->cargar_datos();
			enter();
			$this->objetos[$lista]->obtener_html();
		}else{
			echo ei_mensaje("No es posible mostrar la CARTELERA");
		}
		$lista = $this->cargar_objeto("objeto_cuadro",4);
		if($lista > -1){
			$this->objetos[$lista]->cargar_datos();
			enter();
			$this->objetos[$lista]->obtener_html();
		}else{
			echo ei_mensaje("No es posible mostrar la CARTELERA");
		}
/*		
	echo "<pre>";
	echo "
	
* Ver las NOTAS que me mandaron a MI

(n CUADROS)

carac: paginado, mas nuevo primero
where: MENSAJES enviados al USUARIO ACTUAL ( campo usuario_destino )
Todos podrian ser de una clase subheredada con:
	- Callback para marcar como leido.
	- Responder? (Popup con ABM para mandar un mensaje al remitente)

Listados: 
 	tabla: apex_nota
 	tabla: apex_nota_item		(link al editor del elemento)
	tabla: apex_nota_objeto		(link al editor del elemento)
	tabla: apex_nota_clase		(link al editor del elemento)
	tabla: apex_nota_nucleo		(link al editor del elemento)
	tabla: apex_nota_patron		(link al editor del elemento)

";	
	echo "<pre>";
*/	
		
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}
?>