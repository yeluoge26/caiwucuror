# PowerShell 脚本：自动下载 XAMPP
# 使用方法：以管理员身份运行 PowerShell，执行：.\download_xampp.ps1

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   XAMPP 自动下载工具" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# 检查管理员权限
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "[错误] 请以管理员身份运行 PowerShell！" -ForegroundColor Red
    Write-Host "右键点击 PowerShell，选择'以管理员身份运行'" -ForegroundColor Yellow
    pause
    exit 1
}

# XAMPP 下载链接（PHP 8.1 版本）
$downloadUrl = "https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.1.17/xampp-windows-x64-8.1.17-0-VS16-installer.exe/download"
$installerPath = "$env:TEMP\xampp-installer.exe"
$installPath = "C:\xampp"

Write-Host "[1/3] 检查是否已安装 XAMPP..." -ForegroundColor Yellow
if (Test-Path "$installPath\xampp-control.exe") {
    Write-Host "[✓] XAMPP 已安装在: $installPath" -ForegroundColor Green
    Write-Host ""
    Write-Host "请运行 setup.bat 进行数据库配置" -ForegroundColor Cyan
    pause
    exit 0
}

Write-Host "[✗] 未检测到 XAMPP" -ForegroundColor Red
Write-Host ""

Write-Host "[2/3] 准备下载 XAMPP..." -ForegroundColor Yellow
Write-Host "下载地址: $downloadUrl" -ForegroundColor Cyan
Write-Host "保存位置: $installerPath" -ForegroundColor Cyan
Write-Host ""

$choice = Read-Host "是否开始下载？(Y/N)"
if ($choice -ne "Y" -and $choice -ne "y") {
    Write-Host "已取消下载" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "您可以手动下载 XAMPP：" -ForegroundColor Cyan
    Write-Host "1. 访问: https://www.apachefriends.org/download.html" -ForegroundColor White
    Write-Host "2. 下载 XAMPP for Windows (PHP 8.1)" -ForegroundColor White
    Write-Host "3. 运行安装程序" -ForegroundColor White
    Write-Host "4. 安装完成后运行 setup.bat" -ForegroundColor White
    pause
    exit 0
}

Write-Host ""
Write-Host "[3/3] 正在下载 XAMPP..." -ForegroundColor Yellow
Write-Host "这可能需要几分钟，请耐心等待..." -ForegroundColor Cyan
Write-Host ""

try {
    # 使用 WebClient 下载
    $webClient = New-Object System.Net.WebClient
    $webClient.DownloadFile($downloadUrl, $installerPath)
    
    Write-Host "[✓] 下载完成！" -ForegroundColor Green
    Write-Host ""
    Write-Host "安装文件位置: $installerPath" -ForegroundColor Cyan
    Write-Host ""
    
    $installChoice = Read-Host "是否立即运行安装程序？(Y/N)"
    if ($installChoice -eq "Y" -or $installChoice -eq "y") {
        Write-Host ""
        Write-Host "正在启动安装程序..." -ForegroundColor Yellow
        Write-Host ""
        Write-Host "安装提示：" -ForegroundColor Cyan
        Write-Host "1. 选择安装路径（推荐: C:\xampp）" -ForegroundColor White
        Write-Host "2. 选择组件：Apache、MySQL、PHP、phpMyAdmin（必须）" -ForegroundColor White
        Write-Host "3. 完成安装后，运行 setup.bat 配置数据库" -ForegroundColor White
        Write-Host ""
        
        Start-Process -FilePath $installerPath -Wait
        
        Write-Host ""
        Write-Host "安装完成后，请运行 setup.bat 进行数据库配置" -ForegroundColor Green
    } else {
        Write-Host ""
        Write-Host "安装文件已保存到: $installerPath" -ForegroundColor Cyan
        Write-Host "请手动运行安装程序，然后运行 setup.bat" -ForegroundColor Yellow
    }
    
} catch {
    Write-Host ""
    Write-Host "[错误] 下载失败: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "请手动下载 XAMPP：" -ForegroundColor Yellow
    Write-Host "1. 访问: https://www.apachefriends.org/download.html" -ForegroundColor White
    Write-Host "2. 下载 XAMPP for Windows" -ForegroundColor White
    Write-Host "3. 运行安装程序" -ForegroundColor White
    Write-Host "4. 安装完成后运行 setup.bat" -ForegroundColor White
}

Write-Host ""
pause
