<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>رفع وتعديل المستندات</title>
</head>
<body>

<?php
$baseDir = 'nameseller';

$selectedYear  = $_GET['year']  ?? date('Y');
$selectedMonth = $_GET['month'] ?? date('m');
$selectedDay   = $_GET['day']   ?? date('d');

$uploadPath = "$baseDir/$selectedYear/$selectedMonth/$selectedDay";
createFolderIfNotExists($uploadPath);

// ===== دوال المساعدة =====
function createFolderIfNotExists($path) {
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

function handleFileUpload($inputName, $destinationPath, $newFileName = null) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
        $time = date('H-i-s');
        $rand = rand(1000, 9999);
        $newFileName = $newFileName ?? "$time-$rand.$ext";
        $fullPath = "$destinationPath/$newFileName";
        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $fullPath)) {
            return $newFileName;
        }
    }
    return false;
}

function renderForm($type = 'upload', $oldFile = '') {
    global $selectedYear, $selectedMonth, $selectedDay;
    $hidden = $type === 'update' ? "<input type='hidden' name='old_file' value='$oldFile'>" : '';
    $actionType = $type === 'update' ? 'update' : 'upload';
    echo "
    <h3>" . ($type === 'update' ? "تعديل الملف: $oldFile" : "رفع ملف جديد") . "</h3>
    <form method='post' enctype='multipart/form-data'>
        <input type='file' name='file' required><br><br>
        $hidden
        <input type='hidden' name='action' value='$actionType'>
        <button type='submit'>📤 " . ($type === 'update' ? "تحديث الملف" : "رفع الملف") . "</button>
    </form><hr>
    ";
}

// ===== فلترة التاريخ =====
echo "
<form method='get'>

  <select name='day'>" .
        generateOptions(1, 31, $selectedDay) .
    "</select>
    

    <select name='month'>" .
        generateOptions(1, 12, $selectedMonth) .
    "</select>


 
  
        <select name='year'>" .
        generateOptions(2020, date('Y'), $selectedYear) .
    "</select>

    

    <button type='submit'>🔍 عرض الفواتير</button>
</form><hr>
";

function generateOptions($start, $end, $selected) {
    $options = '';
    for ($i = $start; $i <= $end; $i++) {
        $val = str_pad($i, 2, '0', STR_PAD_LEFT);
        $sel = ($val == $selected) ? 'selected' : '';
        $options .= "<option value='$val' $sel>$val</option>";
    }
    return $options;
}

// ===== معالجة رفع أو تعديل =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'upload') {
        $newName = handleFileUpload('file', $uploadPath);
        echo $newName ? "<p style='color:green;'>✅ تم رفع الملف: $newName</p>" : "<p style='color:red;'>❌ فشل في رفع الملف.</p>";
    }

    if ($action === 'update') {
        $oldFile = $_POST['old_file'];
        $fullOldPath = "$uploadPath/$oldFile";
        if (file_exists($fullOldPath)) unlink($fullOldPath);
        $updated = handleFileUpload('file', $uploadPath, $oldFile);
        echo $updated ? "<p style='color:blue;'>✅ تم تعديل الملف: $oldFile</p>" : "<p style='color:red;'>❌ فشل في تعديل الملف.</p>";
    }
}

// ===== عرض نموذج تعديل أو رفع =====
if (isset($_GET['edit'])) {
    $fileToEdit = $_GET['edit'];
    renderForm('update', $fileToEdit);
} else {
    renderForm('upload');
}

// ===== عرض الملفات المفلترة =====
$files = is_dir($uploadPath) ? array_diff(scandir($uploadPath), ['.', '..']) : [];
if ($files) {
    echo "<h3>📁 الفواتير بتاريخ: $selectedYear-$selectedMonth-$selectedDay</h3><ul>";
  foreach ($files as $file) {
    $query = http_build_query([
        'year' => $selectedYear,
        'month' => $selectedMonth,
        'day' => $selectedDay,
        'edit' => $file
    ]);
    echo "<li>$file 
            <a href='?$query'>🛠️ تعديل</a> | 
            <a href='$uploadPath/$file' download>⬇️ تحميل</a>
          </li>";
}
    echo "</ul>";
} else {
    echo "<p>🚫 لا توجد ملفات في هذا التاريخ.</p>";
}
?>

</body>
</html>

