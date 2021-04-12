<?php

namespace Luna\Rules;

use Luna;
use Illuminate\Database\Eloquent\Model;

class RuleGenerator
{
    protected $creation;
    protected $update;

    public function creation($rule)
    {
        $this->creation = $rule;
        return $this;
    }

    public function update($rule)
    {
        $this->update = $rule;
        return $this;
    }

    public function generateCreationRule()
    {
        return Luna::tap($this->creation);
    }

    public function generateUpdateRule(Model $model)
    {
        return Luna::tap($this->update, $model);
    }
}