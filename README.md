# 研究用比較アプリ（共通バックエンド + 分離フロント）

## 構成

```
/project
 ├ backend   # Laravel (Blade + API)
 ├ frontend  # React (Vite)
 └ docker    # docker-compose（PostgreSQL / backend / frontend）
```

Docker Compose で **PostgreSQL・Laravel・Vite（React）** をまとめて起動します。

- Laravel: `http://localhost:8000`
- React（開発サーバー）: `http://localhost:5173`
- PostgreSQL: ホストからは `localhost:5432`（ユーザー/DB 名とも `research`、パスワード `research`）

## 前提

- Docker Desktop が起動していること
- WSL2 を利用可能な状態であること（Docker Desktop 設定）

## 初回セットアップ

```bash
docker compose -f docker/docker-compose.yml build backend
docker compose -f docker/docker-compose.yml up -d
```

`backend` コンテナ起動時に `php artisan migrate` と `ResearchSampleSeeder` が実行され、DB に初期データが入ります（既存データがある場合はスキップされる条件あり）。

## 動作確認URL

- Blade: `http://localhost:8000/blade/hello`
- API（Hello）: `http://localhost:8000/api/hello`
- API（Items）: `http://localhost:8000/api/items`（レスポンスは `{ "data": [ { "id", "name" }, ... ] }`）
- Laravel ヘルス: `http://localhost:8000/up`
- React: `http://localhost:5173`

## 実装済み最小機能

- **データ**: PostgreSQL 上の `greetings`（Hello メッセージ）と `items`（一覧）を利用
- **Blade**: `resources/views/hello.blade.php` でメッセージと items を表示（Service 経由で DB と同じ内容）
- **API**: `GET /api/hello` は `{ "message": "..." }`、`GET /api/items` は上記 `data` 配列形式で JSON 返却
- **React**: 上記 API を取得して表示。`VITE_API_BASE_URL` 未指定時は **`/api`**（Vite のプロキシで Laravel に転送）。Docker では `VITE_API_PROXY_TARGET` がバックエンド URL

## テストと品質確認

### Laravel テスト

PHPUnit は `phpunit.xml` により **SQLite（メモリ）** で実行されます。

```bash
docker exec docker-backend-1 php artisan test
```

### React lint / build

```bash
docker run --rm -v "c:/Research/research-test-app/frontend:/app" -w /app node:22-alpine sh -lc "npm run lint"
docker run --rm -v "c:/Research/research-test-app/frontend:/app" -w /app node:22-alpine sh -lc "npm run build"
```

## 評価指標の取得方法（最小導線）

### 1) テスト通過率

- CI 実行結果（`.github/workflows/ci.yml`）から成功/失敗を記録
- 指標例: `成功テスト数 / 総テスト数`

### 2) 修正工数

- 作業ごとにブランチ運用し、Git 差分から件数取得
- 例:
  - 変更ファイル数: `git diff --name-only main...HEAD | wc -l`
  - 変更行数: `git diff --shortstat main...HEAD`

### 3) エラー率

- Laravel: `backend/storage/logs/laravel.log`
- React: 開発サーバーログ（`docker compose -f docker/docker-compose.yml logs frontend`）
- API エラー/通信エラー/描画エラーを分類して件数化

### 4) 作業負荷（主観）

- ライブラリ更新作業ごとに簡易アンケート（5 段階）を記録
- 例: 「原因特定しやすさ」「修正しやすさ」「再現しやすさ」

## 停止

```bash
docker compose -f docker/docker-compose.yml down
```
