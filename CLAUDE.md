# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 開発日誌を作成すること

`dev-diary/yyyy-mm-dd_hhmm.md` の形式で開発日誌を作成してください。内容は以下の通りです。

- **日付**: yyyy-mm-dd
- **作業内容**:
    - 何をしたか
    - どのような問題が発生したか
    - どのように解決したか
- **次回の予定**
- **感想**: 開発の進捗や学び
- **気分**: なんかいい感じのことを書く
- **愚痴**: なんかいい感じのことを書く

## コードエクセレンス原則

このプロジェクトは**コードエクセレンス**を最優先とし、以下の4つの柱に基づいて開発を行います：

### 1. 可読性（Readability）
- **表現力のある命名**: 意図が明確に伝わる変数名・関数名・クラス名
- **自己文書化**: コメントに頼らず、コード自体が仕様を表現
- **適切な抽象化**: 複雑さを隠しつつ本質を明確に表現

### 2. 保守性（Maintainability）
- **単一責任**: 1つのクラス・関数は1つの責任のみ
- **疎結合**: モジュール間の依存関係を最小化
- **変更容易性**: 修正時の影響範囲が限定的

### 3. 堅牢性（Robustness）
- **防御的プログラミング**: 不正な入力・状態への適切な対応
- **包括的テスト**: 全ての分岐・エッジケースをカバー
- **例外安全性**: エラー状況での適切な処理

### 4. 効率性（Efficiency）
- **パフォーマンス**: 適切なアルゴリズムとデータ構造
- **リソース管理**: メモリ・I/O・ネットワークの効率的使用
- **スケーラビリティ**: 負荷増加への対応力

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

## 開発環境とコマンド

### 主要コマンド
```bash
# 開発環境起動
composer dev

# テスト実行
composer test
composer tests  # linting + テスト

# 品質チェック
composer lints  # コードスタイル + 静的解析
composer cs-fix # 自動修正

# OpenAPI検証
just spectral
```

### 品質保証ツール
- **PHP CodeSniffer**: PSR-12 + Doctrine Coding Standard
- **PHPStan**: レベルmax、Laravel拡張使用
- **Psalm**: エラーレベル1、Laravelプラグイン使用
- **PHPMD**: app/とsrc/ディレクトリ用設定

## アーキテクチャ

### ディレクトリ構造
- `app/` - Laravelアプリケーション（Controllers、Models、Providers）
- `src/` - Ysato\Scrawlパッケージソースコード
- `tests/` - PHPUnitテスト（FeatureとUnit）
- `adr/` - アーキテクチャ決定記録

### データモデル
- **Users**: ユーザー情報（id, name）
- **Threads**: スレッド（id, title, status[Open/Closed], timestamps, owner）
- **Scratches**: スクラッチエントリ（id, Markdownコンテンツ, timestamps, 所属スレッド）

### 設計原則
- **公開第一**: すべてのコンテンツはデフォルトで公開
- **学習重視**: 学習・開発ジャーナルとしての用途に特化
- **シンプルな権限**: 所有者のみが自分のコンテンツを管理
- **RESTful API**: 適切なHTTPメソッドとページネーション対応

## 開発プロセス（必須遵守事項）

### t-wada流TDD（テスト駆動開発）- 必須実施
**全ての機能実装はt-wada流TDDに従って行うこと。例外は認めない。**

#### TDDサイクル（厳格遵守）
1. **Red**: 失敗するテストを書く
   - 仕様を明確にするテストを作成
   - 実装が存在しない状態で期待値を定義
   - テストが正しく失敗することを確認

2. **Green**: 最小実装でテストを通す
   - テストを通すための最小限の実装
   - 完璧を目指さず、まずは動作させる
   - 品質は後のRefactorフェーズで向上

3. **Refactor**: コード品質を向上
   - 重複排除、可読性向上、設計改善
   - テストが通ることを確認しながら改善
   - Martin Fowlerリファクタリング原則に従う

