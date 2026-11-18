from pathlib import Path
import re
from collections import Counter

roots = [Path("yeahthemes-sparkle"), Path("sparkle-childtheme")]
rules = {
 "removed_create_function": r"\bcreate_function\s*\(",
 "removed_each": r"(?<![\w>])\beach\s*\(",
 "removed_magic_quotes": r"\bget_magic_quotes_(?:gpc|runtime)\s*\(",
 "removed_mysql": r"\bmysql_(?:query|connect|fetch_\w+|real_escape_string)\s*\(",
 "removed_curly_offset": r"\$[A-Za-z_]\w*(?:\[[^\]\n]+\])?\{\s*(?:\d+|['\"][^}]+['\"])\s*\}",
 "deprecated_utf8": r"\b(?:utf8_encode|utf8_decode)\s*\(",
 "deprecated_FILTER_SANITIZE_STRING": r"\bFILTER_SANITIZE_STRING\b",
 "dynamic_property_candidate": r"\$this->\w+\s*=(?!=)",
 "raw_request_input": r"\$_(?:GET|POST|REQUEST|COOKIE)\s*\[",
 "raw_db_query": r"\$wpdb->(?:query|get_results|get_var|get_row)\s*\(",
 "jquery_legacy": r"\.(?:live|die|size|andSelf)\s*\(",
 "legacy_http": r"http://",
}
compiled = {k:re.compile(v) for k,v in rules.items()}
inventory = []
hits = []
for root in roots:
 for p in sorted(x for x in root.rglob("*") if x.is_file()):
  rel = p.as_posix()
  inventory.append((rel, p.suffix.lower() or "(none)", p.stat().st_size))
  if p.suffix.lower() not in {".php",".js",".css",".less",".txt",".xml",".json"}: continue
  s = p.read_text(encoding="utf-8",errors="replace")
  for n,line in enumerate(s.splitlines(),1):
   for k,pat in compiled.items():
    if k=="jquery_legacy" and p.suffix!=".js": continue
    if k not in ("jquery_legacy","legacy_http") and p.suffix!=".php": continue
    if pat.search(line): hits.append((k,rel,n,line.strip()[:150]))
count = Counter(x[0] for x in hits)
with open("sparkle-static-audit.md","w") as f:
 f.write("# Sparkle two-theme full inventory and heuristic findings\n\n")
 f.write("Pattern matches are review candidates, not verified bugs. This is not a substitute for runtime testing or manual review.\n\n")
 f.write("Total files: "+str(len(inventory))+"; matches: "+str(len(hits))+"\n\n")
 f.write("## Candidate matches\n\n| Rule | File | Line | Context |\n|---|---|---:|---|\n")
 for k,path,n,line in hits:
  f.write("| "+k+" | "+path+" | "+str(n)+" | "+line.replace("|","/")+" |\n")
 f.write("\n## Complete file inventory\n\n| Path | Extension | Bytes |\n|---|---|---:|\n")
 for path,ext,size in inventory: f.write("| "+path+" | "+ext+" | "+str(size)+" |\n")
print("files",len(inventory),"matches",len(hits),"rules",dict(count))
