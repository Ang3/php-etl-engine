<?php

namespace Ang3\Component\ETL\Contract\Enum;

enum ErrorStage
{
    case Headers;
    case Field;
    case Row;
    case Writer;
}