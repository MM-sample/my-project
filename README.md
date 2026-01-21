「Nexus Core は、PHP 8.2+ のポテンシャルを最大限に引き出すために設計された
軽量・高速な基盤アーキテクチャです。
トランザクションのスタック管理や、アトミックなバルク操作など、
実務で求められる『堅牢性』と『パフォーマンス』を、最小限の記述で実現します。」

### Key Features
Advanced DI Engine: ReflectionAPIを活用し、コンストラクタベースの依存性注入を自動化。

Hierarchical Configuration: ドットシンタックスによる多階層設定への高速アクセスと、環境変数（.env）の動的キャッシュ。

Smart SQL Builder: カラム定義に基づいた Bulk Insert / Upsert の自動生成、およびインクリメント制御。

Transaction Stack Manager: ネストされたトランザクションの境界を自動制御し、複雑な業務ロジックの整合性を担保。

Flexible Env Handling: 実行環境（Prod/Local）に応じた php.ini の動的書き換えと最適化。

### Design Philosophy (なぜこれを作ったか)
現代のPHP開発において、Laravel等のフルスタックフレームワークは非常に強力ですが、一方で「内部で何が起きているか」をブラックボックス化させがちです。 本プロジェクトでは、以下の3点を証明するためにフルスクラッチでの実装を選択しました。

Framework Agnostic: 特定のフレームワークに依存せず、PHPの本質的な機能を使いこなす設計能力。
Performance First: マジックメソッドやReflectionを適切にキャッシュし、オーバーヘッドを最小限に抑える実装力。
Reliability: 不整合を許さないトランザクション設計など、エンタープライズ用途で必須となる堅牢性の追求。
