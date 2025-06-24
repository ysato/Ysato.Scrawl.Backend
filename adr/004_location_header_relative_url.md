# ADR-004: Locationヘッダーにおける相対URL採用

## ステータス

Accepted

## 概要

POST リクエストによるリソース作成時に返すLocationヘッダーの値について、絶対URLではなく相対URLを採用する。

## 課題

RESTful APIにおいて、リソース作成（POST）のレスポンスでLocationヘッダーを返す際、以下の選択肢がある：

1. **絶対URL**: `http://localhost:8000/threads/101`
2. **相対URL**: `/threads/101`

どちらを採用するかについて統一的な方針を決定する必要がある。

## 決定事項

**相対URLを採用する**

POST /threads および POST /threads/{threadId}/scratches などのすべてのリソース作成エンドポイントにおいて、Locationヘッダーには相対URLを設定する。

## 詳細

### 前提

- RESTful APIとしてHTTP標準に準拠したい
- クライアント側での利用しやすさを考慮したい
- 開発・本番環境間での一貫性を保ちたい

### 制約

- RFC 7231準拠が必要
- OpenAPI仕様との整合性が必要
- フロントエンドからの利用を考慮

### 検討した選択肢

#### 選択肢1: 絶対URLを採用

**メリット：**
- 明示的で分かりやすい
- 一部のHTTPクライアントで処理しやすい場合がある

**デメリット：**
- 環境（開発・本番）ごとにホスト名が変わるため実装が複雑
- プロキシ経由やロードバランサー後方での動作で問題が生じる可能性
- ホスト名やポート番号の管理が煩雑

#### 選択肢2: 相対URLを採用（採用）

**メリット：**
- RFC 7231 Section 7.1.2に明記されている標準的な手法
- 環境に依存しない
- プロキシ・ロードバランサー環境でも安全
- 実装がシンプル

**デメリット：**
- 一部のクライアントで絶対URLへの変換が必要な場合がある

### RFC 7231 Section 7.1.2 の根拠

[RFC 7231 Section 7.1.2](https://datatracker.ietf.org/doc/html/rfc7231#section-7.1.2) では、Locationヘッダーについて以下のように記述されている：

> "For 201 (Created) responses, the Location value refers to the primary resource created by the request."

また、RFC 7231では相対URLの使用が標準的であることが示されており、実際の多くのWebサービスでも相対URLが採用されている。

### 実装方針

```php
// 採用する実装
return response()->json($thread, 201, [
    'Location' => "/threads/{$thread->id}",
]);
```

### OpenAPI仕様での記述

OpenAPI仕様では相対URLに対応した仕様を採用：

```yaml
headers:
  Location:
    description: 作成されたスレッドのURL
    schema:
      type: string
      format: uri-reference  # 相対URLに対応
      example: /threads/101  # 相対URLの例示
```

## 影響

- すべてのリソース作成エンドポイントでLocationヘッダーは相対URLとなる
- フロントエンドでは受信したLocationヘッダーを適切に処理する必要がある
- 環境間での動作の一貫性が保たれる
- プロキシ・CDN環境での動作が安定する