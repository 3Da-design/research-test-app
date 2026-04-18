# 主要ファイル詳細まとめ

このドキュメントは、`research-test-app` の実装を追いやすくするために、主要ファイルの役割と処理内容を整理したものです。

## 1. 全体構成を把握するファイル

### `README.md`

- プロジェクト全体（`backend` / `frontend` / `docker`）の構成、起動手順、動作確認 URL を定義。
- Docker Compose で PostgreSQL・バックエンド・フロントを起動し、マイグレーションと `ResearchSampleSeeder` による初期データ投入について記載。
- 研究用途としての最低限の評価指標（テスト通過率、修正工数、エラー率、作業負荷）を示す運用ガイド。
- 初見時はまずこのファイルを読むことで、実装の意図と最短確認手順を把握できる。

### `docker/docker-compose.yml`

- `postgres`（PostgreSQL 16）、`backend`（Laravel）、`frontend`（Node で Vite dev）のサービス定義。
- `backend` は `postgres` のヘルスチェック後に起動し、`migrate` → `db:seed --class=ResearchSampleSeeder` → `artisan serve` を実行。
- `frontend` は `VITE_API_PROXY_TARGET` でバックエンド URL を渡し、Vite の `/api` プロキシ先として利用できる。

## 2. バックエンド（Laravel）主要ファイル

### `backend/bootstrap/app.php`

- Laravel アプリの起動設定を行うエントリポイント。
- `web.php` / `api.php` / `console.php` のルーティング登録を実施。
- 組み込みのヘルスルート `/up` を有効化。
- `AllowFrontendCors` ミドルウェアを全体に追加し、React フロントからの API アクセスを許可。

### `backend/routes/web.php`

- Web ルート定義。
- `/` を `/blade/hello` にリダイレクト。
- `/blade/hello` を `HelloBladeController::hello()` にマッピング。

### `backend/routes/api.php`

- API ルート定義（プレフィックス `/api`）。
- `GET /api/hello` と `GET /api/items` を `HelloApiController` に紐づけ。
- フロントエンドのデータ取得元として機能する。

### `backend/app/Http/Middleware/AllowFrontendCors.php`

- CORS 用ミドルウェア。
- `Origin` が `localhost` / `127.0.0.1` / `[::1]`（任意ポート）のローカル開発向け URL ならそのオリジンを返し、それ以外は `http://localhost:5173` を返す。
- 許可メソッド/ヘッダーを明示。`OPTIONS` は 204 でプリフライトを処理。

### `backend/app/Http/Controllers/Api/HelloApiController.php`

- API レスポンスを返すコントローラ。
- `ResearchSampleService` を DI で受け取り、`hello()` の結果を JSON 化。
- `items()` は `data` キーで配列をラップした JSON（`{ "data": [ ... ] }`）を返す。

### `backend/app/Http/Controllers/Blade/HelloBladeController.php`

- Blade 表示向けコントローラ。
- `ResearchSampleService` の `hello()['message']` と `items()` を `hello` ビューへ受け渡し。
- API 版と同じデータソース（DB）を使い、Blade 画面でも同一内容を表示。

### `backend/app/Services/ResearchSampleService.php`

- アプリのユースケースをまとめるサービス層。
- `hello()` は `Greeting` モデルから 1 件の `message` を取得（無い場合は固定のフォールバック文字列）。
- `items()` は `ItemRepository::all()` の結果を返す。

### `backend/app/Repositories/ItemRepository.php`

- Item データ取得を担当するリポジトリ層。
- Eloquent の `Item` を `id` 昇順で取得し、`id` / `name` の配列に整形して返す。

### `backend/app/Models/Greeting.php` / `backend/app/Models/Item.php`

- それぞれ `greetings` / `items` テーブルに対応する Eloquent モデル。
- `fillable` で一括代入可能な属性を定義。

### `backend/database/migrations/2026_04_17_000000_create_greetings_table.php`

- `greetings` テーブル（`message` など）を作成。

### `backend/database/migrations/2026_04_17_000001_create_items_table.php`

- `items` テーブル（`name` など）を作成。

