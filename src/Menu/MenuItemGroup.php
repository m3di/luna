<?php


namespace Luna\Menu;


class MenuItemGroup extends MenuItem
{
    protected $title;
    protected $icon;
    protected $items;

    function __construct($title, $icon)
    {
        $this->title = $title;
        $this->icon = $icon;
    }

    function getItemTitle()
    {
        return $this->title;
    }

    function getItemType()
    {
        return 'group';
    }

    function items($items)
    {
        $this->items = is_callable($items) ? call_user_func($items) : $items;
        return $this;
    }

    function getItems() {
        return $this->items;
    }

    public function export()
    {
        return parent::export() + [
                'icon' => $this->icon,
                'items' => array_map(function (MenuItem $item) {
                    return $item->export();
                }, $this->getItems()),
            ];
    }

    public static function make($title, $icon = 'fa fa-cube')
    {
        return new static($title, $icon);
    }
}
