<?php
namespace engine\traits\CRUDable;

trait CRUDable
{

    public function routes()
    {
        $class = get_called_class();
        return [$class, $class::ALLOWED_ROUTES];
    }




    public function post($request)
    {
        $class = get_called_class();
        $model = $class::MODEL;
        $serializer = $class::SERIALIZER;
        $serializer = new $serializer(null, $request->post);
        if($serializer->id_valid())
        {
            $serializer->save();
            return $this->response([
                'type' => 'success',
                'data' => $serializer->data
            ], 201);
        }
        else
        {
            return $this->response([
                'type' => 'error',
                'data' => $serializer->errors
            ], 400);
        }
    }





    public function get($request)
    {
        $class = get_called_class();
        $serializer = $class::SERIALIZER;
        $model = $class::MODEL;
        if($id = $request->get('id'))
        {
            $objects = $model::get(['id' => $id]);
            if(is_null($objects))
            {
                return $this->response([
                    'type' => 'error',
                    'data' => 'Not found'
                ], 404);
            }
        }
        else
        {
            $objects = $model::all();
        }
        $serializer = new $serializer($objects);
        return $this->response([
            'type' => 'success',
            'data' => $serializer->instance
        ], 200);
    }





    public function patch($request)
    {
        $class = get_called_class();
        $model = $class::MODEL;
        $serializer = $class::SERIALIZER;
        $id = $request->post('id', '');
        $obj = $model::get(['id' => $id]);
        if(!$obj)
        {
            return $this->response([
                'type' => 'error',
                'data' => 'Not found'
            ], 404);
        }
        $serializer = new $serializer($obj, $request->post);
        if($serializer->id_valid())
        {
            $serializer->save();
            return $this->response([
                'type' => 'success',
                'data' => $serializer->data
            ], 200);
        }
        else
        {
            return $this->response([
                'type' => 'error',
                'data' => $serializer->errors
            ], 400);
        }
    }





    public function delete($request)
    {
        $class = get_called_class();
        $model = $class::MODEL;
        $serializer = $class::SERIALIZER;
        $id = $request->post('id', '');
        $obj = $model::get(['id' => $id]);
        if(!$obj)
        {
            return $this->response([
                'type' => 'error',
                'data' => 'Not found'
            ], 404);
        }
        $serializer = new $serializer($obj);
        $serializer->delete();
        return $this->response([
            'type' => 'success',
            'data' => 'Object deleted'
        ], 200);
    }




}

?>