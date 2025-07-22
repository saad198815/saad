<?php
// 📁 المسار الأساسي لتخزين الملفات
$baseDir = "nameseller";

// ✅ الحصول على البيانات من الرابط
$year    = $_GET['year']    ?? '';
$month   = $_GET['month']   ?? '';
$day     = $_GET['day']     ?? '';
$name    = $_GET['name']    ?? '';
$content = $_GET['content'] ?? '';

// 🔒 تحقق من القيم
if (!$year || !$month || !$day || !$name) {
    exit("❌ مفقود أحد المعطيات (year, month, day, name)");
}

// 🛠️ أنشئ المجلدات إذا لم تكن موجودة
$dir = "$baseDir/$year/$month/$day";
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

// ✍️ حفظ المحتوى في الملف
$filename = "$dir/$name";
if (file_put_contents($filename, $content) !== false) {
    echo "✅ تم حفظ الملف: $filename";
} else {
    echo "❌ فشل في حفظ الملف.";
}
?>