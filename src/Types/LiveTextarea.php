<?php

namespace Luna\Types;


use Luna\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LiveTextarea extends Type
{
    protected $type = 'live_textarea';

    /**
     * @return static
     */
    static function make($name, $column_name = null)
    {
        return (new static($name))->columnName(is_null($column_name) ? $name : $column_name);
    }

    function handelActionRequest(Request $request, Resource $resource, Model $model)
    {
        if (\Gate::getPolicyFor($model)) {
            \Gate::authorize('update', $model);
        }

        if (\Validator::make($request->only($this->getName()), $this->getUpdateRules($model))->passes()) {
            $this->fillFromRequest($request, $model, function () {

            });
        }

        $model->save();
    }
}