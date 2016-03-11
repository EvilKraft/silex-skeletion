<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;

class LangtabsType extends AbstractType
{

    public function getParent()
    {
        return 'collection';
    }

    public function getName()
    {
        return 'langtabs';
    }
}