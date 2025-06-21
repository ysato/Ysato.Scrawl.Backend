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

# Code Quality Principles

すべてのコード変更において、以下の原則を常に考慮すること：

## 保守性 (Maintainability)
- コードの意図が明確で理解しやすい
- 責任が適切に分離されている
- 命名が一貫性があり意味が明確
- 重複が最小化されている

## 拡張性 (Extensibility)
- 新機能追加時に既存コードの変更が最小限
- インターフェースや抽象化を適切に活用
- 設定や振る舞いがカスタマイズ可能
- オープン/クローズド原則に従っている

## テスト容易性 (Testability)
- 依存関係が注入可能
- 副作用が最小化されている
- 単一責任の原則に従っている
- モック・スタブが作成しやすい

## 設計原則への意識

以下の設計原則を常に意識すること（必ずしも厳密に守る必要はないが、トレードオフを理解した上で判断する）：

- **KISS (Keep It Simple, Stupid)**: シンプルで理解しやすい解決策を選ぶ
- **DRY (Don't Repeat Yourself)**: 重複を避け、知識を一箇所に集約する
- **YAGNI (You Aren't Gonna Need It)**: 現在必要でない機能は実装しない
- **SOLID原則**: 単一責任、開放閉鎖、リスコフ置換、インターフェース分離、依存性逆転

## コーディングスタイル

### フォーマットルール
- **return文の前には必ず1行空ける** - ただし、メソッドがreturn文1行のみの場合はこの限りではない

```php
// ✅ Good: return文の前に空行
public function process(): string
{
    $result = $this->doSomething();
    $processed = $this->transform($result);
    
    return $processed;
}

// ✅ Good: 1行のみのreturnは空行不要
public function getId(): string
{
    return $this->id;
}

// ❌ Bad: return文の前に空行がない
public function calculate(): int
{
    $value = $this->getValue();
    return $value * 2;
}
```

### 命名規則

クラス、メソッド、変数を命名する際は、明確性と具体性を優先する：

- **過度に汎用的な用語を避ける** - `Manager`, `Processor`, `Handler`, `Service`, `Utility` 等は、それが具体的な責任を正確に表現する場合のみ使用
- **ドメイン固有の用語を使用** - 実際のビジネスロジックや技術的文脈を反映した用語を選択
- **具体的なアクションと責任を表現** - 抽象的な概念よりも具体的な機能を示す
- **長い名前を恐れない** - 意図を明確に伝えられるなら長い名前でも良い
- **適切な場合は動作動詞を含める** - `Calculator`, `Validator`, `Builder`, `Parser` など

**例:**
- ✅ `SessionManager` - セッション管理、責任が明確
- ✅ `RuntimeStubGenerator` - 実行時にスタブを生成
- ❌ `DataProcessor` - 汎用的すぎ、どんな処理か不明
- ❌ `FileHandler` - 曖昧、ファイルに何をするのか不明

クラス名がドキュメントとして機能することを目指す - 開発者が名前だけで目的を理解できるように。

## 開発ワークフロー

### コード変更完了時の必須手順
**重要:** 任意のコード変更を完了した際は、必ず以下を実行してください：

1. `just tests` - lint、QA、テストを実行する統合コマンド
2. エラーが発生した場合は修正してから完了とする

### 開発プロセス
このプロジェクトでは**計画ファースト**のアプローチを採用します：

1. **計画作成**: 実装前に必ず詳細な計画を作成
2. **計画確認**: 計画をユーザーに提示し、承認を得る
3. **実装実行**: 承認された計画に基づいて実装を進める

### コーディングルール
- **コメントアウト禁止**: コードの修正でコメントアウトを使用しない
- **削除原則**: 不要なコードは完全に削除する
- **一時的無効化**: コメントアウトは削除すべきコードの一時的な目印としてのみ使用

### 品質ゲート
コミット前には必ず `composer tests` を実行してください。これはリンティング（コードスタイル + 静的解析）とテストの両方を実行し、プロジェクトは最高レベルで厳格なコーディング標準と静的解析を強制します。

### API設計
- OpenAPI仕様ファイル (`openapi.yaml`) でAPI定義
- Spectralを使用したOpenAPI仕様のリンティング
- ドキュメントは `./docs` にGitサブモジュールとして配置
- フロントエンドはNext.jsを使用した別リポジトリ

### Docker統合
異なるシステム間で一貫したPHP/Composer環境のため、コンテナ化された開発ワークフローには `just` コマンドを使用してください。

## 開発プロセス重要事項

**実装前の必須確認:**
- **絶対に実装まで進めずに、まずは計画を立てる**
- **計画段階で必ずユーザーに確認を取る**
- 問題分析、解決方針、具体的な実装手順を明確にしてからユーザー承認を得る
- ユーザーの明示的な承認なしに実装作業（ファイル作成・編集）を開始しない

**README同期ルール:**
- **片方のREADME（README.md または README-ja.md）を更新したら、必ずもう一方も対応する更新を行う**
- 内容の一貫性を保つため、両方のREADMEは常に同期させる
- 一方のみの更新は禁止

**開発履歴記録ルール:**
- **重要なタスクや機能実装を完了した際は、必ず `./history.md` に記録を追記する**
- 記録内容：実際の依頼内容、成果物、学習ポイント
- チームメンバーとの共有と再現性確保のため継続的に更新
- 効率的な再現方法も含めて記述する

## アーキテクチャ決定記録（ADR）

### 作成ルール
- ファイル名: `001_タイトル.md` 形式で連番プレフィックスを付与
- 配置場所: `./adr/` ディレクトリ
- ファイル名は adr ディレクトリ内でユニークとなるようにする

### テンプレート構成
各ADRは以下の構成要素を含む：

1. **概要** - 決定事項の要約
2. **課題** - なぜその問題に取り組むのかを明確に説明
3. **決定事項** - アーキテクチャの方向性を明確に示す
4. **ステータス** - Proposed/Accepted/Rejected/Deprecated/Superseded
5. **詳細**
    - 前提 - 前提条件を明確に記述
    - 制約 - 環境上の制約や追加の制約を記載
    - 検討した選択肢 - 検討した選択肢を具体的に列挙

### 記述ガイドライン
- 簡潔で明確な記述を心がける
- 他者の意見も考慮した内容とする
- 信頼性の高い意思決定記録を目指す
- 最小限の問題のみ文書化する
