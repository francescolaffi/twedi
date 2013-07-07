<?php

namespace lib\Templating;

use \lib\Abstracts\Base_Item;
use \lib\Services\Url_Helper;

class Template_Item extends Base_Item
{
	protected $url_helper;
	
	protected $sub_items_names = array();
	
	protected $sub_items = array();
	
	public function __construct(Url_Helper $url_helper)
	{
		$this->url_helper = $url_helper;
		foreach ($this->sub_items_names as $name => $class) {
			$class = 'app\Template_Items\\'.$class;
			$item = new $class($url_helper);
			$this->sub_items[$name] = $item;
		}
	}
	
	public function get_sub_item($name)
	{
		return @$this->sub_items[$name];
	}
	
	public function set_values( $values ){
		$this->values = $values;
		foreach ($this->sub_items as &$item) {
			$item->set_values($this->values);
		}
	}
	
	/**
	 * overloading for calling subitems methods
	 *
	 * each subitems method have to be prepended with key of the subitem in the
	 * array and an underscore
	 * 
	 * @param string $name 
	 * @param array $args
	 */
	public function __call($name, $args)
	{
		foreach ($this->sub_items as $k => $i) {
			$len = strlen($k)+1;
			if (0 === substr_compare($name, $k.'_', 0, $len)) {
				$name = substr($name, $len);
				if (is_callable($cb = array($i,$name))) {
					return call_user_func_array($cb, $args);
				}
			}
		}
		throw new \LogicException("No method $name");
	}
	
	public function __get($name)
	{
		if (isset($this->values[$name])) {
			return $this->values[$name];
		} elseif (isset($this->sub_items[$name])) {
			return $this->sub_items[$name];
		}
	}
	
	public function url_for($target, array $params = array(), array $query = array())
	{
		return $this->url_helper->url_for($target, $params, $query);
	}
	
	public function nonced_url($target, array $params = array(), $nonce_action, $nonce_key = 'nonce', array $query = array())
	{
		return $this->url_helper->nonced_url($target, $params, $nonce_action, $nonce_key, $query);
	}
	
	public function static_url($asset)
	{
		return $this->url_helper->static_url($asset);
	}
	
	public function format_date($value_name, $format = null, $timezone = null)
	{
		//TODO args check
		
		$dt = new \DateTime($this->values[$value_name], new \DateTimeZone('UTC'));
		$dt->setTimezone(new \DateTimeZone($timezone ?: date_default_timezone_get()));
		return $format ? $dt->format($format) : strftime('%c', $dt->getTimestamp());
	}
}