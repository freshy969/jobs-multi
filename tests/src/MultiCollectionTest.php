<?php namespace JobApis\Jobs\Client\Tests;

use JobApis\Jobs\Client\Collection;
use JobApis\Jobs\Client\MultiCollection;
use Mockery as m;

class MultiCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MultiCollection
     */
    protected $collection;

    public function setUp()
    {
        $this->collection = new MultiCollection();
    }

    public function testItCanAppendCollectionWhenCollectionHasItemsAndErrors()
    {
        $error = uniqid();

        // Add an item to the test collection
        $this->collection->add($this->getItem());

        // Create a new collection with 2 items and 1 error
        $collection = (new Collection())
            ->add($this->getItem())
            ->add($this->getItem())
            ->addError($error);

        $this->collection->append($collection);

        $this->assertEquals(3, $this->collection->count());
        $this->assertEquals($error, $this->collection->getErrors()[0]);
    }

    public function testItCanAppendCollectionWhenCollectionHasNoItems()
    {
        $error = uniqid();

        // Add an item to the test collection
        $this->collection->add($this->getItem());

        // Create a new collection with 1 error
        $collection = (new Collection())
            ->addError($error);

        $this->collection->append($collection);

        $this->assertEquals(1, $this->collection->count());
        $this->assertEquals($error, $this->collection->getErrors()[0]);
    }

    public function testItCanFilterItemsWhenFieldExists()
    {
        // Add some items to the test collection
        $item = $this->getItem();
        $this->collection->add($item)
            ->add($this->getItem());

        // Filter
        $this->collection->filter('id', $item->id);

        // Test the results
        $this->assertEquals(1, $this->collection->count());
        $this->assertEquals($item, $this->collection->get(0));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Property not defined.
     */
    public function testItCannotFilterItemsWhenFieldNotExists()
    {
        // Add some items to the test collection
        $item = $this->getItem();
        $this->collection->add($item)
            ->add($this->getItem());

        // Filter
        $this->collection->filter(uniqid(), $item->id);
    }

    public function testItCanOrderItemsWhenFieldExists()
    {
        // Add some items to the test collection
        $this->collection
            ->add($this->getItem())
            ->add($this->getItem())
            ->add($this->getItem());

        // Filter
        $this->collection->orderBy('id');

        // Test the results
        $this->assertEquals(3, $this->collection->count());
        $prevItem = null;
        foreach($this->collection->all() as $item) {
            if ($prevItem) {
                $this->assertLessThan($prevItem, $item);
            }
            $prevItem = $item;
        }
    }

    public function testItCanOrderItemsAscWhenFieldExists()
    {
        // Add some items to the test collection
        $this->collection
            ->add($this->getItem())
            ->add($this->getItem())
            ->add($this->getItem());

        // Filter
        $this->collection->orderBy('id', 'asc');

        // Test the results
        $this->assertEquals(3, $this->collection->count());
        $prevItem = null;
        foreach($this->collection->all() as $item) {
            if ($prevItem) {
                $this->assertGreaterThan($prevItem, $item);
            }
            $prevItem = $item;
        }
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Property not defined.
     */
    public function testItCannotOrderItemsWhenFieldNotExists()
    {
        // Add some items to the test collection
        $this->collection
            ->add($this->getItem())
            ->add($this->getItem())
            ->add($this->getItem());

        // Filter
        $this->collection->orderBy(uniqid());
    }

    public function testItCanTruncateCollection()
    {
        // Add some items to the test collection
        $this->collection
            ->add($this->getItem())
            ->add($this->getItem())
            ->add($this->getItem());

        // Filter
        $this->collection->truncate(1);

        // Test the results
        $this->assertEquals(1, $this->collection->count());
    }

    private function getItem()
    {
        return (object) [
            'id' => uniqid(),
        ];
    }
}
