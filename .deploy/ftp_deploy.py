#!/usr/bin/env python3
"""suji 테마를 dothome FTP 서버로 동기화한다.

사용법:
  python3 .deploy/ftp_deploy.py --all                 # 전체 업로드
  python3 .deploy/ftp_deploy.py --since <rev>         # rev..HEAD 변경분만
  python3 .deploy/ftp_deploy.py --range <old> <new>   # old..new 변경분만
  옵션: --dry-run (실제 전송 없이 목록만), --delete (원격 삭제까지 반영)

접속 정보는 .deploy/config.env 에서 읽는다 (git 추적 대상 아님).
"""
import ftplib, os, subprocess, sys, posixpath

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
LOCAL_PREFIX = "suji"          # 저장소 내 테마 경로
CONFIG = os.path.join(ROOT, ".deploy", "config.env")


def load_config():
    if not os.path.exists(CONFIG):
        sys.exit("설정 파일이 없습니다: .deploy/config.env")
    cfg = {}
    with open(CONFIG, encoding="utf-8") as fh:
        for line in fh:
            line = line.strip()
            if not line or line.startswith("#") or "=" not in line:
                continue
            k, v = line.split("=", 1)
            cfg[k.strip()] = v.strip().strip('"').strip("'")
    for key in ("FTP_HOST", "FTP_USER", "FTP_PASS", "FTP_REMOTE_DIR"):
        if not cfg.get(key):
            sys.exit(f"config.env 에 {key} 가 없습니다")
    return cfg


def git(*args):
    return subprocess.run(["git", "-C", ROOT, *args],
                          capture_output=True, text=True).stdout


def changed_files(old, new):
    """old..new 사이에 변경된 suji/ 하위 파일을 (상태, 경로) 로 돌려준다."""
    out = git("diff", "--name-status", "-z", old, new)
    items, parts = [], out.split("\0")
    i = 0
    while i < len(parts):
        st = parts[i]
        if not st:
            break
        if st[0] == "R":                       # rename: 상태, 옛경로, 새경로
            old_p, new_p = parts[i + 1], parts[i + 2]
            items.append(("D", old_p))
            items.append(("M", new_p))
            i += 3
        else:
            items.append((st[0], parts[i + 1]))
            i += 2
    return [(s, p) for s, p in items
            if p == LOCAL_PREFIX or p.startswith(LOCAL_PREFIX + "/")]


def all_files():
    out = git("ls-files", "-z", LOCAL_PREFIX)
    return [("M", p) for p in out.split("\0") if p]


class Deployer:
    def __init__(self, cfg, dry):
        self.cfg, self.dry = cfg, dry
        self.made = set()
        self.ftp = None

    def connect(self):
        self.ftp = ftplib.FTP()
        self.ftp.encoding = "utf-8"
        self.ftp.connect(self.cfg["FTP_HOST"], int(self.cfg.get("FTP_PORT", 21)), 30)
        self.ftp.login(self.cfg["FTP_USER"], self.cfg["FTP_PASS"])
        self.ftp.set_pasv(True)

    def remote_path(self, rel):
        # suji/xxx -> <FTP_REMOTE_DIR>/xxx  (FTP_REMOTE_DIR 이 테마 폴더 자체)
        return posixpath.join(self.cfg["FTP_REMOTE_DIR"], rel[len(LOCAL_PREFIX) + 1:])

    def ensure_dir(self, rpath):
        if rpath in self.made or rpath in ("", "/", "."):
            return
        parent = posixpath.dirname(rpath)
        if parent and parent not in self.made:
            self.ensure_dir(parent)
        try:
            self.ftp.mkd(rpath)
        except ftplib.error_perm as e:
            if not str(e).startswith(("550", "521")):   # 이미 존재하면 무시
                raise
        self.made.add(rpath)

    def upload(self, rel):
        lpath = os.path.join(ROOT, rel)
        rpath = self.remote_path(rel)
        print(f"  ↑ {rel} -> {rpath}")
        if self.dry:
            return
        self.ensure_dir(posixpath.dirname(rpath))
        with open(lpath, "rb") as fh:
            self.ftp.storbinary("STOR " + rpath, fh)

    def delete(self, rel):
        rpath = self.remote_path(rel)
        print(f"  ✗ {rel} -> {rpath}")
        if self.dry:
            return
        try:
            self.ftp.delete(rpath)
        except ftplib.error_perm as e:
            print(f"    (삭제 건너뜀: {e})")


def main():
    argv = sys.argv[1:]
    dry = "--dry-run" in argv
    do_delete = "--delete" in argv
    argv = [a for a in argv if a not in ("--dry-run", "--delete")]

    if not argv or argv[0] == "--all":
        items = all_files()
        label = "전체"
    elif argv[0] == "--since":
        items = changed_files(argv[1], "HEAD")
        label = f"{argv[1]}..HEAD"
    elif argv[0] == "--range":
        items = changed_files(argv[1], argv[2])
        label = f"{argv[1]}..{argv[2]}"
    else:
        sys.exit(__doc__)

    uploads = [p for s, p in items if s != "D" and os.path.exists(os.path.join(ROOT, p))]
    deletes = [p for s, p in items if s == "D"]
    if not do_delete:
        deletes = []

    print(f"[FTP 배포] 대상: {label} — 업로드 {len(uploads)}건, 삭제 {len(deletes)}건"
          + (" (dry-run)" if dry else ""))
    if not uploads and not deletes:
        print("  변경된 테마 파일이 없습니다.")
        return

    cfg = load_config()
    d = Deployer(cfg, dry)
    if not dry:
        d.connect()
    try:
        for rel in sorted(uploads):
            d.upload(rel)
        for rel in sorted(deletes):
            d.delete(rel)
    finally:
        if d.ftp:
            try:
                d.ftp.quit()
            except Exception:
                d.ftp.close()
    print("[FTP 배포] 완료" + (" (dry-run)" if dry else ""))


if __name__ == "__main__":
    main()
