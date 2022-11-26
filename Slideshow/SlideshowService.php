<?php
namespace Slideshow;

require_once('Slideshow.php');

use Slideshow\Slideshow;

class SlideshowService {
  private $slideshow;

  public function __construct(Slideshow $slideshow) {
    $this->slideshow = $slideshow;
  }

  public function createSlideshow() {
    $pageHtml = $this->slideshow->slideshowTemplate();
    $pageHtml = $this->slideshow->addSlides($pageHtml);
    $slideshowCreated = $this->slideshow->slideshowFile($pageHtml);
    return $slideshowCreated;
  }
}