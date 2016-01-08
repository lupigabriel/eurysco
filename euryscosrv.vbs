On Error Resume Next
Set WshShell = CreateObject("WScript.Shell")
WshShell.RegWrite "HKLM\SYSTEM\CurrentControlSet\Services\" & WScript.Arguments(0) & "\Parameters\Application",Replace(WScript.Arguments(1),"@",""""),"REG_SZ"
Set WshShell = Nothing