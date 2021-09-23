<?php
namespace core\traits\CRUD;
use core\Response\Response;

trait HasList
{
    public function _list($request)
    {
        $serializer = $this->get_serializer();
        $model = $this->get_model();
        $objects = $this->get_queryset($request);
        if(!is_array($objects))
        {
            return new Response([
                'type' => 'error',
                'data' => 'get_queryset musts return an array.'
            ], 404);
        }
        $serializer = new $serializer($objects, null, true);
        return new Response([
            'type' => 'success',
            'data' => $serializer->data
        ], 200);
    }
}


trait HasRetrieve
{
    public function retrieve($request, $id)
    {
        $serializer = $this->get_serializer();
        $model = $this->get_model();
        $object = $this->get_object($request, $id);
        $serializer = new $serializer($object);
        return new Response([
            'type' => 'success',
            'data' => $serializer->data
        ], 200);
    }
}




trait HasCreate
{
    public function create($request)
    {
        $model = $this->get_model();
        $serializer = $this->get_serializer();
        $serializer = new $serializer(null, $request->post);
        if($serializer->is_valid())
        {
            $serializer->save();
            return new Response([
                'type' => 'success',
                'data' => $serializer->data
            ], 201);
        }
        else
        {
            return new Response([
                'type' => 'error',
                'data' => $serializer->errors
            ], 400);
        }
    }
}




trait HasUpdate
{
    public function update($request, $id)
    {
        $serializer = $this->get_serializer();
        $model = $this->get_model();
        $obj = $this->get_object($request, $id);
        $serializer = new $serializer($obj, $request->post);
        if($serializer->is_valid())
        {
            $serializer->update();
            return new Response([
                'type' => 'success',
                'data' => $serializer->data
            ], 200);
        }
        else
        {
            return new Response([
                'type' => 'error',
                'data' => $serializer->errors
            ], 400);
        }
    }
}




trait HasDestroy
{
    public function destroy($request, $id)
    {
        $serializer = $this->get_serializer();
        $model = $this->get_model();
        $obj = $this->get_object($request, $id);
        $serializer = new $serializer($obj);
        $serializer->destroy();
        return new Response([
            'type' => 'success',
            'data' => 'Object deleted'
        ], 200);
    }
}



?>
