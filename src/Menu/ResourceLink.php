<?php


namespace Luna\Menu;


use Luna\Resources\Resource;

class ResourceLink extends Link
{

    protected $resource;
    /**
     * @var Resource|null
     */
    protected $temp = null;

    function __construct($class)
    {
        $this->resource = $class;
    }

    public static function make($class)
    {
        return new static($class);
    }

    protected function getLinkType()
    {
        return 'resource';
    }

    protected function getTitle()
    {
        return $this->getResourceObject()->getPlural();
    }

    function export()
    {
        return parent::export() + [
                'resource' => (new \ReflectionClass($this->resource))->getShortName(),
            ];
    }

    /**
     * @return Resource
     */
    protected function getResourceObject()
    {
        if (is_object($this->resource)) {
            return $this->resource;
        }

        if (is_null($this->temp)) {
            $this->temp = new $this->resource;
        }

        return $this->temp;
    }
}
