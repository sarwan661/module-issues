<?php

// Define allowed Unicode ranges
$allowed_unicode_ranges = implode('', [
  '\x{0000}-\x{007F}', // English
  '\x{0900}-\x{097F}', // Devanagari (Hindi, Marathi)
  '\x{0980}-\x{09FF}', // Bengali
  '\x{0A00}-\x{0A7F}', // Gurmukhi (Punjabi)
  '\x{0A80}-\x{0AFF}', // Gujarati
  '\x{0B00}-\x{0B7F}', // Odia
  '\x{0B80}-\x{0BFF}', // Tamil
  '\x{0C00}-\x{0C7F}', // Telugu
  '\x{0C80}-\x{0CFF}', // Kannada
  '\x{0D00}-\x{0D7F}', // Malayalam
]);

$foreign_pattern = '/[^' . $allowed_unicode_ranges . ']/u';

$result = db_query("SELECT nid, subject, mail, timestamp, comment, format FROM {comments} WHERE status = 0 ORDER BY nid ASC");

$found = false;

while ($comment = db_fetch_object($result)) {
  $subject = trim($comment->subject);
  $comment_text = trim($comment->comment);

  if (empty($subject) && empty($comment_text)) {
    continue;
  }

  if (preg_match($foreign_pattern, $subject) || preg_match($foreign_pattern, $comment_text)) {
    $found = true;
    $date = format_date($comment->timestamp, 'short');
    print "NID: {$comment->nid}\n";
    print "Subject: {$subject}\n";
    print "Date: {$date}\n";
    print "Comment: " . strip_tags(check_markup($comment_text, $comment->format, FALSE)) . "\n";
    print str_repeat('-', 60) . "\n";
  }
}

if (!$found) {
  print "No published comments with foreign characters found.\n";
}
