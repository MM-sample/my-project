<?php

use Nexus\Core\Environment\Env;
use Nexus\Core\Environment\Ini;
use Nexus\Core\Injection\Injector;
use Nexus\Core\Libs\Config;
use Nexus\Core\Libs\Path\PathManager;
use Nexus\App\Sql\TestSql;

try {

    $rootPath = dirname(__DIR__);

    // オートローダー登録
    spl_autoload_register(function (string $class) use($rootPath):void {
        $class = ltrim($class, '\\');
        // DIRECTORY_SEPARATOR を活用してパスの一貫性を保持
        $_filePath = $rootPath . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        if (file_exists($_filePath)) require_once $_filePath;
    });

    echo '<pre>';
    echo "=== Nexus Core Bootstrap Demo ===\n\n";

    // 1. 基盤初期化
    Injector::callClass(PathManager::class, ['_rootPath' => $rootPath]);
    echo "✓ PathManager Initialized.\n";

    // 2. 環境変数ロード
    Env::init();
    echo "✓ Environment Loaded.\n\n";

    // 3. PHP Ini 実行時書き換えデモ
    echo "[Ini Overwrite Test]\n";
    echo "Default memory_limit: " . Ini::get('memory_limit') . "\n";
    Ini::setAll(Env::getIni());
    echo "Updated memory_limit: " . Ini::get('memory_limit') . " (Loaded from .env)\n\n";

    // 4. 高度な階層型Configアクセス
    echo "[Config Access Test]\n";
    echo "DB Host: ";
    var_dump(Config::get('db.connections.mysql.host'));

    // 5. SQL実行デモ
    echo "\n[Database Transaction & Bulk Test]\n";

    // Injectorにより依存関係を自動解決
    $testSql = Injector::callClass(TestSql::class);

    $testData = [
        ['name' => 'Nexus Engineer',  'kana' => 'ネクサス エンジニア'],
        ['name' => 'Nexus Engineer2', 'kana' => 'ネクサス エンジニア2'],
    ];

    echo "--- Transaction Start via virtualMethod ---\n";
    // virtualMethod により、BEGIN -> register() -> COMMIT が自動化される
    $result = $testSql->virtualMethod('register', $testData);

    echo $result ? "✓ Bulk Insert Success!\n" : "✗ Bulk Insert Failed\n";
    echo "--- Transaction End ---\n";

    echo "\n=== Demo Completed Successfully ===";

} catch(\Throwable $ex) {
    echo "\n--- Error Occurred ---\n";
    echo "Message: " . $ex->getMessage() . "\n";
    echo "File: " . $ex->getFile() . " (Line: " . $ex->getLine() . ")\n";
    // 詳細なスタックトレースが必要な場合は追加
    // var_dump($ex->getTraceAsString());
    exit;
}