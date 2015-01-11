<?php
namespace dobj;

class Event extends DisplayDObj {

	protected $id_network;
	protected $id_host;
	protected $date_created;
	protected $event_date;
	protected $title;
	protected $address_1;
	protected $address_2;
	protected $city;
	protected $country;
	protected $description;
	protected $region;

	// db stuff
	protected $network;
	protected $network_class;
	protected $city_cur;
	protected $region_cur;
	protected $country_cur;
	protected $city_origin;
	protected $region_origin;
	protected $country_origin;
	protected $language_origin;
	protected $origin;
	protected $location;
	protected $usr_image;

	protected $host;
	protected $email;
	protected $username;
	protected $first_name;
	protected $last_name;
	protected $img_link;

	public static function createFromId($id, $dal, $do2db) {

		// stub
	}

	public function display($context) {

	}

	public function getHTML($context, $vars) {

		// get vars
		$cm = $vars['cm'];
		$mustache = $vars['mustache'];

		switch($context) {

		case 'card':

			// get template
			$template = file_get_contents($cm->template_dir . $cm->ds . 'network-event-card.html');
			return $mustache->render($template, array(
				'event' => $this,
				'host' => $this->getName(),
				'date' => $this->formatDate(),
				'vars' => $cm->getVars()
				)
			);
			break;

		case 'modal':

			// get template
			$template = file_get_contents($cm->template_dir . $cm->ds . 'network-event-modal.html');
			return $mustache->render($template, array(
				'event' => $this,
				'host' => $this->getName(),
				'vars' => $cm->getVars()
				)
			);
			break;

		case 'dashboard':
			// get template
			$template = file_get_contents($cm->template_dir . $cm->ds . 'dashboard-event.html');
			return $mustache->render($template, array(
				'event' => $this,
				'host' => $this->getName(),
				'vars' => $cm->getVars()
				)
			);
			break;

		}
	}

	public function getName() {

			// last resort, email
			if (isset($this->email))
				$name = $this->email;

			// make name username
			if (isset($this->username))
				$name = $this->username;

			// prioritize names
			if (isset($this->first_name)) {
				$name = $this->first_name;

				if (isset($this->last_name))
					$name .= ' '.$this->last_name;
			}
		return $name;
	}

	public function formatDate() {
		$date = new \DateTime($this->event_date);
		return $date->format('D jS @ g:iA'); // thanks, php for easy formatting
	}

	public function getSplit($property) {

		switch ($property) {
		case 'month':
			$date = new \DateTime($this->event_date);
			$obj = new \dobj\Blank();
			$obj->month = $date->format('M');
			$obj->year = $date->format('Y');
			return $obj;
		case 'network':
			return $this->getNetworkTitle();
		default:
			return $this->$property;
		}
	}
}
