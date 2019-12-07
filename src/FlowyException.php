<?php
namespace flowy;

if(!defined("flowy_FlowyException")) {
    define("flowy_FlowyException", 1);

    class FlowyException extends \Exception
    {
    }

}