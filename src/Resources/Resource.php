<?php

namespace Luna\Resources;

use Luna\Panels\PanelSimple;
use Luna\Types\Type;
use Models\Category;
use Luna;
use Luna\Actions\Action;
use Luna\Metrics\Metric;
use Luna\Panels\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

abstract class Resource
{
    public $model = 'App\Model';
    public $title = '';

    protected $visible = true;

    /** @var Type[] */
    protected $fields = [];
    /** @var Panel[] */
    protected $panels = [];

    protected $metrics = [];
    protected $actions = [];

    protected $searchable = [];
    protected $primary_key = null;

    protected $singular = null;
    protected $plural = null;

    protected $disableCreatePanel = false;
    protected $disableEditPanel = false;
    protected $disableDetailsPanel = false;
    protected $disableDeleteOption = false;

    protected $showIndexColumn = false;

    function __construct()
    {
        $this->primary_key = (new $this->model)->getKeyName();
    }

    function boot()
    {
        $this->panels = [];
        $this->fields = [];
        $this->metrics = [];
        $this->actions = [];

        $lastPanel = null;

        /** @var Type|Panel $field */
        foreach ($this->fields() as $field) {
            if ($field instanceof Panel) {
                if (!is_null($lastPanel)) {
                    $this->panels[] = $lastPanel;
                    $lastPanel = null;
                }

                $this->panels[] = $field;

                foreach ($field->getFields() as $f) {
                    $this->fields[$f->getName()] = $f;
                }
            } else if (is_a($field, Type::class)) {
                if ($field->usesSeparatedSpace()) {

                    if (!is_null($lastPanel)) {
                        $this->panels[] = $lastPanel;
                        $lastPanel = null;
                    }

                    $this->panels[] = PanelSimple::make(null, false)->appendField($field);
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

        foreach ($this->fields as $field) {
            $field->setResource($this);
        }

        foreach ($this->metrics() as $metric) {
            $this->metrics[] = $metric;
        }

        foreach ($this->actions() as $action) {
            $this->actions[] = $action;
        }
    }

    public function isVisible(): bool
    {
        return Luna::tap($this->visible);
    }

    function fields()
    {
        return [

        ];
    }

    function getField($name)
    {
        return $this->fields[$name];
    }

    function metrics()
    {
        return [

        ];
    }

    function actions()
    {
        return [

        ];
    }

    function getAction($name)
    {
        return $this->actions[$name];
    }

    function getMetric($index)
    {
        return $this->metrics[$index];
    }

    function getSingular()
    {
        return $this->singular;
    }

    function getPlural()
    {
        return $this->plural;
    }

    function getSearchable()
    {
        return $this->searchable;
    }

    /**
     * @return null
     */
    public function getPrimaryKey()
    {
        return $this->primary_key;
    }

    /** @return Type[] */
    function visibleFieldsOnIndex()
    {
        return array_filter($this->fields, function (Type $field) {
            return $field->isVisibleOnIndex() || $field->getName() == $this->primary_key;
        });
    }

    /** @return Type[] */
    function visibleFieldsOnDetails()
    {
        return array_filter($this->fields, function (Type $field) {
            return $field->isVisibleOnDetails() || $field->getName() == $this->primary_key;
        });
    }

    /** @return Type[] */
    function visibleFieldsOnCreate()
    {
        return array_filter($this->fields, function (Type $field) {
            return $field->isVisibleWhenCreating() || $field->getName() == $this->primary_key;
        });
    }

    /** @return Type[] */
    function visibleFieldsOnEdit()
    {
        return array_filter($this->fields, function (Type $field) {
            return $field->isVisibleWhenEditing() || $field->getName() == $this->primary_key;
        });
    }

    function isCreatePanelDisabled(): bool
    {
        return $this->disableCreatePanel;
    }

    function isEditPanelDisabled(): bool
    {
        return $this->disableEditPanel;
    }

    function isDetailsPanelDisabled(): bool
    {
        return $this->disableDetailsPanel;
    }

    public function isDeleteOptionDisabled(): bool
    {
        return $this->disableDeleteOption;
    }

    function export()
    {
        $className = (new \ReflectionClass($this))->getShortName();

        $export = [
            'name' => $className,
            'visible' => $this->isVisible(),
            'singular' => $this->singular ?? str_singular($className),
            'plural' => $this->plural ?? str_plural($this->singular ?? str_singular($className)),
            'primary_key' => $this->primary_key,
            'details' => !$this->disableDetailsPanel,
            'create' => !$this->disableCreatePanel,
            'edit' => !$this->disableEditPanel,
            'remove' => !$this->disableDeleteOption,
            'index_column' => $this->showIndexColumn,
        ];

        $export['panels'] = array_map(function (Panel $a) {
            return $a->export();
        }, $this->panels);

        $export['metrics'] = array_map(function (Metric $a) {
            return $a->export();
        }, $this->metrics);

        $export['actions'] = array_map(function (Action $a) {
            return $a->export();
        }, $this->actions);

        return $export;
    }

    /**
     * @return Builder
     */
    function getQuery()
    {
        return call_user_func([$this->model, 'query']);
    }

    function authorize(Authenticatable $user)
    {
        return true;
    }

    function getCreationRules()
    {
        return array_reduce($this->visibleFieldsOnCreate(), function ($carry, Type $item) {
            $carry = $carry + $item->getCreationRules();
            return $carry;
        }, []);
    }

    function getUpdateRules(Model $model)
    {
        return array_reduce($this->visibleFieldsOnEdit(), function ($carry, Type $item) use ($model) {
            $carry = $carry + $item->getUpdateRules($model);
            return $carry;
        }, []);
    }

    function getRulesMessages()
    {
        return [];
    }

    function getRulesAttributes()
    {
        return array_reduce($this->fields, function ($carry, Type $item) {
            $carry = $carry + $item->getRulesAttributes();
            return $carry;
        }, []);
    }
}
