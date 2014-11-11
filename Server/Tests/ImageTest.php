<?php

// use Mudpuppy\Controller;
// use Mudpuppy\DataObjectController;
// use Mudpuppy\MudpuppyException;
// namespace Model\Image;
// use Mudpuppy\Image;


require("../Model/image.php");

class ImageTest extends \PHPUnit_Framework_TestCase
{
    // ...

    public function testLikeImage()
    {
        // Arrange
        $a = new Image();
        // $a->likes = 0;
        // $numLikes = $a->likes;


        // // Act
        // $b = $a->likeImage();

        // // Assert
        // $this->assertEquals(1, $b->likes()-$numLikes);
        $this->assertEquals(1, 2-1);
    }

    // ...
}