#### 4フェーズ開発プロセス
1. **分析**: 要求と現状の把握
2. **計画**: TDD実装方針の策定
3. **承認**: ユーザーからの明示的承認
4. **TDD実装**: Red→Green→Refactorの厳格実施

### Martin Fowlerリファクタリング原則
- **Small Steps**: 小さなステップで安全に進める
- **Test-Driven**: テストが保証する安全なリファクタリング
- **Refactor Mercilessly**: 設計を継続的に改善
- **Keep It Working**: 常に動作する状態を保つ

### TDD品質指標
- **分岐網羅率**: 100%達成必須
- **テスト先行**: 実装前にテストが存在することを確認
- **OpenAPI準拠**: `ValidatesOpenApiSpec`による自動検証
- **2回実行**: データベース関連テストは必ず2回実行してIDの一意性確認

### 品質ゲート（必須実施）
**コード変更完了時は必ず `composer tests` を実行**
- エラーが発生した場合は必ずユーザーに確認を取る
- 全てのテストが通るまで実装完了とは認めない

## アーキテクチャ規約

### シングルアクションコントローラー
- **`__invoke()` メソッド**のみ使用
- **クラス名**: `〜Action` で終わる（例: `GetAction`, `PostAction`）
- **ディレクトリ構造**: URL構造をそのまま反映

#### 命名例
```
URL: GET /threads/{id}
→ app/Http/Controllers/Threads/Thread/GetAction.php

URL: POST /threads
→ app/Http/Controllers/Threads/PostAction.php
```

### Factory-Firstテストデータ戦略
- **ファクトリ**: テスト固有の動的データ（約95%）
- **シーダー**: 静的なマスターデータのみ（約5%）
- **OpenAPI検証**: `ValidatesOpenApiSpec` による自動検証

## コーディング規約

### フォーマットルール
```php
// ✅ return文の前に空行
public function process(): string
{
    $result = $this->doSomething();
    
    return $result;
}

// ✅ 1行のみの場合は空行不要
public function getId(): string
{
    return $this->id;
}
```

### 禁止事項
- コメントアウトによる修正（削除原則）
- リソースコントローラーの使用
- テストでのSeeder乱用

### コードエクセレンス実装例
```php
// ✅ コードエクセレンス原則に従った実装
class CreateThreadAction
{
    public function __invoke(CreateThreadRequest $request): JsonResponse
    {
        // TDDで先にテストを書いてから実装
        $thread = $this->threadService->createThread(
            userId: $request->user()->id,
            title: $request->validated('title'),
            initialContent: $request->validated('content')
        );
        
        return new JsonResponse($thread, 201);
    }
}

// ✅ 対応するテスト（実装前に作成）
public function testCanCreateThreadWithValidData(): void
{
    // Red: 失敗するテストを最初に書く
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->post('/threads', [
        'title' => 'Learning Laravel',
        'content' => 'Starting my Laravel journey'
    ]);
    
    $response->assertStatus(201);
    $this->assertDatabaseHas('threads', [
        'title' => 'Learning Laravel',
        'user_id' => $user->id
    ]);
}
```

## 重要事項（絶対遵守）

### TDD実装の絶対条件
- **テストファースト**: 実装コードより先にテストを書く
- **Red-Green-Refactor**: このサイクルを厳格に守る
- **例外禁止**: TDDを飛ばした実装は一切認めない

### 実装前の必須確認
- **計画策定**: 実装方針をTDDサイクルで計画
- **ユーザー承認**: 明示的な承認なしに実装開始禁止
- **品質確認**: `composer tests` 通過まで完了とは認めない

### ADR（アーキテクチャ決定記録）
- ファイル名: `001_タイトル.md` 形式で連番プレフィックス
- 配置場所: `./adr/` ディレクトリ
- 重要な技術判断は必ずADRとして記録

---

**重要**: 実装前は必ず計画を立て、ユーザー承認を得てから作業を開始すること
