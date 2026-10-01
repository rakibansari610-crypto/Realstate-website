<?php

namespace App\Models\Scopes;

use App\Tenancy\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrgScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if ($orgId = Tenant::id()) {
            $builder->where($model->getTable().'.org_id', $orgId);
        }
    }
}
