<?php

namespace Luna\Types;


use Illuminate\Database\Eloquent\Builder;
use Luna\Panels\Panel;
use Luna\Panels\PanelSimple;
use Luna\Repositories\ResourceModelRepository;
use Luna\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class HasMany extends Relation
{
    use HasMetrics;

    protected $type = 'has_many';

    protected $visibleOnDetails = true;
    protected $visibleOnIndex = false;
    protected $visibleWhenCreating = false;
    protected $visibleWhenEditing = false;

    protected $usesSeparatedSpace = true;

    /** @var Type[] */
    protected $fields = [];
    /** @var Panel[] */
    protected $panels = [];

    protected $foreignField = null;

    protected $disableCreateLink = false;
    protected $disableEditLink = false;
    protected $disableDetailsLink = false;
    protected $disableDeleteLink = false;

    protected $showIndexColumn = false;

    protected $query = null;
    protected $search = true;
    protected $searchables = null;
    protected $dependencies = [];

    function foreign($field)
    {
        $this->foreignField = $field;
        return $this;
    }

    function getForeignField()
    {
        return is_null($this->foreignField) ? strtolower((new \ReflectionClass($this->resource->model))->getShortName()) : $this->foreignField;
    }

    function disableCreateLink()
    {
        $this->disableCreateLink = true;
        return $this;
    }

    function disableEditLink()
    {
        $this->disableEditLink = true;
        return $this;
    }

    function disableDetailsLink()
    {
        $this->disableDetailsLink = true;
        return $this;
    }

    function disableDeleteLink()
    {
        $this->disableDeleteLink = true;
        return $this;
    }

    function withIndexColumn()
    {
        $this->showIndexColumn = true;
        return $this;
    }

    function query($query)
    {
        $this->query = $query;
        return $this;
    }

    function noSearch()
    {
        $this->search = false;
        return $this;
    }

    function searchable(...$fields)
    {
        $this->searchables = $fields;
        return $this;
    }

    function dependencies(...$dependencies)
    {
        $this->dependencies = $dependencies;
        return $this;
    }

    /**
     * @return static
     */
    static function make($relation, $resource)
    {
        return (new static($relation))
            ->relation($relation)
            ->relationResource($resource);
    }

    function fields($fields)
    {
        $lastPanel = null;

        /** @var Type|Panel $field */
        foreach (call_user_func($fields) as $field) {
            if ($field instanceof Panel) {
                if (!is_null($lastPanel)) {
                    $this->panels[] = $lastPanel;
                    $lastPanel = null;
                }

                $this->panels[] = $field;

                foreach ($field->getFields() as $f) {
                    $this->fields[$f->getName()] = $f;
                }
            } else {
                if ($field->usesSeparatedSpace()) {
                    if (!is_null($lastPanel)) {
                        $this->panels[] = $lastPanel;
                        $lastPanel = null;
                    }

                    $this->panels[] = PanelSimple::make($field->getTitle(), false)->appendField($field);
                    $this->fields[$field->getName()] = $field;
                } else {
                    if (is_null($lastPanel)) {
                        $lastPanel = new PanelSimple();
                    }

                    $lastPanel->appendField($field);
                    $this->fields[$field->getName()] = $field;
                }
            }
        }

        if (!is_null($lastPanel)) {
            $this->panels[] = $lastPanel;
            $lastPanel = null;
        }

        return $this;
    }

    function getTitle()
    {
        return $this->title ?? $this->getRelationResource()->getPlural();
    }

    function fillFromRequest(Request $request, Model $model, \Closure $afterSaveTask)
    {

    }

    public function handelActionRequest(Request $request, Resource $resource, Model $model)
    {
        if ($request->method() == 'GET' && $request->get('action') == 'index') {
            return $this->actionIndex($request, $resource, $model);
        }

        if ($request->method() == 'GET' && $request->get('action') == 'metric') {
            return $this->actionMetric($request, $resource, $model, $request->get('metric'));
        }

        return null;
    }

    function resolveFor(Model $model, $pivot = false)
    {
        return '';
    }

    function displayFor(Model $model, $pivot = false)
    {
        return '';
    }

    function export()
    {
        return parent::export() + [
                'primaryKey' => $this->getRelationResource()->getPrimaryKey(),
                'foreign' => $this->getForeignField(),
                'panels' => array_map(function (Panel $a) {
                    return $a->export();
                }, $this->panels),
                'metrics' => $this->exportMetrics(),
                'create' => !$this->disableCreateLink,
                'edit' => !$this->disableEditLink,
                'details' => !$this->disableDetailsLink,
                'delete' => !$this->disableDeleteLink,
                'index_column' => $this->showIndexColumn,
                'dependencies' => $this->dependencies,
                'search' => $this->search,
            ];
    }

    private function actionIndex(Request $request, Resource $resource, Model $model)
    {
        $relation = $this->getRelationResource();

        $fields = array_filter($this->fields, function (Type $field) use ($relation) {
            return $field->isVisibleOnIndex() || $field->getName() == $relation->getPrimaryKey();
        });

        $query = call_user_func([$model, $this->getRelation()]);

        if ($this->query) {
            if ($result = call_user_func_array($this->query, [$query, $model])) {
                $query = $result;
            }
        }

        $paginate = ResourceModelRepository::make($relation, $query)
            ->fields($fields)
            ->filters(json_decode($request->get('filters', '[]'), true));

        if ($this->searchables) {
            $paginate->searchables($this->searchables);
        }

        $paginate = $paginate
            ->searchFor(trim($request->get('search')))
            ->sortBy(trim($request->get('sort')), $request->has('desc'))
            ->getQueryWithRelations()
            ->paginate(20);

        $data = [];

        foreach ($paginate as $item) {
            $row = [];

            foreach ($fields as $field) {
                $row[$field->getName()] = $field->displayFor($item);
            }

            $data[] = $row;
        }

        return [
            'data' => $data,
            'total' => $paginate->total(),
            'per_page' => $paginate->perPage(),
            'current_page' => $paginate->currentPage(),
        ];
    }

    private function actionMetric($request, $resource, $model, $metric)
    {
        return $this->getMetric($metric)->handelRequest($request, $resource, $model);
    }

    /**
     * @param Builder|\Illuminate\Database\Eloquent\Relations\Relation $query
     * @return mixed
     */
    function getLocalKeyName($query)
    {
        return call_user_func([$query->getModel(), $this->getRelation()])->getOwnerKeyName();
    }
}
