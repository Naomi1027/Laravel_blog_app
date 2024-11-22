# Laravelを使ったブログアプリ

## スタック
```
### backend
- Lang : PHP v8.2
- Framework: Laravel v10.x

### front
- CSS: tailwind v3.x

### Infra
- DB: PostgreSQL v16.x
- WEB: nginx v1.x
- MAIL: mailpit v1.x
```
**Laravel10にしている理由**
- 実際の案件ではLaravel10(とそれより以前のバージョン)のディレクトリ構成のまま動いているプロダクトが多いから
- 最新版(現状Laravel11)ではZennやQiitaなど初学者がよく使うサイトで拾える情報が少ないため
## 開発環境
### URL
- Laravel: http://localhost:8080
- Mailpit: http://localhost:8025

### 環境構築
1. git clone
```
git clone リポジトリに記載されているssh
```

2. cloneしたディレクトリに移動して下記コマンドを実行
```
make init
```

3. その他便利コマンドはMakefile参照

## 本番環境
### URL
- https://www.naomoto27.net
- AWS S3 

- CodeRabbitを導入しました
