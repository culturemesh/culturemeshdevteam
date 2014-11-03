<?php

class DObjListTest extends PHPUnit_Framework_TestCase {

	private $dlist;

	protected function setUp() {

		$this->dlist = new dobj\DObjList();	
	}

	/**
	 * @covers DObjList::__construct
	 *
	 */
	public function testConstruct() {

		$this->assertInstanceOf('dobj\DObjList', $this->dlist);
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @covers DObjList::dInsert
	 */
	public function testInsertFail() {

		$this->setExpectedException('InvalidArgumentException');
		$this->dlist->dInsert('fake string');
	}

	/**
	 * @covers DObjList::dInsert
	 * 
	 */
	public function testInsert() {

		$dobj = new dobj\DObj();
		$this->dlist->dInsert($dobj);

		$this->assertCount(1, $this->dlist);
	}

	/**
	 * @covers DObjList::dInsert
	 * 
	 */
	public function testTraverse() {

		// insert items
		$dobj_1 = new dobj\DObj();
		$dobj_1->id = 1;
		$dobj_2 = new dobj\DObj();
		$dobj_2->id = 2;

		$this->dlist->dInsert($dobj_1);
		$this->dlist->dInsert($dobj_2);

		$dtest = new dobj\DObjList();

		// insert into test array
		foreach ($this->dlist as $do) {

			$dtest->dInsert($do);
		}

		$this->assertCount(2, $dtest);
	}

	/**
	 * @covers DObjList::sort
	 *
	 */
	public function testSortAsc() {

		// insert items
		$dobj_1 = new dobj\DObj();
		$dobj_1->id = 1;
		$dobj_2 = new dobj\DObj();
		$dobj_2->id = 2;
		$dobj_3 = new dobj\DObj();
		$dobj_3->id = 3;
		$dobj_4 = new dobj\DObj();
		$dobj_4->id = 3;
	
		$olist = new dobj\DObjList();
		$olist->dInsert($dobj_1);
		$olist->dInsert($dobj_2);
		$olist->dInsert($dobj_3);
		$olist->dInsert($dobj_4);

		$this->dlist->dInsert($dobj_3);
		$this->dlist->dInsert($dobj_1);
		$this->dlist->dInsert($dobj_4);
		$this->dlist->dInsert($dobj_2);
		// sort ascending on id
		$this->dlist->sort('id', '+');

		//assert
		for ($i = 0; $i < count($olist); $i++) {
			$this->assertEquals($olist[$i]->id, $this->dlist[$i]->id);
		}
	}

	/**
	 * @covers DObjList::sort
	 *
	 */
	public function testSortDesc() {

		// insert items
		$dobj_1 = new dobj\DObj();
		$dobj_1->id = 3;
		$dobj_2 = new dobj\DObj();
		$dobj_2->id = 2;
		$dobj_3 = new dobj\DObj();
		$dobj_3->id = 1;
		$dobj_4 = new dobj\DObj();
		$dobj_4->id = 1;
	
		$olist = new dobj\DObjList();
		$olist->dInsert($dobj_1);
		$olist->dInsert($dobj_2);
		$olist->dInsert($dobj_3);
		$olist->dInsert($dobj_4);

		$this->dlist->dInsert($dobj_3);
		$this->dlist->dInsert($dobj_1);
		$this->dlist->dInsert($dobj_4);
		$this->dlist->dInsert($dobj_2);

		// sort ascending on id
		$this->dlist->sort('id', '-');

		//assert
		for ($i = 0; $i < count($olist); $i++) {
			$this->assertEquals($olist[$i]->id, $this->dlist[$i]->id);
		}
	}
}
?>