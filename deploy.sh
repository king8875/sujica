#!/bin/sh
# 커밋 + GitHub 푸시 + FTP 반영을 한 번에 실행한다.
# 사용법: ./deploy.sh "커밋 메시지"
set -e
cd "$(dirname "$0")"
MSG=${1:-"update suji theme"}

if [ -n "$(git status --porcelain)" ]; then
  git add -A
  git commit -m "$MSG"
else
  echo "커밋할 변경 사항이 없습니다."
fi

git push origin HEAD   # pre-push 훅이 FTP 업로드를 수행
