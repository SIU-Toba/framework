<?php
require_once("objeto.php");
require_once("objeto_ei.php");
require_once('3ros/activecalendar/activecalendar.php');

class objeto_ei_calendario extends objeto_ei
{
	var $calendario;
	var $semana_seleccionada;
	var $dia_seleccionado;
	var $mes_actual;
	var $ver_contenidos;

    function objeto_ei_calendario($id)
/*
    @@acceso: constructor
    @@desc: 
*/
    {
        parent::__construct($id);
		$this->semana_seleccionada = null;
		$this->dia_seleccionado = null;
		$this->mes_actual = null;
		$this->submit = "ei_calendario" . $this->id[1];
		$this->objeto_js = "objeto_calendario_{$id[1]}";
		$this->calendario = new calendario();
	}
	
	function destruir()
	{
		$this->memoria["eventos"] = array();
		if(isset($this->eventos)){
			foreach($this->eventos as $id => $evento ){
				$this->memoria["eventos"][$id] = true;
			}
		}
		//Seleccionar Semana
		if (isset($this->semana_seleccionada)) {
			$this->memoria['semana_seleccionada'] = $this->semana_seleccionada;
		} else {
			unset($this->memoria['semana_seleccionada']);
		}
		//Seleccionar Día		
		if (isset($this->dia_seleccionado)) {
			$this->memoria['dia_seleccionado'] = $this->dia_seleccionado;
		} else {
			unset($this->memoria['dia_seleccionado']);
		}
		//Cambiar Mes 
		if (isset($this->mes_actual)) {
			$this->memoria['mes_actual'] = $this->mes_actual;
		} else {
			unset($this->memoria['mes_actual']);
		}
		parent::destruir();
	}


	function inicializar($parametros)
	{
		$this->id_en_padre = $parametros['id'];		
	}
	
    function cargar_datos($datos=null,$memorizar=true)
/*
    @@acceso: publico
    @@desc: Carga los datos del calendario
    @@retorno: boolean | Estado resultante de la operacion
*/
    {
		if ($datos)
		{
			foreach ($datos as $dato)
			{
				if (isset($dato["dia"]))
				{
					$dia = explode("-", $dato["dia"]);
					$anio = $dia[0];
					$mes = $dia[1];
					$dia = $dia[2];
					$this->calendario->setEventContent($anio, $mes, $dia, $dato["contenido"]);
				}
				elseif (isset($dato["semana"]))	
				{
					$semana = $dato["semana"];
					$anio = $dato["anio"];
					$this->calendario->setWeekEventContent($anio, $semana, $dato["contenido"]);
				}
			}
		}	
	}
	
	function set_ver_contenidos($ver)
	{
		$this->ver_contenidos = $ver;
		$this->calendario->set_mostrar_eventos(true);
	}
	
	function recuperar_interaccion()
	{
		$this->cargar_seleccion_dia();
		$this->cargar_seleccion_semana();
		$this->cargar_cambio_mes();
	}
	
	function cargar_seleccion_dia()
	{
		$this->dia_seleccionado = null;
		if (isset($this->memoria['dia_seleccionado']))
			$this->dia_seleccionado = $this->memoria['dia_seleccionado'];
		if(isset($_POST[$this->submit."__seleccionar_dia"])) {
			$dia = $_POST[$this->submit."__seleccionar_dia"];
			if ($dia != '') {
				$dia = explode(apex_qs_separador, $dia);
				$this->dia_seleccionado["dia"] = $dia[0];
				$this->dia_seleccionado["mes"] = $dia[1];				
				$this->dia_seleccionado["anio"] = $dia[2];	
				$this->calendario->setSelectedDay($dia[0]);
				$this->calendario->setSelectedMonth($dia[1]);
				$this->calendario->setSelectedYear($dia[2]);
			}
		}
	}
	
	function cargar_seleccion_semana()
	{
		$this->semana_seleccionada = null;
		if (isset($this->memoria['semana_seleccionada']))
			$this->semana_seleccionada = $this->memoria['semana_seleccionada'];
		if(isset($_POST[$this->submit."__seleccionar_semana"])) {
			$semana = $_POST[$this->submit."__seleccionar_semana"];
			if ($semana != '') {
				$semana = explode(apex_qs_separador, $semana);
				$this->semana_seleccionada["semana"] = $semana[0];		
				$this->semana_seleccionada["anio"] = $semana[1];
				$this->calendario->setSelectedWeek($semana[0]);
				$this->calendario->setSelectedYear($semana[1]);		
			}
		}
	}
	
