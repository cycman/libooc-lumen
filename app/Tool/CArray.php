<?php
/**
 * 数组相关的一些操作
 */

namespace App\Tool;

class CArray
{
    /**
     * 提取数组指定key的值，返回在该key值中指定的第二个key的所有值
     *
     * @param array $objects 要转变的数组
     * @param string $property 要汇聚的key值
     * @param string $propertyValue 汇聚成数据的key值
     *
     * @return array $objs
     */
    public static function listArrayData($objects, $property, $propertyValue)
    {
        $objs = [];
        foreach ($objects as $object) {
            $objs[$object->$property ?? $object[$property]][] = $object->$propertyValue ?? $object[$propertyValue];
        }
        return $objs;
    }

    /**
     * 根据key取到该key下的所有数组值
     *
     * @param $objects
     * @param string $property
     * @param array $fields
     * @return array
     */
    public static function listArrayDatas($objects, $property, $fields)
    {
        $arRet = [];
        $bObj = is_object(current($objects));
        foreach ($objects as $object) {
            $key = $bObj ? $object->$property : $object[$property];
            $tmp = [];
            foreach ($fields as $field) {
                $val = $bObj ? $object->$field : $object[$field];
                $tmp[$field] = $val;
            }
            $arRet[$key][] = $tmp;
        }
        return $arRet;
    }

    public static function getArrayData($objects, $property, $fields)
    {
        $arRet = [];
        $bObj = is_object(current($objects));
        foreach ($objects as $object) {
            $key = $bObj ? $object->$property : $object[$property];
            $tmp = [];
            foreach ($fields as $field) {
                $val = $bObj ? $object->$field : $object[$field];
                $tmp[$field] = $val;
            }
            $arRet[$key] = $tmp;
        }
        return $arRet;
    }

    /** 判断一个值是否在一组对象(或数组)的某个属性里
     * @param $value
     * @param $objects 数组
     * @param $property 数组或对象的属性
     * @return 如果存在,返回对应的数组或对象,不存在则返回null
     */
    public static function valueInObjs($value, $objects, $property)
    {
        if (empty($objects)) {
            return null;
        }
        $bObj = is_object(current($objects));
        foreach ($objects as $object) {
            $objValue = $bObj ? $object->$property : $object[$property];
            if ($value == $objValue) {
                return $object;
            }
        }

        return null;
    }

    public static function getObjectByValue($value, $objects, $property)
    {
        if (empty($objects)) {
            return null;
        }
        foreach ($objects as $object) {
            if ($value == $object[$property]) {
                return $object;
            }
        }

        return null;
    }

    public static function toString($array)
    {
        $str = implode(", ", $array);
        $str = "[" . $str . "]";

        return $str;
    }

    /** 提取对象或数组的某一列
     * @param $objs
     * @param $property
     * @return array
     */
    public static function listData($objs, $property)
    {
        if (empty($objs)) {
            return [];
        }
        $bObj = is_object(current($objs));
        if ($bObj) {
            $arRet = [];
            foreach ($objs as $object) {
                $arRet[] = $object->$property;
            }
        } else {
            $arRet = array_column($objs, $property);
        }

        return $arRet;
    }

    public static function listDictData($objs, $keyProperty, $valueProperty)
    {
        if (empty($objs)) {
            return [];
        }
        $bObj = is_object(current($objs));
        if ($bObj) {
            $arRet = [];
            foreach ($objs as $object) {
                $arRet[$object->$keyProperty] = $object->$valueProperty;
            }
        } else {
            $arRet = array_column($objs, $valueProperty, $keyProperty);
        }

        return $arRet;
    }


    public static function fromObjs($objects, $properties)
    {
        if (count($properties) == 1) {
            return static::listData($objects, count($properties));
        }
        $arRet = [];
        foreach ($objects as $obj) {
            $ret = [];
            foreach ($properties as $key => $property) {
                if (is_int($key)) {
                    $ret[$property] = $obj->$property;
                } else {
                    $ret[$property] = $obj->$key;
                }
            }
            $arRet[] = $ret;
        }

        return $arRet;
    }

    public static function strictlyIn($value, $array)
    {
        foreach ($array as $item) {
            if ($item === $value) {
                return true;
            }
        }

        return false;
    }

    /** 返回数组的第一个元素
     * @param $items
     * @return mixed
     */
    public static function firstItem($items)
    {
        if (empty($items)) {
            return null;
        }

        return reset($items);
    }

