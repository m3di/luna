<?php

namespace Luna\Panels;


use Luna\Types\Type;

abstract class Panel
{
    /** @var Type[] */
    protected $fields = [];
    protected $assignsSeparateSpace = false;

    abstract function getType();

    function fields($fields)
    {
        foreach (call_user_func($fields) as $field) {
            $this->appendField($field);
        }
        return $this;
    }

    function appendField(Type $field)
    {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * @return Type[]
     */
    function getFields()
    {
        return $this->fields;
    }

    public function assignsSeparateSpace(): bool
    {
        return $this->assignsSeparateSpace;
    }

    function export()
    {
        $fields = [];

        foreach ($this->fields as $field) {
            $fields[$field->getName()] = $field->export();
        }

        return [
            'fields' => $fields,
            'type' => $this->getType(),
        ];
    }
}