	function cargar_cambio_mes()
	{
		if(isset($_POST[$this->submit."__cambiar_mes"])) {
			$mes = $_POST[$this->submit."__cambiar_mes"];
			if ($mes != '') {
				$mes = explode(apex_qs_separador, $mes);
				$this->mes_actual["mes"] = $mes[0];		
				$this->mes_actual["anio"] = $mes[1];		
				$this->calendario->setActMonth($this->mes_actual["mes"]);
				$this->calendario->setActYear($this->mes_actual["anio"]);
				$this->calendario = new calendario(0, $this->calendario->actyear, $this->calendario->actmonth);
			}
		}
	}
	
	public function agregar_observador($observador)
	{
		$this->observadores[] = $observador;
	}

	function eliminar_observador($observador){}

	function get_lista_eventos()
	{
		$eventos = array();
		$eventos += eventos::seleccionar_dia();
		$eventos += eventos::seleccionar_semana();
		$eventos += eventos::cambiar_mes();
		
		return $eventos;
	}
	
	function disparar_eventos()
	{
		$this->recuperar_interaccion();
		if(isset($_POST[$this->submit]) && $_POST[$this->submit]!="") {
			$evento = $_POST[$this->submit];	
			//El evento estaba entre los ofrecidos?
			if(isset($this->memoria['eventos'][$evento]) ) {
				if ($evento == 'seleccionar_dia')
					$parametros = $this->dia_seleccionado;
				elseif ($evento == 'seleccionar_semana')
					$parametros = $this->semana_seleccionada;
				elseif ($evento == 'cambiar_mes')
					$parametros = $this->mes_actual;

				$this->reportar_evento( $evento, $parametros );
			}
		}
	}
	
	function obtener_html()
	{
		//Campos de comunicación con JS
		echo form::hidden($this->submit, '');
		echo form::hidden($this->submit."__seleccionar_semana", '');
		echo form::hidden($this->submit."__seleccionar_dia", '');
		echo form::hidden($this->submit."__cambiar_mes", '');
		
		$this->calendario->enableDatePicker(2000,2010);
		$this->calendario->enableDayLinks();
		$this->calendario->enableWeekLinks();
		if ($this->ver_contenidos)
			$this->calendario->viewEventContents();

		$out = $this->calendario->showMonth($this->objeto_js, $this->eventos);
		echo $out;
	}
	
	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	protected function crear_objeto_js()
	{
		$identado = js::instancia()->identado();
		echo $identado."var {$this->objeto_js} = new objeto_ei_calendario('{$this->objeto_js}', '{$this->submit}');\n";
	}

	//-------------------------------------------------------------------------------

	public function consumo_javascript_global()
	{
		$consumo = parent::consumo_javascript_global();
		$consumo[] = 'clases/objeto_ei_calendario';
		return $consumo;
	}	

}


class calendario extends activecalendar
{
	protected $mostrar_eventos;
	
	function calendario($week=false,$year=false,$month=false,$day=false,$GMTDiff="none")
	{
        parent::__construct($week,$year,$month,$day,$GMTDiff);
	}
	
	function set_mostrar_eventos()
	{
		$this->mostrar_eventos = true;
	}
	
	function showMonth($objeto_js, $eventos)
	{
		$out = $this->mkMonthHead();
		$out .= $this->mkMonthTitle();
		$out .= $this->mkDatePicker($objeto_js, $eventos);
		$out .= $this->mkWeekDays();
		$out .= $this->mkMonthBody($objeto_js, $eventos);
		$out .= $this->mkMonthFoot();
		return $out;
	}
	
