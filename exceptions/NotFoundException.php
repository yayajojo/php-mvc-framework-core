<?php
namespace mayjhao\phphmvc\exceptions;

use Exception;

class NotFoundException extends Exception
{
  protected $code = 404;
  protected $message = 'The page is not found.';
}