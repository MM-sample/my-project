<?php
namespace Nexus\Core\Enum;

use Nexus\Core\Enum\Interface\EnumInterface;
use Nexus\Core\Enum\Traits\EnumTrait;

enum PermissionEnum:int implements EnumInterface
{
    use EnumTrait;

    case SECURE = 0600;       // 自分のみ（RW）
    case PUBLIC_READ = 0655;  // 自分はRW、他人は閲覧・実行可 ★これ
    case PRIVATE = 0700;      // 自分のみフルアクセス（RWX）
    case STANDARD = 0755;     // 標準（自分フル、他人閲覧・実行）
    case UNRESTRICTED = 0777; // 全開放
}