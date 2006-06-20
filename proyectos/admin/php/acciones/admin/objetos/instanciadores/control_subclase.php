<?
		if(isset($this->zona->editable_info["subclase"])){
			echo ei_mensaje("ATENCION: Existe una SUBCLASE definida 
						('".$this->zona->editable_info["subclase"]."').
						El INSTANCIADOR solo puede mostrar el comportamiento
						definido en la clase PADRE del mismo
						('".$this->zona->editable_info["clase"]."') ");
		}
?>