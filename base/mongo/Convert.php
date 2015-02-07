<?php
namespace base\mongo;

use Exception;

/**
 * A convert util.
 **/
class Convert
{
    public static function Json2Str($json)
    {
        $stringContent = is_string($json) ? $json : json_encode($json);
        $objectIdRegular = '/"ObjectId\(\'([0-9a-z]{24})\'\)"/';
        $objectIdReplace = 'ObjectId("$1")';
        $dateReuglar = '/"ISODate\(\'([0-9A-Z\-:\.\+]+)\'\)"/';
        $dateReplace = 'ISODate("$1")';
        // Convert "ObjectId('54c9f4f32736e7c8048b456b')" to ObjectId("54c9f4f32736e7c8048b456b")
        $stringContent = preg_replace($objectIdRegular, $objectIdReplace, $stringContent);
        // Convert "ISODate('2015-01-29T08:53:07.450Z')" to ISODate("2015-01-29T08:53:07.450Z")
        $stringContent = preg_replace($dateReuglar, $dateReplace, $stringContent);

        return $stringContent;
    }

    public static function str2Json($stringContent) {
        $objectIdRegular = '/ObjectId\("([0-9a-z]{24})"\)/';
        $objectIdReplace = '"ObjectId(\'$1\')"';
        $dateReuglar = '/ISODate\("([0-9A-Z\-:\.\+]+)"\)/';
        $dateReplace = '"ISODate(\'$1\')"';
        // Convert ObjectId("54c9f4f32736e7c8048b456b") to "ObjectId('54c9f4f32736e7c8048b456b')"
        $stringContent = preg_replace($objectIdRegular, $objectIdReplace, $stringContent);
        // Convert ISODate("2015-01-29T08:53:07.450Z") to "ISODate('2015-01-29T08:53:07.450Z')"
        $stringContent = preg_replace($dateReuglar, $dateReplace, $stringContent);
        return json_decode($stringContent);
    }

    public static function document2Json($document) {
        if ($document instanceof \MongoId) {
            $document = 'ObjectId(\'' . $document . '\')';
        } else if ($document instanceof \MongoDate) {
            $document = 'ISODate(\'' . gmdate(DATE_ATOM, $document->sec) . '\')';
        } else if (is_array($document)) {
            foreach ($document as &$value) {
                $value = self::document2Json($value);
            }
        }

        return $document;
    }

    public static function Json2Document($jsonData) {
        $objectIdRegular = '/^ObjectId\(\'([0-9a-z]{24})\'\)$/';
        $dateReuglar = '/^ISODate\(\'([0-9A-Z\-:\.\+]+)\'\)$/';
        foreach ($jsonData as &$value) {
            if (preg_match_all($objectIdRegular, $value, $matches)) {
                $value = new \MongoId($matches[1][0]);
            } else if (preg_match_all($dateReuglar, $value, $matches)) {
                $value = new \MongoDate($matches[1][0]);
            } else if (is_array($value)) {
                $value = self::Json2Document($value);
            }
        }

        return $jsonData;
    }
}