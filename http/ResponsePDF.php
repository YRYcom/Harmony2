<?php
namespace Harmony2\Http;

use Adventaj\PDFFacture;

class ResponsePDF implements Response
{
  /** @var PDFFacture */
  private $pdf;

  public function __construct($pdf)
  {
    $this->pdf = $pdf;
  }

  public function getPdf()
  {
    return $this->pdf;
  }

  public function send($return = false)
  {
    if ($return == true) {
      return $this->getPdf();
    }
    $this->getPdf()->display();
  }
}