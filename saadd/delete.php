<?php
// 📁 المسار الأساسي
$baseDir = "nameseller";

// ✅ استلام المعطيات من POST أو GET
$year  = $_REQUEST['year']  ?? '';
$month = $_REQUEST['month'] ?? '';
$day   = $_REQUEST['day']   ?? '';
$name  = $_REQUEST['name']  ?? '';

// 🔐 التحقق من القيم
if (!$year || !$month || !$day || !$name) {
    http_response_code(400);
    exit("❌ المعطيات ناقصة (year, month, day, name)");
}

// 🔍 تحديد مسار الملف
$filePath = "$baseDir/$year/$month/$day/$name";

// 🗑️ حذف الملف
if (file_exists($filePath)) {
    if (unlink($filePath)) {
        echo "✅ تم حذف الملف: $filePath";
    } else {
        http_response_code(500);
        echo "❌ فشل في حذف الملف.";
    }
} else {
    http_response_code(404);
    echo "❌ الملف غير موجود.";
}
?>