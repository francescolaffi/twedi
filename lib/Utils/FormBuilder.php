<?php

namespace lib\Utils;

class FormBuilder {
	
	private $form_id;
	private $form_target;
	private $form_method;
	
	private $has_file_input = false;
	
	private $errors = array();
	
	private $elements = array();

	public function __construct($form_id = '', $target = '#', $method = 'POST')
	{
		if (!$form_id) {
			static $form_index = 0;
			$form_id = 'form_'.$form_index++;
		}
		
		$this->form_id = $form_id;
		$this->form_target = $target;
		$this->form_method = $method;
	}

	public function addTextbox($label, $name, $required, $value)
	{
		$type = 'text';
		$this->elements[$name] = compact('type', 'label', 'required', 'value');
	}

	public function addTextarea($label, $name, $required, $value)
	{
		$type = 'textarea';
		$this->elements[$name] = compact('type', 'label', 'required', 'value');
	}

	public function addPassword($label, $name, $required)
	{
		$type = 'password';
		$this->elements[$name] = compact('type', 'label', 'required');
	}

	public function addCheckbox($label, $name, $required, $checked)
	{
		$type = 'checkbox';
		$this->elements[$name] = compact('type', 'label', 'required', 'checked');
	}

	public function addFile($label, $name, $required)
	{
		$this->has_file_input = true;
		$type = 'file';
		$this->elements[$name] = compact('type', 'label', 'required');
	}

	/**
	 * add multiple checkboxes together
	 * 
	 * @param string $label    top label
	 * @param string $name     http query key
	 * @param bool $required if is required
	 * @param array<assoc<mixed>> $checkboxes array of checkboxes, each checkbox
	 * is an associative array with keys 'name', 'label' and 'checked'
	 */
	public function addMultiCheckbox($label, $name, $required, $checkboxes)
	{
		$type = 'multicheckbox';
		$this->elements[$name] = compact('type', 'label', 'required', 'checkboxes');
	}

	/**
	 * add radio buttons
	 * 
	 * @param string $label    top label
	 * @param string $name     http query key
	 * @param bool $required if is required
	 * @param assoc<string> $radio_buttons associative array of radio buttons, keys
	 * are the http query value and values the labels
	 * @param string|false $default_checked the button initially checked identified by its key in $radio_buttons
	 */
	public function addRadioButtons($label, $name, $required, $radio_buttons, $default_checked)
	{
		$type = 'radiobuttons';
		$this->elements[$name] = compact('type', 'label', 'required', 'radio_buttons', 'default_checked');
	}

	/**
	 * add dropdown select
	 * 
	 * @param string $label    top label
	 * @param string $name     http query key
	 * @param bool $required if is required
	 * @param assoc<string> $options associative array of options, keys
	 * are the http query value and values the displayed name
	 * @param string|false $default_selected the option initially selected identified by its key in $radio_buttons
	 */
	public function addSelect($label, $name, $required, $options, $default_selected)
	{
		$type = 'select';
		$this->elements[$name] = compact('type', 'label', 'required', 'options', 'default_selected');
	}

	public function addHidden($name, $value)
	{
		$type = 'hidden';
		$this->elements[$name] = compact('type', 'value');
	}

	public function addSubmit($name, $value)
	{
		$type = 'submit';
		$this->elements[$name] = compact('type', 'value');
	}

	public function addHtml($html)
	{
		$type = 'html';
		$this->elements[] = compact('type', 'html');
	}
	
	public function setErrors($errors)
	{
		$this->errors = $errors;
	}
	
	public function render()
	{
		//attributes encoder
		$attr_enc = function($v) { return htmlspecialchars($v, ENT_QUOTES); };
		
		$form_id = $attr_enc($this->form_id);
		$target = $attr_enc($this->form_target);
		$enctype = $this->has_file_input ? 'enctype="multipart/form-data"' : '';
		$html = "\n<form id='$form_id' action='$target' method='".\strtolower($this->form_method)."' $enctype ><div>\n\n";
		
		foreach ($this->elements as $name => $element) {
			$type = $element['type'];
			
			if ('html' == $type) {
				$html .= "{$element['html']}\n";
				continue;
			}
			
			$name = $attr_enc($name);
			$input_id = $form_id.'_'.$name;
			$value = $attr_enc(@$element['value']);
			
			if ('hidden' == $type || 'submit' == $type ) {
				$html .= "<input type='$type' name='$name' value='$value' id='$input_id' />\n\n";
				continue;
			}
			
			$err = @$this->errors[$name];
			$err_class = ($err?'class="error"':'');
			
			// element box
			$html .= "<div id='{$input_id}_box' class='form_element form_element_$type'>\n";
			
			// label
			if ($element['label']) {
				if ('radio' == $type || 'multicheckbox' == $type) {
					$label = '<label>';
				} else {
					$label = "<label for='$input_id' id='{$input_id}_label'>";
				}
				if ($element['required']) {
					$label .= "<span class='required_element_sign'>*</span>";
				}
				$label .= $element['label'];
				$label .= "</label>\n";
				if ('checkbox' != $type) {
					$html .= $label;
				}
			}
			
			// input html based on type
			switch ($type) {
				case 'text':
					$html .= "<input $err_class type='text' name='$name' value='$value' id='$input_id' />\n";
					break;
				case 'textarea':
					$html .= "<textarea $err_class name='$name' id='$input_id'>$value</textarea>\n";
					break;
				case 'password':
					$html .= "<input $err_class type='password' name='$name' id='$input_id' />\n";
					break;
				case 'checkbox':
					$html .= "<input $err_class type='checkbox' name='$name' ". (@$element['checked'] ? 'checked="checked" ' : '') ."value='on' id='$input_id' />\n$label";
					break;
				case 'file':
					$html .= "<input $err_class type='file' name='$name' id='$input_id' />\n";
					break;
				case 'multicheckbox':
					foreach ($checkboxes as $v) {
						$ch_name = $attr_enc($v['name']);
						$html .= "<input type='checkbox' name='{$name}[$ch_name]' ". (@$v['checked'] ? 'checked="checked" ' : '') ."value='on' id='{$input_id}_$ch_name' />\n";
						$html .= "<label for='{$input_id}_$ch_name'>".$v['label']."</label>\n";
					}
					break;
				case 'radiobuttons':
					$radiobuttons = array_map($attr_enc, $element['radio_buttons']);
					foreach ($radiobuttons as $k => $v) {
						$html .= "<input type='radio' name='$name' value='$k' ". ($k == $element['default_checked'] ? 'checked="checked" ' : '') ."id='{$input_id}_$k' />\n";
						$html .= "<label for='{$input_id}_$k'>".$v."</label>\n";
					}
					break;
				case 'select':
					$html .= "<select $err_class name='$name' id='{$input_id}'>\n";
					$options = array_map($attr_enc, $element['options']);
					foreach ($options as $k => $v) {
						$html .= "<option value='$k' ". ($k == $element['default_selected'] ? 'selected="selected" ' : '') .">$v</option>\n";
					}
					$html .= "</select>\n";
					break;
			}
			
			// error message
			if ($err) {
				$html .= "<p $err_class>".htmlspecialchars($err)."</p>\n";
			}
			
			//close element box
			$html .= "</div>\n\n";
		}
		
		$html .= "</div></form>\n";
		return $html;
	}
	
}