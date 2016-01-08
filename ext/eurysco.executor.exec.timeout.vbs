On Error Resume Next
WScript.Sleep(WScript.Arguments(0))
Set objWMIService = GetObject("winmgmts:\\.\root\CIMV2")
Set colItems = objWMIService.ExecQuery("SELECT ProcessId FROM Win32_Process WHERE Caption = 'php_eurysco_executor.exe'",,48)
Set objShell = WScript.CreateObject("WScript.shell")
PeaProcessId = ""
For Each objItem in colItems
	PeaProcessId = objItem.ProcessId
Next
Set colItems = objWMIService.ExecQuery("SELECT ProcessId FROM Win32_Process WHERE Caption = 'cmd.exe' AND ParentProcessId = '" & PeaProcessId & "'",,48)
PtoProcessId = ""
For Each objItem in colItems
	PtoProcessId = " /pid " & objItem.ProcessId
Next
If PtoProcessId <> "" Then
	objShell.run "cmd /c taskkill.exe /f" & PtoProcessId & " /t", 0, False
End If
Set objShell = Nothing
Set colItems = Nothing
Set objWMIService = Nothing