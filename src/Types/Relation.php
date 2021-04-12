<?php

namespace Luna\Types;


use Luna\Resources\Resource;

abstract class Relation extends Type
{
    protected $relation = null;
    protected $relation_resource = null;

    function getRelation()
    {
        return $this->relation;
    }

    /**
     * @return Resource
     */
    function getRelationResource()
    {
        return app('luna')->getResource($this->relation_resource);
    }

    function relation($relation)
    {
        $this->relation = $relation;
        $this->columnName = null;
        return $this;
    }

    function relationResource($resource)
    {
        $this->relation_resource = $resource;
        return $this;
    }

    function export()
    {
        $resource = $this->getRelationResource();

        return [
                'relation' => $this->relation,
                'relation_resource' => (new \ReflectionClass($resource))->getShortName(),
            ] + parent::export();
    }

}