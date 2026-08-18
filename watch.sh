#!/bin/sh
# suji/ 를 감시하며 파일 저장 즉시 FTP 로 전송한다. (Ctrl+C 로 중지)
cd "$(dirname "$0")"
exec python3 -u .deploy/ftp_deploy.py --watch
