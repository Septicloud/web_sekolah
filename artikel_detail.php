<?php
// artikel_detail.php (versi diperbaiki & lengkap)
// Pastikan memanggil: artikel_detail.php?url=<FULL_ARTICLE_URL>

if (!isset($_GET['url'])) {
    echo "Artikel tidak ditemukan.";
    exit;
}

$request_url = urldecode($_GET['url']);

// normalisasi: hapus ?m=1
$request_url = preg_replace('/\?m=\d+$/', '', $request_url);

// helper fetch dengan cURL
function fetch_url($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
    $body = curl_exec($ch);
    $err  = curl_error($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return [$body, $err, $info];
}

// bersihkan HTML yang tidak diinginkan
function clean_html($html) {
    $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
    $html = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is', '', $html);
    $html = preg_replace('/<ins\b[^>]*>(.*?)<\/ins>/is', '', $html);
    $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
    return $html;
}

// INISIALISASI supaya tidak ada "Undefined variable"
$konten = "";

// 1) Ambil HTML halaman artikel
list($html, $err, $info) = fetch_url($request_url);
if (!$html || strlen($html) < 50) {
    $html = "";
}

// 2) Jika ada HTML, coba ambil konten dengan DOM/XPath (lebih andal dari regex)
if ($html) {
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $loaded = @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    if ($loaded) {
        $xpath = new DOMXPath($dom);

        // beberapa query yang umum dipakai Blogger (desktop/mobile/page)
        $queries = [
            "//div[contains(@class,'post-body') and contains(@class,'entry-content')]",
            "//div[contains(@class,'post-body')]",
            "//div[contains(@class,'entry-content')]",
            "//div[contains(@id,'post-body')]",
            "//article",
            "//div[contains(@class,'post') and contains(@class,'hentry')]"
        ];

        foreach ($queries as $q) {
            $nodes = $xpath->query($q);
            if ($nodes->length > 0) {
                $node = $nodes->item(0);
                $inner = '';
                foreach ($node->childNodes as $child) {
                    $inner .= $dom->saveHTML($child);
                }
                $konten = trim($inner);
                if (strlen(strip_tags($konten)) > 30) break;
            }
        }
    }
    libxml_clear_errors();
}

// 3) Jika konten masih pendek / kosong => fallback ke feed JSON untuk mencari entry yang cocok
if (strlen(strip_tags($konten)) < 30) {
    // buat url feed JSON (host dari request_url)
    $parts = parse_url($request_url);
    if (!empty($parts['scheme']) && !empty($parts['host'])) {
        $base = $parts['scheme'] . '://' . $parts['host'];
        $feed_json_url = rtrim($base, '/') . "/feeds/posts/default?alt=json&max-results=500";

        list($feed_json, $ferr, $finfo) = fetch_url($feed_json_url);
        if ($feed_json && strlen($feed_json) > 50) {
            $feed_data = json_decode($feed_json, true);
            $entries = $feed_data['feed']['entry'] ?? [];

            $norm_req = rtrim($request_url, "/");
            foreach ($entries as $entry) {
                if (!isset($entry['link'])) continue;
                foreach ($entry['link'] as $l) {
                    $href = $l['href'] ?? '';
                    $href_norm = rtrim(preg_replace('/\?m=\d+$/', '', $href), "/");
                    if ($href_norm === $norm_req) {
                        // dapat entry cocok
                        $content = $entry['content']['$t'] ?? ($entry['summary']['$t'] ?? '');
                        if ($content && strlen(strip_tags($content)) > 30) {
                            $konten = $content;
                            break 2;
                        }

                        // jika content pendek, coba ambil post by id
                        $entry_id_full = $entry['id']['$t'] ?? '';
                        if ($entry_id_full && preg_match('/\/(\d+)$/', $entry_id_full, $mid)) {
                            $postid = $mid[1];
                            $post_feed_url = rtrim($base, '/') . "/feeds/posts/default/{$postid}?alt=json";
                            list($post_json, $perr, $pinfo) = fetch_url($post_feed_url);
                            if ($post_json) {
                                $pf = json_decode($post_json, true);
                                $post_entry = $pf['entry'] ?? null;
                                if ($post_entry) {
                                    $pc = $post_entry['content']['$t'] ?? ($post_entry['summary']['$t'] ?? '');
                                    if ($pc && strlen(strip_tags($pc)) > 30) {
                                        $konten = $pc;
                                        break 2;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

// 4) Very loose fallback: ambil <main> atau big content div
if (strlen(strip_tags($konten)) < 30 && $html) {
    if (preg_match('/<main\b[^>]*>(.*?)<\/main>/is', $html, $m)) {
        $konten = $m[1];
    } elseif (preg_match('/<div\b[^>]*id=["\']content["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
        $konten = $m[1];
    } elseif (preg_match('/<div\b[^>]*class=["\'](content|main|post|article)[^"\']*["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
        $konten = end($m);
    }
}

// 5) Jika masih kosong/tidak valid -> tampilkan pesan debug
if (strlen(strip_tags($konten)) < 30) {
    echo "<h3>Konten tidak dapat diambil dari halaman berikut:</h3>";
    echo "<p>URL: <strong>" . htmlspecialchars($request_url) . "</strong></p>";
    echo "<p>Silakan pastikan itu adalah URL artikel (bukan label/archive/feed). Jika memungkinkan, kirim \"View Page Source\" potongan body artikel.</p>";
    exit;
}

// Bersihkan konten dari tag yang mengganggu
$konten = clean_html($konten);

// Perbaiki gambar src relatif menjadi absolute
$base = parse_url($request_url, PHP_URL_SCHEME) . '://' . parse_url($request_url, PHP_URL_HOST);
$konten = preg_replace_callback('/(<img[^>]+src=["\'])([^"\']+)(["\'])/i', function($m) use ($base) {
    $src = $m[2];
    if (strpos($src, '//') === 0) return $m[1] . 'https:' . $src . $m[3];
    if (preg_match('#^https?://#i', $src)) return $m[1] . $src . $m[3];
    $new = rtrim($base, '/') . '/' . ltrim($src, '/');
    return $m[1] . $new . $m[3];
}, $konten);

// RAPIKAN PARAGRAF: ubah double <br> menjadi paragraf & bungkus kalau belum ada <p>
$konten = preg_replace('/(<br\s*\/?>\s*){2,}/i', '</p><p>', $konten);

// jika tidak ada <p> sama sekali, bungkus seluruh konten
if (!preg_match('/<p\b/i', $konten)) {
    $konten = '<p>' . $konten . '</p>';
}

// hapus span kosong & rapikan spasi ganda
$konten = preg_replace('/<span[^>]*>\s*<\/span>/i', '', $konten);
$konten = preg_replace('/\s{2,}/', ' ', $konten);

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Artikel Detail</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .artikel-wrapper {
        font-size: 17px;
        line-height: 1.8;
        max-width: 850px;
        margin: auto;
    }
    .artikel-wrapper p {
        margin-bottom: 18px;
        text-align: justify;
    }
    .artikel-wrapper img { max-width:100%; margin:16px 0; border-radius:8px; }
    .artikel-wrapper h1,h2,h3 { margin:28px 0 12px; text-align:center; color:#0e9455; }
    .artikel-wrapper ol, .artikel-wrapper ul { margin-left:22px; margin-bottom:20px; }
  </style>
</head>
<body>
<div class="container mt-4">
  <a href="artikel.php" class="btn btn-secondary mb-3">← Kembali</a>
  <div class="card p-4 artikel-wrapper">
    <?php echo $konten; ?>
  </div>
</div>
</body>
</html>
