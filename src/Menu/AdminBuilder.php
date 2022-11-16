<?php

namespace App\Menu;

use Knp\Menu\ItemInterface;
use SmartCoreBundle\Menu\BaseBuilder;

class AdminBuilder extends BaseBuilder
{
    public function mainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $this->addItem($menu, 'Articles', 'article_index', 'edit');

        return $menu;
    }
}
