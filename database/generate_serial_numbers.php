<?php
/**
 * 为已有交易记录生成流水号
 * 使用方法：php database/generate_serial_numbers.php
 */

require_once __DIR__ . '/../app/core/DB.php';

// 连接数据库
try {
    $conn = DB::conn();
    
    // 获取所有没有流水号的交易记录
    $stmt = $conn->prepare("SELECT id, `type`, created_at FROM transactions WHERE serial_number IS NULL OR serial_number = '' ORDER BY id ASC");
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $count = 0;
    $updateStmt = $conn->prepare("UPDATE transactions SET serial_number = ? WHERE id = ?");
    
    foreach ($transactions as $tx) {
        // 生成流水号：日期(YYYYMMDD) + 类别(EXP/INC) + ID
        $datePrefix = date('Ymd', strtotime($tx['created_at']));
        $typePrefix = strtoupper(substr($tx['type'], 0, 3)); // EXP 或 INC
        $serialNumber = $datePrefix . '-' . $typePrefix . '-' . str_pad($tx['id'], 6, '0', STR_PAD_LEFT);
        
        $updateStmt->execute([$serialNumber, $tx['id']]);
        $count++;
        
        echo "已更新交易 ID {$tx['id']}: {$serialNumber}\n";
    }
    
    echo "\n完成！共更新 {$count} 条记录。\n";
    
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
    exit(1);
}

