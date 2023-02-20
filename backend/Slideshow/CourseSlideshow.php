<?php
namespace Backend\Slideshow;
  
require_once('Slideshow.php');

use Backend\Slideshow\Slideshow;  
use Backend\Slideshow\SlideshowService;
use Backend\Slideshow\SlideshowValidator;

class CourseSlideshow implements Slideshow {
  private $season;
  private $year;
  private $organization;
  private $minGrade;
  private $maxGrade;
  private $day;
  private $classTime;
  private $captions;
  private $imageGroups;
  private $newAppend;
  private $showFileName;
  private $showImageFiles;

  public function __construct($slideData) {
    $this->season = $slideData['season'];
    $this->year = $slideData['year'];
    $this->organization = $slideData['organization'];
    $this->minGrade = $slideData['min_grade'];
    $this->maxGrade = $slideData['max_grade'];
    $this->day = $slideData['day'];
    $this->classTime = isset($slideData['class_time']) ? $slideData['class_time'] : '';
    $this->captions = $slideData['caption'];
    $this->imageGroups = json_decode($slideData['image_groups']);
    $this->newAppend = $slideData['new_append'];

    $this->showFileName = $this->organization . $this->minGrade . "-" . $this->maxGrade . $this->day . 
                          $this->classTime . $this->season . $this->year . ".html";

    $this->showImageFiles = $slideData['image_files'];
  }

  public function slideshowTemplate() {
    if ($this->newAppend === 'append') {
      return file_get_contents(__DIR__ . "/../../pages/" . $this->showFileName);    
    }
    return file_get_contents(__DIR__ . "/../templates/course.html");    
  }

  public function addSlideshowHtml($pageHtml) {
    // Add start of project links to top of slideshow page
    $pageHtml = $this->addLinks($pageHtml);

    // Add slideshow
    $imagePath = "images/" . $this->season . $this->year . "/" . $this->organization . $this->minGrade . "-" . $this->maxGrade . $this->day . $this->classTime;
    $caption_index = 0;
    foreach($this->imageGroups as $group) {
      $num_images = count($group);
      foreach($group as $key => $value) {
        $image_src = "$imagePath/$value";
        $image_num = $key + 1;
  
        // Slide html
        $slideHtml = Slideshow::SLIDE_START;
        $slideHtml .= Slideshow::SLIDE_CAPTION_START . $this->captions[$caption_index];
        $slideHtml .= Slideshow::SLIDE_NUM_START . " ($image_num / $num_images) " . Slideshow::SLIDE_NUM_END;
        $slideHtml .= Slideshow::SLIDE_CAPTION_END;
        $slideHtml .= Slideshow::SLIDE_IMAGE_START . $image_src . Slideshow::SLIDE_IMAGE_END;
        $slideHtml .= Slideshow::SLIDE_END;
  
        // Insert slide html into page html
        $slidePos = strpos($pageHtml, "</ul>");
        $pageHtml = substr_replace($pageHtml, $slideHtml, $slidePos, 0);
  
        $caption_index++;
      }
    }

    return $pageHtml;
  }

  public function copySlideshowImages() {
    // Copy image files to webpage image directory
    $imagepath = $_ENV['IMAGES_DIR'] . '/' . $this->season . $this->year . '/' . $this->organization . 
                  $this->minGrade . "-" . $this->maxGrade . $this->day . $this->classTime;
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
    $filepath = $_ENV['PAGES_DIR'];
    $filename = $this->showFileName;
    $fp = fopen("$filepath/$filename", 'w');
    $result = fwrite($fp, $pageHtml);
    fclose($fp);
    return $result;
  }

  // Private methods
  private function addLinks(string $pageHtml) {
    // Get unique project names and their indices
    $projectNames = array_unique($this->captions);
    $linksHtml = '';
    foreach($projectNames as $key => $name) {
      $linksHtml .= "\n\t<a href=\"\" data-index=\"$key\">$name</a>";
    }
    
    // Insert links html into page html
    $linksStartHtml = "<div class=\"carousel-nav\">";
    $linksPos = strpos($pageHtml, $linksStartHtml) + strlen($linksStartHtml);
    $pageHtml = substr_replace($pageHtml, $linksHtml, $linksPos, 0);

    return $pageHtml;
  }
}