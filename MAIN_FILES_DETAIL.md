# 主要ファイル詳細まとめ

このドキュメントは、`research-test-app` の実装を追いやすくするために、主要ファイルの役割と処理内容を整理したものです。

## 1. 全体構成を把握するファイル

### `README.md`
- プロジェクト全体（`backend` / `frontend` / `docker`）の構成、起動手順、動作確認URLを定義。
- 研究用途としての最低限の評価指標（テスト通過率、修正工数、エラー率、作業負荷）を示す運用ガイド。
- 初見時はまずこのファイルを読むことで、実装の意図と最短確認手順を把握できる。

## 2. バックエンド（Laravel）主要ファイル

### `backend/bootstrap/app.php`
- Laravelアプリの起動設定を行うエントリポイント。
- `web.php` / `api.php` / `console.php` のルーティング登録を実施。
- `AllowFrontendCors` ミドルウェアを全体に追加し、ReactフロントからのAPIアクセスを許可。

### `backend/routes/web.php`
- Webルート定義。
- `/` を `/blade/hello` にリダイレクト。
- `/blade/hello` を `HelloBladeController::hello()` にマッピング。

### `backend/routes/api.php`
- APIルート定義。
- `/api/hello` と `/api/items` を `HelloApiController` に紐づけ。
- フロントエンドのデータ取得元として機能する。

### `backend/app/Http/Middleware/AllowFrontendCors.php`
- CORS用ミドルウェア。
- `http://localhost:5173` のオリジンを許可し、許可メソッド/ヘッダーを明示。
- `OPTIONS` リクエストには 204 を返し、プリフライトを処理。

### `backend/app/Http/Controllers/Api/HelloApiController.php`
- APIレスポンスを返すコントローラ。
- `ResearchSampleService` をDIで受け取り、`hello()` と `items()` の結果をJSON化して返却。
- プレゼンテーション層（HTTP）とビジネスロジック層（Service）を分離する役割。

### `backend/app/Http/Controllers/Blade/HelloBladeController.php`
- Blade表示向けコントローラ。
- `ResearchSampleService` の結果を `hello` ビューへ受け渡し。
- API版と同じデータソースを使い、Blade画面でも同一内容を表示。

### `backend/app/Services/ResearchSampleService.php`
- アプリのユースケースをまとめるサービス層。
- `hello()` は固定メッセージを返す。
- `items()` は `ItemRepository` から取得した配列を返す。

### `backend/app/Repositories/ItemRepository.php`
- Itemデータ取得を担当するリポジトリ層。
- 現状はDB未使用で、固定の配列（Laravel/React/Comparison Sample）を返すサンプル実装。
- 将来DB連携に差し替える場合の境界点になるファイル。

### `backend/resources/views/hello.blade.php`
- Blade画面テンプレート。
- `message` と `items` をHTMLにレンダリング。
- `HelloBladeController` から受け取るデータ構造に依存。

### `backend/composer.json`
- PHP依存関係とComposerスクリプトを定義。
- `laravel/framework`、`phpunit`、`laravel/pint` など品質・開発系ツールを含む。
- `setup` / `dev` / `test` スクリプトが、環境構築と検証フローの基盤。

## 3. フロントエンド（React + Vite）主要ファイル

### `frontend/src/main.jsx`
- Reactアプリのクライアントエントリポイント。
- `App` を `StrictMode` でマウント。

### `frontend/src/App.jsx`
- 画面の中心コンポーネント。
- `VITE_API_BASE_URL`（未指定時は `http://localhost:8000/api`）を基準にAPI呼び出し。
- `hello` と `items` を並列取得し、成功時は状態更新、失敗時はエラーメッセージを表示。
- `ApiSection` を利用して表示ロジック（ローディング/エラー/成功）を整理。

### `frontend/src/components/ApiSection.jsx`
- 共通表示コンポーネント。
- タイトル、ローディング、エラー、子要素表示のレイアウトを一元化。
- 表示責務を分離して `App.jsx` の可読性を向上。

### `frontend/vite.config.js`
- Vite設定ファイル。
- `@vitejs/plugin-react` を有効化し、React開発体験（HMR等）を提供。

### `frontend/package.json`
- Node依存関係とnpm scriptsを定義。
- `dev` / `build` / `lint` / `preview` を提供し、開発・検証の標準コマンドを統一。

## 4. 主要なデータ・処理フロー

1. ブラウザが `frontend` を表示し、`App.jsx` が起動。
2. `App.jsx` が `GET /api/hello` と `GET /api/items` を並列実行。
3. Laravelの `routes/api.php` が `HelloApiController` にルーティング。
4. `HelloApiController` が `ResearchSampleService` を呼び出し。
5. `ResearchSampleService` が必要に応じて `ItemRepository` からデータ取得。
6. JSONレスポンスがReactに返り、画面に表示。

## 5. 変更時の影響範囲の目安

- **APIレスポンス形式を変更**: `HelloApiController` と `App.jsx` の双方を確認。
- **表示だけ変更**: Bladeなら `hello.blade.php`、Reactなら `App.jsx` / `ApiSection.jsx` が中心。
- **データ取得元をDB化**: `ItemRepository` を主に改修し、Service/Controllerへの影響を確認。
- **CORSや接続不良対応**: `AllowFrontendCors.php` と `VITE_API_BASE_URL` 設定を優先確認。
