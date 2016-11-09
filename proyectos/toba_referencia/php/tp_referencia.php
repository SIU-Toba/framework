<?php


class tp_referencia extends toba_tp_normal
{
	protected $titulo;

	function barra_superior()
	{
		echo "<div id='barra_superior' class='barra-superior barra-superior-tit'>\n";		
		$this->info_version();
		echo "<div class='item-barra'>";
		$this->generar_ayuda();		
		$titulo = $this->titulo_item();		
		echo "<div class='item-barra-tit'>";
		if ($titulo[0] != '') {
			echo '<span style="font-weight:normal;">' . toba::escaper()->escapeHtml($titulo[0]) . '</span>';
		}
		echo toba::escaper()->escapeHtml($titulo[1])."</div>";
		echo "</div>\n\n";
	}
	
	function titulo_item()
	{
		if (! isset($this->titulo)) {
			$info['basica'] = toba::solicitud()->get_datos_item();
			$item = new toba_item_info($info);
			$item->cargar_rama();

			//Se recorre la rama
			$camino = array('',$item->get_nombre());
			while ($item->get_padre() != null) {
				$item = $item->get_padre();
				if (! $item->es_raiz()) {
					$camino[0] = $item->get_nombre() . ' > ' . $camino[0];
				}
			}
			$this->titulo = $camino;
		}
		return $this->titulo;
	}

/*	function pie()
	{
		php_referencia::instancia()->mostrar();
		parent::pie();	
	}
*/
}

?>