### `backend/database/seeders/ResearchSampleSeeder.php`

- サンプルデータ投入。`Greeting` が空なら Hello メッセージを 1 件作成。
- `Item` が空なら Laravel / React / Comparison Sample の 3 件を `insert`（タイムスタンプ付き）。

### `backend/database/seeders/DatabaseSeeder.php`

- デフォルトのシード処理。ユーザー作成に加え `ResearchSampleSeeder` を呼び出す。

### `backend/resources/views/hello.blade.php`

- Blade 画面テンプレート。
- `message` と `items` を HTML にレンダリング。
- `HelloBladeController` から受け取るデータ構造に依存。

### `backend/tests/Feature/ResearchSampleApiTest.php`

- `RefreshDatabase` と `ResearchSampleSeeder` により、API と Blade が DB 内容を返す/描画することを検証。

### `backend/phpunit.xml`

- テスト実行時は `DB_CONNECTION=sqlite`、`DB_DATABASE=:memory:` など testing 用環境変数を強制（実行時の PostgreSQL とは別にインメモリ SQLite でテスト）。

### `backend/composer.json`

- PHP 依存関係と Composer スクリプトを定義。
- `laravel/framework`、`phpunit`、`laravel/pint` など品質・開発系ツールを含む。
- `setup` / `dev` / `test` スクリプトが、環境構築と検証フローの基盤。

## 3. フロントエンド（React + Vite）主要ファイル

### `frontend/src/main.jsx`

- React アプリのクライアントエントリポイント。
- `App` を `StrictMode` でマウント。

### `frontend/src/App.jsx`

- 画面の中心コンポーネント。
- `VITE_API_BASE_URL`（未指定時は **`/api`**）を基準に `GET .../hello` と `GET .../items` を並列取得。
- `items` レスポンスは `itemsData.data` を使用（Laravel 側の `data` ラップに対応）。
- 成功時は状態更新、失敗時はエラーメッセージを表示。
- `ApiSection` を利用して表示ロジック（ローディング/エラー/成功）を整理。

### `frontend/src/components/ApiSection.jsx`

- 共通表示コンポーネント。
- タイトル、ローディング、エラー、子要素表示のレイアウトを一元化。
- 表示責務を分離して `App.jsx` の可読性を向上。

### `frontend/vite.config.js`

- Vite 設定ファイル。
- `@vitejs/plugin-react` を有効化。
- `VITE_API_PROXY_TARGET`（未設定時は `http://127.0.0.1:8000`）へ `/api` をプロキシ。Docker では Compose の環境変数で `backend:8000` などに向けられる。

### `frontend/package.json`

- Node 依存関係と npm scripts を定義。
- `dev` / `build` / `lint` / `preview` を提供し、開発・検証の標準コマンドを統一。

## 4. 主要なデータ・処理フロー

1. ブラウザが `frontend` を表示し、`App.jsx` が起動。
2. 開発時は `/api` が Vite プロキシ経由で Laravel に転送され、`GET /api/hello` と `GET /api/items` が並列実行される。
3. Laravel の `routes/api.php` が `HelloApiController` にルーティング。
4. `HelloApiController` が `ResearchSampleService` を呼び出し。
5. `hello()` は `greetings` テーブル（`Greeting`）、`items()` は `items` テーブル（`ItemRepository` → `Item`）を参照。
6. JSON レスポンスが React に返り、画面に表示。

## 5. 変更時の影響範囲の目安

- **API レスポンス形式を変更**: `HelloApiController` と `App.jsx` の双方を確認（`items` の `data` ラップ含む）。
- **表示だけ変更**: Blade なら `hello.blade.php`、React なら `App.jsx` / `ApiSection.jsx` が中心。
- **初期データやスキーマ変更**: マイグレーション、`Greeting` / `Item`、必要なら `ResearchSampleSeeder` とテスト（`ResearchSampleApiTest`）を確認。
- **CORS や接続不良対応**: `AllowFrontendCors.php`、Vite の `server.proxy` と `VITE_API_BASE_URL` / `VITE_API_PROXY_TARGET` を優先確認。
