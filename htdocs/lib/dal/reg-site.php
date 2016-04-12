<?php

function registerSite($obj) {

	/*
	 * Inserts a new post
	 *
	 */
	$obj->getTeamMembers = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
SELECT * FROM internal_team
SQL

		/////////////////////////////
		,	'name' => 'getTeamMembers',
			'returning' => True,
			'returning_list' => True,
			'returning_class' => 'dobj\TeamMember',
		));
		$m->setConnection($con);
		return $m;
	};

	$obj->getCareers = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
SELECT * FROM internal_careers
SQL

		/////////////////////////////
		,	'name' => 'getCareers',
			'returning' => True,
			'returning_list' => True,
			'returning_class' => 'dobj\Career',
		));
		$m->setConnection($con);
		return $m;
	};

	$obj->getPress = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
SELECT * FROM internal_press
SQL

		/////////////////////////////
		,	'name' => 'getPress',
			'returning' => True,
			'returning_list' => True,
			'returning_class' => 'dobj\Press',
		));
		$m->setConnection($con);
		return $m;
	};
}

?>
