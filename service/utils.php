<?php

function datetime_format($sourceString, $formatString)
{
    $dateTime = date_create($sourceString);
    return date_format($dateTime, $formatString);
}
