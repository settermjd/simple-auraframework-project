<?php

namespace DatabaseObjects\Entity;

class SalesData
{
    /**
     * @var int
     */
    public $TrackId;

    /**
     * @var float
     */
    public $TotalSales;

    /**
     * @var string
     */
    public $TrackName;

    /**
     * @var string
     */
    public $Genre;

    /**
     * @var string
     */
    public $AlbumTitle;

    /**
     * @var string
     */
    public $ArtistName;

    public function __construct($data = array())
    {
        if (!empty($data)) {
            foreach(get_class_vars(__CLASS__) as $property => $value) {
                if (isset($data[$property])) {
                    $this->{$property} = $data[$property];
                }
            }
        }
    }
}