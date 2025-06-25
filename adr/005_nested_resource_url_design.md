# ADR-005: ネストしたリソースURL設計の採用

## 概要

ScratchリソースのURL設計において、フラットなURL (`/scratches/{id}`) ではなく、ネストしたURL (`/threads/{threadId}/scratches/{scratchId}`) を採用する。

## 課題

ScratchリソースのCRUD操作において、以下の設計選択肢が存在する：

1. **フラット設計**: `/scratches/{scratchId}`
2. **ネスト設計**: `/threads/{threadId}/scratches/{scratchId}`

どちらの設計を採用すべきか決定が必要である。

## 決定事項

**ネストしたURL設計** (`/threads/{threadId}/scratches/{scratchId}`) を採用する。

## ステータス

Accepted

## 詳細

### 前提

- ScratchはThreadに属する従属リソースである
- ScratchはThread無しには存在できない（実存的依存関係）
- Ysato.Scrawlはスレッドベースのコンテンツ管理システムである

### 制約

- Zalando RESTful API Guidelinesに準拠する
- リソース間の関係性をURL構造で明確に表現する
- 開発者にとって直感的で理解しやすいAPI設計を提供する

### 検討した選択肢

#### 選択肢1: フラット設計 (`/scratches/{scratchId}`)

**メリット:**
- URLが短くシンプル
- リソースの独立性を示唆
- 実装が単純

**デメリット:**
- リソース間の階層関係が不明確
- ビジネスドメインの構造を反映しない
- Scratchの所属Threadが URL から判断できない
- 実存的依存関係を表現できない

#### 選択肢2: ネスト設計 (`/threads/{threadId}/scratches/{scratchId}`)

**メリット:**
- **ドメイン構造の明確化**: ThreadとScratchの親子関係を直感的に表現
- **実存的依存の表現**: ScratchがThread無しに存在し得ないことを明示
- **コンテキスト保持**: URLだけでScratchの所属Threadが明確
- **Zalandoガイドライン準拠**: "MUST identify resources and sub-resources via path segments"
- **ビジネスロジック反映**: スレッドベースプラットフォームの本質を表現

**デメリット:**
- URLが長くなる
- 実装が若干複雑（ダブルモデルバインディング）

### Zalandoガイドラインとの整合性

Zalando RESTful API Guidelines では以下を要求：

- **MUST identify resources and sub-resources via path segments**
- **MAY consider using (non-) nested URLs**
- **SHOULD limit number of sub-resource levels**

本決定は以下の点でガイドラインに準拠：

1. **パスセグメントによる識別**: `/threads/{threadId}/scratches/{scratchId}` でリソース階層を明示
2. **適切なネスト**: 1レベルのみのネストで複雑性を抑制
3. **ビジネスドメイン反映**: Thread-Scratchの関係性を正確に表現

### 技術的実装

```php
// Route定義
Route::put('/threads/{thread}/scratches/{scratch}', Scratches\PutAction::class);

// Controller実装
public function __invoke(Thread $thread, Scratch $scratch, PutRequest $request): Response
{
    // Laravel の自動モデルバインディングにより
    // Thread と Scratch の存在が自動検証される
    $scratch->update($request->validated());
    return response()->noContent();
}
```

### 一貫性の確保

既存のScratch関連エンドポイントも同様の設計を採用：
- `POST /threads/{threadId}/scratches` - 新規作成
- `PUT /threads/{threadId}/scratches/{scratchId}` - 更新
- `DELETE /threads/{threadId}/scratches/{scratchId}` - 削除

この決定により、API全体で一貫したリソース階層表現を実現する。
