<?php
namespace Backend\Slideshow;

require_once('Slideshow.php');

use Backend\Slideshow\Slideshow;

class TripSlideshow implements Slideshow {
  private $imageNames;
  private $captions;
  private $location;
  private $year;

  public function __construct($slideData) {
    $this->imageNames = $slideData['image_names'];
    $this->captions = $slideData['caption'];
    $this->location = $slideData['location'];
    $this->year = $slideData['year'];
  }

  public function slideshowTemplate() {
    return file_get_contents(__DIR__ . "/../templates/trip.html");    
  }

  public function addSlides($pageHtml) {
    $trimmedLocation = str_replace(' ', '', $this->location);
    $imagePath = "photography/" . $this->year . "$trimmedLocation/images/";
    foreach($this->imageNames as $index => $name) {
      $image_src = $imagePath . $name;

      // Slide html
      $slideHtml = Slideshow::SLIDE_START;
      $slideHtml .= Slideshow::SLIDE_IMAGE_START . $image_src . Slideshow::SLIDE_IMAGE_END;
      $slideHtml .= Slideshow::SLIDE_CAPTION_START . $this->captions[$index] . Slideshow::SLIDE_CAPTION_END;
      $slideHtml .= Slideshow::SLIDE_END;

      // Insert slide html into page html
      $slidePos = strpos($pageHtml, "</ul>");
      $pageHtml = substr_replace($pageHtml, $slideHtml, $slidePos, 0);
    }

    return $pageHtml;
  }

  public function slideshowFile(string $pageHtml) {
    $filepath = __DIR__ . '/../../trips';
    $filename = $this->year . str_replace(' ', '', $this->location) . "_slideshow.html";
    $fp = fopen("$filepath/$filename", 'w');
    $result = fwrite($fp, $pageHtml);
    fclose($fp);
    return $result;
  }
}