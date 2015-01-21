<?php

class Magniloquent extends Eloquent {

	public function __construct($attributes = array())
	{
		parent::__construct($attributes);
		$this->validationErrors = new MessageBag;
	}

	private function performSave(array $options = array())
	{
		static::$rules = $this->mergeRules();

		// If the validation failed, return false
		if(!$this->validate($this->attributes)) return false;

		// Purge redundant fields
		$this->attributes = $this->purgeRedundant($this->attributes);

		// Auto hash passwords
		$this->attributes = $this->autoHash();

		return parent::save($options);
	}

	/**
	 * Merge Rules
	 * 
	 * Merge the rules arrays to form one set of rules
	 */
	private function mergeRules()
	{
		if($this->exists){
			$merged = array_merge_recursive(static::$rules['save'], static::$rules['update']);
		} else {
			$merged = array_merge_recursive(static::$rules['save'], static::$rules['create']);
		}
		foreach($merged as $field => $rules)
		{
			if(is_array($rules))
			{
				$output[$field] = implode("|", $rules);
			} else {
				$output[$field] = $rules;
			}
		}

		return  $output;
	}

	/**
	 * Validate
	 * 
	 * Validate input against merged rules
	 */
	private function validate($attributes)
	{
		$validation = Validator::make($attributes, static::$rules);

		if($validation->passes()) return true;

		$this->validationErrors = $validation->messages();

		return false;
	}

	/**
	 * Purge Redundant fields
	 * 
	 * Get rid of '_confirmation' fields
	 */
	private function purgeRedundant($attributes)
	{
		foreach($attributes as $key => $value)
		{
			if(!Str::endsWith( $key, '_confirmation'))
			{
				$clean[$key] = $value;
			}
		}

		return $clean;
	}

	/**
	 * Auto hash
	 * 
	 * Auto hash passwords
	 */
	private function autoHash()
	{
		if(isset($this->attributes['password']))
		{
			if($this->attributes['password'] != $this->getOriginal('password'))
			{
				$this->attributes['password'] = Hash::make($this->attributes['password']);
			}
		}
		return $this->attributes;
	}
}