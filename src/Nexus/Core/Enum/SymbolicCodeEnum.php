<?php
namespace Nexus\Core\Enum;

use Nexus\Core\Enum\Interface\EnumInterface;
use Nexus\Core\Enum\Traits\EnumTrait;

enum SymbolicCodeEnum:string implements EnumInterface
{
    use EnumTrait;

    case DOT           = '.';
    case COMMA         = ',';
    case PIPE          = '|';
    case COLON         = ':';
    case SEMICLON      = ';';
    case AT_MARK       = '@';
    case UNDERBAR      = '_';
    case BACKSLASH     = '\\';
    case QUESTION_MARK = '?';
    case SHARP         = "#";
    case EQUAL         = '=';
    case SLASH         = '/';
    case MIDDLE_POINT  = '・';
    case PPERCENT      = '%';
    case CRLF          = '\r\n';
}