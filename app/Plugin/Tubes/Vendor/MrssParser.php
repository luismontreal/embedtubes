<?php

class MrssParser implements Iterator {

	private $position = 0;
	private $xmlFileName;
	private $reader;
	private $isValid;

	public function __construct($fileName) {
		$this->position = 0;
		$this->reader = new XMLReader();
		$this->xmlFileName = $fileName;
		$this->isValid = FALSE;
	}

	public function rewind() {
		$this->position = 0;
		$this->reader->open($this->xmlFileName);

		while ($this->reader->read()) {
			if ($this->reader->nodeType == XMLREADER::ELEMENT && $this->reader->localName == "item") {
				$this->isValid = TRUE;
				break;
			}
		}
	}

	public function current() {
		$itemText = $this->reader->readOuterXML();
		$itemXml = new SimpleXMLElement($itemText);
		$itemArray = $this->__xmlToArray($itemXml);
		return $itemArray['item'];
	}

	public function key() {
		return $this->position;
	}

	public function next() {
		$this->isValid = $this->reader->next("item");
		if ($this->isValid)
			++$this->position;
	}

	public function valid() {
		if (!$this->isValid)
			$this->reader->close();
		return $this->isValid;
	}
	
	private function __xmlToArray(SimpleXMLElement $xml, $options = array()) {
	    $defaults = array(
		'namespaceSeparator' => ':',//you may want this to be something other than a colon
		'attributePrefix' => '@',   //to distinguish between attributes and nodes with the same name
		'alwaysArray' => array(),   //array of xml tag names which should always become arrays
		'autoArray' => true,        //only create arrays for tags which appear more than once
		'textContent' => '$',       //key used for the text content of elements
		'autoText' => true,         //skip textContent key if node has no attributes or child nodes
		'keySearch' => false,       //optional search and replace on tag and attribute names
		'keyReplace' => false       //replace values for above search values (as passed to str_replace())
	    );
	    $options = array_merge($defaults, $options);
	    $namespaces = $xml->getDocNamespaces();
	    $namespaces[''] = null; //add base (empty) namespace
	 
	    //get attributes from all namespaces
	    $attributesArray = array();
	    foreach ($namespaces as $prefix => $namespace) {
		foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
		    //replace characters in attribute name
		    if ($options['keySearch']) $attributeName =
		            str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
		    $attributeKey = $options['attributePrefix']
		            . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
		            . $attributeName;
		    $attributesArray[$attributeKey] = (string)$attribute;
		}
	    }
	 
	    //get child nodes from all namespaces
	    $tagsArray = array();
	    foreach ($namespaces as $prefix => $namespace) {
		foreach ($xml->children($namespace) as $childXml) {
		    //recurse into child nodes
		    $childArray = $this->__xmlToArray($childXml, $options);
		    list($childTagName, $childProperties) = each($childArray);
	 
		    //replace characters in tag name
		    if ($options['keySearch']) $childTagName =
		            str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
		    //add namespace prefix, if any
		    if ($prefix) $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;
	 
		    if (!isset($tagsArray[$childTagName])) {
		        //only entry with this key
		        //test if tags of this type should always be arrays, no matter the element count
		        $tagsArray[$childTagName] =
		                in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
		                ? array($childProperties) : $childProperties;
		    } elseif (
		        is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
		        === range(0, count($tagsArray[$childTagName]) - 1)
		    ) {
		        //key already exists and is integer indexed array
		        $tagsArray[$childTagName][] = $childProperties;
		    } else {
		        //key exists so convert to integer indexed array with previous value in position 0
		        $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
		    }
		}
	    }
	 
	    //get text content of node
	    $textContentArray = array();
	    $plainText = trim((string)$xml);
	    if ($plainText !== '') $textContentArray[$options['textContent']] = $plainText;
	 
	    //stick it all together
	    $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
		    ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;
	 
	    //return node as array
	    return array(
		$xml->getName() => $propertiesArray
	    );
	}

}