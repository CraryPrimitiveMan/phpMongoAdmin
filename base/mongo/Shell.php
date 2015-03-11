<?php
namespace base\mongo;

/**
 * Connection represents a connection to a MongoDb server.
 **/
class Shell
{
    /**
     * Record the position
     * @var PositionStack
     */
    private static $positionStack;
    /**
     * Store the operate
     * @var array
     */
    private static $operate = array(
        '$set'      => array(),
        '$inc'      => array(),
        '$push'     => array(),
        '$pushAll'  => array(),
        '$pop'      => array(),
        '$pull'     => array(),
        '$pullAll'  => array(),
        '$rename'   => array(),     //db.test.update({"age":10}, {'$rename':{"age":"my_age"}})
        '$unset'    => array()      //db.test.update({"age":10}, {'$unset':{"age":1}})
    );


    /**
     * Get the javascript shell of query databases
     * @return string
     */
    public static function queryDb() {
        return "show dbs;";
    }

    /**
     * Get the javascript shell of drop a database
     * @return string
     */
    public static function dropDb($database) {
        return "db.getCollection('$database').drop();";
    }

    /**
     * Get the javascript shell of query collection
     * @return string
     */
    public static function query($collection, $condition = array()) {
        $conditonStr = "";
        if (!empty($condition)) {
            $conditonStr = Convert::Json2Str($condition);
        }
        return "db.getCollection('$collection').find($conditonStr);";
    }

    /**
     * Get the javascript shell of delete
     * @return string
     */
    public static function delete($collection, $id) {
        return "db.getCollection('$collection').remove({'_id': ObjectId('$id')});";
    }

    /**
     * Get the javascript shell of update
     * @return string
     */
    public static function update($collection, $newData, $oldData) {
        if ($newData !== $oldData) {
            $newDataJson = Convert::document2Json($newData);
            if (empty($oldData)) {
                $newDataStr = Convert::Json2Str($newDataJson);
                return "db.getCollection('$collection').insert($newDataStr);";
            } else {
                $id = $oldData['_id'] . '';
                $oldDataJson = Convert::document2Json($oldData);
                $updateStr = self::_generateShell($newDataJson, $oldDataJson);
                return "db.getCollection('$collection').update({'_id': ObjectId('$id')},$updateStr);";
            }
        } else {
            return "";
        }
    }

