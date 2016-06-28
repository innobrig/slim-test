<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


/**
 *
 */
class BaseModel extends Model
{
    protected $guarded        = ['id']; // Tables have a primary key named "id"
    protected $data           = null;
    protected $doReturnArray  = false;
    protected $inputFunc      = 'fromPost';
    protected $inputPath      = null;
    protected $inputDefault   = null;
    protected $inputFilter    = null;
    protected $inputFlags     = [];


    // Custom Event Hooks Start
    public function getFromInput ($inputFunc=null, $path=null, $default=null, $filter=null, $flags=[])
    {
        $inputFunc = $inputFunc        ? $inputFunc : self::$inputFunc;
        $path      = $path             ? $path      : self::$inputPath;
        $default   = $default !== null ? $default   : self::$inputDefault;
        $filter    = $filter           ? $filter    : self::$inputFilter;
        $flags     = $flags            ? $flags     : self::$inputFlags;

        $this->InputPreProcess();
        $this->data = \InnoBrig\FlexInput\Input::$inputFunc ($path, $default, $filter, $flags);
        $this->InputPostProcess();
    }

    public function getFromInputPreProcess ()
    {
        return self::getData();
    }

    public function getFromInputPostProcess ()
    {
        return self::getData();
    }
    // Custom Event Hooks End


    // Determine whether we wish to return object(s) or array(s)
    public function getData ($overrideDoReturnArray=null)
    {
        if ($this->doReturnArray || ($overrideDoReturnArray !== null && $overrideDoReturnArray)) {
            return $this->data->toArray();
        }

        return $this->data;
    }


    public function setData ($data)
    {
        $this->data = $data;
    }


    public function setReturnArray ($doReturnArray)
    {
        $this->doReturnArray = (bool)$doReturnArray;
    }


    // Useful query functions
    public static function selectByField ($value, $field='id', $orderBy=null, $pagesize=0)
    {
        $query = self::where ($field, '=', $value);
        if ($orderBy) {
            $query->orderByRaw ($orderBy);
        }

        if ($pagesize > 0) {
            $query->paginate($pagesize);
        }

        return $query->get();
    }


    public static function selectByFields (array $fieldConditions, $orderBy=null, $pagesize=0)
    {
        if ($fieldConditions) {
            $v = reset ($fieldConditions);
            $k = key ($fieldConditions);
            unset ($fieldConditions[$k]);
        } else {
            $k = $v = 1;
        }

        $query = self::where ($k, '=', $v);
        foreach ($fieldConditions as $k=>$v) {
            $query = $query->where ($k, '=', $v);
        }

        if ($orderBy) {
            $query->orderByRaw ($orderBy);
        }

        if ($pagesize > 0) {
            return $query->paginate($pagesize);
        }

        return $query->get();
    }


    public static function selectOneByField ($value, $field='id')
    {
        return self::where ($field, '=', $value)->first();
    }


    public static function selectOneByFields (array $fieldConditions)
    {
        if ($fieldConditions) {
            $v = reset ($fieldConditions);
            $k = key ($fieldConditions);
            unset ($fieldConditions[$k]);
        } else {
            $k = $v = 1;
        }

        $query = self::where ($k, '=', $v);
        foreach ($fieldConditions as $k=>$v) {
            $query = $query->where ($k, '=', $v);
        }

        return $query->first();
    }
}