	function mkDatePicker($objeto_js, $eventos)
	{
		$pickerSpan = 8;
		if ($this->datePicker)
		{
			$out="<tr><td class=\"".$this->cssPicker."\" colspan=\"".$pickerSpan."\">\n";
			$out.="<form name=\"".$this->cssPickerForm."\" class=\"".$this->cssPickerForm."\" action=\"".$this->urlPicker."\" method=\"get\">\n";
			$out.="<select name=\"".$this->monthID."\" id=\"".$this->monthID."\" class=\"".$this->cssPickerMonth."\">\n";
			for ($z=1;$z<=12;$z++)
			{
				if ($z==$this->actmonth)
					$out.="<option value=\"".$z."\" selected=\"selected\">".$this->getMonthName($z)."</option>\n";
				else
					$out.="<option value=\"".$z."\">".$this->getMonthName($z)."</option>\n";
			}
			$out.="</select>\n";
			$out.="<select name=\"".$this->yearID."\" id=\"".$this->yearID."\" class=\"".$this->cssPickerYear."\">\n";
			for ($z=$this->startYear;$z<=$this->endYear;$z++)
			{
				if ($z==$this->actyear)
					$out.="<option value=\"".$z."\" selected=\"selected\">".$z."</option>\n";
				else
					$out.="<option value=\"".$z."\">".$z."</option>\n";
			}
			$out.="</select>\n";
			$evento_js = eventos::a_javascript('cambiar_mes', $eventos["cambiar_mes"]);
			$js = "{$objeto_js}.set_evento($evento_js);";
			$out.="<input type=\"submit\" value=\"".$this->selBtn."\" class=\"".$this->cssPickerButton."\" style='cursor: pointer' onclick=\"$js\"></input>\n";
			$out.="</form>\n";
			$out.="</td></tr>\n";
		}
		return $out;
	}

	
	function mkMonthBody($objeto_js, $eventos)
	{
		$out="<tr>";
		$monthday=0;
		$out.=$this->mkWeek($this->firstdate, $objeto_js, $eventos);
		for ($x=0; $x<=6; $x++)
		{
			if ($x>=$this->firstday)
			{
				$monthday++;
				$out.=$this->mkDay($monthday, $objeto_js, $eventos);
			}
			else 
				$out .= "<td class=\"".$this->cssNoMonthDay."\"></td>";
		}
		$out.="</tr>\n";
		$goon = $monthday + 1;
		$stop=0;
		for ($x=0; $x<=6; $x++)
		{
			if ($goon>$this->maxdays)
				break;
			if ($stop==1)
				break;
			$out.="<tr>";
			$date = $this->mkActiveTime(0,0,1,$this->actmonth,$goon,$this->actyear);
			$out.=$this->mkWeek($date, $objeto_js, $eventos);
			for ($i=$goon; $i<=$goon+6; $i++)
			{
				if ($i>$this->maxdays)
				{
					$out.="<td class=\"".$this->cssNoMonthDay."\"></td>";
					$stop=1;
				}
				else
					$out.=$this->mkDay($i, $objeto_js, $eventos);
			}
			$goon=$goon+7;
			$out.="</tr>\n";
		}
		return $out;
	}

	
	function mkWeek($date, $objeto_js, $eventos)
	{
		$eventContent = $this->mkWeekEventContent($date);
		
		if (!$this->weekLinks)
		{
			if ($this->mostrar_eventos)
				$out = "<td class=\"".$this->cssMonthDay."\">".$this->weekNumber($date)."</td>\n";
			else
				$out = "<td class=\"".$this->cssMonthDay."\">".$this->weekNumber($date)."</td>\n";
		}
		else
		{
			$evento_js = eventos::a_javascript('seleccionar_semana', $eventos["seleccionar_semana"], "{$this->weekNumber($date)}||{$this->mkActiveDate('Y',$date)}");
			$js = "{$objeto_js}.set_evento($evento_js);";
			if ($this->mostrar_eventos)
				$out = "<td class=\"".$this->cssMonthDay."\" style='cursor: pointer' onclick=\"$js\">".$this->weekNumber($date)."</td>\n";
			else
				$out = "<td class=\"".$this->cssMonthDay."\" style='cursor: pointer' onclick=\"$js\">".$this->weekNumber($date)."</td>\n";	
		}	
	
		return $out;
	}
	
