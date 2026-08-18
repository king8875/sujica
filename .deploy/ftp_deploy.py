#!/usr/bin/env python3
"""suji 테마를 dothome FTP 서버로 동기화한다.

사용법:
  python3 .deploy/ftp_deploy.py --all                 # 전체 업로드
  python3 .deploy/ftp_deploy.py --since <rev>         # rev..HEAD 변경분만
  python3 .deploy/ftp_deploy.py --range <old> <new>   # old..new 변경분만
  python3 .deploy/ftp_deploy.py --watch                # 저장 즉시 자동 전송 (상주)
  python3 .deploy/ftp_deploy.py --files suji/a.php ...  # 지정 파일만
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

    def reconnect(self):
        try:
            if self.ftp:
                self.ftp.close()
        except Exception:
            pass
        self.made.clear()
        self.connect()

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


IGNORE_SUFFIX = ("~", ".swp", ".swx", ".tmp", ".orig", ".rej")
IGNORE_SUBSTR = ("___jb_tmp___", "___jb_old___", "/.git/", "/.idea/", ".DS_Store")


def is_watchable(rel):
    """에디터 임시 파일·숨김 파일은 전송 대상에서 제외한다."""
    if not (rel == LOCAL_PREFIX or rel.startswith(LOCAL_PREFIX + "/")):
        return False
    if rel.endswith(IGNORE_SUFFIX) or any(s in rel for s in IGNORE_SUBSTR):
        return False
    return not any(part.startswith(".") for part in rel.split("/")[1:])


def watch(cfg):
    """suji/ 를 감시하다가 파일이 바뀌면 즉시 FTP 로 올린다."""
    import shutil, threading
    watch_dir = os.path.join(ROOT, LOCAL_PREFIX)
    if not shutil.which("fswatch"):
        sys.exit("fswatch 가 필요합니다:  brew install fswatch")

    d = Deployer(cfg, dry=False)
    d.connect()
    print(f"[감시 시작] {watch_dir}")
    print(f"           -> {cfg['FTP_HOST']}:{cfg['FTP_REMOTE_DIR']}")
    print("           중지: Ctrl+C")

    # FTP 유휴 타임아웃 방지용 keepalive
    stop = threading.Event()

    def keepalive():
        while not stop.wait(45):
            try:
                d.ftp.voidcmd("NOOP")
            except Exception:
                pass

    threading.Thread(target=keepalive, daemon=True).start()

    proc = subprocess.Popen(
        ["fswatch", "-0", "--latency", "0.3", "-r", watch_dir],
        stdout=subprocess.PIPE)
    buf = b""
    try:
        while True:
            # os.read 는 파이프에 도착한 만큼만 즉시 돌려준다
            # (buffered read(n) 은 n 바이트가 찰 때까지 블록된다)
            chunk = os.read(proc.stdout.fileno(), 65536)
            if not chunk:
                break
            buf += chunk
            *paths, buf = buf.split(b"\0")
            seen = []
            for raw in paths:
                abs_p = raw.decode("utf-8", "replace")
                rel = os.path.relpath(abs_p, ROOT)
                if rel in seen or not is_watchable(rel) or os.path.isdir(abs_p):
                    continue
                seen.append(rel)
            for rel in seen:
                for attempt in (1, 2):
                    try:
                        if os.path.exists(os.path.join(ROOT, rel)):
                            d.upload(rel)
                        else:
                            d.delete(rel)
                        break
                    except Exception as e:
                        if attempt == 1:
                            print(f"    (재연결: {e})")
                            d.reconnect()
                        else:
                            print(f"    ⚠ 실패: {rel} — {e}")
    except KeyboardInterrupt:
        print("\n[감시 종료]")
    finally:
        stop.set()
        proc.terminate()
        try:
            d.ftp.quit()
        except Exception:
            pass


def main():
    argv = sys.argv[1:]
    dry = "--dry-run" in argv
    do_delete = "--delete" in argv
    argv = [a for a in argv if a not in ("--dry-run", "--delete")]

    if argv and argv[0] == "--watch":
        watch(load_config())
        return

    if argv and argv[0] == "--files":
        items = [("M", os.path.relpath(os.path.abspath(p), ROOT)) for p in argv[1:]]
        label = f"지정 파일 {len(items)}건"
    elif not argv or argv[0] == "--all":
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
