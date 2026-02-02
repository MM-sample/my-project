<?php
namespace Nexus\Core\Injection;

/**
 * Injector (Lite Edition)
 * * コンストラクタの型宣言に基づいた簡易DIクラス。
 * ※Lite版では、1階層のみの依存解決に限定されており、
 * 依存先がさらに依存を持つ（再帰的な解決が必要な）場合は非対応です。
 */
class Injector
{
    /**
     * 指定されたクラスのインスタンスを取得
     */
    public static function callClass(string $_className, array $_specifiedArguments = []): object
    {
        $_reflectionClass = new \ReflectionClass($_className);

        if (is_null($_reflectionMethod = $_reflectionClass->getConstructor())) {
            return $_reflectionClass->newInstance();
        }

        return $_reflectionClass->newInstanceArgs(self::_resolution($_reflectionMethod, $_specifiedArguments));
    }

    /**
     * 引数の解決
     */
    private static function _resolution(\ReflectionFunctionAbstract $_reflectionMethod, array $_specifiedArguments = []): array
    {
        $_argumentCollection = [];

        foreach ($_reflectionMethod->getParameters() as $_key => $_reflectionParameter) {
            // 指定された引数を優先
            if (isset($_specifiedArguments[$_key])) {
                $_argumentCollection[] = $_specifiedArguments[$_key];
                continue;
            }

            if (isset($_specifiedArguments[$_reflectionParameter->name])) {
                $_argumentCollection[] = $_specifiedArguments[$_reflectionParameter->name];
                continue;
            }

            $_argumentCollection[] = self::_getArgument($_reflectionParameter);
        }
        return $_argumentCollection;
    }

    /**
     * 引数の定義から要素を取得 (Lite版制約付き)
     */
    private static function _getArgument(\ReflectionParameter $_reflectionParameter): mixed
    {
        if ($_reflectionParameter->isDefaultValueAvailable()) {
            return $_reflectionParameter->getDefaultValue();
        }

        $_type = $_reflectionParameter->getType();
        if (is_null($_type) || $_type->isBuiltin()) {
            return null;
        }

        $_targetClassName = $_type->getName();
        $_tempReflection = new \ReflectionClass($_targetClassName);

        $_constructor = $_tempReflection->getConstructor();
        if ($_constructor && $_constructor->getNumberOfRequiredParameters() > 0) {
            return null;
        }

        // 引数なしでインスタンス化できる場合のみ実行
        return $_tempReflection->newInstance();
    }

}