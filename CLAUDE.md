# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## プロジェクト概要

Ysato/ScrawlはPHP 8.4とLaravel 12を使用したAPIバックエンドプロジェクトです：
- **Laravel App** (`app/` ディレクトリ): 標準的なLaravelアプリケーション構造
- **パッケージ** (`src/` ディレクトリ): 開発中のYsato\Scrawlカスタムパッケージ

このリポジトリはAPIを提供するバックエンドサービスです。フロントエンドは別のリポジトリでNext.jsを使用して実装されています。

### システムの目的
Ysato.Scrawlは学習・開発ジャーナルプラットフォームです：
- **学習体験の文書化**: ユーザーが学習過程や開発進捗を記録
- **知識共有**: 学習体験や洞察をコミュニティと共有
- **進捗追跡**: 学習プロジェクトの進行状況を管理
- **公開ポートフォリオ**: 学習の軌跡と専門知識を公開

### 主要機能
- **スレッドベースの投稿**: 学習トピックやプロジェクトをスレッドとして作成
- **スクラッチシステム**: スレッド内に段階的な更新や補足情報を記録
- **Markdownサポート**: リッチテキスト形式でのコンテンツ作成
- **公開アクセス**: すべてのコンテンツが公開で閲覧可能
- **進捗管理**: Open/Closedステータスでプロジェクトの完了状態を管理

プロジェクトは開発依存関係としてysato/catalystを使用し、包括的な品質保証ツールを含んでいます。

## 開発コマンド

### 主要な開発ワークフロー
```bash
# 完全な開発環境の起動（サーバー、キュー、ログ）
composer dev

# 全テストの実行
composer test

# カバレッジ付きテスト実行（xdebug使用）
composer coverage

# カバレッジ付きテスト実行（pcov使用 - より高速）
composer pcov
```

### 品質保証
```bash
# 全リンティング（コードスタイル + 静的解析）
composer lints

# リンティングを含む完全なテストスイート
composer tests

# 個別のQAツール
composer cs              # PHP CodeSniffer チェック
composer cs-fix          # PHP CodeSniffer 自動修正
composer phpmd           # PHP Mess Detector
composer qa              # PHPStan + Psalm
```

### API開発
```bash
# OpenAPI仕様のリンティング
just spectral

# Laravel開発サーバー起動
php artisan serve
```

### Docker ベース開発
```bash
# Dockerイメージのビルド
just build

# Docker経由でcomposer実行
just composer install
just composer require vendor/package

# OpenAPI リンティング
just spectral

# GitHub Actions をローカルで実行
just act

# Dockerイメージのクリーンアップ
just clean
```

## アーキテクチャ

### ディレクトリ構造
- `app/` - Laravelアプリケーション（Controllers、Models、Providers）
- `src/` - Ysato\Scrawlパッケージソースコード
- `tests/` - PHPUnitテスト（FeatureとUnit）
- `docs/` - ドキュメント（ysato/Ysato.Scrawl.DocsからのGitサブモジュール）
- `docker/` - 開発ツール用Docker設定

### データモデル
- **Users**: ユーザー情報（id, name）
- **Threads**: スレッド（id, title, status[Open/Closed], timestamps, owner）
- **Scratches**: スクラッチエントリ（id, Markdownコンテンツ, timestamps, 所属スレッド）

### ユーザー権限
**登録ユーザー**:
- 自分のスレッド作成・編集・削除
- 自分のスクラッチ追加・編集・削除
- スレッドステータス変更（Open/Closed）
- 全公開コンテンツ閲覧

**ゲストユーザー**:
- 全公開コンテンツの閲覧のみ

### テスト戦略
- **Feature Tests**: `tests/Feature/` - Laravelアプリケーションテスト
- **Unit Tests**: `tests/Unit/` - パッケージとユーティリティテスト
- テスト用SQLiteインメモリデータベース使用
- xdebugまたはpcov経由でカバレッジレポート利用可能

### コード品質設定
- **PHP CodeSniffer**: PSR-12 + Doctrine Coding Standardとカスタム除外設定
- **PHPStan**: レベルmaxでLaravel拡張使用
- **Psalm**: エラーレベル1でLaravelプラグイン使用
- **PHPMD**: app/とsrc/ディレクトリ用に設定
- 全ツールにレガシーコード用ベースラインファイル有り

### オートローディング
- `App\` 名前空間は `app/` にマップ
- `Ysato\Scrawl\` 名前空間は `src/` にマップ
- `Tests\` 名前空間は `tests/` にマップ

## 重要な開発ノート

### パッケージ開発
`src/` ディレクトリには中核となるYsato\Scrawlパッケージが含まれています。パッケージ機能を作業する際は、スタンドアロン使用とLaravel統合の両方で互換性があることを確認してください。

### システム固有の設計原則
- **公開第一**: すべてのコンテンツはデフォルトで公開（プライベートコンテンツなし）
- **学習重視**: 学習・開発ジャーナルとしての用途に特化
- **段階的文書化**: スクラッチシステムによる継続的更新サポート
- **シンプルな権限**: 所有者のみが自分のコンテンツを管理
- **RESTful API**: 適切なHTTPメソッドとページネーション対応

### コントローラー設計方針
このプロジェクトでは**シングルアクションコントローラー**のみを使用します：

#### 基本ルール
- **リソースコントローラーは使用しない**
- **`__invoke()` メソッド**を使用したシングルアクションコントローラー
- **クラス名**: `〜Action` で終わる（例: `GetAction`, `PostAction`）
- **ディレクトリ構造**: URL構造をそのまま反映

#### ディレクトリ・ファイル構造
URLパスとHTTPメソッドをディレクトリ構造とファイル名に直接対応：

```
URL: GET /threads/{id}
→ app/Http/Controllers/Threads/Thread/GetAction.php

URL: GET /threads/{threadId}/scratches  
→ app/Http/Controllers/Threads/Thread/Scratches/GetAction.php

URL: POST /threads
→ app/Http/Controllers/Threads/PostAction.php

URL: PUT /threads/{id}
→ app/Http/Controllers/Threads/Thread/PutAction.php

URL: DELETE /threads/{id}
→ app/Http/Controllers/Threads/Thread/DeleteAction.php
```

#### 命名規則
- **HTTPメソッド → ファイル名**: `Get`, `Post`, `Put`, `Delete` + `Action`
- **パスパラメータ → ディレクトリ**: `{id}`, `{threadId}` → `Thread/`（単数形）
- **リソース名 → ディレクトリ**: `threads` → `Threads/`、`scratches` → `Scratches/`

### 品質ゲート
コミット前には必ず `composer tests` を実行してください。これはリンティング（コードスタイル + 静的解析）とテストの両方を実行し、プロジェクトは最高レベルで厳格なコーディング標準と静的解析を強制します。

### API設計
- OpenAPI仕様ファイル (`openapi.yaml`) でAPI定義
- Spectralを使用したOpenAPI仕様のリンティング
- ドキュメントは `./docs` にGitサブモジュールとして配置
- フロントエンドはNext.jsを使用した別リポジトリ

### Docker統合
異なるシステム間で一貫したPHP/Composer環境のため、コンテナ化された開発ワークフローには `just` コマンドを使用してください。