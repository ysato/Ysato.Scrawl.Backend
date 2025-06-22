# ADR-003: 階層型Seederとトレイトによるテストデータ管理

## 概要

テストにおけるデータ管理に**階層型Seeder + トレイト方式**を採用し、テストコード内でのFactory直接使用を禁止する。

## 課題

### 背景
Laravelアプリケーションの規模拡大に伴い、テストデータ管理が複雑化している：

1. **データパターンの多様化**: 空データ、少数データ、大量データなど異なるテストシナリオ
2. **テーブル数の増加**: 30テーブル以上での管理困難
3. **Factory散在**: テストメソッドごとのFactory直接使用による重複とメンテナンス困難
4. **依存関係の複雑化**: 関連テーブル間の整合性確保の困難

### 従来方式の問題点
```php
// 問題のあるパターン
public function testExample(): void
{
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);
    // テストごとに重複する構築ロジック
}
```

## 決定事項

**ステータス**: Accepted

### 採用方式: 階層型Seeder + トレイト

1. **Factory直接使用の全面禁止**
   - テストコード内でのFactory直接呼び出しを禁止
   - FactoryはSeeder内でのみ使用

2. **階層型Seeder構造**
   ```
   BaseTestSeeder (共通データ)
   ├── ThreadTestSeeder (スレッド関連)
   ├── UserTestSeeder (ユーザー関連)
   └── [機能別Seeder]
   ```

3. **トレイトによる共通化**
   ```
   Traits/
   ├── CreatesUsers
   ├── CreatesThreads
   └── CreatesScratches
   ```

## 詳細

### 前提条件
- Laravel 12のSeederとFactory機能を使用
- PHPUnitのRefreshDatabaseトレイトによるテストデータベース管理
- SQLiteインメモリデータベースでのテスト実行

### 制約条件
- テストの実行速度を維持する必要がある
- 既存テストとの互換性を保つ必要がある
- 開発者の学習コストを最小化する必要がある

### 検討した選択肢

#### 1. 単一Seeder方式
**概要**: 全テストで共通のSeederを使用
```php
class TestSeeder extends Seeder
{
    public function run(): void
    {
        // 全テーブルのデータを一括作成
    }
}
```
**メリット**: シンプル、理解しやすい
**デメリット**: 巨大化、変更影響範囲の拡大
**評価**: ❌ 大規模化に対応困難

#### 2. 複数Seeder方式
**概要**: テストケースごとに専用Seederを作成
```php
class EmptyDataSeeder extends Seeder { }
class LimitedDataSeeder extends Seeder { }
class FullDataSeeder extends Seeder { }
```
**メリット**: テストシナリオごとに最適化
**デメリット**: Seeder数の爆発的増加、重複コード
**評価**: ❌ 保守性の問題

#### 3. Factory直接使用方式
**概要**: 各テストメソッドでFactoryを直接使用
```php
public function testExample(): void
{
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);
}
```
**メリット**: 最大の柔軟性
**デメリット**: 重複コード、複雑性の増大
**評価**: ❌ 大規模アプリで管理困難

#### 4. 階層型Seeder + トレイト方式 (採用)
**概要**: 階層化されたSeederとトレイトの組み合わせ
```php
class BaseTestSeeder extends Seeder
{
    use CreatesUsers;
    
    public function run(): void
    {
        $this->createUsers();
    }
}

class ThreadTestSeeder extends BaseTestSeeder
{
    use CreatesThreads;
    
    public function runWithLimitedData(): void
    {
        parent::run();
        $this->createLimitedThreads();
    }
}
```
**メリット**: スケーラブル、再利用性、保守性
**デメリット**: 初期実装コスト
**評価**: ✅ 長期的な保守性とスケーラビリティを実現

### 実装パターン

#### 基本構造
```php
// 1. トレイト: 再利用可能なデータ作成ロジック
trait CreatesUsers
{
    protected function createUsers(): void
    {
        User::factory()->create(['id' => 1, 'name' => 'Test User']);
    }
    
    protected function createMultipleUsers(int $count = 5): void
    {
        User::factory()->count($count)->create();
    }
}

// 2. BaseSeeder: 共通データ
class BaseTestSeeder extends Seeder
{
    use CreatesUsers;
    
    public function run(): void
    {
        $this->createUsers();
    }
}

// 3. 機能別Seeder: 特定機能のテストデータ
class ThreadTestSeeder extends BaseTestSeeder
{
    use CreatesThreads, CreatesScratches;
    
    public function run(): void
    {
        parent::run();
        $this->createStandardThreads();
    }
    
    public function runWithLimitedData(): void
    {
        parent::run();
        $this->createLimitedThreads();
    }
}
```

#### 使用方法
```php
class GetActionTest extends TestCase
{
    // 標準データセット
    protected string $seeder = ThreadTestSeeder::class;
    
    public function testWithSpecialData(): void
    {
        // 特別なデータパターンが必要な場合
        $seeder = new ThreadTestSeeder();
        $seeder->runWithLimitedData();
        
        // テスト実行
    }
}
```

### 利点と効果

#### スケーラビリティ
- **30テーブル以上対応**: 階層化により管理可能な構造
- **データパターン拡張**: メソッド追加で新パターン対応

#### 再利用性
- **トレイトによる共通化**: データ作成ロジックの重複排除
- **継承による拡張**: BaseSeederの機能を各機能で活用

#### 保守性
- **責任の分離**: 各トレイトが特定テーブルの責任を持つ
- **依存関係の明確化**: 階層構造により依存関係が可視化

#### 柔軟性
- **データパターン切り替え**: メソッド呼び出しで簡単に変更
- **段階的構築**: 必要なデータのみ段階的に作成

### 制約事項

#### 開発ルール
1. **Factory直接使用禁止**: テストコード内でFactory::create()は使用不可
2. **Seeder内でのみFactory使用**: データ作成はSeederとトレイト内に限定
3. **命名規則**: `Creates{ModelName}` トレイト、`{Feature}TestSeeder` クラス

#### パフォーマンス考慮
- データ作成の最小化: 必要最小限のデータのみ作成
- メモリ使用量の最適化: 大量データ作成時の注意

### 移行戦略

#### 段階的移行
1. **新規テスト**: 新方式で実装
2. **既存テスト**: 問題発生時に新方式へ移行
3. **レガシーSeeder**: 段階的に新方式へ統合

#### 教育とドキュメント
- CLAUDE.mdでのルール明文化
- 実装例とベストプラクティスの共有

## 今後の方針

### 拡張計画
- 他機能（認証、通知等）への適用
- パフォーマンステストデータ用Seederの追加
- 本番環境デモデータ用Seederの整備

### 継続的改善
- トレイトの粒度最適化
- データ作成パターンの標準化
- テスト実行時間の継続的な監視

## 関連ドキュメント

- CLAUDE.md: テストデータ作成ルール
- ADR-002: テストデータ Seeder Only 原則
- Laravel Documentation: Database Testing
- PHPUnit Documentation: Database Testing