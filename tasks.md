# Current Task: GET /threads/{threadId} 実装

## 完了済み ✅

### Red Phase
- [x] OpenAPI仕様の確認 (ThreadDetailスキーマ)
- [x] 基本的な失敗テストの作成
- [x] テストをOpenAPIスキーマに準拠するよう修正

### Green Phase
- [x] ルート追加 (`/threads/{thread}`)
- [x] コントローラー作成 (`app/Http/Controllers/Threads/Thread/GetAction.php`)
- [x] 最小実装でテスト通過確認
- [x] OpenAPIスキーマ検証の通過

## 残りのTODO 🔄

### Refactor Phase
- [x] **コントローラーのリファクタリング**
  - [x] 直接配列返却からResponderパターンへの移行
  - [x] `GetResponder`クラスの作成検討
  - [x] 既存の`GetResponder`パターンとの一貫性確保

- [ ] **テストケースの拡充**
  - [x] 404エラーケース（存在しないスレッド）
  - [ ] スクラッチ付きスレッドのテスト
  - [ ] スクラッチの並び順テスト（作成日時昇順）
  - [ ] 複数スクラッチでの詳細データ検証

- [ ] **エッジケースのテスト**
  - [ ] スクラッチが0件のスレッド
  - [ ] 削除されたユーザーのスレッド（該当する場合）
  - [ ] 不正なスレッドID（文字列など）

### 品質保証
- [ ] **品質ゲート**
  - [ ] `composer tests` 全テスト通過確認
  - [ ] 静的解析エラーの確認・修正
  - [ ] コードスタイル検証

### 実際のスクラッチ取得機能
- [ ] **スクラッチ関連の実装**
  - [ ] `scratches`リレーション取得の実装
  - [ ] スクラッチの作成日時昇順ソート
  - [ ] N+1問題の回避（Eager Loading）

### 次のステップ準備
- [ ] **次の機能への準備**
  - [ ] POST /threads/{threadId}/scratches の実装準備
  - [ ] PUT /threads/{threadId} の実装準備
  - [ ] DELETE /threads/{threadId} の実装準備

## 現在のステータス
- **フェーズ**: Refactor Phase
- **最新テスト結果**: ✅ PASS (1 test, 11 assertions)
- **OpenAPI準拠**: ✅ 検証済み

## 注意事項
- t-wada流TDDを厳格に遵守
- 各機能は1つずつ段階的に実装
- テストファーストの原則を維持
- OpenAPIスキーマとの整合性を常に確認
