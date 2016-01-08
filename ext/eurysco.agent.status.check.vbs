On Error Resume Next
Set objWMIService = GetObject("winmgmts:\\.\root\CIMV2")
Set fs = CreateObject("Scripting.FileSystemObject")
For eLoop = 0 To 1
	WScript.Sleep(60000)
	Set colItems = objWMIService.ExecQuery("SELECT State FROM Win32_Service WHERE Name = 'euryscoAgent'",,48)
	PeaState = ""
	For Each objItem in colItems
		PeaState = objItem.State
	Next
	If fs.FileExists(WScript.Arguments(0)) Then
		Set f = fs.GetFile(WScript.Arguments(0))
		If PeaState = "Running" And DateDiff("n", f.DateLastModified, Now()) > 10 Then
			WScript.Sleep(60000)
			Set objShell = WScript.CreateObject("WScript.shell")
			objShell.run "cmd /c sc.exe stop ""euryscoAgent"" & sc.exe start ""euryscoAgent""", 0, False
			Set objShell = Nothing
			Set f = Nothing
			Set fs = Nothing
			Set colItems = Nothing
			Set objWMIService = Nothing
			WScript.Quit
		End If
		If PeaState <> "Running" Then
			Set f = Nothing
			Set fs = Nothing
			Set colItems = Nothing
			Set objWMIService = Nothing
			WScript.Quit
		End If
	Else
		Set fs = Nothing
		Set colItems = Nothing
		Set objWMIService = Nothing
		WScript.Quit
	
	End If
	eLoop = 0
Next