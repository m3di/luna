<?php

namespace Luna\Controllers;

use Luna\Actions\Action;
use Luna\Metrics\Metric;
use Luna\Repositories\ResourceModelRepository;
use Luna\Resources\Resource;
use Luna\Types\Type;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Luna;

class LunaController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    function __construct()
    {
        $this->middleware(function (Request $request, \Closure $next) {
            if (count($files = array_keys($request->allFiles())) > 0) {
                $request->merge(array_map(function ($val) {
                    return json_decode($val, true);
                }, array_diff_key($request->all(), array_flip($files))));
            }
            return $next($request);
        });
    }

    function index()
    {
        return view('luna.index');
    }

    function meta()
    {
        return Luna::exportResources();
    }

    function paginate(Request $request, $resource)
    {
        /** @var Resource $resource */
        $resource = luna::getResource($resource);
        $fields = $resource->visibleFieldsOnIndex();

        $paginate = ResourceModelRepository::make($resource)
            ->fields($fields)
            ->filters(json_decode($request->get('filters', '[]'), true))
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

    function typeRetrieve(Request $request, $resource, $type)
    {
        /** @var Resource $resource */
        $resource = luna::getResource($resource);

        /** @var Type $type */
        $type = $resource->getField($type);

        $model = $request->get('model');

        if (!is_null($model)) {
            $model = $resource->getQuery()->findOrFail($model);

            if (\Gate::getPolicyFor($resource->model)) {
                $this->authorize('update', $model);
            }
        }

        return $type->handelRetrieveRequest($request, $resource, $model);
    }

    function details($resource, $model)
    {
        /** @var Resource $resource */
        $resource = luna::getResource($resource);

        if ($resource->isDetailsPanelDisabled()) {
            abort(403);
        }

        $fields = $resource->visibleFieldsOnDetails();

        $model = ResourceModelRepository::make($resource)
            ->fields($fields)
            ->getQueryWithRelations()
            ->findOrFail($model);

        if (\Gate::getPolicyFor($resource->model)) {
            $this->authorize('view', $model);
        }

        $data = [];

        foreach ($fields as $field) {
            $data[$field->getName()] = $field->displayFor($model);
        }

        return response()->json($data);
    }

    function create(Request $request, $resource)
    {
        /** @var Resource $resource */
        $resource = luna::getResource($resource);

        if ($resource->isCreatePanelDisabled()) {
            abort(403);
        }

        if (\Gate::getPolicyFor($resource->model)) {
            $this->authorize('create', $resource->model);
        }

        $this->validate($request, $resource->getCreationRules(), $resource->getRulesMessages(), $resource->getRulesAttributes());

        $fields = $resource->visibleFieldsOnCreate();
        $model = new $resource->model();
        $resource->fireCreating($request, $model);

        $afterSaveJobs = [];
        $appendJob = function (\Closure $job) use (&$afterSaveJobs) {
            $afterSaveJobs[] = $job;
        };

        foreach ($fields as $field) {
            $field->fillFromRequest($request, $model, $appendJob);
        }

        $model->save();

        foreach ($afterSaveJobs as $job) {
            call_user_func_array($job, [$request, $model]);
        }

        $resource->fireCreated($request, $model);

        return response()->json(true);
    }

    function edit($resource, $model)
    {
        /** @var Resource $resource */
        $resource = luna::getResource($resource);

        if ($resource->isEditPanelDisabled()) {
            abort(403);
        }

        $fields = $resource->visibleFieldsOnEdit();

        $model = ResourceModelRepository::make($resource)
            ->fields($fields)
            ->getQueryWithRelations()
            ->findOrFail($model);

        if (\Gate::getPolicyFor($resource->model)) {
            $this->authorize('update', $model);
        }

        $data = [];

        foreach ($fields as $field) {
            $data[$field->getName()] = $field->resolveFor($model);
        }

        return response()->json($data);
    }

    function update(Request $request, $resource, $model)
    {
        /** @var Resource $resource */
        $resource = luna::getResource($resource);

        if ($resource->isEditPanelDisabled()) {
            abort(403);
        }

        $model = $resource->getQuery()->findOrFail($model);

        if (\Gate::getPolicyFor($resource->model)) {
            $this->authorize('update', $model);
        }

        $this->validate($request, $resource->getUpdateRules($model), $resource->getRulesMessages(), $resource->getRulesAttributes());
        $resource->fireUpdating($request, $model);

        $fields = $resource->visibleFieldsOnEdit();

        $afterSaveJobs = [];
        $appendJob = function (\Closure $job) use (&$afterSaveJobs) {
            $afterSaveJobs[] = $job;
        };

        foreach ($fields as $field) {
            $field->fillFromRequest($request, $model, $appendJob);
        }

        $model->save();

        foreach ($afterSaveJobs as $job) {
            call_user_func_array($job, [$request, $model]);
        }

        $resource->fireUpdated($request, $model);

        return response()->json(true);
    }

    function destroy(Request $request, $resource, $model)
    {
        /** @var Resource $resource */
        $resource = luna::getResource($resource);

        if ($resource->isDeleteOptionDisabled()) {
            abort(403);
        }

        $model = $resource->getQuery()->findOrFail($model);

        if (\Gate::getPolicyFor($resource->model)) {
            $this->authorize('delete', $model);
        }

        $resource->fireDeleting($request, $model);
        $result = $model->delete();
        $resource->fireDeleted($request, $model);

        return response()->json($result);
    }

    function typeAction(Request $request, $resource, $model, $type)
    {
        /** @var Resource $resource */
        $resource = luna::getResource($resource);

        $model = $resource->getQuery()->findOrFail($model);

        if (\Gate::getPolicyFor($resource->model)) {
            //$this->authorize('update', $model);
        }

        /** @var Type $type */
        $type = $resource->getField($type);

        return $type->handelActionRequest($request, $resource, $model);
    }

    function action(Request $request, $resource, $action)
    {
        /** @var Resource $resource */
        $resource = luna::getResource($resource);

        /** @var Action $action */
        $action = $resource->getAction($action);

        $fields = $action->getFields();

        $this->validate($request, $action->getRules(), $action->getRulesMessages(), $action->getRulesAttributes());

        $data = [];

        foreach ($fields as $field) {
            $data = $data + $field->extractValuesFromRequest($request, null);
        }

        return $action->handel($data, $resource->getQuery()->findMany($request->get('models')));
    }

    function metric(Request $request, $resource, $metricIndex)
    {
        /** @var Resource $resource */
        $resource = luna::getResource($resource);

        /** @var Metric $metric */
        $metric = $resource->getMetric($metricIndex);

        return $metric->handelRequest($request, $resource);
    }

}
