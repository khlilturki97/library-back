<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Class CrudRepository
 * @package App\Repositories
 */
abstract class CrudRepository
{
    /**
     * CrudRepository constructor.
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records of model
     * @param array $attributes
     * @param array $conditions
     * @param array $relations
     * @param array $orderBy
     * @param int|null $offset
     * @param int|null $limit
     * @param array $nullConditions
     * @return mixed
     * @throws \Exception
     */
    public function all(array $attributes = array('*'), array $conditions = [], array $relations = [], array $orderBy = [], int $offset = -1, int $limit = -1, array $nullConditions = [])
    {
        return $this->buildQuery($attributes, $conditions, $relations, $orderBy, $offset, $limit, $nullConditions)->get();
    }

    /**
     * Build query to select $attributes where $conditions with $relations
     * @param array $attributes
     * @param array $conditions
     * @param array $relations
     * @param array $orderBy
     * @param int $offset
     * @param int $limit
     * @param array $nullConditions
     * @return mixed
     * @throws \Exception
     */
    protected function buildQuery(array $attributes, array $conditions, array $relations, array $orderBy, int $offset, int $limit, array $nullConditions)
    {
        $query = $this->model->select($attributes);
        if (count($conditions) > 0) {
            $this->is_assoc($conditions);
            foreach ($conditions as $key => $condition) {
                $query = $query->where($key, 'like', '%' . $condition . '%');
            }
        }
        if (count($orderBy) > 0) {
            $this->is_assoc($orderBy);
            foreach ($orderBy as $column => $order) {
                $columnRelations = explode('@', $column);
                if (count($columnRelations) == 1) {
                    $queryString = '`' . $column . '` ' . strtoupper($order);

                    $query = $query->orderByRaw($queryString);
                } else {
                    $column = $columnRelations[count($columnRelations) - 1];
                    unset($columnRelations[count($columnRelations) - 1]);
                    $relation = join('.', $columnRelations);
                    $query = $query->whereHas($relation, function ($q) use ($column, $order, $columnRelations) {
                        $queryString = '-`' . $column . '` ' . strtoupper($order);
                        Log::info($queryString);
                        $q->orderByRaw($queryString);
                    });
                    Log::info($order);
                    Log::info(strpos($order, 'desc'));
                    if ($order == 'desc') {

                        $relation = $columnRelations[0];
                        Log::info(print_r($this->model->$relation()->getForeignKeyName(), true));
//                        $query = $query->union($this->model->whereNull($this->model->$relation()->getForeignKeyName()));
                    }
                    if ($order == 'asc') {

                        $relation = $columnRelations[0];
                        Log::info(print_r($this->model->$relation()->getForeignKeyName(), true));
                        //$query = $this->model->whereNull($this->model->$relation()->getForeignKeyName())->union($query);
                    }

                }
            }
        }
        if (count($nullConditions) > 0) {
            foreach ($nullConditions as $attribute => $nullable) {
                Log::info($attribute);
                Log::info($nullable);
                if ($nullable) {
                    $query = $query->whereNull($attribute);
                } else {
                    $query = $query->whereNotNull($attribute);
                }
            }
        }
        if (count($relations) > 0) {
            $query = $query->with($relations);
        }
        if ($offset >= 0)
            $query->offset($offset);
        if ($limit >= 0)
            $query->limit($limit);
        return $query;
    }

    /**
     * Check if an array is associative or not
     * @param $array
     * @return void
     * @throws \Exception
     */
    private function is_assoc($array)
    {
        if (array_keys($array) === range(0, count($array) - 1)) {
            throw(new \Exception('Array must be associative'));
        }
    }

