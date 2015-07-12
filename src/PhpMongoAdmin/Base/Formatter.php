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
     * Covert a document to string
     * @param $document
     * @return string
     */
    public static function document2Str($document) {
        $jsonDoc = static::document2Json($document);
        return static::Json2Str($jsonDoc);
    }

    /**
     * Covert a string to document
     * @param $str
     * @return mixed
     */
    public static function str2Document($str) {
        $jsonDoc = static::str2Json($str);
        return static::json2Document($jsonDoc);
    }

    /**
     * Covert a document to json
     * The json seems like:
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
            $document = 'ISODate("' . gmdate(DATE_ATOM, $document->sec) . '")';
        } else if (is_array($document)) {
            foreach ($document as &$value) {
                $value = self::document2Json($value);
            }
        }

        return $document;
    }

    /**
     * Covert a json to str
     * @param $jsonDoc
     * @return string
     */
    public static function Json2Str($jsonDoc) {
        $stringContent = is_string($jsonDoc) ? $json : json_encode($jsonDoc);
        $objectIdRegular = '/"ObjectId\(\\\\"([0-9a-z]{24})\\\\"\)"/';
        $objectIdReplace = 'ObjectId("$1")';
        $dateReuglar = '/"ISODate\(\\\\"([0-9A-Z\-:\.\+]+)\\\\"\)"/';
        $dateReplace = 'ISODate("$1")';
        // Convert "ObjectId(\"54c9f4f32736e7c8048b456b\")" to ObjectId("54c9f4f32736e7c8048b456b")
        $stringContent = preg_replace($objectIdRegular, $objectIdReplace, $stringContent);
        // Convert "ISODate(\"2015-01-29T08:53:07.450Z\")" to ISODate("2015-01-29T08:53:07.450Z")
        $stringContent = preg_replace($dateReuglar, $dateReplace, $stringContent);

        return $stringContent;
    }

    /**
     * Covert a str to json
     * @param $stringContent
     * @return mixed
     */
    public static function str2Json($stringContent) {
        $objectIdRegular = '/ObjectId\("([0-9a-z]{24})"\)/';
        $objectIdReplace = '"ObjectId("$1")"';
        $dateReuglar = '/ISODate\("([0-9A-Z\-:\.\+]+)"\)/';
        $dateReplace = '"ISODate("$1")"';
        // Convert ObjectId("54c9f4f32736e7c8048b456b") to "ObjectId(\"54c9f4f32736e7c8048b456b\")"
        $stringContent = preg_replace($objectIdRegular, $objectIdReplace, $stringContent);
        // Convert ISODate("2015-01-29T08:53:07.450Z") to "ISODate(\"2015-01-29T08:53:07.450Z\")"
        $stringContent = preg_replace($dateReuglar, $dateReplace, $stringContent);
        return json_decode($stringContent, true);
    }

    /**
     * Covert a json to document
     * @param $jsonDoc
     * @return string
     */
    public static function json2Document($jsonDoc) {
        $objectIdRegular = '/^ObjectId\("([0-9a-z]{24})"\)$/';
        $dateReuglar = '/^ISODate\("([0-9A-Z\-:\.\+]+)"\)$/';
        foreach ($jsonDoc as &$value) {
            if (preg_match_all($objectIdRegular, $value, $matches)) {
                $value = new MongoId($matches[1][0]);
            } else if (preg_match_all($dateReuglar, $value, $matches)) {
                $value = new MongoDate(strtotime($matches[1][0]));
            } else if (is_array($value)) {
                $value = self::Json2Document($value);
            }
        }

        return $jsonDoc;
    }
}