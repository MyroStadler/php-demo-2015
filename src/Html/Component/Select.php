<?php

namespace Acme\Html\Component;

class Select
{
	protected $_id;
	protected $_selectedId;
	protected $_options;

	public function __construct($id, $selectedId='') {
		$this->_id = $id;
		$this->_selectedId = $selectedId;
	}
	
	
	//***************************************************
	// public
	//***************************************************
	public function addOption($id, $value, $isSelected=false, $maxLen=0) {
		$valueLen = strlen($value);
		if($isSelected) {
			$this->_selectedId = $id;
		}
		if(!isset($this->_options)) {
			$this->_options = array();
		}
		$this->_options[] = array($id, $maxLen == 0 || $valueLen <= $maxLen ? $value : substr($value, 0, $maxLen) . '...');
		return $this;
	}
	
	public function addOptionsByRows(array $rows, $idField, $valueField, $maxLen=0) {
		if(!isset($this->_options)) {
			$this->_options = array();
		}
		foreach($rows as $row) {
			$value = $row[$valueField];
			if($maxLen != 0 && strlen($value) > $maxLen) {
				$value = substr($value, 0, $maxLen) . '...';
			}
			$this->_options[] = array($row[$idField], $value);
		}
		return $this;
	}
	
	//***************************************************
	// Magical
	//***************************************************
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);
        if (!method_exists($this, $method)) {
            throw new Exception('Setting invalid select property ' . $name);
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        if (!method_exists($this, $method)) {
            throw new Exception('Getting invalid select property ' . $name);
        }
        return $this->$method();
    }
	
	public function __toString() {
		$options = array();
		$hasSelectedId = isset($this->_selectedId);
		if($this->_options && count($this->_options) > 0) {
			foreach($this->_options as $option) {
				$id = $option[0];
				$value = $option[1];
				$selected = $hasSelectedId && $id == $this->_selectedId ? ' selected="selected"' : '';
				$options[] = sprintf('<option value="%s"%s>%s</option>',
						$id, 
						$selected,
						$value);
			}
		}
		return sprintf('<select id="%s" name="%s">%s</select>',
				$this->_id,
				$this->_id,
				implode("\n", $options));
	}


	//***************************************************
	// Getters / Setters
	//***************************************************
	
	public function setId($id) {
		$this->_id = $id;
	}
	
	public function getId() {
		return $this->_id;
	}
	
	public function setSelectedId($id) {
		$this->_selectedId = $id;
	}
	
	public function getSelectedId() {
		return $this->_selectedId;
	}
}