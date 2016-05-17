<?php
namespace App\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class LangtabsType extends AbstractType
{

    public function getParent()
    {
        return CollectionType::class;
    }

    public function getName()
    {
        return 'langtabs';
    }
}