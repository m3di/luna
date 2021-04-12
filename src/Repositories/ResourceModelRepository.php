<?php

namespace Luna\Repositories;


use Luna\Resources\Resource;
use Luna\Types\Relation;
use Luna\Types\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ResourceModelRepository
{
    /** @var Resource */
    protected $resource;

    /** @var Builder */
    protected $query;

    /** @var Type[] */
    protected $fields = [];

    protected $searchable = [];

    /**
     * Repository constructor.
     * @param Resource $builder
     */
    public function __construct(Resource $resource, $query = null)
    {
        $this->resource = $resource;
        $this->query = is_null($query) ? $resource->getQuery() : $query;

        $this->searchable = $resource->getSearchable();
    }

    public static function make($resource, $query = null)
    {
        return new static($resource, $query);
    }

    function searchables(array $fields)
    {
        $this->searchable = $fields;
        return $this;
    }

    function searchFor($q)
    {
        $q = trim($q);

        if (count($this->searchable) > 0 && $q) {
            $this->query->where(function (Builder $query) use ($q) {
                foreach ($this->searchable as $item) {
                    $query->orWhere($item, 'LIKE', '%' . $q . '%');
                }
            });
        }

        return $this;
    }

    function fields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    function filters($filters)
    {
        foreach ($this->fields as $field) {
            if ($field->isFilterable() && isset($filters[$field->getName()])) {
                $field->applyFilterToBuilder($this->query, $filters[$field->getName()]);
            }
        }

        return $this;
    }

    function sortBy($sort, $desc)
    {
        if ($sort) {
            foreach ($this->fields as $field) {
                if ($field->getName() == $sort && $field->isSortable()) {
                    $field->applySort($this->query, $desc);
                    return $this;
                }
            }
        }

        return $this;
    }

    /**
     * @return Builder
     */
    function getQuery()
    {
        return $this->query;
    }

    /**
     * @return Builder
     */
    function getQueryWithRelations()
    {
        $relations = [];

        foreach ($this->fields as $field) {
            if ($field instanceof Relation) {
                $rel = $field->getRelation();

                if ($rel) {
                    $relations[] = $rel;
                }
            }
        }

        return $this->query->with($relations);
    }
}
