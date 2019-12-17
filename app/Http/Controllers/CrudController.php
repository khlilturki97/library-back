<?php

namespace App\Http\Controllers;

use App\Repositories\CrudRepository;
use App\Repositories\LogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

abstract class CrudController extends Controller
{
    protected $repository;
    protected $relations;
    protected $orderBy;
    protected $conditions;
    protected $nullConditions;

    public function __construct(CrudRepository $repository, array $relations = [], array $orderBy = [], array $conditions = [], array $nullConditions = [])
    {
        $this->repository = $repository;
        $this->relations = $relations;
        $this->orderBy = $orderBy;
        $this->conditions = $conditions;
        $this->nullConditions = $nullConditions;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $offset = $request->input('offset');
        $limit = $request->input('limit');
        $orderBy = $request->input('order_by');
        $orderByType = $request->input('order_by_type');

        if (isset($orderBy) && isset($orderByType)) {
            $this->orderBy = [];
            $this->orderBy[$orderBy] = $orderByType;
        }

        Log::info(print_r($this->orderBy, true));
        if ((isset($offset)) && (isset($limit))) {
            if (($all = $this->repository->all(['*'], $this->conditions, $this->relations, $this->orderBy, $offset, $limit, $this->nullConditions)) instanceof \Exception)
                return response()->json(
                    ['error' => config('messages.error.all')],
                    400);
            return response()->json([
                'count' => $this->repository->count($this->conditions, $this->nullConditions),
                'elements' => $all
            ]);
        } else {
            if (($all = $this->repository->all(['*'], $this->conditions, $this->relations, $this->orderBy, -1, -1, $this->nullConditions)) instanceof \Exception)
                return response()->json(
                    ['error' => config('messages.error.all')],
                    400);
            return response()->json(
                $all
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (($store = $this->repository->store($request->all())) instanceof \Exception)
            return response()->json(
                ['error' => $store->getMessage()],
                400);
        return response()->json(
            $store
        );
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function show($id)
    {
        if (($show = $this->repository->show(['id' => $id], ['*'], $this->conditions, $this->relations, $this->orderBy)) instanceof \Exception)
            return response()->json(
                ['error' => config('messages.error.show')],
                400);
        return response()->json(
            $show
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {
        if (($update = $this->repository->update(['id' => $id], $request->all())) instanceof \Exception)
            return response()->json(
                ['error' => $update],
                400);
        return response()->json(
            $update
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (($destroy = $this->repository->destroy(['id' => $id])) instanceof \Exception)
            return response()->json(
                ['error' => config('messages.error.destroy')],
                400);
        return response()->json(
            ['success' => config('messages.success.destroy')]
        );
    }

    public function search(Request $request)
    {
        $offset = $request->input('offset');
        $limit = $request->input('limit');
        $orderBy = $request->input('order_by');
        $orderByType = $request->input('order_by_type');

        if (isset($orderBy) && isset($orderByType)) {
            $this->orderBy = [];
            $this->orderBy[$orderBy] = $orderByType;
        }

        Log::info(print_r($this->orderBy, true));

        $input = $request->all();
        unset($input['offset']);
        unset($input['limit']);
        unset($input['order_by']);
        unset($input['order_by_type']);
        Log::info(print_r($input, true));

        if ((isset($offset)) && (isset($limit))) {
            Log::info('offset');
            if (($all = $this->repository->search($input, ['*'], $this->conditions, $this->relations, $this->orderBy, $offset, $limit, $this->nullConditions)) instanceof \Exception)
                return response()->json(
                    ['error' => config('messages.error.all')],
                    400);
            return response()->json([
                'count' => $this->repository->count($this->conditions, $this->nullConditions, $input),
                'elements' => $all
            ]);
        } else {
            if (($all = $this->repository->search($input, ['*'], $this->conditions, $this->relations, $this->orderBy, -1, -1, $this->nullConditions)) instanceof \Exception)
                return response()->json(
                    ['error' => config('messages.error.all')],
                    400);
            return response()->json(
                $all
            );
        }
    }
}