<?php

namespace Luna\Types;


use Carbon\Carbon;
use Luna\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Luna\Rules\RuleGenerator;

class DatePicker extends Type
{
    protected $type = 'date_picker';
    protected $format = 'YYYY-MM-DD';
    protected $display_format = 'jYYYY jMMMM jDD';
    protected $locale = 'fa';
    protected $v_type = 'date';

    /**
     * @return static
     */
    static function make($name, $column_name = null)
    {
        return (new static($name))->columnName(is_null($column_name) ? $name : $column_name);
    }

    public function __construct($name)
    {
        parent::__construct($name);

        $rule = function() {
            return function ($attribute, $value, $fail) {
                if (Carbon::createFromIsoFormat($this->format, $value)->isoFormat($this->format) !== $value) {
                    $fail(trans('validation.date_format', [
                        'attribute' => $this->title ?? $attribute,
                        'format' => $this->format
                    ]));
                }
            };
        };

        $this->inheritedRules[] = (new RuleGenerator())->creation($rule)->update($rule);

        $this->resolver = function ($value) {
            return $value ?? Carbon::createFromTimeString($value)->isoFormat($this->format);
        };
    }

    function extractValuesFromRequest(Request $request, ?Model $model = null)
    {
        $value = $request->get($this->name);

        return $this->getColumnName() ? [
            $this->getColumnName() => $value ?? Carbon::createFromIsoFormat($this->format, $value),
        ] : [];
    }

    function export()
    {
        return parent::export() + [
                'format' => $this->format,
                'display_format' => $this->display_format,
                'locale' => $this->locale,
                'v_type' => $this->v_type,
            ];
    }

    function displayFormat(string $display_format)
    {
        $this->display_format = $display_format;
        return $this;
    }

    function jalali() {
        $this->locale = 'fa';
        return $this;
    }

    function georgian() {
        $this->locale = 'en';
        return $this;
    }

    function hybrid() {
        $this->locale = 'fa,en';
        return $this;
    }

    function yearOnly() {
        $this->v_type = 'year';
        return $this;
    }

    function yearMonthOnly() {
        $this->v_type = 'year-month';
        return $this;
    }

    function withTime() {
        $this->v_type = 'year-month';
        return $this;
    }

    function fullDate() {
        $this->v_type = 'date';
        return $this;
    }
}