    /**
     * Store a model
     * @param array $data
     * @return mixed
     */
    public function store(array $data)
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $exception) {
            Log::error('exception');
            Log::error($exception);
            return $exception;
        }
    }

    /**
     * Update a record where $pks
     * @param array $pks
     * @param array $data
     * @return \Exception|mixed
     */
    public function update(array $pks, array $data)
    {
        try {
            if (($model = $this->show($pks)) instanceof \Exception) {
                Log::error($model);
                throw $model;
            }
            Log::info('c bon');
            $model->update($data);
            return $model;
        } catch (\SQLiteException $exception) {
            Log::error($exception);
            return $exception;
        }
    }

    /**
     * Get one model of model where $pks
     * @param array $pks
     * @param array $attributes
     * @param array $conditions
     * @param array $relations
     * @param array $orderBy
     * @param array $nullConditions
     * @return mixed
     * @throws \Exception
     */
    public function show(array $pks, array $attributes = ['*'], array $conditions = [], array $relations = [], array $orderBy = [], array $nullConditions = [])
    {
        $this->is_assoc($pks);
        $conditions = array_merge($conditions, $pks);
        try {
            return $this->buildQuery($attributes, $conditions, $relations, $orderBy, -1, -1, $nullConditions)->firstOrFail();
        } catch (\Exception $exception) {
            Log::error($exception);
            return $exception;
        }
    }

    /**
     * Delete record of model where $pks
     * @param array $pks
     * @return \Exception
     */
    public function destroy(array $pks)
    {
        try {
            return $this->show($pks)->delete();
        } catch (\Exception $exception) {
            Log::error($exception);
            return $exception;
        }
    }

    /**
     * @param array $conditions
     * @param array $nullConditions
     * @param string $search
     * @param array $attributes
     * @param array $relations
     * @param array $orderBy
     * @param int $offset
     * @param int $limit
     * @return mixed
     * @throws \Exception
     */
    public function count(array $conditions = [], array $nullConditions = [], $search = [], array $attributes = array('*'), array $relations = [], array $orderBy = [], int $offset = -1, int $limit = -1)
    {
        $query = $this->model->selectRaw('count(*) as count');
        if (count($conditions) > 0) {
            $this->is_assoc($conditions);
            foreach ($conditions as $column => $condition) {
                $query = $query->where($column, $condition);
            }
        }
        if (count($nullConditions) > 0) {
            $this->is_assoc($nullConditions);
            foreach ($nullConditions as $column => $condition) {
                if ($condition)
                    $query = $query->whereNull($column);
                else
                    $query = $query->whereNotNull($column);
            }
        }
        if (count($search) > 0) {
            $query = $this->buildSearchQuery($query, $search);
        }
        return $query->first()->count;
    }

    private function buildSearchQuery($query, $search)
    {
        for ($i = 0; $i < count($search); $i++) {
            $attributes = array_keys($search)[$i];
            $value = $search[$attributes];
            $fields = explode('@', $attributes);
            if (count($fields) == 1) {
                $query = $query->where($attributes, 'like', '%' . $value . '%');
            } else {
                $column = $fields[count($fields) - 1];
                unset($fields[count($fields) - 1]);
                $relation = join('.', $fields);
                $query = $query->whereHas($relation, function ($q) use ($value, $column) {
                    $q->where($column, 'like', '%' . $value . '%');
                });
            }
        }

        return $query;
    }

    public function deleteFile($dir)
    {
        if (unlink($dir))
            return true;
        return false;
    }

//    public function moveFile($dir, $file)
//    {
//        if ($file) {
//            //echo 'if img ok'."///////////////////";
//            if ($file->isValid()) {
//                $path = 'uploads/' . $dir;
//                $extension = $file->getClientOriginalExtension();
//                do {
//                    $fileName = $this->generateRandomString(10) . '.' . $extension;
//                } while (file_exists($path . '/' . $fileName));
//                //echo $fileName."///////////////////";
//                if ($file->move($path, $fileName))
//                    return $path . '/' . $fileName;
//            }
//        }
//        return 'error moving file';
//    }
//
//    private function generateRandomString($length = 10)
//    {
//        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
//        $charactersLength = strlen($characters);
//        $randomString = '';
//        for ($i = 0; $i < $length; $i++) {
//            $randomString .= $characters[rand(0, $charactersLength - 1)];
//        }
//        return $randomString;
//    }

    public function search(array $search, array $attributes = array('*'), array $conditions = [], array $relations = [], array $orderBy = [], int $offset = -1, int $limit = -1, array $nullConditions = [])
    {
        $this->is_assoc($search);
        if (count($search) > 0) {
            Log::info('search');
            Log::info(print_r($search, true));
            Log::info(print_r($orderBy, true));
            $q = $this->buildQuery($attributes, array_merge($conditions, $search), $relations, $orderBy, $offset, $limit, $nullConditions);
            return $q->get();
        }
        return [];
    }
}
