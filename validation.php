<?php


  class validation
        {
          public function emptyPost($value)
              {
                if (!empty($value))
                  {
                    return $value;
                  }
                  else
                  {
                    throw new Exception("Please try again");
                  }

              }

        }







 ?>
