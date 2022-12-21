<?php

namespace Backend\Slideshow;

interface Slideshow {
  const SLIDE_START = "\t<li class=\"card\" data-target=\"card\">";
  const SLIDE_END = "\n\t\t</li>\n\t";
  const SLIDE_CAPTION_START = "\n\t\t\t<p class=\"caption\">";
  const SLIDE_CAPTION_END = "</p>";
  const SLIDE_NUM_START = "<span class=\"slide-number\">";
  const SLIDE_NUM_END = "</span>";
  const SLIDE_IMAGE_START = "\n\t\t\t<img src=\"";
  const SLIDE_IMAGE_END = "\" alt=\"\">";

  public function slideshowTemplate();
  public function addSlides($pageHtml);
  public function copySlideshowImages();
  public function slideshowFile(string $pageHtml);
}