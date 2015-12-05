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

		$dobj = new dobj\User();
		$this->dlist->dInsert($dobj);

		$this->assertCount(1, $this->dlist);
	}

	/**
	 * @covers DObjList::dInsert
	 * 
	 */
	public function testTraverse() {

		// insert items
		$dobj_1 = new dobj\User();
		$dobj_1->id = 1;
		$dobj_2 = new dobj\User();
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

	public function testSection() {

		// insert items
		$dobj_1 = new dobj\Blank();
		$dobj_1->id = 3;
		$dobj_2 = new dobj\Blank();
		$dobj_2->id = 2;
		$dobj_3 = new dobj\Blank();
		$dobj_3->id = 1;
		$dobj_4 = new dobj\Blank();
		$dobj_4->id = 1;
		$dobj_5 = new dobj\Blank();
		$dobj_5->id = 3;
	
		$olist = new dobj\DObjList();
		$olist->dInsert($dobj_1);
		$olist->dInsert($dobj_2);
		$olist->dInsert($dobj_3);
		$olist->dInsert($dobj_4);
		$olist->dInsert($dobj_5);

		$solist = $olist->splits('id');

		$this->assertEquals(3, count($solist));
	}

	public function testMultiSection() {

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

		$this->assertEquals(3, count($solist));
		$this->assertEquals(5, $solist->countAll());
		$this->assertEquals(2, $solist->countSection('id', 1));
		$this->assertEquals(3, $solist->countSection('class', 'a'));
		$this->assertEquals(2, $solist->countSection('class', 'b'));
	}

	/**
	 * @covers UserList::sort
	 *
	 */
	public function testSortAsc() {

		$this->markTestSkipped('Not sorting lists atm');

		// insert items
		$dobj_1 = new dobj\User();
		$dobj_1->id = 1;
		$dobj_2 = new dobj\User();
		$dobj_2->id = 2;
		$dobj_3 = new dobj\User();
		$dobj_3->id = 3;
		$dobj_4 = new dobj\User();
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
		/*
		for ($i = 0; $i < count($olist); $i++) {
			$this->assertEquals($olist[$i]->id, $this->dlist[$i]->id);
		}
		 */
	}

	/**
	 * @covers DObjList::sort
	 *
	 */
	public function testSortDesc() {

		$this->markTestSkipped('Not sorting list atm');

		// insert items
		$dobj_1 = new dobj\User();
		$dobj_1->id = 3;
		$dobj_2 = new dobj\User();
		$dobj_2->id = 2;
		$dobj_3 = new dobj\User();
		$dobj_3->id = 1;
		$dobj_4 = new dobj\User();
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
		/*
		for ($i = 0; $i < count($olist); $i++) {
			$this->assertEquals($olist[$i]->id, $this->dlist[$i]->id);
		}
		 */
	}

	/**
	 * @covers Nothing, really
	 *
	 */
	public function testElementChange() {

		$x = new dobj\Blank();
		$x->number = 1;
		$y = new dobj\Blank();
		$y->number = 2;
		$z = new dobj\Blank();
		$z->number = 3;

		$list = new dobj\DObjList();
		$list->dInsert($x);
		$list->dInsert($y);

		$this->assertEquals($list[0]->number, 1);

		// make the change
		$list[0] = $z;

		$this->assertEquals($list[0]->number, 3);
	}

}
?>
