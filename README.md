# sujica — suji 워드프레스 테마

수지성당 사이트(https://sujica.dothome.co.kr)의 커스텀 테마 저장소.

- 로컬 `suji/` ↔ 서버 `html/wp-content/themes/suji/` 가 1:1 대응한다.
- 저장소 루트의 나머지 파일(README, .deploy 등)은 서버에 올라가지 않는다.

## 작업 흐름

`suji/` 안의 파일을 고친 뒤 푸시하면 GitHub 반영과 FTP 업로드가 함께 일어난다.

```sh
./deploy.sh "무엇을 바꿨는지"     # add + commit + push + FTP
```

PhpStorm 의 Commit/Push 버튼을 써도 동일하게 동작한다 — `pre-push` 훅이
GitHub 로 보내기 직전에 변경된 테마 파일만 FTP 로 올리고, 지운 파일은 서버에서도 지운다.

FTP 업로드가 실패하면 푸시도 중단된다. FTP 없이 GitHub 에만 올리려면:

```sh
SKIP_FTP=1 git push
```

## 수동 배포

```sh
python3 .deploy/ftp_deploy.py --all              # 전체 다시 업로드
python3 .deploy/ftp_deploy.py --since HEAD~3     # 최근 3커밋 변경분
python3 .deploy/ftp_deploy.py --all --dry-run    # 전송 없이 목록만
```

## 접속 정보

`.deploy/config.env` 에 있고 `.gitignore` 로 제외되어 있다 (이 저장소는 public).
다른 PC 에서 클론하면 `.deploy/config.env.example` 을 복사해 값을 채워야 한다.

## 새 PC 에서 클론했을 때

```sh
git config core.hooksPath .githooks
cp .deploy/config.env.example .deploy/config.env   # 값 채우기
```
