<?php

namespace SIUToba\rest\http;

use SimpleXMLElement;

class vista_xml extends vista_respuesta
{
    protected function get_content_type()
    {
        return 'application/xml';
    }

    public function get_cuerpo()
    {
        $data = $this->respuesta->get_data();
        $xml_root = new SimpleXMLElement("<?xml version=\"1.0\"?><data></data>");
        $this->array_to_xml($data, $xml_root);

        return $xml_root->asXML();
    }

    public function array_to_xml($data, SimpleXMLElement &$xml)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild("$key");
                    $this->array_to_xml($value, $subnode);
                } else {
                    $subnode = $xml->addChild("item$key");
                    $this->array_to_xml($value, $subnode);
                }
            } else {
                $xml->addChild("$key", "$value");
            }
        }
    }
}
