# ADR-001: OpenAPI仕様検証ライブラリの選択

## 概要

APIテストにおいてOpenAPI仕様への準拠を自動検証するため、Laravel OpenAPI Validator（kirschbaum-development/laravel-openapi-validator）を採用する。

## 課題

1. **API仕様準拠の保証**: 実装されたAPIがOpenAPI仕様と一致していることを継続的に検証する必要がある
2. **テスト効率の向上**: 手動での仕様チェックによる工数とヒューマンエラーを削減したい
3. **品質ゲートの強化**: CI/CDパイプラインでAPI仕様違反を自動検出したい

## 決定事項

**Laravel OpenAPI Validator**を採用し、元ライブラリ（PHP League OpenAPI PSR-7 Validator）の直接使用は見送る。

## ステータス

Accepted

## 詳細

### 前提

- Laravel 12フレームワークを使用
- OpenAPI 3.0仕様でAPI定義済み
- PHPUnitを使用したテスト環境
- API仕様準拠の自動検証が必要

### 制約

- Laravel HTTPテストとの統合が必要
- 開発チームのLaravel習熟度が高く、PSR-7の専門知識は限定的
- 迅速な導入と最小限の学習コストが要求される

### 検討した選択肢

#### 選択肢1: Laravel OpenAPI Validator（採用）

**利点:**
- Laravel完全統合：トレイト1つ（`ValidatesOpenApiSpec`）でテストに組み込み可能
- 設定の簡素化：Laravel HTTPテストと自然に連携
- 最小限のボイラープレート：追加コードがほとんど不要
- Laravel向け最適化：認証、ルーティング、レスポンス処理が統合済み

**欠点:**
- 間接依存：元ライブラリの更新が遅れる可能性
- カスタマイズ制限：Laravel特有の機能に限定
- メンテナンス依存：ラッパーの維持に依存

#### 選択肢2: PHP League OpenAPI PSR-7 Validator（見送り）

**利点:**
- 直接制御：全機能に直接アクセス可能
- 拡張性：カスタムフォーマット、詳細設定が可能
- フレームワーク非依存：将来の移植性

**欠点:**
- 設定複雑：PSR-7メッセージの変換が必要
- ボイラープレート：Laravel統合のためのコード量増加
- 学習コスト：PSR-7の理解が必要

### 採用理由

1. **開発効率**: プロジェクトではAPIテストの迅速な導入が重要
2. **メンテナンス性**: Laravelプロジェクトに最適化された実装により保守が容易
3. **チーム効率**: 学習コストが低く、コードレビューが容易
4. **統合の自然さ**: 既存のLaravel HTTPテストワークフローとシームレスに統合

### 実装指針

- テストクラスで`ValidatesOpenApiSpec`トレイトを使用
- OpenAPI仕様ファイル（`openapi.yaml`）を適切に配置
- 必要に応じて検証をスキップするオプションを活用
- CI/CDパイプラインでの自動実行を設定