<?php

namespace rest\http;

class vista_json extends vista_respuesta
{

	protected $pretty_print = true;

	protected function get_content_type()
	{
		return 'application/json';
	}


	public function get_cuerpo()
	{
		$data = $this->respuesta->get_data();
		if (!empty($data) && $this->respuesta->get_encoding_datos() !== 'utf-8') {
			$data = $this->utf8_encode_fields($data);
		}

		if ($this->pretty_print) {
			if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
				$output = json_encode($data, JSON_PRETTY_PRINT);
			} else {
				$output = $this->prettyPrint(json_encode($data));
			}
		} else {
			$output = json_encode($data);
		}
		return $output;
	}


	protected function prettyPrint($json)
	{
		$result = '';
		$level = 0;
		$prev_char = '';
		$in_quotes = false;
		$ends_line_level = NULL;
		$json_length = strlen($json);

		for ($i = 0; $i < $json_length; $i++) {
			$char = $json[$i];
			$new_line_level = NULL;
			$post = "";
			if ($ends_line_level !== NULL) {
				$new_line_level = $ends_line_level;
				$ends_line_level = NULL;
			}
			if ($char === '"' && $prev_char != '\\') {
				$in_quotes = !$in_quotes;
			} else {
				if (!$in_quotes) {
					switch ($char) {
						case '}':
						case ']':
							$level--;
							$ends_line_level = NULL;
							$new_line_level = $level;
							break;

						case '{':
						case '[':
							$level++;
						case ',':
							$ends_line_level = $level;
							break;

						case ':':
							$post = " ";
							break;

						case " ":
						case "\t":
						case "\n":
						case "\r":
							$char = "";
							$ends_line_level = $new_line_level;
							$new_line_level = NULL;
							break;
					}
				}
			}
			if ($new_line_level !== NULL) {
				$result .= "\n" . str_repeat("\t", $new_line_level);
			}
			$result .= $char . $post;
			$prev_char = $char;
		}

		return $result;
	}


	protected function utf8_encode_fields(array $elements)
	{
		foreach ($elements as $key => $element) {
			if (is_array($element)) {
				$elements[$key] = $this->utf8_encode_fields($element);
			} else {
				$elements[$key] = utf8_encode($element);
			}
		}

		return $elements;
	}
}