<?php
$ch = curl_init('https://www.tech.ac.jp/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
exif_imagetype(string $filename): int|false
print(htmlspecialchars($res));
exif_read_data(
    resource|string $file,
    ?string $required_sections = null,
    bool $as_arrays = false,
    bool $read_thumbnail = false
): array|false
exif_tagname(int $index): string|false
exif_thumbnail(
    resource|string $file,
    int &$width = null,
    int &$height = null,
    int &$image_type = null
): string|false

