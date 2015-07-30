<?php

namespace Aura\Framework_Project;

use DatabaseObjects\Entity\SalesData;

/**
 * @coversDefaultClass \DatabaseObjects\Entity\SalesData
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testObjectIsInitiallyEmpty()
    {
        $salesData = new SalesData(array());
        foreach(get_object_vars($salesData) as $property) {
            $this->assertTrue(is_null($property), "Property {$property} should be null.");
        }
    }

    /**
     * @covers ::__construct
     */
    public function testObjectCanBeInitialised()
    {
        $data = [
            'TrackId' => 1, 'TotalSales' => 100, 'TrackName' => 'Hey Baby!',
            'Genre' => 'Bubblegum Pop', 'AlbumTitle' => 'Tragic Love Songs',
            'ArtistName' => 'Unknown'
        ];

        $salesData = new SalesData($data);

        foreach(get_object_vars($salesData) as $property => $value) {
            $this->assertEquals(
                $data[$property],
                $salesData->{$property},
                sprintf("Property %s should be set to %s", $property, $data[$property])
            );
        }
    }
}
