<?
$grafico =	$this->cargar_objeto("objeto_grafico",0);
		if($grafico > -1){

				if($this->objetos[$grafico]->cargar_datos()===true)
				{
					$this->objetos[$grafico]->obtener_html();
				}else{
					$this->objetos[$grafico]->mostrar_estado();
					
				}
			}
?>