    /**
     * Whether array is assoc
     * @param $arr
     * @return bool
     */
    private static function isAssoc($arr) {
        return is_array($arr) && array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * Whether the new and old value has the same type
     * @param $newValue
     * @param $oldValue
     * @return bool
     */
    private static function isSameType($newValue, $oldValue) {
        return gettype($oldValue) === gettype($newValue);
    }

    /**
     * Whether the new and old array has the some same value
     * @param $newValue Array
     * @param $oldValue Array
     * @return bool
     */
    private static function hasSameValue($newJson, $oldJson) {
        foreach ($newJson as $newKey => $newValue) {
            foreach ($oldJson as $oldKey => $oldValue) {
                if ($newValue === $oldValue) {
                    return true;
                }
            }
        }
        return false;
        //$sameValue = array_intersect($newValue, $oldValue);
        //return !empty($sameValue);
    }

    /**
     * Whether the $str is a string
     * @param $str
     * @return bool
     */
    private static function isString($str) {
        $objectIdRegular = '/ObjectId\(["\']{1}([0-9a-z]{24})["\']{1}\)/';
        $dateReuglar = '/ISODate\(["\']{1}([0-9A-Z\-:\.\+]+)["\']{1}\)/';
        return is_string($str) && !preg_match($objectIdRegular, $str) && !preg_match($dateReuglar, $str);
    }

    private static function _analyze($newJson, $oldJson) {
        foreach ($newJson as $newKey => $newValue) {
            // Add the position to stack
            self::$positionStack->push($newKey);
            $finished = false;
            if (!array_key_exists($newKey, $oldJson)) {
                // New key in array
                foreach ($oldJson as $oldKey => $oldValue) {
                    if ($newValue === $oldValue) {
                        self::$operate['$rename'][] = array('key' => $oldKey , 'value' => $newKey);
                        unset($oldJson[$oldKey]);
                        $finished = true;
                        break;
                    }
                }
            } else if ($oldJson[$newKey] !== $newValue) {
                $oldValue = $oldJson[$newKey];
                if (self::isSameType($newValue, $oldValue)) {
                    // has the same type
                    if (is_int($oldValue) && is_int($newValue)) {
                        // int type
                        self::$operate['$inc'][] = array('key' => self::$positionStack->position, 'value' => $newValue - $oldValue);
                        $finished = true;
                    } else if (is_array($newValue) && is_array($oldValue) && self::hasSameValue($newValue, $oldValue)) {
                        // array type and has same value
                        if (!self::isAssoc($newValue) && !self::isAssoc($oldValue)) {
                            // the normal array type
                            /*
                            $matrix = array();
                            $newArrayCount = count($newValue);
                            for ($index = 0; $index < $newArrayCount; $index++) {
                                $matrix[] = array();
                            }
                            $length = 0;
                            $end = 0;
                            foreach ($newValue as $newIndex => $newItem) {
                                foreach ($oldValue as $oldIndex => $oldItem) {
                                    $lastCount = 0;
                                    if (!empty($matrix[$newIndex - 1][$oldIndex - 1])) {
                                        $lastCount = $matrix[$newIndex - 1][$oldIndex - 1];
                                    }
                                    if ($newValue === $oldValue) {
                                        $matrix[$newIndex][$oldIndex] = $lastCount + 1;
                                    }
                                    if (!empty($matrix[$newIndex][$oldIndex]) && $matrix[$newIndex][$oldIndex] > $length) {
                                        $length = $matrix[$newIndex][$oldIndex];
                                        $end = $newIndex;
                                    }
                                }
                            }
                            $start = $end = $length;
                            */
                            // Add the array item
                            $push = array_diff($newValue, $oldValue);
                            // Remove the array item
                            $pull = array_diff($oldValue, $newValue);
                            if (!empty($push)) {
                                // Remove the keys
                                $push = array_values($push);
                                if (count($push) === 1) {
                                    self::$operate['$push'][] = array('key' => self::$positionStack->position, 'value' => $push[0]);
                                } else {
                                    self::$operate['$pushAll'][] = array('key' => self::$positionStack->position, 'value' => $push);
                                }
                            }

                            if (!empty($pull)) {
                                // Remove the keys
                                $pull = array_values($pull);
                                if (count($pull) === 1) {
                                    self::$operate['$pull'][] = array('key' => self::$positionStack->position, 'value' => $pull[0]);
                                } else {
                                    self::$operate['$pullAll'][] = array('key' => self::$positionStack->position, 'value' => $pull);
                                }
                            }

                            if(empty($push) && empty($pull)) {
                                $positionArr = array('key' => self::$positionStack->position, 'value' => $newValue);
                                self::$operate['$set'][] = $positionArr;
                            }
                            $finished = true;
                        } else if (self::isAssoc($newValue) && self::isAssoc($oldValue)) {
                            // the assoc array type
                            self::_analyze($newValue, $oldValue);
                            $finished = true;
                        }

                    } else {
                        $positionArr = array('key' => self::$positionStack->position, 'value' => $newValue);
                        self::$operate['$set'][] = $positionArr;
                        $finished = true;
                    }
                }
            } else {
                $finished = true;
            }

            if(!$finished) {
                $positionArr = array('key' => self::$positionStack->position, 'value' => $newValue);
                self::$operate['$set'][] = $positionArr;
            }
            self::$positionStack->pop();
        }

        foreach ($oldJson as $oldKey => $oldValue) {
            // unset the extra key in $oldJson
            self::$positionStack->push($oldKey);
            if(!array_key_exists($oldKey, $newJson)) {
                $positionArr = array('key' => self::$positionStack->position, 'value' => 1);
                self::$operate['$unset'][] = $positionArr;
            }
            self::$positionStack->pop();
        }

    }

    /**
     * Generate a shell for update action
     * @param $newJson
     * @param $oldJson
     * @return string
     */
    private static function _generateShell($newJson, $oldJson)
    {
        self::$positionStack = new PositionStack();
        self::_analyze($newJson, $oldJson);
        $shell = '{ ';
        foreach (self::$operate as $key => $positionArr) {
            if (!empty($positionArr)) {
                $shell .= $key . ': {';
                foreach ($positionArr as $value) {
                    $valueStr = self::isString($value['value']) ? '"' . $value['value'] . '"' : Convert::Json2Str($value['value']);
                    $shell .= '"' . $value['key'] . '" : ' . $valueStr . ',';
                }
                $shell = rtrim($shell, ',');
                $shell .= '}, ';
            }
        }
        $shell = rtrim($shell, ', ');
        //$shell = Convert::Json2Str($shell);

        return $shell . '}';
    }
}