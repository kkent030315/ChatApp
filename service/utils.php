<?php

/*
 * Convert DateTime String To Specific Format
 * @return string|bool
 */
function datetime_format($sourceString, $formatString)
{
    $dateTime = date_create($sourceString);
    return date_format($dateTime, $formatString);
}
