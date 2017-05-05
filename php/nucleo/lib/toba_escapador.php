<?php
use Zend\Escaper\Escaper;
/**
 * Clase utilizada para escapar los diferentes datos que se envian al cliente
 * @package Seguridad
 */
class toba_escapador extends Escaper
{
	private $_es_editor;
	private $tags_formato = array('b', 'i','p','s','u', 'em', 'pre', 'strike', 'strong', 'span');
	
	function __construct($encoding = null)
	{
		$this->_es_editor = (toba::proyecto()->get_id() === 'toba_editor');
		parent::__construct($encoding);
	}
		
	function escapeHtml($input)
	{		
		if (isset($this->_es_editor) && ($this->_es_editor === true)) {
			return htmlentities($input, ENT_QUOTES , apex_default_charset);
		}  
		$tags = $this->quitar_tags($input);
		if (! empty($tags)) {
			$resultado = '';
			for( $i = 0; $i < count(current($tags)); $i++) {
				if (isset($tags[1][$i])) {
					$valor =  parent::escapeHtml($tags[2][$i]);
					$resultado .=  "<{$tags[1][$i]}>{$valor}</{$tags[1][$i]}>";
				}
			}
		} else {
			$resultado = parent::escapeHtml($input);
		}
		return  $resultado;
	}
	
	function escapeHtmlAttr($input)
	{
		$tags = $this->quitar_tags($input);	
		if (! empty($tags)) {
			$resultado = '';
			for( $i = 0; $i < count(current($tags)); $i++) {
				if (isset($tags[1][$i])) {
					$valor =  (isset($this->_es_editor) && ($this->_es_editor === true)) ? $tags[2][$i] : parent::escapeHtmlAttr($tags[2][$i]);
					$resultado .=  "<{$tags[1][$i]}>{$valor}</{$tags[1][$i]}>";
				}
			}
		} else {
			$resultado = (isset($this->_es_editor) && ($this->_es_editor === true)) ? $input: parent::escapeHtmlAttr($input);
		}
		return  $resultado;
	}
	
	function escapeCss($texto)
	{
		return (isset($this->_es_editor) && ($this->_es_editor === true)) ? $texto: parent::escapeCss($texto);
	}
	
	function escapeJs($texto)
	{
		return (isset($this->_es_editor) && ($this->_es_editor === true)) ? $texto: parent::escapeJs($texto);
	}
	
	function quitar_tags($input)
	{
		$lista_tags = implode('|', $this->tags_formato);
		$pattern = '@<('. $lista_tags.')>(.*?)</\1>@i';
		if (! is_array($input)) { 
		    $cant = preg_match_all($pattern, $input, $matches); 
		} else { 
		    throw new toba_error_def('Se esta pasando un array o una matriz a un campo que espera un valor escalar'); 
		}
		return ($cant !== 0  && $cant !== false) ? $matches : array();
	}
}
?>
