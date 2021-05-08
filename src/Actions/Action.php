<?php

namespace Luna\Actions;


use Luna\Types\Type;
use Illuminate\Support\Collection;

abstract class Action
{
    protected $title;
    protected $fields = [];

    function __construct()
    {
        foreach ($this->fields() as $field) {
            $this->fields[$field->getName()] = $field;
        }
    }

    public static function make()
    {
        return new static();
    }

    public abstract function handel(array $fields, Collection $models);

    public function fields()
    {
        return [

        ];
    }

    function getFields()
    {
        return $this->fields;
    }

    function getRules()
    {
        return array_reduce($this->getFields(), function ($carry, Type $item) {
            $carry = $carry + $item->getCreationRules();
            return $carry;
        }, []);
    }

    function getRulesMessages()
    {
        return [];
    }

    function getRulesAttributes()
    {
        return array_reduce($this->getFields(), function ($carry, Type $item) {
            if ($item->getName() != $item->getTitle()) {
                $carry[$item->getName()] = $item->getTitle();
            }
            return $carry;
        }, []);
    }

    function export()
    {
        return [
            'title' => $this->title,
        ];
    }

    public static function download($content, $filename, $mimeType)
    {
        return [
            'action' => 'download',
            'download' => [
                'content' => base64_encode($content),
                'filename' => $filename,
                'mime' => $mimeType,
            ]
        ];
    }

    public static function message($title, $text, $type = 'info', $refresh = false)
    {
        return [
            'action' => 'message',
            'message' => [
                'title' => $title,
                'text' => $text,
                'type' => $type,
            ],
            'refresh' => $refresh,
        ];
    }

    public static function success($title, $text)
    {
        return static::message($title, $text, 'success');
    }

    public static function error($title, $text)
    {
        return static::message($title, $text, 'error');
    }

    public static function redirect($url, $newTab = false)
    {
        return [
            'action' => 'redirect',
            'redirect' => [
                'url' => $url,
                'new_tab' => $newTab,
            ]
        ];
    }

    public static function refresh()
    {
        return [
            'action' => 'refresh',
        ];
    }
}
