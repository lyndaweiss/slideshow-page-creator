<?php
namespace Backend\Slideshow;

require_once('Slideshow.php');

use Backend\Slideshow\Slideshow;

class SlideshowService {
  private $slideshow;

  public function __construct(Slideshow $slideshow) {
    $this->slideshow = $slideshow;
  }

  public function createSlideshow() {
    $pageHtml = $this->slideshow->slideshowTemplate();
    $pageHtml = $this->slideshow->addSlideshowHtml($pageHtml);
    $slideshowCreated = $this->slideshow->slideshowFile($pageHtml);
    $this->slideshow->copySlideshowImages();
    return $slideshowCreated;
  }
}