    /*
     * 根据key值删除数组中的元素
     * @param array $array 数组
     * @param array $filterKey 需要删除的key值数组
     * return array 过滤后的数组
     * */
    public static function filterByKey($array, $filterKey)
    {
        if (isset($filterKey)) {//删除点操作
            foreach ($filterKey as $key) {
                if (isset($array[$key])) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }

    /*
     * 数组过滤掉所有小于等于0的数字
     * @param array $array 原数据
     * return array 过滤后的数据
     * */
    public static function filterLteZero($array)
    {
        $condition = function ($var) {
            return $var > 0;
        };

        $result = array_filter($array, $condition);

        return $result;
    }

    /*
     * 排序获取数据第X个数
     * @param array $array 原数据
     * @param num $percent 百分比
     * @param num $default 如果取的节点上没有数据的默认返回
     * return num
     * */
    public static function getArrayPercent($array, $percent, $default = 0)
    {
        $count = count($array);
        $index = round($count * $percent) - 1; //四舍五入计算

        return (isset($array[$index]) ? $array[$index] : $default);
    }

    /*
     * 让数组某项值作为key
     *
     * */
    public static function setArrayKey($array, $key)
    {
        $return = [];

        if (is_array($array)) {
            $bObj = is_object(current($array));
            foreach ($array as $item) {
                if ($bObj) {
                    $return[$item->$key] = $item;
                } else {
                    $return[$item[$key]] = $item;
                }
            }
        }
        return $return;
    }

    public static function listDataDyadicArr($objs, $property1, $property2)
    {
        $arRet = [];

        if (!empty($objs)) {
            $bObj = is_object(current($objs));
            foreach ($objs as $obj) {
                if ($bObj) {
                    $fields1 = $obj->$property1;
                    $fields2 = $obj->$property2;
                } else {
                    $fields1 = $obj[$property1];
                    $fields2 = $obj[$property2];
                }
                $arRet[$fields1][] = $fields2;
            }
        }

        return $arRet;
    }

    /**
     * 对象转为数组
     * @param $obj
     * @return mixed
     */
    public static function object2array($object)
    {
        return json_decode(json_encode($object), true);
    }

    /**
     * 普通字符串强转为一维数组
     * @param $str
     */
    public static function str2array(&$str)
    {
        if (empty($str)) {
            $str = [];
        } elseif (!is_array($str)) {
            $str = (array)$str;
        }
    }

    /**
     * 通过指定字段比较旧对象数组和新对象数组，返回相同的项，新增的项以及删除的项
     * @param $oldObjs object/array 旧的数组
     * @param $newObjs object/array 新的对象
     * @param $cmpAttr string 指定比较字段
     * @return array
     */
    public static function arCmpByAttr($oldObjs, $newObjs, $cmpAttr)
    {
        $sameObj = array();
        $deleteObj = array();

        if (!empty($oldObjs)) {
            $bObj = is_object(current($oldObjs));
            foreach ($oldObjs as $oldObj) {
                $bIn = false;
                foreach ($newObjs as $key => $newObj) {
                    if ($bObj) {
                        $oldAttr = $oldObj->$cmpAttr;
                        $newAttr = $newObj->$cmpAttr;
                    } else {
                        $oldAttr = $oldObj[$cmpAttr];
                        $newAttr = $newObj[$cmpAttr];
                    }

                    if ($oldAttr == $newAttr) {
                        $sameObj[] = $oldObj;
                        $bIn = true;
                        unset($newObjs[$key]);
                        break;
                    }
                }

                if (!$bIn) {
                    $deleteObj[] = $oldObj;
                }
            }
        }
        return array('add' => $newObjs, 'delete' => $deleteObj, 'same' => $sameObj);
    }

    /**
     * 一维数组比较旧数组和新数组，返回相同的项，新增的项以及删除的项
     * @param $oldArr array 旧的数组
     * @param $newArr array 新的数组
     * @return array
     */
    public static function arCmp($oldArr, $newArr)
    {

        $same = array_intersect($oldArr, $newArr);
        $delete = array_diff($oldArr, $same);
        $add = array_diff($newArr, $same);

        return array('add' => $add, 'delete' => $delete, 'same' => $same);
    }

    //判断值是否在对象数组的属性里
    public static function valueInObjsBoolean($value, $objects, $property)
    {
        $value = self::valueInObjs($value, $objects, $property);
        return !($value === null);
    }
}
