<?php

function registerConversation($obj) {
	/*
	 * TEST
	 */
	$obj->getConversationById = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM conversations
WHERE id=?
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getConversationById',
			'params' => array('id'),
			'param_types' => 's',
			'returning' => true,
			'returning_list' => False,
			'returning_class' => 'dobj\Conversation',
			'returning_cols' => array('id')
		));

		$m->setConnection($con);
		return $m;
	};
}
?>
