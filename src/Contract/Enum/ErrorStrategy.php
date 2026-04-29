<?php

namespace Ang3\Component\ETL\Contract\Enum;

enum ErrorStrategy
{
    case Continue;
    case Stop;
}