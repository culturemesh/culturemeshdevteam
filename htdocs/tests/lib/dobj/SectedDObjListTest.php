<?php

class SectedDObjListTest extends PHPUnit_Framework_TestCase {

	public function setUp() {

	}

	public function testOrder() {

		$ol = new \dobj\DObjList();

		// insert items
		$dobj_1 = new dobj\Blank();
		$dobj_1->id = 3;
		$dobj_1->class = 'a';
		$dobj_2 = new dobj\Blank();
		$dobj_2->id = 2;
		$dobj_2->class = 'a';
		$dobj_3 = new dobj\Blank();
		$dobj_3->id = 1;
		$dobj_3->class = 'a';
		$dobj_4 = new dobj\Blank();
		$dobj_4->id = 1;
		$dobj_4->class = 'b';
		$dobj_5 = new dobj\Blank();
		$dobj_5->id = 3;
		$dobj_5->class = 'b';
	
		$olist = new dobj\DObjList();
		$olist->dInsert($dobj_1);
		$olist->dInsert($dobj_2);
		$olist->dInsert($dobj_3);
		$olist->dInsert($dobj_4);
		$olist->dInsert($dobj_5);

		$solist = $olist->splits(array('id', 'class'));

		$this->assertEquals(true, $solist->order('class', array(
			'a' => 3,
			'b' => 2)
		));

		/*
		$solist->dumpElement(0);
		 */
	}
}

?>