	function mkDay($var, $objeto_js, $eventos)
	{
		$eventContent=$this->mkEventContent($var);
		
		$evento_js = eventos::a_javascript('seleccionar_dia', $eventos["seleccionar_dia"], "{$var}||{$this->actmonth}||{$this->actyear}");
		$js = "{$objeto_js}.set_evento($evento_js);";

		if (($this->dayLinks) && ((!$this->enableSatSelection && ($this->getWeekday($var) == 0)) || ((!$this->enableSunSelection && $this->getWeekday($var) == 6))))
			$out="<td class=\"".$this->cssMonthDay."\">".$var."</td>";
		else
		{
			if ($this->javaScriptDay)
				$linkstr="<a href=\"javascript:".$this->javaScriptDay."(".$this->actyear.",".$this->actmonth.",".$var.")\">".$var."</a>";
			if ($this->isEvent($var))
			{
				if ($this->eventUrl)
				{
					$out="<td class=\"".$this->eventID."\"><a href=\"".$this->eventUrl."\">".$var."</a>".$eventContent."</td>";
					$this->eventUrl=false;
				}
				else
				{
					if (!$this->dayLinks)
						$out="<td class=\"".$this->eventID."\">".$var.$eventContent."</td>";
					else
						$out="<td class=\"".$this->eventID."\"style='cursor: pointer' onclick=\"$js\">".$var.$eventContent."</td>";
				}		
			}
			elseif ($var==$this->getSelectedDay() && $this->actmonth==$this->getSelectedMonth() && $this->actyear==$this->getSelectedYear())
			{
				if (!$this->dayLinks)
				{
					if ($this->mostrar_eventos)
						$out="<td class=\"".$this->cssSelecDay."\">".$var.$eventContent."</td>";
					else
						$out="<td class=\"".$this->cssSelecDay."\">".$var."</td>";	
				}		
				else
				{
					if ($this->mostrar_eventos)
						$out="<td class=\"".$this->cssSelecDay."\"style='cursor: pointer' onclick=\"$js\">".$var.$eventContent."</td>";
					else
						$out="<td class=\"".$this->cssSelecDay."\"style='cursor: pointer' onclick=\"$js\">".$var."</td>";
				}		
			}
			elseif ($var==$this->daytoday && $this->actmonth==$this->monthtoday && $this->actyear==$this->yeartoday)
			{
				if (!$this->dayLinks)
				{
					if ($this->mostrar_eventos)
						$out="<td class=\"".$this->cssToday."\">".$var.$eventContent."</td>";
					else
						$out="<td class=\"".$this->cssToday."\">".$var."</td>";
				}			
				else
				{
					if ($this->mostrar_eventos)
						$out="<td class=\"".$this->cssToday."\"style='cursor: pointer' onclick=\"$js\">".$var.$eventContent."</td>";
					else
						$out="<td class=\"".$this->cssToday."\"style='cursor: pointer' onclick=\"$js\">".$var."</td>";	
				}		
			}
			elseif ($this->getWeekday($var)==0 && $this->crSunClass)
			{
				if (!$this->dayLinks)
				{
					if ($this->mostrar_eventos)
						$out="<td class=\"".$this->cssSunday."\">".$var.$eventContent."</td>";
					else
						$out="<td class=\"".$this->cssSunday."\">".$var."</td>";	
				}	
				else
				{
					if ($this->mostrar_eventos)				
						$out="<td class=\"".$this->cssSunday."\"style='cursor: pointer' onclick=\"$js\">".$var.$eventContent."</td>";
					else
						$out="<td class=\"".$this->cssSunday."\"style='cursor: pointer' onclick=\"$js\">".$var."</td>";	
				}	
			}
			elseif ($this->getWeekday($var)==6 && $this->crSatClass)
			{
				if (!$this->dayLinks)
				{
					if ($this->mostrar_eventos)				
						$out="<td class=\"".$this->cssSaturday."\">".$var.$eventContent."</td>";
					else
						$out="<td class=\"".$this->cssSaturday."\">".$var."</td>";	
				}	
				else
				{
					if ($this->mostrar_eventos)				
						$out="<td class=\"".$this->cssSaturday."\"style='cursor: pointer' onclick=\"$js\">".$var.$eventContent."</td>";
					else
						$out="<td class=\"".$this->cssSaturday."\"style='cursor: pointer' onclick=\"$js\">".$var."</td>";						
				}	
			}
			else
			{
				if (!$this->dayLinks)
				{
					if ($this->mostrar_eventos)				
						$out="<td class=\"".$this->cssMonthDay."\">".$var.$eventContent."</td>";
					else
						$out="<td class=\"".$this->cssMonthDay."\">".$var."</td>";
				}	
				else
				{
					if ($this->mostrar_eventos)				
						$out="<td class=\"".$this->cssMonthDay."\"style='cursor: pointer' onclick=\"$js\">".$var.$eventContent."</td>";
					else
						$out="<td class=\"".$this->cssMonthDay."\"style='cursor: pointer' onclick=\"$js\">".$var."</td>";
				}		
			}
		}
		return $out;
	}
}

?>