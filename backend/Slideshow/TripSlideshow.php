<?php
namespace Backend\Slideshow;

require_once('Slideshow.php');

use Backend\Slideshow\Slideshow;

class TripSlideshow implements Slideshow {
  private $imageNames;
  private $captions;
  private $location;
  private $trimmedLocation;
  private $year;
  private $showImageFiles;

  public function __construct($slideData) {
    $this->imageNames = $slideData['image_names'];
    $this->captions = $slideData['caption'];
    $this->location = $slideData['location'];
    $this->trimmedLocation = str_replace(' ', '', $this->location);
    $this->year = $slideData['year'];
    $this->showImageFiles = $slideData['image_files'];
  }

  public function slideshowTemplate() {
    return file_get_contents(__DIR__ . "/../templates/trip.html");    
  }

  public function addSlideshowHtml($pageHtml) {
    $imagepath = 'photography/' . $this->year . $this->trimmedLocation . '/images';
    foreach($this->imageNames as $index => $name) {
      $image_src = "$imagepath/$name";

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

  public function copySlideshowImages() {
    // Copy image files to webpage image directory
    $imagepath = $_ENV['IMAGES_DIR'] . '/' . $this->year . $this->trimmedLocation . '/images';
    if (!file_exists($imagepath)) {
      mkdir($imagepath, 0705, true);
    }
    $imagefiles = $this->showImageFiles;
    foreach ($imagefiles["error"] as $key => $error) {
      if ($error == UPLOAD_ERR_OK) {
          $tmp_name = $imagefiles["tmp_name"][$key];
          // basename() may prevent filesystem traversal attacks;
          // further validation/sanitation of the filename may be appropriate
          $name = basename($imagefiles["name"][$key]);
          move_uploaded_file($tmp_name, "$imagepath/$name");
      }
    }
  }

  public function slideshowFile(string $pageHtml) {
    $filepath = $_ENV['PAGES_DIR'] . '/' . $this->year . $this->trimmedLocation;
    $filename = $this->year . $this->trimmedLocation . "_slideshow.html";
    $fp = fopen("$filepath/$filename", 'w');
    $result = fwrite($fp, $pageHtml);
    fclose($fp);
    return $result;
  }
}