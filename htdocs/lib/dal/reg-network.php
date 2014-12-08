<?php

function registerNetwork($obj) {

	$obj->getNetworkById = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM networks
WHERE id=?
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getNetworkById',
			'params' => array('id'),
			'param_types' => 's',
			'nullable' => array(),
			'returning' => true,
			'returning_list' => False,
			'returning_class' => 'dobj\Network',
			'returning_cols' => array('id', 'id_city_cur', 'city_cur', 'id_region_cur',
					'region_cur' , 'id_country_cur', 'country_cur', 'id_city_origin',
					'city_origin', 'id_region_origin', 'region_origin',
					'id_country_origin', 'country_origin', 'id_language_origin',
					'language_origin', 'network_class', 'date_added'
		 		)
		));

		$m->setConnection($con);

		return $m;
	};
}

?>
