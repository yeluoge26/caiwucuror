<?php
require_once __DIR__ . '/../core/DB.php';

class Attachment {
  public static function findByTransaction($transactionId) {
    $stmt = DB::conn()->prepare("
      SELECT a.*, u.display_name as uploader_name
      FROM attachments a
      LEFT JOIN users u ON a.uploaded_by = u.id
      WHERE a.transaction_id = ?
      ORDER BY a.created_at DESC
    ");
    $stmt->execute([$transactionId]);
    return $stmt->fetchAll();
  }

  public static function find($id) {
    $stmt = DB::conn()->prepare("SELECT * FROM attachments WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  public static function create($data) {
    $stmt = DB::conn()->prepare("
      INSERT INTO attachments (transaction_id, file_path, file_type, uploaded_by)
      VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([
      $data['transaction_id'],
      $data['file_path'],
      $data['file_type'] ?? null,
      $data['uploaded_by']
    ]);
  }

  public static function delete($id) {
    $attachment = self::find($id);
    if ($attachment) {
      // 删除文件
      $filePath = __DIR__ . '/../../public/' . $attachment['file_path'];
      if (file_exists($filePath)) {
        @unlink($filePath);
      }
      // 删除数据库记录
      $stmt = DB::conn()->prepare("DELETE FROM attachments WHERE id = ?");
      return $stmt->execute([$id]);
    }
    return false;
  }

  public static function deleteByTransaction($transactionId) {
    $attachments = self::findByTransaction($transactionId);
    foreach ($attachments as $att) {
      self::delete($att['id']);
    }
    return true;
  }
}

