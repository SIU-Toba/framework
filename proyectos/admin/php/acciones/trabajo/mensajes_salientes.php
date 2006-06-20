<?
if($editable = $this->zona->obtener_editable_propagado()){
	$this->zona->cargar_editable();
	$this->zona->obtener_html_barra_superior();
	
echo "<pre>";
echo "
	
* Ver las NOTAS que me mande YO

(n LISTADOS)

carac: paginado, mas nuevo primero
where: MENSAJES enviados POR USUARIO ACTUAL ( campo usuario_origen )
Todos podrian ser de una clase subheredada con:
	- Callback ver cuales fueron LEIDOS.

Listados: 

	tabla: apex_nota (Cuando el destinatario no es NULL)
	tabla: apex_nota_item		(link al editor del elemento)
	tabla: apex_nota_objeto     (link al editor del elemento)
	tabla: apex_nota_clase      (link al editor del elemento)
	tabla: apex_nota_nucleo     (link al editor del elemento)
	tabla: apex_nota_patron     (link al editor del elemento)

";	
	echo "<pre>";
		$where[] = "usuario_origen = '" . $editable[0] . "'";
		$lista = $this->cargar_objeto("objeto_cuadro",0);
		if($lista > -1){
			$this->objetos[$lista]->cargar_datos($where);
			enter();
			$this->objetos[$lista]->obtener_html();
		}else{
			echo ei_mensaje("No es posible mostrar la CARTELERA");
		}
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

		$this->zona->obtener_html_barra_inferior();
}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
}
?>