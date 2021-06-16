<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Command\Person;

class DeletePersonCommand
{
   public function __construct(
       public int $id
   ){}
}