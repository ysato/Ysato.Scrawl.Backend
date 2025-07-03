# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 基本設定

### コミュニケーション規約
**すべてのコミュニケーションは日本語で行う**
- 分析、計画、実装、説明のすべてを日本語で実施

### プロジェクト概要
**Ysato.Scrawl**: 学習・開発ジャーナルプラットフォーム  
**技術スタック**: PHP 8.4 + Laravel 12 APIバックエンド

### 開発日誌とADR
**開発日誌**: `dev-diary/template.md`参照  
**ADR**: `adr/template.md`参照

## 開発環境・プロセス

### 主要コマンド
```bash
# テスト実行
composer test
composer tests  # linting + テスト

# 品質チェック
composer lints  # コードスタイル + 静的解析
composer cs-fix # 自動修正

# OpenAPI検証
just spectral
```

### タスク分類
- **複雑タスク**: 新機能・大規模リファクタリング・アーキテクチャ変更 → 4フェーズ必須
- **単純タスク**: typo修正・軽微な設定変更 → 直接実装可能

### 4フェーズプロセス
#### フェーズ1: 分析
- 現状把握と問題の特定
- 影響範囲の調査
- 制約・前提条件の確認

#### フェーズ2: 計画
- 作業方針の策定
- 実行ステップの設計
- 意図の明確化（なぜ必要か）

#### フェーズ3: 承認
- 意図と計画の説明
- ユーザーからの明示的承認取得

#### フェーズ4: 実行
- 計画に沿った段階的実行
- 継続的な検証
- 品質ゲート通過

### 品質ゲート（必須実施）
**全ての作業完了時は必ず `composer tests` を実行**
- エラーが発生した場合は必ずユーザーに確認を取る
- `composer tests`が通るまで完了とは認めない

## 実装規約

- **コードエクセレンス最優先**
- **t-wadaの推奨するTDD**
- **Martin Fowlerの推奨するリファクタリング**

### 品質指標
- **分岐網羅率100%達成**
- **OpenAPI準拠検証**: `ValidatesOpenApiSpec`使用

### アーキテクチャ規約

#### シングルアクションコントローラー
- `__invoke()` メソッドのみ使用
- クラス名は `〜Action` で終わる

**命名例**
```
URL: GET /threads/{id}
→ app/Http/Controllers/Threads/Thread/GetAction.php

URL: POST /threads
→ app/Http/Controllers/Threads/PostAction.php
```

#### Factory-Firstテストデータ戦略
- ファクトリを積極活用（動的データ）
- シーダーは静的マスターデータのみ

### コーディング規約

#### フォーマットルール
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

#### 禁止事項
- コメントアウトによる修正（削除原則）
