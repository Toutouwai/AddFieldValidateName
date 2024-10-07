<?php namespace ProcessWire;

class AddFieldValidateName extends WireData implements Module {

	/**
	 * Ready
	 */
	public function ready() {
		$this->addHookBefore('ProcessField::executeAdd', $this, 'beforeAdd');
	}

	/**
	 * Before ProcessField::executeAdd
	 *
	 * @param HookEvent $event
	 */
	protected function beforeAdd(HookEvent $event) {
		$config = $this->wire()->config;
		$fields = $this->wire()->fields;
		$languages = $this->wire()->languages;

		// Add module assets
		$info = $this->wire()->modules->getModuleInfo($this);
		$version = $info['version'];
		$config->scripts->add($config->urls->$this . "$this.js?v=$version");
		$config->js($this->className, ['base_url' => $this->wire()->page->url . 'add']);

		// Validate field name on AJAX request
		$validate_name = $this->wire()->input->get('validate_field_name');
		if($config->ajax && $validate_name) {
			$event->replace = true;

			// Below is largely copied from ProcessField::isAllowedName()
			$_name = $validate_name;
			$name = $this->wire()->sanitizer->fieldName($validate_name);
			$allowed = false;
			$okay = array('files', 'datetime'); // field names that are okay, even if they collide with something else
			$error = '';

			if(empty($name)) {
				$error = $this->_('Field name is empty');
			} else if($name !== $_name) {
				$error = $this->_('Field names may only contain ASCII letters, digits or underscore.');
			} else if($fields->get($name)) {
				$error = sprintf($this->_('Field name "%s" is already in use'), $name);
			} else if(($this->wire($name) || $fields->isNative($name)) && !in_array($name, $okay)) {
				$error = sprintf($this->_('Field name "%s" is a reserved word'), $name);
			} else if(preg_match('/^(_|\d)/', $name)) {
				$error = $this->_('Field names may not begin with "_" or digits');
			} else if($languages && $languages->get($name)->id) {
				$error = $this->_('Field name may not be the same as a Language name');
			} else {
				$allowed = true;
			}

			// Return JSON response
			$event->return = json_encode([
				'allowed' => $allowed,
				'error' => $error,
			]);
		}
	}

}
