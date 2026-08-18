# sujica — suji 워드프레스 테마

수지성당 사이트(https://sujica.dothome.co.kr)의 커스텀 테마 저장소.

- 로컬 `suji/` ↔ 서버 `html/wp-content/themes/suji/` 가 1:1 대응한다.
- 저장소 루트의 나머지 파일(README, .deploy 등)은 서버에 올라가지 않는다.

## 저장 즉시 FTP 전송

`suji/` 안의 파일을 저장하는 순간 서버에 반영된다. 두 가지 방법이 있고, **둘 중 하나만** 쓰면 된다.

### 1) PhpStorm 자동 업로드 (권장 — 별도 프로세스 없음)

`.idea/` 에 서버(`sujica`)와 매핑(`suji/` → `/html/wp-content/themes/suji`),
자동 업로드(Always)가 이미 설정되어 있다. PhpStorm 을 다시 열고 비밀번호만 한 번 입력하면 된다.

- `Settings > Build, Execution, Deployment > Deployment` 에서 `sujica` 선택 → Password 입력 → `Test Connection`
- 확인: `Tools > Deployment > Automatic Upload (always)` 에 체크
- 전송 기록은 하단 `File Transfer` 탭에 남는다

### 2) 감시 스크립트 (에디터 무관)

```sh
./watch.sh        # 실행해두면 저장할 때마다 자동 전송, Ctrl+C 로 중지
```

VS Code, vim 등 어떤 편집기로 저장해도 동작한다. `fswatch` 가 필요하다 (`brew install fswatch`).
편집기 임시 파일(`~`, `.swp`, `___jb_tmp___`)과 숨김 파일은 전송하지 않는다.

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
python3 .deploy/ftp_deploy.py --files suji/style.css   # 특정 파일만
```

> 폴더 삭제는 자동으로 반영되지 않는다(파일만 삭제된다). 서버에서 빈 폴더를 지우려면
> FTP 클라이언트나 호스팅 파일관리자에서 직접 지운다.

## 접속 정보

`.deploy/config.env` 에 있고 `.gitignore` 로 제외되어 있다 (이 저장소는 public).
다른 PC 에서 클론하면 `.deploy/config.env.example` 을 복사해 값을 채워야 한다.

## 새 PC 에서 클론했을 때

```sh
git config core.hooksPath .githooks
cp .deploy/config.env.example .deploy/config.env   # 값 채우기
```
