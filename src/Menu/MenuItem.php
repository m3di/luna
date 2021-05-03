<?php


namespace Luna\Menu;


abstract class MenuItem
{
    abstract function getItemTitle();
    abstract function getItemType();

    function export() {
        return [
            'title' => $this->getItemTitle(),
            'type' => $this->getItemType(),
        ];
    }
}
