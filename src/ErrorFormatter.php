<?php

namespace yii\graphql;

use Yii;
use GraphQL\Error\DebugFlag;
use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use yii\graphql\exceptions\ValidatorException;
use yii\web\HttpException;

/**
 * Class ErrorFormatter
 * @package yii\graphql
 */
class ErrorFormatter
{
    public static function formatError(Error $e)
    {
        $previous = $e->getPrevious();
        if ($previous instanceof \Throwable) {
            Yii::$app->getErrorHandler()->logException($previous);
            if ($previous instanceof ValidatorException) {
                return $previous->formatErrors;
            }

            if ($previous instanceof HttpException) {
                return ['code' => $previous->statusCode, 'message' => $previous->getMessage()];
            } else {
                return ['code' => $previous->getCode(), 'message' => $previous->getMessage()];
            }
        } else {
            Yii::error($e->getMessage(), $e::class);
        }

        $debugFlag = YII_DEBUG
            ? (DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE)
            : DebugFlag::NONE;

        return FormattedError::createFromException($e, $debugFlag);
    }
}
