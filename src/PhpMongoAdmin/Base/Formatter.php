<?php
namespace PhpMongoAdmin\Base;

use MongoId;
use MongoDate;

/**
 * Class Formatter provides a set of commonly used data formatting methods between document and string.
 * @package PhpMongoAdmin\Base
 */
class Formatter {

    /**
     * Covert a document to json
     * The json seems like the followings:
     * {
     *      "_id": "ObjectId(\"54c9f4f32736e7c8048b456b\")"
     *      "createAt" : "ISODate(\"2015-01-29T08:53:07.450Z\")"
     * }
     * @param $document
     * @return mixed
     */
    public static function document2Json($document) {
        if ($document instanceof MongoId) {
            $document = 'ObjectId("' . $document . '")';
        } else if ($document instanceof MongoDate) {
            // 'ISODate("' . date("Y-m-d\\TH:i:s.", (float)$document->sec) . $document->usec/1000 . 'Z")';
            // 'ISODate("' . gmdate(DATE_ATOM, $document->sec) . '")';
            $document = 'ISODate("' . date("Y-m-d\\TH:i:s.", (float)$document->sec) . $document->usec/1000 . 'Z")';
        } else if (is_array($document)) {
            foreach ($document as &$value) {
                $value = self::document2Json($value);
            }
        }

        return $document;
    }

    /**
     * Covert a json to document
     * @param $jsonDoc
     * @return string
     */
    public static function json2Document($jsonDoc) {
        $objectIdRegular = '/^ObjectId\((\'|")([0-9a-z]{24})(\'|")\)$/';
        $dateRegular = '/^ISODate\((\'|")([0-9T\-:]+)\.([0-9]+)Z(\'|")\)$/';
        foreach ($jsonDoc as &$value) {
            if (preg_match_all($objectIdRegular, $value, $matches)) {
                $value = new MongoId($matches[2][0]);
            } else if (preg_match_all($dateRegular, $value, $matches)) {
                $sec = strtotime($matches[2][0]);
                $usec = (int) $matches[3][0] * 1000;
                $value = new MongoDate($sec, $usec);
            } else if (is_array($value)) {
                $value = self::Json2Document($value);
            }
        }

        return $jsonDoc;
    }

    /**
     * Covert a string to MongoId
     * The string is "ObjectId(\"54c9f4f32736e7c8048b456b\")" or "54c9f4f32736e7c8048b456b"
     * @param $str
     * @return bool|MongoId
     */
    public static function str2MongoId($str) {
        $id = false;
        $objectIdRegular = '/^ObjectId\((\'|")([0-9a-z]{24})(\'|")\)$/';
        $idRegular = '/^[0-9a-z]{24}$/';

        if (preg_match_all($objectIdRegular, $str, $matches)) {
            $id = new MongoId($matches[2][0]);
        } else if (preg_match($idRegular, $str)) {
            $id = new MongoId($str);
        }

        return $id;
    }
}