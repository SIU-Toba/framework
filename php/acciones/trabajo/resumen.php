<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
	echo "<pre>";
	echo "

* Ver un resumen del ESTADO

- Nro. de Notas entrantes sin leer
- Nro. Notas Salientes leidas
- Cantidad de mensajes en cartelera

- Usuarios logueados al sistema

- Cantidad de OBJETOS en USO
- Cantidad de ITEMS en USO

- Cantidad de tareas pendientes

	
";	
	echo "<pre>";
		
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}
?>