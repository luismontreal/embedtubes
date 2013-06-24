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
		$itemArray = current(LibFE_Helpers::xmlToArray($itemXml));
		return $itemArray;
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

}