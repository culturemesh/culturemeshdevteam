<?php
namespace api;

class ComponentTwitterQuery extends TwitterQuery {

	protected $component_scope;
	protected $component_arg;

	/*
	 * No overloaded constructors...
	 *
	 * But we're still going to allow the user to build a query
	 * with one line of code
	 */
	public function __construct($network=NULL, $component) {


		// Checking to make sure we're getting a network
		//
		if ($network != NULL) {

			if (get_class($network) != 'dobj\Network') {
				throw new \Exception('TwitterQuery: Cannot build a query, was not passed a dobj\Network');
			}

			$this->buildSearch($network, $component);
		}

	}

	/*
	 * If network has language recognized by Twitter,
	 * gather data in language
	 */
	public function buildSearch($network, $component) {

		if ($component == NULL) {
			throw new \Exception('ComponentTwitterQuery: Component is NULL');
		}

		$raw_component = NULL;

		if ($component == 'origin') {
			$this->component_scope = $network->query_origin_scope;
			$raw_component = $network->getQueryOriginComponent('array');
		}
		else if ($component == 'location') {
			$this->component_scope = $network->query_location_scope;
			$raw_component = $network->getQueryLocationComponent('array');
		}

		$component = $this->explodeSlash($raw_component);
		$component_arg = $this->prepareComponent($component);

		$this->addComponents($network, $component, $component_arg);
		$this->addResultType('mixed');
		$this->addSinceDate($network);

		if ($this->using_until_date) {
			$this->addUntilDate();
		}

		// record query variables
		$this->component_arg = $component_arg;
	}

	/*
	 * Add components with some modifications.
	 * For starters, we're only adding one component
	 */
	public function addComponents($network, $component, $arg) {

		$link_term = $this->op_space . $this->op_or . $this->op_space;
		$language_code = NULL;

		// special language considerations
		if ($network->network_class == '_l') {

			$raw_language = $network->getQueryOriginComponent();
			$language_component = $this->explodeSlash($raw_language);
			$language_arg = $this->prepareComponent($language_component);

			// check for the language code
			$language_code = $this->getLanguageCode($language_arg);
		}

		if ($language_code === False) {
			$this->addArg($arg, $link_term);
		}
		else {
			// add location
			$this->addArg($arg, $link_term);
		}

		// do this next thing as well
		$this->filterRetweets();

		// add language code if kosher
		if (isset($language_code) && $language_code !== False) {
			$this->addLanguageTerm($language_code);
		}
	}
}
